<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{
    protected $fillable = [
        'short_video_id',
        'user_id',
    ];

    public function video()
    {
        return $this->belongsTo(ShortVideo::class, 'short_video_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
