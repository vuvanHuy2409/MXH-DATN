<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null; // Only have created_at

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'post_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Người nhận thông báo.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Người tạo hành động (like, follow, reply...).
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Bài viết liên quan (nullable cho follow).
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
