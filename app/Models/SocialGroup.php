<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SocialGroup extends Model
{
    protected $fillable = ['type', 'name', 'class_name', 'slug', 'description', 'avatar_url', 'cover_url', 'creator_id', 'privacy', 'join_code'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($group) {
            $group->slug = Str::slug($group->name) . '-' . Str::random(5);
            $group->join_code = self::generateUniqueCode();
        });
    }

    public static function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(5));
        } while (self::where('join_code', $code)->exists());
        return $code;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id')
                    ->withPivot('role', 'joined_at');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'group_id');
    }

    /**
     * Kiểm tra xem một người dùng có phải là quản trị viên (admin) hoặc người tạo (creator) của nhóm hay không.
     *
     * @param int|null $userId
     * @return bool
     */
    public function isAdmin($userId = null)
    {
        $userId = $userId ?: auth()->id();
        if (!$userId) return false;

        // Người tạo luôn là admin
        if ($this->creator_id === $userId) return true;

        // Kiểm tra trong bảng group_members
        return $this->members()
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->exists();
    }
}
