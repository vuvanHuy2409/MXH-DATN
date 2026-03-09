<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'join_code',
        'name',
        'theme_color',
        'avatar_url',
        'last_message_id',
        'creator_id',
        'requires_approval',
    ];

    public static function generateUniqueJoinCode()
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(5))); // This would be longer than 5
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5);
        } while (self::where('join_code', $code)->exists());

        return $code;
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'participants', 'conversation_id', 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
