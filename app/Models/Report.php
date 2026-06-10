<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['user_id', 'reported_id', 'type', 'reason', 'details', 'status'];

    // Danh sách lý do báo cáo với icon và màu sắc
    public static array $REASONS = [
        'spam'          => ['label' => 'Spam / Quảng cáo',               'icon' => '📢', 'color' => '#f59e0b'],
        'harassment'    => ['label' => 'Ngôn từ thù địch / Quấy rối',   'icon' => '💬', 'color' => '#ef4444'],
        'inappropriate' => ['label' => 'Nội dung không phù hợp / Khiêu dâm', 'icon' => '🚫', 'color' => '#8b5cf6'],
        'violence'      => ['label' => 'Nội dung bạo lực',               'icon' => '🔞', 'color' => '#dc2626'],
        'misinformation'=> ['label' => 'Tin giả / Lừa đảo',             'icon' => '🎣', 'color' => '#ea580c'],
        'privacy'       => ['label' => 'Vi phạm quyền riêng tư',         'icon' => '🔒', 'color' => '#0284c7'],
        'other'         => ['label' => 'Khác',                           'icon' => '⚠️', 'color' => '#6b7280'],
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reportedPost()
    {
        return $this->belongsTo(Post::class, 'reported_id');
    }

    public function reportedComment()
    {
        return $this->belongsTo(Comment::class, 'reported_id');
    }

    public function reportedMessage()
    {
        return $this->belongsTo(Message::class, 'reported_id');
    }

    public function reportedObject()
    {
        if ($this->type === 'post') {
            return $this->belongsTo(Post::class, 'reported_id');
        } elseif ($this->type === 'user') {
            return $this->belongsTo(User::class, 'reported_id');
        } elseif ($this->type === 'comment') {
            return $this->belongsTo(Comment::class, 'reported_id');
        } elseif ($this->type === 'message') {
            return $this->belongsTo(Message::class, 'reported_id');
        }
        return null;
    }
}
