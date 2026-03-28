<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AmazonCouponController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function fetch(Request $request)
    {
        // Optionally, check for superadmin role
        if (!auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized');
        }
        // Run the command
        try {
            Artisan::call('coupons:fetch-amazon');
            $output = Artisan::output();
            return response()->json(['success' => true, 'message' => $output]);
        } catch (\Exception $e) {
            Log::error('Amazon coupon fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch coupons.']);
        }
    }
}
