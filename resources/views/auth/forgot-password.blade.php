<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu · EAUT</title>
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
            --c-success:   #10b981;
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
            --c-success:   #34d399;
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
            line-height: 1.5;
        }

        /* ── Messages ── */
        .error-box, .success-box {
            margin: 20px 0 4px;
            padding: 12px 16px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
        }
        .error-box {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: var(--c-error);
            animation: shake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }
        .success-box {
            background: rgba(16,185,129,0.08);
            border: 1px solid rgba(16,185,129,0.2);
            color: var(--c-success);
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
            margin-top: 10px;
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
        .btn-submit:hover::after { transition: left 0.5s ease; left: 160%; }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit.loading { pointer-events: none; opacity: 0.8; }
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
    </style>
</head>
<body>
    <div class="aurora"><div class="orb3"></div></div>
    <div class="grid-bg"></div>
    <div class="theme-toggle" id="themeToggle" onclick="toggleTheme()"></div>

    <div class="page-wrap">
        <div class="card">
            <div class="logo-wrap">
                <div class="logo-ring">
                    <div class="logo-inner">
                        <img src="{{ asset('images/logo.png') }}" alt="EAUT">
                    </div>
                </div>
            </div>

            <div class="heading">
                <h1>Quên <span>mật khẩu?</span></h1>
                <p>Nhập email tài khoản của bạn để nhận mã OTP khôi phục (Hiệu lực 2 phút)</p>
            </div>

            @if(session('status'))
                <div class="success-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" id="forgotForm">
                @csrf
                <div class="field">
                    <input type="text" id="email_prefix" name="email_prefix" value="{{ old('email_prefix') }}" placeholder="Email, Username hoặc Mã SV" required autofocus>
                    <label for="email_prefix">Mã SV hoặc email đầy đủ</label>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="btn-text">Gửi mã OTP</span>
                    <span class="btn-spinner"></span>
                </button>
            </form>

            <div class="divider"><span>quay lại?</span></div>

            <div class="footer-links">
                <a href="{{ route('login') }}">← Quay lại đăng nhập</a>
            </div>
        </div>
    </div>

    <script>
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

    document.getElementById('forgotForm').addEventListener('submit', function() {
        document.getElementById('submitBtn').classList.add('loading');
    });
    </script>
</body>
</html>
