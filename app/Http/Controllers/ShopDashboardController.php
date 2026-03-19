<?php

namespace App\Http\Controllers;

use App\Models\Update;

class ShopDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'shopowner']);
    }

    public function index()
    {
        $shop = auth()->user()->shops()->first();
        $revenueTotal = $shop ? $shop->revenues()->sum('amount') : 0;
        $offers = $shop ? Update::where('city_id', $shop->city_id)->offers()->get() : collect();
        return view('shop_dashboard.index', compact('shop', 'revenueTotal', 'offers'));
    }
}
