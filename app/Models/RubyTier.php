<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RubyTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tier_name',
        'ruby_points',
        'achieved_at',
    ];

    protected $casts = [
        'achieved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Constants for tier names and points
    const TIERS = [
        'silver' => 5000,
        'gold' => 10000,
        'diamond' => 15000,
        'red' => 20000,
    ];

    const TIER_NAMES = [
        'silver' => 'Silver Ruby',
        'gold' => 'Gold Ruby',
        'diamond' => 'Diamond Ruby',
        'red' => 'Red Ruby',
    ];

    const TIER_TAGS = [
        'silver' => 'Silver Ruby Taker',
        'gold' => 'Golden Ruby Holder',
        'diamond' => 'Diamond Ruby Holder',
        'red' => 'Red Ruby Achiever',
    ];

    const TIER_EMOJIS = [
        'silver' => '🥈',
        'gold' => '🥇',
        'diamond' => '💎',
        'red' => '🔴',
    ];
}
