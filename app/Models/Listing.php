<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Listing extends Model
{
    use HasFactory;

    const CATEGORY_TYPES = ['sell', 'rent', 'service', 'vehicle', 'land'];

    protected $fillable = [
        'title',
        'description',
        'category',
        'subcategory',
        'price',
        'contact_number',
        'location',
        'status',
        'user_id',
        'city_id',
        'shop_id',
        'photos',
        'payment_transaction_id',
        'payment_screenshot_path',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
