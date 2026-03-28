<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_video_id',
        'user_id',
        'parent_id',
        'comment_text',
        'pinned',
    ];

    // Relationships
    public function video()
    {
        return $this->belongsTo(ShortVideo::class, 'short_video_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(VideoComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(VideoComment::class, 'parent_id')->with('user:id,first_name,last_name')->withCount('likes')->orderBy('created_at');
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }
}
