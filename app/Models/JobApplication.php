<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'message', 'resume_url', 'job_id'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
