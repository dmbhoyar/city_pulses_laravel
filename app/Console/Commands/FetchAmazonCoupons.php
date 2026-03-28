<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Coupon;

class FetchAmazonCoupons extends Command
{
    protected $signature = 'coupons:fetch-amazon';
    protected $description = 'Fetch latest Amazon coupons from RapidAPI and store in DB';

    public function handle()
    {
        // Use a dynamic start_date: 30 days ago
        
        $url = "https://get-amazon-coupon.p.rapidapi.com/amazon/coupon/?start_date=2023-10-01&page=1&sort=addtime_desc";
        $headers = [
            'Content-Type' => 'application/json',
            'x-rapidapi-host' => 'get-amazon-coupon.p.rapidapi.com',
            'x-rapidapi-key' => '5c477d754amshd4e077d9be89c39p1008e3jsn6883e3513f6e',
        ];

        $response = Http::withHeaders($headers)->get($url);

        if (!$response->ok()) {
            $this->error('Failed to fetch coupons: ' . $response->status());
            Log::error('Amazon coupon fetch failed', ['status' => $response->status(), 'body' => $response->body()]);
            return 1;
        }

        $coupons = $response->json('data') ?? [];
        $count = 0;
        foreach ($coupons as $coupon) {
            // Try to find a code field in the API response
            $code = $coupon['code'] ?? $coupon['coupon_code'] ?? $coupon['discount_code'] ?? null;
            // If no code, fallback to percent (not ideal, but for display)
            $displayCode = $code ?? $coupon['percent'] ?? null;
            Coupon::updateOrCreate(
                [
                    'title' => $coupon['title'] ?? '',
                    'source' => 'amazon',
                ],
                [
                    'store' => 'Amazon',
                    'image_url' => null, // Not provided in API
                    'discount_text' => $displayCode, // Use code if present, else percent
                    'description' => $coupon['percent'] ?? null, // Save percent as description if code is present
                    'shop_url' => $coupon['url'] ?? null,
                    'points_required' => null, // Not provided in API
                    'category' => null, // Not provided in API
                    'expiry_date' => isset($coupon['end_time']) ? substr($coupon['end_time'], 0, 10) : null,
                    'raw_data' => json_encode($coupon),
                ]
            );
            $count++;
        }
        $this->info("Fetched and stored $count coupons.");
        return 0;
    }
}
