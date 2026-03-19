<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'category', 'company', 'location', 'external_url', 'city_id', 'user_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopeSearch($query, $q)
    {
        if (!$q) return $query;
        $like = '%' . strtolower($q) . '%';
        return $query->whereRaw('LOWER(title) LIKE ?', [$like])
            ->orWhereRaw('LOWER(description) LIKE ?', [$like])
            ->orWhereRaw('LOWER(category) LIKE ?', [$like]);
    }
}
