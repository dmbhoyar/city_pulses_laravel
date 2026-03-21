<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\ShopsController;
use App\Http\Controllers\FarmingController;
use App\Http\Controllers\UpdatesController;
use App\Http\Controllers\MyshopController;
use App\Http\Controllers\MyserviceController;
use App\Http\Controllers\NewspaperController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\RentsController;
use App\Http\Controllers\BuyController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\ShopDashboardController;
use App\Http\Controllers\SubscriptionsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Admin\ShopsController as AdminShopsController;
use App\Http\Controllers\Admin\SubscriptionsController as AdminSubscriptionsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\CitiesController as AdminCitiesController;
use App\Http\Controllers\Admin\JobsController as AdminJobsController;
use App\Http\Controllers\Admin\OffersController as AdminOffersController;
use App\Http\Controllers\Webhooks\StripeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/set_city', [HomeController::class, 'setCity'])->name('set_city');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Jobs
Route::resource('jobs', JobsController::class);
Route::get('/jobs/{job}/apply', [JobsController::class, 'apply'])->name('jobs.apply');
Route::post('/jobs/{job}/apply', [JobsController::class, 'submitApplication'])->name('jobs.submit_application');

// Shops
Route::resource('shops', ShopsController::class);

// Listings
Route::resource('listings', ListingsController::class);

// Farming
Route::resource('farming', FarmingController::class);

// Updates
Route::resource('updates', UpdatesController::class);

// Rents
Route::get('/rents', [RentsController::class, 'index'])->name('rents.index');
Route::get('/rents/{listing}', [RentsController::class, 'show'])->name('rents.show');

// Buy (listings with sell category)
Route::get('/buy', [BuyController::class, 'index'])->name('buy.index');
Route::get('/buy/{listing}', [BuyController::class, 'show'])->name('buy.show');
Route::get('/buy/new', [BuyController::class, 'create'])->name('buy.new');
Route::post('/buy', [BuyController::class, 'store'])->name('buy.create');
Route::get('/buy/{listing}/edit', [BuyController::class, 'edit'])->name('buy.edit');
Route::patch('/buy/{listing}', [BuyController::class, 'update'])->name('buy.update');
Route::delete('/buy/{listing}', [BuyController::class, 'destroy'])->name('buy.destroy');

// Services
Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
Route::get('/services/{publicSlug}', [ServicesController::class, 'show'])->name('services.show');
Route::post('/services/{publicSlug}/reviews', [ServicesController::class, 'storeReview'])
    ->name('services.reviews.store');

// Newspaper
Route::get('/newspaper', [NewspaperController::class, 'show'])->name('newspaper');

// Offers
Route::get('/offers', [OffersController::class, 'index'])->name('offers');

// About
Route::view('/about', 'about.index')->name('about');

Route::get('/robots.txt', function () {
    $content = [
        'User-agent: *',
        'Allow: /',
        'Sitemap: ' . url('/sitemap.xml'),
    ];

    return response(implode(PHP_EOL, $content), 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
});

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'changefreq' => 'hourly', 'priority' => '1.0'],
        ['loc' => route('offers'), 'changefreq' => 'daily', 'priority' => '0.9'],
        ['loc' => route('about'), 'changefreq' => 'weekly', 'priority' => '0.7'],
        ['loc' => route('updates.index'), 'changefreq' => 'hourly', 'priority' => '0.9'],
        ['loc' => route('jobs.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
        ['loc' => route('farming.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
        ['loc' => route('rents.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
        ['loc' => route('buy.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
        ['loc' => route('services.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
        ['loc' => route('newspaper'), 'changefreq' => 'daily', 'priority' => '0.7'],
    ];

    return response()
        ->view('shared.sitemap_xml', ['urls' => $urls])
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

// Shop Dashboard
Route::get('/shop_dashboard', [ShopDashboardController::class, 'index'])->name('shop_dashboard')->middleware('auth');

// Subscriptions
Route::get('/subscriptions/new', [SubscriptionsController::class, 'create'])->name('subscriptions.new')->middleware('auth');
Route::post('/subscriptions', [SubscriptionsController::class, 'store'])->name('subscriptions.create')->middleware('auth');
Route::patch('/subscriptions/template', [SubscriptionsController::class, 'updateTemplate'])->name('subscriptions.template.update')->middleware('auth');
Route::post('/subscriptions/template-unlock-request', [SubscriptionsController::class, 'requestTemplateUnlock'])->name('subscriptions.template_unlock_request')->middleware('auth');

// MyShop
Route::middleware('auth')->group(function () {
    Route::get('/myshop', [MyshopController::class, 'index'])->name('myshop');
    Route::get('/myshop/configure', [MyshopController::class, 'configure'])->name('configure_myshop');
    Route::patch('/myshop/configure', [MyshopController::class, 'configureSave'])->name('configure_myshop_save');
    Route::get('/myshop/unlock', [MyshopController::class, 'unlockPage'])->name('myshop.unlock');
    Route::get('/myshop/requests', [MyshopController::class, 'clientRequests'])->name('myshop_requests');
    Route::patch('/myshop/requests/{id}', [MyshopController::class, 'updateClientRequest'])->name('myshop_requests_update');
    Route::get('/myshop/workers', [MyshopController::class, 'workers'])->name('workers_myshop');
    Route::post('/myshop/workers', [MyshopController::class, 'createWorker'])->name('create_worker_myshop');
    Route::patch('/myshop/workers', [MyshopController::class, 'updateWorker'])->name('update_worker_myshop');
    Route::get('/myshop/worker/{id}/experience', [MyshopController::class, 'workerExperience'])->name('worker_experience_myshop');
    Route::post('/myshop/subscribe', [MyshopController::class, 'subscribe'])->name('myshop_subscribe');
    Route::get('/myshop/offer/new', [MyshopController::class, 'offerNew'])->name('myshop_offer_new');
    Route::post('/myshop/offer', [MyshopController::class, 'offerCreate'])->name('myshop_offer_create');
    Route::get('/myshop/experience', [MyshopController::class, 'experience'])->name('myshop_experience');
    Route::get('/myshop/idcard', [MyshopController::class, 'idcard'])->name('myshop_idcard');
});

// MyService
Route::middleware('auth')->group(function () {
    Route::get('/myservice', [MyserviceController::class, 'index'])->name('myservice');
    Route::get('/myservice/configure', [MyserviceController::class, 'configure'])->name('configure_myservice');
    Route::patch('/myservice/configure', [MyserviceController::class, 'configureSave'])->name('configure_myservice_save');
    Route::post('/myservice/configure', [MyserviceController::class, 'configureSave']);
    Route::get('/myservice/unlock', [MyserviceController::class, 'unlockPage'])->name('myservice.unlock');
    Route::get('/myservice/workers', [MyserviceController::class, 'workers'])->name('workers_myservice');
    Route::post('/myservice/workers', [MyserviceController::class, 'createWorker'])->name('create_worker_myservice');
    Route::patch('/myservice/workers', [MyserviceController::class, 'updateWorker'])->name('update_worker_myservice');
    Route::get('/myservice/worker/{id}/experience', [MyserviceController::class, 'workerExperience'])->name('worker_experience_myservice');
    Route::get('/myservice/offer/new', [MyserviceController::class, 'offerNew'])->name('myservice_offer_new');
    Route::post('/myservice/offer', [MyserviceController::class, 'offerCreate'])->name('myservice_offer_create');
    Route::get('/myservice/requests', [MyserviceController::class, 'clientRequests'])->name('myservice_requests');
    Route::patch('/myservice/requests/{id}', [MyserviceController::class, 'updateClientRequest'])->name('myservice_requests_update');
    Route::get('/myservice/experience', [MyserviceController::class, 'experience'])->name('myservice_experience');
    Route::get('/myservice/idcard', [MyserviceController::class, 'idcard'])->name('myservice_idcard');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUsersController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [AdminUsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUsersController::class, 'destroy'])->name('users.destroy');
    Route::get('/cities', [AdminCitiesController::class, 'index'])->name('cities.index');
    Route::post('/cities', [AdminCitiesController::class, 'store'])->name('cities.store');
    Route::patch('/cities/{city}', [AdminCitiesController::class, 'update'])->name('cities.update');
    Route::delete('/cities/{city}', [AdminCitiesController::class, 'destroy'])->name('cities.destroy');
    Route::get('/jobs', [AdminJobsController::class, 'index'])->name('jobs.index');
    Route::post('/jobs', [AdminJobsController::class, 'store'])->name('jobs.store');
    Route::patch('/jobs/{job}', [AdminJobsController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [AdminJobsController::class, 'destroy'])->name('jobs.destroy');
    Route::get('/offers', [AdminOffersController::class, 'index'])->name('offers.index');
    Route::post('/offers', [AdminOffersController::class, 'store'])->name('offers.store');
    Route::patch('/offers/{offer}', [AdminOffersController::class, 'update'])->name('offers.update');
    Route::delete('/offers/{offer}', [AdminOffersController::class, 'destroy'])->name('offers.destroy');
    Route::get('/shops', [AdminShopsController::class, 'index'])->name('shops.index');
    Route::delete('/shops/{shop}', [AdminShopsController::class, 'destroy'])->name('shops.destroy');
    Route::get('/subscriptions', [AdminSubscriptionsController::class, 'index'])->name('subscriptions.index');
    Route::patch('/subscriptions/{subscription}/status', [AdminSubscriptionsController::class, 'updateSubscriptionStatus'])->name('subscriptions.status');
    Route::patch('/template-unlock-requests/{unlockRequest}/status', [AdminSubscriptionsController::class, 'updateUnlockStatus'])->name('template_unlock_requests.status');
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});

// Webhooks
Route::post('/webhooks/stripe', [StripeController::class, 'create'])->name('webhooks.stripe');

// Public client request capture (from template forms)
Route::post('/client-requests', [ShopsController::class, 'storeClientRequest'])->name('client_requests.store');

// Public Service ID Card
Route::get('/{publicSlug}/id-card', [ShopsController::class, 'publicIdcardBySlug'])
    ->where('publicSlug', '[a-z0-9-]+')
    ->name('shops.idcard.public');

Route::get('/{service}/{provider}/id-card', [ShopsController::class, 'publicIdcard'])
    ->where([
        'service' => '[a-z0-9-]+',
        'provider' => '[a-z0-9-]+',
    ])
    ->name('shops.idcard.public.legacy');

// Public Service Page (custom slug)
Route::get('/{publicSlug}', [ShopsController::class, 'publicShowBySlug'])
    ->where('publicSlug', '[a-z0-9-]+')
    ->name('shops.public');

// Public Service Page (legacy service/provider slug)
Route::get('/{service}/{provider}', [ShopsController::class, 'publicShow'])
    ->where([
        'service' => '[a-z0-9-]+',
        'provider' => '[a-z0-9-]+',
    ])
    ->name('shops.public.legacy');

// Health check
Route::get('/up', function () {
    return response('OK', 200);
})->name('health_check');
