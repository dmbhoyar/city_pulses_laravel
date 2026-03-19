<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class BuyController extends Controller
{
    public function index()
    {
        $listings = Listing::where('category', 'sell')->where('status', 'active')->orderByDesc('created_at')->paginate(20);
        return view('buy.index', compact('listings'));
    }

    public function show(Listing $listing)
    {
        return view('buy.show', compact('listing'));
    }

    public function create()
    {
        $listing = new Listing(['category' => 'sell']);
        return view('buy.create', compact('listing'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'nullable|numeric',
            'contact_number' => 'nullable|string|max:20',
            'city_id'        => 'nullable|exists:cities,id',
            'location'       => 'nullable|string|max:255',
        ]);
        $validated['category'] = 'sell';
        $listing = auth()->user()?->listings()->create($validated) ?? Listing::create($validated);
        return redirect()->route('buy.show', $listing)->with('notice', 'Listing created.');
    }

    public function edit(Listing $listing)
    {
        return view('buy.edit', compact('listing'));
    }

    public function update(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'nullable|numeric',
            'contact_number' => 'nullable|string|max:20',
            'city_id'        => 'nullable|exists:cities,id',
            'location'       => 'nullable|string|max:255',
        ]);
        $listing->update($validated);
        return redirect()->route('buy.show', $listing)->with('notice', 'Listing updated.');
    }

    public function destroy(Listing $listing)
    {
        $listing->delete();
        return redirect()->route('buy.index')->with('notice', 'Listing removed.');
    }
}
