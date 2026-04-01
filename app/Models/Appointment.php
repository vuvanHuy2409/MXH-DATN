<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'creator_id',
        'title',
        'appointment_time',
        'location',
        'description',
        'status',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function appointmentParticipants()
    {
        return $this->hasMany(AppointmentParticipant::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'appointment_participants')
                    ->withPivot('status', 'reminded', 'reminded_at')
                    ->withTimestamps();
    }
}
