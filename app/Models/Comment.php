<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'content',
        'image_url',
        'reply_count',
    ];

    protected $with = ['user']; // Luôn tải user kèm theo comment

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Quan hệ với bình luận cha
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Quan hệ với các phản hồi con
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
