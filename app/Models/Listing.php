<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Listing extends Model
{
    use HasFactory;

    const CATEGORY_TYPES = ['sell', 'rent', 'service', 'vehicle', 'land'];

    protected $fillable = ['title', 'description', 'category', 'subcategory', 'price', 'contact_number', 'location', 'status', 'user_id', 'city_id', 'shop_id'];

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
}
