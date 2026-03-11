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
        });
    }
}
