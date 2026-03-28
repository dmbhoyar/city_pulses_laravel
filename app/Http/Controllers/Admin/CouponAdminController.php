<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponAdminController extends Controller
{
    // Delete a specific coupon by ID
    public function deleteCoupon($id)
    {
        if (!auth()->user()->isSuperadmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['error' => 'Coupon not found'], 404);
        }
        $coupon->delete();
        return response()->json(['success' => true]);
    }

    // Set points required for a coupon
    public function setPoints(Request $request, $id)
    {
        if (!auth()->user()->isSuperadmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['error' => 'Coupon not found'], 404);
        }
        $points = (int) $request->input('points_required');
        if ($points < 1) {
            return response()->json(['error' => 'Invalid points value'], 422);
        }
        $coupon->points_required = $points;
        $coupon->save();
        return response()->json(['success' => true, 'points_required' => $coupon->points_required]);
    }

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // Delete all expired coupons
    public function deleteExpired(Request $request)
    {
        if (!auth()->user()->isSuperadmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $deleted = Coupon::whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->delete();
        return response()->json(['success' => true, 'deleted' => $deleted]);
    }
}
