@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1160px !important; }

    /* ── Layout ── */
    .chat-layout {
        display: flex;
        height: calc(100vh - 100px);
        background: var(--glass-bg);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border: 1px solid var(--glass-border);
        border-radius: 28px;
        overflow: hidden;
        margin: 20px 0;
        box-shadow: var(--glass-shadow);
    }

    /* ── Sidebar ── */
    .chat-sidebar {
        width: 340px;
        flex-shrink: 0;
        border-right: 1px solid var(--glass-border);
        display: flex;
        flex-direction: column;
        background: rgba(248,250,252,0.6);
    }
    [data-theme="dark"] .chat-sidebar {
        background: rgba(10,14,28,0.7);
        border-right-color: rgba(255,255,255,0.06);
    }

    .chat-sidebar-header {
        padding: 20px 16px 14px;
        border-bottom: 1px solid var(--glass-border);
    }
    [data-theme="dark"] .chat-sidebar-header { border-bottom-color: rgba(255,255,255,0.06); }

    .sidebar-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }
    .sidebar-title {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.4px;
    }

    /* Sidebar back button */
    .sidebar-back-btn {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: rgba(0,0,0,0.04);
        border: 1px solid var(--glass-border);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        color: var(--secondary-text);
        transition: all 0.2s;
        text-decoration: none;
    }
    .sidebar-back-btn:hover { background: rgba(0,98,255,0.07); color: #0062FF; border-color: rgba(0,98,255,0.2); }
    [data-theme="dark"] .sidebar-back-btn { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08); }
    [data-theme="dark"] .sidebar-back-btn:hover { background: rgba(77,148,255,0.12); color: #4D94FF; }

    /* Search */
    .sidebar-search-wrap { position: relative; }
    .sidebar-search {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border-radius: 12px;
        border: 1.5px solid var(--glass-border);
        background: rgba(255,255,255,0.7);
        font-size: 13.5px;
        color: var(--text-color);
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
        transition: all 0.25s;
    }
    .sidebar-search::placeholder { color: var(--secondary-text); }
    .sidebar-search:focus {
        border-color: #0062FF;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(0,98,255,0.1);
    }
    [data-theme="dark"] .sidebar-search {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.08);
        color: var(--text-color);
    }
    [data-theme="dark"] .sidebar-search:focus {
        background: rgba(255,255,255,0.08);
        border-color: rgba(77,148,255,0.5);
        box-shadow: 0 0 0 3px rgba(77,148,255,0.12);
    }
    [data-theme="dark"] .sidebar-search::placeholder { color: rgba(255,255,255,0.3); }
    .sidebar-search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        color: var(--secondary-text);
        pointer-events: none; opacity: 0.55;
    }

    /* Conversation list */
    .conversation-list-container {
        flex-grow: 1;
        overflow-y: auto;
        padding: 6px;
        scrollbar-width: thin;
        scrollbar-color: var(--glass-border) transparent;
    }
    .conversation-list-container::-webkit-scrollbar { width: 4px; }
    .conversation-list-container::-webkit-scrollbar-track { background: transparent; }
    .conversation-list-container::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 4px; }

    .conversation-item-sidebar {
        padding: 11px 12px;
        display: flex;
        gap: 12px;
        align-items: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.18s;
        border-radius: 14px;
        border-left: none;
        position: relative;
    }
    .conversation-item-sidebar:hover {
        background: rgba(0,98,255,0.05);
        transform: translateX(2px);
    }
    .conversation-item-sidebar.active {
        background: rgba(0,98,255,0.08);
    }
    .conversation-item-sidebar.active::before {
        content: '';
        position: absolute;
        left: 0; top: 20%;
        height: 60%; width: 3px;
        background: #0062FF;
        border-radius: 0 3px 3px 0;
    }
    [data-theme="dark"] .conversation-item-sidebar:hover { background: rgba(77,148,255,0.08); }
    [data-theme="dark"] .conversation-item-sidebar.active { background: rgba(77,148,255,0.1); }
    [data-theme="dark"] .conversation-item-sidebar.active::before { background: #4D94FF; }

    /* Conv item avatar */
    .conv-sidebar-avatar {
        width: 46px; height: 46px;
        border-radius: 14px;
        background-size: cover;
        background-position: center;
        flex-shrink: 0;
    }
    .conv-sidebar-avatar.group { border-radius: 14px; border: 1.5px solid rgba(0,98,255,0.3); }
    .conv-sidebar-online {
        position: absolute; bottom: 1px; right: 1px;
        width: 11px; height: 11px;
        background: #34c759;
        border: 2px solid white;
        border-radius: 50%;
    }
    [data-theme="dark"] .conv-sidebar-online { border-color: #0a0e1c; }

    /* Conv item unread dot */
    .conv-sidebar-unread {
        width: 9px; height: 9px;
        background: #0062FF;
        border-radius: 50%;
        flex-shrink: 0;
        box-shadow: 0 0 6px rgba(0,98,255,0.5);
    }
    [data-theme="dark"] .conv-sidebar-unread { background: #4D94FF; box-shadow: 0 0 6px rgba(77,148,255,0.5); }

    /* Group badge in sidebar */
    .conv-badge-group {
        background: rgba(0,98,255,0.1);
        color: #0062FF;
        font-size: 8px;
        font-weight: 800;
        padding: 1px 5px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }
    [data-theme="dark"] .conv-badge-group { background: rgba(77,148,255,0.15); color: #4D94FF; }

    /* ── Chat Main ── */
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: transparent;
        position: relative;
        min-width: 0;
    }

    /* Chat header */
    .chat-header {
        padding: 13px 18px;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        align-items: center;
        gap: 13px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(16px);
        flex-shrink: 0;
    }
    [data-theme="dark"] .chat-header {
        background: rgba(12,14,24,0.88);
        border-bottom-color: rgba(255,255,255,0.05);
    }

    .chat-header-avatar {
        width: 42px; height: 42px;
        border-radius: 14px;
        background-size: cover;
        background-position: center;
        flex-shrink: 0;
        position: relative;
    }

    .chat-header-info { flex-grow: 1; min-width: 0; }
    .chat-header-name {
        font-weight: 800;
        font-size: 15.5px;
        display: flex; align-items: center; gap: 8px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .chat-header-sub {
        font-size: 12px;
        color: var(--secondary-text);
        font-weight: 500;
        margin-top: 1px;
    }
    .status-dot-online { color: #34c759; font-weight: 700; }

    .chat-type-badge {
        font-size: 10px; font-weight: 800;
        padding: 2px 8px; border-radius: 6px;
        text-transform: uppercase; letter-spacing: 0.4px;
        flex-shrink: 0;
    }
    .chat-type-badge.group { background: rgba(0,98,255,0.1); color: #0062FF; }
    .chat-type-badge.direct { background: rgba(52,199,89,0.1); color: #34c759; }
    [data-theme="dark"] .chat-type-badge.group { background: rgba(77,148,255,0.15); color: #4D94FF; }

    .chat-header-actions { display: flex; gap: 8px; flex-shrink: 0; }
    .chat-header-btn {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: rgba(0,0,0,0.04);
        border: 1px solid var(--glass-border);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        color: var(--secondary-text);
        transition: all 0.2s;
    }
    .chat-header-btn:hover {
        background: rgba(0,98,255,0.08);
        color: #0062FF;
        border-color: rgba(0,98,255,0.2);
        transform: scale(1.06);
    }
    [data-theme="dark"] .chat-header-btn { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08); }
    [data-theme="dark"] .chat-header-btn:hover { background: rgba(77,148,255,0.12); color: #4D94FF; border-color: rgba(77,148,255,0.3); }

    /* Search panel */
    .msg-search-panel {
        display: none;
        padding: 10px 18px;
        background: rgba(0,0,0,0.02);
        border-bottom: 1px solid var(--glass-border);
        position: relative;
    }
    [data-theme="dark"] .msg-search-panel { background: rgba(255,255,255,0.02); border-bottom-color: rgba(255,255,255,0.05); }

    /* Messages container */
    #messages-container {
        flex-grow: 1;
        overflow-y: auto;
        padding: 20px 18px;
        display: flex;
        flex-direction: column;
        gap: 3px;
        scrollbar-width: thin;
        scrollbar-color: var(--glass-border) transparent;
    }
    #messages-container::-webkit-scrollbar { width: 4px; }
    #messages-container::-webkit-scrollbar-track { background: transparent; }
    #messages-container::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 4px; }
    [data-theme="dark"] #messages-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); }

    /* Message wrappers */
    .message-wrapper {
        display: flex;
        gap: 8px;
        max-width: 72%;
        position: relative;
        animation: fadeIn 0.25s ease both;
    }
    .message-wrapper.me { align-self: flex-end; flex-direction: row-reverse; }
    .message-wrapper.other { align-self: flex-start; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Avatar in messages */
    .msg-avatar {
        width: 32px; height: 32px;
        border-radius: 10px;
        background-size: cover;
        background-position: center;
        flex-shrink: 0;
        align-self: flex-end;
    }

    /* Message content wrapper */
    .message-content-wrapper {
        display: flex;
        flex-direction: column;
        position: relative;
        max-width: 100%;
    }
    .message-wrapper.me .message-content-wrapper { align-items: flex-end; }
    .message-wrapper.other .message-content-wrapper { align-items: flex-start; }

    /* Message time */
    .msg-time {
        font-size: 10.5px;
        opacity: 0.5;
        margin-top: 4px;
        padding: 0 2px;
    }
    [data-theme="dark"] .msg-time { color: rgba(255,255,255,0.4); opacity: 1; }

    /* Message actions trigger */
    .message-actions-trigger {
        opacity: 0;
        transition: opacity 0.2s;
        cursor: pointer;
        padding: 4px;
        border-radius: 8px;
        background: rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        justify-content: center;
        align-self: center;
        flex-shrink: 0;
        width: 28px; height: 28px;
    }
    .message-wrapper:hover .message-actions-trigger { opacity: 1; }
    [data-theme="dark"] .message-actions-trigger { background: rgba(255,255,255,0.07); color: var(--secondary-text); }

    /* Bubble */
    .bubble {
        border-radius: 20px;
        padding: 10px 16px;
        font-size: 14.5px;
        line-height: 1.55;
        position: relative;
        font-weight: 500;
        transition: transform 0.15s ease;
        word-break: break-word;
    }
    .bubble:hover { transform: scale(1.005); }
    .bubble a { color: inherit; text-decoration: underline; font-weight: 700; }

    .message-wrapper.me .bubble {
        border-bottom-right-radius: 5px;
        color: #fff;
        box-shadow: 0 4px 14px rgba(0,98,255,0.25);
    }
    .message-wrapper.me .bubble a { color: rgba(255,255,255,0.9); }
    .message-wrapper.other .bubble {
        border-bottom-left-radius: 5px;
        background: rgba(0,0,0,0.05);
        color: var(--text-color);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    /* Replied message info */
    .replied-message-info {
        background: rgba(0,0,0,0.05);
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 6px;
        border-left: 2px solid #0062FF;
        opacity: 0.85;
    }
    [data-theme="dark"] .replied-message-info {
        background: rgba(255,255,255,0.06);
        border-left-color: #4D94FF;
    }

    /* Action dropdown */
    .msg-action-menu {
        display: none;
        position: absolute;
        bottom: 100%;
        background: var(--glass-bg);
        backdrop-filter: blur(24px);
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
        z-index: 100;
        min-width: 132px;
        overflow: hidden;
        margin-bottom: 4px;
    }
    .message-wrapper.me .msg-action-menu { right: 0; }
    .message-wrapper.other .msg-action-menu { left: 0; }

    .msg-action-item {
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .msg-action-item:hover { background: rgba(0,0,0,0.05); }
    [data-theme="dark"] .msg-action-menu { background: rgba(20,22,36,0.98); border-color: rgba(255,255,255,0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.6); }
    [data-theme="dark"] .msg-action-item:hover { background: rgba(77,148,255,0.08); }

    /* System message */
    .sys-msg {
        align-self: center;
        padding: 5px 14px;
        background: rgba(0,0,0,0.04);
        border-radius: 20px;
        font-size: 12px;
        color: var(--secondary-text);
        margin: 6px 0;
        font-weight: 500;
    }
    [data-theme="dark"] .sys-msg { background: rgba(255,255,255,0.05); }

    /* Appointment cards */
    .appt-card-created {
        background: rgba(255,149,0,0.08);
        border: 1px solid rgba(255,149,0,0.25);
        border-left: 3px solid #ff9500;
        border-radius: 16px;
        padding: 12px 16px;
        min-width: 220px;
    }
    .appt-card-due {
        background: rgba(255,59,48,0.08);
        border: 1px solid rgba(255,59,48,0.25);
        border-left: 3px solid #ff3b30;
        border-radius: 16px;
        padding: 12px 16px;
        min-width: 220px;
        animation: pulseRemind 1.5s ease-in-out 4;
    }
    @keyframes pulseRemind {
        0%,100% { box-shadow: 0 0 0 0 rgba(255,59,48,0.3); }
        50% { box-shadow: 0 0 0 8px rgba(255,59,48,0); }
    }
    [data-theme="dark"] .appt-card-created { background: rgba(255,149,0,0.1); border-color: rgba(255,149,0,0.2); }
    [data-theme="dark"] .appt-card-due { background: rgba(255,59,48,0.1); border-color: rgba(255,59,48,0.2); }

    /* Reply preview */
    .reply-preview {
        padding: 10px 16px;
        background: rgba(0,98,255,0.04);
        border-left: 3px solid #0062FF;
        display: none;
        justify-content: space-between;
        align-items: center;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid var(--glass-border);
    }
    [data-theme="dark"] .reply-preview { background: rgba(77,148,255,0.06); border-left-color: #4D94FF; border-bottom-color: rgba(255,255,255,0.06); }

    /* Media preview */
    #mediaMsgPreview {
        background: rgba(0,98,255,0.03);
        border-left: 3px solid #0062FF;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid var(--glass-border);
        position: relative;
    }
    [data-theme="dark"] #mediaMsgPreview { background: rgba(77,148,255,0.06); border-left-color: #4D94FF; border-bottom-color: rgba(255,255,255,0.06); }

    /* Input area */
    .chat-input-area {
        padding: 14px 16px;
        border-top: 1px solid var(--glass-border);
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(16px);
        flex-shrink: 0;
    }
    [data-theme="dark"] .chat-input-area { background: rgba(10,12,22,0.92); border-top-color: rgba(255,255,255,0.05); }

    /* Input action bubbles */
    .input-action-bubble {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 1.5px solid var(--glass-border);
        flex-shrink: 0;
    }
    .input-action-bubble:hover {
        transform: translateY(-2px) scale(1.06);
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    .input-action-bubble:active { transform: scale(0.94); }
    [data-theme="dark"] .input-action-bubble { border-color: rgba(255,255,255,0.08); }
    [data-theme="dark"] .input-action-bubble:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.4); }

    /* Input wrapper */
    #inputWrapper {
        transition: all 0.25s;
    }
    #inputWrapper:focus-within {
        border-color: rgba(0,98,255,0.35) !important;
        box-shadow: 0 0 0 3px rgba(0,98,255,0.08) !important;
    }
    [data-theme="dark"] #inputWrapper {
        background: rgba(20,20,30,0.9) !important;
        border-color: rgba(255,255,255,0.08) !important;
    }
    [data-theme="dark"] #inputWrapper:focus-within {
        border-color: rgba(77,148,255,0.35) !important;
        box-shadow: 0 0 0 3px rgba(77,148,255,0.08) !important;
    }
    [data-theme="dark"] #mainChatInput { color: #eeeef0; }
    [data-theme="dark"] #mainChatInput::placeholder { color: rgba(255,255,255,0.25); }

    /* Send button */
    [data-theme="dark"] #sendBtn:not([disabled]) { border-color: rgba(255,255,255,0.15) !important; color: #eeeef0 !important; }
    [data-theme="dark"] #sendBtn:not([disabled]):hover { background: rgba(77,148,255,0.15) !important; border-color: rgba(77,148,255,0.4) !important; color: #4D94FF !important; }

    /* Modal settings tabs */
    .settings-tab {
        padding: 10px 20px;
        cursor: pointer;
        font-weight: 700;
        font-size: 13.5px;
        opacity: 0.5;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .settings-tab.active { opacity: 1; border-bottom-color: #0062FF; color: #0062FF; }
    [data-theme="dark"] .settings-tab.active { border-bottom-color: #4D94FF; color: #4D94FF; }

    /* Dark mode bubbles */
    [data-theme="dark"] .message-wrapper.me .bubble {
        background: linear-gradient(145deg, #1e3a6e, #1a3060) !important;
        color: #e8f0fe !important;
        box-shadow: 0 4px 20px rgba(30,58,110,0.4) !important;
    }
    [data-theme="dark"] .message-wrapper.other .bubble {
        background: rgba(26,28,42,0.95) !important;
        color: #eeeef0 !important;
        border: 1px solid rgba(255,255,255,0.07);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important;
    }

    /* Appointment toast */
    #apptReminderToast {
        position: fixed; top: 80px; left: 50%;
        transform: translateX(-50%) translateY(-20px);
        z-index: 9999;
        background: linear-gradient(135deg, #ff3b30, #ff6b00);
        color: white; padding: 14px 22px; border-radius: 20px;
        box-shadow: 0 8px 25px rgba(255,59,48,0.4);
        font-weight: 700; font-size: 14px;
        display: none; opacity: 0;
        transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
        max-width: 360px; text-align: center;
    }
    #apptReminderToast.show { display: block; opacity: 1; transform: translateX(-50%) translateY(0); }

    /* Dark mode layout */
    [data-theme="dark"] .chat-layout {
        background: rgba(10,14,26,0.97);
        border-color: rgba(255,255,255,0.06);
        box-shadow: 0 30px 80px rgba(0,0,0,0.8);
    }

    /* Scrollbar */
    [data-theme="dark"] ::-webkit-scrollbar { width: 4px; height: 4px; }
    [data-theme="dark"] ::-webkit-scrollbar-track { background: transparent; }
    [data-theme="dark"] ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
    [data-theme="dark"] ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.18); }

    [data-theme="dark"] img[onclick*="openLightbox"],
    [data-theme="dark"] video[controls] { box-shadow: 0 8px 30px rgba(0,0,0,0.5) !important; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateX(-50%) translateY(10px); }
        to   { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 900px) {
        .chat-sidebar { display: none; }
        .container { max-width: 650px !important; }
    }
    @media (max-width: 600px) {
        .chat-layout { border-radius: 0; margin: 0; height: calc(100vh - 60px); }
    }
</style>

<div class="chat-layout">
    <!-- Cột trái: Danh sách hội thoại -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <div class="sidebar-header-top">
                <span class="sidebar-title">Tin nhắn</span>
                <a href="{{ route('messages.index') }}" class="sidebar-back-btn" title="Về trang tin nhắn">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </a>
            </div>
            <div class="sidebar-search-wrap">
                <svg class="sidebar-search-icon" viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="sidebar-search" placeholder="Tìm kiếm cuộc trò chuyện...">
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
                $lastPreview = $conv->lastMessage
                    ? (($conv->lastMessage->sender_id === auth()->id() ? 'Bạn: ' : '') . ($conv->lastMessage->message_type === 'text' ? \Illuminate\Support\Str::limit($conv->lastMessage->content, 38) : ($conv->lastMessage->message_type === 'image' ? '📷 Ảnh' : '📎 Tệp')))
                    : 'Bắt đầu trò chuyện...';
            @endphp
            <a href="{{ route('messages.show', $conv->id) }}" class="conversation-item-sidebar {{ $isCurrent ? 'active' : '' }}">
                <div style="position: relative; flex-shrink: 0;">
                    <div class="conv-sidebar-avatar {{ $convIsGroup ? 'group' : '' }}"
                         style="background-image: url('{{ $convAvatar ?? asset('avatars/user.png') }}')"></div>
                    @if(!$convIsGroup && isset($other) && $other && $other->is_online)
                    <div class="conv-sidebar-online"></div>
                    @endif
                </div>
                <div style="flex-grow: 1; overflow: hidden; min-width: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 4px;">
                        <div style="display: flex; align-items: center; gap: 5px; min-width: 0; overflow: hidden;">
                            <span style="font-weight: {{ $convUnread ? '800' : '700' }}; font-size: 13.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $convName }}</span>
                            @if($convIsGroup)<span class="conv-badge-group">nhóm</span>@endif
                        </div>
                        <span style="font-size: 11px; color: var(--secondary-text); opacity: 0.7; flex-shrink: 0;">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
                    </div>
                    <div style="font-size: 12.5px; color: var(--secondary-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; font-weight: {{ $convUnread ? '700' : '400' }}; {{ $convUnread ? 'color: var(--text-color);' : '' }}">
                        {{ $lastPreview }}
                    </div>
                </div>
                @if($convUnread)<div class="conv-sidebar-unread"></div>@endif
            </a>
            @endforeach
        </div>
    </div>

    <!-- Cột phải: Nội dung chat -->
    <div class="chat-main">
        @php
        $themeColor = $conversation->theme_color ?? '#0062FF';
        @endphp

        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-avatar"
                 style="background-image: url('{{ $conversation->type === 'direct' ? ($otherUser->avatar_url ?? asset('avatars/user.png')) : ($conversation->avatar_url ?? asset('avatars/user.png')) }}'); border: 2px solid {{ $themeColor }}30;">
                @if($conversation->type === 'direct' && $otherUser && $otherUser->is_online)
                <div style="position: absolute; bottom: -2px; right: -2px; width: 12px; height: 12px; background: #34c759; border: 2px solid white; border-radius: 50%;"></div>
                @endif
            </div>
            <div class="chat-header-info">
                <div class="chat-header-name">
                    {{ $conversation->type === 'direct' ? ($otherUser->username ?? 'Người dùng') : ($conversation->name ?? 'Nhóm') }}
                    @if($conversation->type === 'group')
                    <span class="chat-type-badge group">Nhóm</span>
                    @else
                    <span class="chat-type-badge direct">Bạn bè</span>
                    @endif
                </div>
                <div class="chat-header-sub">
                    @if($conversation->type === 'group')
                        {{ $conversation->users()->count() }} thành viên
                    @elseif($otherUser && $otherUser->is_online)
                        <span class="status-dot-online">● </span>Đang hoạt động
                    @else
                        Ngoại tuyến
                    @endif
                </div>
            </div>
            <div class="chat-header-actions">
                <div class="chat-header-btn" onclick="toggleSearch()" title="Tìm kiếm">
                    <svg viewBox="0 0 24 24" width="17" height="17" stroke="currentColor" stroke-width="2.2" fill="none">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <div class="chat-header-btn" onclick="openGroupSettingsModal()" title="Cài đặt">
                    <svg viewBox="0 0 24 24" width="17" height="17" stroke="currentColor" stroke-width="2.2" fill="none">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Search Panel -->
        <div id="msgSearchPanel" class="msg-search-panel">
            <div style="position: relative;">
                <input type="text" id="msgSearchInput" placeholder="Tìm tin nhắn..."
                    style="width: 100%; border-radius: 12px; padding: 9px 36px 9px 14px; background: var(--glass-bg); border: 1.5px solid var(--glass-border); outline: none; font-size: 14px; font-family: inherit; color: var(--text-color); box-sizing: border-box;">
                <div onclick="toggleSearch()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; opacity: 0.45; font-size: 20px; line-height: 1;">&times;</div>
            </div>
            <div id="msgSearchResults" class="glass-bubble" style="display: none; margin-top: 8px; max-height: 300px; overflow-y: auto; border-radius: 14px; z-index: 2000; position: absolute; left: 18px; right: 18px; border: 1px solid var(--glass-border); box-shadow: 0 8px 24px rgba(0,0,0,0.1);"></div>
        </div>

        <!-- Messages Container -->
        <div id="messages-container" style="flex-grow: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
            @php
            if (!function_exists('getFileIconInfo')) {
            function getFileIconInfo($fileName) {
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $icons = [
            'pdf' => ['color' => '#ff3b30', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <path d="M9 15l2 2 4-4"></path>'],
            'doc' => ['color' => '#0062FF', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <line x1="10" y1="9" x2="8" y2="9"></line>'],
            'docx' => ['color' => '#0062FF', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <line x1="10" y1="9" x2="8" y2="9"></line>'],
            'xls' => ['color' => '#28a745', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="8" y1="13" x2="16" y2="13"></line>
            <line x1="8" y1="17" x2="16" y2="17"></line>
            <line x1="8" y1="9" x2="10" y2="9"></line>'],
            'xlsx' => ['color' => '#28a745', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="8" y1="13" x2="16" y2="13"></line>
            <line x1="8" y1="17" x2="16" y2="17"></line>
            <line x1="8" y1="9" x2="10" y2="9"></line>'],
            'ppt' => ['color' => '#fd7e14', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <path d="M9 13l3 3 3-3"></path>'],
            'pptx' => ['color' => '#fd7e14', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <path d="M9 13l3 3 3-3"></path>'],
            'zip' => ['color' => '#6c757d', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <path d="M12 12v6"></path>
            <path d="M10 16l2 2 2-2"></path>'],
            'rar' => ['color' => '#6c757d', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <path d="M12 12v6"></path>
            <path d="M10 16l2 2 2-2"></path>'],
            'txt' => ['color' => '#000', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <line x1="10" y1="9" x2="8" y2="9"></line>'],
            ];

            return $icons[$ext] ?? ['color' => '#6e6e73', 'icon' => '<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
            <polyline points="13 2 13 9 20 9"></polyline>'];
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
            @php $apptType = $message->metadata['type'] ?? null; @endphp
            @if($apptType === 'appointment_created')
            <div style="align-self: center; margin: 10px 0;">
                <div class="appt-card-created">
                    <div style="font-size:11px; font-weight:800; color:#ff9500; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">📅 Đã đặt lịch hẹn</div>
                    {!! $message->content !!}
                </div>
            </div>
            @elseif($apptType === 'appointment_due')
            @php $apptIsMe = $message->sender_id === auth()->id(); @endphp
            <div class="message-wrapper {{ $apptIsMe ? 'me' : 'other' }}">
                @if(!$apptIsMe && $message->sender)
                <div class="msg-avatar" style="background-image: url('{{ $message->sender->avatar_url ?? asset('avatars/user.png') }}')"></div>
                @endif
                <div class="message-content-wrapper">
                    <div class="appt-card-due" style="max-width: 280px;">
                        <div style="font-size:11px; font-weight:800; color:#ff3b30; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">⏰ LỊCH HẸN ĐÃ ĐẾN GIỜ!</div>
                        {!! $message->content !!}
                    </div>
                    <span class="msg-time">{{ $message->created_at->format('H:i') }}</span>
                </div>
            </div>
            @else
            <div class="sys-msg">{!! $message->content !!}</div>
            @endif
            @else
            <div class="message-wrapper {{ $isMe ? 'me' : 'other' }}" id="msg-{{ $message->id }}">
                @if(!$isMe)
                <div class="msg-avatar" style="background-image: url('{{ $message->sender->avatar_url ?? asset('avatars/user.png') }}')"></div>
                @endif

                <div class="message-content-wrapper">
                    @if($message->parent_id)
                    <div class="replied-message-info">
                        <strong>{{ $message->parent->sender->username }}</strong>:
                        {{ $message->parent->message_type === 'text' ? (mb_strlen($message->parent->content) > 35 ? mb_substr($message->parent->content, 0, 35).'...' : $message->parent->content) : '[Phương tiện]' }}
                    </div>
                    @endif

                    <div class="{{ $isMedia ? '' : 'bubble' }}" style="{{ $isMe && !$isMedia ? 'background: '.$themeColor.';' : (!$isMedia ? '' : '') }}{{ $isMedia ? 'border-radius: 18px; overflow: hidden;' : '' }}">
                        @if($message->message_type === 'image')
                        <img src="{{ asset($message->content) }}" onclick="openLightbox(this.src)" style="max-width: 280px; border-radius: 18px; cursor: zoom-in; display: block; box-shadow: 0 6px 18px rgba(0,0,0,0.12);">
                        @elseif($message->message_type === 'video')
                        <video src="{{ asset($message->content) }}" controls playsinline style="max-width: 280px; border-radius: 18px; display: block; box-shadow: 0 6px 18px rgba(0,0,0,0.12);">
                            <a href="{{ asset($message->content) }}" download>Tải về</a>
                        </video>
                        @elseif($message->message_type === 'file')
                        @php
                            $fileName = $message->metadata['file_name'] ?? 'file';
                            $fileInfo = getFileIconInfo($fileName);
                        @endphp
                        <a href="{{ asset($message->content) }}" download="{{ $fileName }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; padding: 4px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $isMe ? 'rgba(255,255,255,0.22)' : $fileInfo['color'].'18' }}; display: flex; align-items: center; justify-content: center; color: {{ $isMe ? 'white' : $fileInfo['color'] }}; flex-shrink: 0;">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none">{!! $fileInfo['icon'] !!}</svg>
                            </div>
                            <div style="overflow: hidden; flex-grow: 1; min-width: 0;">
                                <div style="font-weight: 700; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">{{ $fileName }}</div>
                                <div style="font-size: 11px; opacity: 0.7; margin-top: 2px;">{{ isset($message->metadata['file_size']) ? round($message->metadata['file_size'] / 1024, 1) . ' KB' : '' }} · {{ strtoupper(pathinfo($fileName, PATHINFO_EXTENSION)) }}</div>
                            </div>
                        </a>
                        @else
                        @php
                            $content = e($message->content);
                            $content = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank">$1</a>', $content);
                            $content = preg_replace('/(?<!href=")(www\.[^\s]+) /', '<a href="http://$1" target="_blank">$1</a>', $content);
                            $content = preg_replace('/(?<![">])\b([a-zA-Z0-9.-]+\.(?:com|net|org|vn|edu|gov|io|info|me))\b/i', '<a href="http://$1" target="_blank">$1</a>', $content);
                        @endphp
                        {!! nl2br($content) !!}
                        @endif
                    </div>
                    <span class="msg-time">{{ $message->created_at->format('H:i') }}</span>
                </div>

                <!-- Actions trigger -->
                <div style="position: relative; align-self: center;">
                    <div class="message-actions-trigger" onclick="toggleMsgMenu({{ $message->id }})">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                            <circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>
                        </svg>
                    </div>
                    <div id="msg-menu-{{ $message->id }}" class="msg-action-menu">
                        <div class="msg-action-item" onclick="replyToMessage({{ $message->id }}, '{{ $message->sender->username }}', {{ $message->message_type === 'text' ? json_encode($message->content) : "'[Phương tiện]'" }})">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none">
                                <polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/>
                            </svg>
                            Trả lời
                        </div>
                        @if($isMe)
                        <div class="msg-action-item" style="color: #ff3b30;" onclick="deleteMessage({{ $message->id }})">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                            Xóa
                        </div>
                        @else
                        <div class="msg-action-item" style="color: #ff9500;" onclick="alert('Đã báo cáo tin nhắn này.')">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                                <line x1="4" y1="22" x2="4" y2="15"/>
                            </svg>
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
        <div class="chat-input-area">
            @if($isFriend || $conversation->type === 'group')
            <!-- Reply Preview -->
            <div id="replyPreview" class="reply-preview">
                <div style="overflow: hidden; min-width: 0;">
                    <div style="font-size: 12px; font-weight: 800; color: #0062FF;">Đang trả lời <span id="replyUser"></span></div>
                    <div id="replyText" style="font-size: 12.5px; opacity: 0.7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                </div>
                <div onclick="cancelReply()" style="cursor: pointer; opacity: 0.45; padding: 4px; font-size: 20px; line-height: 1; flex-shrink: 0;">&times;</div>
            </div>

            <!-- Media Preview -->
            <div id="mediaMsgPreview" style="display: none; padding: 10px 14px; border-radius: 12px 12px 0 0; position: relative;">
                <div id="mediaMsgPreviewContent" style="display: flex; align-items: center; gap: 12px;"></div>
                <div onclick="clearMediaMsgPreview()" style="position: absolute; right: 10px; top: 10px; cursor: pointer; opacity: 0.45; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.06); border-radius: 50%; font-size: 16px; line-height: 1;">&times;</div>
            </div>

            <form action="{{ route('messages.store', $conversation->id) }}" method="POST" enctype="multipart/form-data" id="chat-form" onsubmit="handleChatSubmit(event)">
                @csrf
                <input type="hidden" name="parent_id" id="replyParentId" value="">

                <div style="display: flex; gap: 8px; align-items: flex-end;">
                    <!-- Action buttons -->
                    <div style="display: flex; gap: 6px; padding-bottom: 2px;">
                        <label class="input-action-bubble" title="Gửi ảnh" style="background: rgba(0,98,255,0.08); color: #0062FF;">
                            <input type="file" name="image" id="msgImageInput" accept="image/*" style="display:none" onchange="previewMsgMedia(this, 'image')">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none">
                                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </label>
                        <label class="input-action-bubble" title="Gửi tệp" style="background: rgba(77,148,255,0.08); color: #4D94FF;">
                            <input type="file" name="file" id="msgFileInput" style="display:none" onchange="previewMsgMedia(this, 'file')">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="12" y1="12" x2="12" y2="18"/><polyline points="9 15 12 18 15 15"/>
                            </svg>
                        </label>
                        <div class="input-action-bubble" onclick="openAppointmentModal()" title="Đặt lịch hẹn" style="background: rgba(255,149,0,0.08); color: #ff9500; cursor: pointer;">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Input wrapper -->
                    <div style="flex-grow: 1; display: flex; gap: 8px; align-items: flex-end; background: var(--glass-bg); border: 1.5px solid var(--glass-border); border-radius: 22px; padding: 6px 10px 6px 16px; transition: all 0.25s;" id="inputWrapper">
                        <textarea id="mainChatInput" name="content" placeholder="Nhập tin nhắn..." rows="1"
                            style="flex-grow: 1; background: transparent; border: none; outline: none; padding: 8px 4px; font-size: 14.5px; resize: none; max-height: 140px; font-weight: 500; color: var(--text-color); font-family: inherit; line-height: 1.5;" oninput="validateChatInput()"></textarea>
                        <button type="submit" id="sendBtn" disabled style="background: transparent; border: 1.5px solid var(--glass-border); color: var(--secondary-text); padding: 8px 16px; border-radius: 14px; font-weight: 800; font-size: 13.5px; cursor: not-allowed; opacity: 0.4; transition: all 0.2s; flex-shrink: 0; font-family: inherit;">
                            <span id="sendText">Gửi</span>
                            <svg id="loadingIcon" style="display: none; animation: spin 1s linear infinite; vertical-align: middle;" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="3" fill="none">
                                <circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
            @else
            <div style="text-align: center; font-size: 14px; color: #ff3b30; padding: 14px 20px; background: rgba(255,59,48,0.05); border-radius: 14px; font-weight: 600; border: 1px solid rgba(255,59,48,0.12);">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none" style="vertical-align: middle; margin-right: 6px;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Bạn chỉ có thể nhắn tin cho bạn bè.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Appointment Reminder Toast -->
<div id="apptReminderToast">
    <div id="apptReminderToastText"></div>
</div>

<!-- Appointment Modal -->
<div id="appointmentModal" class="modal" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); backdrop-filter: blur(15px); z-index: 6000;">
    <div class="glass-bubble" style="max-width: 460px; width: 95%; border-radius: 28px; padding: 0; overflow: hidden;">
        <div style="padding: 22px 25px 18px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin: 0; font-size: 18px; font-weight: 800;">📅 Đặt lịch hẹn</h3>
                <p style="margin: 4px 0 0; font-size: 13px; color: var(--secondary-text);">Tạo lịch nhắc cho cuộc hội thoại này</p>
            </div>
            <span onclick="closeAppointmentModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5; line-height: 1;">&times;</span>
        </div>
        <div style="padding: 22px 25px;">
            <form id="appointmentForm" onsubmit="submitAppointment(event)">
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 6px;">Tiêu đề *</label>
                        <input type="text" id="apptTitle" placeholder="Vd: Họp nhóm, Gặp mặt..." required
                            style="width: 100%; padding: 11px 14px; border-radius: 12px; border: 1.5px solid var(--glass-border); background: rgba(0,0,0,0.02); font-size: 14px; font-weight: 600; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#ff9500'" onblur="this.style.borderColor='var(--glass-border)'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 6px;">Thời gian *</label>
                        <input type="datetime-local" id="apptTime" required
                            style="width: 100%; padding: 11px 14px; border-radius: 12px; border: 1.5px solid var(--glass-border); background: rgba(0,0,0,0.02); font-size: 14px; font-weight: 600; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#ff9500'" onblur="this.style.borderColor='var(--glass-border)'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 6px;">Địa điểm</label>
                        <input type="text" id="apptLocation" placeholder="Tùy chọn..."
                            style="width: 100%; padding: 11px 14px; border-radius: 12px; border: 1.5px solid var(--glass-border); background: rgba(0,0,0,0.02); font-size: 14px; font-weight: 600; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#ff9500'" onblur="this.style.borderColor='var(--glass-border)'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 6px;">Mô tả</label>
                        <textarea id="apptDescription" placeholder="Ghi chú thêm..." rows="2"
                            style="width: 100%; padding: 11px 14px; border-radius: 12px; border: 1.5px solid var(--glass-border); background: rgba(0,0,0,0.02); font-size: 14px; font-weight: 600; outline: none; resize: none; box-sizing: border-box; transition: border-color 0.2s; font-family: inherit;"
                            onfocus="this.style.borderColor='#ff9500'" onblur="this.style.borderColor='var(--glass-border)'"></textarea>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="closeAppointmentModal()" style="flex: 1; padding: 13px; border-radius: 14px; border: 1.5px solid var(--glass-border); background: transparent; font-weight: 700; font-size: 14px; cursor: pointer; color: var(--text-color);">Hủy</button>
                    <button type="submit" id="apptSubmitBtn" style="flex: 2; padding: 13px; border-radius: 14px; border: none; background: #ff9500; color: white; font-weight: 800; font-size: 14px; cursor: pointer; transition: opacity 0.2s;">
                        <span id="apptSubmitText">Đặt lịch</span>
                        <svg id="apptSubmitLoading" style="display:none; animation: spin 1s linear infinite; vertical-align: middle;" viewBox="0 0 24 24" width="16" height="16" stroke="white" stroke-width="3" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
                    </button>
                </div>
            </form>
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
        observer.observe(container, {
            childList: true
        });
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
                        ${['#0062FF', '#ff2d55', '#34c759', '#5856d6', '#ff9500', '#af52de'].map(c => `
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
        const isAdmin = '{{ $conversation->creator_id }}' == '{{ auth()->id() }}';
        
        content.innerHTML = '<div style="text-align: center; padding: 20px; opacity: 0.5;">Đang tải danh sách thành viên...</div>';

        fetch('{{ route("messages.members", $conversation->id) }}')
            .then(res => res.json())
            .then(members => {
                let html = `
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        ${isAdmin ? `
                            <div style="padding-bottom: 20px; border-bottom: 1px solid var(--glass-border);">
                                <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 10px;">Mời thêm thành viên</label>
                                <div style="position: relative;">
                                    <input type="text" id="inviteMemberSearch" placeholder="Tìm tên bạn bè..." 
                                        style="width: 100%; padding: 12px 15px 12px 40px; border-radius: 14px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-size: 14px; outline: none; font-weight: 600;"
                                        oninput="searchFriendsToInvite(this.value)">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" style="position: absolute; left: 15px; top: 13px; opacity: 0.4;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </div>
                                <div id="inviteResults" style="margin-top: 10px; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                        ` : ''}

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; margin-bottom: 15px;">Danh sách thành viên (${members.length})</label>
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                ${members.map(m => `
                                    <div style="display: flex; align-items: center; gap: 12px; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <a href="/@${m.username}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                                                <div class="avatar" style="width: 40px; height: 40px; background-image: url('${m.avatar_url || '/avatars/user.png'}'); background-size: cover; border-radius: 12px;"></div>
                                                <div>
                                                    <div style="font-weight: 700; font-size: 14px;">${m.username}</div>
                                                    <div style="font-size: 11px; opacity: 0.6; text-transform: uppercase; font-weight: 800;">${m.pivot.role === 'admin' ? 'Trưởng nhóm' : 'Thành viên'}</div>
                                                </div>
                                            </a>
                                        </div>
                                        ${isAdmin && m.id != '{{ auth()->id() }}' ? `
                                            <button onclick="kickMember(${m.id})" style="background: none; border: none; color: #ff3b30; cursor: pointer; font-size: 12px; font-weight: 700; opacity: 0.6;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">Mời ra</button>
                                        ` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                `;
                content.innerHTML = html;
            });
    }

    function searchFriendsToInvite(q) {
        const results = document.getElementById('inviteResults');
        if (!q.trim()) {
            results.innerHTML = '';
            return;
        }

        fetch('{{ route("api.friends.search") }}?q=' + q)
            .then(res => res.json())
            .then(friends => {
                if (friends.length === 0) {
                    results.innerHTML = '<div style="padding: 10px; font-size: 13px; opacity: 0.5; text-align: center;">Không tìm thấy bạn bè nào</div>';
                    return;
                }

                results.innerHTML = friends.map(u => `
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: rgba(0,0,0,0.02); border-radius: 12px; margin-bottom: 5px;">
                        <a href="/@${u.username}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                            <div class="avatar" style="width: 32px; height: 32px; background-image: url('${u.avatar_url || '/avatars/user.png'}'); background-size: cover; border-radius: 8px;"></div>
                            <span style="font-size: 13px; font-weight: 700;">${u.username}</span>
                        </a>
                        <button onclick="inviteMember(${u.id})" style="background: var(--accent-color); color: white; border: none; padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 11px; cursor: pointer;">Mời</button>
                    </div>
                `).join('');
            });
    }

    function inviteMember(userId) {
        fetch('{{ route("messages.add_members", $conversation->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_ids: [userId]
            })
        }).then(() => {
            document.getElementById('inviteMemberSearch').value = '';
            document.getElementById('inviteResults').innerHTML = '';
            renderMembersTab();
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

                <div style="padding: 15px; background: rgba(255,149,0,0.05); border-radius: 18px; border: 1px solid rgba(255,149,0,0.2); cursor: pointer;" onclick="openAppointmentsList()">
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #ff9500; color: white; display: flex; align-items: center; justify-content: center;">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <span style="font-weight: 700; font-size: 14px;">Lịch hẹn</span>
                    </div>
                </div>
            </div>
        `;
    }

    function openAppointmentsList() {
        const content = document.getElementById('settingsContent');
        content.innerHTML = '<div style="text-align:center;padding:20px;opacity:0.5;">Đang tải...</div>';

        fetch('{{ route("messages.get_appointments", $conversation->id) }}')
            .then(res => res.json())
            .then(appts => {
                let html = `<div style="margin-bottom:20px;"><button onclick="renderToolsTab()" style="background:none;border:none;color:var(--accent-color);font-weight:700;cursor:pointer;display:flex;align-items:center;gap:5px;"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="3" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg> Quay lại</button></div>`;
                html += `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;"><label style="font-size:12px;font-weight:800;color:var(--secondary-text);text-transform:uppercase;">Lịch hẹn sắp tới (${appts.length})</label><button onclick="openAppointmentModal()" style="background:#ff9500;color:white;border:none;padding:6px 14px;border-radius:10px;font-weight:700;font-size:12px;cursor:pointer;">+ Thêm</button></div>`;

                if (appts.length === 0) {
                    html += '<div style="text-align:center;padding:40px 20px;opacity:0.5;">Chưa có lịch hẹn nào.</div>';
                } else {
                    html += '<div style="display:flex;flex-direction:column;gap:12px;">';
                    appts.forEach(a => {
                        const dt = new Date(a.appointment_time);
                        const timeStr = dt.toLocaleString('vi-VN', {hour:'2-digit',minute:'2-digit',day:'2-digit',month:'2-digit',year:'numeric'});
                        html += `
                            <div style="background:rgba(255,149,0,0.06);border:1px solid rgba(255,149,0,0.2);border-left:3px solid #ff9500;border-radius:14px;padding:14px;">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                                    <div style="flex:1;">
                                        <div style="font-weight:800;font-size:14px;margin-bottom:4px;">${a.title}</div>
                                        <div style="font-size:12px;opacity:0.7;">⏰ ${timeStr}</div>
                                        ${a.location ? `<div style="font-size:12px;opacity:0.7;margin-top:2px;">📍 ${a.location}</div>` : ''}
                                        ${a.description ? `<div style="font-size:12px;opacity:0.6;margin-top:4px;">📝 ${a.description}</div>` : ''}
                                    </div>
                                    <button onclick="cancelAppointmentFromList(${a.id})" style="background:none;border:none;color:#ff3b30;font-size:11px;font-weight:700;cursor:pointer;opacity:0.6;flex-shrink:0;margin-left:8px;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">Hủy</button>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }
                content.innerHTML = html;
            });
    }

    function cancelAppointmentFromList(apptId) {
        if (!confirm('Hủy lịch hẹn này?')) return;
        fetch('{{ url("messages/".$conversation->id."/appointments") }}/' + apptId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => openAppointmentsList());
    }

    function updateThemeColor(color) {
        fetch('{{ route("messages.update_color", $conversation->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                color: color
            })
        }).then(() => location.reload());
    }

    function copyJoinCode(code) {
        navigator.clipboard.writeText(code).then(() => alert('Đã sao chép mã mời!'));
    }

    function kickMember(userId) {
        if (confirm('Mời thành viên này ra khỏi nhóm?')) {
            fetch(`/messages/{{ $conversation->id }}/kick/` + userId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => renderMembersTab());
        }
    }

    function leaveChat() {
        if (confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) {
            fetch('{{ route("messages.delete_chat", $conversation->id) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => window.location.href = '{{ route("messages.index") }}');
        }
    }

    function openMediaGallery(type = 'all') {
        const content = document.getElementById('settingsContent');
        content.innerHTML = '<div style="text-align: center; padding: 20px; opacity: 0.5;">Đang tải...</div>';

        fetch('{{ route("messages.media", $conversation->id) }}')
            .then(res => res.json())
            .then(media => {
                if (media.length === 0) {
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
                        if (m.message_type === 'image') {
                            html += `<img src="${src}" onclick="openLightbox(this.src)" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 12px; cursor: pointer; border: 1px solid var(--glass-border);">`;
                        } else if (m.message_type === 'video') {
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
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: ${isUrl ? '#0062FF' : '#8e44ad'}; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
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
        if (q.length < 2) {
            results.style.display = 'none';
            return;
        }

        fetch('{{ route("messages.search", $conversation->id) }}?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(messages => {
                results.innerHTML = '';
                if (messages.length === 0) {
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
                            if (target) {
                                target.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
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
            if (m.id !== 'msg-menu-' + id) m.style.display = 'none';
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

    function validateChatInput() {
        const content = document.getElementById('mainChatInput').value.trim();
        const hasMedia = document.getElementById('mediaMsgPreview').style.display !== 'none';
        const btn = document.getElementById('sendBtn');

        if (content || hasMedia) {
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            btn.style.opacity = '1';
            btn.style.background = 'var(--glass-bg)';
            btn.onmouseover = () => {
                btn.style.background = 'var(--text-color)';
                btn.style.color = 'var(--glass-bg)';
            };
            btn.onmouseout = () => {
                btn.style.background = 'var(--glass-bg)';
                btn.style.color = 'var(--text-color)';
            };
        } else {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            btn.style.opacity = '0.4';
            btn.style.background = 'var(--glass-bg)';
            btn.onmouseover = null;
            btn.onmouseout = null;
        }
    }

    function previewMsgMedia(input, type) {
        const file = input.files[0];
        if (!file) return;

        if (file.size > 200 * 1024 * 1024) {
            alert('Tệp tin quá lớn. Vui lòng chọn tệp nhỏ hơn 200MB.');
            input.value = '';
            return;
        }

        const preview = document.getElementById('mediaMsgPreview');
        const content = document.getElementById('mediaMsgPreviewContent');
        const inputWrapper = document.getElementById('inputWrapper');

        if (type !== 'image') document.getElementById('msgImageInput').value = '';
        if (type !== 'file') document.getElementById('msgFileInput').value = '';
        window._pendingZipBlob = null;
        window._pendingZipName = null;

        preview.style.display = 'block';
        inputWrapper.style.borderRadius = '0 0 24px 24px';

        if (type === 'image') {
            const reader = new FileReader();
            reader.onload = function(e) {
                content.innerHTML = `<img src="${e.target.result}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 1px solid var(--glass-border);">
                                     <div style="font-size: 13px; font-weight: 600; color: var(--text-color);">${file.name}</div>`;
                validateChatInput();
            };
            reader.readAsDataURL(file);
            return;
        }

        // File type → nén thành ZIP trước khi gửi
        const originalSizeMB = (file.size / 1024 / 1024).toFixed(1);
        const zipName = file.name + '.zip';

        content.innerHTML = `
            <div style="width: 50px; height: 50px; border-radius: 8px; background: rgba(94,158,255,0.1); display: flex; align-items: center; justify-content: center; border: 1px solid rgba(94,158,255,0.2); flex-shrink:0;">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="#4D94FF" stroke-width="2.2" fill="none">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="12" y1="12" x2="12" y2="18"></line>
                    <polyline points="9 15 12 18 15 15"></polyline>
                </svg>
            </div>
            <div style="min-width:0;">
                <div style="font-size: 13px; font-weight: 700; color: var(--text-color); margin-bottom: 3px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${zipName}</div>
                <div id="zipStatusInfo" style="font-size: 11.5px; color: var(--secondary-text);">⏳ Đang nén ${originalSizeMB}MB...</div>
            </div>`;
        validateChatInput();

        // Nén file bằng JSZip
        const zip = new JSZip();
        zip.file(file.name, file);
        zip.generateAsync({ type: 'blob', compression: 'DEFLATE', compressionOptions: { level: 6 } })
            .then(blob => {
                window._pendingZipBlob = blob;
                window._pendingZipName = zipName;
                const compressedMB = (blob.size / 1024 / 1024).toFixed(1);
                const ratio = Math.round((1 - blob.size / file.size) * 100);
                const statusEl = document.getElementById('zipStatusInfo');
                if (statusEl) {
                    statusEl.textContent = ratio > 0
                        ? `✅ ${compressedMB}MB (giảm ${ratio}% từ ${originalSizeMB}MB)`
                        : `✅ ${compressedMB}MB`;
                }
            })
            .catch(() => {
                // Nếu lỗi, gửi file gốc
                const statusEl = document.getElementById('zipStatusInfo');
                if (statusEl) statusEl.textContent = `${originalSizeMB}MB (gửi trực tiếp)`;
            });
    }

    function clearMediaMsgPreview() {
        document.getElementById('mediaMsgPreview').style.display = 'none';
        document.getElementById('inputWrapper').style.borderRadius = '24px';
        document.getElementById('msgImageInput').value = '';
        document.getElementById('msgFileInput').value = '';
        window._pendingZipBlob = null;
        window._pendingZipName = null;
        validateChatInput();
    }

    function handleChatSubmit(event) {
        if (event && event.preventDefault) event.preventDefault();

        const form = event.target || event.currentTarget || document.getElementById('chat-form');
        if (!(form instanceof HTMLFormElement)) {
            console.error('Submit target is not a form:', form);
            return;
        }

        const sendBtn = document.getElementById('sendBtn');
        const sendText = document.getElementById('sendText');
        const loadingIcon = document.getElementById('loadingIcon');

        console.log('Submitting form to:', form.action);

        if (!form.action) {
            alert('Lỗi: Form thiếu action.');
            return;
        }

        const formData = new FormData(form);

        // Nếu có file đã được nén thành ZIP, thay thế file gốc
        if (window._pendingZipBlob && window._pendingZipName) {
            formData.delete('file');
            formData.append('file', window._pendingZipBlob, window._pendingZipName);
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Disable UI
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.style.opacity = '0.7';
        }
        if (sendText) sendText.style.display = 'none';
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
                        data = {
                            message: "Lỗi máy chủ (500) hoặc phản hồi không đúng định dạng."
                        };
                    }
                } catch (e) {
                    data = {
                        message: "Lỗi xử lý phản hồi từ máy chủ."
                    };
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
                if (sendText) sendText.style.display = 'block';
                if (loadingIcon) loadingIcon.style.display = 'none';
            });
    }

    function deleteMessage(id) {
        if (confirm('Xóa tin nhắn này đối với mọi người?')) {
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

    // ===================== APPOINTMENT FEATURE =====================

    function openAppointmentModal() {
        // Đặt mặc định thời gian là 1 giờ sau
        const now = new Date();
        now.setHours(now.getHours() + 1, 0, 0, 0);
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('apptTime').value =
            `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
        document.getElementById('appointmentModal').style.display = 'flex';
        document.body.classList.add('modal-open');
        document.getElementById('apptTitle').focus();
    }

    function closeAppointmentModal() {
        document.getElementById('appointmentModal').style.display = 'none';
        document.body.classList.remove('modal-open');
        document.getElementById('appointmentForm').reset();
    }

    document.getElementById('appointmentModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeAppointmentModal();
    });

    function submitAppointment(e) {
        e.preventDefault();
        const title = document.getElementById('apptTitle').value.trim();
        const time  = document.getElementById('apptTime').value;
        const loc   = document.getElementById('apptLocation').value.trim();
        const desc  = document.getElementById('apptDescription').value.trim();

        if (!title || !time) return;

        const submitBtn  = document.getElementById('apptSubmitBtn');
        const submitText = document.getElementById('apptSubmitText');
        const loadIcon   = document.getElementById('apptSubmitLoading');
        submitBtn.disabled = true;
        submitText.style.display = 'none';
        loadIcon.style.display   = 'inline';

        fetch('{{ route("messages.create_appointment", $conversation->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ title, appointment_time: time, location: loc, description: desc })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                closeAppointmentModal();
                // Đặt timer nhắc nhở phía client (khi user đang mở trang)
                scheduleClientReminder(data.appointment);
                location.reload();
            } else {
                alert('Không thể tạo lịch hẹn. Vui lòng thử lại.');
            }
        })
        .catch(() => alert('Lỗi kết nối. Vui lòng thử lại.'))
        .finally(() => {
            submitBtn.disabled = false;
            submitText.style.display = 'inline';
            loadIcon.style.display   = 'none';
        });
    }

    // Hiển thị toast nhắc nhở
    function showReminderToast(title) {
        const toast = document.getElementById('apptReminderToast');
        document.getElementById('apptReminderToastText').innerHTML =
            '⏰ Lịch hẹn đã đến giờ!<br><strong>' + title + '</strong>';
        toast.style.display = 'block';
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => { toast.style.display = 'none'; }, 400);
        }, 6000);
    }

    // Đặt timer client-side cho một lịch hẹn
    function scheduleClientReminder(appt) {
        const dueMs = new Date(appt.appointment_time).getTime() - Date.now();
        if (dueMs > 0 && dueMs < 24 * 60 * 60 * 1000) { // chỉ đặt nếu trong 24h
            setTimeout(() => {
                showReminderToast(appt.title);
                // Tải lại trang sau 2s để hiển thị tin nhắn nhắc nhở từ server
                setTimeout(() => location.reload(), 2000);
            }, dueMs);
        }
    }

    // Khi trang tải, đặt timer cho tất cả lịch hẹn sắp tới
    (function loadAndScheduleReminders() {
        fetch('{{ route("messages.get_appointments", $conversation->id) }}')
            .then(res => res.json())
            .then(appts => {
                appts.forEach(a => scheduleClientReminder(a));
            })
            .catch(() => {});
    })();

    // ===================== MESSAGE POLLING (30s) =====================
    // Lấy ID tin nhắn cuối cùng hiện tại
    let lastMessageId = {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};
    const conversationId = {{ $conversation->id }};

    function pollNewMessages() {
        if (!lastMessageId) return;
        fetch(`/api/chat/${conversationId}/messages?since=${lastMessageId}`)
            .then(res => res.json())
            .then(newMsgs => {
                if (!Array.isArray(newMsgs) || newMsgs.length === 0) return;
                newMsgs.forEach(msg => appendNewMessage(msg));
                lastMessageId = newMsgs[newMsgs.length - 1].id;
                scrollToBottom();
            })
            .catch(() => {});
    }

    function appendNewMessage(msg) {
        const container = document.getElementById('messages-container');
        let html = '';

        if (msg.message_type === 'system') {
            const apptType = msg.metadata && msg.metadata.type;
            if (apptType === 'appointment_created') {
                html = `<div style="align-self:center;margin:8px 0;">
                    <div class="appt-card-created">
                        <div style="font-size:11px;font-weight:800;color:#ff9500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">📅 Đã đặt lịch hẹn</div>
                        <div>${msg.content}</div>
                    </div>
                </div>`;
            } else if (apptType === 'appointment_due') {
                html = `<div style="align-self:center;margin:8px 0;">
                    <div class="appt-card-due">
                        <div style="font-size:11px;font-weight:800;color:#ff3b30;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">⏰ LỊCH HẸN ĐÃ ĐẾN GIỜ!</div>
                        <div>${msg.content}</div>
                    </div>
                </div>`;
                showReminderToast(msg.content.replace(/<[^>]*>/g, '').trim().split('\n')[0]);
            } else {
                html = `<div style="align-self:center;padding:6px 15px;background:rgba(0,0,0,0.04);border-radius:12px;font-size:12px;color:var(--secondary-text);margin:5px 0;">${msg.content}</div>`;
            }
        } else {
            // Tin nhắn thường - reload để đảm bảo hiển thị đúng
            location.reload();
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        container.appendChild(wrapper.firstElementChild);
    }

    // Bắt đầu polling sau 30 giây, lặp mỗi 30 giây
    setInterval(pollNewMessages, 30000);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
@endsection