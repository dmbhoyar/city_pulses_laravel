<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_video_id',
        'user_id',
        'platform',
    ];

    public $timestamps = false;

    // Relationships
    public function video()
    {
        return $this->belongsTo(ShortVideo::class, 'short_video_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
