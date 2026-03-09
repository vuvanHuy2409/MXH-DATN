@extends('layouts.app')

@section('content')
<style>
    /* Mở rộng container cho trang tin nhắn */
    .container {
        max-width: 1100px !important;
    }

    .chat-layout {
        display: flex;
        height: calc(100vh - 100px);
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        overflow: hidden;
        margin: 20px 0;
        box-shadow: var(--glass-shadow);
    }

    /* Sidebar danh sách hội thoại */
    .chat-sidebar {
        width: 350px;
        border-right: 1px solid var(--glass-border);
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.05);
    }

    .chat-sidebar-header {
        padding: 25px 20px 15px;
    }

    .conversation-list-container {
        flex-grow: 1;
        overflow-y: auto;
    }

    /* Vùng chat chính */
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: transparent;
        position: relative;
    }

    .conversation-item-sidebar {
        padding: 15px 20px;
        display: flex;
        gap: 12px;
        align-items: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }

    .conversation-item-sidebar:hover {
        background: rgba(0, 0, 0, 0.03);
    }

    .conversation-item-sidebar.active {
        background: rgba(0, 113, 227, 0.08);
        border-left-color: var(--accent-color);
    }

    /* Responsive */
    @media (max-width: 900px) {
        .chat-sidebar {
            display: none; /* Ẩn sidebar trên mobile khi đang xem chat */
        }
        .container {
            max-width: 650px !important;
        }
    }

    .filter-tab-mini {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 15px;
        background: rgba(0,0,0,0.05);
        cursor: pointer;
        font-weight: 700;
    }
    
    .filter-tab-mini.active {
        background: var(--text-color);
        color: white;
    }
</style>

<div class="chat-layout">
    <!-- Cột trái: Danh sách hội thoại -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 800;">Tin nhắn</h2>
                <button onclick="openGroupModal()" style="background: none; border: none; cursor: pointer; color: var(--accent-color);">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </button>
            </div>
            
            <!-- Search Mini -->
            <div style="position: relative; margin-bottom: 15px;">
                <input type="text" placeholder="Tìm kiếm..." style="width: 100%; padding: 10px 15px 10px 35px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-size: 14px; outline: none;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
        </div>

        <div class="conversation-list-container">
            @foreach($conversations as $conv)
                @php
                    $convIsGroup = $conv->type === 'group';
                    $convName = $conv->name;
                    $convAvatar = $conv->avatar_url;
                    if (!$convIsGroup) {
                        $other = $conv->users->where('id', '!=', auth()->id())->first();
                        $convName = $other ? $other->username : 'Người dùng';
                        $convAvatar = $other ? $other->avatar_url : null;
                    }
                    $isCurrent = $conv->id === $conversation->id;
                    $convUnread = $conv->lastMessage && !$conv->lastMessage->is_read && $conv->lastMessage->sender_id !== auth()->id();
                @endphp
                <a href="{{ route('messages.show', $conv->id) }}" class="conversation-item-sidebar {{ $isCurrent ? 'active' : '' }}">
                    <div style="position: relative; flex-shrink: 0;">
                        <div class="avatar" style="background-image: url('{{ $convAvatar ?? asset('default-avatar.png') }}'); background-size: cover; width: 48px; height: 48px; border: {{ $convIsGroup ? '1.5px solid var(--accent-color)' : 'none' }}"></div>
                        @if(!$convIsGroup && $other && $other->is_online)
                            <div style="position: absolute; bottom: 1px; right: 1px; width: 12px; height: 12px; background: #34c759; border: 2px solid white; border-radius: 50%;"></div>
                        @endif
                    </div>
                    <div style="flex-grow: 1; overflow: hidden;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <span style="font-weight: 700; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%;">{{ $convName }}</span>
                            <span style="font-size: 11px; opacity: 0.6;">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
                        </div>
                        <div style="font-size: 13px; color: var(--secondary-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; font-weight: {{ $convUnread ? '700' : '400' }}">
                            {{ $conv->lastMessage ? ($conv->lastMessage->sender_id === auth()->id() ? 'Bạn: ' : '') . ($conv->lastMessage->message_type === 'text' ? $conv->lastMessage->content : 'Đã gửi một file') : 'Bắt đầu trò chuyện...' }}
                        </div>
                    </div>
                    @if($convUnread)
                        <div style="width: 8px; height: 8px; background: var(--accent-color); border-radius: 50%; flex-shrink: 0;"></div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Cột phải: Nội dung chat -->
    <div class="chat-main">
        @php
            $themeColor = $conversation->theme_color ?? '#0071e3';
        @endphp
        
        <!-- Chat Header -->
        <div style="padding: 15px 20px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; gap: 15px; background: rgba(255,255,255,0.02);">
            <a href="{{ route('messages.index') }}" class="mobile-only" style="text-decoration: none; color: inherit; opacity: 0.7; display: none;">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <div class="avatar" style="width: 40px; height: 40px; background-image: url('{{ $conversation->type === 'direct' ? ($otherUser->avatar_url ?? '') : ($conversation->avatar_url ?? '') }}'); background-size: cover; border: 2px solid {{ $themeColor }};"></div>
            <div style="flex-grow: 1;">
                <div style="font-weight: 800; font-size: 16px;">{{ $conversation->type === 'direct' ? ($otherUser->username ?? 'Người dùng') : ($conversation->name ?? 'Nhóm') }}</div>
                <div style="font-size: 12px; color: var(--secondary-text); font-weight: 500;">
                    {{ $conversation->type === 'group' ? $conversation->users()->count() . ' thành viên' : 'Đang hoạt động' }}
                </div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <div onclick="toggleSearch()" style="cursor: pointer; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.03);">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div onclick="openGroupSettingsModal()" style="cursor: pointer; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.03);">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Integrated Search Panel -->
        <div id="msgSearchPanel" style="display: none; padding: 10px 20px; background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--glass-border);">
            <div style="position: relative;">
                <input type="text" id="msgSearchInput" placeholder="Tìm tin nhắn..." 
                       style="width: 100%; border-radius: 10px; padding: 8px 35px 8px 12px; background: white; border: 1px solid var(--glass-border); outline:none; font-size: 14px;">
                <div onclick="toggleSearch()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; opacity: 0.5;">&times;</div>
            </div>
            <div id="msgSearchResults" class="glass-bubble" style="display: none; margin-top: 10px; max-height: 300px; overflow-y: auto; border-radius: 12px; z-index: 2000; position: absolute; left: 20px; right: 20px;"></div>
        </div>

        <!-- Messages Container -->
        <div id="messages-container" style="flex-grow: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px;">
            @foreach($messages as $message)
                @php
                    $isMe = $message->sender_id === auth()->id();
                    $isMedia = in_array($message->message_type, ['image', 'video']);
                    $isSystem = $message->message_type === 'system';
                @endphp

                @if($isSystem)
                    <div style="align-self: center; padding: 6px 15px; background: rgba(0,0,0,0.04); border-radius: 12px; font-size: 12px; color: var(--secondary-text); margin: 5px 0;">
                        {!! $message->content !!}
                    </div>
                @else
                    <div class="message-wrapper {{ $isMe ? 'me' : 'other' }}" id="msg-{{ $message->id }}" style="display: flex; gap: 8px; flex-direction: {{ $isMe ? 'row-reverse' : 'row' }}; max-width: 85%; align-self: {{ $isMe ? 'flex-end' : 'flex-start' }};">
                        @if(!$isMe)
                            <div class="avatar" style="width: 28px; height: 28px; background-image: url('{{ $message->sender->avatar_url }}'); background-size: cover; align-self: flex-end; flex-shrink: 0;"></div>
                        @endif
                        
                        <div style="display: flex; flex-direction: column; align-items: {{ $isMe ? 'flex-end' : 'flex-start' }};">
                            <div class="{{ $isMedia ? '' : 'bubble' }}" style="{{ $isMe && !$isMedia ? 'background:'.$themeColor.'; color: white;' : (!$isMedia ? 'background: rgba(0,0,0,0.05);' : '') }} border-radius: 18px; padding: {{ $isMedia ? '0' : '10px 16px' }}; font-size: 14.5px; position: relative;">
                                @if($message->message_type === 'image')
                                    <img src="{{ asset($message->content) }}" onclick="openLightbox(this.src)" style="max-width: 250px; border-radius: 15px; cursor: zoom-in;">
                                @elseif($message->message_type === 'video')
                                    <video src="{{ asset($message->content) }}" controls style="max-width: 250px; border-radius: 15px;"></video>
                                @else
                                    {!! nl2br(e($message->content)) !!}
                                @endif
                            </div>
                            <span style="font-size: 10px; opacity: 0.5; margin-top: 4px;">{{ $message->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Input Area -->
        <div style="padding: 15px 20px; border-top: 1px solid var(--glass-border);">
            @if($isFriend || $conversation->type === 'group')
                <form action="{{ route('messages.store', $conversation->id) }}" method="POST" enctype="multipart/form-data" id="chat-form">
                    @csrf
                    <div style="display: flex; gap: 10px; align-items: center; background: rgba(0,0,0,0.03); border-radius: 25px; padding: 5px 15px;">
                        <label style="cursor: pointer; opacity: 0.6;">
                            <input type="file" name="image" accept="image/*" style="display:none" onchange="this.form.submit()">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </label>
                        <textarea id="mainChatInput" name="content" placeholder="Nhập tin nhắn..." rows="1" style="flex-grow: 1; background: transparent; border: none; outline: none; padding: 10px 0; font-size: 14px; resize: none;"></textarea>
                        <button type="submit" style="background: none; border: none; color: {{ $themeColor }}; font-weight: 800; cursor: pointer;">Gửi</button>
                    </div>
                </form>
            @else
                <div style="text-align: center; font-size: 13px; color: #ff3b30; padding: 10px;">Bạn chỉ có thể nhắn tin cho bạn bè.</div>
            @endif
        </div>
    </div>
</div>

<script>
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;

    function toggleSearch() {
        const panel = document.getElementById('msgSearchPanel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }

    // Tự động điều chỉnh chiều cao textarea
    document.getElementById('mainChatInput')?.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Enter để gửi
    document.getElementById('mainChatInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (this.value.trim() !== '') this.form.submit();
        }
    });
</script>
@endsection
