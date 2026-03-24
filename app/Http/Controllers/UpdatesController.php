<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Job;
use App\Models\Market;
use App\Models\Update;
use App\Services\IndianMarketsClient;
use App\Services\NewsClient;
use App\Services\TextTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdatesController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::query()->orderBy('name')->get(['id', 'name', 'agmarknet_district', 'agmarknet_state']);

        $selectedCity = null;
        if ($request->filled('city_id')) {
            $selectedCity = $cities->firstWhere('id', (int) $request->input('city_id'));
        } elseif ($request->session()->has('city_id')) {
            $selectedCity = $cities->firstWhere('id', (int) $request->session()->get('city_id'));
        }

        $updates = Update::query()
            ->with(['city', 'shop'])
            ->when($selectedCity, fn ($query) => $query->where('city_id', $selectedCity->id))
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(30)
            ->get();

        $eventUpdates = Update::query()
            ->with('city')
            ->events()
            ->when($selectedCity, fn ($query) => $query->where('city_id', $selectedCity->id))
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(8)
            ->get();

        $offerUpdates = Update::query()
            ->with('city')
            ->offers()
            ->when($selectedCity, fn ($query) => $query->where('city_id', $selectedCity->id))
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(6)
            ->get();

        $jobs = Job::query()
            ->with('city')
            ->when($selectedCity, fn ($query) => $query->where('city_id', $selectedCity->id))
            ->latest()
            ->limit(12)
            ->get();

        $marketRows = collect();
        if ($selectedCity) {
            $agmarkRows = $this->fetchAgmarknetRates($selectedCity);
            if (!empty($agmarkRows)) {
                $marketRows = $this->buildMarketRowsFromAgmarknet($agmarkRows, 8);
            }
        }

        if ($marketRows->isEmpty()) {
            $marketRows = Market::query()
                ->with('city_rel')
                ->when($selectedCity, fn ($query) => $query->where('city_id', $selectedCity->id))
                ->orderByDesc('price_date')
                ->latest('id')
                ->limit(8)
                ->get();
        }

        $cityNews = [];
        if ($selectedCity) {
            $cityNews = NewsClient::fetchCityNews($selectedCity->name, 'India', 8);
        }

        $marketIndices = IndianMarketsClient::fetchIndices();
        $liveBarMetals = $this->buildLiveBarMetals();

        $locale = app()->getLocale();
        if ($locale !== 'en') {
            $this->translateUpdateCollection($updates, $locale);
            $this->translateUpdateCollection($eventUpdates, $locale);
            $this->translateUpdateCollection($offerUpdates, $locale);
            $this->translateJobCollection($jobs, $locale);
            $this->translateCityNewsItems($cityNews, $locale);
            $this->translateMarketCollection($marketRows, $locale);
        }

        return view('updates.index', compact(
            'updates',
            'cities',
            'selectedCity',
            'eventUpdates',
            'offerUpdates',
            'jobs',
            'marketRows',
            'cityNews',
            'marketIndices',
            'liveBarMetals'
        ));
    }

    public function show(Update $update)
    {
        return view('updates.show', compact('update'));
    }

    public function create()
    {
        $update = new Update();
        $cities = City::orderBy('name')->get();
        return view('updates.create', compact('update', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        $update = Update::create($validated);
        return redirect()->route('updates.show', $update)->with('notice', 'Update created.');
    }

    public function edit(Update $update)
    {
        $cities = City::orderBy('name')->get();
        return view('updates.edit', compact('update', 'cities'));
    }

    public function update(Request $request, Update $update)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        $update->update($validated);
        return redirect()->route('updates.show', $update)->with('notice', 'Update updated.');
    }

    public function destroy(Update $update)
    {
        $update->delete();
        return redirect()->route('updates.index')->with('notice', 'Update removed.');
    }

    private function fetchAgmarknetRates(City $city): array
    {
        $cacheKey = 'updates_agmarknet_rates_city_' . $city->id;
        $staleCacheKey = 'updates_agmarknet_rates_city_stale_' . $city->id;
        $apiKey = env('DATA_GOV_API_KEY')
            ?: env('AGMARKNET_API_KEY')
            ?: '579b464db66ec23bdd000001c20c0593c63b4ae97757e11d2e3f369e';

        $resourceId = env('DATA_GOV_RESOURCE_ID', '35985678-0d79-46b4-9ed6-6f13308a1d24');
        $baseUrl = sprintf('https://api.data.gov.in/resource/%s', $resourceId);

        $districtCandidates = [];
        if (filled($city->agmarknet_district)) {
            $districtCandidates = array_values(array_unique(array_filter([
                ucfirst(strtolower((string) $city->agmarknet_district)),
                (string) $city->agmarknet_district,
            ], fn ($value) => filled($value))));
        } elseif (filled($city->name)) {
            $districtCandidates = array_values(array_unique(array_filter([
                ucfirst(strtolower((string) $city->name)),
                (string) $city->name,
            ], fn ($value) => filled($value))));
        }

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
                        ->retry(2, 500)
                        ->acceptJson()
                        ->withHeaders(['User-Agent' => 'AajchaOffer/1.0'])
                        ->get($baseUrl, $query);

                    if (!$response->successful()) {
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
                                'Market' => $row['Market'] ?? null,
                                'Commodity' => $row['Commodity'] ?? null,
                                'Variety' => $row['Variety'] ?? null,
                                'Min_Price' => $row['Min_Price'] ?? null,
                                'Max_Price' => $row['Max_Price'] ?? null,
                                'Modal_Price' => $row['Modal_Price'] ?? null,
                                'Arrival_Date' => $row['Arrival_Date'] ?? null,
                            ];
                        })
                        ->values()
                        ->all();

                    if (!empty($normalized)) {
                        Cache::put($cacheKey, $normalized, now()->addMinutes(45));
                        Cache::put($staleCacheKey, $normalized, now()->addDay());
                        return $normalized;
                    }
                } catch (\Throwable $e) {
                    Log::warning('[UpdatesController] Agmarknet fetch exception: ' . $e->getMessage(), [
                        'city' => $city->name,
                        'state' => $state,
                        'district' => $district,
                    ]);
                }
            }
        }

        $cachedRows = Cache::get($cacheKey, []);
        if (is_array($cachedRows) && !empty($cachedRows)) {
            return $cachedRows;
        }

        $staleCachedRows = Cache::get($staleCacheKey, []);
        if (is_array($staleCachedRows) && !empty($staleCachedRows)) {
            return $staleCachedRows;
        }

        return [];
    }

    private function buildMarketRowsFromAgmarknet(array $rows, int $limit = 8): Collection
    {
        return collect($rows)
            ->filter(function ($row) {
                return filled($row['Commodity'] ?? null) && is_numeric($row['Modal_Price'] ?? null);
            })
            ->map(function ($row) {
                return [
                    'commodity' => (string) ($row['Commodity'] ?? ''),
                    'modal_price' => (float) ($row['Modal_Price'] ?? 0),
                    'city' => (string) ($row['Market'] ?? ''),
                    'date' => (string) ($row['Arrival_Date'] ?? ''),
                ];
            })
            ->sortByDesc(function ($row) {
                return optional($this->parseAgmarkDate($row['date']))->timestamp ?? 0;
            })
            ->groupBy('commodity')
            ->map(function ($commodityRows) {
                return $commodityRows->first();
            })
            ->values()
            ->take($limit)
            ->map(function ($row) {
                return (object) [
                    'commodity' => $row['commodity'],
                    'modal_price' => $row['modal_price'],
                    'rate' => $row['modal_price'],
                    'city' => $row['city'],
                    'price_date' => $this->parseAgmarkDate($row['date']),
                    'city_rel' => null,
                ];
            })
            ->values();
    }

    private function parseAgmarkDate(?string $value): ?\Carbon\Carbon
    {
        if (!filled($value)) {
            return null;
        }

        try {
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $value);
            }
            return \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function buildLiveBarMetals(): array
    {
        $cacheKey = 'updates_livebar_metals';
        $cached = Cache::get($cacheKey);
        if (
            is_array($cached)
            && is_numeric($cached['usd_inr'] ?? null)
            && is_numeric($cached['gold_10g'] ?? null)
            && is_numeric($cached['silver_kg'] ?? null)
        ) {
            return $cached;
        }

        $usdInr = $this->fetchUsdToInr() ?? 84.10;
        $goldOz = $this->fetchPriceFromGoldApi('XAU');
        $silverOz = $this->fetchPriceFromGoldApi('XAG');

        if (!$goldOz || !$silverOz) {
            return [
                'usd_inr' => $usdInr,
                'gold_10g' => null,
                'silver_kg' => null,
            ];
        }

        $result = [
            'usd_inr' => round($usdInr, 2),
            'gold_10g' => (int) round(($goldOz / 31.1035) * 10 * $usdInr),
            'silver_kg' => (int) round(($silverOz / 31.1035) * 1000 * $usdInr),
        ];

        Cache::put($cacheKey, $result, now()->addMinutes(10));
        return $result;
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
            Log::warning('[UpdatesController] gold-api fetch failed for ' . $symbol . ': ' . $e->getMessage());
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

            $fallback = Http::timeout(6)
                ->retry(1, 300)
                ->acceptJson()
                ->withHeaders(['User-Agent' => 'AajchaOffer/1.0'])
                ->get('https://api.exchangerate-api.com/v4/latest/USD');

            if ($fallback->successful()) {
                $inr = $fallback->json('rates.INR');
                if (is_numeric($inr)) {
                    return (float) $inr;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[UpdatesController] USD/INR fetch failed: ' . $e->getMessage());
        }

        return null;
    }

    private function translateUpdateCollection(Collection $items, string $locale): void
    {
        $items->transform(function ($item) use ($locale) {
            if (!empty($item->title)) {
                $item->title = TextTranslationService::translate((string) $item->title, $locale);
            }

            if (!empty($item->content)) {
                $item->content = TextTranslationService::translate((string) $item->content, $locale);
            }

            if (!empty($item->update_type)) {
                $item->update_type = TextTranslationService::translate((string) $item->update_type, $locale);
            }

            return $item;
        });
    }

    private function translateJobCollection(Collection $items, string $locale): void
    {
        $items->transform(function ($job) use ($locale) {
            if (!empty($job->title)) {
                $job->title = TextTranslationService::translate((string) $job->title, $locale);
            }

            if (!empty($job->description)) {
                $job->description = TextTranslationService::translate((string) $job->description, $locale);
            }

            if (!empty($job->company)) {
                $job->company = TextTranslationService::translate((string) $job->company, $locale);
            }

            if (!empty($job->category)) {
                $job->category = TextTranslationService::translate((string) $job->category, $locale);
            }

            return $job;
        });
    }

    private function translateCityNewsItems(array &$items, string $locale): void
    {
        foreach ($items as $index => $row) {
            $items[$index]['title'] = TextTranslationService::translate((string) ($row['title'] ?? ''), $locale);
            $items[$index]['source'] = TextTranslationService::translate((string) ($row['source'] ?? ''), $locale);
        }
    }

    private function translateMarketCollection(Collection $items, string $locale): void
    {
        $items->transform(function ($market) use ($locale) {
            if (!empty($market->commodity)) {
                $market->commodity = TextTranslationService::translate((string) $market->commodity, $locale);
            }

            return $market;
        });
    }
}
