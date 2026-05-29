<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        set_time_limit(0);
        // Ẩn các thông báo Notice và Warning đè lên giao diện (ví dụ: Broken pipe khi dùng artisan serve)
        if (app()->environment('local')) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        }

        \Illuminate\Support\Carbon::setLocale('vi');

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $userId = Auth::id();
                
                $unreadNotifCount = Notification::where('user_id', $userId)
                    ->where('is_read', false)
                    ->count();
                
                $unreadMsgCount = \App\Models\Message::whereIn('conversation_id', function($query) use ($userId) {
                    $query->select('conversation_id')
                          ->from('participants')
                          ->where('user_id', $userId);
                })
                ->where('sender_id', '!=', $userId)
                ->where('is_read', false)
                ->count();

                $view->with('unreadNotificationsCount', $unreadNotifCount);
                $view->with('unreadMessagesCount', $unreadMsgCount);
            } else {
                $view->with('unreadNotificationsCount', 0);
                $view->with('unreadMessagesCount', 0);
            }

            // Kiểm tra trạng thái API kiểm duyệt (Cache trong 10 giây để phản hồi nhanh)
            $toxicService = app(\App\Services\ToxicDetectionService::class);
            $isApiAvailable = \Illuminate\Support\Facades\Cache::remember('toxic_api_available', 10, function() use ($toxicService) {
                return $toxicService->isAvailable();
            });
            $view->with('isToxicApiAvailable', $isApiAvailable);
        });
    }
}
