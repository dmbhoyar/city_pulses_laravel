<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\BusinessProfile;
use App\Models\Subscription;
use App\Models\TemplateUnlockRequest;
use App\Models\Update;
use App\Models\User;

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

        $isService = $user->isServiceProvider();
        $shop      = $user->shops()->first();

        $revenueTotal = $shop ? $shop->revenues()->sum('amount') : 0;

        $offers = $shop && $shop->city_id
            ? Update::where('city_id', $shop->city_id)->offers()->latest()->take(6)->get()
            : collect();

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

        $workersCount = $shop ? User::where('shop_id', $shop->id)->count() : 0;

        $uid = $user->id;
        $invoiceStats = [
            'total'  => Invoice::where('user_id', $uid)->count(),
            'draft'  => Invoice::where('user_id', $uid)->where('status', 'draft')->count(),
            'sent'   => Invoice::where('user_id', $uid)->where('status', 'sent')->count(),
            'paid'   => Invoice::where('user_id', $uid)->where('status', 'paid')->count(),
            'earned' => (float) Invoice::where('user_id', $uid)->where('status', 'paid')->sum('total'),
        ];

        $recentInvoices = Invoice::where('user_id', $uid)->latest()->take(5)->get();

        $profile = BusinessProfile::forUser($uid);
        $profileFields = ['business_name', 'business_phone', 'business_address', 'gstin', 'bank_name', 'payment_qr', 'signature'];
        $profileFilled = collect($profileFields)->filter(fn($f) => !empty($profile->$f))->count();
        $profilePct    = (int) round($profileFilled / count($profileFields) * 100);

        return view('shop_dashboard.index', compact(
            'shop', 'revenueTotal', 'offers', 'activeSubscription', 'astroUnlockRequest',
            'workersCount', 'invoiceStats', 'recentInvoices', 'profile', 'profilePct', 'isService'
        ));
    }
}
