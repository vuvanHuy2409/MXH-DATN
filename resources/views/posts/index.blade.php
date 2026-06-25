@extends('layouts.app')

@section('content')
<style>
    /* ── Create post card ── */
    .create-post-card {
        margin: 10px 0 22px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 22px;
        box-shadow: var(--glass-shadow);
        overflow: hidden;
        transition: box-shadow 0.25s, transform 0.25s;
    }
    .create-post-card:hover {
        box-shadow: 0 12px 32px rgba(0,98,255,0.08), 0 0 0 1px rgba(0,98,255,0.1);
        transform: translateY(-1px);
    }
    [data-theme="dark"] .create-post-card {
        background: rgba(18,20,34,0.82);
        border-color: rgba(255,255,255,0.07);
    }
    [data-theme="dark"] .create-post-card:hover {
        box-shadow: 0 12px 32px rgba(0,0,0,0.4), 0 0 0 1px rgba(77,148,255,0.2);
    }

    .create-post-top {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px 14px;
        cursor: pointer;
    }
    .create-post-avatar {
        width: 44px; height: 44px;
        border-radius: 14px;
        background-size: cover;
        background-position: center;
        flex-shrink: 0;
        border: 2px solid rgba(255,255,255,0.8);
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    .create-post-placeholder {
        flex-grow: 1;
        background: rgba(0,0,0,0.04);
        border-radius: 12px;
        padding: 11px 16px;
        font-size: 14.5px;
        font-weight: 500;
        color: var(--secondary-text);
        transition: background 0.2s;
    }
    .create-post-top:hover .create-post-placeholder { background: rgba(0,98,255,0.05); }
    [data-theme="dark"] .create-post-placeholder { background: rgba(255,255,255,0.05); }
    [data-theme="dark"] .create-post-top:hover .create-post-placeholder { background: rgba(77,148,255,0.08); }

    .create-post-btn {
        background: #0062FF;
        color: #fff;
        padding: 9px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13.5px;
        flex-shrink: 0;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(0,98,255,0.3);
        transition: all 0.2s;
    }
    .create-post-btn:hover { background: #0052D9; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,98,255,0.4); }

    .create-post-actions {
        display: flex;
        gap: 2px;
        padding: 4px 14px 10px;
        border-top: 1px solid var(--glass-border);
    }
    [data-theme="dark"] .create-post-actions { border-top-color: rgba(255,255,255,0.05); }

    .cp-action-btn {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: var(--secondary-text);
        cursor: pointer;
        transition: all 0.18s;
        border: none;
        background: transparent;
        font-family: inherit;
    }
    .cp-action-btn:hover { background: rgba(0,98,255,0.06); color: #0062FF; }
    [data-theme="dark"] .cp-action-btn:hover { background: rgba(77,148,255,0.1); color: #4D94FF; }

    /* ── Feed tabs ── */
    .feed-tabs {
        display: flex;
        gap: 4px;
        margin: 6px 0 20px;
        background: rgba(0,0,0,0.03);
        padding: 4px;
        border-radius: 16px;
        width: fit-content;
    }
    [data-theme="dark"] .feed-tabs { background: rgba(255,255,255,0.04); }

    .tab-item {
        padding: 8px 22px;
        cursor: pointer;
        font-weight: 700;
        font-size: 13.5px;
        border-radius: 12px;
        transition: all 0.25s ease;
        color: var(--secondary-text);
    }
    .tab-item.active {
        background: white;
        color: var(--text-color);
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
    }
    [data-theme="dark"] .tab-item.active {
        background: rgba(77, 148, 255, 0.14);
        color: #4D94FF;
        box-shadow: 0 3px 12px rgba(77, 148, 255, 0.18);
    }

    /* Glassmorphic spinner */
    .feed-loading-spinner {
        display: none;
        justify-content: center;
        align-items: center;
        min-height: 80px;
        opacity: 0.85;
    }
    .glass-spinner {
        width: 36px;
        height: 36px;
        border: 4px solid var(--glass-border);
        border-top-color: var(--accent-color);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        box-shadow: 0 4px 15px rgba(0, 98, 255, 0.1);
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @media (min-width: 1280px) {
        .home-sidebar-right {
            position: fixed;
            right: 30px;
            top: 90px;
            width: 310px;
            max-height: calc(100vh - 130px);
            overflow-y: auto;
            z-index: 999;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .home-sidebar-right::-webkit-scrollbar {
            display: none;
        }
        .home-main-content {
            width: 100%;
        }
    }
    @media (max-width: 1279px) {
        .home-sidebar-right {
            display: none;
        }
        .home-main-content {
            width: 100%;
        }
    }
    .home-sidebar-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: var(--glass-shadow);
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.25s;
        position: relative;
    }
    .home-sidebar-card:hover {
        box-shadow: 0 12px 32px rgba(0,98,255,0.04);
    }
    [data-theme="dark"] .home-sidebar-card {
        background: rgba(18,20,34,0.82);
        border-color: rgba(255,255,255,0.07);
    }
    .sidebar-title {
        font-size: 15px;
        font-weight: 800;
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-color);
        letter-spacing: -0.3px;
    }
    .sidebar-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .sidebar-item {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
        padding: 8px 12px;
        border-radius: 16px;
        transition: all 0.2s;
        border: 1.5px solid transparent;
    }
    .sidebar-item:hover {
        background: rgba(0, 98, 255, 0.05);
        border-color: rgba(0, 98, 255, 0.1);
        transform: translateX(2px);
    }
    [data-theme="dark"] .sidebar-item:hover {
        background: rgba(77, 148, 255, 0.08);
        border-color: rgba(77, 148, 255, 0.15);
    }
    .sidebar-item-avatar {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        background-size: cover;
        background-position: center;
        flex-shrink: 0;
        border: 1.5px solid rgba(255,255,255,0.8);
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    [data-theme="dark"] .sidebar-item-avatar {
        border-color: rgba(255,255,255,0.08);
    }
    .sidebar-item-info {
        flex-grow: 1;
        min-width: 0;
    }
    .sidebar-item-name {
        font-weight: 700;
        font-size: 13.5px;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--text-color);
    }
    .sidebar-item-sub {
        font-size: 11.5px;
        color: var(--secondary-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sidebar-empty {
        font-size: 13px;
        color: var(--secondary-text);
        text-align: center;
        padding: 10px 0;
        opacity: 0.7;
    }
    
    /* Scrollable lists in sidebar */
    .sidebar-scroll-list {
        max-height: 312px; /* 6 items * 52px = 312px */
        overflow-y: auto;
        padding-right: 4px;
        -ms-overflow-style: none;
        scrollbar-width: thin;
    }
    
    /* Unread badge & Styling */
    .sidebar-item-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }
    .sidebar-unread-badge {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: var(--accent-color);
        border: 2px solid var(--glass-bg);
    }
    .sidebar-unread .sidebar-item-name {
        font-weight: 800;
        color: var(--accent-color);
    }
    .sidebar-unread .sidebar-item-sub {
        font-weight: 700;
        color: var(--text-color);
    }

    /* Mini Chat Styles */
    .mini-chat-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--glass-border);
        margin-bottom: 10px;
    }
    .mini-chat-back-btn {
        background: transparent;
        border: none;
        color: var(--secondary-text);
        cursor: pointer;
        padding: 4px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, color 0.2s;
    }
    .mini-chat-back-btn:hover {
        background: rgba(0, 98, 255, 0.08);
        color: var(--accent-color);
    }
    .mini-chat-user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        background-size: cover;
        background-position: center;
        border: 1.5px solid rgba(255,255,255,0.8);
    }
    .mini-chat-user-name {
        font-weight: 800;
        font-size: 13.5px;
        color: var(--text-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex-grow: 1;
    }
    .mini-chat-messages {
        height: 220px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-right: 4px;
        margin-bottom: 10px;
    }
    .mini-message {
        max-width: 85%;
        padding: 8px 12px;
        border-radius: 14px;
        font-size: 12.5px;
        line-height: 1.4;
        word-break: break-word;
    }
    .mini-message.me {
        align-self: flex-end;
        background: var(--accent-color);
        color: #fff;
        border-bottom-right-radius: 4px;
    }
    .mini-message.other {
        align-self: flex-start;
        background: rgba(0, 0, 0, 0.05);
        color: var(--text-color);
        border-bottom-left-radius: 4px;
    }
    [data-theme="dark"] .mini-message.other {
        background: rgba(255, 255, 255, 0.06);
    }
    #mini-chat-form {
        display: flex;
        gap: 6px;
        border-top: 1px solid var(--glass-border);
        padding-top: 10px;
        width: 100%;
        box-sizing: border-box;
        align-items: center;
    }
    #mini-chat-input {
        flex-grow: 1;
        min-width: 0;
        background: rgba(0, 0, 0, 0.03);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 8px 12px;
        font-size: 12.5px;
        color: var(--text-color);
        outline: none;
        box-sizing: border-box;
    }
    [data-theme="dark"] #mini-chat-input {
        background: rgba(255, 255, 255, 0.04);
    }
    .mini-chat-send-btn {
        background: transparent;
        border: 1.5px solid var(--glass-border);
        color: var(--secondary-text);
        padding: 6px 14px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 12.5px;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
        font-family: inherit;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mini-chat-send-btn:hover {
        background: rgba(0, 98, 255, 0.05);
        border-color: rgba(0, 98, 255, 0.2);
        color: var(--accent-color);
    }
    [data-theme="dark"] .mini-chat-send-btn {
        border-color: rgba(255, 255, 255, 0.15);
        color: #eeeef0;
    }
    [data-theme="dark"] .mini-chat-send-btn:hover {
        background: rgba(77, 148, 255, 0.15);
        border-color: rgba(77, 148, 255, 0.4);
        color: #4D94FF;
    }
    
    .sidebar-see-more {
        opacity: 0.9;
        transition: opacity 0.2s, background 0.2s;
    }
    .sidebar-see-more:hover {
        opacity: 1;
    }
    .see-more-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 98, 255, 0.1) !important;
        color: var(--accent-color);
        border: 1.5px solid rgba(0, 98, 255, 0.2) !important;
    }
    .text-accent {
        color: var(--accent-color) !important;
    }
    
    /* Plus Button */
    .mini-chat-plus-btn {
        background: transparent;
        border: 1px solid var(--glass-border);
        color: var(--secondary-text);
        border-radius: 12px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s, color 0.2s;
    }
    .mini-chat-plus-btn:hover {
        background: rgba(0, 98, 255, 0.08);
        color: var(--accent-color);
        transform: scale(1.05);
    }

    /* Popover action menu */
    .mini-chat-popover-menu {
        position: absolute;
        bottom: 55px;
        left: 20px;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: var(--glass-shadow);
        display: flex;
        flex-direction: column;
        padding: 6px;
        gap: 2px;
        z-index: 1010;
        animation: fadeIn 0.15s ease-out;
    }
    [data-theme="dark"] .mini-chat-popover-menu {
        background: rgba(22, 22, 32, 0.96);
    }
    .mini-menu-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        border: none;
        color: var(--text-color);
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        border-radius: 10px;
        text-align: left;
        width: 140px;
        transition: background-color 0.2s;
    }
    .mini-menu-item:hover {
        background: rgba(0, 98, 255, 0.08);
        color: var(--accent-color);
    }
    [data-theme="dark"] .mini-menu-item:hover {
        background: rgba(77, 148, 255, 0.12);
        color: #4D94FF;
    }

    /* Mini Appointment Styles */
    .mini-appointment-container {
        padding: 10px 4px;
        animation: slideUp 0.25s ease-out;
    }
    .mini-appointment-header {
        font-weight: 800;
        font-size: 14px;
        margin-bottom: 12px;
        color: var(--text-color);
        border-bottom: 1px solid var(--glass-border);
        padding-bottom: 8px;
    }
    .mini-form-group {
        margin-bottom: 8px;
    }
    .mini-form-group label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 3px;
        color: var(--secondary-text);
    }
    .mini-form-group input {
        width: 100%;
        box-sizing: border-box;
        background: rgba(0, 0, 0, 0.03);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 12px;
        color: var(--text-color);
        outline: none;
    }
    [data-theme="dark"] .mini-form-group input {
        background: rgba(255, 255, 255, 0.04);
    }
    .mini-appointment-actions {
        display: flex;
        gap: 6px;
        margin-top: 12px;
        justify-content: flex-end;
    }
    .mini-btn-cancel {
        background: transparent;
        border: 1px solid var(--glass-border);
        color: var(--secondary-text);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .mini-btn-submit {
        background: var(--accent-color);
        border: none;
        color: #fff;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .mini-btn-submit:hover {
        background: #0052D9;
    }
    
    .mini-chat-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }
    .mini-chat-header-action-btn {
        color: var(--secondary-text);
        transition: color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mini-chat-header-action-btn:hover {
        color: var(--accent-color);
    }
</style>

<div class="home-layout">
    <!-- Main Feed Content -->
    <div class="home-main-content">
        <div style="padding: 0 10px;">
            <!-- Create Post Card -->
            <div class="create-post-card">
                <div class="create-post-top" onclick="openModal()">
                    <div class="create-post-avatar" style="background-image: url('{{ auth()->user()->avatar_url }}')"></div>
                    <div class="create-post-placeholder">Có gì mới hôm nay, {{ explode(' ', auth()->user()->studentDetail->full_name ?? auth()->user()->username)[0] }}?</div>
                    <button class="create-post-btn" type="button" onclick="event.stopPropagation(); openModal()">Đăng</button>
                </div>
                <div class="create-post-actions">
                    <button class="cp-action-btn" onclick="openModal('media')" type="button">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                        </svg>
                        Ảnh/Video
                    </button>
                    <button class="cp-action-btn" onclick="openModal('file')" type="button">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/>
                        </svg>
                        Tài liệu
                    </button>
                    <button class="cp-action-btn" onclick="openModal('link')" type="button">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                        Link
                    </button>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="feed-tabs">
                <div id="tab-foryou" onclick="switchTab('foryou')" class="tab-item active">Dành cho bạn</div>
                <div id="tab-following" onclick="switchTab('following')" class="tab-item">Đang theo dõi</div>
            </div>

            <!-- Tab Content: Dành cho bạn -->
            <div id="content-foryou">
                <div class="feed-posts">
                    @forelse($posts as $post)
                        @include('posts._item', ['post' => $post, 'prefix' => 'fy'])
                    @empty
                    <p class="no-posts-text" style="text-align: center; padding: 50px; opacity: 0.5;">Chưa có bài viết nào.</p>
                    @endforelse
                </div>
                <div id="loading-foryou" class="feed-loading-spinner">
                    <div class="glass-spinner"></div>
                </div>
            </div>

            <!-- Tab Content: Đang theo dõi -->
            <div id="content-following" style="display: none;">
                <div class="feed-posts">
                    @forelse($followingPosts as $post)
                        @include('posts._item', ['post' => $post, 'prefix' => 'fl'])
                    @empty
                    <p class="no-posts-text" style="text-align: center; padding: 50px; opacity: 0.5;">Theo dõi thêm bạn bè để xem bài viết.</p>
                    @endforelse
                </div>
                <div id="loading-following" class="feed-loading-spinner">
                    <div class="glass-spinner"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar (Shortcuts) -->
    <div class="home-sidebar-right">
        <!-- Message Shortcuts Card -->
        <div class="home-sidebar-card" id="sidebar-message-card">
            <!-- Header for Conversation List -->
            <div id="sidebar-message-list-header">
                <div class="sidebar-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                    Lối tắt tin nhắn
                </div>
            </div>

            <!-- List of Conversations -->
            <div id="sidebar-message-list" class="sidebar-scroll-list">
                @forelse($conversations as $conv)
                    @php
                        $other = $conv->users->where('id', '!=', auth()->id())->first();
                        $convName = $other ? ($other->student->full_name ?? ($other->teacher->full_name ?? $other->username)) : 'Người dùng';
                        $convAvatar = $other ? $other->avatar_url : '/avatars/default_avatar.jpg';
                        $lastMsgText = 'Trò chuyện trực tiếp';
                        if ($conv->lastMessage) {
                            $lastMsgText = $conv->lastMessage->message_type === 'text' 
                                ? \Illuminate\Support\Str::limit($conv->lastMessage->content, 22) 
                                : ($conv->lastMessage->message_type === 'image' ? '📷 Đã gửi ảnh' : ($conv->lastMessage->message_type === 'system' ? '🔔 Lịch hẹn' : '📎 Đã gửi tệp'));
                        }
                        $isUnread = $conv->lastMessage && !$conv->lastMessage->is_read && $conv->lastMessage->sender_id !== auth()->id();
                    @endphp
                    <div class="sidebar-item conversation-shortcut-item {{ $isUnread ? 'sidebar-unread' : '' }}" 
                         data-conversation-id="{{ $conv->id }}"
                         data-name="{{ $convName }}"
                         data-avatar="{{ $convAvatar }}"
                         style="cursor: pointer;">
                        <div class="sidebar-item-avatar-wrapper">
                            <div class="sidebar-item-avatar" style="background-image: url('{{ $convAvatar }}')"></div>
                            @if($isUnread)
                                <div class="sidebar-unread-badge"></div>
                            @endif
                        </div>
                        <div class="sidebar-item-info">
                            <div class="sidebar-item-name">{{ $convName }}</div>
                            <div class="sidebar-item-sub" id="conv-last-msg-{{ $conv->id }}">{{ $lastMsgText }}</div>
                        </div>
                    </div>
                @empty
                    <div class="sidebar-empty">Chưa có cuộc trò chuyện nào.</div>
                @endforelse
            </div>

            <!-- Mini Chat View Container -->
            <div id="sidebar-mini-chat" style="display: none;">
                <!-- Header with Back Button and Name -->
                <div class="mini-chat-header">
                    <button class="mini-chat-back-btn" onclick="closeMiniChat()">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                    </button>
                    <div class="mini-chat-user-avatar" id="mini-chat-avatar"></div>
                    <div class="mini-chat-user-name" id="mini-chat-name"></div>
                    <div class="mini-chat-header-actions">
                        <a id="mini-chat-open-full" href="#" class="mini-chat-header-action-btn" title="Mở trang tin nhắn đầy đủ">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2 2V8a2 2 0 0 1 2-2h6"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="mini-chat-messages" id="mini-chat-messages-container">
                </div>

                <!-- Mini Appointment Panel -->
                <div id="mini-appointment-panel" style="display: none;" class="mini-appointment-container">
                    <div class="mini-appointment-header">Đặt lịch hẹn</div>
                    <form id="mini-appointment-form" onsubmit="submitMiniAppointment(event)">
                        <div class="mini-form-group">
                            <label>Tiêu đề *</label>
                            <input type="text" id="mini-appt-title" required placeholder="Ví dụ: Họp nhóm">
                        </div>
                        <div class="mini-form-group">
                            <label>Thời gian *</label>
                            <input type="datetime-local" id="mini-appt-time" required>
                        </div>
                        <div class="mini-form-group">
                            <label>Địa điểm</label>
                            <input type="text" id="mini-appt-location" placeholder="Ví dụ: Phòng A101">
                        </div>
                        <div class="mini-form-group">
                            <label>Ghi chú</label>
                            <input type="text" id="mini-appt-description" placeholder="Ví dụ: Mang theo laptop">
                        </div>
                        <div class="mini-appointment-actions">
                            <button type="button" class="mini-btn-cancel" onclick="closeMiniAppointment()">Hủy</button>
                            <button type="submit" class="mini-btn-submit">Lên lịch</button>
                        </div>
                    </form>
                </div>

                <!-- Input area -->
                <form id="mini-chat-form" onsubmit="sendMiniChatMessage(event)">
                    <button type="button" class="mini-chat-plus-btn" onclick="toggleMiniChatMenu(event)">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                    <input type="text" id="mini-chat-input" placeholder="Nhập tin nhắn..." autocomplete="off">
                    <button type="submit" class="mini-chat-send-btn">
                        Gửi
                    </button>
                </form>

                <!-- Popover action menu -->
                <div id="mini-chat-action-menu" class="mini-chat-popover-menu" style="display: none;">
                    <button type="button" class="mini-menu-item" onclick="triggerMiniImageUpload()">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        Gửi ảnh
                    </button>
                    <button type="button" class="mini-menu-item" onclick="triggerMiniFileUpload()">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                            <polyline points="13 2 13 9 20 9"/>
                        </svg>
                        Gửi tệp
                    </button>
                    <button type="button" class="mini-menu-item" onclick="openMiniAppointmentModal()">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Đặt lịch hẹn
                    </button>
                </div>

                <!-- Hidden inputs for file upload -->
                <input type="file" id="mini-chat-image-input" style="display: none;" accept="image/*" onchange="uploadMiniImage(this)">
                <input type="file" id="mini-chat-file-input" style="display: none;" onchange="uploadMiniFile(this)">
            </div>
        </div>

        <!-- Community Shortcuts Card -->
        <div class="home-sidebar-card">
            <div class="sidebar-title">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Lối tắt cộng đồng
            </div>
            <div class="sidebar-scroll-list">
                @php 
                    $hasCommunity = false;
                    $allCommunities = collect();
                    foreach($joinedCommunities as $group) {
                        $allCommunities->push(['group' => $group, 'type' => 'joined']);
                    }
                    if ($joinedCommunities->count() < 6) {
                        foreach($suggestedCommunities as $group) {
                            $allCommunities->push(['group' => $group, 'type' => 'suggested']);
                        }
                    }
                @endphp
                @foreach($allCommunities as $item)
                    @php 
                        $group = $item['group'];
                        $type = $item['type'];
                        $hasCommunity = true;
                    @endphp
                    <a href="{{ route('groups.index', $group->slug) }}" class="sidebar-item">
                        <div class="sidebar-item-avatar" style="background-image: url('{{ $group->avatar_url ?? '/uploads/groups/community-default.png' }}')"></div>
                        <div class="sidebar-item-info">
                            <div class="sidebar-item-name">{{ $group->name }}</div>
                            <div class="sidebar-item-sub">
                                {{ $type === 'joined' ? Str::limit($group->description, 28) : 'Được đề xuất' }}
                            </div>
                        </div>
                    </a>
                @endforeach
                
                @if($hasCommunity)
                    <a href="{{ route('groups.index') }}" class="sidebar-item sidebar-see-more">
                        <div class="sidebar-item-avatar see-more-avatar">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 8 16 12 12 16"></polyline>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                        </div>
                        <div class="sidebar-item-info">
                            <div class="sidebar-item-name text-accent">Xem thêm cộng đồng</div>
                            <div class="sidebar-item-sub">Khám phá tất cả các nhóm</div>
                        </div>
                    </a>
                @endif

                @if(!$hasCommunity)
                    <div class="sidebar-empty">Chưa có cộng đồng nào.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    let activeMiniConversationId = null;
    let miniChatInterval = null;
    let lastMiniMessageId = 0;

    // Attach click listeners to shortcut items
    document.addEventListener('DOMContentLoaded', function() {
        const listContainer = document.getElementById('sidebar-message-list');
        if (listContainer) {
            listContainer.addEventListener('click', function(e) {
                const item = e.target.closest('.conversation-shortcut-item');
                if (item) {
                    e.preventDefault();
                    const conversationId = item.getAttribute('data-conversation-id');
                    const name = item.getAttribute('data-name');
                    const avatar = item.getAttribute('data-avatar');
                    openMiniChat(conversationId, name, avatar, item);
                }
            });
        }
    });

    function openMiniChat(conversationId, name, avatar, itemElement) {
        activeMiniConversationId = conversationId;
        lastMiniMessageId = 0;
        
        // Remove unread indicators if any
        if (itemElement) {
            itemElement.classList.remove('sidebar-unread');
            const badge = itemElement.querySelector('.sidebar-unread-badge');
            if (badge) badge.remove();
        }

        // Setup Header UI
        document.getElementById('mini-chat-name').innerText = name;
        document.getElementById('mini-chat-avatar').style.backgroundImage = `url('${avatar}')`;
        document.getElementById('mini-chat-open-full').href = `/messages/${conversationId}`;

        // Switch View
        document.getElementById('sidebar-message-list-header').style.display = 'none';
        document.getElementById('sidebar-message-list').style.display = 'none';
        document.getElementById('sidebar-mini-chat').style.display = 'block';

        // Clear and load initial messages
        const msgContainer = document.getElementById('mini-chat-messages-container');
        msgContainer.innerHTML = '<div class="sidebar-empty">Đang tải tin nhắn...</div>';
        
        loadMiniChatMessages(conversationId, true);

        // Start polling for new messages
        if (miniChatInterval) clearInterval(miniChatInterval);
        miniChatInterval = setInterval(function() {
            loadMiniChatMessages(conversationId, false);
        }, 3000);
    }

    function closeMiniChat() {
        if (miniChatInterval) {
            clearInterval(miniChatInterval);
            miniChatInterval = null;
        }
        activeMiniConversationId = null;
        
        // Close menu & appointment panel if open
        closeMiniChatMenu();
        closeMiniAppointment();
        
        // Switch back View
        document.getElementById('sidebar-mini-chat').style.display = 'none';
        document.getElementById('sidebar-message-list-header').style.display = 'block';
        document.getElementById('sidebar-message-list').style.display = 'block';
    }

    function loadMiniChatMessages(conversationId, isInitial) {
        if (activeMiniConversationId !== conversationId) return;

        let url = `/api/chat/${conversationId}/messages`;
        if (!isInitial && lastMiniMessageId > 0) {
            url += `?since=${lastMiniMessageId}`;
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(messages => {
            if (activeMiniConversationId !== conversationId) return;
            
            const msgContainer = document.getElementById('mini-chat-messages-container');
            if (isInitial) {
                msgContainer.innerHTML = '';
            }

            if (messages.length > 0) {
                messages.forEach(msg => {
                    renderSingleMiniMessage(msg);
                });
            } else if (isInitial) {
                msgContainer.innerHTML = '<div class="sidebar-empty">Chưa có tin nhắn nào. Bắt đầu trò chuyện!</div>';
            }
        })
        .catch(err => {
            console.error('Lỗi tải tin nhắn lối tắt:', err);
            if (isInitial) {
                const msgContainer = document.getElementById('mini-chat-messages-container');
                msgContainer.innerHTML = '<div class="sidebar-empty">Không thể tải tin nhắn.</div>';
            }
        });
    }

    function renderSingleMiniMessage(msg) {
        const msgContainer = document.getElementById('mini-chat-messages-container');
        // Prevent duplicates
        if (document.getElementById(`mini-msg-${msg.id}`)) return;

        // Remove empty placeholder if any
        const emptyEl = msgContainer.querySelector('.sidebar-empty');
        if (emptyEl) emptyEl.remove();

        const msgDiv = document.createElement('div');
        msgDiv.id = `mini-msg-${msg.id}`;
        msgDiv.classList.add('mini-message');
        msgDiv.classList.add(msg.sender_id === {{ auth()->id() }} ? 'me' : 'other');
        
        if (msg.message_type === 'text') {
            msgDiv.textContent = msg.content;
        } else if (msg.message_type === 'image') {
            const src = msg.content.startsWith('storage/') ? `/${msg.content}` : `/${msg.content}`;
            msgDiv.innerHTML = `<img src="${src}" style="max-width: 100%; border-radius: 8px;" />`;
        } else if (msg.message_type === 'system') {
            msgDiv.innerHTML = msg.content;
            msgDiv.classList.remove('me', 'other');
            msgDiv.classList.add('other');
            msgDiv.style.fontStyle = 'italic';
            msgDiv.style.opacity = '0.85';
            msgDiv.style.background = 'rgba(0, 0, 0, 0.03)';
            msgDiv.style.border = '1px dashed var(--glass-border)';
            msgDiv.style.maxWidth = '90%';
        } else {
            const src = msg.content.startsWith('storage/') ? `/${msg.content}` : `/${msg.content}`;
            msgDiv.innerHTML = `<a href="${src}" target="_blank" style="color: inherit; text-decoration: underline; display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                Tệp đính kèm
            </a>`;
        }
        
        msgContainer.appendChild(msgDiv);
        msgContainer.scrollTop = msgContainer.scrollHeight;
        if (msg.id > lastMiniMessageId) {
            lastMiniMessageId = msg.id;
        }

        // Dynamically update the outer conversation list's last message text in real time
        if (activeMiniConversationId) {
            const outerLastMsgEl = document.getElementById(`conv-last-msg-${activeMiniConversationId}`);
            if (outerLastMsgEl) {
                let summaryText = 'Trò chuyện trực tiếp';
                if (msg.message_type === 'text') {
                    summaryText = msg.content.length > 22 ? msg.content.substring(0, 22) + '...' : msg.content;
                } else if (msg.message_type === 'image') {
                    summaryText = '📷 Đã gửi ảnh';
                } else if (msg.message_type === 'system') {
                    summaryText = '🔔 Lịch hẹn';
                } else {
                    summaryText = '📎 Đã gửi tệp';
                }
                outerLastMsgEl.textContent = summaryText;
            }
        }
    }

    function sendMiniChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('mini-chat-input');
        const text = input.value.trim();
        if (!text || !activeMiniConversationId) return;

        input.value = '';
        input.focus();

        const formData = new FormData();
        formData.append('content', text);
        formData.append('message_type', 'text');

        fetch(`/messages/${activeMiniConversationId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(msg => {
            if (msg.error) {
                alert(msg.error);
                return;
            }
            renderSingleMiniMessage(msg);
        })
        .catch(err => {
            console.error('Lỗi gửi tin nhắn lối tắt:', err);
            alert('Không thể gửi tin nhắn. Vui lòng thử lại.');
        });
    }

    /* Menu and Popovers JS Actions */
    function toggleMiniChatMenu(e) {
        e.stopPropagation();
        const menu = document.getElementById('mini-chat-action-menu');
        const isHidden = menu.style.display === 'none';
        
        if (isHidden) {
            menu.style.display = 'flex';
            document.addEventListener('click', documentClickCloseMenu);
        } else {
            closeMiniChatMenu();
        }
    }
    
    function closeMiniChatMenu() {
        const menu = document.getElementById('mini-chat-action-menu');
        if (menu) menu.style.display = 'none';
        document.removeEventListener('click', documentClickCloseMenu);
    }
    
    function documentClickCloseMenu(e) {
        const menu = document.getElementById('mini-chat-action-menu');
        const btn = document.querySelector('.mini-chat-plus-btn');
        if (menu && !menu.contains(e.target) && !btn.contains(e.target)) {
            closeMiniChatMenu();
        }
    }

    function triggerMiniImageUpload() {
        closeMiniChatMenu();
        document.getElementById('mini-chat-image-input').click();
    }
    function triggerMiniFileUpload() {
        closeMiniChatMenu();
        document.getElementById('mini-chat-file-input').click();
    }
    
    function uploadMiniImage(input) {
        const file = input.files[0];
        if (!file || !activeMiniConversationId) return;
        
        appendMiniSystemMessage('Đang gửi ảnh...');
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('message_type', 'image');
        
        sendMiniMediaMessage(formData);
        input.value = '';
    }
    
    function uploadMiniFile(input) {
        const file = input.files[0];
        if (!file || !activeMiniConversationId) return;
        
        appendMiniSystemMessage('Đang gửi tệp...');
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('message_type', 'file');
        
        sendMiniMediaMessage(formData);
        input.value = '';
    }
    
    function sendMiniMediaMessage(formData) {
        fetch(`/messages/${activeMiniConversationId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(msg => {
            if (msg.error) {
                alert(msg.error);
                return;
            }
            removeMiniSystemMessages();
            renderSingleMiniMessage(msg);
        })
        .catch(err => {
            console.error('Lỗi gửi media:', err);
            removeMiniSystemMessages();
            alert('Gửi tệp thất bại.');
        });
    }
    
    function appendMiniSystemMessage(text) {
        const msgContainer = document.getElementById('mini-chat-messages-container');
        const div = document.createElement('div');
        div.classList.add('mini-message', 'other', 'mini-system-indicator');
        div.style.fontStyle = 'italic';
        div.style.opacity = '0.7';
        div.textContent = text;
        msgContainer.appendChild(div);
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }
    
    function removeMiniSystemMessages() {
        document.querySelectorAll('.mini-system-indicator').forEach(el => el.remove());
    }

    function openMiniAppointmentModal() {
        closeMiniChatMenu();
        document.getElementById('mini-chat-messages-container').style.display = 'none';
        document.getElementById('mini-chat-form').style.display = 'none';
        document.getElementById('mini-appointment-panel').style.display = 'block';
    }

    function closeMiniAppointment() {
        const panel = document.getElementById('mini-appointment-panel');
        if (!panel) return;
        panel.style.display = 'none';
        
        const msgs = document.getElementById('mini-chat-messages-container');
        const form = document.getElementById('mini-chat-form');
        if (msgs) msgs.style.display = 'flex';
        if (form) form.style.display = 'flex';
        
        document.getElementById('mini-appt-title').value = '';
        document.getElementById('mini-appt-time').value = '';
        document.getElementById('mini-appt-location').value = '';
        document.getElementById('mini-appt-description').value = '';
    }

    function submitMiniAppointment(e) {
        e.preventDefault();
        const title = document.getElementById('mini-appt-title').value.trim();
        const time = document.getElementById('mini-appt-time').value;
        const location = document.getElementById('mini-appt-location').value.trim();
        const description = document.getElementById('mini-appt-description').value.trim();

        if (!title || !time || !activeMiniConversationId) return;

        const formData = new FormData();
        formData.append('title', title);
        formData.append('appointment_time', time);
        if (location) formData.append('location', location);
        if (description) formData.append('description', description);

        fetch(`/messages/${activeMiniConversationId}/appointments`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                closeMiniAppointment();
                loadMiniChatMessages(activeMiniConversationId, false);
            } else {
                alert('Tạo lịch hẹn thất bại.');
            }
        })
        .catch(err => {
            console.error('Lỗi tạo lịch hẹn:', err);
            alert('Tạo lịch hẹn thất bại. Vui lòng kiểm tra lại thời gian.');
        });
    }

    function openModal(type) {
        document.getElementById('postModal').style.display = 'flex';
        document.body.classList.add('modal-open');
        
        if (type === 'link') {
            toggleLinkInput(true);
        } else if (type === 'media') {
            document.getElementById('mediaInput').click();
        } else if (type === 'file') {
            document.getElementById('fileInput').click();
        }
    }

    // Infinite Scroll Pagination State
    let activeTab = 'foryou';
    let foryouPage = 1;
    let followingPage = 1;
    let foryouHasMore = @json($posts->count() === 15);
    let followingHasMore = @json($followingPosts->count() === 15);
    let isLoading = false;

    function switchTab(tab) {
        activeTab = tab;
        document.getElementById('content-foryou').style.display = tab === 'foryou' ? 'block' : 'none';
        document.getElementById('content-following').style.display = tab === 'following' ? 'block' : 'none';
        
        const t1 = document.getElementById('tab-foryou');
        const t2 = document.getElementById('tab-following');
        
        if (tab === 'foryou') {
            t1.classList.add('active');
            t2.classList.remove('active');
        } else {
            t2.classList.add('active');
            t1.classList.remove('active');
        }

        // Trigger check load immediately if window is not scrollable yet after switching
        checkScroll();
    }

    function checkScroll() {
        if (isLoading) return;

        let hasMore = (activeTab === 'foryou') ? foryouHasMore : followingHasMore;
        if (!hasMore) return;

        // Check if user is scrolled near the bottom (300px from the bottom)
        const scrollPosition = window.innerHeight + window.scrollY;
        const pageHeight = document.documentElement.scrollHeight;
        
        if (pageHeight - scrollPosition < 300) {
            loadMorePosts();
        }
    }

    // Attach scroll event listener
    window.addEventListener('scroll', throttle(checkScroll, 200));
    window.addEventListener('resize', throttle(checkScroll, 200));

    // Throttle utility
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }

    function loadMorePosts() {
        if (isLoading) return;
        isLoading = true;

        const nextPage = (activeTab === 'foryou') ? foryouPage + 1 : followingPage + 1;
        const loadingElement = document.getElementById(`loading-${activeTab}`);
        
        if (loadingElement) {
            loadingElement.style.display = 'flex';
        }

        fetch(`/?tab=${activeTab}&page=${nextPage}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (activeTab === 'foryou') {
                foryouPage = nextPage;
                foryouHasMore = data.hasMore;
                
                const container = document.querySelector('#content-foryou .feed-posts');
                if (data.html && data.html.trim() !== '') {
                    // Remove no-posts text if it was there
                    const noPostsText = container.querySelector('.no-posts-text');
                    if (noPostsText) noPostsText.remove();
                    
                    container.insertAdjacentHTML('beforeend', data.html);
                }
            } else {
                followingPage = nextPage;
                followingHasMore = data.hasMore;

                const container = document.querySelector('#content-following .feed-posts');
                if (data.html && data.html.trim() !== '') {
                    const noPostsText = container.querySelector('.no-posts-text');
                    if (noPostsText) noPostsText.remove();

                    container.insertAdjacentHTML('beforeend', data.html);
                }
            }
        })
        .catch(err => {
            console.error('Error fetching more posts:', err);
        })
        .finally(() => {
            isLoading = false;
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
            // Check scroll again in case the new content still doesn't fill the viewport
            checkScroll();
        });
    }

    // Trigger an initial check in case page is very tall / screen is very large
    document.addEventListener('DOMContentLoaded', () => {
        checkScroll();
    });

    function toggleDropdown(id) {
        const dropdown = document.getElementById("dropdown-" + id);
        if (dropdown) dropdown.classList.toggle("show");
    }
</script>
@endsection

@section('extra_content')
<!-- SIDE PANEL FOR COMMENTS -->
<div id="commentSidePanel" class="comment-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.2); backdrop-filter: blur(10px); justify-content: flex-end;">
    <div class="glass-bubble" style="width: 100%; max-width: 500px; height: 100%; border-radius: 35px 0 0 35px; display: flex; flex-direction: column; overflow: hidden; animation: slideLeft 0.4s cubic-bezier(0.16, 1, 0.3, 1);">
        <!-- Header -->
        <div style="padding: 25px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h3 style="margin: 0; font-size: 20px; font-weight: 800;">Bình luận</h3>
            <div onclick="closeCommentSidePanel()" style="cursor: pointer; width: 36px; height: 36px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.1)'" onmouseout="this.style.background='rgba(0,0,0,0.05)'">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </div>
        </div>

        <!-- Source Post Info (Mini) -->
        <div style="padding: 20px 25px; background: rgba(0,0,0,0.02); display: flex; gap: 12px; align-items: flex-start; border-bottom: 1px solid var(--glass-border);">
            <div id="panelSourceAvatar" class="avatar" style="width: 32px; height: 32px; background-size: cover; border-radius: 50%; flex-shrink: 0;"></div>
            <div style="flex-grow: 1; min-width: 0;">
                <div id="panelSourceUsername" style="font-weight: 800; font-size: 14px; margin-bottom: 2px;"></div>
                <div id="panelSourceContent" style="font-size: 13px; opacity: 0.8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
            </div>
        </div>

        <!-- Comments List -->
        <div id="panelActualComments" style="flex-grow: 1; overflow-y: auto; padding: 25px; display: flex; flex-direction: column; gap: 5px;">
            <!-- Comments will be rendered here -->
        </div>

        <!-- Image Preview for Panel -->
        <div id="panelImagePreviewContainer" style="display: none; padding: 10px 25px; background: rgba(0,0,0,0.02); position: relative;">
            <img id="panelImagePreview" src="" style="max-height: 80px; border-radius: 8px;">
            <span onclick="removePanelImage()" style="position: absolute; top: 5px; right: 30px; cursor: pointer; background: rgba(0,0,0,0.5); color: white; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">&times;</span>
        </div>

        <!-- Reply Indicator -->
        <div id="panelReplyIndicator" style="display: none; padding: 10px 25px; background: rgba(0,113,227,0.05); border-top: 1px solid var(--glass-border); align-items: center; justify-content: space-between;">
            <div style="font-size: 12px; font-weight: 700; color: var(--accent-color);">Đang trả lời <span id="panelReplyUser"></span></div>
            <div onclick="cancelPanelReply()" style="cursor: pointer; opacity: 0.5;"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
        </div>

        <!-- Input Area -->
        <div style="padding: 20px 25px 40px; border-top: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(20px);">
            <div id="panelInputWrapper" style="display: flex; gap: 12px; align-items: center; background: rgba(0,0,0,0.03); border: 1px solid var(--glass-border); border-radius: 24px; padding: 8px 18px; transition: border-color 0.2s;">
                <div class="avatar" style="background-image: url('{{ auth()->user()->avatar_url }}'); background-size: cover; width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;"></div>
                <input type="text" id="panelCommentInput" placeholder="Viết bình luận..." style="flex-grow: 1; background: transparent; border: none; outline: none; padding: 10px 0; font-size: 14px; color: var(--text-color);" oninput="clearPanelError()">
                <label style="cursor: pointer; opacity: 0.6; display: flex; align-items: center;">
                    <input type="file" id="panelCommentImageInput" accept="image/*" style="display: none;" onchange="previewPanelImage(this)">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </label>
                <button onclick="submitPanelComment()" style="background: none; border: none; color: var(--accent-color); font-weight: 800; cursor: pointer; padding: 5px 10px; font-size: 14px;">Đăng</button>
            </div>
            <!-- Thông báo lỗi vi phạm nội dung (ẩn mặc định) -->
            <div id="panelErrorBox" style="display: none; margin-top: 8px; padding: 10px 14px; background: rgba(255,59,48,0.08); border: 1px solid rgba(255,59,48,0.25); border-left: 3px solid #ff3b30; border-radius: 12px; font-size: 13px; font-weight: 600; color: #ff3b30; align-items: flex-start; gap: 8px;">
                <span style="font-size: 15px; flex-shrink: 0;">⚠️</span>
                <span id="panelErrorText"></span>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideLeft {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    .author-badge {
        background: var(--accent-color);
        color: white;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 6px;
        text-transform: uppercase;
        margin-left: 8px;
    }
    .comment-thread-line {
        position: absolute;
        width: 2px;
        background: var(--glass-border);
        opacity: 0.4;
        border-radius: 1px;
    }
</style>

<script>
    function closeShareModal(e) {
        if (e.target.id === 'shareModal') document.getElementById('shareModal').style.display = 'none';
    }

    let activePanelPostId = null;
    let activePanelAuthorId = null;
    let activeParentCommentId = null;

    function openCommentSidePanel(postId, username, content, avatar, authorId) {
        activePanelPostId = postId;
        activePanelAuthorId = authorId;
        activeParentCommentId = postId;
        document.getElementById('panelSourceUsername').innerText = username;
        document.getElementById('panelSourceContent').innerText = content;
        document.getElementById('panelSourceAvatar').style.backgroundImage = `url('${avatar}')`;
        document.getElementById('panelActualComments').innerHTML = '<p style="text-align: center; opacity: 0.5; padding: 20px;">Đang tải...</p>';
        document.getElementById('commentSidePanel').style.display = 'flex';
        document.body.classList.add('modal-open');
        cancelPanelReply();
        fetch(`/posts/${postId}/comments`)
            .then(res => res.json())
            .then(comments => {
                const list = document.getElementById('panelActualComments');
                list.innerHTML = '';
                if (comments.length === 0) {
                    list.innerHTML = '<p style="text-align: center; opacity: 0.4; padding: 20px;">Chưa có bình luận nào.</p>';
                } else {
                    comments.forEach(c => list.appendChild(createPanelCommentElement(c)));
                }
            });
    }

    function closeCommentSidePanel() { 
        document.getElementById('commentSidePanel').style.display = 'none'; 
        document.body.classList.remove('modal-open');
    }

    function createPanelCommentElement(c) {
        const div = document.createElement('div');
        div.className = 'comment-item';
        const isNested = c.parent_id && c.parent_id != activePanelPostId;
        div.style.cssText = `display: flex; gap: 12px; position: relative; margin-left: ${isNested ? '45px' : '0px'}; margin-bottom: 15px; background: rgba(0,0,0,0.02); padding: 15px; border-radius: 22px; border: 1px solid rgba(0,0,0,0.1); flex-direction: column;`;
        const authorBadge = c.user_id === activePanelAuthorId ? '<span class="author-badge">Tác giả</span>' : '';
        const imageHtml = c.image_url ? `
            <div style="margin-top: 10px; border-radius: 12px; overflow: hidden; border: 1px solid var(--glass-border); max-width: 200px;">
                <img src="${c.image_url}" style="width: 100%; display: block; cursor: pointer;" onclick="openLightbox('${c.image_url}')">
            </div>
        ` : '';
        
        div.innerHTML = `
            <div style="display: flex; gap: 12px;">
                <div class="avatar" style="width: 34px; height: 34px; background-image: url('${c.user.avatar_url}'); background-size: cover; flex-shrink: 0; z-index: 2; border-radius: 50%; border: 1px solid rgba(0,0,0,0.1);"></div>
                <div style="flex-grow: 1; z-index: 2;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                        <div style="display: flex; align-items: center;">
                            <strong style="font-size: 13.5px; font-weight: 750;">${c.user.username}</strong>
                            ${authorBadge}
                        </div>
                        <span style="font-size: 11px; opacity: 0.5;">${new Date(c.created_at).toLocaleDateString()}</span>
                    </div>
                    <div style="font-size: 14px; line-height: 1.5; color: var(--text-color);">${escapeHtml(c.content)}</div>
                    ${imageHtml}
                    <div style="margin-top: 8px; display: flex; gap: 15px; align-items: center;">
                        <span onclick="preparePanelReply(${c.id}, '${c.user.username}')" style="font-size: 12px; font-weight: 700; color: var(--accent-color); cursor: pointer; opacity: 0.8; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">Trả lời</span>
                    </div>
                </div>
            </div>
        `;
        return div;
    }

    function previewPanelImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('panelImagePreview').src = e.target.result;
                document.getElementById('panelImagePreviewContainer').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePanelImage() {
        document.getElementById('panelCommentImageInput').value = '';
        document.getElementById('panelImagePreviewContainer').style.display = 'none';
        document.getElementById('panelImagePreview').src = '';
    }

    function preparePanelReply(id, user) {
        activeParentCommentId = id;
        document.getElementById('panelReplyUser').innerText = '@' + user;
        document.getElementById('panelReplyIndicator').style.display = 'flex';
        document.getElementById('panelCommentInput').focus();
    }

    function cancelPanelReply() {
        activeParentCommentId = activePanelPostId;
        document.getElementById('panelReplyIndicator').style.display = 'none';
    }

    function showPanelError(message) {
        const box = document.getElementById('panelErrorBox');
        const text = document.getElementById('panelErrorText');
        const wrapper = document.getElementById('panelInputWrapper');
        if (text) text.textContent = message;
        if (box) box.style.display = 'flex';
        if (wrapper) wrapper.style.borderColor = '#ff3b30';
        const input = document.getElementById('panelCommentInput');
        if (input) {
            input.focus();
            const len = input.value.length;
            input.setSelectionRange(len, len);
            input.style.animation = 'none';
            void input.offsetWidth;
            input.style.animation = 'commentShake 0.4s ease';
        }
    }

    function clearPanelError() {
        const box = document.getElementById('panelErrorBox');
        const wrapper = document.getElementById('panelInputWrapper');
        if (box) box.style.display = 'none';
        if (wrapper) wrapper.style.borderColor = 'var(--glass-border)';
    }

    function submitPanelComment() {
        const input = document.getElementById('panelCommentInput');
        const content = input.value.trim();
        const imageFile = document.getElementById('panelCommentImageInput').files[0];
        const btn = document.querySelector('#panelInputWrapper button');

        if (!content && !imageFile) return;

        clearPanelError();
        if (btn) { btn.disabled = true; btn.textContent = '...'; }

        const formData = new FormData();
        formData.append('content', content);
        if (imageFile) formData.append('image', imageFile);
        if (activeParentCommentId && activeParentCommentId != activePanelPostId) {
            formData.append('parent_id', activeParentCommentId);
        }

        fetch(`/posts/${activePanelPostId}/reply`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        }).then(async res => {
            const data = await res.json().catch(() => ({}));
            if (res.ok) {
                // Thành công: xóa input, thêm comment vào list
                input.value = '';
                removePanelImage();
                cancelPanelReply();
                const list = document.getElementById('panelActualComments');
                if (list.innerText.includes('Chưa có bình luận')) list.innerHTML = '';
                list.appendChild(createPanelCommentElement(data));
                list.scrollTop = list.scrollHeight;
                document.querySelectorAll(`.comment-count-display[data-post-id="${activePanelPostId}"]`).forEach(el => {
                    el.innerText = parseInt(el.innerText || 0) + 1;
                });
            } else {
                // Lỗi kiểm duyệt: hiển thị thông báo inline, KHÔNG xóa nội dung
                const errorMsg = data.errors?.content?.[0]
                    || data.message
                    || 'Nội dung không thể gửi. Vui lòng chỉnh sửa và thử lại.';
                showPanelError(errorMsg);
            }
        }).catch(err => {
            console.error('Panel comment error:', err);
            showPanelError('Không thể kết nối máy chủ. Vui lòng thử lại.');
        }).finally(() => {
            if (btn) { btn.disabled = false; btn.textContent = 'Đăng'; }
        });
    }

    function escapeHtml(text) { const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }
    function sharePost(id) { navigator.clipboard.writeText(window.location.origin + '/posts/' + id); alert('Đã sao chép liên kết!'); }
</script>
@endsection
