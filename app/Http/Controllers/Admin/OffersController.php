<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CouponRedemption;
use App\Models\Shop;
use App\Models\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $pendingCount = CouponRedemption::where('status', 'pending')
            ->whereNotNull('offer_id')
            ->count();

        return view('admin.offers.index', compact('offers', 'cities', 'shops', 'q', 'pendingCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'nullable|string',
            'source_url'       => 'nullable|url|max:255',
            'published_at'     => 'nullable|date',
            'city_id'          => 'nullable|exists:cities,id',
            'shop_id'          => 'nullable|exists:shops,id',
            'points_required'  => 'nullable|integer|min:0|max:99999',
            'offer_category'   => 'nullable|in:coupon,product',
            'photo'            => 'nullable|image|max:4096',
        ]);

        $validated['update_type']    = 'offer';
        $validated['offer_category'] = $validated['offer_category'] ?? 'coupon';

        if (!empty($validated['shop_id']) && empty($validated['city_id'])) {
            $validated['city_id'] = Shop::query()->whereKey($validated['shop_id'])->value('city_id');
        }

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('offers', 'public');
        }

        unset($validated['photo']);
        Update::create($validated);

        return redirect()->route('admin.offers.index')->with('notice', 'Offer created successfully.');
    }

    public function update(Request $request, Update $offer)
    {
        if ($offer->update_type !== 'offer') {
            return redirect()->route('admin.offers.index')->with('alert', 'Only offer updates can be edited here.');
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'nullable|string',
            'source_url'       => 'nullable|url|max:255',
            'published_at'     => 'nullable|date',
            'city_id'          => 'nullable|exists:cities,id',
            'shop_id'          => 'nullable|exists:shops,id',
            'points_required'  => 'nullable|integer|min:0|max:99999',
            'offer_category'   => 'nullable|in:coupon,product',
            'photo'            => 'nullable|image|max:4096',
        ]);

        $validated['update_type']    = 'offer';
        $validated['offer_category'] = $validated['offer_category'] ?? 'coupon';

        if (!empty($validated['shop_id']) && empty($validated['city_id'])) {
            $validated['city_id'] = Shop::query()->whereKey($validated['shop_id'])->value('city_id');
        }

        if ($request->hasFile('photo')) {
            if ($offer->photo_path) {
                Storage::disk('public')->delete($offer->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('offers', 'public');
        }

        unset($validated['photo']);
        $offer->update($validated);

        return redirect()->route('admin.offers.index')->with('notice', 'Offer updated successfully.');
    }

    public function destroy(Update $offer)
    {
        if ($offer->update_type !== 'offer') {
            return redirect()->route('admin.offers.index')->with('alert', 'Only offer updates can be deleted here.');
        }

        if ($offer->photo_path) {
            Storage::disk('public')->delete($offer->photo_path);
        }

        $offer->delete();

        return redirect()->route('admin.offers.index')->with('notice', 'Offer deleted successfully.');
    }

    // Redemptions management
    public function redemptions(Request $request)
    {
        $statusFilter = $request->input('status', '');
        $q            = trim((string) $request->input('q', ''));

        $redemptions = CouponRedemption::query()
            ->with(['user:id,first_name,last_name,email', 'offer:id,title,photo_path,points_required,source_url'])
            ->whereNotNull('offer_id')
            ->when($statusFilter !== '', fn($qb) => $qb->where('status', $statusFilter))
            ->when($q !== '', function ($qb) use ($q) {
                $like = '%' . strtolower($q) . '%';
                $qb->whereHas('user', function ($uq) use ($like) {
                    $uq->whereRaw('LOWER(first_name) LIKE ?', [$like])
                       ->orWhereRaw('LOWER(last_name) LIKE ?', [$like])
                       ->orWhereRaw('LOWER(email) LIKE ?', [$like]);
                })->orWhereRaw('LOWER(title) LIKE ?', [$like]);
            })
            ->orderByDesc('redeemed_at')
            ->paginate(25)
            ->withQueryString();

        $counts = CouponRedemption::whereNotNull('offer_id')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.offers.redemptions', compact('redemptions', 'statusFilter', 'q', 'counts'));
    }

    public function updateRedemptionStatus(Request $request, CouponRedemption $redemption)
    {
        $validated = $request->validate([
            'status'      => 'required|in:pending,approved,on_the_way,delivered,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $redemption->update([
            'status'       => $validated['status'],
            'admin_notes'  => $validated['admin_notes'] ?? $redemption->admin_notes,
            'processed_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $redemption->status,
                'label'   => CouponRedemption::STATUS_LABELS[$redemption->status],
            ]);
        }

        return redirect()->back()->with('notice', 'Redemption status updated.');
    }
}
