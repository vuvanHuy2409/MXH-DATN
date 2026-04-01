<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\GroupMember;
use App\Models\Notification;
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
        // Kiểm tra quyền thành viên nếu bài viết thuộc về một nhóm
        if ($post->group_id) {
            $isMember = GroupMember::where('group_id', $post->group_id)
                ->where('user_id', Auth::id())
                ->exists();
            
            if (!$isMember) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Bạn cần tham gia cộng đồng này để có thể bình luận.'], 403);
                }
                return back()->with('error', 'Bạn cần tham gia cộng đồng này để có thể bình luận.');
            }
        }

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

        $parentComment = null;
        // Nếu là trả lời cho một bình luận khác, tăng count cho bình luận đó
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if ($parentComment) {
                $parentComment->increment('reply_count');
            }
        }

        // --- HỆ THỐNG THÔNG BÁO ---
        $authId = Auth::id();

        // 1. Thông báo cho chủ bài viết (nếu không phải chính họ đăng bình luận)
        if ($post->user_id !== $authId) {
            Notification::create([
                'user_id' => $post->user_id,
                'actor_id' => $authId,
                'type' => 'reply',
                'post_id' => $post->id,
            ]);
        }

        // 2. Thông báo cho chủ bình luận cha (nếu có bình luận cha và không phải chính họ trả lời)
        if ($parentComment && $parentComment->user_id !== $authId && $parentComment->user_id !== $post->user_id) {
            Notification::create([
                'user_id' => $parentComment->user_id,
                'actor_id' => $authId,
                'type' => 'reply',
                'post_id' => $post->id,
            ]);
        }
        // --- KẾT THÚC THÔNG BÁO ---

        if ($request->ajax() || $request->wantsJson()) {
            $comment->load('user');
            return response()->json($comment);
        }

        return back();
    }
}
