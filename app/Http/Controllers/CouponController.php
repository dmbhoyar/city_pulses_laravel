<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\CouponApiService;
use App\Models\CouponRedemption;
use Illuminate\Support\Facades\Auth;
use App\Models\Update;

class CouponController extends Controller
{
    // Show the coupon hub page (Blade view)
    public function index()
    {
        $coupons = \App\Models\Coupon::where(function($q) {
            $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
        })->orderByDesc('id')->get();
        return view('offers.index', [
            'apiCoupons' => $coupons,
        ]);
    }

    // API: Get available coupons (AJAX)
    public function getCoupons(Request $request)
    {
        // Fetch coupons from the database (including Amazon coupons)
        $coupons = \App\Models\Coupon::where(function($q) {
            $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
        })
        ->orderByDesc('id')
        ->get()
        ->map(function($coupon) {
            return [
                'id' => $coupon->id,
                'title' => $coupon->title,
                'store' => $coupon->store,
                'code' => $coupon->discount_text, // Use discount_text for code if that's where it's stored
                'coupon_code' => $coupon->discount_text,
                'expiry' => $coupon->expiry_date,
                'shop_url' => $coupon->shop_url,
                'source' => $coupon->source,
            ];
        });
        return response()->json(['coupons' => $coupons]);
    }

    // API: Redeem a coupon
    public function redeem(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // If redeeming a coupon
        if ($request->has('coupon_code')) {
            $request->validate([
                'coupon_code' => 'required|string',
                'store' => 'nullable|string',
                'title' => 'nullable|string',
            ]);
            // Prevent duplicate redemption
            $exists = CouponRedemption::where('user_id', $user->id)
                ->where('coupon_code', $request->coupon_code)
                ->exists();
            if ($exists) {
                return response()->json(['error' => 'Already redeemed'], 409);
            }
            $redemption = CouponRedemption::create([
                'user_id' => $user->id,
                'coupon_code' => $request->coupon_code,
                'store' => $request->store,
                'title' => $request->title,
                'redeemed_at' => now(),
            ]);
            // Deduct points
            $points = $request->input('points_required', 200);
            $user->ruby_points = max(0, $user->ruby_points - $points);
            $user->save();
            return response()->json(['success' => true, 'redemption' => $redemption]);
        }

        // If redeeming an offer
        if ($request->has('offer_id')) {
            $request->validate([
                'offer_id' => 'required|integer|exists:updates,id',
            ]);
            $offer = Update::find($request->offer_id);
            if (!$offer || $offer->update_type !== 'offer') {
                return response()->json(['error' => 'Invalid offer'], 400);
            }
            // Prevent duplicate redemption
            $exists = CouponRedemption::where('user_id', $user->id)
                ->where('coupon_code', 'offer_'.$offer->id)
                ->exists();
            if ($exists) {
                return response()->json(['error' => 'Already redeemed'], 409);
            }
            $points = $offer->points_required ?? 200;
            if ($user->ruby_points < $points) {
                return response()->json(['error' => 'Not enough points'], 400);
            }
            $redemption = CouponRedemption::create([
                'user_id' => $user->id,
                'coupon_code' => 'offer_'.$offer->id,
                'store' => $offer->shop->name ?? '',
                'title' => $offer->title,
                'redeemed_at' => now(),
            ]);
            $user->ruby_points = max(0, $user->ruby_points - $points);
            $user->save();
            return response()->json(['success' => true, 'redemption' => $redemption]);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    // API: Get user's redemption history
    public function myRedemptions(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $redemptions = CouponRedemption::where('user_id', $user->id)
            ->orderByDesc('redeemed_at')
            ->get();
        return response()->json(['redemptions' => $redemptions]);
    }
}
