<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IndianMarketsClient
{
    /**
     * Fetch Indian stock market indices
     * Returns array of {symbol, name, price, change}
     */
    public static function fetchIndices(): array
    {
        $cacheKey = 'indian_markets_indices';
        
        // Try cache first (2 minute TTL for frequent updates)
        $cached = Cache::get($cacheKey);
        if (!empty($cached) && is_array($cached)) {
            Log::info("IndianMarketsClient: cache hit");
            return $cached;
        }

        $indices = [
            ['symbol' => '^BSESN', 'name' => 'SENSEX'],
            ['symbol' => '^NSEI', 'name' => 'NIFTY 50'],
            ['symbol' => '^NSEBANK', 'name' => 'BANK NIFTY'],
            ['symbol' => '^CNXIT', 'name' => 'NIFTY IT'],
        ];

        $results = [];

        foreach ($indices as $item) {
            try {
                // Use public Yahoo Finance endpoint with timeout
                $response = Http::timeout(5)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (compatible; AajchaOffer/1.0)'
                    ])
                    ->get("https://query1.finance.yahoo.com/v8/finance/chart/{$item['symbol']}", [
                        'interval' => '1d',
                        'range' => '5d'
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $meta = $data['chart']['result'][0]['meta'] ?? null;
                    
                    if ($meta) {
                        $price = (float)($meta['regularMarketPrice'] ?? 0);
                        $prevClose = (float)($meta['chartPreviousClose'] ?? $meta['previousClose'] ?? 0);
                        
                        if ($price > 0 && $prevClose > 0) {
                            $change = (($price - $prevClose) / $prevClose) * 100;
                            $results[] = [
                                'name' => $item['name'],
                                'symbol' => $item['symbol'],
                                'price' => round($price, 2),
                                'change' => round($change, 2),
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("IndianMarketsClient: fetch failed for {$item['symbol']}: " . $e->getMessage());
            }
        }

        // Cache successful results for 2 minutes
        if (!empty($results)) {
            Cache::put($cacheKey, $results, now()->addMinutes(2));
            Log::info("IndianMarketsClient: fetched " . count($results) . " indices");
        }

        return $results;
    }
}
