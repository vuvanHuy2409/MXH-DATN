<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} - Threads Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #D1E9F6;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-color: #1d1d1f;
            --secondary-text: #6e6e73;
            --accent-color: #0071e3;
        }

        [data-theme="dark"] {
            --bg-main: #0a0a0a;
            --glass-bg: rgba(28, 28, 30, 0.8);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-color: #f5f5f7;
            --secondary-text: #98989d;
            --accent-color: #0a84ff;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--bg-main);
            background-image: radial-gradient(at 0% 0%, hsla(200, 100%, 90%, 1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(190, 100%, 85%, 1) 0, transparent 50%);
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }

        [data-theme="dark"] body {
            background-image: radial-gradient(at 0% 0%, hsla(240, 10%, 15%, 1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(240, 10%, 10%, 1) 0, transparent 50%);
        }

        .theme-toggle {
            position: fixed;
            top: 25px;
            right: 25px;
            width: 45px;
            height: 45px;
            border-radius: 15px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-color);
            backdrop-filter: blur(10px);
            z-index: 1000;
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: var(--glass-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-radius: 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 25px;
            overflow: hidden;
            border: 2px solid var(--glass-border);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            transition: transform 0.3s ease;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
            color: var(--text-color);
        }

        p.subtitle {
            text-align: center;
            color: var(--secondary-text);
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            margin-left: 5px;
            color: var(--secondary-text);
        }

        .email-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .email-input-wrapper input, input[type="password"] {
            width: 100%;
            padding: 14px 18px;
            border-radius: 16px;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.2);
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
            color: var(--text-color);
        }

        [data-theme="dark"] .email-input-wrapper input, 
        [data-theme="dark"] input[type="password"] {
            background: rgba(0, 0, 0, 0.2);
        }

        .email-input-wrapper input:focus, input[type="password"]:focus {
            background: var(--glass-bg);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            border-radius: 18px;
            border: none;
            background: var(--text-color);
            color: var(--bg-main);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .footer-links {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: var(--secondary-text);
        }

        .footer-links a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 700;
        }

        .error-msg {
            background: rgba(255, 59, 48, 0.1);
            color: #ff3b30;
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 59, 48, 0.2);
        }
    </style>
</head>

<body>
    <div class="theme-toggle" id="themeToggle" onclick="toggleTheme()">
        <!-- Icon will be injected by JS -->
    </div>

    <div class="login-container">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <h1>{{ __('Welcome back') }}</h1>
        <p class="subtitle">{{ __('Login to your EAUT account') }}</p>

        @if($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>{{ __('Student ID or Full Email') }}</label>
                <div class="email-input-wrapper">
                    <input type="text" name="email_prefix" value="{{ old('email_prefix') }}"
                        placeholder="{{ __('Enter ID or email address') }}" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label style="display: flex; justify-content: space-between;">
                    {{ __('Password') }}
                    <a href="{{ route('password.request') }}" style="color: var(--accent-color); text-decoration: none; font-weight: 600; font-size: 12px;">{{ __('Forgot Password?') }}</a>
                </label>
                <input type="password" name="password" placeholder="{{ __('Enter password') }}" required>
            </div>
            <button type="submit" class="btn-login">{{ __('Login') }}</button>
        </form>

        <div class="footer-links">
            {{ __("Don't have an account?") }} <a href="{{ route('register') }}">{{ __('Join now') }}</a>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const targetTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);
            updateThemeIcon(targetTheme);
        }

        function updateThemeIcon(theme) {
            const toggle = document.getElementById('themeToggle');
            const sunIcon = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
            const moonIcon = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
            toggle.innerHTML = theme === 'dark' ? sunIcon : moonIcon;
        }

        // Initialize theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
    </script>
</body>

</html>
