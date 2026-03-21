<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $cities = City::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . strtolower($q) . '%';
                $query->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(agmarknet_district) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(agmarknet_market) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(agmarknet_state) LIKE ?', [$like]);
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.cities.index', compact('cities', 'q'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:cities,name',
            'agmarknet_district' => 'nullable|string|max:120',
            'agmarknet_market' => 'nullable|string|max:120',
            'agmarknet_state' => 'nullable|string|max:120',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        City::create($validated);

        return redirect()->route('admin.cities.index')->with('notice', 'City added successfully.');
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:cities,name,' . $city->id,
            'agmarknet_district' => 'nullable|string|max:120',
            'agmarknet_market' => 'nullable|string|max:120',
            'agmarknet_state' => 'nullable|string|max:120',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $city->update($validated);

        return redirect()->route('admin.cities.index')->with('notice', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('admin.cities.index')->with('notice', 'City deleted successfully.');
    }
}
