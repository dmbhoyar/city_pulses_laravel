<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'template_key',
        'source',
        'customer_name',
        'phone',
        'email',
        'dob',
        'service_name',
        'message',
        'status',
        'admin_notes',
        'contacted_at',
        'resolved_at',
        'meta',
    ];

    protected $casts = [
        'dob' => 'date',
        'contacted_at' => 'datetime',
        'resolved_at' => 'datetime',
        'meta' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
