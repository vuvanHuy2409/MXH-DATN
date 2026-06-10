<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ToxicDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SystemSettingController extends Controller
{
    public function index()
    {
        $defaultConfig = config('services.toxic_detector.url', 'http://127.0.0.1:8000');
        $parsed = parse_url($defaultConfig);
        
        $defaultUrl = (($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? '127.0.0.1'));
        $defaultPort = $parsed['port'] ?? '8000';

        $dbUrl = Setting::get('toxic_detector_url', $defaultUrl);
        $dbPort = Setting::get('toxic_detector_port', $defaultPort);

        // Ghép cổng vào URL nếu chưa có
        if ($dbPort && !parse_url($dbUrl, PHP_URL_PORT)) {
            $toxicUrl = rtrim($dbUrl, '/') . ':' . $dbPort;
        } else {
            $toxicUrl = $dbUrl;
        }

        return view('admin.settings', compact('toxicUrl'));
    }

    public function testConnection(Request $request)
    {
        $url = $request->url;
        $fullUrl = rtrim($url, '/');

        try {
            // Sử dụng endpoint /health để kiểm tra nhanh
            $response = Http::timeout(3)->get($fullUrl . '/health');

            if ($response->successful()) {
                // Tự động cấu hình và lưu cài đặt khi kết nối thành công!
                $port = parse_url($url, PHP_URL_PORT);
                Setting::set('toxic_detector_url', $url);
                Setting::set('toxic_detector_port', $port ?? '');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Kết nối thành công! Hệ thống đã tự động lưu địa chỉ cấu hình API mới.'
                ]);
            }

            if ($response->status() === 429) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'API đang quá tải (Too Many Requests). Vui lòng thử lại sau.'
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'API trả về lỗi: ' . $response->status()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể kết nối tới API: ' . $e->getMessage() . '. Hãy đảm bảo URL chính xác và máy chủ AI đang chạy.'
            ]);
        }
    }
}
