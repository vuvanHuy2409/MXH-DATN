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

        $toxicPort = Setting::get('toxic_detector_port', $defaultPort);
        $toxicUrl = Setting::get('toxic_detector_url', $defaultUrl);

        return view('admin.settings', compact('toxicPort', 'toxicUrl', 'defaultUrl', 'defaultPort'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'toxic_detector_port' => 'required|numeric|between:1,65535',
            'toxic_detector_url' => 'required|url',
        ]);

        $save1 = Setting::set('toxic_detector_port', $request->toxic_detector_port);
        $save2 = Setting::set('toxic_detector_url', $request->toxic_detector_url);

        if (!$save1 || !$save2) {
            return back()->with('error', 'Lỗi: Không thể lưu cài đặt. Có thể bạn chưa chạy lệnh migrate để tạo bảng settings.');
        }

        return back()->with('success', 'Đã cập nhật cài đặt hệ thống.');
    }

    public function testConnection(Request $request)
    {
        $url = $request->url;
        $port = $request->port;
        $fullUrl = rtrim($url, '/') . ':' . $port;

        try {
            // Sử dụng endpoint /health để kiểm tra nhanh
            $response = Http::timeout(3)->get($fullUrl . '/health');

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Kết nối thành công! API đang hoạt động.'
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
                'message' => 'Không thể kết nối tới API: ' . $e->getMessage() . '. Hãy đảm bảo URL và Port chính xác và máy chủ AI đang chạy.'
            ]);
        }
    }
}
