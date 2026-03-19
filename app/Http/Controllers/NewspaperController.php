<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Update;
use Illuminate\Http\Request;

class NewspaperController extends Controller
{
    public function show(Request $request)
    {
        $city = $request->filled('city_id') ? City::find($request->input('city_id')) : null;
        $updates = $city
            ? Update::where('city_id', $city->id)->orderByDesc('published_at')->limit(50)->get()
            : Update::orderByDesc('published_at')->limit(50)->get();
        return view('newspaper.show', compact('city', 'updates'));
    }
}
