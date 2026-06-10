<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Đường dẫn tới file JSON lưu cấu hình.
     */
    protected static function getFilePath()
    {
        return storage_path('app/settings.json');
    }

    /**
     * Lấy giá trị cài đặt theo key.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        try {
            return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
                $path = self::getFilePath();
                if (!file_exists($path)) {
                    return $default;
                }
                $json = file_get_contents($path);
                $data = json_decode($json, true);
                return isset($data[$key]) ? $data[$key] : $default;
            });
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Lưu giá trị cài đặt.
     * 
     * @param string $key
     * @param mixed $value
     * @return bool Trả về true nếu thành công, false nếu thất bại
     */
    public static function set(string $key, $value): bool
    {
        try {
            $path = self::getFilePath();
            $data = [];
            if (file_exists($path)) {
                $json = file_get_contents($path);
                $data = json_decode($json, true) ?: [];
            }
            $data[$key] = $value;
            
            // Đảm bảo thư mục cha tồn tại
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
            Cache::forget("setting_{$key}");
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Cannot save setting {$key}: " . $e->getMessage());
            return false;
        }
    }
}
