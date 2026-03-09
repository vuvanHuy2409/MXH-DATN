<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Threads Clone</title>
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
            margin: 0; padding: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--bg-main);
            background-attachment: fixed;
            height: 100vh;
            display: flex; justify-content: center; align-items: center;
        }

        .container {
            width: 100%; max-width: 400px;
            padding: 40px;
            background: var(--glass-bg);
            backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-radius: 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }

        h1 { font-size: 24px; font-weight: 800; text-align: center; margin-bottom: 10px; }
        p.subtitle { text-align: center; color: var(--secondary-text); font-size: 14px; margin-bottom: 30px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--secondary-text); }

        .email-input-wrapper { position: relative; display: flex; align-items: center; }
        .email-input-wrapper input { padding-right: 130px; width: 100%; padding: 14px 18px; border-radius: 16px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.5); font-size: 15px; outline: none; }
        .email-suffix { position: absolute; right: 18px; color: var(--secondary-text); font-weight: 600; font-size: 15px; }

        .btn-submit { width: 100%; padding: 15px; border-radius: 18px; border: none; background: #000; color: #fff; font-size: 16px; font-weight: 700; cursor: pointer; }
        .footer-links { margin-top: 25px; text-align: center; font-size: 14px; color: var(--secondary-text); }
        .footer-links a { color: var(--accent-color); text-decoration: none; font-weight: 700; }

        .error-msg { background: rgba(255, 59, 48, 0.1); color: #ff3b30; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; border: 1px solid rgba(255, 59, 48, 0.2); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quên mật khẩu?</h1>
        <p class="subtitle">Nhập email của bạn để nhận mã OTP khôi phục mật khẩu</p>

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Tài khoản Email EAUT</label>
                <div class="email-input-wrapper">
                    <input type="text" name="email_prefix" value="{{ old('email_prefix') }}" 
                           placeholder="8 số MSV hoặc tên viết tắt" required autofocus>
                    <span class="email-suffix">@eaut.edu.vn</span>
                </div>
            </div>
            <button type="submit" class="btn-submit">Gửi mã OTP</button>
        </form>

        <div class="footer-links">
            Quay lại <a href="{{ route('login') }}">Đăng nhập</a>
        </div>
    </div>
</body>
</html>
