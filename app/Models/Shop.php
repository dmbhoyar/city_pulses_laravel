<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'phone', 'address', 'template', 'page_config', 'user_id', 'city_id'];

    protected $casts = [
        'page_config' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    public function getPageConfigAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_string($value)) return json_decode($value, true) ?? [];
        return [];
    }
}
