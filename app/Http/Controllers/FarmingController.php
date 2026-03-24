<?php

namespace App\Http\Controllers;

use App\Models\Farming;
use App\Models\City;
use App\Models\Job;
use App\Models\Market;
use App\Services\NewsClient;
use App\Services\TextTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FarmingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $selectedCityId = null;
        if ($request->filled('city_id')) {
            $selectedCityId = (int) $request->input('city_id');
            $request->session()->put('city_id', $selectedCityId);
        } elseif ($request->session()->has('city_id')) {
            $selectedCityId = (int) $request->session()->get('city_id');
        }

        $q = trim((string) $request->input('q', ''));

        $farmingQuery = Farming::query()
            ->with('city')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', "%{$q}%")
                        ->orWhere('author_name', 'like', "%{$q}%")
                        ->orWhere('content', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at');

        if ($selectedCityId) {
            $farmingQuery->where('city_id', $selectedCityId);
        }

        $farmings = $farmingQuery->paginate(12)->withQueryString();

        $cities = City::query()->orderBy('name')->get(['id', 'name', 'latitude', 'longitude', 'agmarknet_district', 'agmarknet_state']);
        $selectedCity = $selectedCityId ? $cities->firstWhere('id', $selectedCityId) : null;
        $selectedCityName = $selectedCity?->name;

        $totalArticles = Farming::query()->count();
        $cityArticles = $selectedCityId
            ? Farming::query()->where('city_id', $selectedCityId)->count()
            : $totalArticles;

        $marketRows = Market::query()
            ->when($selectedCityId, fn ($query) => $query->where('city_id', $selectedCityId))
            ->whereNotNull('commodity')
            ->orderByDesc('price_date')
            ->orderByDesc('created_at')
            ->limit(18)
            ->get();

        $agriJobs = Job::query()
            ->with('city')
            ->when($selectedCityId, fn ($query) => $query->where('city_id', $selectedCityId))
            ->where(function ($query) {
                $query->where('title', 'like', '%farm%')
                    ->orWhere('title', 'like', '%agri%')
                    ->orWhere('description', 'like', '%farm%')
                    ->orWhere('description', 'like', '%agri%')
                    ->orWhere('category', 'like', '%farm%')
                    ->orWhere('category', 'like', '%agri%');
            })
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $cityNews = [];
        if ($selectedCityName) {
            $cityNews = NewsClient::fetchCityNews("{$selectedCityName} farming agriculture mandi", 'India', 6);
        }

        $topCommodities = $this->buildCommoditySummary($marketRows);

        $locale = app()->getLocale();
        if ($locale !== 'en') {
            $farmings->getCollection()->transform(function ($item) use ($locale) {
                if (is_string($item->title) && trim($item->title) !== '') {
                    $item->title = TextTranslationService::translate($item->title, $locale);
                }

                if (is_string($item->content) && trim($item->content) !== '') {
                    $item->content = TextTranslationService::translate($item->content, $locale);
                }

                return $item;
            });

            $agriJobs->transform(function ($item) use ($locale) {
                if (is_string($item->title) && trim($item->title) !== '') {
                    $item->title = TextTranslationService::translate($item->title, $locale);
                }

                if (is_string($item->description) && trim($item->description) !== '') {
                    $item->description = TextTranslationService::translate($item->description, $locale);
                }

                if (is_string($item->category) && trim($item->category) !== '') {
                    $item->category = TextTranslationService::translate($item->category, $locale);
                }

                return $item;
            });

            $marketRows->transform(function ($item) use ($locale) {
                if (is_string($item->commodity) && trim($item->commodity) !== '') {
                    $item->commodity = TextTranslationService::translate($item->commodity, $locale);
                }

                if (is_string($item->district) && trim($item->district) !== '') {
                    $item->district = TextTranslationService::translate($item->district, $locale);
                }

                return $item;
            });

            foreach ($cityNews as $index => $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (isset($item['title']) && is_string($item['title']) && trim($item['title']) !== '') {
                    $cityNews[$index]['title'] = TextTranslationService::translate($item['title'], $locale);
                }

                if (isset($item['source']) && is_string($item['source']) && trim($item['source']) !== '') {
                    $cityNews[$index]['source'] = TextTranslationService::translate($item['source'], $locale);
                }
            }

            $topCommodities = $topCommodities->map(function (array $row) use ($locale) {
                if (isset($row['name']) && is_string($row['name']) && trim($row['name']) !== '') {
                    $row['name'] = TextTranslationService::translate($row['name'], $locale);
                }

                return $row;
            });
        }

        return view('farming.index', compact(
            'farmings',
            'q',
            'cities',
            'selectedCityId',
            'selectedCityName',
            'selectedCity',
            'totalArticles',
            'cityArticles',
            'marketRows',
            'agriJobs',
            'cityNews',
            'topCommodities'
        ));
    }

    public function show(Farming $farming)
    {
        return view('farming.show', compact('farming'));
    }

    public function liveMandi(Request $request)
    {
        $validated = $request->validate([
            'state' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'limit' => 'nullable|integer|min:1|max:30',
        ]);

        $state = trim((string) ($validated['state'] ?? 'Maharashtra')) ?: 'Maharashtra';
        $district = trim((string) ($validated['district'] ?? ''));
        $limit = (int) ($validated['limit'] ?? 14);

        $resourceId = env('DATA_GOV_RESOURCE_ID', '35985678-0d79-46b4-9ed6-6f13308a1d24');
        $apiKey = env('DATA_GOV_API_KEY', '579b464db66ec23bdd000001c20c0593c63b4ae97757e11d2e3f369e');
        $baseUrl = sprintf('https://api.data.gov.in/resource/%s', $resourceId);

        $cacheKey = 'farming_live_mandi_' . md5(strtolower($state . '|' . $district . '|' . $limit));
        $staleKey = $cacheKey . '_stale';

        try {
            $records = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($baseUrl, $apiKey, $state, $district, $limit, $staleKey) {
                $params = [
                    'api-key' => $apiKey,
                    'format' => 'json',
                    'limit' => $limit,
                    'filters[State]' => $state,
                ];

                if ($district !== '') {
                    $params['filters[District]'] = $district;
                }

                $response = Http::acceptJson()->timeout(12)->retry(2, 300)->get($baseUrl, $params);

                if (!$response->successful()) {
                    throw new \RuntimeException('Agmarknet request failed with status ' . $response->status());
                }

                $json = $response->json();
                $items = array_values(array_filter($json['records'] ?? [], fn ($row) => is_array($row)));

                if (!empty($items)) {
                    Cache::put($staleKey, $items, now()->addHours(12));
                }

                return $items;
            });

            return response()->json([
                'records' => $records,
                'source' => 'agmarknet',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Farming live mandi fetch failed', [
                'state' => $state,
                'district' => $district,
                'error' => $e->getMessage(),
            ]);

            $stale = Cache::get($staleKey, []);
            if (!empty($stale)) {
                return response()->json([
                    'records' => $stale,
                    'source' => 'stale',
                ]);
            }

            $fallback = Market::query()
                ->whereNotNull('commodity')
                ->when($district !== '', function ($query) use ($district) {
                    $query->where(function ($inner) use ($district) {
                        $inner->where('district', 'like', '%' . $district . '%')
                            ->orWhere('city', 'like', '%' . $district . '%');
                    });
                })
                ->orderByDesc('price_date')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->map(function ($row) {
                    return [
                        'Commodity' => $row->commodity,
                        'Market' => $row->city,
                        'District' => $row->district,
                        'Modal_Price' => $row->modal_price,
                        'Min_Price' => $row->min_price,
                        'Max_Price' => $row->max_price,
                        'Arrival_Date' => optional($row->price_date)->format('d/m/Y')
                            ?: optional($row->updated_at)->format('d/m/Y'),
                    ];
                })
                ->values();

            return response()->json([
                'records' => $fallback,
                'source' => 'database',
            ]);
        }
    }

    public function create()
    {
        $farming = new Farming();
        $cities = City::orderBy('name')->get();
        return view('farming.create', compact('farming', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author_name' => 'required|string|max:120',
            'content'     => 'required|string',
            'city_id'     => 'required|exists:cities,id',
        ]);

        $validated['content'] = $this->sanitizeRichContent((string) $validated['content']);

        $farming = Farming::create($validated);
        return redirect()->route('farming.show', $farming)->with('notice', 'Blog published successfully.');
    }

    public function edit(Farming $farming)
    {
        $this->ensureSuperadmin();

        $cities = City::orderBy('name')->get();
        return view('farming.edit', compact('farming', 'cities'));
    }

    public function update(Request $request, Farming $farming)
    {
        $this->ensureSuperadmin();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author_name' => 'required|string|max:120',
            'content'     => 'required|string',
            'city_id'     => 'required|exists:cities,id',
        ]);

        $validated['content'] = $this->sanitizeRichContent((string) $validated['content']);

        $farming->update($validated);
        return redirect()->route('farming.show', $farming)->with('notice', 'Farming record updated.');
    }

    public function destroy(Farming $farming)
    {
        $this->ensureSuperadmin();

        $farming->delete();
        return redirect()->route('farming.index')->with('notice', 'Farming record removed.');
    }

    private function ensureSuperadmin(): void
    {
        $user = auth()->user();

        if (!$user || !$user->isSuperadmin()) {
            abort(403, 'Only superadmin can manage farming records from this page.');
        }
    }

    private function buildCommoditySummary(Collection $rows): Collection
    {
        return $rows
            ->filter(fn ($row) => !empty($row->commodity))
            ->groupBy(fn ($row) => strtolower((string) $row->commodity))
            ->map(function (Collection $group, string $key) {
                $latest = $group->first();

                return [
                    'name' => ucwords($key),
                    'modal_price' => $latest?->modal_price,
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->take(6);
    }

    private function sanitizeRichContent(string $html): string
    {
        $content = trim($html);

        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content) ?? '';
        $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content) ?? $content;
        $content = preg_replace('/on\w+\s*=\s*"[^"]*"/i', '', $content) ?? $content;
        $content = preg_replace('/on\w+\s*=\s*\'[^\']*\'/i', '', $content) ?? $content;
        $content = preg_replace('/on\w+\s*=\s*[^\s>]+/i', '', $content) ?? $content;
        $content = preg_replace('/javascript\s*:/i', '', $content) ?? $content;

        $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><a><img><span><div>';
        $content = strip_tags($content, $allowed);

        if ($content === '') {
            return '<p></p>';
        }

        return $content;
    }
}
