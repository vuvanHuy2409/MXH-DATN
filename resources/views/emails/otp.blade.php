<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'SF Pro Text', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f5f5f7; margin: 0; padding: 0; }
        .wrapper { padding: 40px 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .header { background: #000000; padding: 40px 20px; text-align: center; color: #ffffff; }
        .content { padding: 40px; text-align: center; }
        .otp-code { font-size: 42px; font-weight: 800; color: #0071e3; letter-spacing: 8px; margin: 30px 0; padding: 20px; background: #f5f5f7; border-radius: 16px; display: inline-block; width: 80%; }
        .footer { padding: 30px; text-align: center; color: #86868b; font-size: 13px; border-top: 1px solid #f5f5f7; }
        .warning { color: #ff3b30; font-size: 14px; margin-top: 20px; font-weight: 500; }
        h1 { margin: 0; font-size: 24px; font-weight: 700; }
        p { color: #1d1d1f; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>{{ config('app.name') }}</h1>
            </div>
            <div class="content">
                <h2 style="font-size: 20px; font-weight: 600;">Mã xác thực của bạn</h2>
                <p>Vui lòng sử dụng mã dưới đây để hoàn tất quy trình của bạn.</p>
                <div class="otp-code">{{ $otp }}</div>
                <p class="warning">Lưu ý: Mã này chỉ có hiệu lực trong vòng 1 phút.</p>
            </div>
            <div class="footer">
                <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
