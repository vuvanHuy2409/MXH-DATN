<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực đăng ký - Threads Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #D1E9F6;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --accent-color: #0071e3;
            --secondary-text: #6e6e73;
        }

        body {
            margin: 0; padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
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

        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--secondary-text); }
        input { width: 100%; padding: 14px 18px; border-radius: 16px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.5); font-size: 15px; box-sizing: border-box; outline: none; text-align: center; font-size: 24px; letter-spacing: 10px; }
        input:focus { background: #fff; border-color: var(--accent-color); }

        .btn-submit { width: 100%; padding: 15px; border-radius: 18px; border: none; background: #000; color: #fff; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 10px; }
        .error-msg { background: rgba(255, 59, 48, 0.1); color: #ff3b30; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; border: 1px solid rgba(255, 59, 48, 0.2); }
        .success-msg { background: rgba(52, 199, 89, 0.1); color: #28a745; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; border: 1px solid rgba(52, 199, 89, 0.2); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Xác nhận đăng ký</h1>
        <p class="subtitle">Nhập mã OTP 6 số đã được gửi tới {{ $email }} để hoàn tất đăng ký</p>

        @if(session('status'))
            <div class="success-msg">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('register.verify') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="form-group">
                <label>Mã xác thực OTP</label>
                <input type="text" name="otp" placeholder="XXXXXX" required autofocus maxlength="6">
            </div>

            <button type="submit" class="btn-submit">Xác nhận tài khoản</button>
        </form>
    </div>
</body>
</html>
