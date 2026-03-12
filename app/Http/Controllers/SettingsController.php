<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackMail;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function help()
    {
        return view('settings.help');
    }

    public function sendFeedback(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:10|max:2000',
        ]);

        $user = Auth::user();
        
        try {
            Mail::to('huyberrrrr@gmail.com')->send(new FeedbackMail($request->content, $user));
            return back()->with('status', 'Cảm ơn bạn! Ý kiến của bạn đã được gửi đi thành công.');
        } catch (\Exception $e) {
            return back()->withErrors(['feedback' => 'Có lỗi xảy ra khi gửi phản hồi. Vui lòng thử lại sau.']);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => __('Mật khẩu hiện tại không chính xác.')]);
        }

        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return back()->with('status', __('Mật khẩu đã được thay đổi thành công.'));
    }

    public function togglePrivacy(Request $request)
    {
        $user = Auth::user();
        $user->is_private = !$user->is_private;
        $user->save();

        return response()->json([
            'status' => 'success',
            'is_private' => $user->is_private,
            'message' => $user->is_private ? 'Tài khoản hiện đang ở chế độ riêng tư.' : 'Tài khoản hiện đang ở chế độ công khai.'
        ]);
    }
}
