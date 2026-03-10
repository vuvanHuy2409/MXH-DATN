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
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
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

        .logo:hover {
            transform: scale(1.05) rotate(3deg);
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

        .email-input-wrapper input {
            padding-right: 130px;
            width: 100%;
            padding: 14px 18px;
            border-radius: 16px;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.5);
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
        }

        .email-input-wrapper input:focus {
            background: #fff;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
        }

        .email-suffix {
            position: absolute;
            right: 18px;
            color: var(--secondary-text);
            font-weight: 600;
            font-size: 15px;
            pointer-events: none;
        }

        input[type="password"] {
            width: 100%;
            padding: 14px 18px;
            border-radius: 16px;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.5);
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
        }

        input[type="password"]:focus {
            background: #fff;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            border-radius: 18px;
            border: none;
            background: #000;
            color: #fff;
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
                <label>{{ __('Email Prefix') }}</label>
                <div class="email-input-wrapper">
                    <input type="text" name="email_prefix" value="{{ old('email_prefix') }}"
                        placeholder="{{ __('8-digit student ID or username') }}" required autofocus>
                    <span class="email-suffix"></span>
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
</body>

</html>
