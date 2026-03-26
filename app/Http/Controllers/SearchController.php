<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $users = collect();
        $posts = collect();
        $me = Auth::user();
        $meFollowerIds = $me->followers()->pluck('follower_id')->toArray();

        if ($query) {
            // Tìm người dùng
            if ($type === 'all' || $type === 'people') {
                $users = User::where(function($q) use ($query) {
                    $q->where('username', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhereHas('student', function($sq) use ($query) {
                          $sq->where('full_name', 'LIKE', "%{$query}%")
                             ->orWhere('student_id', 'LIKE', "%{$query}%");
                      })
                      ->orWhereHas('teacher', function($tq) use ($query) {
                          $tq->where('full_name', 'LIKE', "%{$query}%");
                      });
                })
                ->where('id', '!=', $me->id)
                ->with(['student', 'teacher'])
                ->withCount(['followers'])
                ->limit(20)
                ->get();

                foreach($users as $u) {
                    $u->follows_me = in_array($u->id, $meFollowerIds);
                }
            }

            // Tìm bài viết
            if ($type === 'all' || $type === 'posts') {
                $posts = Post::with(['user', 'media', 'group'])
                    ->withCount(['likes', 'reposts'])
                    ->where('content', 'LIKE', "%{$query}%")
                    ->latest()
                    ->limit(20)
                    ->get();
            }
        }

        // Gợi ý đơn giản: Lấy 8 người mình chưa theo dõi
        $followingIds = $me->following()->pluck('following_id')->toArray();
        $followingIds[] = $me->id;
        $suggestions = User::whereNotIn('id', $followingIds)
            ->withCount(['followers'])
            ->inRandomOrder()
            ->limit(8)
            ->get();
        
        foreach($suggestions as $s) {
            $s->follows_me = in_array($s->id, $meFollowerIds);
        }

        return view('search.index', compact('users', 'posts', 'suggestions', 'query', 'type'));
    }

    public function suggestions()
    {
        return response()->json([]);
    }
}
