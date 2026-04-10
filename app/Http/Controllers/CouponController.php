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
                'code' => $coupon->discount_text,
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
                'user_id'      => $user->id,
                'coupon_ref_id'=> $coupon->id,
                'coupon_code'  => 'coupon_' . $coupon->id,
                'store'        => $coupon->store,
                'title'        => $coupon->title,
                'redeemed_at'  => now(),
                'status'       => 'pending',
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
            $offer = Update::find($request->input('offer_id'));
            if (!$offer || $offer->update_type !== 'offer') {
                return response()->json(['error' => 'Invalid offer'], 400);
            }

            $isProduct = ($offer->offer_category ?? 'coupon') === 'product';

            // For product offers, validate delivery address
            $rules = ['offer_id' => 'required|integer|exists:updates,id'];
            if ($isProduct) {
                $rules['delivery_name']     = 'required|string|max:120';
                $rules['delivery_phone']    = 'required|string|max:20';
                $rules['delivery_address1'] = 'required|string|max:255';
                $rules['delivery_address2'] = 'nullable|string|max:255';
                $rules['delivery_city']     = 'required|string|max:100';
                $rules['delivery_state']    = 'required|string|max:100';
                $rules['delivery_pincode']  = 'required|string|max:20';
                $rules['delivery_landmark'] = 'nullable|string|max:255';
            }
            $request->validate($rules);

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

            $data = [
                'user_id'     => $user->id,
                'offer_id'    => $offer->id,
                'coupon_code' => 'offer_'.$offer->id,
                'store'       => $offer->shop->name ?? '',
                'title'       => $offer->title,
                'redeemed_at' => now(),
                'status'      => 'pending',
            ];

            if ($isProduct) {
                $data['delivery_name']     = $request->input('delivery_name');
                $data['delivery_phone']    = $request->input('delivery_phone');
                $data['delivery_address1'] = $request->input('delivery_address1');
                $data['delivery_address2'] = $request->input('delivery_address2');
                $data['delivery_city']     = $request->input('delivery_city');
                $data['delivery_state']    = $request->input('delivery_state');
                $data['delivery_pincode']  = $request->input('delivery_pincode');
                $data['delivery_landmark'] = $request->input('delivery_landmark');
            }

            $redemption = CouponRedemption::create($data);
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
