<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\ToxicContent;
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
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:160', new ToxicContent],
            'link_url' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],
        ]);

        $data = [
            'username' => $request->username,
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
        $myFollowingIds = auth()->user()->following()->pluck('users.id')->toArray();
        $followers->each(function ($u) use ($myFollowingIds) {
            $u->is_followed_by_me = in_array($u->id, $myFollowingIds);
        });
        return response()->json($followers);
    }

    public function following(User $user)
    {
        $following = $user->following()->withCount(['followers'])->get();
        $myFollowingIds = auth()->user()->following()->pluck('users.id')->toArray();
        $following->each(function ($u) use ($myFollowingIds) {
            $u->is_followed_by_me = in_array($u->id, $myFollowingIds);
        });
        return response()->json($following);
    }

    public function mutual(User $user)
    {
        $myFollowingIds = auth()->user()->following()->pluck('users.id')->toArray();
        $profileFollowerIds = $user->followers()->pluck('users.id')->toArray();
        $mutualIds = array_intersect($myFollowingIds, $profileFollowerIds);
        $mutualIds = array_diff($mutualIds, [auth()->id()]); // remove self
        
        $mutualUsers = User::whereIn('id', $mutualIds)->withCount(['followers'])->get();
        $mutualUsers->each(function ($u) {
            $u->is_followed_by_me = true; // they are in $myFollowingIds
        });
        return response()->json($mutualUsers);
    }
}
