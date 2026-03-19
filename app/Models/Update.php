<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Update extends Model
{
    use HasFactory;

    const UPDATE_TYPES = ['general', 'offer', 'event'];

    protected $fillable = ['title', 'content', 'update_type', 'source_url', 'published_at', 'city_id'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function scopeOffers(Builder $query): Builder
    {
        return $query->where('update_type', 'offer');
    }

    public function scopeEvents(Builder $query): Builder
    {
        return $query->where('update_type', 'event');
    }

    public function isOffer(): bool
    {
        return $this->update_type === 'offer';
    }

    public function isEvent(): bool
    {
        return $this->update_type === 'event';
    }
}
