<?php

namespace App\Http\Controllers;

use App\Models\Repost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepostController extends Controller
{
    /**
     * Bật hoặc tắt trạng thái Repost cho một bài viết.
     */
    public function toggle(Post $post)
    {
        $userId = Auth::id();
        $query = Repost::where('user_id', $userId)->where('post_id', $post->id);

        if ($query->exists()) {
            $query->delete();
            $status = 'unreposted';
        } else {
            Repost::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
            $status = 'reposted';

            // Tạo thông báo
            if ($post->user_id !== $userId) {
                \App\Models\Notification::create([
                    'user_id' => $post->user_id,
                    'actor_id' => $userId,
                    'type' => 'repost',
                    'post_id' => $post->id,
                ]);
            }
        }

        // Lấy lại số lượng repost mới nhất
        $count = Repost::where('post_id', $post->id)->count();

        return response()->json([
            'status' => $status,
            'count' => $count
        ]);
    }
}
