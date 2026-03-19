<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Market extends Model
{
    use HasFactory;

    protected $fillable = ['city', 'district', 'commodity', 'min_price', 'max_price', 'modal_price', 'rate', 'latitude', 'longitude', 'price_date', 'source_url', 'city_id'];

    protected $casts = [
        'price_date' => 'date',
    ];

    public function city_rel()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Haversine distance in km
     */
    public static function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $radPerDeg = M_PI / 180;
        $rkm = 6371;
        $dlatRad = ($lat2 - $lat1) * $radPerDeg;
        $dlonRad = ($lon2 - $lon1) * $radPerDeg;
        $lat1Rad = $lat1 * $radPerDeg;
        $lat2Rad = $lat2 * $radPerDeg;

        $a = sin($dlatRad / 2) ** 2 + cos($lat1Rad) * cos($lat2Rad) * sin($dlonRad / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $rkm * $c;
    }

    public static function nearbyRates(float $lat, float $lng, float $maxKm = 50): \Illuminate\Support\Collection
    {
        return static::all()->filter(function ($m) use ($lat, $lng, $maxKm) {
            if (!$m->latitude || !$m->longitude) return false;
            return static::haversine($lat, $lng, (float)$m->latitude, (float)$m->longitude) <= $maxKm;
        });
    }
}
