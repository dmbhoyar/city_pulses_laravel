<?php

namespace App\Http\Controllers;

use App\Models\Farming;
use App\Models\City;
use Illuminate\Http\Request;

class FarmingController extends Controller
{
    public function index()
    {
        $farmings = Farming::orderByDesc('created_at')->get();
        return view('farming.index', compact('farmings'));
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
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        $farming = Farming::create($validated);
        return redirect()->route('farming.show', $farming)->with('notice', 'Farming record created.');
    }

    public function edit(Farming $farming)
    {
        $cities = City::orderBy('name')->get();
        return view('farming.edit', compact('farming', 'cities'));
    }

    public function update(Request $request, Farming $farming)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        $farming->update($validated);
        return redirect()->route('farming.show', $farming)->with('notice', 'Farming record updated.');
    }

    public function destroy(Farming $farming)
    {
        $farming->delete();
        return redirect()->route('farming.index')->with('notice', 'Farming record removed.');
    }
}
