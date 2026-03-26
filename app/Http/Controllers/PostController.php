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
        $user = auth()->user();
        $followingIds = $user->following()->pluck('users.id')->toArray();

        // Lấy danh sách ID các nhóm mà người dùng hiện tại đã tham gia
        $myGroupIds = \App\Models\GroupMember::where('user_id', $userId)->pluck('group_id')->toArray();

        // 1. DÀNH CHO BẠN: Lấy bài viết cá nhân (công khai), nhóm công khai, và nhóm riêng tư mình đã tham gia
        $posts = Post::where(function($query) use ($myGroupIds, $followingIds, $userId) {
                // Bài viết cá nhân
                $query->where(function($q) use ($followingIds, $userId) {
                    $q->whereNull('group_id')
                      ->whereHas('user', function($u) use ($followingIds, $userId) {
                          $u->where('is_private', false)    // Tài khoản công khai
                            ->orWhereIn('id', $followingIds) // Hoặc người mình theo dõi
                            ->orWhere('id', $userId);       // Hoặc chính mình
                      });
                })
                // HOẶC Bài viết trong cộng đồng (Vẫn hiển thị bình thường ngoài trang chủ)
                ->orWhereHas('group', function($q) {
                    $q->where('privacy', 'public'); // Nhóm công khai
                })
                // HOẶC Bài viết trong nhóm mình đã tham gia
                ->orWhereIn('group_id', $myGroupIds);
            })
            ->with(['user', 'media', 'likes', 'group', 'reposts' => function($query) use ($followingIds) {
                $query->whereIn('user_id', $followingIds)->with('user');
            }])
            ->withCount(['reposts'])
            ->orderByRaw('user_id = ? DESC', [$userId]) 
            ->orderBy('created_at', 'DESC') 
            ->limit(50) 
            ->get();

        // 2. ĐANG THEO DÕI: 
        $repostedPostIds = \App\Models\Repost::whereIn('user_id', $followingIds)
            ->pluck('post_id')
            ->toArray();

        $followingPosts = Post::where(function($query) use ($followingIds, $repostedPostIds, $myGroupIds) {
                $query->whereIn('user_id', $followingIds) // Người mình theo dõi
                      ->orWhereIn('id', $repostedPostIds) // Bài được repost bởi người mình theo dõi
                      ->orWhereIn('group_id', $myGroupIds); // Bài trong nhóm mình tham gia
            })
            ->with(['user', 'media', 'likes', 'group', 'reposts' => function($query) use ($followingIds) {
                $query->whereIn('user_id', $followingIds)->with('user');
            }])
            ->withCount(['reposts'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $meFollowerIds = auth()->user()->followers()->pluck('follower_id')->toArray();
        foreach ($posts as $post) {
            $post->user->follows_me = in_array($post->user_id, $meFollowerIds);
        }

        foreach ($followingPosts as $post) {
            $post->followed_reposters = $post->reposts->pluck('user.username')->toArray();
            $post->user->follows_me = in_array($post->user_id, $meFollowerIds);
        }

        return view('posts.index', compact('posts', 'followingPosts'));
    }

    /**
     * Get comments for a post in chronological order.
     */
    public function getComments(Post $post)
    {
        $comments = $post->comments()
            ->with(['user'])
            ->oldest()
            ->get();
            
        return response()->json($comments);
    }

    public function getLikes(Post $post)
    {
        $likes = $post->likes()->with('user')->get()->pluck('user');
        return response()->json($likes);
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
        $post->load(['user', 'media', 'likes', 'group', 'comments' => function($query) {
            $query->with(['user', 'parent.user'])->oldest();
        }]);

        $commentsByParent = $post->comments->groupBy('parent_id');
        $rootComments = $commentsByParent->get(null) ?? collect();

        foreach ($rootComments as $root) {
            $allFlatReplies = [];
            $visited = [];
            $this->collectReplies($root->id, $commentsByParent, $allFlatReplies, $visited);
            $root->all_flat_replies = $allFlatReplies;
        }
        
        return view('posts.show', compact('post', 'rootComments'));
    }

    /**
     * Hàm hỗ trợ đệ quy an toàn để gom bình luận
     */
    private function collectReplies($parentId, $commentsByParent, &$allReplies, &$visited)
    {
        if (isset($visited[$parentId]) || count($allReplies) > 500) return;
        $visited[$parentId] = true;

        $replies = $commentsByParent->get($parentId) ?? [];
        foreach ($replies as $reply) {
            $allReplies[] = $reply;
            $this->collectReplies($reply->id, $commentsByParent, $allReplies, $visited);
        }
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
        $canDelete = $post->user_id === auth()->id();

        // Nếu bài viết thuộc về một nhóm, cho phép admin của nhóm xóa
        if (!$canDelete && $post->group_id && $post->group) {
            if ($post->group->isAdmin(auth()->id())) {
                $canDelete = true;
            }
        }

        if (!$canDelete) {
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
