<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToxicDetectionService
{
    protected string $apiUrl;

    public function __construct()
    {
        $defaultConfig = config('services.toxic_detector.url', 'http://127.0.0.1:8000');
        $parsed = parse_url($defaultConfig);
        
        $defaultHost = (($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? '127.0.0.1'));
        $defaultPort = $parsed['port'] ?? '8000';

        $dbUrl = \App\Models\Setting::get('toxic_detector_url', $defaultHost);
        $dbPort = \App\Models\Setting::get('toxic_detector_port', $defaultPort);

        $this->apiUrl = rtrim($dbUrl, '/') . ':' . $dbPort;
    }

    /**
     * Kiểm tra nội dung có vi phạm (Toxic) hay không.
     *
     * @param string|null $text
     * @return array|null Trả về mảng thông tin vi phạm hoặc lỗi nếu có, ngược lại trả về null.
     */
    public function validateContent(?string $text): ?array
    {
        if (empty(trim($text))) {
            return null;
        }

        try {
            $response = Http::timeout(5)->post($this->apiUrl . '/predict', [
                'text' => $text,
                'strategy' => 'stacking_thresh'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Kiểm tra xem label có phải là vi phạm không (HATE hoặc OFFENSIVE)
                if (in_array($data['label'], ['HATE', 'OFFENSIVE'])) {
                    return [
                        'label' => $data['label'],
                        'confidence' => $data['confidence'],
                        'banned_word' => $data['banned_word'] ?? null,
                    ];
                }
                
                return null;
            }
            
            Log::error('Toxic Detection API returned error: ' . $response->status() . ' - ' . $response->body());
            return ['error' => 'API_ERROR'];
        } catch (\Exception $e) {
            Log::error('Toxic Detection API Exception: ' . $e->getMessage());
            return ['error' => 'API_DOWN'];
        }
    }

    /**
     * Kiểm tra xem API kiểm duyệt có đang hoạt động hay không.
     * 
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            // Sử dụng endpoint /health đã được định nghĩa trong API Python
            $response = Http::timeout(2)->get($this->apiUrl . '/health');

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
