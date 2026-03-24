<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TextTranslationService
{
    private const PROTECTED_BRANDS = [
        'AajchaOffer',
        'CityPulse',
    ];

    public static function translate(string $text, string $targetLocale, string $sourceLocale = 'auto'): string
    {
        $value = trim($text);
        $target = strtolower(trim($targetLocale));

        if ($value === '' || $target === '' || $target === 'en') {
            return $value;
        }

        [$maskedValue, $restoreMap] = self::maskBrands($value);
        $cacheKey = 'gtx:' . md5($sourceLocale . '|' . $target . '|' . $maskedValue);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($value, $maskedValue, $restoreMap, $target, $sourceLocale) {
            try {
                $response = Http::timeout(4)
                    ->retry(1, 120)
                    ->get('https://translate.googleapis.com/translate_a/single', [
                        'client' => 'gtx',
                        'sl' => $sourceLocale,
                        'tl' => $target,
                        'dt' => 't',
                        'q' => $maskedValue,
                    ]);

                if (!$response->successful()) {
                    return $value;
                }

                $payload = $response->json();
                if (!is_array($payload) || !isset($payload[0]) || !is_array($payload[0])) {
                    return $value;
                }

                $translated = '';
                foreach ($payload[0] as $part) {
                    if (is_array($part) && isset($part[0])) {
                        $translated .= (string) $part[0];
                    }
                }

                $translated = self::restoreBrands($translated, $restoreMap);
                return trim($translated) !== '' ? $translated : $value;
            } catch (\Throwable $e) {
                return $value;
            }
        });
    }

    private static function maskBrands(string $text): array
    {
        $masked = $text;
        $restoreMap = [];

        foreach (self::PROTECTED_BRANDS as $index => $brand) {
            $token = "__BRAND_{$index}__";
            $pattern = '/' . preg_quote($brand, '/') . '/iu';

            if (preg_match($pattern, $masked)) {
                $restoreMap[$token] = $brand;
                $masked = preg_replace($pattern, $token, $masked);
            }
        }

        return [$masked, $restoreMap];
    }

    private static function restoreBrands(string $text, array $restoreMap): string
    {
        if (empty($restoreMap)) {
            return $text;
        }

        return strtr($text, $restoreMap);
    }
}
