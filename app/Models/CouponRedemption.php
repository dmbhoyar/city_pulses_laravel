<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_code',
        'store',
        'title',
        'redeemed_at',
    ];

    protected $dates = [
        'redeemed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
