<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class NewsClient
{
    /**
     * Fetch news items for a city from Google News RSS
     * Returns array of hashes: { title, link, pubDate, source }
     */
    public static function fetchCityNews(string $cityName, string $country = 'India', int $limit = 4): array
    {
        if (empty(trim($cityName))) {
            return [];
        }

        $query = "{$cityName} {$country}";
        $url = 'https://news.google.com/rss/search?' . http_build_query([
            'q' => $query,
            'hl' => 'en-IN',
            'gl' => 'IN',
            'ceid' => 'IN:en'
        ]);

        Log::info("NewsClient: fetching news URL: {$url}");

        try {
            $response = Http::timeout(6)
                ->withHeaders(['User-Agent' => 'AajchaOffer/1.0'])
                ->get($url);

            if (!$response->successful()) {
                Log::warning("NewsClient: HTTP error fetching {$url}: {$response->status()}");
                return [];
            }

            $body = $response->body();
            
            // Suppress warnings for malformed XML
            $xml = @simplexml_load_string($body);
            if ($xml === false) {
                Log::warning("NewsClient: Failed to parse XML for {$cityName}");
                return [];
            }

            $items = [];
            $count = 0;

            if (isset($xml->channel->item)) {
                foreach ($xml->channel->item as $item) {
                    if ($count >= $limit) {
                        break;
                    }

                    $title = isset($item->title) ? (string)$item->title : '';
                    $link = isset($item->link) ? (string)$item->link : '';
                    $pubDate = isset($item->pubDate) ? (string)$item->pubDate : '';
                    
                    // Extract source from source tag if present
                    $source = '';
                    if (isset($item->source)) {
                        $source = (string)$item->source;
                    }
                    // Fallback: try to extract from description or use generic
                    if (empty($source)) {
                        $source = 'News';
                    }

                    $items[] = [
                        'title' => $title,
                        'link' => $link,
                        'pubDate' => $pubDate,
                        'source' => $source
                    ];

                    $count++;
                }
            }

            Log::info("NewsClient: fetched " . count($items) . " items for {$cityName}");
            return $items;

        } catch (\Exception $e) {
            Log::warning("NewsClient: fetch failed for {$cityName}: " . $e->getMessage());
            return [];
        }
    }
}
