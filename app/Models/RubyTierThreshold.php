<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RubyTierThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'tier_name',
        'threshold_points',
        'first_achiever_id',
        'first_achieved_at',
    ];

    protected $casts = [
        'first_achieved_at' => 'datetime',
    ];

    // Relationships
    public function firstAchiever()
    {
        return $this->belongsTo(User::class, 'first_achiever_id');
    }
}
