<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Listing;
use App\Models\Subscription;
use App\Models\TemplateUnlockRequest;
use App\Models\City;
use App\Models\Job;
use App\Models\Update;
use App\Models\UserSubmission;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $users_count = User::count();
        $shops_count = Shop::count();
        $listings_count = Listing::count();
        $subscriptions_count = Subscription::count();
        $unlock_requests_pending_count = TemplateUnlockRequest::where('status', 'pending')->count();
        $cities_count = City::count();
        $jobs_count = Job::count();
        $offers_count = Update::where('update_type', 'offer')->count();
        $pending_submissions_count = UserSubmission::where('status', 'pending')->count();

        return view('admin.dashboard.index', compact(
            'users_count',
            'shops_count',
            'listings_count',
            'subscriptions_count',
            'unlock_requests_pending_count',
            'cities_count',
            'jobs_count',
            'offers_count',
            'pending_submissions_count'
        ));
    }
}
