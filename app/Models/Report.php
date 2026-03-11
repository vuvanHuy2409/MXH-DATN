<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['user_id', 'reported_id', 'type', 'reason', 'details', 'status'];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reportedObject()
    {
        if ($this->type === 'group') {
            return $this->belongsTo(SocialGroup::class, 'reported_id');
        } elseif ($this->type === 'post') {
            return $this->belongsTo(Post::class, 'reported_id');
        } elseif ($this->type === 'user') {
            return $this->belongsTo(User::class, 'reported_id');
        }
        return null;
    }
}
