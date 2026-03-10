<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the posts (Timeline).
     */
    public function index()
    {
        $userId = auth()->id();
        $followingIds = auth()->user()->following()->pluck('users.id')->toArray();

        // 1. DÀNH CHO BẠN: Lấy tất cả bài viết gốc không thuộc nhóm
        $posts = Post::whereNull('group_id')
            ->with(['user', 'media', 'likes'])
            ->withCount(['reposts'])
            ->orderByRaw('user_id = ? DESC', [$userId]) // Ưu tiên bài viết của mình lên đầu
            ->orderByRaw('DATE(created_at) DESC, RAND()') // Giữ nguyên xắp xếp cũ cho các bài viết khác
            ->get();

        // Thêm thông tin người theo dõi đã repost cho tab Dành cho bạn
        foreach ($posts as $post) {
            $post->followed_reposters = \App\Models\Repost::where('post_id', $post->id)
                ->whereIn('user_id', $followingIds)
                ->with('user')
                ->get()
                ->pluck('user.username')
                ->toArray();
        }

        // 2. ĐANG THEO DÕI: 
        // Lấy IDs của các bài viết từ người đang theo dõi
        $followingOriginalPostIds = Post::whereIn('user_id', $followingIds)
            ->pluck('id')
            ->toArray();

        // Lấy IDs của các bài viết được repost bởi người đang theo dõi
        $repostedPostIds = \App\Models\Repost::whereIn('user_id', $followingIds)
            ->pluck('post_id')
            ->toArray();

        // Gộp tất cả IDs bài viết cần hiển thị
        $allFollowingPostIds = array_unique(array_merge($followingOriginalPostIds, $repostedPostIds));

        $followingPosts = Post::whereIn('id', $allFollowingPostIds)
            ->whereNull('group_id')
            ->with(['user', 'media', 'likes'])
            ->withCount(['reposts'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Gắn danh sách người theo dõi đã repost vào từng bài
        foreach ($followingPosts as $post) {
            $post->followed_reposters = \App\Models\Repost::where('post_id', $post->id)
                ->whereIn('user_id', $followingIds)
                ->with('user')
                ->get()
                ->pluck('user.username')
                ->toArray();
        }

        return view('posts.index', compact('posts', 'followingPosts'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');
        
        try {
            $request->validate([
                'content' => 'required|max:500',
                'link_url' => 'nullable', // Bớt gắt gao URL validation
                'group_id' => 'nullable|exists:social_groups,id',
                'media.*' => 'nullable|file|max:102400', // Tăng max size lên 100MB
            ]);

            $post = Post::create([
                'user_id' => auth()->id(),
                'group_id' => $request->filled('group_id') ? $request->group_id : null,
                'content' => $request->content,
                'link_url' => $request->link_url,
            ]);

            // Xử lý upload nhiều file (ảnh, gif, file khác)
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());
                    
                    // Xác định loại media
                    if ($extension === 'gif') {
                        $mediaType = 'gif';
                        $path = $file->store('posts/images', 'public');
                    } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'svg'])) {
                        $mediaType = 'image';
                        $path = $file->store('posts/images', 'public');
                    } else {
                        $mediaType = 'file';
                        $path = $file->store('posts/files', 'public');
                    }

                    \App\Models\PostMedia::create([
                        'post_id' => $post->id,
                        'media_url' => '/storage/' . $path,
                        'file_name' => $originalName,
                        'media_type' => $mediaType,
                    ]);
                }
            }

            return redirect()->back()->with('status', 'Bài viết đã được đăng!');
        } catch (\Exception $e) {
            \Log::error("Post creation failed: " . $e->getMessage());
            return redirect()->back()->withErrors(['content' => 'Đã xảy ra lỗi khi đăng bài: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified post (Thread detail).
     */
    public function show(Post $post)
    {
        // Tải bài gốc và tất cả các bình luận để xử lý phân cấp
        $post->load(['user', 'media', 'likes', 'comments' => function($query) {
            $query->with(['user', 'parent.user'])->oldest();
        }]);
        
        return view('posts.show', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|max:500',
        ]);

        $post->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'status' => 'success',
            'content' => $post->content
        ]);
    }

    /**
     * Xóa bài viết (Kèm theo các bình luận liên quan).
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => __('Không có quyền')], 403);
        }

        // Khi xóa Post, các Comment sẽ tự động bị xóa do ràng buộc foreign key (onDelete cascade)
        $post->delete();

        if (request()->ajax()) {
            return response()->json(['status' => 'success']);
        }

        return redirect()->route('home');
    }

    /**
     * Xóa một bình luận cụ thể.
     */
    public function destroyComment($commentId)
    {
        $comment = \App\Models\Comment::findOrFail($commentId);
        
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => __('Không có quyền')], 403);
        }

        $post = \App\Models\Post::find($comment->post_id);
        
        // Tính tổng số mục sẽ bị xóa (chính nó + các phản hồi con)
        $countToDelete = 1 + $comment->replies()->count();

        // Cập nhật lại số lượng reply_count của bài viết
        if ($post) {
            $post->decrement('reply_count', $countToDelete);
        }

        $comment->delete(); 

        return response()->json(['status' => 'success']);
    }
}
