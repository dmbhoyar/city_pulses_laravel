<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CouponApiService
{
    // Hostinger shared hosting: use file cache, short TTL, and avoid hitting API too often
    protected static $cacheKey = 'coupon_api_data';
    protected static $cacheTtl = 10 * 60; // 10 minutes

    /**
     * Fetch coupons from external API (RapidAPI or similar)
     * @return array
     */
    public static function fetchCoupons(): array
    {
        $cached = Cache::get(self::$cacheKey);
        if ($cached && is_array($cached)) {
            return $cached;
        }

        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => env('RAPIDAPI_KEY'),
                'X-RapidAPI-Host' => env('RAPIDAPI_HOST', 'free-coupon-codes.p.rapidapi.com'),
            ])->timeout(10)->get('https://free-coupon-codes.p.rapidapi.com/v1/coupons', [
                'store' => 'amazon', // Example param, adjust as needed
                'country' => 'IN',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data)) {
                    Cache::put(self::$cacheKey, $data, self::$cacheTtl);
                    return $data;
                }
            }
        } catch (\Exception $e) {
            Log::warning('CouponApiService: API fetch failed: ' . $e->getMessage());
        }
        return $cached ?: [];
    }
}
