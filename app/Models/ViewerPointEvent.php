<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewerPointEvent extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'subject_type',
        'subject_id',
        'points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
