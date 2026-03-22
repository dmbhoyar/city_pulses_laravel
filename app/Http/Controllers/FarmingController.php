<?php

namespace App\Http\Controllers;

use App\Models\Farming;
use App\Models\City;
use App\Models\Job;
use App\Models\Market;
use App\Services\NewsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
