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
        'shop_id', 'subscription_expires_at', 'ruby_points', 'shorts_role',
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

    public function templateUnlockRequests()
    {
        return $this->hasMany(TemplateUnlockRequest::class);
    }

    public function serviceReviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    // ShortsPlay Relationships
    public function shortVideos()
    {
        return $this->hasMany(ShortVideo::class, 'creator_id');
    }

    public function videoLikes()
    {
        return $this->hasMany(VideoLike::class);
    }

    public function videoComments()
    {
        return $this->hasMany(VideoComment::class);
    }

    public function videoShares()
    {
        return $this->hasMany(VideoShare::class);
    }

    public function shortVideoSubscriptions()
    {
        return $this->hasMany(ShortVideoSubscription::class);
    }

    public function creatorSubscriptions()
    {
        return $this->hasMany(ShortVideoSubscription::class, 'creator_id');
    }

    public function rubyTiers()
    {
        return $this->hasMany(RubyTier::class);
    }

    // Role helpers
    public function isShopowner(): bool
    {
        return in_array(strtolower((string) $this->role), ['shopowner', 'shop_owner', 'shop-owner'], true);
    }

    public function isServiceProvider(): bool
    {
        return in_array(strtolower((string) $this->role), ['service_provider', 'serviceprovider', 'service-provider'], true);
    }

    public function isShopworker(): bool
    {
        return in_array(strtolower((string) $this->role), ['shopworker', 'shop_worker', 'shop-worker'], true);
    }

    public function isSuperadmin(): bool
    {
        return in_array(strtolower((string) $this->role), ['superadmin', 'super_admin', 'super-admin', 'admin'], true);
    }

    public function isSeller(): bool
    {
        return in_array(strtolower((string) $this->role), ['seller'], true);
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);
        return implode(' ', $parts) ?: $this->email;
    }
}
