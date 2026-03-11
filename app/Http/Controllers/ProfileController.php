<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa hồ sơ.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin hồ sơ.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'bio' => ['nullable', 'string', 'max:160'],
            'link_url' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'bio' => $request->bio,
            'link_url' => $request->link_url,
        ];

        // Xử lý upload ảnh đại diện
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('avatars'), $filename);
            
            // Xóa ảnh cũ nếu có (và không phải ảnh mặc định)
            if ($user->avatar_url && file_exists(public_path($user->avatar_url)) && strpos($user->avatar_url, 'avatars/') !== false) {
                // @unlink(public_path($user->avatar_url));
            }

            $data['avatar_url'] = '/avatars/' . $filename;
        }

        $user->update($data);

        return redirect()->route('profile.me')->with('success', __('Hồ sơ đã được cập nhật!'));
    }

    public function followers(User $user)
    {
        $followers = $user->followers()->withCount(['followers'])->get();
        return response()->json($followers);
    }

    public function following(User $user)
    {
        $following = $user->following()->withCount(['followers'])->get();
        return response()->json($following);
    }
}
