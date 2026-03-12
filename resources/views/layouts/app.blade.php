<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Threads Clone') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Apple Glassmorphism Colors - Updated to Pale Sky Blue */
            --bg-main: #D1E9F6;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.5);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.05);

            --text-color: #1d1d1f;
            --secondary-text: #6e6e73;
            --accent-color: #0071e3;

            --modal-overlay: rgba(0, 0, 0, 0.15);
            --lightbox-bg: rgba(255, 255, 255, 0.85);
            --card-radius: 20px;
        }

        [data-theme="dark"] {
            --bg-main: #0a0a0a;
            --glass-bg: rgba(28, 28, 30, 0.8);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);

            --text-color: #f5f5f7;
            --secondary-text: #98989d;
            --accent-color: #0a84ff;
            --lightbox-bg: rgba(0, 0, 0, 0.85);
        }

        [data-theme="dark"] body {
            background-image: radial-gradient(at 0% 0%, hsla(240, 10%, 15%, 1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(240, 10%, 10%, 1) 0, transparent 50%);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-main);
            background-image: radial-gradient(at 0% 0%, hsla(200, 100%, 90%, 1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(190, 100%, 85%, 1) 0, transparent 50%);
            background-attachment: fixed;
            color: var(--text-color);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 650px;
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.0);
            /* Transparent to show gradient */
            backdrop-filter: blur(0px);
            /* Main container doesn't need blur */
            border: none;
        }

        /* Glass Sidebar */
        .sidebar-nav {
            position: fixed;
            left: 30px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            gap: 35px;
            box-shadow: var(--glass-shadow);
            z-index: 1000;
        }

        .nav-item {
            color: var(--secondary-text);
            text-decoration: none;
            font-size: 28px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50px;
            height: 50px;
            border-radius: 15px;
        }

        .nav-item:hover,
        .nav-item.active {
            color: var(--accent-color);
            background: rgba(255, 255, 255, 0.5);
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        header {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
        }

        .post-card {
            display: flex;
            gap: 15px;
            padding: 20px 30px 20px 20px;
            /* Lề phải 30px cố định */
            transition: background 0.2s;
            position: relative;
            width: 100%;
            box-sizing: border-box;
            /* Quan trọng: Đảm bảo padding không làm tăng chiều rộng card */
        }

        .post-card:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .post-content {
            flex-grow: 1;
        }

        /* Glass Cards & Bubbles */
        .glass-bubble {
            background: var(--glass-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            box-shadow: var(--glass-shadow);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .main-wrapper {
            margin: 20px 0;
            overflow: hidden;
        }

        .post-divider {
            height: 0.5px;
            background: #000000;
            margin: 0;
            opacity: 0.8;
        }

        /* Notifications Modal Styles */
        #notifModal .modal-content {
            max-width: 500px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow: hidden;
        }

        .notif-list {
            overflow-y: auto;
            flex-grow: 1;
            padding: 10px 20px;
        }

        .nav-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ff3b30;
            color: white;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
            border: 2px solid var(--glass-bg);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 10;
            pointer-events: none;
            line-height: 1;
            animation: badge-pulse 2s infinite;
        }

        @keyframes badge-pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 59, 48, 0.7);
            }

            70% {
                transform: scale(1.15);
                box-shadow: 0 0 0 6px rgba(255, 59, 48, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 59, 48, 0);
            }
        }

        .nav-item.active .nav-badge,
        .nav-item:hover .nav-badge {
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Modal Styles - Modern Side Panel (Parallel to Taskbar) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.05);
            /* Làm mờ nền cực nhẹ để vẫn thấy feed */
            backdrop-filter: blur(2px);
            z-index: 2000;
            justify-content: flex-end;
            /* Đẩy bảng sang bên phải */
            align-items: center;
        }

        .modal-content {
            background: var(--glass-bg);
            backdrop-filter: blur(60px);
            -webkit-backdrop-filter: blur(60px);
            border-left: 1px solid var(--glass-border);
            box-shadow: -10px 0 50px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 500px;
            /* Độ rộng của bảng bình luận */
            height: 95vh;
            margin-right: 20px;
            /* Khoảng cách với mép phải */
            border-radius: 32px;
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow: hidden;
            animation: panelSlideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes panelSlideIn {
            0% {
                transform: translateX(100%) scale(0.95);
                opacity: 0;
            }

            100% {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        /* Khi mở modal, vẫn cho phép cuộn feed ở dưới để xem bài khác */
        body.modal-open {
            overflow: hidden;
            /* Giữ feed đứng yên khi đang tập trung bình luận */
        }

        @media (max-width: 900px) {
            .modal-content {
                max-width: 100%;
                margin-right: 0;
                height: 80vh;
                margin-top: auto;
                border-radius: 32px 32px 0 0;
                animation: panelSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            }
        }

        @keyframes panelSlideUp {
            0% {
                transform: translateY(100%);
            }

            100% {
                transform: translateY(0);
            }
        }

        /* Inputs & Buttons */
        input,
        textarea,
        .form-control {
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 12px 15px;
            color: var(--text-color);
            font-family: inherit;
            transition: all 0.2s;
        }

        input:focus,
        textarea:focus {
            background: rgba(255, 255, 255, 0.5);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.3);
        }

        .btn-post,
        button[type="submit"] {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 122, 255, 0.4);
            transition: transform 0.2s;
        }

        .btn-post:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 122, 255, 0.5);
        }

        .avatar {
            border: 2px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 50% !important;
            background-position: center;
        }

        /* Main Logo Styles */
        .main-logo {
            position: fixed;
            top: 25px;
            left: 25px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            z-index: 4000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .main-logo:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .main-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 900px) {
            .main-logo {
                top: 15px;
                left: 15px;
                width: 45px;
                height: 45px;
            }
        }

        /* Post Media Styles - Mix between Grid and Horizontal Scroll */
        .post-media-container {
            margin-top: 12px;
            border-radius: 14px;
            overflow: hidden;
        }

        /* Khi có nhiều ảnh: Cuộn ngang */
        .post-media-horizontal {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            scroll-snap-type: x mandatory;
            padding-bottom: 4px;
        }

        .post-media-horizontal::-webkit-scrollbar {
            display: none;
        }

        .post-media-horizontal .post-media-item {
            flex: 0 0 130px;
            /* Giảm từ 180px xuống 130px */
            scroll-snap-align: start;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            aspect-ratio: 1 / 1;
            background: rgba(0, 0, 0, 0.05);
        }

        /* Khi chỉ có 1 ảnh: Hiển thị to tự nhiên */
        .post-media-single {
            display: inline-block;
            /* Chuyển sang inline-block để khung ôm vừa ảnh */
            max-width: 80%;
            /* Giới hạn chiều rộng */
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }

        .post-media-item img,
        .post-media-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .post-media-single img,
        .post-media-single video {
            width: auto;
            max-width: 100%;
            max-height: 300px;
            /* Giảm từ 450px xuống 300px */
            object-fit: contain;
            /* Đổi sang contain để không bị mất góc ảnh khi nhỏ lại */
            display: block;
        }

        /* Comment Threading & Badges */
        .comment-thread-line {
            position: absolute;
            left: 19px;
            top: 45px;
            bottom: -15px;
            width: 2px;
            background: var(--glass-border);
            border-radius: 1px;
            z-index: 1;
        }

        .author-badge {
            background: var(--accent-color);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 6px;
            margin-left: 6px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .comment-item {
            position: relative;
            animation: fadeIn 0.3s ease-out;
        }

        /* Reposted Item Styling */
        .post-card.is-repost {
            background: rgba(0, 195, 0, 0.05);
            /* Xanh lá nhạt cho bài đăng lại */
            border-left: 4px solid #00c300;
            border-radius: 20px;
        }

        .repost-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #00c300;
            font-weight: 700;
            margin-bottom: 10px;
            padding-left: 5px;
        }

        /* Side Panel Comment Modal - Taskbar Style Mirror (Completely Outside) */
        .comment-modal {
            display: none;
            position: fixed;
            /* Căn lề trên khớp với icon Trang chủ (50% - nửa chiều cao sidebar + padding 30px) */
            top: calc(50% - 237.5px);
            right: 30px;
            width: 400px;
            /* Chiều cao linh hoạt để không bị tràn lề dưới */
            height: calc(100vh - (50% - 237.5px) - 30px);
            max-height: 85vh;
            background: var(--glass-bg);
            backdrop-filter: blur(50px);
            -webkit-backdrop-filter: blur(50px);
            border: 1px solid var(--glass-border);
            border-radius: 35px;
            box-shadow: var(--glass-shadow);
            z-index: 3000;
            flex-direction: column;
            animation: mirrorSlideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes mirrorSlideIn {
            0% {
                transform: translateX(150px);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .comment-modal-header {
            padding: 18px 25px;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .comment-modal-body {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px 25px;
            scrollbar-width: none;
        }

        .comment-modal-body::-webkit-scrollbar {
            display: none;
        }

        .comment-modal-footer {
            padding: 20px 25px;
            border-top: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.02);
            border-radius: 0 0 35px 35px;
        }

        /* Dropdown Menu Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            min-width: 180px;
            box-shadow: var(--glass-shadow);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            z-index: 100;
            overflow: hidden;
            margin-top: 8px;
            animation: dropdownFade 0.2s ease-out;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-content a,
        .dropdown-content button {
            color: var(--text-color);
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            border: none;
            background: transparent;
            text-align: left;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .dropdown-content a:hover,
        .dropdown-content button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .dropdown-content .danger {
            color: #ff3b30;
        }

        .show {
            display: block;
        }

        /* Modal Open State - Prevent Body Scroll */
        body.modal-open {
            overflow: hidden !important;
            height: 100vh;
        }

        /* Modal Styles - Apple Style */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--modal-overlay);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            z-index: 2000;
            justify-content: center;
            align-items: flex-start;
            padding-top: 100px;
            overflow-y: auto;
        }

        .modal-content {
            background: var(--glass-bg);
            backdrop-filter: blur(50px);
            -webkit-backdrop-filter: blur(50px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            padding: 30px;
            width: 90%;
            max-width: 550px;
            margin-bottom: 50px;
            animation: modalPop 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        @keyframes modalPop {
            0% {
                transform: translateY(20px) scale(0.98);
                opacity: 0;
            }

            100% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Mobile Nav */
        @media (max-width: 900px) {
            .sidebar-nav {
                position: fixed;
                bottom: 20px;
                left: 50%;
                top: auto;
                transform: translateX(-50%);
                width: 90%;
                max-width: 400px;
                flex-direction: row;
                justify-content: space-around;
                padding: 15px 20px;
                border-radius: 30px;
                z-index: 3000;
            }

            header {
                top: 0;
                margin: 0 0 20px 0;
                border-radius: 0 0 24px 24px;
            }

            .container {
                padding-bottom: 100px;
            }
        }
    </style>
    <style>
        /* Like Button Animations */
        @keyframes heartPop {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.4);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes sparkle {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
                opacity: 1;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .like-animate {
            animation: heartPop 0.45s cubic-bezier(0.17, 0.89, 0.32, 1.49);
        }

        .like-btn.liked {
            color: #ff3b30 !important;
        }

        .like-btn.liked svg {
            fill: #ff3b30 !important;
            stroke: #ff3b30 !important;
        }

        .sparkle-effect {
            position: absolute;
            pointer-events: none;
            width: 40px;
            height: 40px;
            background: radial-gradient(circle, #ff3b30 20%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
            animation: sparkle 0.5s ease-out forwards;
        }
    </style>
</head>

<body class="{{ request()->is('messages*') ? 'messages-page' : '' }}">
    <a href="/" class="main-logo" title="{{ config('app.name') }}">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </a>

    <nav class="sidebar-nav" style="z-index: 3000;">
        <!-- Home -->
        <a href="/" class="nav-item {{ request()->is('/') ? 'active' : '' }}" title="{{ __('Home') }}">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </a>

        <!-- Search -->
        <a href="{{ route('search') }}" class="nav-item {{ request()->is('search*') ? 'active' : '' }}" title="{{ __('Search') }}">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </a>

        <!-- Messages -->
        <a href="{{ route('messages.index') }}" class="nav-item {{ request()->is('messages*') ? 'active' : '' }}" title="{{ __('Messages') }}" style="position: relative;">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
            </svg>
            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
            <div class="nav-badge">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</div>
            @endif
        </a>

        <!-- Notifications (Bell) -->
        <a href="javascript:void(0)" onclick="openNotifModal()" class="nav-item {{ request()->is('notifications*') ? 'active' : '' }}" title="{{ __('Notifications') }}" style="position: relative;">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
            <div id="notif-badge" class="nav-badge">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</div>
            @endif
        </a>

        <!-- Groups -->
        <a href="{{ route('groups.index') }}" class="nav-item {{ request()->is('groups*') ? 'active' : '' }}" title="Cộng đồng">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </a>

        <!-- Profile -->
        <a href="{{ route('profile.me') }}" class="nav-item {{ request()->is('profile*') ? 'active' : '' }}" title="{{ __('Profile') }}">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </a>

        <!-- Settings -->
        <a href="{{ route('settings') }}" class="nav-item {{ request()->is('settings*') ? 'active' : '' }}" title="{{ __('Settings') }}">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
        </a>
    </nav>

    <div class="container">
        <div class="glass-bubble main-wrapper">
            @if(!request()->is('messages*'))
            <header>
                <div style="width: 30px;"></div>
                <div></div>
                <div style="width: 30px;"></div>
            </header>
            @endif

            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Modal Window Đăng bài -->
    <div id="postModal" class="modal" style="backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
        <div class="modal-content glass-bubble" style="max-width: 600px; border-radius: 32px; padding: 0; overflow: hidden; border: 1px solid rgba(255,255,255,0.4); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
            <!-- Modal Header -->
            <div style="padding: 20px 25px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.05);">
                <span style="width: 32px;"></span>
                <h3 id="modalMainTitle" style="margin: 0; font-size: 18px; font-weight: 800; letter-spacing: -0.5px; color: var(--text-color);">Tạo bài viết</h3>
                <div onclick="closeModal()" style="cursor: pointer; width: 34px; height: 34px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.1)'" onmouseout="this.style.background='rgba(0,0,0,0.05)'">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </div>
            </div>

            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" style="padding: 0;">
                @csrf
                <div style="padding: 25px; max-height: 65vh; overflow-y: auto;">
                    <!-- User & Group Context Area -->
                    <div id="modalContextArea" style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="position: relative;">
                                <div class="avatar" style="background-image: url('{{ auth()->check() ? auth()->user()->avatar_url : '' }}'); background-size: cover; width: 52px; height: 52px; border: 2px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 16px !important;"></div>
                                <div id="groupMiniAvatar" style="display: none; position: absolute; bottom: -4px; right: -4px; width: 24px; height: 24px; border-radius: 8px; border: 2px solid white; background-size: cover; background-color: var(--accent-color); box-shadow: 0 2px 6px rgba(0,0,0,0.2);"></div>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 16px; color: var(--text-color);">{{ auth()->check() ? auth()->user()->username : 'Khách' }}</div>
                                <div id="modalPostStatus" style="font-size: 12px; color: var(--secondary-text); font-weight: 600; display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                    Công khai
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Textarea -->
                    @if($errors->has('content'))
                        <div style="color: #ff3b30; font-size: 13px; font-weight: 700; margin-bottom: 10px;">{{ $errors->first('content') }}</div>
                    @endif
                    <textarea name="content" rows="4" placeholder="Bạn đang nghĩ gì?" required autofocus
                              style="width: 100%; border: none; background: transparent; color: inherit; font-size: 18px; line-height: 1.6; outline: none; padding: 0; resize: none; min-height: 120px; font-weight: 500;">{{ old('content') }}</textarea>

                    <!-- Link Input Area -->
                    <div id="linkInputContainer" style="display: none; margin-top: 15px; background: rgba(0,113,227,0.03); padding: 15px; border-radius: 18px; border: 1px dashed var(--accent-color); animation: slideDown 0.3s ease;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: white; display: flex; align-items: center; justify-content: center; color: var(--accent-color); box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            </div>
                            <input type="url" name="link_url" id="linkInput" placeholder="Dán link liên kết tại đây..."
                                   style="flex-grow: 1; border: none; background: transparent; color: inherit; font-size: 14px; outline: none; font-weight: 600;">
                        </div>
                    </div>

                    <!-- Media Preview Area -->
                    <div id="mediaPreviewContainer" style="display: none; margin-top: 20px;">
                        <div id="mediaPreviewGrid" style="display: flex; gap: 12px; overflow-x: auto; padding: 5px; scroll-snap-type: x mandatory; scrollbar-width: none;"></div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div style="padding: 15px 25px 25px; border-top: 1px solid var(--glass-border); background: rgba(255,255,255,0.02); display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 8px;">
                        <label style="cursor: pointer; width: 44px; height: 44px; border-radius: 14px; background: rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: center; color: var(--secondary-text); transition: all 0.2s;" 
                               title="Thêm ảnh" onmouseover="this.style.background='rgba(0,113,227,0.1)'; this.style.color='var(--accent-color)'" onmouseout="this.style.background='rgba(0,0,0,0.03)'; this.style.color='var(--secondary-text)'">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <input type="file" name="media[]" accept="image/*,.gif" id="mediaInput" style="display: none;" onchange="previewMedia(event)" multiple>
                        </label>
                        <label style="cursor: pointer; width: 44px; height: 44px; border-radius: 14px; background: rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: center; color: var(--secondary-text); transition: all 0.2s;" 
                               title="Thêm tài liệu" onmouseover="this.style.background='rgba(0,113,227,0.1)'; this.style.color='var(--accent-color)'" onmouseout="this.style.background='rgba(0,0,0,0.03)'; this.style.color='var(--secondary-text)'">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                            <input type="file" name="media[]" id="fileInput" style="display: none;" onchange="previewMedia(event)" multiple>
                        </label>
                        <div onclick="toggleLinkInput()" id="linkToggleButton" style="cursor: pointer; width: 44px; height: 44px; border-radius: 14px; background: rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: center; color: var(--secondary-text); transition: all 0.2s;" 
                             title="Gán link" onmouseover="this.style.background='rgba(0,113,227,0.1)'; this.style.color='var(--accent-color)'" onmouseout="this.style.background='rgba(0,0,0,0.03)'; this.style.color='var(--secondary-text)'">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        </div>
                    </div>

                    <button type="submit" class="btn-post" style="padding: 12px 35px; border-radius: 16px; font-weight: 800; border: none; font-size: 15px; box-shadow: 0 10px 25px rgba(0, 113, 227, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 30px rgba(0, 113, 227, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0, 113, 227, 0.3)'">Đăng bài</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Window Thông báo -->
    <div id="notifModal" class="modal">
        <div class="modal-content glass-bubble">
            <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">Thông báo</h3>
                <span style="cursor: pointer; font-size: 20px; opacity: 0.6;" onclick="closeNotifModal()">&times;</span>
            </div>
            <div id="notifList" class="notif-list">
                <p style="text-align: center; padding: 20px; color: var(--secondary-text);">Đang tải thông báo...</p>
            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div id="imageLightbox" class="modal" onclick="closeLightbox()" style="background: var(--lightbox-bg); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); z-index: 5000; display: none; align-items: center; justify-content: center; padding: 0;">
        <img id="lightboxImg" style="max-width: 90%; max-height: 90%; object-fit: contain; box-shadow: 0 10px 40px rgba(0,0,0,0.2); transition: transform 0.3s ease; border-radius: 8px;">
    </div>

    <!-- Welcome Modal -->
    @if(session('show_welcome'))
    <div id="welcomeModal" class="modal" style="display: flex; background: rgba(0,0,0,0.15); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); align-items: center; padding-top: 0;">
        <div class="modal-content glass-bubble" style="max-width: 420px; text-align: center; padding: 50px 40px; border-radius: 40px; border: 1px solid rgba(255,255,255,0.6); box-shadow: 0 30px 60px rgba(0,0,0,0.12);">
            <div style="width: 80px; height: 80px; background: white; border-radius: 24px; margin: 0 auto 25px; display: flex; align-items: center; justify-content: center; font-size: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); animation: welcomeEmoji 1s infinite alternate ease-in-out;">🎉</div>
            <h2 style="margin: 0 0 12px 0; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; color: #1d1d1f;">{{ __('Welcome!') }}</h2>
            <p style="color: #6e6e73; line-height: 1.6; margin-bottom: 35px; font-size: 16px; font-weight: 500;">
                {{ __('Chào mừng bạn đã gia nhập') }} <strong>{{ config('app.name') }}</strong>.<br>{{ __('Không gian kết nối, chia sẻ và lan tỏa cảm hứng dành riêng cho cộng đồng EAUT.') }}
            </p>
            <button onclick="closeWelcomeModal()" class="btn-post" style="width: 100%; padding: 18px; border-radius: 22px; font-size: 17px; font-weight: 700; background: #000; color: #fff; box-shadow: 0 15px 30px rgba(0,0,0,0.15); transition: all 0.3s ease;">
                {{ __('Start Now') }}
            </button>
        </div>
    </div>
    <style>
        @keyframes welcomeEmoji {
            from {
                transform: translateY(0) rotate(-5deg);
            }

            to {
                transform: translateY(-8px) rotate(5deg);
            }
        }
    </style>
    <script>
        function closeWelcomeModal() {
            const modal = document.getElementById('welcomeModal');
            modal.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }, 400);
        }
        document.body.classList.add('modal-open');
    </script>
    @endif

    <style>
        .lightbox-open {
            overflow: hidden !important;
        }

        #imageLightbox img {
            animation: zoomIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>

    <script>
        function openLikesModal(postId) {
            const modal = document.getElementById('likesModal');
            const list = document.getElementById('likesList');
            if (!modal || !list) return;

            list.innerHTML = '<div style="text-align:center; padding: 30px;"><div class="loading-spinner"></div></div>';
            modal.style.display = 'flex';
            document.body.classList.add('modal-open');

            fetch(`/posts/${postId}/likes`)
                .then(res => res.json())
                .then(users => {
                    if (users.length === 0) {
                        list.innerHTML = '<div style="text-align:center; padding: 40px; opacity: 0.5;">Chưa có lượt thích nào.</div>';
                        return;
                    }
                    list.innerHTML = '';
                    users.forEach(u => {
                        const item = document.createElement('div');
                        item.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 0.5px solid var(--glass-border);';
                        item.innerHTML = `
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <a href="/@${u.username}"><div class="avatar" style="width: 40px; height: 40px; border-radius: 50%; background-image: url('${u.avatar_url}'); background-size: cover;"></div></a>
                                <div>
                                    <a href="/@${u.username}" style="text-decoration: none; color: var(--text-color); font-weight: 700; font-size: 14px;">${u.username}</a>
                                </div>
                            </div>
                            <a href="/@${u.username}" style="text-decoration: none; font-size: 12px; font-weight: 800; color: var(--accent-color);">Xem hồ sơ</a>
                        `;
                        list.appendChild(item);
                    });
                });
        }

        function closeLikesModal() {
            document.getElementById('likesModal').style.display = 'none';
            document.body.classList.remove('modal-open');
        }

        function toggleLike(postId) {
            const btns = document.querySelectorAll(`.like-btn[data-post-id="${postId}"]`);
            const token = '{{ csrf_token() }}';

            btns.forEach(btn => {
                const countSpan = btn.querySelector('.like-count');
                const svg = btn.querySelector('svg');
                const isLiked = btn.classList.contains('liked');
                let count = parseInt(countSpan.innerText) || 0;

                if (isLiked) {
                    btn.classList.remove('liked');
                    btn.style.color = 'inherit';
                    svg.setAttribute('fill', 'none');
                    countSpan.innerText = Math.max(0, count - 1);
                } else {
                    btn.classList.add('liked');
                    btn.style.color = '#ff3b30';
                    svg.setAttribute('fill', 'currentColor');
                    countSpan.innerText = count + 1;
                }
            });

            fetch('/posts/' + postId + '/like', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json()).then(data => {
                    btns.forEach(btn => {
                        if (btn.querySelector('.like-count')) btn.querySelector('.like-count').innerText = data.count;
                    });
                });
        }

        function toggleRepost(id) {
            const btns = document.querySelectorAll(`.repost-btn[data-post-id="${id}"]`);
            const token = '{{ csrf_token() }}';

            btns.forEach(btn => {
                const countSpan = btn.querySelector('.repost-count');
                const isReposted = btn.classList.contains('reposted');
                let count = parseInt(countSpan.innerText) || 0;
                if (isReposted) {
                    btn.classList.remove('reposted');
                    btn.style.color = 'inherit';
                    countSpan.innerText = Math.max(0, count - 1);
                } else {
                    btn.classList.add('reposted');
                    btn.style.color = '#00c300';
                    countSpan.innerText = count + 1;
                }
            });
            fetch(`/posts/${id}/repost`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json()).then(data => {
                    btns.forEach(btn => {
                        if (btn.querySelector('.repost-count')) btn.querySelector('.repost-count').innerText = data.count;
                    });
                });
        }

        function deletePost(id) {
            if (confirm('Xóa bài viết này?')) {
                fetch(`/posts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => location.reload());
            }
        }

        function deleteComment(id) {
            if (confirm('Xóa bình luận này?')) {
                fetch(`/comments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => location.reload());
            }
        }

        function sharePost(id) {
            navigator.clipboard.writeText(window.location.origin + '/posts/' + id);
            alert('Đã sao chép liên kết bài viết!');
        }

        function openLightbox(src) {
            const lightbox = document.getElementById('imageLightbox');
            const img = document.getElementById('lightboxImg');
            img.src = src;
            lightbox.style.display = 'flex';
            document.body.classList.add('lightbox-open');
        }

        function closeLightbox() {
            document.getElementById('imageLightbox').style.display = 'none';
            document.body.classList.remove('lightbox-open');
        }

        // Modal Logic
        function openModal() {
            document.getElementById('postModal').style.display = 'flex';
            document.body.classList.add('modal-open');
        }

        function closeModal() {
            document.getElementById('postModal').style.display = 'none';
            document.body.classList.remove('modal-open');
            // Clean up group-related elements
            const form = document.querySelector('#postModal form');
            if (form) {
                const groupInput = form.querySelector('input[name="group_id"]');
                if (groupInput) groupInput.value = '';
                const groupLabel = form.querySelector('#modalGroupLabel');
                if (groupLabel) groupLabel.remove();
                const placeholder = form.querySelector('textarea');
                if (placeholder) placeholder.placeholder = "Bạn đang nghĩ gì?";
            }
        }

        function openNotifModal() {
            document.getElementById('notifModal').style.display = 'flex';
            document.body.classList.add('modal-open');
            fetchNotifications();
            // Mark as read
            fetch('/api/notifications/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const badge = document.getElementById('notif-badge');
            if (badge) badge.remove();
        }

        function closeNotifModal() {
            document.getElementById('notifModal').style.display = 'none';
            document.body.classList.remove('modal-open');
        }

        function fetchNotifications() {
            fetch('/api/notifications')
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('notifList');
                    if (data.length === 0) {
                        list.innerHTML = '<div style="padding: 40px 20px; text-align: center; color: var(--secondary-text);"><svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px; opacity: 0.3;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><br>Chưa có thông báo nào.</div>';
                        return;
                    }
                    list.innerHTML = '';
                    data.forEach(n => {
                        let icon = '👤';
                        let color = '#8e44ad';
                        if (n.type === 'like') {
                            icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="currentColor"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
                            color = '#ff3b30';
                        } else if (n.type === 'reply') {
                            icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="currentColor"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>';
                            color = '#0095f6';
                        } else if (n.type === 'repost') {
                            icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>';
                            color = '#00c300';
                        }

                        const item = document.createElement('div');
                        item.className = 'notif-item';
                        let itemBg = 'transparent';
                        if (n.type === 'follow') itemBg = 'rgba(142, 68, 173, 0.05)';

                        const actor = n.aggregate_actors[0];
                        const count = n.aggregate_count;
                        let actorText = `<strong>${actor.username}</strong>`;
                        if (count > 1) {
                            actorText += `<span style="color: var(--secondary-text);"> và ${count - 1} người khác</span>`;
                        }

                        let targetUrl = '#';
                        if (n.type === 'follow') {
                            targetUrl = `/@${actor.username}`;
                        } else if (n.post_id) {
                            targetUrl = `/posts/${n.post_id}`;
                        }

                        item.style.cssText = `padding: 15px 0; display: flex; gap: 12px; border-bottom: 1px solid var(--glass-border); align-items: center; cursor: pointer; transition: background 0.2s; background: ${itemBg};`;
                        item.onmouseover = () => item.style.background = n.type === 'follow' ? 'rgba(142, 68, 173, 0.1)' : 'rgba(0,0,0,0.02)';
                        item.onmouseout = () => item.style.background = itemBg;
                        item.onclick = () => window.location.href = targetUrl;

                        let actionText = '';
                        if (n.type === 'like') actionText = 'đã thích bài viết của bạn.';
                        else if (n.type === 'reply') actionText = 'đã bình luận bài viết của bạn.';
                        else if (n.type === 'repost') actionText = 'đã đăng lại bài viết của bạn.';
                        else actionText = 'đã theo dõi bạn.';

                        let groupBadge = '';
                        if (n.post && n.post.group) {
                            groupBadge = `
                                <span style="display: inline-flex; align-items: center; gap: 4px; background: rgba(0,113,227,0.05); padding: 1px 6px; border-radius: 4px; margin-left: 4px; border: 1px solid rgba(0,113,227,0.1);">
                                    <img src="${n.post.group.avatar_url}" style="width: 12px; height: 12px; border-radius: 2px;">
                                    <span style="font-size: 10px; font-weight: 700; color: var(--accent-color);">${n.post.group.name}</span>
                                </span>
                            `;
                        }

                        item.innerHTML = `
                            <div style="position: relative; flex-shrink: 0;">
                                <div class="avatar" style="width: 40px; height: 40px; background-image: url('${actor.avatar_url}'); background-size: cover; border-radius: 50%;"></div>
                                <div style="position: absolute; bottom: -2px; right: -2px; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: ${color}; color: white; border: 2px solid var(--glass-bg);">${icon}</div>
                            </div>
                            <div style="flex-grow: 1; min-width: 0;">
                                <div style="font-size: 14px; line-height: 1.4;">
                                    ${actorText} <span style="color: var(--secondary-text);">${actionText}</span> ${groupBadge}
                                </div>
                                <div style="color: var(--secondary-text); font-size: 12px; margin-top: 2px;">${n.created_at_human}</div>
                            </div>
                        `;
                        list.appendChild(item);
                    });
                });
        }

        function updateUnreadCounts() {
            fetch('{{ route('api.unread_counts') }}')
                .then(res => res.json())
                .then(data => {
                    // Update Message Badge
                    const msgLink = document.querySelector('a[title="Tin nhắn"]');
                    if (msgLink) {
                        let msgBadge = msgLink.querySelector('.nav-badge');
                        if (data.unreadMessagesCount > 0) {
                            if (msgBadge) {
                                msgBadge.textContent = data.unreadMessagesCount > 99 ? '99+' : data.unreadMessagesCount;
                            } else {
                                msgBadge = document.createElement('div');
                                msgBadge.className = 'nav-badge';
                                msgBadge.textContent = data.unreadMessagesCount > 99 ? '99+' : data.unreadMessagesCount;
                                msgLink.appendChild(msgBadge);
                            }
                        } else if (msgBadge) {
                            msgBadge.remove();
                        }
                    }

                    // Update Notification Badge
                    const notifLink = document.querySelector('a[onclick="openNotifModal()"]');
                    if (notifLink) {
                        let notifBadge = notifLink.querySelector('.nav-badge');
                        if (data.unreadNotificationsCount > 0) {
                            if (notifBadge) {
                                notifBadge.id = 'notif-badge'; // Ensure ID is preserved
                                notifBadge.textContent = data.unreadNotificationsCount > 99 ? '99+' : data.unreadNotificationsCount;
                            } else {
                                notifBadge = document.createElement('div');
                                notifBadge.id = 'notif-badge';
                                notifBadge.className = 'nav-badge';
                                notifBadge.textContent = data.unreadNotificationsCount > 99 ? '99+' : data.unreadNotificationsCount;
                                notifLink.appendChild(notifBadge);
                            }

                            // Real-time update list if modal is open
                            const modal = document.getElementById('notifModal');
                            if (modal && modal.style.display === 'flex') {
                                fetchNotifications();
                            }
                        } else if (notifBadge) {
                            notifBadge.remove();
                        }
                    }
                });
        }

        // Poll every 10 seconds
        @auth
        setInterval(updateUnreadCounts, 10000);
        @endauth

        window.onclick = function(event) {
            if (event.target == document.getElementById('postModal')) closeModal();
            if (event.target == document.getElementById('notifModal')) closeNotifModal();
            if (event.target == document.getElementById('commentModal')) {
                if (typeof closeCommentModal === 'function') closeCommentModal();
                else document.getElementById('commentModal').style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }

        // Theme Logic
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const targetTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);
            updateThemeIcon(targetTheme);
        }

        function updateThemeIcon(theme) {
            const sunIcon = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
            const moonIcon = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';

            const iconEl = document.getElementById('theme-icon');
            if (iconEl) iconEl.innerHTML = theme === 'dark' ? sunIcon : moonIcon;

            const settingsIconEl = document.getElementById('settings-theme-icon');
            if (settingsIconEl) settingsIconEl.innerHTML = theme === 'dark' ? sunIcon : moonIcon;

            // Update settings thumb position if exists
            const thumb = document.getElementById('theme-switch-thumb');
            if (thumb) thumb.style.left = theme === 'dark' ? '22px' : '2px';
        }

        function toggleLinkInput() {
            const container = document.getElementById('linkInputContainer');
            const input = document.getElementById('linkInput');
            if (container.style.display === 'none') {
                container.style.display = 'block';
                input.focus();
            } else {
                container.style.display = 'none';
                input.value = '';
            }
        }

        // Media Preview Logic (Multiple files)
        function previewMedia(event) {
            const files = event.target.files;
            const grid = document.getElementById('mediaPreviewGrid');
            const container = document.getElementById('mediaPreviewContainer');

            // Không xóa grid cũ để cho phép chọn từ cả 2 nút
            // grid.innerHTML = ''; 
            if (files.length > 0) {
                container.style.display = 'block';
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                const itemDiv = document.createElement('div');
                itemDiv.style.cssText = 'position: relative; flex: 0 0 130px; border-radius: 12px; overflow: hidden; border: 1px solid var(--glass-border); aspect-ratio: 1; background: rgba(0,0,0,0.05); scroll-snap-align: start; display: flex; align-items: center; justify-content: center;';

                if (file.type.startsWith('image/')) {
                    reader.onload = function(e) {
                        itemDiv.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; display: block; object-fit: cover;">`;
                        addRemoveButton(itemDiv);
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Generic file
                    itemDiv.innerHTML = `
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; text-align: center; width: 100%;">
                            <svg viewBox="0 0 24 24" width="32" height="32" stroke="var(--accent-color)" stroke-width="2" fill="none" style="margin-bottom: 5px;"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                            <div style="font-size: 10px; font-weight: 700; color: var(--text-color); word-break: break-all; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${file.name}</div>
                        </div>
                    `;
                    addRemoveButton(itemDiv);
                }
                grid.appendChild(itemDiv);
            }
        }

        function addRemoveButton(container) {
            const removeBtn = document.createElement('span');
            removeBtn.innerHTML = '&times;';
            removeBtn.style.cssText = 'position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.5); color: #fff; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 16px; font-weight: bold; backdrop-filter: blur(4px); z-index: 10;';
            removeBtn.onclick = function() {
                container.remove();
                if (document.getElementById('mediaPreviewGrid').children.length === 0) {
                    document.getElementById('mediaPreviewContainer').style.display = 'none';
                }
            };
            container.appendChild(removeBtn);
        }

        function removeAllMedia() {
            document.getElementById('mediaPreviewGrid').innerHTML = '';
            document.getElementById('mediaPreviewContainer').style.display = 'none';
            document.getElementById('mediaInput').value = '';
            const fileInput = document.getElementById('fileInput');
            if (fileInput) fileInput.value = '';
        }

        // Khởi tạo theme từ localStorage
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);

        // Show modal if there are validation errors
        @if($errors->has('content') || $errors->has('media.*') || $errors->has('link_url'))
            window.onload = function() {
                openModal();
            };
        @endif

        // Dropdown Toggle Logic
        function toggleDropdown(id) {
            document.getElementById("dropdown-" + id).classList.toggle("show");
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        });
    </script>
    <!-- Likes Modal -->
    <div id="likesModal" class="modal" onclick="if(event.target === this) closeLikesModal()">
        <div class="modal-content glass-bubble" style="max-width: 400px; padding: 0; overflow: hidden; border-radius: 28px;">
            <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 800;">Lượt thích</h3>
                <span onclick="closeLikesModal()" style="cursor: pointer; opacity: 0.5; font-size: 24px;">&times;</span>
            </div>
            <div id="likesList" style="max-height: 400px; overflow-y: auto; padding: 0 20px;"></div>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div id="editPostModal" class="modal" style="display: none; background: rgba(0,0,0,0.3); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); align-items: center; justify-content: center; z-index: 6000;">
        <div class="modal-content glass-bubble" style="max-width: 550px; padding: 0; border-radius: 28px; width: 90%; overflow: hidden;">
            <div style="padding: 20px 25px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 800;">Chỉnh sửa bài viết</h3>
                <span onclick="closeEditPostModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
            </div>
            <div style="padding: 25px;">
                <textarea id="editPostContent" style="width: 100%; min-height: 150px; border: none; background: transparent; font-size: 16px; color: var(--text-color); resize: none; outline: none;" placeholder="Bạn đang nghĩ gì?"></textarea>
                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button onclick="closeEditPostModal()" style="padding: 10px 20px; border-radius: 12px; border: 1px solid var(--glass-border); background: transparent; font-weight: 600; cursor: pointer;">Hủy</button>
                    <button onclick="submitEditPost()" class="btn-post" style="padding: 10px 25px; border-radius: 12px; font-weight: 700;">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentEditingPostId = null;

        function openEditPostModal(id, content) {
            currentEditingPostId = id;
            document.getElementById('editPostContent').value = content;
            document.getElementById('editPostModal').style.display = 'flex';
            document.body.classList.add('modal-open');
            // Đóng tất cả dropdown đang mở
            document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        }

        function closeEditPostModal() {
            document.getElementById('editPostModal').style.display = 'none';
            document.body.classList.remove('modal-open');
            currentEditingPostId = null;
        }

        function submitEditPost() {
            const content = document.getElementById('editPostContent').value.trim();
            if (!content) return;

            fetch(`/posts/${currentEditingPostId}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: content
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Cập nhật nội dung trên giao diện cho tất cả các bản sao của bài viết này
                        const postWrappers = document.querySelectorAll(`[id$="-${currentEditingPostId}"]`);
                        postWrappers.forEach(wrapper => {
                            // Tìm thẻ div chứa text bài viết
                            const textEl = wrapper.querySelector('.post-text, [style*="font-size: 15px"]');
                            if (textEl) textEl.innerText = data.content;
                        });
                        closeEditPostModal();
                    }
                });
        }

        function sharePost(id) {
            const url = window.location.origin + '/posts/' + id;
            navigator.clipboard.writeText(url).then(() => {
                alert('Đã sao chép liên kết bài viết vào bộ nhớ tạm!');
            }).catch(err => {
                console.error('Không thể sao chép: ', err);
            });
        }
    </script>
</body>

</html>