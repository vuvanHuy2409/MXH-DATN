<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Bật hoặc tắt trạng thái Like cho một bài viết.
     */
    public function toggle(Post $post)
    {
        $userId = Auth::id();
        $query = Like::where('user_id', $userId)->where('post_id', $post->id);

        if ($query->exists()) {
            $query->delete();
            $post->decrement('like_count');
            $status = 'unliked';
        } else {
            Like::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
            $post->increment('like_count');
            $status = 'liked';

            // Tạo thông báo (Chỉ tạo nếu người like không phải là chủ bài viết)
            if ($post->user_id !== $userId) {
                Notification::create([
                    'user_id' => $post->user_id,
                    'actor_id' => $userId,
                    'type' => 'like',
                    'post_id' => $post->id,
                ]);
            }
        }

        if (request()->wantsJson()) {
            return response()->json(['status' => $status, 'count' => $post->like_count]);
        }

        return back();
    }
}
