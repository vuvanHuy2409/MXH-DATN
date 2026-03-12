<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Hiển thị danh sách thông báo của người dùng.
     */
    public function index()
    {
        $rawNotifications = Notification::with(['actor', 'post.group'])
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->get();

        $notifications = $this->aggregateNotifications($rawNotifications);

        return view('notifications.index', compact('notifications'));
    }

    public function apiIndex()
    {
        $rawNotifications = Notification::with(['actor', 'post.group'])
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->get();

        $notifications = $this->aggregateNotifications($rawNotifications)
            ->map(function($n) {
                $n->created_at_human = $n->created_at->diffForHumans();
                return $n;
            });

        return response()->json($notifications);
    }

    /**
     * Gộp các thông báo trùng lặp (ví dụ: nhiều người cùng like 1 bài viết).
     */
    private function aggregateNotifications($notifications)
    {
        // Nhóm theo loại và bài viết (nếu có)
        // Lưu ý: Chỉ gộp các thông báo gần nhau (hoặc tất cả tùy logic, ở đây chọn gộp tất cả chưa đọc hoặc trong cùng danh sách hiện tại)
        $grouped = $notifications->groupBy(function ($item) {
            return $item->type . '_' . ($item->post_id ?? '0');
        });

        return $grouped->map(function ($group) {
            $latest = $group->first(); // Thông báo mới nhất trong nhóm
            
            // Lấy danh sách các actor (người thực hiện) khác nhau
            $actors = $group->pluck('actor')->unique('id');
            
            $latest->aggregate_actors = $actors;
            $latest->aggregate_count = $actors->count();
            
            // Nếu có ít nhất 1 thông báo trong nhóm chưa đọc, coi như cả nhóm chưa đọc
            $latest->is_read = $group->every('is_read', true);
            
            return $latest;
        })->values()->sortByDesc('id');
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }

    public function unreadCounts()
    {
        $userId = Auth::id();
        
        $unreadNotifCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
        
        $unreadMsgCount = \App\Models\Message::whereIn('conversation_id', function($query) use ($userId) {
            $query->select('conversation_id')
                  ->from('participants')
                  ->where('user_id', $userId);
        })
        ->where('sender_id', '!=', $userId)
        ->where('is_read', false)
        ->count();

        return response()->json([
            'unreadNotificationsCount' => $unreadNotifCount,
            'unreadMessagesCount' => $unreadMsgCount,
        ]);
    }
}
