<?php

// Tăng giới hạn tài nguyên cho Laravel ngay từ khi khởi động
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            return back()->withErrors(['avatar' => 'Dung lượng ảnh hoặc dữ liệu quá lớn. Vui lòng chọn tệp nhỏ hơn (dưới 10MB).'])->withInput();
        });
    })->create();
