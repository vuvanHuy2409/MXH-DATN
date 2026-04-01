<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { border-bottom: 2px solid #0062FF; padding-bottom: 10px; margin-bottom: 20px; }
        .user-info { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .content { white-space: pre-wrap; background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Phản hồi người dùng mới</h2>
        </div>
        <div class="user-info">
            <strong>Người gửi:</strong> {{ $user->username }}<br>
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>Thời gian:</strong> {{ now()->format('H:i d/m/Y') }}
        </div>
        <div class="content">
            {{ $content }}
        </div>
    </div>
</body>
</html>
