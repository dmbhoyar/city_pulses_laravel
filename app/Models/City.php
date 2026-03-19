<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'agmarknet_district', 'agmarknet_market', 'agmarknet_state', 'latitude', 'longitude'];

    public function markets()
    {
        return $this->hasMany(Market::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function farmings()
    {
        return $this->hasMany(Farming::class);
    }

    public function updates()
    {
        return $this->hasMany(\App\Models\Update::class);
    }
}
