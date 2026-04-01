<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentParticipant extends Model
{
    protected $fillable = [
        'appointment_id',
        'user_id',
        'status',
        'reminded',
        'reminded_at',
    ];

    protected $casts = [
        'reminded' => 'boolean',
        'reminded_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
