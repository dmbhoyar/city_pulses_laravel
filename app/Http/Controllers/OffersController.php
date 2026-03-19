<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Update;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    public function index(Request $request)
    {
        $cityId = $request->session()->get('city_id');
        $city   = $cityId ? City::find($cityId) : null;
        $today  = now()->toDateString();

        if ($city) {
            $offers = Update::where('city_id', $city->id)->offers()->latest()->get();
            $events = Update::where('city_id', $city->id)->events()->whereDate('created_at', $today)->latest()->get();
        } else {
            $offers = Update::offers()->latest()->get();
            $events = Update::events()->whereDate('created_at', $today)->latest()->get();
        }

        return view('offers.index', compact('city', 'offers', 'events', 'today'));
    }
}
