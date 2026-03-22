<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Farming extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author_name', 'content', 'city_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
