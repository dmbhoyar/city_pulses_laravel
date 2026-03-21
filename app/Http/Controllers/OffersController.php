<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Update;
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

        return view('offers.index', compact('city', 'offers', 'events', 'today'));
    }
}
