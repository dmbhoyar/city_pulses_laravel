<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Shop;
use App\Models\Update;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $offers = Update::query()
            ->with(['city:id,name', 'shop:id,name,city_id,user_id'])
            ->where('update_type', 'offer')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . strtolower($q) . '%';
                $query->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(content) LIKE ?', [$like]);
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $cities = City::query()->orderBy('name')->get(['id', 'name']);
        $shops = Shop::query()->with('user:id,email')->orderBy('name')->get(['id', 'name', 'city_id', 'user_id']);

        return view('admin.offers.index', compact('offers', 'cities', 'shops', 'q'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'source_url' => 'nullable|url|max:255',
            'published_at' => 'nullable|date',
            'city_id' => 'nullable|exists:cities,id',
            'shop_id' => 'nullable|exists:shops,id',
        ]);

        $validated['update_type'] = 'offer';
        if (!empty($validated['shop_id']) && empty($validated['city_id'])) {
            $validated['city_id'] = Shop::query()->whereKey($validated['shop_id'])->value('city_id');
        }

        Update::create($validated);

        return redirect()->route('admin.offers.index')->with('notice', 'Offer created successfully.');
    }

    public function update(Request $request, Update $offer)
    {
        if ($offer->update_type !== 'offer') {
            return redirect()->route('admin.offers.index')->with('alert', 'Only offer updates can be edited here.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'source_url' => 'nullable|url|max:255',
            'published_at' => 'nullable|date',
            'city_id' => 'nullable|exists:cities,id',
            'shop_id' => 'nullable|exists:shops,id',
        ]);

        $validated['update_type'] = 'offer';
        if (!empty($validated['shop_id']) && empty($validated['city_id'])) {
            $validated['city_id'] = Shop::query()->whereKey($validated['shop_id'])->value('city_id');
        }

        $offer->update($validated);

        return redirect()->route('admin.offers.index')->with('notice', 'Offer updated successfully.');
    }

    public function destroy(Update $offer)
    {
        if ($offer->update_type !== 'offer') {
            return redirect()->route('admin.offers.index')->with('alert', 'Only offer updates can be deleted here.');
        }

        $offer->delete();

        return redirect()->route('admin.offers.index')->with('notice', 'Offer deleted successfully.');
    }
}
