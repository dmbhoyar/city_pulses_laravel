<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'title', 'store', 'image_url', 'discount_text', 'description', 'shop_url', 'points_required', 'category', 'expiry_date', 'source', 'raw_data'
    ];
}
