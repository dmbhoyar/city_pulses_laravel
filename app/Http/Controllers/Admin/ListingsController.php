<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class ListingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $status = trim((string) $request->input('status', 'pending'));

        $query = Listing::query()
            ->with(['user', 'city', 'reviewer'])
            ->where('category', 'sell');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $listings = $query->latest('id')->paginate(25)->withQueryString();

        return view('admin.listings.index', compact('listings', 'status'));
    }

    public function updateStatus(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,active,rejected,removed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $listing->update([
            'status' => $validated['status'],
            'admin_notes' => trim((string) ($validated['admin_notes'] ?? '')),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('notice', 'Listing status updated successfully.');
    }
}
