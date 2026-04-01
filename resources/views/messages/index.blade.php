@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1160px !important; }

    /* ── Layout ── */
    .msg-shell {
        display: flex;
        height: calc(100vh - 100px);
        margin: 20px 0;
        border-radius: 28px;
        overflow: hidden;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        box-shadow: var(--glass-shadow);
    }

    /* ── Sidebar ── */
    .msg-sidebar {
        width: 360px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--glass-border);
        background: rgba(248, 250, 252, 0.6);
    }
    [data-theme="dark"] .msg-sidebar {
        background: rgba(10, 14, 28, 0.7);
        border-right-color: rgba(255,255,255,0.06);
    }

    /* Header */
    .msg-header {
        padding: 22px 20px 16px;
        border-bottom: 1px solid var(--glass-border);
    }
    [data-theme="dark"] .msg-header { border-bottom-color: rgba(255,255,255,0.06); }

    .msg-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .msg-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--text-color);
    }
    .msg-actions { display: flex; gap: 8px; }

    .msg-btn {
        width: 36px; height: 36px;
        border-radius: 10px;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        color: var(--secondary-text);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .msg-btn:hover { background: #E2E8F0; color: var(--text-color); transform: scale(1.05); }
    .msg-btn.primary {
        background: #0062FF;
        border-color: #0062FF;
        color: #fff;
        box-shadow: 0 4px 12px rgba(0,98,255,0.25);
    }
    .msg-btn.primary:hover {
        background: #0052D9;
        box-shadow: 0 6px 16px rgba(0,98,255,0.35);
        transform: scale(1.05);
    }
    [data-theme="dark"] .msg-btn {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.08);
        color: var(--secondary-text);
    }
    [data-theme="dark"] .msg-btn:hover { background: rgba(255,255,255,0.12); color: var(--text-color); }

    /* Search */
    .msg-search-wrap {
        position: relative;
    }
    .msg-search {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border-radius: 12px;
        border: 1.5px solid var(--glass-border);
        background: rgba(255,255,255,0.6);
        font-size: 13.5px;
        color: var(--text-color);
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
        transition: all 0.25s;
    }
    .msg-search::placeholder { color: var(--secondary-text); }
    .msg-search:focus {
        border-color: #0062FF;
        background: rgba(255,255,255,0.9);
        box-shadow: 0 0 0 3px rgba(0,98,255,0.1);
    }
    [data-theme="dark"] .msg-search {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.08);
        color: var(--text-color);
    }
    [data-theme="dark"] .msg-search::placeholder { color: rgba(255,255,255,0.3); }
    [data-theme="dark"] .msg-search:focus {
        background: rgba(255,255,255,0.08);
        border-color: rgba(77,148,255,0.5);
        box-shadow: 0 0 0 3px rgba(77,148,255,0.12);
    }
    .msg-search-icon {
        position: absolute;
        left: 12px; top: 50%;
        transform: translateY(-50%);
        color: var(--secondary-text);
        pointer-events: none;
        opacity: 0.6;
    }

    /* DM search results */
    .dm-results {
        display: none;
        position: absolute;
        left: 0; right: 0;
        top: calc(100% + 6px);
        background: var(--glass-bg);
        backdrop-filter: blur(30px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: 0 16px 48px rgba(0,0,0,0.1);
        z-index: 100;
        max-height: 280px;
        overflow-y: auto;
        padding: 6px;
    }
    [data-theme="dark"] .dm-results {
        background: rgba(12,16,30,0.98);
        border-color: rgba(255,255,255,0.08);
        box-shadow: 0 20px 60px rgba(0,0,0,0.6);
    }
    .dm-result-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        text-decoration: none;
        color: var(--text-color);
        transition: background 0.15s;
    }
    .dm-result-item:hover { background: rgba(0,98,255,0.06); }
    [data-theme="dark"] .dm-result-item:hover { background: rgba(77,148,255,0.1); }

    /* Filter tabs */
    .msg-filters {
        display: flex;
        gap: 4px;
        padding: 12px 20px 0;
    }
    .filter-tab {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        border: 1.5px solid transparent;
        color: var(--secondary-text);
        background: transparent;
        transition: all 0.2s;
        text-decoration: none;
        white-space: nowrap;
    }
    .filter-tab:hover { color: var(--text-color); background: rgba(0,98,255,0.06); }
    .filter-tab.active {
        background: #0062FF;
        color: #fff;
        box-shadow: 0 4px 12px rgba(0,98,255,0.25);
    }
    [data-theme="dark"] .filter-tab:hover { background: rgba(77,148,255,0.1); }

    /* Conv list */
    .conv-list {
        flex-grow: 1;
        overflow-y: auto;
        padding: 8px 8px 8px;
        scrollbar-width: thin;
        scrollbar-color: var(--glass-border) transparent;
    }
    .conv-list::-webkit-scrollbar { width: 4px; }
    .conv-list::-webkit-scrollbar-track { background: transparent; }
    .conv-list::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 4px; }

    .conv-item {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 11px 12px;
        border-radius: 14px;
        text-decoration: none;
        color: inherit;
        transition: all 0.18s;
        animation: convIn 0.4s ease both;
        position: relative;
    }
    .conv-item:hover {
        background: rgba(0,98,255,0.05);
        transform: translateX(2px);
    }
    [data-theme="dark"] .conv-item:hover { background: rgba(77,148,255,0.08); }

    @keyframes convIn {
        from { opacity: 0; transform: translateX(-10px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    .conv-avatar-wrap { position: relative; flex-shrink: 0; }
    .conv-avatar {
        width: 50px; height: 50px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
        background-color: var(--glass-border);
        flex-shrink: 0;
    }
    .conv-avatar.group { border-radius: 14px; }
    .conv-online {
        position: absolute;
        bottom: 1px; right: 1px;
        width: 12px; height: 12px;
        background: #22C55E;
        border-radius: 50%;
        border: 2px solid #F8FAFC;
    }
    [data-theme="dark"] .conv-online { border-color: #0B0F1A; }

    .conv-body { flex-grow: 1; min-width: 0; }
    .conv-row1 {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 3px;
    }
    .conv-name-wrap { display: flex; align-items: center; gap: 6px; min-width: 0; }
    .conv-name {
        font-size: 14.5px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--text-color);
    }
    .conv-name.unread { font-weight: 800; }
    .conv-badge-group {
        font-size: 9px; font-weight: 800;
        padding: 2px 6px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(0,98,255,0.1);
        color: #0062FF;
        flex-shrink: 0;
    }
    [data-theme="dark"] .conv-badge-group {
        background: rgba(77,148,255,0.12);
        color: #4D94FF;
    }
    .conv-time {
        font-size: 11px;
        color: var(--secondary-text);
        flex-shrink: 0;
        font-weight: 500;
    }
    .conv-preview {
        font-size: 13px;
        color: var(--secondary-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 400;
    }
    .conv-preview.unread {
        font-weight: 700;
        color: var(--text-color);
    }
    .conv-unread-dot {
        width: 9px; height: 9px;
        background: #0062FF;
        border-radius: 50%;
        flex-shrink: 0;
        box-shadow: 0 0 8px rgba(0,98,255,0.5);
        animation: dotPulse 2.5s ease infinite;
    }
    @keyframes dotPulse {
        0%,100% { box-shadow: 0 0 6px rgba(0,98,255,0.4); }
        50%      { box-shadow: 0 0 14px rgba(0,98,255,0.7); }
    }

    .conv-empty {
        text-align: center;
        padding: 48px 24px;
        color: var(--secondary-text);
    }
    .conv-empty-icon {
        width: 56px; height: 56px;
        margin: 0 auto 14px;
        background: rgba(0,98,255,0.06);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        color: #0062FF;
    }
    [data-theme="dark"] .conv-empty-icon { background: rgba(77,148,255,0.1); color: #4D94FF; }
    .conv-empty p { font-size: 13.5px; line-height: 1.5; margin: 0; }

    /* ── Right empty panel ── */
    .msg-welcome {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 48px;
        position: relative;
        overflow: hidden;
        background: transparent;
    }

    /* Decorative circles */
    .welcome-orb {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .orb-a {
        width: 380px; height: 380px;
        background: radial-gradient(circle, rgba(0,98,255,0.07) 0, transparent 70%);
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        animation: orbPulse 4s ease-in-out infinite;
    }
    .orb-b {
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(56,189,248,0.06) 0, transparent 70%);
        top: 20%; right: 15%;
        animation: orbPulse 5s ease-in-out infinite reverse;
    }
    @keyframes orbPulse {
        0%,100% { transform: translate(-50%,-50%) scale(1); }
        50%      { transform: translate(-50%,-50%) scale(1.08); }
    }
    .orb-b { animation: orbFloat 5s ease-in-out infinite; }
    @keyframes orbFloat {
        0%,100% { transform: translateY(0); }
        50%      { transform: translateY(-18px); }
    }

    /* Big icon */
    .welcome-icon-wrap {
        position: relative;
        width: 120px; height: 120px;
        margin-bottom: 28px;
        animation: welcomeIn 0.8s cubic-bezier(0.22,1,0.36,1) both;
    }
    @keyframes welcomeIn {
        from { opacity: 0; transform: scale(0.7) translateY(20px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .welcome-icon-bg {
        width: 100%; height: 100%;
        border-radius: 36px;
        background: linear-gradient(145deg, rgba(0,98,255,0.1), rgba(56,189,248,0.08));
        border: 1.5px solid rgba(0,98,255,0.15);
        display: flex; align-items: center; justify-content: center;
        color: #0062FF;
        box-shadow: 0 16px 48px rgba(0,98,255,0.12);
    }
    [data-theme="dark"] .welcome-icon-bg {
        background: linear-gradient(145deg, rgba(77,148,255,0.12), rgba(56,189,248,0.08));
        border-color: rgba(77,148,255,0.2);
        color: #4D94FF;
    }
    /* Floating dots around icon */
    .welcome-dot {
        position: absolute;
        border-radius: 50%;
        background: #0062FF;
        opacity: 0.35;
    }
    [data-theme="dark"] .welcome-dot { background: #4D94FF; }
    .wd1 { width: 8px; height: 8px; top: -4px; right: 16px; animation: wdFloat 3s ease infinite; }
    .wd2 { width: 5px; height: 5px; bottom: 8px; left: -8px; animation: wdFloat 3.5s ease infinite 0.5s; }
    .wd3 { width: 6px; height: 6px; top: 30%; right: -12px; animation: wdFloat 4s ease infinite 1s; }
    @keyframes wdFloat {
        0%,100% { transform: translateY(0); opacity: 0.35; }
        50%      { transform: translateY(-8px); opacity: 0.6; }
    }

    .welcome-text {
        text-align: center;
        animation: welcomeIn 0.8s 0.1s cubic-bezier(0.22,1,0.36,1) both;
    }
    .welcome-text h3 {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--text-color);
        margin: 0 0 10px;
    }
    .welcome-text h3 span {
        background: linear-gradient(135deg, #0062FF, #38BDF8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .welcome-text p {
        font-size: 14.5px;
        color: var(--secondary-text);
        line-height: 1.65;
        max-width: 320px;
        margin: 0 0 28px;
    }
    .welcome-btns {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
        animation: welcomeIn 0.8s 0.18s cubic-bezier(0.22,1,0.36,1) both;
    }
    .btn-welcome-primary {
        display: flex; align-items: center; gap: 8px;
        padding: 12px 22px;
        border-radius: 14px;
        border: none;
        background: #0062FF;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 16px rgba(0,98,255,0.3);
    }
    .btn-welcome-primary:hover {
        background: #0052D9;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,98,255,0.4);
    }
    .btn-welcome-secondary {
        display: flex; align-items: center; gap: 8px;
        padding: 12px 22px;
        border-radius: 14px;
        border: 1.5px solid var(--glass-border);
        background: transparent;
        color: var(--text-color);
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-welcome-secondary:hover {
        background: rgba(0,98,255,0.06);
        border-color: #0062FF;
        color: #0062FF;
        transform: translateY(-2px);
    }
    [data-theme="dark"] .btn-welcome-secondary:hover {
        background: rgba(77,148,255,0.1);
        border-color: #4D94FF;
        color: #4D94FF;
    }

    /* Stats row */
    .welcome-stats {
        display: flex;
        gap: 24px;
        margin-top: 36px;
        animation: welcomeIn 0.8s 0.25s cubic-bezier(0.22,1,0.36,1) both;
    }
    .stat-item {
        text-align: center;
    }
    .stat-num {
        font-size: 20px;
        font-weight: 800;
        color: #0062FF;
        letter-spacing: -0.5px;
    }
    [data-theme="dark"] .stat-num { color: #4D94FF; }
    .stat-label {
        font-size: 11.5px;
        color: var(--secondary-text);
        font-weight: 500;
        margin-top: 2px;
    }
    .stat-divider {
        width: 1px;
        background: var(--glass-border);
        align-self: center;
        height: 28px;
    }

    /* ── Modals ── */
    .msg-modal-content {
        background: var(--glass-bg);
        backdrop-filter: blur(40px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        width: 90%;
        max-width: 480px;
        overflow: hidden;
        animation: modalIn 0.35s cubic-bezier(0.22,1,0.36,1);
        box-shadow: 0 32px 80px rgba(0,0,0,0.15);
    }
    [data-theme="dark"] .msg-modal-content {
        background: rgba(12,16,30,0.97);
        box-shadow: 0 32px 80px rgba(0,0,0,0.65);
        border-color: rgba(255,255,255,0.07);
    }
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-hd {
        padding: 20px 24px;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    [data-theme="dark"] .modal-hd { border-bottom-color: rgba(255,255,255,0.07); }
    .modal-hd h3 { margin: 0; font-size: 17px; font-weight: 800; }
    .modal-close {
        width: 30px; height: 30px;
        border-radius: 8px;
        background: rgba(0,0,0,0.05);
        border: none;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--secondary-text);
        font-size: 18px;
        transition: all 0.15s;
    }
    .modal-close:hover { background: rgba(239,68,68,0.1); color: #ef4444; }
    [data-theme="dark"] .modal-close { background: rgba(255,255,255,0.07); }

    .modal-body { padding: 20px 24px; }

    .modal-field { margin-bottom: 18px; }
    .modal-label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--secondary-text);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .modal-input {
        width: 100%;
        padding: 11px 14px;
        border-radius: 12px;
        border: 1.5px solid var(--glass-border);
        background: rgba(255,255,255,0.6);
        font-size: 14px;
        font-family: inherit;
        color: var(--text-color);
        outline: none;
        box-sizing: border-box;
        transition: all 0.2s;
    }
    .modal-input:focus {
        border-color: #0062FF;
        background: rgba(255,255,255,0.9);
        box-shadow: 0 0 0 3px rgba(0,98,255,0.1);
    }
    [data-theme="dark"] .modal-input {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.09);
        color: var(--text-color);
    }
    [data-theme="dark"] .modal-input:focus {
        border-color: rgba(77,148,255,0.5);
        background: rgba(255,255,255,0.08);
        box-shadow: 0 0 0 3px rgba(77,148,255,0.12);
    }
    .modal-input::placeholder { color: var(--secondary-text); }
    [data-theme="dark"] .modal-input::placeholder { color: rgba(255,255,255,0.3); }

    .modal-code-input {
        font-size: 28px;
        font-weight: 800;
        text-align: center;
        letter-spacing: 10px;
        text-transform: uppercase;
        padding: 16px 14px;
    }

    .chips-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        min-height: 0;
        margin-bottom: 10px;
    }
    .chip {
        background: rgba(0,98,255,0.1);
        color: #0062FF;
        padding: 5px 10px 5px 12px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        display: flex; align-items: center; gap: 6px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .chip:hover { background: rgba(239,68,68,0.1); color: #ef4444; }
    [data-theme="dark"] .chip { background: rgba(77,148,255,0.12); color: #4D94FF; }

    .friends-list {
        max-height: 200px;
        overflow-y: auto;
        background: rgba(0,0,0,0.02);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 4px;
        scrollbar-width: thin;
    }
    [data-theme="dark"] .friends-list {
        background: rgba(255,255,255,0.03);
        border-color: rgba(255,255,255,0.07);
    }
    .friend-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 9px;
        cursor: pointer;
        transition: background 0.15s;
        font-size: 13.5px;
        font-weight: 600;
    }
    .friend-row:hover { background: rgba(0,98,255,0.06); }
    .friend-row.selected { background: rgba(0,98,255,0.08); }
    [data-theme="dark"] .friend-row:hover { background: rgba(77,148,255,0.1); }

    .btn-modal-submit {
        width: 100%;
        padding: 13px;
        border-radius: 13px;
        border: none;
        background: #0062FF;
        color: #fff;
        font-size: 14.5px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 16px rgba(0,98,255,0.25);
    }
    .btn-modal-submit:hover {
        background: #0052D9;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0,98,255,0.35);
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .msg-shell { border-radius: 0; border: none; margin: 0; height: calc(100vh - 60px); }
        .msg-sidebar { width: 100%; border-right: none; }
        .msg-welcome { display: none; }
        .container { max-width: 100% !important; padding: 0 !important; }
    }
</style>

<div class="msg-shell">
    {{-- ── Sidebar ── --}}
    <div class="msg-sidebar">
        <div class="msg-header">
            <div class="msg-header-top">
                <span class="msg-title">Tin nhắn</span>
                <div class="msg-actions">
                    <button class="msg-btn" onclick="openJoinCodeModal()" title="Nhập mã nhóm">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                    </button>
                    <button class="msg-btn primary" onclick="openGroupModal()" title="Tạo nhóm mới">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="msg-search-wrap">
                <svg class="msg-search-icon" viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input class="msg-search" type="text" id="dmSearch" placeholder="Tìm bạn bè để nhắn tin...">
                <div class="dm-results" id="dmSearchResults"></div>
            </div>
        </div>

        {{-- Filter tabs --}}
        <div class="msg-filters">
            <a href="{{ route('messages.index', ['filter' => 'all']) }}"
               class="filter-tab {{ $filter === 'all' ? 'active' : '' }}">Tất cả</a>
            <a href="{{ route('messages.index', ['filter' => 'friends']) }}"
               class="filter-tab {{ $filter === 'friends' ? 'active' : '' }}">Bạn bè</a>
            <a href="{{ route('messages.index', ['filter' => 'groups']) }}"
               class="filter-tab {{ $filter === 'groups' ? 'active' : '' }}">Nhóm</a>
            <a href="{{ route('messages.index', ['filter' => 'unread']) }}"
               class="filter-tab {{ $filter === 'unread' ? 'active' : '' }}">Chưa đọc</a>
        </div>

        <div class="conv-list">
            @forelse($conversations as $i => $conv)
            @php
                $isGroup = $conv->type === 'group';
                $convName = $conv->name;
                $convAvatar = $conv->avatar_url;
                if (!$isGroup) {
                    $other = $conv->users->where('id', '!=', auth()->id())->first();
                    $convName = $other ? $other->username : 'Người dùng';
                    $convAvatar = $other ? $other->avatar_url : null;
                }
                $unread = $conv->lastMessage && !$conv->lastMessage->is_read && $conv->lastMessage->sender_id !== auth()->id();
            @endphp
            <a href="{{ route('messages.show', $conv->id) }}"
               class="conv-item"
               style="animation-delay: {{ $i * 40 }}ms">
                <div class="conv-avatar-wrap">
                    <div class="conv-avatar {{ $isGroup ? 'group' : '' }}"
                         style="background-image: url('{{ $convAvatar ?? asset('avatars/user.png') }}')"></div>
                    @if(!$isGroup && isset($other) && $other && $other->is_online)
                    <div class="conv-online"></div>
                    @endif
                </div>
                <div class="conv-body">
                    <div class="conv-row1">
                        <div class="conv-name-wrap">
                            <span class="conv-name {{ $unread ? 'unread' : '' }}">{{ $convName }}</span>
                            @if($isGroup)
                            <span class="conv-badge-group">nhóm</span>
                            @endif
                        </div>
                        <span class="conv-time">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
                    </div>
                    <div class="conv-preview {{ $unread ? 'unread' : '' }}">
                        @if($conv->lastMessage)
                            {{ $conv->lastMessage->sender_id === auth()->id() ? 'Bạn: ' : '' }}{{ $conv->lastMessage->message_type === 'text' ? \Illuminate\Support\Str::limit($conv->lastMessage->content, 45) : ($conv->lastMessage->message_type === 'image' ? '📷 Đã gửi ảnh' : '📎 Đã gửi tệp đính kèm') }}
                        @else
                            Bắt đầu cuộc trò chuyện...
                        @endif
                    </div>
                </div>
                @if($unread)
                <div class="conv-unread-dot"></div>
                @endif
            </a>
            @empty
            <div class="conv-empty">
                <div class="conv-empty-icon">
                    <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <p>Chưa có cuộc trò chuyện nào.<br>Hãy kết bạn để bắt đầu nhắn tin!</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Welcome panel ── --}}
    <div class="msg-welcome">
        <div class="welcome-orb orb-a"></div>
        <div class="welcome-orb orb-b"></div>

        <div class="welcome-icon-wrap">
            <div class="welcome-icon-bg">
                <svg viewBox="0 0 24 24" width="52" height="52" stroke="currentColor" stroke-width="1.5" fill="none">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="welcome-dot wd1"></div>
            <div class="welcome-dot wd2"></div>
            <div class="welcome-dot wd3"></div>
        </div>

        <div class="welcome-text">
            <h3>Tin nhắn <span>của bạn</span></h3>
            <p>Chọn một cuộc trò chuyện hoặc bắt đầu kết nối với bạn bè. Mọi tin nhắn đều được lưu trữ an toàn.</p>
            <div class="welcome-btns">
                <button class="btn-welcome-primary" onclick="openGroupModal()">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Tạo nhóm mới
                </button>
                <button class="btn-welcome-secondary" onclick="openJoinCodeModal()">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Nhập mã nhóm
                </button>
            </div>
        </div>

        <div class="welcome-stats">
            <div class="stat-item">
                <div class="stat-num">{{ $conversations->count() }}</div>
                <div class="stat-label">Cuộc trò chuyện</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <div class="stat-num">{{ $friends->count() }}</div>
                <div class="stat-label">Bạn bè</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <div class="stat-num">{{ $conversations->where('type','group')->count() }}</div>
                <div class="stat-label">Nhóm</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Group Modal ── --}}
<div id="groupModal" class="modal" style="align-items:center; padding-top:0;">
    <div class="msg-modal-content">
        <div class="modal-hd">
            <h3>Tạo nhóm trò chuyện</h3>
            <button class="modal-close" onclick="closeGroupModal()">×</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('messages.group') }}" method="POST">
                @csrf
                <div class="modal-field">
                    <label class="modal-label">Tên nhóm</label>
                    <input class="modal-input" type="text" name="name" required placeholder="VD: Nhóm học tập...">
                </div>
                <div class="modal-field">
                    <label class="modal-label">Thêm thành viên</label>
                    <div class="chips-wrap" id="selectedChips"></div>
                    <input class="modal-input" type="text" id="friendSearch" placeholder="Tìm kiếm bạn bè...">
                </div>
                <div class="friends-list" id="friendsList"></div>
                <div id="hiddenInputs"></div>
                <button type="submit" class="btn-modal-submit" style="margin-top:20px;">Tạo nhóm</button>
            </form>
        </div>
    </div>
</div>

{{-- ── Join Code Modal ── --}}
<div id="joinCodeModal" class="modal" style="align-items:center; padding-top:0;">
    <div class="msg-modal-content" style="max-width:380px;">
        <div class="modal-hd">
            <h3>Gia nhập bằng mã</h3>
            <button class="modal-close" onclick="closeJoinCodeModal()">×</button>
        </div>
        <div class="modal-body">
            <p style="font-size:13.5px; color:var(--secondary-text); margin:0 0 20px; line-height:1.6;">
                Nhập mã 5 ký tự được cung cấp bởi quản trị viên nhóm.
            </p>
            <form action="{{ route('messages.join_by_code') }}" method="POST">
                @csrf
                <div class="modal-field">
                    <input class="modal-input modal-code-input" type="text" name="code"
                           maxlength="5" required placeholder="ABCDE" autofocus>
                </div>
                <button type="submit" class="btn-modal-submit">Tham gia nhóm</button>
            </form>
        </div>
    </div>
</div>

<script>
const friendsUrl = "{{ url('/api/friends/search') }}";
const selectedFriends = new Map();

/* ── Modals ── */
function openGroupModal()    { document.getElementById('groupModal').style.display = 'flex'; loadFriends(''); }
function closeGroupModal()   { document.getElementById('groupModal').style.display = 'none'; }
function openJoinCodeModal() { document.getElementById('joinCodeModal').style.display = 'flex'; }
function closeJoinCodeModal(){ document.getElementById('joinCodeModal').style.display = 'none'; }

window.onclick = e => {
    if (e.target.id === 'groupModal')    closeGroupModal();
    if (e.target.id === 'joinCodeModal') closeJoinCodeModal();
};

/* ── Friend search in group modal ── */
document.getElementById('friendSearch').addEventListener('input', function() {
    loadFriends(this.value.trim());
});

function loadFriends(q) {
    fetch(friendsUrl + '?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(list => {
            const el = document.getElementById('friendsList');
            el.innerHTML = list.length ? '' : '<div style="padding:16px;text-align:center;font-size:13px;opacity:.5">Không tìm thấy</div>';
            list.forEach(f => {
                const d = document.createElement('div');
                d.className = 'friend-row' + (selectedFriends.has(f.id) ? ' selected' : '');
                d.innerHTML = `<div class="avatar" style="width:32px;height:32px;background-image:url('${f.avatar_url}');background-size:cover;flex-shrink:0"></div>
                               <span>${f.username}</span>
                               ${selectedFriends.has(f.id) ? '<svg style="margin-left:auto;color:#0062FF" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"/></svg>' : ''}`;
                d.onclick = () => { toggleFriend(f); loadFriends(document.getElementById('friendSearch').value); };
                el.appendChild(d);
            });
        });
}

function toggleFriend(f) {
    if (selectedFriends.has(f.id)) selectedFriends.delete(f.id);
    else selectedFriends.set(f.id, f);
    renderChips();
}

function renderChips() {
    const chips = document.getElementById('selectedChips');
    const hidden = document.getElementById('hiddenInputs');
    chips.innerHTML = '';
    hidden.innerHTML = '';
    selectedFriends.forEach(f => {
        chips.innerHTML += `<div class="chip" onclick="toggleFriend({id:${f.id},username:'${f.username}'})">
            ${f.username} <span style="font-size:16px;line-height:1">×</span>
        </div>`;
        hidden.innerHTML += `<input type="hidden" name="user_ids[]" value="${f.id}">`;
    });
}

/* ── DM search in sidebar ── */
const dmSearch  = document.getElementById('dmSearch');
const dmResults = document.getElementById('dmSearchResults');

dmSearch.addEventListener('input', function() {
    const q = this.value.trim();
    if (!q) { dmResults.style.display = 'none'; return; }
    fetch(friendsUrl + '?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(list => {
            dmResults.innerHTML = '';
            if (!list.length) {
                dmResults.innerHTML = '<div style="padding:16px;text-align:center;font-size:13px;opacity:.5">Không tìm thấy bạn bè</div>';
                dmResults.style.display = 'block'; return;
            }
            list.forEach(f => {
                const a = document.createElement('a');
                a.className = 'dm-result-item';
                a.href = '/messages/direct/' + f.id;
                a.innerHTML = `<div class="avatar" style="width:38px;height:38px;background-image:url('${f.avatar_url}');background-size:cover;flex-shrink:0"></div>
                               <div><div style="font-weight:700;font-size:13.5px">${f.username}</div>
                               <div style="font-size:12px;opacity:.6">Nhắn tin trực tiếp</div></div>`;
                dmResults.appendChild(a);
            });
            dmResults.style.display = 'block';
        });
});

document.addEventListener('click', e => {
    if (!dmSearch.contains(e.target) && !dmResults.contains(e.target)) {
        dmResults.style.display = 'none';
    }
});
</script>
@endsection
