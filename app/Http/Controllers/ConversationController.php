<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'all'); // all, friends, groups, unread

        $query = $user->conversations()
            ->with(['lastMessage.sender', 'users' => function ($query) use ($user) {
                $query->where('users.id', '!=', $user->id);
            }])
            ->orderBy('updated_at', 'desc');

        if ($filter === 'friends') {
            $query->where('type', 'direct');
        } elseif ($filter === 'groups') {
            $query->where('type', 'group');
        } elseif ($filter === 'unread') {
            $query->whereHas('lastMessage', function($q) use ($user) {
                $q->where('is_read', false)->where('sender_id', '!=', $user->id);
            });
        }

        $conversations = $query->get();
        
        // Chỉ lấy bạn bè thực sự (theo dõi lẫn nhau)
        $friends = $user->following()
            ->whereIn('users.id', $user->followers()->pluck('users.id'))
            ->get();

        return view('messages.index', compact('conversations', 'friends', 'filter'));
    }

    public function show(Conversation $conversation, Request $request)
    {
        $user = auth()->user();
        if (!$conversation->users()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        $filter = $request->query('filter', 'all');
        $conversations = $user->conversations()
            ->with(['lastMessage.sender', 'users' => function ($query) use ($user) {
                $query->where('users.id', '!=', $user->id);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Kiểm tra xem có còn là bạn bè không nếu là chat 1-1
        $isFriend = true;
        $otherUser = null;
        if ($conversation->type === 'direct') {
            $otherUser = $conversation->users()->where('users.id', '!=', $user->id)->first();
            if ($otherUser) {
                $isFriend = $user->following()->where('users.id', $otherUser->id)->exists() && 
                           $user->followers()->where('users.id', $otherUser->id)->exists();
            }
        }

        $conversation->messages()->where('sender_id', '!=', $user->id)->where('is_read', false)->update(['is_read' => true]);

        if ($conversation->type === 'group' && !$conversation->join_code) {
            $conversation->update(['join_code' => Conversation::generateUniqueJoinCode()]);
        }

        $messages = $conversation->messages()->with(['sender', 'parent.sender'])->oldest()->get();

        return view('messages.show', compact('conversation', 'messages', 'otherUser', 'isFriend', 'conversations', 'filter'));
    }

    public static function findOrCreateDirect($userId1, $userId2)
    {
        // Kiểm tra mutual follow trước khi tạo/tìm
        $user1 = User::find($userId1);
        $isMutual = $user1->following()->where('users.id', $userId2)->exists() && 
                    $user1->followers()->where('users.id', $userId2)->exists();
        
        if (!$isMutual) return null;

        $conversation = Conversation::where('type', 'direct')
            ->whereHas('participants', function ($query) use ($userId1) {
                $query->where('user_id', $userId1);
            })
            ->whereHas('participants', function ($query) use ($userId2) {
                $query->where('user_id', $userId2);
            })
            ->first();

        if (!$conversation) {
            DB::transaction(function () use (&$conversation, $userId1, $userId2) {
                $conversation = Conversation::create([
                    'type' => 'direct',
                    'updated_at' => now(),
                ]);

                Participant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId1,
                    'role' => 'member',
                    'joined_at' => now(),
                ]);

                Participant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId2,
                    'role' => 'member',
                    'joined_at' => now(),
                ]);
            });
        }

        return $conversation;
    }

    public function startDirect(User $user)
    {
        $conversation = self::findOrCreateDirect(auth()->id(), $user->id);
        if (!$conversation) {
            return back()->with('error', __('Bạn chỉ có thể nhắn tin với người là bạn bè (theo dõi lẫn nhau).'));
        }
        return redirect()->route('messages.show', $conversation->id);
    }

    public function startGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = array_unique(array_filter($request->user_ids, function($id) {
            return $id != auth()->id();
        }));

        $conversation = null;
        DB::transaction(function () use ($request, $userIds, &$conversation) {
            $conversation = Conversation::create([
                'type' => 'group',
                'name' => $request->name,
                'join_code' => Conversation::generateUniqueJoinCode(),
                'creator_id' => auth()->id(),
            ]);

            Participant::create([
                'conversation_id' => $conversation->id,
                'user_id' => auth()->id(),
                'role' => 'admin',
                'status' => 'active',
                'joined_at' => now(),
            ]);

            foreach ($userIds as $userId) {
                Participant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                    'role' => 'member',
                    'status' => 'active',
                    'joined_at' => now(),
                ]);
            }
        });

        return redirect()->route('messages.show', $conversation->id);
    }

    public function toggleMute(Conversation $conversation)
    {
        $participant = Participant::where('conversation_id', $conversation->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $participant->update(['is_muted' => !$participant->is_muted]);
        return response()->json(['is_muted' => $participant->is_muted]);
    }

    public function leaveGroup(Conversation $conversation)
    {
        if ($conversation->type !== 'group') abort(403);
        
        $user = auth()->user();
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message_type' => 'system',
            'content' => "👋 **{$user->username}** đã rời khỏi nhóm.",
        ]);

        Participant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()->route('messages.index');
    }

    public function kickMember(Conversation $conversation, User $user)
    {
        if ($conversation->creator_id !== auth()->id()) abort(403);
        if ($user->id === auth()->id()) return back();

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'message_type' => 'system',
            'content' => "🚫 **{$user->username}** đã bị quản trị viên mời ra khỏi nhóm.",
        ]);

        Participant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->delete();

        return response()->json(['status' => 'success']);
    }

    public function addMembers(Request $request, Conversation $conversation)
    {
        $request->validate(['user_ids' => 'required|array|min:1']);
        $status = $conversation->requires_approval && $conversation->creator_id !== auth()->id() ? 'pending' : 'active';

        foreach ($request->user_ids as $userId) {
            Participant::updateOrCreate(
                ['conversation_id' => $conversation->id, 'user_id' => $userId],
                ['role' => 'member', 'status' => $status, 'joined_at' => now()]
            );
            
            $u = User::find($userId);
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'message_type' => 'system',
                'content' => "📥 **{$u->username}** đã được thêm vào nhóm.",
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return back();
    }

    public function approveMember(Conversation $conversation, User $user)
    {
        if ($conversation->creator_id !== auth()->id()) abort(403);
        Participant::where('conversation_id', $conversation->id)->where('user_id', $user->id)->update(['status' => 'active']);
        return response()->json(['status' => 'success']);
    }

    public function getMedia(Conversation $conversation)
    {
        $media = $conversation->messages()
            ->where(function($query) {
                $query->whereIn('message_type', ['image', 'video', 'file', 'link'])
                      ->orWhere(function($q) {
                          $q->where('message_type', 'text')
                            ->where(function($inner) {
                                $inner->where('content', 'like', '%http%')
                                      ->orWhere('content', 'like', '%.com%')
                                      ->orWhere('content', 'like', '%.vn%')
                                      ->orWhere('content', 'like', '%.net%')
                                      ->orWhere('content', 'like', '%.org%')
                                      ->orWhere('content', 'like', '%.edu%')
                                      ->orWhere('content', 'like', '%.gov%');
                            });
                      });
            })
            ->latest()
            ->get();
            
        return response()->json($media);
    }

    public function searchMessages(Request $request, Conversation $conversation)
    {
        $q = $request->query('q');
        $messages = $conversation->messages()->with('sender')->where('content', 'like', "%$q%")->latest()->get();
        return response()->json($messages);
    }

    public function updateGroupAvatar(Request $request, Conversation $conversation)
    {
        if ($conversation->creator_id !== auth()->id()) abort(403);
        $request->validate(['avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120']);
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('groups/avatars', 'public');
            $conversation->update(['avatar_url' => '/storage/' . $path]);
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'message_type' => 'system',
                'content' => "🖼️ **" . auth()->user()->username . "** đã thay đổi ảnh đại diện nhóm.",
            ]);
        }
        return back();
    }

    public function updateName(Request $request, Conversation $conversation)
    {
        if ($conversation->creator_id !== auth()->id()) abort(403);
        $request->validate(['name' => 'required|string|max:100']);
        $oldName = $conversation->name;
        $conversation->update(['name' => $request->name]);
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'message_type' => 'system',
            'content' => "✏️ **" . auth()->user()->username . "** đã đổi tên nhóm từ \"$oldName\" thành \"{$request->name}\".",
        ]);
        return back();
    }

    public function updateThemeColor(Request $request, Conversation $conversation)
    {
        $request->validate(['color' => 'required|string|max:7']);
        $conversation->update(['theme_color' => $request->color]);
        return response()->json(['status' => 'success', 'color' => $request->color]);
    }

    public function togglePin(Conversation $conversation, Message $message)
    {
        if ($message->conversation_id !== $conversation->id) abort(403);
        $message->update(['is_pinned' => !$message->is_pinned]);
        return response()->json(['status' => 'success', 'is_pinned' => $message->is_pinned]);
    }

    public function createAppointment(Request $request, Conversation $conversation)
    {
        $request->validate(['title' => 'required|string|max:255', 'appointment_time' => 'required|date', 'location' => 'nullable|string|max:255']);
        $appointment = \App\Models\Appointment::create(['conversation_id' => $conversation->id, 'creator_id' => auth()->id(), 'title' => $request->title, 'appointment_time' => $request->appointment_time, 'location' => $request->location]);
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'message_type' => 'system',
            'content' => "📅 Đã tạo lịch hẹn: **{$request->title}** lúc " . date('H:i d/m', strtotime($request->appointment_time)),
        ]);
        return response()->json(['status' => 'success', 'appointment' => $appointment]);
    }

    public function getAppointments(Conversation $conversation)
    {
        $appointments = $conversation->appointments()->where('status', 'upcoming')->latest()->get();
        return response()->json($appointments);
    }

    public function deleteMessage(Conversation $conversation, Message $message)
    {
        if ($message->conversation_id !== $conversation->id) abort(403);
        if ($message->sender_id !== auth()->id() && $conversation->creator_id !== auth()->id()) abort(403);
        $message->delete();
        return response()->json(['status' => 'success']);
    }

    public function deleteChat(Conversation $conversation)
    {
        $user = auth()->user();
        if ($conversation->type === 'group') {
            Message::create(['conversation_id' => $conversation->id, 'sender_id' => $user->id, 'message_type' => 'system', 'content' => "👋 **{$user->username}** đã rời khỏi nhóm."]);
        }
        Participant::where('conversation_id', $conversation->id)->where('user_id', $user->id)->delete();
        return response()->json(['status' => 'success']);
    }

    public function searchFriends(Request $request)
    {
        $user = auth()->user();
        $query = $request->get('q', '');
        
        // Tìm kiếm chỉ trong danh sách bạn bè (mutual follow)
        $friends = $user->following()
            ->whereIn('users.id', $user->followers()->pluck('users.id'))
            ->where(function($q) use ($query) {
                $q->where('username', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->select('users.id', 'username', 'avatar_url')
            ->limit(20)
            ->get();
            
        return response()->json($friends);
    }

    public function apiStartDirect(User $user)
    {
        $conversation = self::findOrCreateDirect(auth()->id(), $user->id);
        if (!$conversation) {
            return response()->json(['error' => __('Bạn chỉ có thể nhắn tin với người là bạn bè.')], 403);
        }
        $messages = $conversation->messages()->with('sender')->oldest()->get();
        return response()->json(['conversation_id' => $conversation->id, 'messages' => $messages]);
    }

    public function apiMessages(Conversation $conversation)
    {
        if (!$conversation->users()->where('users.id', auth()->id())->exists()) return response()->json(['error' => 'Forbidden'], 403);
        $messages = $conversation->messages()->with('sender')->oldest()->get();
        return response()->json($messages);
    }

    public function joinByCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:5']);
        $code = strtoupper($request->code);
        $conversation = Conversation::where('type', 'group')->where('join_code', $code)->first();
        if (!$conversation) return back()->withErrors(['code' => __('Mã nhóm không tồn tại.')]);
        $exists = Participant::where('conversation_id', $conversation->id)->where('user_id', auth()->id())->exists();
        if ($exists) return redirect()->route('messages.show', $conversation->id);
        Participant::create(['conversation_id' => $conversation->id, 'user_id' => auth()->id(), 'role' => 'member', 'status' => 'active', 'joined_at' => now()]);
        Message::create(['conversation_id' => $conversation->id, 'sender_id' => auth()->id(), 'message_type' => 'system', 'content' => "👋 **" . auth()->user()->username . "** đã gia nhập nhóm bằng mã mời."]);
        return redirect()->route('messages.show', $conversation->id);
    }

    public function refreshJoinCode(Conversation $conversation)
    {
        if ($conversation->creator_id !== auth()->id()) abort(403);
        $conversation->update(['join_code' => Conversation::generateUniqueJoinCode()]);
        return back();
    }

    public function getMembers(Conversation $conversation)
    {
        if (!$conversation->users()->where('users.id', auth()->id())->exists()) {
            abort(403);
        }

        $members = $conversation->users()->withPivot('role', 'status', 'joined_at')->get();
        return response()->json($members);
    }
}
