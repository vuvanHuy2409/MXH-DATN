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
        // Kiểm tra cơ bản
        $request->validate([
            'content' => 'nullable|max:500',
            'parent_id' => 'nullable',
            'image' => 'nullable|image|max:10240',
        ]);

        // Kiểm tra ít nhất có 1 trong 2
        if (!$request->filled('content') && !$request->hasFile('image')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Vui lòng nhập nội dung hoặc chọn ảnh'], 422);
            }
            return back()->with('error', 'Vui lòng nhập nội dung hoặc chọn ảnh');
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('comments/images', 'public');
            $imageUrl = '/storage/' . $path;
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'parent_id' => $request->parent_id ?: null,
            'content' => $request->content ?? '',
            'image_url' => $imageUrl,
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
