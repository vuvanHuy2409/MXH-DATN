<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function changeLanguage(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,vi',
        ]);

        Session::put('locale', $request->locale);

        return back()->with('status', $request->locale == 'vi' ? 'Đã đổi ngôn ngữ sang Tiếng Việt' : 'Language changed to English');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return back()->with('status', 'Mật khẩu đã được thay đổi thành công.');
    }
}
