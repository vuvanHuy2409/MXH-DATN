<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    /**
     * Gửi email OTP qua EmailJS API
     */
    private function sendEmailViaEmailJS($to_email, $otp)
    {
        $response = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
            'service_id' => env('EMAILJS_SERVICE_ID'),
            'template_id' => env('EMAILJS_TEMPLATE_ID'),
            'user_id' => env('EMAILJS_PUBLIC_KEY'),
            'template_params' => [
                'to_email' => $to_email,
                'otp' => $otp,
                'app_name' => config('app.name'),
            ],
        ]);

        return $response->successful();
    }

    public function showForgotPassword() { return view('auth.forgot-password'); }

    public function sendOtpForgotPassword(Request $request)
    {
        $request->validate(['email_prefix' => 'required']);
        $email = $request->email_prefix . '@eaut.edu.vn';
        
        if (!User::where('email', $email)->exists()) {
            return back()->withErrors(['email_prefix' => __('Email không tồn tại trong hệ thống.')])->withInput();
        }

        $otpCode = rand(100000, 999999);
        
        // Lưu vào Cache trong 1 phút
        Cache::put('otp_forgot_password_' . $email, $otpCode, now()->addMinute());

        Mail::to($email)->send(new SendOtpMail($otpCode));

        return redirect()->route('password.reset.form', ['email' => $email])->with('status', __('Mã OTP đã được gửi về email của bạn (hiệu lực 1 phút).'));
    }

    public function showResetPassword(Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);

        $cachedOtp = Cache::get('otp_forgot_password_' . $request->email);

        if (!$cachedOtp) {
            return back()->withErrors(['otp' => __('Mã OTP đã hết hạn (chỉ có hiệu lực trong 1 phút).')])->withInput();
        }

        if ($cachedOtp != $request->otp) {
            return back()->withErrors(['otp' => __('Mã OTP không chính xác.')])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $user->password_hash = Hash::make($request->password);
        $user->save();

        Cache::forget('otp_forgot_password_' . $request->email);

        return redirect('/login')->with('status', __('Mật khẩu đã được thay đổi thành công.'));
    }
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $request->validate(['email_prefix' => 'required', 'password' => 'required']);
        $email = $request->email_prefix . '@eaut.edu.vn';
        if (Auth::attempt(['email' => $email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
        return back()->withErrors(['email_prefix' => __('Thông tin đăng nhập không chính xác.')])->withInput();
    }

    public function showRegister()
    {
        $faculties = Faculty::all();
        return view('auth.register', compact('faculties'));
    }

    public function register(Request $request)
    {
        $user_type = $request->user_type;

        if ($user_type === 'student') {
            $request->validate([
                'student_id_prefix' => 'required|digits:8|unique:student_details,student_id',
                'full_name' => 'required|string|max:100',
                'faculty_id' => 'required|exists:faculties,id',
                'dob' => 'required|date',
                'class' => 'required|string|max:50',
                'password' => 'required|min:6|confirmed',
            ]);
            $email = $request->student_id_prefix . '@eaut.edu.vn';
        } else {
            $request->validate([
                'full_name' => 'required|string|max:100',
                'faculty_id' => 'required|exists:faculties,id',
                'teacher_email_prefix' => 'required|string|max:50',
                'password' => 'required|min:6|confirmed',
            ]);
            $email = $request->teacher_email_prefix . '@eaut.edu.vn';
        }

        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => __('Email này đã tồn tại.')])->withInput();
        }

        // Lưu thông tin vào session
        $registration_data = $request->all();
        $registration_data['email'] = $email;
        session(['registration_data' => $registration_data]);

        $otpCode = rand(100000, 999999);
        
        // Lưu vào Cache trong 1 phút
        Cache::put('otp_registration_' . $email, $otpCode, now()->addMinute());

        Mail::to($email)->send(new SendOtpMail($otpCode));

        return redirect()->route('register.verify.form', ['email' => $email])->with('status', __('Mã xác thực đã được gửi về email của bạn (hiệu lực 1 phút).'));
    }

    public function showVerifyRegistration(Request $request)
    {
        $email = $request->query('email');
        return view('auth.verify-registration', compact('email'));
    }

    public function verifyRegistration(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $cachedOtp = Cache::get('otp_registration_' . $request->email);

        if (!$cachedOtp) {
            return back()->withErrors(['otp' => __('Mã OTP đã hết hạn (chỉ có hiệu lực trong 1 phút).')])->withInput();
        }

        if ($cachedOtp != $request->otp) {
            return back()->withErrors(['otp' => __('Mã OTP không chính xác.')])->withInput();
        }

        $data = session('registration_data');
        if (!$data || $data['email'] !== $request->email) {
            return redirect()->route('register')->withErrors(['email' => __('Dữ liệu đăng ký không hợp lệ. Vui lòng đăng ký lại.')]);
        }

        // Tạo username tự động từ họ tên
        $baseUsername = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $this->removeVietnameseSigns($data['full_name'])));
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) { $username = $baseUsername . $counter++; }

        DB::transaction(function () use ($data, $username) {
            $user = User::create([
                'username' => $username,
                'email' => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'user_type' => $data['user_type'],
                'avatar_url' => '/avatars/user.png',
            ]);

            if ($data['user_type'] === 'student') {
                StudentDetail::create([
                    'user_id' => $user->id,
                    'student_id' => $data['student_id_prefix'],
                    'full_name' => $data['full_name'],
                    'dob' => $data['dob'],
                    'class' => $data['class'],
                    'faculty_id' => $data['faculty_id'],
                ]);
            } else {
                TeacherDetail::create([
                    'user_id' => $user->id,
                    'full_name' => $data['full_name'],
                    'faculty_id' => $data['faculty_id'],
                ]);
            }

            Auth::login($user);
            session()->forget('registration_data');
        });

        Cache::forget('otp_registration_' . $request->email);

        return redirect('/')->with('show_welcome', true);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function removeVietnameseSigns($str) {
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ', 'd'=>'đ', 'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị', 'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ', 'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ', 'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ', 'D'=>'Đ', 'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị', 'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ', 'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự', 'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($unicode as $nonUnicode=>$uni){ $str = preg_replace("/($uni)/i", $nonUnicode, $str); }
        return $str;
    }
}
