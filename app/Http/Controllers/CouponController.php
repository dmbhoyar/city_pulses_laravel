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

        // If redeeming a coupon (by coupon model ID)
        if ($request->has('coupon_id')) {
            $request->validate(['coupon_id' => 'required|integer|exists:coupons,id']);

            $coupon = \App\Models\Coupon::findOrFail($request->coupon_id);
            $points = ($coupon->points_required > 0) ? (int) $coupon->points_required : 200;

            if ($user->ruby_points < $points) {
                return response()->json(['error' => __('ui.offers_insufficient_points')], 400);
            }

            $exists = CouponRedemption::where('user_id', $user->id)
                ->where('coupon_code', 'coupon_' . $coupon->id)
                ->exists();
            if ($exists) {
                return response()->json(['error' => 'Already redeemed'], 409);
            }

            $redemption = CouponRedemption::create([
                'user_id'     => $user->id,
                'coupon_code' => 'coupon_' . $coupon->id,
                'store'       => $coupon->store,
                'title'       => $coupon->title,
                'redeemed_at' => now(),
            ]);
            $user->ruby_points = max(0, $user->ruby_points - $points);
            $user->save();

            return response()->json([
                'success'     => true,
                'redemption'  => $redemption,
                'coupon_code' => $coupon->discount_text,
                'points_left' => $user->ruby_points,
            ]);
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
            return response()->json([
                'success'    => true,
                'redemption' => $redemption,
                'points_left'=> $user->ruby_points,
            ]);
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
