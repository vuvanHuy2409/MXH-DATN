@extends('layouts.app')

@section('content')
<script>
    // Hide unread message badge when viewing conversation
    document.addEventListener('DOMContentLoaded', function() {
        const msgLink = document.querySelector('a[title="Tin nhắn"]');
        if (msgLink) {
            const badge = msgLink.querySelector('.nav-badge');
            if (badge) badge.remove();
        }
    });
</script>
@php
    $themeColor = $conversation->theme_color ?? '#0071e3';
@endphp
<div style="display: flex; flex-direction: column; height: calc(100vh - 100px); padding: 20px 0; position: relative;">
    <!-- Chat Header -->
    <div class="glass-bubble" style="margin-bottom: 10px; padding: 0; position: relative; z-index: 1000;">
        <div style="display: flex; align-items: center; gap: 15px; padding: 12px 20px;">
            <a href="{{ route('messages.index') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; opacity: 0.7;">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <div class="avatar" style="width: 40px; height: 40px; background-image: url('{{ $conversation->type === 'direct' ? ($otherUser->avatar_url ?? '') : ($conversation->avatar_url ?? '') }}'); background-size: cover; border: 2px solid {{ $themeColor }};"></div>
            <div style="flex-grow: 1;">
                <div style="font-weight: 800; font-size: 16px; letter-spacing: -0.3px;">{{ $conversation->type === 'direct' ? ($otherUser->username ?? 'Người dùng') : ($conversation->name ?? 'Nhóm') }}</div>
                <div style="font-size: 12px; color: var(--secondary-text); font-weight: 500; opacity: 0.8;">
                    {{ $conversation->type === 'group' ? $conversation->users()->count() . ' thành viên' : 'Đang hoạt động' }}
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; align-items: center;">
                <div onclick="toggleSearch()" style="cursor: pointer; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.05)'" onmouseout="this.style.background='transparent'">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div class="dropdown">
                    <div id="headerDots" onclick="toggleMenu(event, 'chatMenu')" style="cursor: pointer; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background 0.2s; position: relative;">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                    </div>
                    <div id="chatMenu" class="dropdown-content" style="right: 0; min-width: 240px; border-radius: 20px; overflow: hidden; padding: 8px 0; border: 1px solid var(--glass-border); box-shadow: 0 15px 35px rgba(0,0,0,0.15);">
                        <div style="padding: 12px 20px; font-size: 11px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 0.5px;">Màu chủ đề</div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; padding: 5px 20px 15px;">
                            @foreach(['#0071e3', '#34c759', '#ff3b30', '#af52de', '#ff9500', '#5856d6', '#ff2d55', '#000000'] as $color)
                                <div onclick="changeThemeColor('{{ $color }}')" style="width: 32px; height: 32px; border-radius: 50%; background: {{ $color }}; cursor: pointer; border: 2.2px solid {{ $themeColor === $color ? 'var(--text-color)' : 'transparent' }}; transition: transform 0.2s;"></div>
                            @endforeach
                        </div>
                        <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>
                        <button onclick="openAppointmentModal()">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            Tạo lịch hẹn
                        </button>
                        <button onclick="openMediaModal()">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            Media & File
                        </button>
                        @if($conversation->type === 'group')
                            <button onclick="openGroupSettingsModal()">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                Thông tin nhóm
                            </button>
                        @endif
                        <button onclick="leaveChat()" class="danger">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            {{ $conversation->type === 'group' ? 'Rời nhóm' : 'Xoá đoạn chat' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Integrated Search Panel -->
    <div id="msgSearchPanel" style="display: none; padding: 0 20px 10px; position: relative; z-index: 999; animation: fadeIn 0.2s;">
        <div style="position: relative;">
            <input type="text" id="msgSearchInput" placeholder="Tìm tin nhắn..." 
                   style="width: 100%; border-radius: 12px; padding: 10px 40px 10px 15px; background: rgba(0,0,0,0.03); border: 1px solid var(--glass-border); outline:none;">
            <div onclick="toggleSearch()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; opacity: 0.5;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </div>
        </div>
        <div id="msgSearchResults" class="glass-bubble" style="display: none; margin-top: 10px; max-height: 300px; overflow-y: auto; border-radius: 16px; border: 1px solid var(--glass-border); box-shadow: 0 10px 25px rgba(0,0,0,0.1);"></div>
    </div>

    <!-- Messages Container -->
    <div id="messages-container" style="flex-grow: 1; overflow-y: auto; padding: 20px 25px; display: flex; flex-direction: column; gap: 15px;">
        @foreach($messages as $message)
            @php
                $isMe = $message->sender_id === auth()->id();
                $isMedia = in_array($message->message_type, ['image', 'video']);
                $isSystem = $message->message_type === 'system';
            @endphp

            @if($isSystem)
                <div style="align-self: center; padding: 10px 20px; background: rgba(0,0,0,0.04); border-radius: 14px; font-size: 13px; color: var(--secondary-text); text-align: center; max-width: 85%; margin: 8px 0; border: 1px solid var(--glass-border);">
                    {!! $message->content !!}
                </div>
            @else
                <div class="message-wrapper {{ $isMe ? 'me' : 'other' }}" id="msg-{{ $message->id }}" style="display: flex; gap: 10px; align-items: flex-end; flex-direction: {{ $isMe ? 'row-reverse' : 'row' }}; max-width: 90%; align-self: {{ $isMe ? 'flex-end' : 'flex-start' }};">
                    @if(!$isMe)
                        <div class="avatar" style="width: 32px; height: 32px; background-image: url('{{ $message->sender->avatar_url }}'); background-size: cover; flex-shrink: 0;"></div>
                    @endif
                    
                    <div style="display: flex; flex-direction: column; align-items: {{ $isMe ? 'flex-end' : 'flex-start' }}; overflow: visible;">
                        @if($conversation->type === 'group' && !$isMe)
                            <span style="font-size: 11px; color: var(--secondary-text); margin-bottom: 2px; margin-left: 5px; font-weight: 700;">{{ $message->sender->username }}</span>
                        @endif

                                                        @if($message->parent)
                                                    <div onclick="scrollToMessage({{ $message->parent_id }})" style="cursor:pointer; background: rgba(0,0,0,0.05); padding: 8px 12px 15px; border-radius: 18px 18px 5px 5px; font-size: 12px; margin-bottom: -10px; border-left: 3px solid {{ $themeColor }}; opacity: 0.8; max-width: 90%; align-self: {{ $isMe ? 'flex-end' : 'flex-start' }}; display: flex; gap: 8px; align-items: center;">
                                                        @if($message->parent->message_type === 'image')
                                                            <img src="{{ asset($message->parent->content) }}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 6px; filter: blur(1px); opacity: 0.8;">
                                                        @endif
                                                        <div style="overflow: hidden;">
                                                            <div style="font-weight: 800; color: {{ $themeColor }};">{{ $message->parent->sender->username ?? 'Người dùng' }}</div>
                                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                {{ $message->parent->message_type === 'image' ? 'Hình ảnh' : ($message->parent->message_type === 'video' ? '🎥 Video' : $message->parent->content) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif                        
                        <div style="display: flex; align-items: center; gap: 4px; flex-direction: {{ $isMe ? 'row-reverse' : 'row' }}; width: 100%; position: relative;">
                            <div class="{{ $isMedia ? '' : 'liquid-glass' }} {{ $isMe && !$isMedia ? 'bubble-me' : ($isMedia ? '' : 'bubble-other') }}" 
                                 style="{{ $isMedia ? '' : 'padding: 12px 18px; border-radius: 22px;' }} font-size: 15px; line-height: 1.5; position: relative; {{ $isMe && !$isMedia ? 'background:'. $themeColor .' !important;' : '' }}">
                                
                                @if($message->message_type === 'image')
                                    <img src="{{ asset($message->content) }}" id="img-{{ $message->id }}" onclick="openLightbox(this.src)" style="max-width: 250px; max-height: 300px; object-fit: cover; border-radius: 18px; display: block; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: zoom-in;">
                                @elseif($message->message_type === 'video')
                                    <video src="{{ asset($message->content) }}" controls style="max-width: 300px; max-height: 300px; border-radius: 18px; display: block; box-shadow: 0 4px 15px rgba(0,0,0,0.1);"></video>
                                @elseif($message->message_type === 'post_share')
                                    @php
                                        $sharedPostId = $message->metadata['post_id'] ?? null;
                                        $sharedPost = $sharedPostId ? \App\Models\Post::with(['user', 'media'])->find($sharedPostId) : null;
                                    @endphp
                                    @if($sharedPost)
                                    <div onclick="window.location.href='{{ route('posts.show', $sharedPost->id) }}'" style="cursor: pointer; background: var(--glass-bg); border-radius: 20px; overflow: hidden; width: 260px; border: 1px solid var(--glass-border); box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                        @if($sharedPost->media->count() > 0)
                                            <div style="height: 140px; width: 100%; position: relative;">
                                                <img src="{{ asset($sharedPost->media->first()->media_url) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); color: white; padding: 4px 8px; border-radius: 8px; font-size: 10px; font-weight: 800; backdrop-filter: blur(4px);">BÀI VIẾT</div>
                                            </div>
                                        @endif
                                        <div style="padding: 15px;">
                                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                                <div class="avatar" style="width: 24px; height: 24px; background-image: url('{{ $sharedPost->user->avatar_url }}'); background-size: cover; border-radius: 50%;"></div>
                                                <span style="font-size: 13px; font-weight: 800; color: var(--text-color);">{{ $sharedPost->user->username }}</span>
                                            </div>
                                            <div style="font-size: 14px; color: var(--text-color); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; opacity: 0.9;">
                                                {{ $sharedPost->content }}
                                            </div>
                                            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--glass-border); color: var(--accent-color); font-size: 12px; font-weight: 800; display: flex; align-items: center; gap: 5px;">
                                                <span>Xem chi tiết</span>
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div style="padding: 15px; background: rgba(0,0,0,0.05); border-radius: 15px; font-size: 13px; opacity: 0.5; font-style: italic; border: 1px dashed var(--glass-border);">Bài viết này hiện không khả dụng.</div>
                                    @endif
                                @elseif($message->message_type === 'file')
                                    @php $fileName = $message->metadata['file_name'] ?? 'Tệp đính kèm'; @endphp
                                    <div onclick="const a = document.createElement('a'); a.href='/{{ $message->content }}'; a.download='{{ $fileName }}'; a.click();" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="{{ $isMe ? 'white' : $themeColor }}" stroke-width="2" fill="none"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                        <span style="font-size: 13px; font-weight: 600; text-decoration: underline;">{{ $fileName }}</span>
                                    </div>
                                @else
                                    <span class="message-text" data-raw-content="{{ $message->content }}">{!! nl2br(e($message->content)) !!}</span>
                                @endif
                            </div>

                            <!-- Message Action Menu -->
                            <div class="dropdown msg-dropdown" style="flex-shrink: 0; position: static;">
                                <div onclick="toggleMenu(event, 'msgMenu-{{ $message->id }}')" class="msg-dots" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                </div>
                                <div id="msgMenu-{{ $message->id }}" class="dropdown-content msg-menu-content" style="z-index: 3000; position: absolute; {{ $isMe ? 'right: 40px;' : 'left: 40px;' }} top: 0;">
                                    <button onclick="prepareReply({{ $message->id }}, '{{ $message->sender->username }}', '{{ addslashes($message->content) }}', '{{ $message->message_type }}')">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>
                                        Trả lời
                                    </button>
                                    <button class="danger" onclick="alert('Đã báo cáo tin nhắn!')">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                                        Báo cáo
                                    </button>
                                    @if($isMe || $conversation->creator_id === auth()->id())
                                        <button class="danger" onclick="deleteMessage({{ $message->id }})">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            Xoá
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span style="font-size: 10px; color: var(--secondary-text); margin-top: 4px; padding: 0 5px; opacity: 0.6;">
                            {{ $message->created_at ? $message->created_at->format('H:i') : '' }}
                        </span>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Reply Preview -->
    <div id="replyPreview" style="display: none; padding: 12px 20px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-top: 1px solid var(--glass-border); border-left: 4px solid {{ $themeColor }}; animation: fadeIn 0.2s;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex-grow: 1; min-width: 0; display: flex; gap: 10px; align-items: center;">
                <div id="replyImagePreview" style="display: none;"></div>
                <div style="overflow: hidden;">
                    <div style="font-size: 12px; font-weight: 800; color: {{ $themeColor }}; margin-bottom: 2px;">Đang trả lời {{ '@' }}<span id="replyUser"></span></div>
                    <div id="replyContentDisplay" style="font-size: 13px; opacity: 0.8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                </div>
            </div>
            <div onclick="cancelReply()" style="cursor: pointer; padding: 5px; opacity: 0.5;"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
        </div>
    </div>

    <!-- Input Area -->
    <div style="padding: 20px; border-top: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(20px);">
        @if($conversation->type === 'direct' && !$isFriend)
            <div style="padding: 15px; background: rgba(255,59,48,0.08); border: 1px solid rgba(255,59,48,0.2); border-radius: 16px; text-align: center; color: #ff3b30; font-size: 14px; font-weight: 700;">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" style="vertical-align: middle; margin-right: 8px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Người này đã hủy theo dõi bạn hoặc bạn đã hủy theo dõi họ. Bạn không thể nhắn tin.
            </div>
        @else
            <form action="{{ route('messages.store', $conversation->id) }}" method="POST" enctype="multipart/form-data" id="chat-form">
                @csrf
                <input type="hidden" name="parent_id" id="replyIdInput">
                <input type="hidden" name="message_type" id="msgTypeInput" value="text">
                <div style="display: flex; gap: 12px; align-items: flex-end;">
                    <div style="flex-grow: 1; background: rgba(0,0,0,0.03); border: 1px solid var(--glass-border); border-radius: 24px; padding: 5px 15px; display: flex; align-items: center; gap: 10px;">
                        <label style="cursor: pointer; opacity: 0.6;" title="Gửi tệp tin">
                            <input type="file" name="file" id="fileInput" style="display:none" onchange="submitWithFile('file')">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                        </label>
                        <label style="cursor: pointer; opacity: 0.6;" title="Gửi ảnh">
                            <input type="file" name="image" accept="image/*" id="imageInput" style="display:none" onchange="submitWithFile('image')">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </label>
                        <textarea id="mainChatInput" name="content" placeholder="Nhập tin nhắn..." rows="1" required style="flex-grow: 1; background: transparent; border: none; outline: none; padding: 10px 0; font-size: 15px; resize: none; color: var(--text-color);"></textarea>
                    </div>
                    <button type="submit" class="btn-post" style="padding: 10px 22px; border-radius: 20px; background: {{ $themeColor }}; border: none; font-weight: 700;">Gửi</button>
                </div>
            </form>
        @endif
    </div>
</div>

<!-- Modals -->
<div id="mediaModal" class="modal">
    <div class="modal-content glass-bubble" style="max-width: 600px; padding: 0; overflow: hidden; display: flex; flex-direction: column; max-height: 80vh; border-radius: 28px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-weight: 800;">Media & File</h3>
            <span onclick="window.location.href='{{ route("messages.show", $conversation->id) }}'" style="cursor: pointer; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; font-size: 20px; opacity: 0.6;">&times;</span>
        </div>
        <div style="display: flex; background: rgba(0,0,0,0.02); padding: 5px;">
            <div onclick="switchMediaTab('media')" id="tab-media" class="media-tab active">Hình ảnh & Video</div>
            <div onclick="switchMediaTab('files')" id="tab-files" class="media-tab">Tệp tin</div>
        </div>
        <div id="mediaContainer" style="padding: 20px; overflow-y: auto; flex-grow: 1; display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;"></div>
        <div id="filesContainer" style="padding: 10px 20px; overflow-y: auto; flex-grow: 1; display: none; flex-direction: column; gap: 10px;"></div>
    </div>
</div>

<div id="groupSettingsModal" class="modal">
    <div class="modal-content glass-bubble" style="max-width: 500px; padding: 0; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; border-radius: 28px;">
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-weight: 800;">Thông tin nhóm</h3>
            <span onclick="window.location.href='{{ route("messages.show", $conversation->id) }}'" style="cursor: pointer; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; font-size: 20px; opacity: 0.6;">&times;</span>
        </div>

        <div style="overflow-y: auto; flex-grow: 1; padding: 20px;">
            <!-- Phần 1: Ảnh và Tên nhóm -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 15px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border);">
                <div style="position: relative; cursor: pointer;" onclick="document.getElementById('newGroupAvatar').click()">
                    <div class="avatar" id="groupAvatarDisplay" style="width: 110px; height: 110px; background-image: url('{{ $conversation->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($conversation->name) . '&background=random&size=200' }}'); background-size: cover; border: 4px solid {{ $themeColor }}; transition: filter 0.3s;" onmouseover="this.style.filter='brightness(0.8)'" onmouseout="this.style.filter='brightness(1)'"></div>
                    @if($conversation->creator_id === auth()->id())
                        <div style="position: absolute; bottom: 0; right: 0; background: var(--accent-color); color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        </div>
                        <form id="avatarUpdateForm" action="{{ route('messages.update_avatar', $conversation->id) }}" method="POST" enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="file" id="newGroupAvatar" name="avatar" accept="image/*" onchange="this.form.submit()">
                        </form>
                    @endif
                </div>
                
                @if($conversation->creator_id === auth()->id())
                    <div onclick="document.getElementById('newGroupAvatar').click()" style="font-size: 13px; font-weight: 700; color: {{ $themeColor }}; cursor: pointer; margin-top: -5px; opacity: 0.9;">Thay đổi ảnh nhóm</div>
                    
                    <form action="{{ route('messages.update_name', $conversation->id) }}" method="POST" style="width: 100%; display: flex; gap: 8px; margin-top: 10px;">
                        @csrf
                        <input type="text" name="name" value="{{ $conversation->name }}" style="flex-grow: 1; padding: 12px 15px; border-radius: 14px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-weight: 800; text-align: center; font-size: 16px;">
                        <button type="submit" style="padding: 8px 18px; background: {{ $themeColor }}; color: white; border: none; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Lưu</button>
                    </form>
                @else
                    <div style="font-weight: 800; font-size: 20px; letter-spacing: -0.5px;">{{ $conversation->name }}</div>
                @endif

                <!-- Mã nhóm -->
                <div style="margin-top: 10px; background: rgba(0,0,0,0.03); padding: 15px 20px; border-radius: 16px; border: 1px dashed var(--glass-border); text-align: center; width: 100%; box-sizing: border-box; position: relative;">
                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; opacity: 0.5; margin-bottom: 8px;">Mã nhóm để tham gia</div>
                    <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                        <span id="groupJoinCode" style="font-size: 26px; font-weight: 900; letter-spacing: 4px; color: {{ $themeColor }}; font-family: monospace;">{{ $conversation->join_code }}</span>
                        <button onclick="copyGroupCode('{{ $conversation->join_code }}')" id="copyBtn" style="background: var(--accent-color); border: none; cursor: pointer; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Phần 2: Thêm thành viên (Input search) -->
            <div style="margin-bottom: 25px;">
                <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 800;">Thêm thành viên</h4>
                <div style="position: relative; margin-bottom: 10px;">
                    <input type="text" id="memberSearch" placeholder="Tìm kiếm bạn bè..." 
                           style="width: 100%; border-radius: 12px; padding: 12px 12px 12px 40px; background: rgba(0,0,0,0.03); border: 1px solid var(--glass-border); outline:none;">
                    <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.4;">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                </div>
                <form id="addMemberForm" action="{{ route('messages.add_members', $conversation->id) }}" method="POST">
                    @csrf
                    <div id="friendsResultList" style="max-height: 200px; overflow-y: auto; border-radius: 12px;"></div>
                    <button id="confirmAddBtn" type="submit" style="display: none; width: 100%; margin-top: 10px; background: {{ $themeColor }}; color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer;">Xác nhận thêm</button>
                </form>
            </div>

            <!-- Phần 3: Danh sách thành viên -->
            <div>
                <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 800;">Thành viên ({{ $conversation->participants->count() }})</h4>
                <div id="membersList">
                    @foreach($conversation->participants as $p)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="avatar" style="width: 36px; height: 36px; background-image: url('{{ $p->user->avatar_url ?? asset('default-avatar.png') }}'); background-size: cover;"></div>
                                <div>
                                    <div style="font-weight: 700; font-size: 14px;">{{ $p->user->username ?? 'Người dùng' }}</div>
                                    <div style="font-size: 11px; opacity: 0.6;">{{ $p->role === 'admin' ? 'Chủ nhóm' : 'Thành viên' }}</div>
                                </div>
                            </div>
                            @if($conversation->creator_id === auth()->id() && $p->user_id !== auth()->id())
                                <button onclick="kickMember({{ $p->user_id }})" style="color: #ff3b30; background: transparent; border: none; font-size: 12px; font-weight: 700; cursor: pointer; padding: 5px 10px; border-radius: 8px;" onmouseover="this.style.background='rgba(255,59,48,0.1)'" onmouseout="this.style.background='transparent'">Kick</button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div id="appointmentModal" class="modal">
    <div class="modal-content glass-bubble" style="max-width: 450px; border-radius: 28px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 800;">Tạo lịch hẹn mới</h3>
            <span onclick="window.location.href='{{ route("messages.show", $conversation->id) }}'" style="cursor: pointer; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; font-size: 20px; opacity: 0.6;">&times;</span>
        </div>
        <form id="appointmentForm" onsubmit="submitAppointment(event)" style="padding: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 700;">Tiêu đề cuộc hẹn</label>
                <input type="text" id="aptTitle" required style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03);" placeholder="Ví dụ: Đi cafe, Họp nhóm...">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 700;">Thời gian</label>
                <input type="datetime-local" id="aptTime" required style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03);">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 700;">Địa điểm (tùy chọn)</label>
                <input type="text" id="aptLocation" style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03);" placeholder="Nhập địa chỉ...">
            </div>
            <button type="submit" class="btn-post" style="width: 100%; padding: 14px; background: {{ $themeColor }}; border: none;">Tạo lịch hẹn</button>
        </form>
    </div>
</div>

<script>
    const convId = {{ $conversation->id }};
    const container = document.getElementById('messages-container');
    
    // Cuộn xuống tin nhắn mới nhất ngay lập tức
    container.scrollTop = container.scrollHeight;

    function linkify(text) {
        // Updated regex to catch domains like google.com
        const urlRegex = /(https?:\/\/[^\s]+|([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?)/g;
        return text.replace(urlRegex, function(url) {
            let href = url;
            if (!url.match(/^https?:\/\//i)) {
                href = 'http://' + url;
            }
            return `<a href="${href}" target="_blank" style="color: inherit; text-decoration: underline; font-weight: 700;">${url}</a>`;
        });
    }

    function applyLinkifyToExisting() {
        document.querySelectorAll('.message-text').forEach(el => {
            const raw = el.getAttribute('data-raw-content') || el.innerText;
            const escaped = document.createElement('div');
            escaped.innerText = raw;
            const htmlWithLinks = linkify(escaped.innerHTML).replace(/\n/g, '<br>');
            el.innerHTML = htmlWithLinks;
        });
    }

    // Cuộn lại sau khi toàn bộ ảnh/nội dung tải xong để đảm bảo vị trí chính xác
    window.onload = () => {
        container.scrollTop = container.scrollHeight;
        applyLinkifyToExisting();
    };

    // Keyboard Shortcuts (Enter to send, Shift+Enter for new line)
    document.getElementById('mainChatInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (this.value.trim() !== '') {
                document.getElementById('chat-form').submit();
            }
        }
    });

    // Auto-resize textarea
    document.getElementById('mainChatInput').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    function toggleMenu(event, id) {
        if(event) event.stopPropagation();
        const menu = document.getElementById(id);
        const wasShowing = menu.classList.contains('show');
        document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        if (!wasShowing) menu.classList.add('show');
    }

    function closeModal(id) { 
        document.getElementById(id).style.display = 'none'; 
        document.body.classList.remove('modal-open');
    }

    // TÌM KIẾM TIN NHẮN
    function toggleSearch() {
        const panel = document.getElementById('msgSearchPanel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        if(panel.style.display === 'block') document.getElementById('msgSearchInput').focus();
    }

    document.getElementById('msgSearchInput').addEventListener('input', function() {
        const q = this.value.trim();
        const resultsBox = document.getElementById('msgSearchResults');
        if(q.length < 2) { resultsBox.style.display = 'none'; return; }

        fetch(`/messages/${convId}/search?q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(messages => {
                resultsBox.innerHTML = '';
                if(messages.length === 0) {
                    resultsBox.innerHTML = '<div style="padding:15px; text-align:center; opacity:0.5; font-size:13px;">Không tìm thấy tin nhắn nào</div>';
                } else {
                    messages.forEach(m => {
                        const item = document.createElement('div');
                        item.style.cssText = 'padding:12px 15px; border-bottom:1px solid rgba(0,0,0,0.03); cursor:pointer; transition:all 0.2s;';
                        item.onmouseover = () => item.style.background = 'rgba(0,0,0,0.02)';
                        item.onmouseout = () => item.style.background = 'transparent';
                        item.onclick = () => scrollToMessage(m.id);
                        
                        item.innerHTML = `
                            <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                                <strong style="font-size:13px;">${m.sender.username}</strong>
                                <span style="font-size:10px; opacity:0.5;">${new Date(m.created_at).toLocaleDateString()}</span>
                            </div>
                            <div style="font-size:13px; opacity:0.8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${m.content}</div>
                        `;
                        resultsBox.appendChild(item);
                    });
                }
                resultsBox.style.display = 'block';
            });
    });

    function scrollToMessage(id) {
        const el = document.getElementById(`msg-${id}`);
        document.getElementById('msgSearchResults').style.display = 'none';
        if(el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.style.transition = 'all 0.5s';
            el.style.background = 'rgba(0,113,227,0.1)';
            el.style.borderRadius = '22px';
            setTimeout(() => el.style.background = '', 2000);
        }
    }

    // TRẢ LỜI TIN NHẮN
    function prepareReply(id, user, content, type) {
        document.getElementById('replyIdInput').value = id;
        document.getElementById('replyUser').innerText = user;
        const display = document.getElementById('replyContentDisplay');
        const imgPrev = document.getElementById('replyImagePreview');
        
        if(type === 'image') {
            const fullUrl = content.startsWith('http') ? content : `/${content}`;
            imgPrev.innerHTML = `<img src="${fullUrl}" style="width:35px; height:35px; object-fit:cover; border-radius:6px; filter:blur(1px); opacity:0.8;">`;
            imgPrev.style.display = 'block';
            display.innerText = 'Hình ảnh';
        } else {
            imgPrev.style.display = 'none';
            display.innerText = content;
        }
        document.getElementById('replyPreview').style.display = 'block';
        document.getElementById('mainChatInput').focus();
    }

    function cancelReply() {
        document.getElementById('replyIdInput').value = '';
        document.getElementById('replyPreview').style.display = 'none';
    }

    function submitWithFile(type) {
        document.getElementById('msgTypeInput').value = type;
        document.getElementById('chat-form').submit();
    }

    // Các hàm khác giữ nguyên...
    function changeThemeColor(color) {
        fetch(`/messages/${convId}/color`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ color: color })
        }).then(() => location.reload());
    }

    function deleteMessage(msgId) {
        if(confirm('Xoá tin nhắn?')) {
            fetch(`/messages/${convId}/messages/${msgId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(() => document.getElementById(`msg-${msgId}`).remove());
        }
    }

    function leaveChat() {
        const isGroup = {{ $conversation->type === 'group' ? 'true' : 'false' }};
        const msg = isGroup ? 'Bạn có chắc chắn muốn rời khỏi nhóm này?' : 'Xoá toàn bộ cuộc hội thoại này?';
        
        if(confirm(msg)) {
            fetch(`/messages/${convId}/delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(() => window.location.href = '{{ route("messages.index") }}');
        }
    }

    function openGroupSettingsModal() {
        document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        document.getElementById('groupSettingsModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    }

    document.getElementById('memberSearch')?.addEventListener('input', function() {
        const q = this.value.trim();
        const resultsBox = document.getElementById('friendsResultList');
        const confirmBtn = document.getElementById('confirmAddBtn');
        
        if(q.length < 1) { 
            resultsBox.innerHTML = ''; 
            confirmBtn.style.display = 'none';
            return; 
        }

        fetch(`/api/friends/search?q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(friends => {
                resultsBox.innerHTML = '';
                if(friends.length === 0) {
                    resultsBox.innerHTML = '<p style="text-align: center; color: var(--secondary-text); padding: 15px;">Không tìm thấy bạn bè nào</p>';
                    confirmBtn.style.display = 'none';
                } else {
                    friends.forEach(f => {
                        const item = document.createElement('label');
                        item.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 10px; cursor: pointer; border-radius: 10px; transition: background 0.2s;';
                        item.onmouseover = () => item.style.background = 'rgba(0,0,0,0.03)';
                        item.onmouseout = () => item.style.background = 'transparent';
                        
                        item.innerHTML = `
                            <input type="checkbox" name="user_ids[]" value="${f.id}" onchange="toggleConfirmBtn()" style="width: 18px; height: 18px;">
                            <div class="avatar" style="width: 32px; height: 32px; background-image: url('${f.avatar_url}'); background-size: cover; border-radius: 50%;"></div>
                            <span style="font-weight: 600; font-size: 14px;">${f.username}</span>
                        `;
                        resultsBox.appendChild(item);
                    });
                }
            });
    });

    function toggleConfirmBtn() {
        const selected = document.querySelectorAll('input[name="user_ids[]"]:checked');
        document.getElementById('confirmAddBtn').style.display = selected.length > 0 ? 'block' : 'none';
    }

    function openMediaModal() { 
        document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        document.getElementById('mediaModal').style.display = 'flex'; 
        document.body.classList.add('modal-open'); 
        openMediaModalActual(); 
    }
    function openAppointmentModal() { 
        document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        document.getElementById('appointmentModal').style.display = 'flex'; 
        document.body.classList.add('modal-open'); 
    }

    async function copyImage(imgUrl) {
        try {
            const data = await fetch(imgUrl);
            const blob = await data.blob();
            await navigator.clipboard.write([new ClipboardItem({ [blob.type]: blob })]);
            alert('Đã sao chép ảnh!');
        } catch (err) { alert('Sao chép ảnh thất bại'); }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => alert('Đã sao chép văn bản!'));
    }

    function copyGroupCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            const btn = document.getElementById('copyBtn');
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            btn.style.background = '#34c759';
            setTimeout(() => {
                btn.innerHTML = originalIcon;
                btn.style.background = '';
            }, 2000);
        });
    }

    window.onclick = (e) => { 
        if (!e.target.closest('.dropdown')) document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        if (e.target.classList.contains('modal')) {
            window.location.href = '{{ route("messages.show", $conversation->id) }}';
        }
    }

    function openMediaModalActual() {
        const mediaCont = document.getElementById('mediaContainer');
        const fileCont = document.getElementById('filesContainer');
        fetch(`/messages/${convId}/media`)
            .then(res => res.json())
            .then(data => {
                mediaCont.innerHTML = ''; fileCont.innerHTML = '';
                data.forEach(m => {
                    const path = m.content.startsWith('http') ? m.content : `/${m.content}`;
                    if(m.message_type === 'image' || m.message_type === 'video') {
                        let html = m.message_type === 'image' 
                            ? `<img src="${path}" style="width:100%; aspect-ratio:1; object-fit:cover; border-radius:12px; cursor:pointer;" onclick="window.open('${path}')">`
                            : `<div style="position:relative;"><video src="${path}" style="width:100%; aspect-ratio:1; object-fit:cover; border-radius:12px;"></video><div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.2); border-radius:12px; cursor:pointer;" onclick="window.open('${path}')"><svg viewBox="0 0 24 24" width="24" height="24" fill="white"><path d="M8 5v14l11-7z"/></svg></div></div>`;
                        mediaCont.innerHTML += html;
                    } else {
                        const name = m.metadata?.file_name || 'Tệp tin';
                        fileCont.innerHTML += `<div style="padding:12px; background:rgba(0,0,0,0.03); border-radius:12px; cursor:pointer;" onclick="window.open('${path}')">${name}</div>`;
                    }
                });
            });
    }

    function switchMediaTab(tab) {
        document.getElementById('tab-media').classList.toggle('active', tab === 'media');
        document.getElementById('tab-files').classList.toggle('active', tab === 'files');
        document.getElementById('mediaContainer').style.display = tab === 'media' ? 'grid' : 'none';
        document.getElementById('filesContainer').style.display = tab === 'files' ? 'flex' : 'none';
    }

    function submitAppointment(event) {
        event.preventDefault();
        const title = document.getElementById('aptTitle').value;
        const time = document.getElementById('aptTime').value;
        const location = document.getElementById('aptLocation').value;

        fetch(`/messages/${convId}/appointments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: title,
                appointment_time: time,
                location: location
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Tải lại trang để hiển thị tin nhắn hệ thống "Đã tạo lịch hẹn"
                window.location.href = '{{ route("messages.show", $conversation->id) }}';
            } else {
                alert('Có lỗi xảy ra khi tạo lịch hẹn.');
            }
        });
    }

    // Tự động tải tin nhắn mới
    let lastMessageId = {{ $messages->last()->id ?? 0 }};
    function refreshMessages() {
        fetch(`/api/chat/${convId}/messages`)
            .then(res => res.json())
            .then(messages => {
                const container = document.getElementById('messages-container');
                let hasNew = false;
                
                messages.forEach(m => {
                    if (m.id > lastMessageId) {
                        renderSingleMessage(m);
                        lastMessageId = m.id;
                        hasNew = true;
                    }
                });

                if (hasNew) {
                    container.scrollTop = container.scrollHeight;
                }
            });
    }

    function renderSingleMessage(m) {
        const container = document.getElementById('messages-container');
        const isMe = m.sender_id == {{ auth()->id() }};
        const isSystem = m.message_type === 'system';
        const div = document.createElement('div');
        
        if (isSystem) {
            div.style.cssText = 'align-self: center; padding: 10px 20px; background: rgba(0,0,0,0.04); border-radius: 14px; font-size: 13px; color: var(--secondary-text); text-align: center; max-width: 85%; margin: 8px 0; border: 1px solid var(--glass-border);';
            div.innerHTML = m.content;
        } else {
            div.className = `message-wrapper ${isMe ? 'me' : 'other'}`;
            div.id = `msg-${m.id}`;
            div.style.cssText = `display: flex; gap: 10px; align-items: flex-end; flex-direction: ${isMe ? 'row-reverse' : 'row'}; max-width: 90%; align-self: ${isMe ? 'flex-end' : 'flex-start'};`;
            
            let avatarUrl = m.sender ? m.sender.avatar_url : '/default-avatar.png';
            let username = m.sender ? m.sender.username : 'Người dùng';
            
            let avatarHtml = !isMe ? `<div class="avatar" style="width: 32px; height: 32px; background-image: url('${avatarUrl}'); background-size: cover; flex-shrink: 0;"></div>` : '';
            let userHeader = ({{ $conversation->type === 'group' ? 'true' : 'false' }} && !isMe) ? `<span style="font-size: 11px; color: var(--secondary-text); margin-bottom: 2px; margin-left: 5px; font-weight: 700;">${username}</span>` : '';
            
            let contentHtml = '';
            if (m.message_type === 'image') {
                contentHtml = `<img src="/${m.content}" onclick="openLightbox(this.src)" style="max-width: 250px; max-height: 300px; object-fit: cover; border-radius: 18px; display: block; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: zoom-in;">`;
            } else if (m.message_type === 'video') {
                contentHtml = `<video src="/${m.content}" controls style="max-width: 300px; max-height: 300px; border-radius: 18px; display: block; box-shadow: 0 4px 15px rgba(0,0,0,0.1)"></video>`;
            } else if (m.message_type === 'post_share') {
                const postId = m.metadata ? m.metadata.post_id : null;
                contentHtml = `<div onclick="window.location.href='/posts/${postId}'" style="cursor: pointer; background: var(--glass-bg); border-radius: 20px; overflow: hidden; width: 260px; border: 1px solid var(--glass-border); box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div style="padding: 15px;">
                        <div style="font-weight: 800; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span style="background: var(--accent-color); color: white; padding: 2px 6px; border-radius: 6px; font-size: 10px;">BÀI VIẾT</span>
                            <span style="font-size: 12px; opacity: 0.6;">Được chia sẻ</span>
                        </div>
                        <div style="font-size: 14px; opacity: 0.9; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${m.content || 'Nhấn để xem nội dung bài viết'}
                        </div>
                        <div style="margin-top: 10px; color: var(--accent-color); font-size: 11px; font-weight: 800;">XEM CHI TIẾT →</div>
                    </div>
                </div>`;
            } else {
                const escaped = document.createElement('div');
                escaped.innerText = m.content;
                const linkified = linkify(escaped.innerHTML).replace(/\n/g, '<br>');
                contentHtml = `<span class="message-text">${linkified}</span>`;
            }

            div.innerHTML = `
                ${avatarHtml}
                <div style="display: flex; flex-direction: column; align-items: ${isMe ? 'flex-end' : 'flex-start'}; overflow: visible;">
                    ${userHeader}
                    <div style="display: flex; align-items: center; gap: 4px; flex-direction: ${isMe ? 'row-reverse' : 'row'}; width: 100%; position: relative;">
                        <div class="${m.message_type === 'text' ? 'liquid-glass' : ''} ${isMe && m.message_type === 'text' ? 'bubble-me' : (m.message_type === 'text' ? 'bubble-other' : '')}" 
                             style="${m.message_type === 'text' ? 'padding: 12px 18px; border-radius: 22px;' : ''} font-size: 15px; line-height: 1.5; position: relative; ${isMe && m.message_type === 'text' ? 'background:{{ $themeColor }} !important;' : ''}">
                            ${contentHtml}
                        </div>
                    </div>
                    <span style="font-size: 10px; color: var(--secondary-text); margin-top: 4px; padding: 0 5px; opacity: 0.6;">Vừa xong</span>
                </div>
            `;
        }
        container.appendChild(div);
    }

    setInterval(refreshMessages, 5000);

    // Nhắc lịch hẹn
    const notifiedAppointments = new Set();
    function checkAppointments() {
        fetch(`/messages/${convId}/appointments`)
            .then(res => res.json())
            .then(data => {
                const now = new Date();
                data.forEach(apt => {
                    const aptTime = new Date(apt.appointment_time);
                    const diff = aptTime - now;
                    
                    if (diff <= 60000 && diff > -60000 && !notifiedAppointments.has(apt.id)) {
                        renderSingleMessage({
                            message_type: 'system',
                            content: `🔔 **NHẮC HẸN:** "${apt.title}" đã đến giờ! <br> 📍 Địa điểm: ${apt.location || 'Không có'}`
                        });
                        notifiedAppointments.add(apt.id);
                        document.getElementById('messages-container').scrollTop = document.getElementById('messages-container').scrollHeight;
                    }
                });
            });
    }

    // Kiểm tra mỗi 30 giây
    setInterval(checkAppointments, 30000);
    checkAppointments();
</script>

<style>
    .media-tab { flex: 1; text-align: center; padding: 10px; font-size: 13px; font-weight: 700; color: var(--secondary-text); cursor: pointer; border-radius: 10px; transition: all 0.2s; }
    .media-tab.active { background: white; color: var(--text-color); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .message-wrapper:hover .msg-dots { opacity: 1 !important; }
    .msg-dots { opacity: 0; cursor: pointer; color: var(--secondary-text); background: transparent; }
    .msg-dots:hover { background: rgba(0,0,0,0.05) !important; }
    .msg-menu-content { min-width: 170px; border-radius: 18px !important; border: 1px solid var(--glass-border); box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; padding: 6px !important; }
    .msg-menu-content button { padding: 10px 15px !important; font-size: 13px !important; border-radius: 12px !important; width: 100%; border: none; background: transparent; text-align: left; display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; color: var(--text-color); }
    .bubble-me { color: white !important; border-bottom-right-radius: 4px !important; }
    .bubble-other { background: rgba(0,0,0,0.05) !important; border-bottom-left-radius: 4px !important; }
    .dropdown-content { display: none; position: absolute; background: var(--glass-bg); backdrop-filter: blur(30px); z-index: 2000; margin-top: 8px; }
    .dropdown-content button { width: 100%; border: none; background: transparent; padding: 12px 20px; text-align: left; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 14px; cursor: pointer; color: var(--text-color); }
    .show { display: block; }
</style>
@endsection
