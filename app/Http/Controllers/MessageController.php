<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $user = auth()->user();
        // Check if user is participant
        if (!$conversation->users()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        // Kiểm tra quan hệ bạn bè nếu là chat 1-1
        if ($conversation->type === 'direct') {
            $otherUser = $conversation->users()->where('users.id', '!=', $user->id)->first();
            if ($otherUser) {
                $isFriend = $user->following()->where('users.id', $otherUser->id)->exists() && 
                           $user->followers()->where('users.id', $otherUser->id)->exists();
                
                if (!$isFriend) {
                    return response()->json([
                        'error' => 'Người này đã hủy theo dõi bạn hoặc bạn đã hủy theo dõi họ. Bạn không thể gửi tin nhắn.'
                    ], 403);
                }
            }
        }

        $request->validate([
            'content' => 'nullable|string|max:1000',
            'message_type' => 'nullable|in:text,image,video,file,call_log,post_share',
            'image' => 'nullable|image|max:10240',
            'video' => 'nullable|mimetypes:video/mp4,video/quicktime|max:20480',
            'file' => 'nullable|file|max:51200',
            'metadata' => 'nullable|array',
        ]);

        $messageType = $request->message_type ?? 'text';
        $content = $request->content;
        $metadata = $request->metadata;

        // Handle File Uploads
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('messages/images', 'public');
            $content = 'storage/' . $path;
            $messageType = 'image';
        } elseif ($request->hasFile('video')) {
            $path = $request->file('video')->store('messages/videos', 'public');
            $content = 'storage/' . $path;
            $messageType = 'video';
        } elseif ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('messages/files', 'public');
            $content = 'storage/' . $path;
            $messageType = 'file';
            $metadata = [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ];
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'parent_id' => $request->parent_id,
            'sender_id' => auth()->id(),
            'message_type' => $messageType,
            'content' => $content,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);

        // Update conversation last message id
        $conversation->update([
            'last_message_id' => $message->id,
        ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($message->load('sender'));
        }

        return back();
    }
}
