<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProfile extends Model
{
    protected $fillable = [
        'user_id',
        // Business identity
        'business_name', 'business_phone', 'business_email', 'business_address', 'business_logo',
        // Tax
        'gstin',
        // Bank
        'bank_name', 'bank_account', 'bank_ifsc', 'bank_holder',
        // Files
        'payment_qr', 'signature',
        // Defaults
        'default_terms', 'default_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Get or create a blank profile for the given user. */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }
}
