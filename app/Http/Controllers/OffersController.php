<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Update;
use App\Services\TextTranslationService;
use App\Services\CouponApiService;
use App\Models\CouponRedemption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    public function index(Request $request)
    {
        $cityId = (int) $request->session()->get('city_id', 0);
        $city   = $cityId ? City::find($cityId) : null;

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

        $today  = now();
        $todayDate = $today->toDateString();

        $offersQuery = Update::query()
            ->with(['shop.user', 'city'])
            ->offers();

        if ($city) {
            $offersQuery->where(function ($query) use ($city) {
                $query->where('city_id', $city->id)
                    ->orWhereHas('shop', function ($shopQuery) use ($city) {
                        $shopQuery->whereJsonContains('page_config->service_cities', (int) $city->id);
                    });
            });

            $events = Update::where('city_id', $city->id)
                ->events()
                ->whereDate('created_at', $todayDate)
                ->latest()
                ->get();
        } else {
            $events = Update::events()->whereDate('created_at', $todayDate)->latest()->get();
        }

        $offers = $offersQuery->latest()->get();

        $locale = app()->getLocale();
        if ($locale !== 'en') {
            $offers->transform(function ($offer) use ($locale) {
                if (is_string($offer->title) && trim($offer->title) !== '') {
                    $offer->title = TextTranslationService::translate($offer->title, $locale);
                }

                if (is_string($offer->content) && trim($offer->content) !== '') {
                    $offer->content = TextTranslationService::translate($offer->content, $locale);
                }

                return $offer;
            });

            $events->transform(function ($event) use ($locale) {
                if (is_string($event->title) && trim($event->title) !== '') {
                    $event->title = TextTranslationService::translate($event->title, $locale);
                }

                if (is_string($event->content) && trim($event->content) !== '') {
                    $event->content = TextTranslationService::translate($event->content, $locale);
                }

                return $event;
            });
        }

        // Fetch coupons from the database (not API)
        $apiCoupons = \App\Models\Coupon::where(function($q) {
            $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
        })->orderByDesc('id')->get();

        // Fetch user coupon redemptions
        $userRedemptions = [];
        if (Auth::check()) {
            $userRedemptions = CouponRedemption::where('user_id', Auth::id())
                ->orderByDesc('redeemed_at')
                ->get();
        }

        return view('offers.index', compact('city', 'offers', 'events', 'today', 'apiCoupons', 'userRedemptions'));
    }
}
