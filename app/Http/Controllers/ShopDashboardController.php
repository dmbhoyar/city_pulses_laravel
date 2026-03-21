<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\TemplateUnlockRequest;
use App\Models\Update;

class ShopDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        if (!$user || (!$user->isShopowner() && !$user->isServiceProvider() && !$user->isSuperadmin())) {
            abort(403);
        }

        $shop = auth()->user()->shops()->first();
        $revenueTotal = $shop ? $shop->revenues()->sum('amount') : 0;
        $offers = $shop ? Update::where('city_id', $shop->city_id)->offers()->get() : collect();

        $activeSubscription = Subscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        $astroUnlockRequest = $shop
            ? TemplateUnlockRequest::query()
                ->where('shop_id', $shop->id)
                ->where('template_key', 'astro_dynamic')
                ->latest('id')
                ->first()
            : null;

        return view('shop_dashboard.index', compact('shop', 'revenueTotal', 'offers', 'activeSubscription', 'astroUnlockRequest'));
    }
}
