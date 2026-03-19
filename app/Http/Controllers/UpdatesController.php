<?php

namespace App\Http\Controllers;

use App\Models\Update;
use App\Models\City;
use Illuminate\Http\Request;

class UpdatesController extends Controller
{
    public function index(Request $request)
    {
        $updates = Update::when($request->filled('city_id'), fn($q) => $q->where('city_id', $request->input('city_id')))
            ->orderByDesc('created_at')->get();
        return view('updates.index', compact('updates'));
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
}
