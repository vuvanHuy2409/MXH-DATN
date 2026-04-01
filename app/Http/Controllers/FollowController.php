<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use App\Models\Conversation;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    /**
     * Theo dõi hoặc Bỏ theo dõi một người dùng.
     */
    public function toggle(User $user)
    {
        $me = Auth::user();

        // Không thể tự theo dõi chính mình
        if ($me->id === $user->id) {
            return back();
        }

        $query = Follow::where('follower_id', $me->id)->where('following_id', $user->id);

        if ($query->exists()) {
            $query->delete();
            $me->decrement('following_count');
            $user->decrement('follower_count');
            $status = 'unfollowed';
        } else {
            Follow::create([
                'follower_id' => $me->id,
                'following_id' => $user->id,
            ]);
            $me->increment('following_count');
            $user->increment('follower_count');
            $status = 'followed';

            // Tạo thông báo
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'actor_id' => $me->id,
                'type' => 'follow',
            ]);

            // Kiểm tra mutual follow → tự động tạo cuộc hội thoại trực tiếp
            $isMutual = Follow::where('follower_id', $user->id)
                ->where('following_id', $me->id)
                ->exists();

            if ($isMutual) {
                ConversationController::findOrCreateDirect($me->id, $user->id);
            }
        }

        // Xóa cache liên quan để dữ liệu mới phản ánh ngay
        Cache::forget("user_{$me->id}_following");
        Cache::forget("user_{$me->id}_follower_ids");
        Cache::forget("user_{$me->id}_friends");
        Cache::forget("user_{$me->id}_friend_ids");
        Cache::forget("user_{$user->id}_follower_ids");
        Cache::forget("user_{$user->id}_friends");
        Cache::forget("user_{$user->id}_friend_ids");
        Cache::forget("friend_{$me->id}_{$user->id}");
        Cache::forget("friend_{$user->id}_{$me->id}");
        Cache::forget("unread_notif_{$user->id}");

        return back();
    }
}
