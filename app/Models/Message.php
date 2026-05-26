<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $casts = [
        'created_at' => 'datetime',
        'metadata' => 'array',
        'is_read' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    protected $fillable = [
        'conversation_id',
        'parent_id',
        'sender_id',
        'message_type',
        'content',
        'metadata',
        'is_read',
        'is_pinned',
        'created_at',
        'deleted_at',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id')->with('sender');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
