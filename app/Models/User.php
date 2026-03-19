<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'mobile_number',
        'password', 'role', 'experience', 'tags',
        'shop_id', 'subscription_expires_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Role helpers
    public function isShopowner(): bool
    {
        return $this->role === 'shopowner';
    }

    public function isServiceProvider(): bool
    {
        return $this->role === 'service_provider';
    }

    public function isShopworker(): bool
    {
        return $this->role === 'shopworker';
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);
        return implode(' ', $parts) ?: $this->email;
    }
}
