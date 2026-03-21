<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

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

    public function clientRequests()
    {
        return $this->hasMany(ClientRequest::class);
    }

    public function serviceReviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    public function getPageConfigAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_string($value)) return json_decode($value, true) ?? [];
        return [];
    }

    public function getPublicPageSlugAttribute(): string
    {
        $cfg = $this->page_config ?? [];
        $configured = Str::slug((string) ($cfg['public_slug'] ?? ''));

        if ($configured !== '') {
            return $configured;
        }

        $fallback = trim($this->public_service_slug . '-' . $this->public_provider_slug, '-');

        return Str::slug($fallback) ?: 'service-page';
    }

    public function getPublicServiceSlugAttribute(): string
    {
        $cfg = $this->page_config ?? [];
        $services = is_array($cfg['services'] ?? null) ? $cfg['services'] : [];
        $firstServiceName = trim((string) data_get($services, '0.name', ''));
        $base = $firstServiceName !== '' ? $firstServiceName : ($this->name ?: 'service');

        return Str::slug($base) ?: 'service';
    }

    public function getPublicProviderSlugAttribute(): string
    {
        $cfg = $this->page_config ?? [];
        $tc = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];
        $configured = trim((string) ($tc['provider_name'] ?? ''));
        $ownerName = trim((string) optional($this->user)->full_name);

        $fallbackEmail = (string) optional($this->user)->email;
        $fallbackFromEmail = $fallbackEmail ? Str::before($fallbackEmail, '@') : '';
        $base = $configured !== '' ? $configured : ($ownerName !== '' ? $ownerName : $fallbackFromEmail);

        return Str::slug($base) ?: 'provider';
    }
}
