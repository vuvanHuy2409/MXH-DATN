<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\RepostController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Các Route Guest (Chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/register/verify', [AuthController::class, 'showVerifyRegistration'])->name('register.verify.form');
    Route::post('/register/verify', [AuthController::class, 'verifyRegistration'])->name('register.verify');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendOtpForgotPassword'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Các Route Auth (Đã đăng nhập)
Route::middleware('auth')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('home');
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/api/suggestions', [SearchController::class, 'suggestions'])->name('api.suggestions');
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/api/notifications', [NotificationController::class, 'apiIndex'])->name('api.notifications');
    Route::post('/api/notifications/read', [NotificationController::class, 'markAllRead'])->name('api.notifications.read');
    Route::get('/api/unread-counts', [NotificationController::class, 'unreadCounts'])->name('api.unread_counts');
    
    // Social Groups
    Route::get('/groups/{slug?}', [App\Http\Controllers\SocialGroupController::class, 'index'])->name('groups.index');
    Route::post('/groups', [App\Http\Controllers\SocialGroupController::class, 'store'])->name('groups.store');
    Route::put('/groups/{group:slug}', [App\Http\Controllers\SocialGroupController::class, 'update'])->name('groups.update');
    Route::post('/groups/{group:slug}/avatar', [App\Http\Controllers\SocialGroupController::class, 'updateAvatar'])->name('groups.update_avatar');
    Route::delete('/groups/{group:slug}', [App\Http\Controllers\SocialGroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('/groups/join-by-code', [App\Http\Controllers\SocialGroupController::class, 'joinByCode'])->name('groups.join_by_code');
    // Bỏ route groups.show riêng biệt vì đã gộp vào index
    Route::get('/groups/view/{group:slug}', function($slug) { return redirect()->route('groups.index', $slug); })->name('groups.show');
    Route::post('/groups/{group:slug}/join', [App\Http\Controllers\SocialGroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group:slug}/leave', [App\Http\Controllers\SocialGroupController::class, 'leave'])->name('groups.leave');
    Route::get('/groups/{group:slug}/members', [App\Http\Controllers\SocialGroupController::class, 'getMembers'])->name('groups.members');
    Route::get('/groups/{group:slug}/search-users', [App\Http\Controllers\SocialGroupController::class, 'searchUsers'])->name('groups.search_users');
    Route::post('/groups/{group:slug}/add-member', [App\Http\Controllers\SocialGroupController::class, 'addMember'])->name('groups.add_member');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Tương tác bài viết
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::get('/posts/{post}/likes', [PostController::class, 'getLikes'])->name('posts.likes');
    Route::get('/posts/{post}/comments', [PostController::class, 'getComments'])->name('posts.comments');
    Route::post('/posts/{post}/reply', [ReplyController::class, 'store'])->name('posts.reply');
    Route::delete('/comments/{comment}', [PostController::class, 'destroyComment'])->name('comments.destroy');
    Route::post('/posts/{post}/repost', [RepostController::class, 'toggle'])->name('posts.repost');

    // Follow
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');

    // API: Tìm kiếm bạn bè (mutual follow)
    Route::get('/api/friends/search', [ConversationController::class, 'searchFriends'])->name('api.friends.search');

    // API: Chat widget (dùng trên trang profile)
    Route::get('/api/chat/start/{user}', [ConversationController::class, 'apiStartDirect'])->name('api.chat.start');
    Route::get('/api/chat/{conversation}/messages', [ConversationController::class, 'apiMessages'])->name('api.chat.messages');
    
    // API: Follow lists
    Route::get('/api/users/{user}/followers', [ProfileController::class, 'followers'])->name('api.followers');
    Route::get('/api/users/{user}/following', [ProfileController::class, 'following'])->name('api.following');

    // Messaging
    Route::get('/messages', [ConversationController::class, 'index'])->name('messages.index');
    Route::get('/messages/direct/{user}', [ConversationController::class, 'startDirect'])->name('messages.direct');
    Route::post('/messages/group', [ConversationController::class, 'startGroup'])->name('messages.group');
    Route::post('/messages/join-by-code', [ConversationController::class, 'joinByCode'])->name('messages.join_by_code');
    
    Route::prefix('messages/{conversation}')->group(function () {
        Route::get('/', [ConversationController::class, 'show'])->name('messages.show');
        Route::post('/', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/mute', [ConversationController::class, 'toggleMute'])->name('messages.mute');
        Route::post('/leave', [ConversationController::class, 'leaveGroup'])->name('messages.leave');
        Route::delete('/kick/{user}', [ConversationController::class, 'kickMember'])->name('messages.kick');
        Route::post('/add-members', [ConversationController::class, 'addMembers'])->name('messages.add_members');
        Route::post('/approve/{user}', [ConversationController::class, 'approveMember'])->name('messages.approve');
        Route::get('/members', [ConversationController::class, 'getMembers'])->name('messages.members');
        Route::get('/media', [ConversationController::class, 'getMedia'])->name('messages.media');
        Route::get('/search', [ConversationController::class, 'searchMessages'])->name('messages.search');
        Route::post('/avatar', [ConversationController::class, 'updateGroupAvatar'])->name('messages.update_avatar');
        Route::post('/name', [ConversationController::class, 'updateName'])->name('messages.update_name');
        Route::post('/refresh-code', [ConversationController::class, 'refreshJoinCode'])->name('messages.refresh_code');
        Route::post('/color', [ConversationController::class, 'updateThemeColor'])->name('messages.update_color');
        Route::delete('/chat', [ConversationController::class, 'deleteChat'])->name('messages.delete_chat');
        Route::post('/messages/{message}/pin', [ConversationController::class, 'togglePin'])->name('messages.toggle_pin');
        Route::delete('/messages/{message}', [ConversationController::class, 'deleteMessage'])->name('messages.delete_message');
        Route::post('/appointments', [ConversationController::class, 'createAppointment'])->name('messages.create_appointment');
        Route::get('/appointments', [ConversationController::class, 'getAppointments'])->name('messages.get_appointments');
        Route::delete('/delete', [ConversationController::class, 'deleteChat'])->name('messages.delete_chat');
    });

    // Profile cá nhân (Your Profile)
    Route::get('/profile', function () {
        $user = auth()->user()->loadCount(['followers', 'following'])->load(['student.faculty', 'teacher.faculty']);
        $posts = $user->posts()->with(['user', 'media', 'likes'])->withCount(['reposts'])->latest()->get();
        $replies = \App\Models\Comment::where('user_id', $user->id)->with(['user', 'post.user'])->latest()->get();
        
        $repostedIds = \App\Models\Repost::where('user_id', $user->id)->pluck('post_id');
        $reposts = \App\Models\Post::whereIn('id', $repostedIds)->with(['user', 'media', 'likes'])->withCount(['reposts'])->latest()->get();

        $photos = \App\Models\PostMedia::whereIn('post_id', $user->posts()->pluck('id'))
            ->whereIn('media_type', ['image', 'gif'])
            ->latest('id')
            ->get();

        $canSeeContent = true;
        return view('profile.show', compact('user', 'posts', 'replies', 'reposts', 'photos', 'canSeeContent'));
    })->name('profile.me');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

    // Profile người khác (Guest Profile)
    Route::get('/@{username}', function ($username) {
        $user = \App\Models\User::where('username', $username)
            ->withCount(['followers', 'following'])
            ->with(['student.faculty', 'teacher.faculty'])
            ->firstOrFail();

        // Nếu là chính mình thì redirect sang trang profile cá nhân
        if ($user->id === auth()->id()) {
            return redirect()->route('profile.me');
        }

        $isFollowing = auth()->user()->following->contains($user->id);
        $followsMe = auth()->user()->followers->contains($user->id);
        $canSeeContent = !$user->is_private || $isFollowing;

        $posts = collect();
        $replies = collect();
        $reposts = collect();
        $photos = collect();

        if ($canSeeContent) {
            $posts = $user->posts()->with(['user', 'media', 'likes'])->withCount(['reposts'])->latest()->get();
            $replies = \App\Models\Comment::where('user_id', $user->id)->with(['user', 'post.user'])->latest()->get();

            $repostedIds = \App\Models\Repost::where('user_id', $user->id)->pluck('post_id');
            $reposts = \App\Models\Post::whereIn('id', $repostedIds)->with(['user', 'media', 'likes'])->withCount(['reposts'])->latest()->get();
            
            $photos = \App\Models\PostMedia::whereIn('post_id', $user->posts()->pluck('id'))
                ->whereIn('media_type', ['image', 'gif'])
                ->latest('id')
                ->get();
        }

        return view('profile.show', compact('user', 'posts', 'replies', 'reposts', 'photos', 'isFollowing', 'followsMe', 'canSeeContent'));
    })->name('profile.show');

    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/privacy', [App\Http\Controllers\SettingsController::class, 'togglePrivacy'])->name('settings.privacy');
    Route::get('/settings/help', [App\Http\Controllers\SettingsController::class, 'help'])->name('settings.help');
    Route::post('/settings/feedback', [App\Http\Controllers\SettingsController::class, 'sendFeedback'])->name('settings.feedback');
    Route::post('/settings/change-password', [App\Http\Controllers\SettingsController::class, 'changePassword'])->name('settings.change-password');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
Route::post('/reports', [\App\Http\Controllers\ReportController::class, 'store'])->name('reports.store')->middleware('auth');
