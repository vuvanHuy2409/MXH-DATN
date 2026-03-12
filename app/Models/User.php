<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'email', 'password_hash', 'user_type', 'avatar_url', 'bio', 'link_url', 'is_private'
    ];

    protected $hidden = ['password_hash', 'remember_token'];

    public function getAuthPassword() { return $this->password_hash; }

    // Quan hệ lấy thông tin chi tiết tùy theo loại user
    public function student() { return $this->hasOne(StudentDetail::class); }
    public function teacher() { return $this->hasOne(TeacherDetail::class); }

    // Helper lấy tên đầy đủ linh hoạt
    public function getFullNameAttribute() {
        return $this->user_type === 'student' 
            ? ($this->student->full_name ?? $this->username)
            : ($this->teacher->full_name ?? $this->username);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function socialGroups()
    {
        return $this->belongsToMany(SocialGroup::class, 'group_members', 'user_id', 'group_id')
                    ->withPivot('role', 'joined_at');
    }
    public function comments() { return $this->hasMany(Comment::class); }
    public function following() { return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id'); }
    public function followers() { return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id'); }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'participants', 'user_id', 'conversation_id')
            ->withPivot(['role', 'status', 'is_muted', 'joined_at']);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function reposts()
    {
        return $this->hasMany(Repost::class);
    }
}
