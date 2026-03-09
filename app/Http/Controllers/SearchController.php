<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Hiển thị trang tìm kiếm và gợi ý người dùng.
     */
    public function index(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all'); // 'all', 'people', 'posts'
        $users = collect();
        $posts = collect();

        if ($query) {
            if ($type === 'all' || $type === 'people') {
                // Tìm kiếm người dùng theo username
                $users = User::where('username', 'LIKE', "%{$query}%")
                    ->where('id', '!=', Auth::id())
                    ->withCount(['followers'])
                    ->limit(20)
                    ->get();
            }

            if ($type === 'all' || $type === 'posts') {
                // Tìm kiếm bài viết theo nội dung
                $posts = Post::with(['user', 'media'])
                    ->withCount(['likes', 'reposts'])
                    ->where('content', 'LIKE', "%{$query}%")
                    ->latest()
                    ->limit(20)
                    ->get();
            }
        }

        // Gợi ý người dùng (những người mà mình chưa theo dõi)
        $me = Auth::user();
        $followingIds = $me->following()->pluck('following_id')->toArray();
        $followingIds[] = $me->id;

        $suggestions = User::whereNotIn('id', $followingIds)
            ->withCount(['followers'])
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('search.index', compact('users', 'posts', 'suggestions', 'query', 'type'));
    }
}
