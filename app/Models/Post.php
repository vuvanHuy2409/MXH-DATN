<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    const UPDATED_AT = null; // Vô hiệu hóa updated_at vì mxh.sql không có

    protected $fillable = [
        'user_id',
        'group_id',
        'conversation_id',
        'parent_id',
        'post_type',
        'content',
        'link_url',
        'like_count',
        'reply_count',
        'moderation_status',
        'ai_flagged',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(SocialGroup::class, 'group_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function rootComments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class);
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
