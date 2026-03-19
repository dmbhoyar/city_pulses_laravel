<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $q = $request->input('q');
        $listings = Listing::where('status', 'active');
        if ($request->filled('city_id')) $listings->where('city_id', $request->input('city_id'));
        if ($request->filled('category')) $listings->where('category', $request->input('category'));
        $listings = $listings->orderByDesc('created_at')->paginate(20);
        return view('listings.index', compact('listings', 'q'));
    }

    public function show(Listing $listing)
    {
        return view('listings.show', compact('listing'));
    }

    public function create()
    {
        $listing = new Listing();
        return view('listings.create', compact('listing'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|in:sell,rent,service,vehicle,land',
            'subcategory'    => 'nullable|string|max:100',
            'price'          => 'nullable|numeric',
            'contact_number' => 'nullable|string|max:20',
            'city_id'        => 'nullable|exists:cities,id',
            'shop_id'        => 'nullable|exists:shops,id',
            'location'       => 'nullable|string|max:255',
        ]);
        $listing = auth()->user()->listings()->create($validated);
        return redirect()->route('listings.show', $listing)->with('notice', 'Listing created.');
    }

    public function edit(Listing $listing)
    {
        $this->authorizeOwner($listing);
        return view('listings.edit', compact('listing'));
    }

    public function update(Request $request, Listing $listing)
    {
        $this->authorizeOwner($listing);
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|in:sell,rent,service,vehicle,land',
            'subcategory'    => 'nullable|string|max:100',
            'price'          => 'nullable|numeric',
            'contact_number' => 'nullable|string|max:20',
            'city_id'        => 'nullable|exists:cities,id',
            'location'       => 'nullable|string|max:255',
        ]);
        $listing->update($validated);
        return redirect()->route('listings.show', $listing)->with('notice', 'Listing updated.');
    }

    public function destroy(Listing $listing)
    {
        $this->authorizeOwner($listing);
        $listing->update(['status' => 'removed']);
        return redirect()->route('listings.index')->with('notice', 'Listing removed.');
    }

    private function authorizeOwner(Listing $listing): void
    {
        $user = auth()->user();
        if (
            $listing->user_id !== $user->id &&
            !$user->isSuperadmin() &&
            !($user->isShopowner() && $listing->shop?->user_id === $user->id)
        ) {
            abort(403, 'Not authorized');
        }
    }
}
