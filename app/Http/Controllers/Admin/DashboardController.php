<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Listing;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $usersCount    = User::count();
        $shopsCount    = Shop::count();
        $listingsCount = Listing::count();
        return view('admin.dashboard.index', compact('usersCount', 'shopsCount', 'listingsCount'));
    }
}
