<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        $shops = Shop::orderByDesc('created_at')->paginate(20);
        return view('shops.index', compact('shops'));
    }

    public function show(Shop $shop)
    {
        return view('shops.show', compact('shop'));
    }

    public function create()
    {
        $shop = new Shop();
        return view('shops.create', compact('shop'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:500',
            'city_id'     => 'nullable|exists:cities,id',
            'template'    => 'nullable|string|max:100',
        ]);
        $shop = auth()->user()->shops()->create($validated);
        return redirect()->route('shops.show', $shop)->with('notice', 'Shop created.');
    }

    public function edit(Shop $shop)
    {
        $this->authorizeOwner($shop);
        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $this->authorizeOwner($shop);
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:500',
            'city_id'     => 'nullable|exists:cities,id',
            'template'    => 'nullable|string|max:100',
        ]);
        $shop->update($validated);
        return redirect()->route('shops.show', $shop)->with('notice', 'Shop updated.');
    }

    public function destroy(Shop $shop)
    {
        $this->authorizeOwner($shop);
        $shop->delete();
        return redirect()->route('shops.index')->with('notice', 'Shop removed.');
    }

    private function authorizeOwner(Shop $shop): void
    {
        if ($shop->user_id !== auth()->id() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Not authorized');
        }
    }
}
