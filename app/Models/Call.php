<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'caller_id',
        'call_type',
        'status',
        'started_at',
        'ended_at',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }
}
