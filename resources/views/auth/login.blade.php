<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập · EAUT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Tokens ── */
        :root {
            --c-bg:        #F8FAFC;
            --c-card:      rgba(255,255,255,0.88);
            --c-border:    #E2E8F0;
            --c-text:      #0F172A;
            --c-muted:     #64748B;
            --c-accent:    #0062FF;
            --c-accent2:   #0052D9;
            --c-accent3:   #38BDF8;
            --c-error:     #ef4444;
            --c-input-bg:  rgba(255,255,255,0.7);
            --c-input-bdr: rgba(0,98,255,0.18);
            --shadow-card: 0 32px 80px -12px rgba(0,98,255,0.12), 0 0 0 1px #E2E8F0;
            --orb1: #93C5FD;
            --orb2: #BAE6FD;
            --orb3: #FDE68A;
        }
        [data-theme="dark"] {
            --c-bg:        #0B0F1A;
            --c-card:      rgba(10,15,30,0.82);
            --c-border:    rgba(255,255,255,0.07);
            --c-text:      #E8EEFF;
            --c-muted:     #7B8DB0;
            --c-accent:    #4D94FF;
            --c-accent2:   #2563EB;
            --c-accent3:   #38BDF8;
            --c-error:     #f87171;
            --c-input-bg:  rgba(255,255,255,0.04);
            --c-input-bdr: rgba(77,148,255,0.2);
            --shadow-card: 0 32px 80px -12px rgba(0,0,0,0.65), 0 0 0 1px rgba(255,255,255,0.06);
            --orb1: #1D4ED8;
            --orb2: #0369A1;
            --orb3: #92400e;
        }

        /* ── Base ── */
        html, body {
            height: 100%;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--c-bg);
            color: var(--c-text);
            overflow: hidden;
        }

        /* ── Aurora background ── */
        .aurora {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }
        .aurora::before, .aurora::after, .aurora .orb3 {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.55;
            animation: drift 14s ease-in-out infinite alternate;
        }
        [data-theme="dark"] .aurora::before,
        [data-theme="dark"] .aurora::after,
        [data-theme="dark"] .aurora .orb3 { opacity: 0.35; }

        .aurora::before {
            width: 700px; height: 700px;
            background: radial-gradient(circle, var(--orb1), transparent 70%);
            top: -200px; left: -150px;
            animation-duration: 16s;
        }
        .aurora::after {
            width: 600px; height: 600px;
            background: radial-gradient(circle, var(--orb2), transparent 70%);
            bottom: -180px; right: -120px;
            animation-duration: 12s;
            animation-delay: -4s;
        }
        .aurora .orb3 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, var(--orb3), transparent 70%);
            top: 50%; left: 55%;
            transform: translate(-50%, -50%);
            animation-duration: 18s;
            animation-delay: -8s;
        }
        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(40px, -30px) scale(1.05); }
            66%  { transform: translate(-20px, 40px) scale(0.97); }
            100% { transform: translate(30px, 20px) scale(1.03); }
        }

        /* ── Grid dots ── */
        .grid-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                radial-gradient(circle, rgba(99,102,241,0.12) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
        }
        [data-theme="dark"] .grid-bg {
            background-image:
                radial-gradient(circle, rgba(129,140,248,0.1) 1px, transparent 1px);
        }

        /* ── Layout ── */
        .page-wrap {
            position: relative;
            z-index: 1;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ── Card ── */
        .card {
            width: 100%;
            max-width: 420px;
            background: var(--c-card);
            backdrop-filter: blur(40px) saturate(1.8);
            -webkit-backdrop-filter: blur(40px) saturate(1.8);
            border: 1px solid var(--c-border);
            border-radius: 28px;
            padding: 44px 40px 36px;
            box-shadow: var(--shadow-card);
            animation: cardIn 0.9s cubic-bezier(0.22, 1, 0.36, 1) both;
            position: relative;
            overflow: hidden;
        }
        /* Shimmer top border */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg,
                transparent 0%,
                var(--c-accent) 30%,
                var(--c-accent2) 60%,
                var(--c-accent3) 80%,
                transparent 100%);
            opacity: 0.7;
            animation: shimmerBorder 4s linear infinite;
            background-size: 200% 100%;
        }
        @keyframes shimmerBorder {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Logo ── */
        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
            animation: cardIn 0.9s 0.05s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .logo-ring {
            position: relative;
            width: 72px; height: 72px;
        }
        .logo-ring::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, var(--c-accent), var(--c-accent2), var(--c-accent3), var(--c-accent));
            animation: rotateBorder 4s linear infinite;
            z-index: 0;
        }
        @keyframes rotateBorder {
            to { transform: rotate(360deg); }
        }
        .logo-inner {
            position: relative;
            z-index: 1;
            width: 72px; height: 72px;
            border-radius: 50%;
            overflow: hidden;
            background: #fff;
            border: 3px solid var(--c-bg);
        }
        [data-theme="dark"] .logo-inner { background: #1e1b4b; border-color: var(--c-bg); }
        .logo-inner img { width: 100%; height: 100%; object-fit: cover; }

        /* ── Heading ── */
        .heading {
            text-align: center;
            animation: cardIn 0.9s 0.1s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .heading h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.6px;
            color: var(--c-text);
            line-height: 1.2;
        }
        .heading h1 span {
            background: linear-gradient(135deg, var(--c-accent), var(--c-accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .heading p {
            margin-top: 6px;
            font-size: 14px;
            color: var(--c-muted);
            font-weight: 400;
        }

        /* ── Error ── */
        .error-box {
            margin: 20px 0 4px;
            padding: 12px 16px;
            border-radius: 14px;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--c-error);
            font-weight: 500;
            animation: shake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }
        [data-theme="dark"] .error-box {
            background: rgba(239,68,68,0.12);
            border-color: rgba(239,68,68,0.25);
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }

        /* ── Form ── */
        form {
            margin-top: 24px;
            animation: cardIn 0.9s 0.15s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        /* Floating label field */
        .field {
            position: relative;
            margin-bottom: 18px;
        }
        .field input {
            width: 100%;
            padding: 20px 16px 8px;
            border-radius: 14px;
            border: 1.5px solid var(--c-input-bdr);
            background: var(--c-input-bg);
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: var(--c-text);
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            -webkit-appearance: none;
        }
        .field input::placeholder { color: transparent; }
        .field label {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: var(--c-muted);
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }
        .field input:focus + label,
        .field input:not(:placeholder-shown) + label {
            top: 10px;
            transform: translateY(0);
            font-size: 11px;
            font-weight: 600;
            color: var(--c-accent);
            letter-spacing: 0.3px;
        }
        .field input:focus {
            border-color: var(--c-accent);
            background: var(--c-input-bg);
            box-shadow: 0 0 0 4px rgba(79,70,229,0.1);
        }
        [data-theme="dark"] .field input:focus {
            box-shadow: 0 0 0 4px rgba(129,140,248,0.12);
        }

        /* Password toggle */
        .field .pw-toggle {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--c-muted);
            background: none; border: none; padding: 4px;
            display: flex; align-items: center;
            transition: color 0.2s;
        }
        .field .pw-toggle:hover { color: var(--c-accent); }

        /* Forgot link row */
        .forgot-row {
            display: flex;
            justify-content: flex-end;
            margin-top: -8px;
            margin-bottom: 22px;
        }
        .forgot-row a {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--c-accent);
            text-decoration: none;
            opacity: 0.85;
            transition: opacity 0.2s;
        }
        .forgot-row a:hover { opacity: 1; }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%;
            padding: 15px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(135deg, var(--c-accent) 0%, var(--c-accent2) 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(0,98,255,0.3);
        }
        .btn-submit::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: skewX(-20deg);
            transition: none;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,98,255,0.4); }
        .btn-submit:hover::after {
            transition: left 0.5s ease;
            left: 160%;
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        .btn-submit .btn-text { transition: opacity 0.2s; }
        .btn-submit .btn-spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }
        .btn-submit.loading .btn-text { opacity: 0; }
        .btn-submit.loading .btn-spinner { display: block; }
        @keyframes spin { to { transform: translate(-50%,-50%) rotate(360deg); } }

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--c-input-bdr);
        }
        .divider span { font-size: 12px; color: var(--c-muted); white-space: nowrap; }

        /* ── Footer ── */
        .footer-links {
            animation: cardIn 0.9s 0.2s cubic-bezier(0.22, 1, 0.36, 1) both;
            text-align: center;
            font-size: 13.5px;
            color: var(--c-muted);
        }
        .footer-links a {
            color: var(--c-accent);
            font-weight: 700;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .footer-links a:hover { opacity: 0.8; }

        /* ── Theme toggle ── */
        .theme-toggle {
            position: fixed;
            top: 22px; right: 22px;
            z-index: 100;
            width: 42px; height: 42px;
            border-radius: 12px;
            background: var(--c-card);
            border: 1px solid var(--c-border);
            backdrop-filter: blur(20px);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--c-text);
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .theme-toggle:hover { transform: scale(1.08); box-shadow: 0 6px 18px rgba(0,0,0,0.13); }

        /* ── DB Offline overlay ── */
        .offline-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: var(--c-bg);
        }
        .offline-card {
            width: 100%;
            max-width: 400px;
            background: var(--c-card);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid var(--c-border);
            border-radius: 28px;
            padding: 44px 40px;
            text-align: center;
            box-shadow: var(--shadow-card);
            animation: cardIn 0.9s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        /* Pulse ring animation */
        .offline-pulse {
            position: relative;
            width: 80px; height: 80px;
            margin: 0 auto 28px;
        }
        .offline-pulse::before, .offline-pulse::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid rgba(245,158,11,0.4);
            animation: pulseRing 2s ease-out infinite;
        }
        .offline-pulse::after { animation-delay: 0.7s; }
        @keyframes pulseRing {
            0%   { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.8); opacity: 0; }
        }
        .offline-icon-inner {
            position: absolute;
            inset: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(251,191,36,0.1));
            border: 1.5px solid rgba(245,158,11,0.3);
            display: flex; align-items: center; justify-content: center;
        }
        .offline-icon-inner svg { color: #f59e0b; }
        [data-theme="dark"] .offline-icon-inner svg { color: #fbbf24; }

        .offline-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--c-text);
            margin-bottom: 10px;
        }
        .offline-desc {
            font-size: 14px;
            color: var(--c-muted);
            line-height: 1.65;
            margin-bottom: 32px;
        }
        /* Arc progress */
        .offline-progress {
            position: relative;
            width: 64px; height: 64px;
            margin: 0 auto 14px;
        }
        .offline-progress svg {
            transform: rotate(-90deg);
        }
        .offline-progress .track {
            fill: none;
            stroke: var(--c-input-bdr);
            stroke-width: 4;
        }
        .offline-progress .fill {
            fill: none;
            stroke: var(--c-accent);
            stroke-width: 4;
            stroke-linecap: round;
            stroke-dasharray: 163; /* 2π×26 */
            stroke-dashoffset: 163;
            transition: stroke-dashoffset 1s linear;
        }
        .offline-count-text {
            position: absolute;
            inset: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            font-weight: 800;
            color: var(--c-accent);
        }
        .offline-label {
            font-size: 13px;
            color: var(--c-muted);
            margin-bottom: 20px;
        }
        .btn-retry {
            padding: 12px 30px;
            border-radius: 12px;
            border: 1.5px solid var(--c-accent);
            background: transparent;
            color: var(--c-accent);
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-retry:hover {
            background: var(--c-accent);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(0,98,255,0.3);
        }
    </style>
</head>
<body>
    <!-- Aurora & grid -->
    <div class="aurora"><div class="orb3"></div></div>
    <div class="grid-bg"></div>

    <!-- Theme toggle -->
    <div class="theme-toggle" id="themeToggle" onclick="toggleTheme()"></div>

    {{-- DB Offline overlay --}}
    @php $dbOffline = $dbOffline ?? ($errors->first() === 'db_offline'); @endphp
    @if($dbOffline)
    <div class="offline-overlay" id="offlineOverlay">
        <!-- Aurora inside overlay too -->
        <div class="aurora" style="position:absolute;z-index:0;"><div class="orb3"></div></div>
        <div class="grid-bg" style="position:absolute;z-index:0;"></div>
        <div class="offline-card" style="position:relative;z-index:1;">
            <div class="offline-pulse">
                <div class="offline-icon-inner">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>
                </div>
            </div>
            <div class="offline-title">Đang khởi động…</div>
            <div class="offline-desc">
                Máy chủ chưa sẵn sàng.<br>
                Tự động thử lại sau vài giây.
            </div>

            <div class="offline-progress">
                <svg width="64" height="64" viewBox="0 0 64 64">
                    <circle class="track" cx="32" cy="32" r="26"/>
                    <circle class="fill" id="arcFill" cx="32" cy="32" r="26"/>
                </svg>
                <div class="offline-count-text" id="cdNum">10</div>
            </div>
            <div class="offline-label">giây</div>
            <button class="btn-retry" onclick="window.location.reload()">Thử ngay</button>
        </div>
    </div>
    @endif

    <!-- Main page -->
    <div class="page-wrap">
        <div class="card">
            <!-- Logo -->
            <div class="logo-wrap">
                <div class="logo-ring">
                    <div class="logo-inner">
                        <img src="{{ asset('images/logo.png') }}" alt="EAUT">
                    </div>
                </div>
            </div>

            <!-- Heading -->
            <div class="heading">
                <h1>Chào <span>mừng trở lại</span></h1>
                <p>Đăng nhập vào tài khoản EAUT của bạn</p>
            </div>

            <!-- Error -->
            @if($errors->any() && $errors->first() !== 'db_offline')
            <div class="error-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf

                <div class="field">
                    <input type="text" id="email_prefix" name="email_prefix"
                           value="{{ old('email_prefix') }}"
                           placeholder="Mã SV hoặc email" required autocomplete="username">
                    <label for="email_prefix">Mã SV hoặc email đầy đủ</label>
                </div>

                <div class="field">
                    <input type="password" id="password" name="password"
                           placeholder="Mật khẩu" required autocomplete="current-password">
                    <label for="password">Mật khẩu</label>
                    <button type="button" class="pw-toggle" id="pwToggle" tabindex="-1"
                            onclick="togglePw()">
                        <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <div class="forgot-row">
                    <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="btn-text">Đăng nhập</span>
                    <span class="btn-spinner"></span>
                </button>
            </form>

            <div class="divider"><span>chưa có tài khoản?</span></div>

            <div class="footer-links">
                <a href="{{ route('register') }}">Tạo tài khoản ngay →</a>
            </div>
        </div>
    </div>

    <script>
    /* ── Theme ── */
    const SUN = `<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>`;
    const MOON = `<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>`;

    function applyTheme(t) {
        document.documentElement.setAttribute('data-theme', t);
        document.getElementById('themeToggle').innerHTML = t === 'dark' ? SUN : MOON;
    }
    function toggleTheme() {
        const t = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', t);
        applyTheme(t);
    }
    applyTheme(localStorage.getItem('theme') || 'light');

    /* ── Password toggle ── */
    const EYE_OPEN = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
    const EYE_OFF  = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
    let pwVisible = false;
    function togglePw() {
        pwVisible = !pwVisible;
        const inp = document.getElementById('password');
        inp.type = pwVisible ? 'text' : 'password';
        document.getElementById('pwToggle').innerHTML = pwVisible ? EYE_OFF : EYE_OPEN;
    }

    /* ── Submit loading state ── */
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.classList.add('loading');
    });

    /* ── DB Offline countdown ── */
    (function () {
        const overlay = document.getElementById('offlineOverlay');
        if (!overlay) return;

        const TOTAL = 10;
        const CIRCUMFERENCE = 163; // 2π×26
        let s = TOTAL;
        const numEl  = document.getElementById('cdNum');
        const arcEl  = document.getElementById('arcFill');

        function tick() {
            // update number
            numEl.textContent = s;
            // update arc: full at TOTAL, empty at 0
            const offset = CIRCUMFERENCE * (1 - s / TOTAL);
            arcEl.style.strokeDashoffset = CIRCUMFERENCE - offset;

            if (s <= 0) { window.location.reload(); return; }
            s--;
            setTimeout(tick, 1000);
        }
        // init arc to full
        arcEl.style.strokeDashoffset = CIRCUMFERENCE;
        setTimeout(tick, 50);
    })();
    </script>
</body>
</html>
