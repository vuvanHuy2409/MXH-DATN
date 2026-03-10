<?php

// Tắt thông báo Notice/Warning đè lên UI (đặc biệt là lỗi Broken pipe trên Mac)
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0'); // Không hiển thị lỗi trực tiếp ra HTML

// Tăng giới hạn tài nguyên cho Laravel
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
