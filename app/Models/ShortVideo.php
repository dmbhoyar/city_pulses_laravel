<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortVideo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'creator_id',
        'title',
        'description',
        'source_url',
        'embed_id',
        'video_type',
        'thumbnail_url',
        'duration',
        'status',
        'admin_notes',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function likes()
    {
        return $this->hasMany(VideoLike::class, 'short_video_id');
    }

    public function comments()
    {
        return $this->hasMany(VideoComment::class, 'short_video_id');
    }

    public function shares()
    {
        return $this->hasMany(VideoShare::class, 'short_video_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(ShortVideoSubscription::class, 'short_video_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getTotalRubyPointsAttribute()
    {
        $likes = $this->likes()->count() * 1;
        $comments = $this->comments()->count() * 2;
        $shares = $this->shares()->count() * 3;
        $subscriptions = $this->subscriptions()->count() * 5;

        return $likes + $comments + $shares + $subscriptions;
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function getSharesCountAttribute()
    {
        return $this->shares()->count();
    }

    public function getSubscribersCountAttribute()
    {
        return $this->subscriptions()->distinct('user_id')->count();
    }
}
