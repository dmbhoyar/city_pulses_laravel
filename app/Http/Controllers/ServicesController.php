<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\ServiceReview;
use App\Models\Shop;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $cityId = $request->session()->get('city_id');
        $city = $cityId ? City::find($cityId) : null;

        if (!$city) {
            $city = City::query()
                ->whereRaw('LOWER(name) = ?', ['washim'])
                ->first();

            if (!$city) {
                $city = City::query()->orderBy('id')->first();
            }

            if ($city) {
                $request->session()->put('city_id', $city->id);
            }
        }

        $baseQuery = Shop::query()
            ->with(['user', 'city'])
            ->whereHas('user', fn ($q) => $q->whereIn('role', ['service_provider', 'serviceprovider', 'service-provider']))
            ->whereHas('user.subscriptions', function ($q) {
                $q->where('status', 'active')
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '>', now());
            });

        if ($city) {
            $baseQuery->whereJsonContains('page_config->service_cities', (int) $city->id);
        }

        $services = (clone $baseQuery)
            ->latest('id')
            ->paginate(18);

        return view('services.index', compact('services', 'city'));
    }

    public function show(string $publicSlug)
    {
        $slug = Str::slug($publicSlug);

        $service = Shop::query()
            ->with(['user', 'city'])
            ->latest('id')
            ->get()
            ->first(fn (Shop $shop) => $shop->public_page_slug === $slug);

        abort_if(!$service, 404);

        $isActiveSubscription = Subscription::query()
            ->where('user_id', $service->user_id)
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->exists();

        abort_if(!$isActiveSubscription, 404);

        $cfg = is_array($service->page_config) ? $service->page_config : [];
        $serviceCityIds = is_array($cfg['service_cities'] ?? null) ? $cfg['service_cities'] : [];
        $serviceCities = empty($serviceCityIds)
            ? collect()
            : City::query()->whereIn('id', $serviceCityIds)->orderBy('name')->pluck('name');

        $reviews = ServiceReview::query()
            ->where('shop_id', $service->id)
            ->with('user')
            ->latest('id')
            ->get();

        $myReview = auth()->check()
            ? ServiceReview::query()
                ->where('shop_id', $service->id)
                ->where('user_id', auth()->id())
                ->first()
            : null;

        $reviewStats = [
            'count' => $reviews->count(),
            'average' => $reviews->count() ? round((float) $reviews->avg('rating'), 1) : 0,
        ];

        return view('services.show', compact('service', 'reviews', 'myReview', 'reviewStats', 'serviceCities'));
    }

    public function storeReview(Request $request, string $publicSlug)
    {
        $slug = Str::slug($publicSlug);

        $service = Shop::query()
            ->latest('id')
            ->get()
            ->first(fn (Shop $shop) => $shop->public_page_slug === $slug);

        abort_if(!$service, 404);

        $isActiveSubscription = Subscription::query()
            ->where('user_id', $service->user_id)
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->exists();

        abort_if(!$isActiveSubscription, 404);

        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ];

        if (!auth()->check()) {
            $rules['reviewer_name'] = 'required|string|max:120';
            $rules['reviewer_email'] = 'required|email|max:190';
        }

        $validated = $request->validate($rules);

        if (auth()->check()) {
            $authUser = auth()->user();
            ServiceReview::query()->updateOrCreate(
                [
                    'shop_id' => $service->id,
                    'user_id' => auth()->id(),
                ],
                [
                    'reviewer_name' => trim((string) ($authUser?->full_name ?? '')),
                    'reviewer_email' => trim((string) ($authUser?->email ?? '')),
                    'rating' => (int) $validated['rating'],
                    'review' => trim((string) ($validated['review'] ?? '')),
                ]
            );
        } else {
            ServiceReview::query()->create([
                'shop_id' => $service->id,
                'user_id' => null,
                'reviewer_name' => trim((string) ($validated['reviewer_name'] ?? '')),
                'reviewer_email' => trim((string) ($validated['reviewer_email'] ?? '')),
                'rating' => (int) $validated['rating'],
                'review' => trim((string) ($validated['review'] ?? '')),
            ]);
        }

        return back()->with('notice', 'Your rating and review has been saved.');
    }
}
