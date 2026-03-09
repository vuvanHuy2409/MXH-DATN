<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'media_url',
        'media_type',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
