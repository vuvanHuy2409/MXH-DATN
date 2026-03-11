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

    /* Message Actions */
    .message-wrapper {
        position: relative;
    }
    
    .message-actions-trigger {
        opacity: 0;
        transition: opacity 0.2s;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        background: rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        justify-content: center;
        align-self: center;
    }
    
    .message-wrapper:hover .message-actions-trigger {
        opacity: 1;
    }

    .bubble {
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
    }

    .bubble a {
        color: inherit;
        text-decoration: underline;
        font-weight: 700;
    }
    
    .message-wrapper.me .bubble a {
        color: #fff;
    }
    
    .message-wrapper.me .bubble {
        border-bottom-right-radius: 4px !important;
    }
    
    .message-wrapper.other .bubble {
        border-bottom-left-radius: 4px !important;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Action Dropdown */
    .msg-action-menu {
        display: none;
        position: absolute;
        bottom: 100%;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        z-index: 100;
        min-width: 120px;
        overflow: hidden;
    }
    
    .message-wrapper.me .msg-action-menu { right: 0; }
    .message-wrapper.other .msg-action-menu { left: 0; }

    .msg-action-item {
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .msg-action-item:hover {
        background: rgba(0,0,0,0.05);
    }

    /* Reply Style */
    .reply-preview {
        padding: 10px 15px;
        background: rgba(0,0,0,0.03);
        border-left: 4px solid var(--accent-color);
        display: none;
        justify-content: space-between;
        align-items: center;
        border-radius: 12px 12px 0 0;
    }

    .replied-message-info {
        background: rgba(0,0,0,0.05);
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 5px;
        border-left: 2px solid var(--accent-color);
        opacity: 0.8;
    }

    /* Modal Tabs */
    .settings-tab {
        padding: 10px 20px;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        opacity: 0.5;
        border-bottom: 2px solid transparent;
    }
    .settings-tab.active {
        opacity: 1;
        border-bottom-color: var(--accent-color);
        color: var(--accent-color);
    }
</style>

<div class="chat-layout">
    <!-- Cột trái: Danh sách hội thoại -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 800;">Tin nhắn</h2>
                <button onclick="window.location.href='{{ route('messages.index') }}'" style="background: none; border: none; cursor: pointer; color: var(--accent-color);">
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
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 5px;">
                            <div style="display: flex; align-items: center; gap: 6px; min-width: 0;">
                                <span style="font-weight: 700; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $convName }}</span>
                                @if($convIsGroup)
                                    <span style="background: rgba(0,113,227,0.1); color: var(--accent-color); font-size: 8px; font-weight: 800; padding: 1px 4px; border-radius: 4px; text-transform: uppercase; flex-shrink: 0;">Nhóm</span>
                                @endif
                            </div>
                            <span style="font-size: 11px; opacity: 0.6; flex-shrink: 0;">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
                        </div>
                        <div style="font-size: 13px; color: var(--secondary-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; font-weight: {{ $convUnread ? '700' : '400' }}">
                            {{ $conv->lastMessage ? ($conv->lastMessage->sender_id === auth()->id() ? 'Bạn: ' : '') . ($conv->lastMessage->message_type === 'text' ? $conv->lastMessage->content : ($conv->lastMessage->message_type === 'image' ? 'Đã gửi một ảnh' : ($conv->lastMessage->message_type === 'video' ? 'Đã gửi một video' : 'Đã gửi một tài liệu'))) : 'Bắt đầu trò chuyện...' }}
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
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="font-weight: 800; font-size: 16px;">{{ $conversation->type === 'direct' ? ($otherUser->username ?? 'Người dùng') : ($conversation->name ?? 'Nhóm') }}</div>
                    @if($conversation->type === 'group')
                        <span style="background: rgba(0,113,227,0.1); color: var(--accent-color); font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Nhóm</span>
                    @else
                        <span style="background: rgba(52,199,89,0.1); color: #34c759; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Bạn bè</span>
                    @endif
                </div>
                <div style="font-size: 12px; color: var(--secondary-text); font-weight: 500;">
                    {{ $conversation->type === 'group' ? $conversation->users()->count() . ' thành viên' : ($otherUser && $otherUser->is_online ? 'Đang hoạt động' : 'Ngoại tuyến') }}
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
        <div id="messages-container" style="flex-grow: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
            @php
                if (!function_exists('getFileIconInfo')) {
                    function getFileIconInfo($fileName) {
                        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $icons = [
                            'pdf' => ['color' => '#ff3b30', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path>'],
                            'doc' => ['color' => '#0071e3', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line>'],
                            'docx' => ['color' => '#0071e3', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line>'],
                            'xls' => ['color' => '#28a745', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><line x1="8" y1="9" x2="10" y2="9"></line>'],
                            'xlsx' => ['color' => '#28a745', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><line x1="8" y1="9" x2="10" y2="9"></line>'],
                            'ppt' => ['color' => '#fd7e14', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 13l3 3 3-3"></path>'],
                            'pptx' => ['color' => '#fd7e14', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 13l3 3 3-3"></path>'],
                            'zip' => ['color' => '#6c757d', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M12 12v6"></path><path d="M10 16l2 2 2-2"></path>'],
                            'rar' => ['color' => '#6c757d', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M12 12v6"></path><path d="M10 16l2 2 2-2"></path>'],
                            'txt' => ['color' => '#000', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line>'],
                        ];

                        return $icons[$ext] ?? ['color' => '#6e6e73', 'icon' => '<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline>'];
                    }
                }
            @endphp
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
                    <div class="message-wrapper {{ $isMe ? 'me' : 'other' }}" id="msg-{{ $message->id }}" style="display: flex; gap: 10px; flex-direction: {{ $isMe ? 'row-reverse' : 'row' }}; max-width: 85%; align-self: {{ $isMe ? 'flex-end' : 'flex-start' }}; animation: fadeIn 0.3s ease;">
                        @if(!$isMe)
                            <div class="avatar" style="width: 32px; height: 32px; background-image: url('{{ $message->sender->avatar_url }}'); background-size: cover; align-self: flex-end; flex-shrink: 0; border-radius: 10px !important;"></div>
                        @endif
                        
                        <div style="display: flex; flex-direction: column; align-items: {{ $isMe ? 'flex-end' : 'flex-start' }}; position: relative;" class="message-content-wrapper">
                            @if($message->parent_id)
                                <div class="replied-message-info">
                                    <strong>{{ $message->parent->sender->username }}</strong>: 
                                    {{ $message->parent->message_type === 'text' ? (mb_strlen($message->parent->content) > 30 ? mb_substr($message->parent->content, 0, 30).'...' : $message->parent->content) : '[Phương tiện]' }}
                                </div>
                            @endif

                            <div class="{{ $isMedia ? '' : 'bubble' }}" style="{{ $isMe && !$isMedia ? 'background: '.$themeColor.'; color: white;' : (!$isMedia ? 'background: rgba(0,0,0,0.05); color: var(--text-color);' : '') }} border-radius: 20px; padding: {{ $isMedia ? '0' : '12px 18px' }}; font-size: 15px; line-height: 1.4; position: relative; font-weight: 500;">
                                @if($message->message_type === 'image')
                                    <img src="{{ asset($message->content) }}" onclick="openLightbox(this.src)" style="max-width: 280px; border-radius: 18px; cursor: zoom-in; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                @elseif($message->message_type === 'video')
                                    <video src="{{ asset($message->content) }}" controls playsinline style="max-width: 280px; border-radius: 18px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                        Trình duyệt của bạn không hỗ trợ phát video. <a href="{{ asset($message->content) }}" download>Tải về</a>
                                    </video>
                                @elseif($message->message_type === 'file')
                                    @php 
                                        $fileName = $message->metadata['file_name'] ?? 'file';
                                        $fileInfo = getFileIconInfo($fileName);
                                    @endphp
                                    <a href="{{ asset($message->content) }}" download="{{ $fileName }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px; padding: 5px;">
                                        <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $isMe ? 'rgba(255,255,255,0.2)' : $fileInfo['color'].'15' }}; display: flex; align-items: center; justify-content: center; color: {{ $isMe ? 'white' : $fileInfo['color'] }};">
                                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none">{!! $fileInfo['icon'] !!}</svg>
                                        </div>
                                        <div style="overflow: hidden; flex-grow: 1;">
                                            <div style="font-weight: 700; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">{{ $fileName }}</div>
                                            <div style="font-size: 11px; opacity: 0.7;">{{ isset($message->metadata['file_size']) ? round($message->metadata['file_size'] / 1024, 1) . ' KB' : 'Tải về' }} ({{ strtoupper(pathinfo($fileName, PATHINFO_EXTENSION)) }})</div>
                                        </div>
                                    </a>
                                @else
                                    @php
                                        $content = e($message->content);
                                        // 1. Nhận diện URL bắt đầu bằng http/https
                                        $content = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank">$1</a>', $content);
                                        // 2. Nhận diện URL bắt đầu bằng www
                                        $content = preg_replace('/(?<!href=")(www\.[^\s]+)/', '<a href="http://$1" target="_blank">$1</a>', $content);
                                        // 3. Nhận diện các tên miền phổ biến chưa được gắn link (naked domains)
                                        $content = preg_replace('/(?<![">])\b([a-zA-Z0-9.-]+\.(?:com|net|org|vn|edu|gov|io|info|me))\b/i', '<a href="http://$1" target="_blank">$1</a>', $content);
                                    @endphp
                                    {!! nl2br($content) !!}
                                @endif
                            </div>
                            <span style="font-size: 10px; opacity: 0.5; margin-top: 4px;">{{ $message->created_at->format('H:i') }}</span>
                        </div>

                        <!-- Three dots trigger -->
                        <div style="position: relative; align-self: center;">
                            <div class="message-actions-trigger" onclick="toggleMsgMenu({{ $message->id }})">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </div>
                            
                            <!-- Action Menu -->
                            <div id="msg-menu-{{ $message->id }}" class="msg-action-menu">
                                <div class="msg-action-item" onclick="replyToMessage({{ $message->id }}, '{{ $message->sender->username }}', '{{ $message->message_type === 'text' ? addslashes($message->content) : '[Phương tiện]' }}')">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>
                                    Trả lời
                                </div>
                                @if($isMe)
                                    <div class="msg-action-item" style="color: #ff3b30;" onclick="deleteMessage({{ $message->id }})">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Xóa
                                    </div>
                                @else
                                    <div class="msg-action-item" style="color: #ff9500;" onclick="alert('Đã báo cáo tin nhắn này.')">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                                        Báo cáo
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Input Area -->
        <div style="padding: 20px; border-top: 1px solid var(--glass-border); background: rgba(255,255,255,0.01);">
            @if($isFriend || $conversation->type === 'group')
                <!-- Reply Preview -->
                <div id="replyPreview" class="reply-preview">
                    <div style="overflow: hidden;">
                        <div style="font-size: 12px; font-weight: 800; color: var(--accent-color);">Đang trả lời <span id="replyUser"></span></div>
                        <div id="replyText" style="font-size: 13px; opacity: 0.7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                    </div>
                    <div onclick="cancelReply()" style="cursor: pointer; opacity: 0.5; padding: 5px;">&times;</div>
                </div>

                <!-- Media Preview -->
                <div id="mediaMsgPreview" style="display: none; padding: 10px 15px; background: rgba(0,0,0,0.02); border-left: 4px solid var(--accent-color); border-radius: 12px 12px 0 0; border-bottom: 1px solid var(--glass-border); position: relative;">
                    <div id="mediaMsgPreviewContent" style="display: flex; align-items: center; gap: 12px;"></div>
                    <div onclick="clearMediaMsgPreview()" style="position: absolute; right: 10px; top: 10px; cursor: pointer; opacity: 0.5; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.05); border-radius: 50%;">&times;</div>
                </div>

                <form action="{{ route('messages.store', $conversation->id) }}" method="POST" enctype="multipart/form-data" id="chat-form" onsubmit="handleChatSubmit(event)">
                    @csrf
                    <input type="hidden" name="parent_id" id="replyParentId" value="">
                    <div style="display: flex; gap: 8px; align-items: flex-end; background: white; border: 1.5px solid var(--glass-border); border-radius: 24px; padding: 8px 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: all 0.3s;" id="inputWrapper">
                        <div style="display: flex; gap: 4px; padding-bottom: 4px;">
                            <label style="cursor: pointer; opacity: 0.6; transition: all 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='var(--accent-color)'" onmouseout="this.style.opacity='0.6'; this.style.color='inherit'" title="Ảnh">
                                <input type="file" name="image" id="msgImageInput" accept="image/*" style="display:none" onchange="previewMsgMedia(this, 'image')">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            </label>
                            <label style="cursor: pointer; opacity: 0.6; transition: all 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='#af52de'" onmouseout="this.style.opacity='0.6'; this.style.color='inherit'" title="Tài liệu">
                                <input type="file" name="file" id="msgFileInput" style="display:none" onchange="previewMsgMedia(this, 'file')">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                            </label>
                        </div>
                        <textarea id="mainChatInput" name="content" placeholder="Nhập tin nhắn..." rows="1" 
                                  style="flex-grow: 1; background: transparent; border: none; outline: none; padding: 10px 0; font-size: 15px; resize: none; max-height: 150px; font-weight: 500; color: var(--text-color);"></textarea>
                        <button type="submit" id="sendBtn" style="background: var(--accent-color); border: none; color: white; width: 34px; height: 34px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; margin-bottom: 2px; transition: transform 0.2s; box-shadow: 0 4px 10px rgba(0,113,227,0.3);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                            <svg id="sendIcon" viewBox="0 0 24 24" width="16" height="16" stroke="white" stroke-width="3" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            <svg id="loadingIcon" style="display: none; animation: spin 1s linear infinite;" viewBox="0 0 24 24" width="16" height="16" stroke="white" stroke-width="3" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
                        </button>
                    </div>
                </form>
            @else
                <div style="text-align: center; font-size: 14px; color: #ff3b30; padding: 15px; background: rgba(255,59,48,0.05); border-radius: 15px; font-weight: 600;">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" style="vertical-align: middle; margin-right: 5px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Bạn chỉ có thể nhắn tin cho bạn bè.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Conversation Settings Modal -->
<div id="groupSettingsModal" class="modal" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); backdrop-filter: blur(15px); z-index: 5000;">
    <div class="modal-content glass-bubble" style="max-width: 500px; padding: 0; border-radius: 32px; width: 95%; overflow: hidden; height: 80vh; display: flex; flex-direction: column;">
        <!-- Modal Header -->
        <div style="padding: 25px; border-bottom: 1px solid var(--glass-border); text-align: center; position: relative;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 800;">Cài đặt hội thoại</h3>
            <span onclick="closeGroupSettingsModal()" style="position: absolute; right: 25px; top: 22px; cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
        </div>

        <!-- Modal Tabs -->
        <div style="display: flex; justify-content: center; background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--glass-border);">
            <div class="settings-tab active" id="tab-info" onclick="switchSettingsTab('info')">Thông tin</div>
            @if($conversation->type === 'group')
                <div class="settings-tab" id="tab-members" onclick="switchSettingsTab('members')">Thành viên</div>
            @endif
            <div class="settings-tab" id="tab-tools" onclick="switchSettingsTab('tools')">Tiện ích</div>
        </div>

        <div id="settingsContent" style="flex-grow: 1; overflow-y: auto; padding: 25px;">
            <!-- Content will be injected here -->
        </div>
    </div>
</div>

<script>
    const container = document.getElementById('messages-container');
    
    function scrollToBottom() {
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Scroll immediately
    scrollToBottom();

    // Scroll again after a short delay to account for rendering
    setTimeout(scrollToBottom, 100);

    // Scroll again after all content (like images) is loaded
    window.addEventListener('load', scrollToBottom);

    // MutationObserver to scroll when new messages are added (if any AJAX is used)
    const observer = new MutationObserver(scrollToBottom);
    if (container) {
        observer.observe(container, { childList: true });
    }

    function openGroupSettingsModal() {
        document.getElementById('groupSettingsModal').style.display = 'flex';
        document.body.classList.add('modal-open');
        switchSettingsTab('info');
    }

    function closeGroupSettingsModal() {
        document.getElementById('groupSettingsModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function switchSettingsTab(tab) {
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('tab-' + tab)?.classList.add('active');
        
        const content = document.getElementById('settingsContent');
        if (tab === 'info') {
            renderInfoTab();
        } else if (tab === 'members') {
            renderMembersTab();
        } else if (tab === 'tools') {
            renderToolsTab();
        }
    }

    function renderInfoTab() {
        const content = document.getElementById('settingsContent');
        const isGroup = '{{ $conversation->type }}' === 'group';
        const isAdmin = '{{ $conversation->creator_id }}' == '{{ auth()->id() }}';
        
        let html = `
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="position: relative; display: inline-block;">
                    <div class="avatar" style="width: 100px; height: 100px; background-image: url('${isGroup ? '{{ $conversation->avatar_url }}' : '{{ $otherUser->avatar_url ?? "" }}'}'); background-size: cover; border-radius: 30px; border: 4px solid white; box-shadow: 0 10px 25px rgba(0,0,0,0.1);"></div>
                    ${isGroup && isAdmin ? `
                        <label style="position: absolute; bottom: -5px; right: -5px; background: var(--accent-color); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; cursor: pointer;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="white" stroke-width="2.5" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            <form action="{{ route('messages.update_avatar', $conversation->id) }}" method="POST" enctype="multipart/form-data" style="display:none">
                                @csrf
                                <input type="file" name="avatar" onchange="this.form.submit()">
                            </form>
                        </label>
                    ` : ''}
                </div>
                <h2 style="margin: 15px 0 5px; font-size: 20px; font-weight: 800;">${isGroup ? '{{ $conversation->name }}' : '{{ $otherUser->username ?? "Người dùng" }}'}</h2>
                <p style="color: var(--secondary-text); font-size: 14px;">${isGroup ? 'Cuộc trò chuyện nhóm' : 'Trò chuyện trực tiếp'}</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                ${isGroup && isAdmin ? `
                    <div style="padding: 15px; background: rgba(0,0,0,0.02); border-radius: 18px; border: 1px solid var(--glass-border);">
                        <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 10px;">Tên nhóm</label>
                        <form action="{{ route('messages.update_name', $conversation->id) }}" method="POST" style="display: flex; gap: 10px;">
                            @csrf
                            <input type="text" name="name" value="{{ $conversation->name }}" style="flex-grow: 1; border: none; background: white; padding: 8px 12px; border-radius: 10px; font-weight: 600; outline: none; border: 1px solid var(--glass-border);">
                            <button type="submit" style="background: var(--accent-color); color: white; border: none; padding: 8px 15px; border-radius: 10px; font-weight: 700; cursor: pointer;">Lưu</button>
                        </form>
                    </div>
                ` : ''}

                <div style="padding: 15px; background: rgba(0,0,0,0.02); border-radius: 18px; border: 1px solid var(--glass-border);">
                    <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 10px;">Màu chủ đề</label>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        ${['#0071e3', '#ff2d55', '#34c759', '#5856d6', '#ff9500', '#af52de'].map(c => `
                            <div onclick="updateThemeColor('${c}')" style="width: 30px; height: 30px; border-radius: 50%; background: ${c}; cursor: pointer; border: 3px solid ${'{{ $themeColor }}' === c ? 'white' : 'transparent'}; box-shadow: 0 0 0 1px ${'{{ $themeColor }}' === c ? c : 'transparent'};"></div>
                        `).join('')}
                    </div>
                </div>

                ${isGroup ? `
                    <div style="padding: 15px; background: rgba(0,0,0,0.02); border-radius: 18px; border: 1px solid var(--glass-border);">
                        <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 5px;">Mã tham gia</label>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <code style="font-size: 18px; font-weight: 800; letter-spacing: 2px;">{{ $conversation->join_code }}</code>
                            <button onclick="copyJoinCode('{{ $conversation->join_code }}')" style="background: none; border: none; color: var(--accent-color); font-weight: 700; cursor: pointer; font-size: 13px;">Sao chép</button>
                        </div>
                    </div>
                ` : ''}
            </div>

            <div style="margin-top: 20px;">
                <button onclick="leaveChat()" style="width: 100%; padding: 15px; border-radius: 18px; background: rgba(255,59,48,0.05); color: #ff3b30; border: none; font-weight: 800; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,59,48,0.1)'" onmouseout="this.style.background='rgba(255,59,48,0.05)'">
                    ${isGroup ? 'Rời khỏi nhóm' : 'Xóa cuộc trò chuyện'}
                </button>
            </div>
        `;
        content.innerHTML = html;
    }

    function renderMembersTab() {
        const content = document.getElementById('settingsContent');
        content.innerHTML = '<div style="text-align: center; padding: 20px; opacity: 0.5;">Đang tải danh sách thành viên...</div>';
        
        fetch('{{ route('messages.members', $conversation->id) }}')
            .then(res => res.json())
            .then(members => {
                let html = `
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        ${members.map(m => `
                            <div style="display: flex; align-items: center; gap: 12px; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="avatar" style="width: 40px; height: 40px; background-image: url('${m.avatar_url}'); background-size: cover; border-radius: 12px;"></div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px;">${m.username}</div>
                                        <div style="font-size: 11px; opacity: 0.6; text-transform: uppercase; font-weight: 800;">${m.pivot.role === 'admin' ? 'Trưởng nhóm' : 'Thành viên'}</div>
                                    </div>
                                </div>
                                ${'{{ $conversation->creator_id }}' == '{{ auth()->id() }}' && m.id != '{{ auth()->id() }}' ? `
                                    <button onclick="kickMember(${m.id})" style="background: none; border: none; color: #ff3b30; cursor: pointer; font-size: 12px; font-weight: 700; opacity: 0.6;" onmouseover="this.style.opacity='1'">Mời ra</button>
                                ` : ''}
                            </div>
                        `).join('')}
                    </div>
                `;
                content.innerHTML = html;
            });
    }

    function renderToolsTab() {
        const content = document.getElementById('settingsContent');
        content.innerHTML = `
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div style="padding: 15px; background: rgba(0,0,0,0.02); border-radius: 18px; border: 1px solid var(--glass-border); cursor: pointer;" onclick="openMediaGallery('media')">
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #34c759; color: white; display: flex; align-items: center; justify-content: center;">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </div>
                        <span style="font-weight: 700; font-size: 14px;">Ảnh & Video đã gửi</span>
                    </div>
                </div>

                <div style="padding: 15px; background: rgba(0,0,0,0.02); border-radius: 18px; border: 1px solid var(--glass-border); cursor: pointer;" onclick="openMediaGallery('files')">
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #af52de; color: white; display: flex; align-items: center; justify-content: center;">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                        </div>
                        <span style="font-weight: 700; font-size: 14px;">File & Liên kết đã gửi</span>
                    </div>
                </div>
            </div>
        `;
    }

    function updateThemeColor(color) {
        fetch('{{ route('messages.update_color', $conversation->id) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ color: color })
        }).then(() => location.reload());
    }

    function copyJoinCode(code) {
        navigator.clipboard.writeText(code).then(() => alert('Đã sao chép mã mời!'));
    }

    function kickMember(userId) {
        if(confirm('Mời thành viên này ra khỏi nhóm?')) {
            fetch(`/messages/{{ $conversation->id }}/kick/` + userId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => renderMembersTab());
        }
    }

    function leaveChat() {
        if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) {
            fetch('{{ route('messages.delete_chat', $conversation->id) }}', {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => window.location.href = '{{ route('messages.index') }}');
        }
    }

    function openMediaGallery(type = 'all') {
        const content = document.getElementById('settingsContent');
        content.innerHTML = '<div style="text-align: center; padding: 20px; opacity: 0.5;">Đang tải...</div>';
        
        fetch('{{ route('messages.media', $conversation->id) }}')
            .then(res => res.json())
            .then(media => {
                if(media.length === 0) {
                    content.innerHTML = '<div style="text-align: center; padding: 40px 20px; opacity: 0.5;">Chưa có phương tiện nào.</div>';
                    return;
                }

                const photos = media.filter(m => m.message_type === 'image' || m.message_type === 'video');
                const linkRegex = /\b([a-zA-Z0-9.-]+\.(?:com|net|org|vn|edu|gov|io|info|me))\b/i;
                const links = media.filter(m => m.message_type === 'file' || m.message_type === 'link' || (m.message_type === 'text' && (m.content.includes('http') || linkRegex.test(m.content))));

                let html = '<div style="margin-bottom: 20px;"><button onclick="renderToolsTab()" style="background:none; border:none; color:var(--accent-color); font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px;"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="3" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg> Quay lại</button></div>';

                if ((type === 'all' || type === 'media') && photos.length > 0) {
                    html += `<label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 15px;">Ảnh & Video (${photos.length})</label>`;
                    html += `<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 30px;">`;
                    photos.forEach(m => {
                        const src = m.content.startsWith('http') ? m.content : '{{ asset("") }}' + m.content;
                        if(m.message_type === 'image') {
                            html += `<img src="${src}" onclick="openLightbox(this.src)" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 12px; cursor: pointer; border: 1px solid var(--glass-border);">`;
                        } else if(m.message_type === 'video') {
                            html += `<div style="position: relative; width: 100%; aspect-ratio: 1;">
                                        <video src="${src}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px; border: 1px solid var(--glass-border);"></video>
                                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 12px; pointer-events: none;">
                                            <svg viewBox="0 0 24 24" width="20" height="20" fill="white"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                        </div>
                                     </div>`;
                        }
                    });
                    html += `</div>`;
                }

                if ((type === 'all' || type === 'files') && links.length > 0) {
                    html += `<label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 15px;">Tệp & Liên kết (${links.length})</label>`;
                    html += `<div style="display: flex; flex-direction: column; gap: 10px;">`;
                    links.forEach(m => {
                        let isUrl = m.message_type === 'link' || (m.message_type === 'text' && (m.content.includes('http') || linkRegex.test(m.content)));
                        let url = '';
                        let label = '';

                        if (isUrl) {
                            const urlMatch = m.content.match(/(https?:\/\/[^\s]+)/) || m.content.match(/\b([a-zA-Z0-9.-]+\.(?:com|net|org|vn|edu|gov|io|info|me))\b/i);
                            url = urlMatch ? urlMatch[0] : m.content;
                            if (!url.startsWith('http')) {
                                url = 'http://' + url;
                            }
                            label = url.length > 40 ? url.substring(0, 40) + '...' : url;
                        } else {
                            url = m.content.startsWith('http') || m.content.startsWith('/') ? m.content : '{{ asset("") }}' + m.content;
                            label = m.metadata?.file_name || 'Tệp đính kèm';
                        }
                        
                        html += `
                            <a href="${url}" target="_blank" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(0,0,0,0.02); border-radius: 14px; border: 1px solid var(--glass-border); text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,113,227,0.05)'" onmouseout="this.style.background='rgba(0,0,0,0.02)'">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: ${isUrl ? '#0071e3' : '#8e44ad'}; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    ${isUrl ? 
                                        '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>' : 
                                        '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>'
                                    }
                                </div>
                                <div style="overflow: hidden;">
                                    <div style="font-weight: 700; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${label}</div>
                                    <div style="font-size: 11px; opacity: 0.5;">${new Date(m.created_at).toLocaleDateString('vi-VN')}</div>
                                </div>
                            </a>
                        `;
                    });
                    html += `</div>`;
                }

                if (type === 'media' && photos.length === 0) {
                    html += '<div style="text-align: center; padding: 40px 20px; opacity: 0.5;">Chưa có ảnh hoặc video nào.</div>';
                }
                if (type === 'files' && links.length === 0) {
                    html += '<div style="text-align: center; padding: 40px 20px; opacity: 0.5;">Chưa có tệp hoặc liên kết nào.</div>';
                }

                content.innerHTML = html;
            });
    }

    function toggleSearch() {
        const panel = document.getElementById('msgSearchPanel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        if (panel.style.display === 'block') document.getElementById('msgSearchInput').focus();
    }

    document.getElementById('msgSearchInput')?.addEventListener('input', function() {
        const q = this.value.trim();
        const results = document.getElementById('msgSearchResults');
        if(q.length < 2) { results.style.display = 'none'; return; }
        
        fetch('{{ route('messages.search', $conversation->id) }}?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(messages => {
                results.innerHTML = '';
                if(messages.length === 0) {
                    results.innerHTML = '<div style="padding: 15px; text-align: center; opacity: 0.5;">Không tìm thấy tin nhắn.</div>';
                } else {
                    messages.forEach(m => {
                        const div = document.createElement('div');
                        div.style.cssText = 'padding: 12px; border-bottom: 1px solid var(--glass-border); cursor: pointer;';
                        div.onmouseover = () => div.style.background = 'rgba(0,0,0,0.03)';
                        div.onmouseout = () => div.style.background = 'transparent';
                        div.innerHTML = `<div style="font-weight:700; font-size:12px;">${m.sender.username}</div><div style="font-size:13px; opacity:0.8;">${m.content}</div>`;
                        div.onclick = () => {
                            const target = document.getElementById('msg-' + m.id);
                            if(target) {
                                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                target.style.background = 'rgba(0,113,227,0.1)';
                                setTimeout(() => target.style.background = 'transparent', 2000);
                                toggleSearch();
                            }
                        };
                        results.appendChild(div);
                    });
                }
                results.style.display = 'block';
            });
    });

    // Message Action Logic
    function toggleMsgMenu(id) {
        // Close all other menus
        document.querySelectorAll('.msg-action-menu').forEach(m => {
            if(m.id !== 'msg-menu-' + id) m.style.display = 'none';
        });
        
        const menu = document.getElementById('msg-menu-' + id);
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        
        // Prevent click from bubbling up
        event.stopPropagation();
    }

    // Close menus on body click
    window.addEventListener('click', () => {
        document.querySelectorAll('.msg-action-menu').forEach(m => m.style.display = 'none');
    });

    function replyToMessage(id, username, text) {
        document.getElementById('replyParentId').value = id;
        document.getElementById('replyUser').innerText = username;
        document.getElementById('replyText').innerText = text;
        document.getElementById('replyPreview').style.display = 'flex';
        document.getElementById('inputWrapper').style.borderRadius = '0 0 24px 24px';
        document.getElementById('mainChatInput').focus();
    }

    function cancelReply() {
        document.getElementById('replyParentId').value = '';
        document.getElementById('replyPreview').style.display = 'none';
        document.getElementById('inputWrapper').style.borderRadius = '24px';
    }

    function previewMsgMedia(input, type) {
        const file = input.files[0];
        if (!file) return;

        // Check file size (e.g., 100MB limit)
        if (file.size > 100 * 1024 * 1024) {
            alert('Tệp tin quá lớn. Vui lòng chọn tệp nhỏ hơn 100MB.');
            input.value = '';
            return;
        }

        const preview = document.getElementById('mediaMsgPreview');
        const content = document.getElementById('mediaMsgPreviewContent');
        const inputWrapper = document.getElementById('inputWrapper');
        
        // Clear other inputs
        if (type !== 'image') document.getElementById('msgImageInput').value = '';
        if (type !== 'file') document.getElementById('msgFileInput').value = '';

        content.innerHTML = '';
        preview.style.display = 'block';
        inputWrapper.style.borderRadius = '0 0 24px 24px';

        const reader = new FileReader();
        reader.onload = function(e) {
            if (type === 'image') {
                content.innerHTML = `<img src="${e.target.result}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 1px solid var(--glass-border);">
                                     <div style="font-size: 13px; font-weight: 600; color: var(--text-color);">${file.name}</div>`;
            } else {
                content.innerHTML = `<div style="width: 50px; height: 50px; border-radius: 8px; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border);">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                     </div>
                                     <div style="font-size: 13px; font-weight: 600; color: var(--text-color);">${file.name}</div>`;
            }
        };
        reader.readAsDataURL(file);
    }

    function clearMediaMsgPreview() {
        document.getElementById('mediaMsgPreview').style.display = 'none';
        document.getElementById('inputWrapper').style.borderRadius = '24px';
        document.getElementById('msgImageInput').value = '';
        document.getElementById('msgFileInput').value = '';
    }

    // Enter để gửi
    document.getElementById('mainChatInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const input = this;
            const form = input.closest('form');
            if (!form) return;
            
            if (input.value.trim() !== '' || document.getElementById('mediaMsgPreview').style.display !== 'none') {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    const event = new Event('submit', { cancelable: true, bubbles: true });
                    if (form.dispatchEvent(event)) {
                        handleChatSubmit({ target: form, preventDefault: () => {} });
                    }
                }
            }
        }
    });

    function handleChatSubmit(event) {
        if (event && event.preventDefault) event.preventDefault();
        
        const form = event.target || event.currentTarget || document.getElementById('chat-form');
        if (!(form instanceof HTMLFormElement)) {
            console.error('Submit target is not a form:', form);
            return;
        }

        const sendBtn = document.getElementById('sendBtn');
        const sendIcon = document.getElementById('sendIcon');
        const loadingIcon = document.getElementById('loadingIcon');
        
        console.log('Submitting form to:', form.action);
        
        if (!form.action) {
            alert('Lỗi: Form thiếu action.');
            return;
        }

        const formData = new FormData(form);
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Disable UI
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.style.opacity = '0.7';
        }
        if (sendIcon) sendIcon.style.display = 'none';
        if (loadingIcon) loadingIcon.style.display = 'block';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token || ''
            }
        })
        .then(async response => {
            console.log('Response status:', response.status);
            let data;
            try {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.warn('Non-JSON response:', text);
                    data = { message: "Lỗi máy chủ (500) hoặc phản hồi không đúng định dạng." };
                }
            } catch (e) {
                data = { message: "Lỗi xử lý phản hồi từ máy chủ." };
            }

            if (response.ok) {
                form.reset();
                if (typeof clearMediaMsgPreview === 'function') clearMediaMsgPreview();
                if (typeof cancelReply === 'function') cancelReply();
                location.reload(); 
            } else {
                throw new Error(data.error || data.message || 'Lỗi không xác định');
            }
        })
        .catch(error => {
            console.error('Upload Error Details:', error);
            alert('Không thể gửi tin nhắn: ' + (error.message || 'Lỗi kết nối'));
        })
        .finally(() => {
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.style.opacity = '1';
            }
            if (sendIcon) sendIcon.style.display = 'block';
            if (loadingIcon) loadingIcon.style.display = 'none';
        });
    }

    function deleteMessage(id) {
        if(confirm('Xóa tin nhắn này đối với mọi người?')) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch(`/messages/{{ $conversation->id }}/messages/${id}`, {
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': token || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(() => location.reload());
        }
    }

    // Tự động điều chỉnh chiều cao textarea
    document.getElementById('mainChatInput')?.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
</script>
@endsection
