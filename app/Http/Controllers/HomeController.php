<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Market;
use App\Models\Update;
use App\Models\Job;
use App\Models\Farming;
use App\Services\NewsClient;
use App\Services\IndianMarketsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // City selection
        $city = null;
        if ($request->session()->has('city_id')) {
            $city = City::find($request->session()->get('city_id'));
        } elseif ($request->filled('city')) {
            $city = City::where('name', $request->input('city'))->first();
            if ($city) $request->session()->put('city_id', $city->id);
        }

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        // Market rates
        $marketRates = [];
        $ratesToday = collect();
        $ratesYesterday = collect();
        $ratesTomorrow = collect();

        if ($city) {
            // Try AgMarkNet API first
            $rows = $this->fetchAgmarknetRates($city);

            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $market = trim($r['Market'] ?? '');
                    $commodity = trim($r['Commodity'] ?? '');
                    if (!isset($marketRates[$market])) $marketRates[$market] = [];
                    if (!isset($marketRates[$market][$commodity])) $marketRates[$market][$commodity] = [];
                    $marketRates[$market][$commodity][] = [
                        'variety'     => $r['Variety'] ?? null,
                        'grade'       => $r['Grade'] ?? null,
                        'min_price'   => $r['Min_Price'] ?? null,
                        'max_price'   => $r['Max_Price'] ?? null,
                        'modal_price' => $r['Modal_Price'] ?? null,
                        'date'        => $r['Arrival_Date'] ?? null,
                    ];
                }
            } else {
                // DB fallback
                [$ratesToday, $ratesYesterday, $ratesTomorrow] = $this->fallbackMarketRates($city);
            }
        } else {
            [$ratesToday, $ratesYesterday, $ratesTomorrow] = $this->fallbackMarketRates(null);
        }

        // Metals prices
        $metals = ['gold' => null, 'silver' => null];
        $metalsMeta = [];
        $goldPrices = [];
        $silverPrices = [];

        try {
            $metalData = $this->fetchMetals();
            if ($metalData) {
                $metals['gold']   = $metalData['gold'] ?? null;
                $metals['silver'] = $metalData['silver'] ?? null;
                $metalsMeta       = $metalData['_meta'] ?? [];
            }
        } catch (\Exception $e) {
            \Log::warning('[HomeController] Metals fetch failed: ' . $e->getMessage());
        }

        if ($metals['gold']) {
            $goldPrices = $this->computeGoldPrices((float)$metals['gold']);
        }
        if ($metals['silver']) {
            $silverPrices = $this->computeSilverPrices((float)$metals['silver']);
        }

        // Expose individual price variables to the view for backward compatibility
        if (!empty($goldPrices) && is_array($goldPrices)) {
            foreach ($goldPrices as $k => $v) {
                ${$k} = $v;
            }
        }
        if (!empty($silverPrices) && is_array($silverPrices)) {
            foreach ($silverPrices as $k => $v) {
                ${$k} = $v;
            }
        }

        // News
        $cityNews = [];
        if ($city) {
            try {
                $cityNews = $this->fetchCityNews($city->name, 5);
            } catch (\Exception $e) {
                \Log::warning('[HomeController] News fetch failed: ' . $e->getMessage());
            }
        }

        // Indian Markets
        $indianMarkets = [];
        try {
            $indianMarkets = IndianMarketsClient::fetchIndices();
        } catch (\Exception $e) {
            \Log::warning('[HomeController] Indian markets fetch failed: ' . $e->getMessage());
        }

        // Offers (DB)
        $offers = $city
            ? Update::where('city_id', $city->id)->offers()->latest()->get()
            : Update::offers()->latest()->get();

        // Weather
        $weather = null;
        if ($city && $city->latitude && $city->longitude) {
            try {
                $weather = $this->fetchWeather($city->latitude, $city->longitude);
            } catch (\Exception $e) {}
        }

        // Updates sidebar
        $updates = $city
            ? Update::where('city_id', $city->id)->latest()->take(5)->get()
            : Update::latest()->take(5)->get();

        // Jobs sidebar
        $jobs = $city
            ? Job::where('city_id', $city->id)->latest()->take(5)->get()
            : Job::latest()->take(5)->get();

        // Farmings
        $farmings = $city
            ? Farming::where('city_id', $city->id)->latest()->take(5)->get()
            : Farming::latest()->take(5)->get();

        // Cities list for selector
        $cities = City::orderBy('name')->pluck('name');
        $cityRecords = City::orderBy('name')->get(['id', 'name', 'latitude', 'longitude']);

        $viewData = compact(
            'city', 'cities', 'marketRates', 'ratesToday', 'ratesYesterday', 'ratesTomorrow',
            'metals', 'metalsMeta', 'goldPrices', 'silverPrices',
            'cityNews', 'indianMarkets', 'offers', 'weather', 'updates', 'jobs', 'farmings', 'cityRecords'
        );

        // Ensure expected blade variables exist (avoid undefined variable notices)
        $defaults = [
            'gold_24_per_g' => null,
            'gold_24_per_10g_retail' => null,
            'gold_24_per_10g_spot' => null,
            'gold_22_per_g' => null,
            'gold_22_per_10g_retail' => null,
            'gold_22_per_10g_spot' => null,
            'gold_18_per_g' => null,
            'gold_18_per_10g_retail' => null,
            'gold_18_per_10g_spot' => null,
            'silver_per_10g_spot' => null,
            'silver_per_10g_retail' => null,
            'silver_per_kg_spot' => null,
            'silver_per_kg_retail' => null,
        ];

        $viewData = array_merge($viewData, $defaults, is_array($goldPrices) ? $goldPrices : [], is_array($silverPrices) ? $silverPrices : []);

        // Flatten nested gold detail arrays (gold_24, gold_22, gold_18) into blade-friendly names
        foreach (['gold_24', 'gold_22', 'gold_18'] as $gkey) {
            if (!empty($viewData[$gkey]) && is_array($viewData[$gkey])) {
                $viewData["{$gkey}_per_10g_spot"] = $viewData[$gkey]['spot_per_10g'] ?? null;
                $viewData["{$gkey}_per_10g_retail"] = $viewData[$gkey]['final_retail'] ?? null;
            }
        }

        return view('home.index', $viewData);
    }

    public function setCity(Request $request)
    {
        $city = City::find($request->input('city_id'));
        if ($city) {
            $request->session()->put('city_id', $city->id);
            return redirect()->route('home')->with('notice', 'City set to ' . $city->name);
        }
        return redirect()->route('home')->with('alert', 'City not found');
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function fetchAgmarknetRates(City $city): array
    {
        $apiKey = env('DATA_GOV_API_KEY')
            ?: env('AGMARKNET_API_KEY')
            ?: '579b464db66ec23bdd000001c20c0593c63b4ae97757e11d2e3f369e';

        $resourceId = env('DATA_GOV_RESOURCE_ID', '35985678-0d79-46b4-9ed6-6f13308a1d24');
        $baseUrl = sprintf('https://api.data.gov.in/resource/%s', $resourceId);

        $districtCandidates = array_values(array_unique(array_filter([
            ucfirst(strtolower((string) $city->agmarknet_district)),
            ucfirst(strtolower((string) $city->name)),
            $city->agmarknet_district,
            $city->name,
        ], fn ($value) => filled($value))));

        $stateCandidates = array_values(array_unique(array_filter([
            $city->agmarknet_state,
            'Maharashtra',
        ], fn ($value) => filled($value))));

        $dateFrom = now()->subDays(4)->toDateString();
        $dateTo = now()->toDateString();

        foreach ($stateCandidates as $state) {
            foreach ($districtCandidates as $district) {
                try {
                    $query = [
                        'api-key' => $apiKey,
                        'format' => 'json',
                        'filters[State]' => trim((string) $state),
                        'filters[District]' => trim((string) $district),
                        'sort[Market]' => 'desc',
                        'range[Arrival_Date][gte]' => $dateFrom,
                        'range[Arrival_Date][lte]' => $dateTo,
                    ];

                    $response = Http::timeout(12)
                        ->retry(1, 300)
                        ->acceptJson()
                        ->withHeaders([
                            'User-Agent' => 'AajchaOffer/1.0',
                        ])
                        ->get($baseUrl, $query);

                    if (!$response->successful()) {
                        Log::warning('[HomeController] Agmarknet request failed', [
                            'status' => $response->status(),
                            'state' => $state,
                            'district' => $district,
                        ]);
                        continue;
                    }

                    $records = $response->json('records');
                    if (!is_array($records) || empty($records)) {
                        continue;
                    }

                    $normalized = collect($records)
                        ->filter(function ($row) {
                            return filled($row['Market'] ?? null)
                                && filled($row['Commodity'] ?? null)
                                && (
                                    filled($row['Modal_Price'] ?? null)
                                    || filled($row['Min_Price'] ?? null)
                                    || filled($row['Max_Price'] ?? null)
                                );
                        })
                        ->map(function ($row) {
                            return [
                                'State' => $row['State'] ?? null,
                                'District' => $row['District'] ?? null,
                                'Market' => $row['Market'] ?? null,
                                'Commodity' => $row['Commodity'] ?? null,
                                'Variety' => $row['Variety'] ?? null,
                                'Grade' => $row['Grade'] ?? null,
                                'Min_Price' => $row['Min_Price'] ?? null,
                                'Max_Price' => $row['Max_Price'] ?? null,
                                'Modal_Price' => $row['Modal_Price'] ?? null,
                                'Arrival_Date' => $row['Arrival_Date'] ?? null,
                            ];
                        })
                        ->values()
                        ->all();

                    if (!empty($normalized)) {
                        Log::info('[HomeController] Agmarknet data fetched', [
                            'city' => $city->name,
                            'state' => $state,
                            'district' => $district,
                            'records' => count($normalized),
                        ]);

                        return $normalized;
                    }
                } catch (\Throwable $e) {
                    Log::warning('[HomeController] Agmarknet fetch exception: ' . $e->getMessage(), [
                        'city' => $city->name,
                        'state' => $state,
                        'district' => $district,
                    ]);
                }
            }
        }

        return [];
    }

    private function fallbackMarketRates(?City $city): array
    {
        $scoped = Market::query();

        if ($city) {
            $scoped->where(function ($query) use ($city) {
                $query->where('city_id', $city->id);

                if (filled($city->agmarknet_district)) {
                    $query->orWhere('district', 'like', '%' . $city->agmarknet_district . '%');
                }

                $query->orWhere('city', 'like', '%' . $city->name . '%');
            });
        }

        $latestDate = (clone $scoped)->max('price_date');

        if ($latestDate) {
            $latest = \Carbon\Carbon::parse($latestDate);
            $previous = $latest->copy()->subDay()->toDateString();
            $next = $latest->copy()->addDay()->toDateString();

            $ratesToday = (clone $scoped)
                ->whereDate('price_date', $latestDate)
                ->orderByDesc('modal_price')
                ->orderByDesc('updated_at')
                ->limit(50)
                ->get();

            $ratesYesterday = (clone $scoped)
                ->whereDate('price_date', $previous)
                ->orderByDesc('modal_price')
                ->orderByDesc('updated_at')
                ->limit(50)
                ->get();

            $ratesTomorrow = (clone $scoped)
                ->whereDate('price_date', $next)
                ->orderByDesc('modal_price')
                ->orderByDesc('updated_at')
                ->limit(50)
                ->get();

            if ($ratesToday->isNotEmpty()) {
                return [$ratesToday, $ratesYesterday, $ratesTomorrow];
            }
        }

        $ratesToday = (clone $scoped)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        if ($ratesToday->isEmpty() && $city) {
            $ratesToday = Market::query()
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->limit(50)
                ->get();
        }

        return [$ratesToday, collect(), collect()];
    }

    private function fetchMetals(): ?array
    {
        $cacheKey = 'metals_inr_per_g';

        try {
            $cached = Cache::get($cacheKey);
            if (
                is_array($cached)
                && is_numeric($cached['gold'] ?? null)
                && ($cached['gold'] ?? 0) > 1000
                && is_numeric($cached['silver'] ?? null)
                && ($cached['silver'] ?? 0) > 50
            ) {
                return $cached;
            }

            // ── Tier 1: Scrape ibja.co for Gold 999 per gram (INR, same source as PhonePe/Jar/Groww)
            $goldPerG  = $this->fetchIbjaGold();

            // ── Tier 2: Scrape ibjarates.com for Silver 999 per kg → per gram
            $silverPerG = $this->fetchIbjaSilver();

            // ── ENV overrides (manual correction if IBJA is down)
            if (!$goldPerG && filled(env('METALS_GOLD_PER_G'))) {
                $goldPerG = (float) env('METALS_GOLD_PER_G');
            }
            if (!$silverPerG && filled(env('METALS_SILVER_PER_G'))) {
                $silverPerG = (float) env('METALS_SILVER_PER_G');
            }

            if (!$goldPerG || !$silverPerG) {
                Log::warning('[HomeController] Metals: IBJA scrape failed, gold=' . $goldPerG . ' silver=' . $silverPerG);
                return null;
            }

            $result = [
                'gold'   => round((float) $goldPerG, 2),
                'silver' => round((float) $silverPerG, 2),
                '_meta'  => [
                    'source'     => 'ibja.co',
                    'usd_to_inr' => null,
                    'fetched_at' => now()->toDateTimeString(),
                ],
            ];

            Cache::put($cacheKey, $result, now()->addMinutes(10));
            Log::info('[HomeController] Metals from IBJA: gold=' . $result['gold'] . '/g silver=' . $result['silver'] . '/g');

            return $result;
        } catch (\Throwable $e) {
            Log::warning('[HomeController] Metals fetch exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Scrape IBJA rates for Gold 999 (24K equivalent) per gram in INR.
     * Primary source: ibjarates.com <span id="lblGold999_PM">147889</span> (per 10g)
     * Fallback source: ibja.co <span id="lblFineGold999">₹ 14789</span> (per gram)
     */
    private function fetchIbjaGold(): ?float
    {
        try {
            // Primary source: ibjarates.com (same table family used for silver)
            $response = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AajchaOffer/1.0)'])
                ->get('https://ibjarates.com/index.aspx');

            if ($response->successful()) {
                $html = $response->body();

                // PM rate per 10g: id="lblGold999_PM">147889
                if (preg_match('/id="lblGold999_PM"[^>]*>\s*([\d,]+)/u', $html, $m)) {
                    $per10g = (float) str_replace(',', '', $m[1]);
                    if ($per10g > 10000) {
                        return round($per10g / 10, 2);
                    }
                }

                // AM fallback per 10g
                if (preg_match('/id="lblGold999_AM"[^>]*>\s*([\d,]+)/u', $html, $m)) {
                    $per10g = (float) str_replace(',', '', $m[1]);
                    if ($per10g > 10000) {
                        return round($per10g / 10, 2);
                    }
                }
            }

            // Secondary fallback: ibja.co direct per gram tag
            $fallback = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AajchaOffer/1.0)'])
                ->get('https://ibja.co/');

            if (!$fallback->successful()) {
                return null;
            }

            $html = $fallback->body();

            // Match: id="lblFineGold999">₹ 14789
            if (preg_match('/id="lblFineGold999"[^>]*>[^₹<]*₹\s*([\d,]+)/u', $html, $m)) {
                $val = (float) str_replace(',', '', $m[1]);
                if ($val > 1000) {
                    return $val;
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::warning('[HomeController] IBJA gold scrape failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Scrape ibjarates.com for Silver 999 PM per kg in INR, returns per gram.
     * Source: <span id="lblSilver999_PM">229873</span>
     */
    private function fetchIbjaSilver(): ?float
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AajchaOffer/1.0)'])
                ->get('https://ibjarates.com/index.aspx');

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            // PM rate: id="lblSilver999_PM">229873
            if (preg_match('/id="lblSilver999_PM"[^>]*>\s*([\d,]+)/u', $html, $m)) {
                $perKg = (float) str_replace(',', '', $m[1]);
                if ($perKg > 10000) {
                    return round($perKg / 1000, 4); // convert to per gram
                }
            }

            // Fallback: AM rate
            if (preg_match('/id="Silver999_AM"[^>]*>\s*([\d,]+)/u', $html, $m)) {
                $perKg = (float) str_replace(',', '', $m[1]);
                if ($perKg > 10000) {
                    return round($perKg / 1000, 4);
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::warning('[HomeController] IBJA silver scrape failed: ' . $e->getMessage());
            return null;
        }
    }

    private function fetchPriceFromGoldApi(string $symbol): ?float
    {
        $apiKey = env('GOLD_API_KEY');
        $url = 'https://api.gold-api.com/price/' . $symbol;
        if (filled($apiKey)) {
            $url .= '?api_key=' . urlencode((string) $apiKey);
        }

        try {
            $response = Http::timeout(6)
                ->retry(1, 300)
                ->acceptJson()
                ->withHeaders(['User-Agent' => 'AajchaOffer/1.0'])
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $parsed = $response->json();
            if (!is_array($parsed)) {
                return null;
            }

            $price = $parsed['price']
                ?? $parsed['value']
                ?? $parsed['result']
                ?? ($parsed['data']['price'] ?? null)
                ?? ($parsed['rates'][$symbol] ?? null);

            return is_numeric($price) ? (float) $price : null;
        } catch (\Throwable $e) {
            Log::warning('[HomeController] gold-api fetch failed for ' . $symbol . ': ' . $e->getMessage());
            return null;
        }
    }

    private function fetchUsdToInr(): ?float
    {
        try {
            $latest = Http::timeout(6)
                ->retry(1, 300)
                ->acceptJson()
                ->withHeaders(['User-Agent' => 'AajchaOffer/1.0'])
                ->get('https://api.exchangerate.host/latest', [
                    'base' => 'USD',
                    'symbols' => 'INR',
                ]);

            if ($latest->successful()) {
                $inr = $latest->json('rates.INR');
                if (is_numeric($inr)) {
                    return (float) $inr;
                }
            }

            foreach (['https://open.er-api.com/v6/latest/USD', 'https://api.exchangerate-api.com/v4/latest/USD'] as $url) {
                $fallback = Http::timeout(6)->retry(1, 300)->acceptJson()
                    ->withHeaders(['User-Agent' => 'AajchaOffer/1.0'])->get($url);
                if ($fallback->successful()) {
                    $inr = $fallback->json('rates.INR');
                    if (is_numeric($inr)) return (float) $inr;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[HomeController] USD/INR fetch failed: ' . $e->getMessage());
        }

        return null;
    }

    private function fetchAltGoldprice(): ?array
    {
        try {
            $response = Http::timeout(6)
                ->retry(1, 300)
                ->acceptJson()
                ->withHeaders(['User-Agent' => 'AajchaOffer/1.0'])
                ->get('https://data-asg.goldprice.org/dbXRates/USD');

            if (!$response->successful()) {
                return null;
            }

            $item = $response->json('items.0');
            if (!is_array($item)) {
                return null;
            }

            $xau = $item['xauPrice'] ?? $item['xau'] ?? $item['XAU'] ?? null;
            $xag = $item['xagPrice'] ?? $item['xag'] ?? $item['XAG'] ?? null;

            if (!is_numeric($xau) && !is_numeric($xag)) {
                return null;
            }

            return [
                'xau' => is_numeric($xau) ? (float) $xau : null,
                'xag' => is_numeric($xag) ? (float) $xag : null,
            ];
        } catch (\Throwable $e) {
            Log::warning('[HomeController] alternate goldprice fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    private function fetchCityNews(string $cityName, int $limit = 5): array
    {
        $cacheKey = 'city_news_' . md5(strtolower($cityName));
        
        // Try cache first (5 minute TTL)
        $cached = Cache::get($cacheKey);
        if (!empty($cached) && is_array($cached)) {
            Log::info("NewsClient: news cache hit for {$cityName}");
            return $cached;
        }

        // Fetch from Google News RSS
        $news = NewsClient::fetchCityNews($cityName, 'India', $limit);
        
        // Cache result for 5 minutes
        if (!empty($news)) {
            Cache::put($cacheKey, $news, now()->addMinutes(5));
        }
        
        return $news;
    }

    private function fetchWeather(float $lat, float $lng): ?array
    {
        // Implement weather API call
        return null;
    }

    private function computeGoldPrices(float $gold24): array
    {
        $wastagePct         = (float)(env('METALS_WASTAGE_PERCENT', 0.5));
        $makingType         = env('METALS_MAKING_TYPE', 'fixed');
        $makingPer10g       = (float)(env('METALS_MAKING_PER_10G', 800));
        $makingPercent      = (float)(env('METALS_MAKING_PERCENT', 2.0));
        $dealerPremiumPct   = (float)(env('METALS_DEALER_PREMIUM_PERCENT', 0));
        $gstPct             = (float)(env('METALS_GST_PERCENT', 3.0));
        $roundNearest       = (int)(env('METALS_ROUND_NEAREST', 10));

        $computeRetail = function (float $spotPerG, int $karat) use (
            $wastagePct, $makingType, $makingPer10g, $makingPercent,
            $dealerPremiumPct, $gstPct, $roundNearest
        ): array {
            $purityRatio         = $karat / 24.0;
            $metalValue10g       = $spotPerG * $purityRatio * 10.0;
            $metalAfterWastage   = $metalValue10g * (1.0 + $wastagePct / 100.0);
            $makingCharge        = ($makingType === 'percent')
                ? $metalAfterWastage * ($makingPercent / 100.0)
                : $makingPer10g;
            $premium = ($metalAfterWastage + $makingCharge) * ($dealerPremiumPct / 100.0);
            $taxable = $metalAfterWastage + $makingCharge + $premium;
            $gst = $taxable * ($gstPct / 100.0);
            $final = $taxable + $gst;
            $rn = $roundNearest > 0 ? $roundNearest : 1;
            $finalRounded = round($final / $rn) * $rn;
            return [
                'spot_per_10g'       => round($metalValue10g),
                'metal_after_wastage'=> round($metalAfterWastage),
                'making_charge'      => round($makingCharge),
                'premium'            => round($premium),
                'taxable_value'      => round($taxable),
                'gst'                => round($gst),
                'final_retail'       => (int)$finalRounded,
            ];
        };

        $gold22f = $gold24 * (22.0 / 24.0);
        $gold18f = $gold24 * (18.0 / 24.0);

        return [
            'gold_24_per_g'          => round($gold24),
            'gold_22_per_g'          => round($gold22f),
            'gold_18_per_g'          => round($gold18f),
            'gold_24'                => $computeRetail($gold24, 24),
            'gold_22'                => $computeRetail($gold22f, 22),
            'gold_18'                => $computeRetail($gold18f, 18),
        ];
    }

    private function computeSilverPrices(float $silverG): array
    {
        $markupPct   = (float)(env('METALS_MARKUP_PERCENT', 0));
        $making10g   = (float)(env('METALS_MAKING_PER_10G', 0));
        $taxPct      = (float)(env('METALS_TAX_PERCENT', 0));

        $spot10g   = $silverG * 10.0;
        $preTax    = $spot10g * (1.0 + $markupPct / 100.0) + $making10g;
        $tax       = $preTax * ($taxPct / 100.0);
        $retail10g = round($preTax + $tax);

        return [
            'silver_per_10g_spot'   => round($spot10g),
            'silver_per_10g_retail' => $retail10g,
            'silver_per_kg_spot'    => round($silverG * 1000.0),
            'silver_per_kg_retail'  => $retail10g * 100,
        ];
    }
}
