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
        $notifications = Notification::with(['actor', 'post'])
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function apiIndex()
    {
        $notifications = Notification::with(['actor', 'post'])
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->get()
            ->map(function($n) {
                $n->created_at_human = $n->created_at->diffForHumans();
                return $n;
            });

        return response()->json($notifications);
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
