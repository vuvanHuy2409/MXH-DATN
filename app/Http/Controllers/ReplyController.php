<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    /**
     * Store a reply (comment) for a post.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|max:500',
            // parent_id có thể null (bình luận bài viết) hoặc phải tồn tại trong bảng comments (trả lời bình luận)
            'parent_id' => 'nullable' 
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'parent_id' => $request->parent_id ?: null, // Đảm bảo lưu null nếu trống
            'content' => $request->content,
        ]);

        // Tăng số lượng bình luận cho bài viết gốc
        $post->increment('reply_count');

        // Nếu là trả lời cho một bình luận khác, tăng count cho bình luận đó
        if ($request->parent_id) {
            Comment::where('id', $request->parent_id)->increment('reply_count');
        }

        // Thông báo
        if ($post->user_id !== Auth::id()) {
            \App\Models\Notification::create([
                'user_id' => $post->user_id,
                'actor_id' => Auth::id(),
                'type' => 'reply',
                'post_id' => $post->id,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $comment->load('user');
            return response()->json($comment);
        }

        return back();
    }
}
