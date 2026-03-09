<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'student_id', 'full_name', 'dob', 'class', 'faculty_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function faculty() { return $this->belongsTo(Faculty::class); }
}
