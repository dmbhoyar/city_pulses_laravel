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

class UpdatesController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::query()->orderBy('name')->get(['id', 'name']);

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

        $marketRows = Market::query()
            ->with('city_rel')
            ->when($selectedCity, fn ($query) => $query->where('city_id', $selectedCity->id))
            ->orderByDesc('price_date')
            ->latest('id')
            ->limit(8)
            ->get();

        $cityNews = [];
        if ($selectedCity) {
            $cityNews = NewsClient::fetchCityNews($selectedCity->name, 'India', 8);
        }

        $marketIndices = IndianMarketsClient::fetchIndices();

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
            'marketIndices'
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
