<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

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
                if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    return $default;
                }
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
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
     * @return bool Trả về true nếu thành công, false nếu thất bại (ví dụ chưa migrate)
     */
    public static function set(string $key, $value): bool
    {
        try {
            self::updateOrCreate(['key' => $key], ['value' => $value]);
            Cache::forget("setting_{$key}");
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Cannot save setting {$key}: " . $e->getMessage());
            return false;
        }
    }
}
