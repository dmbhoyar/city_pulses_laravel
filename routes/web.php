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
Route::get('/services/{listing}', [ServicesController::class, 'show'])->name('services.show');

// Newspaper
Route::get('/newspaper', [NewspaperController::class, 'show'])->name('newspaper');

// Offers
Route::get('/offers', [OffersController::class, 'index'])->name('offers');

// About
Route::view('/about', 'about.index')->name('about');

// Shop Dashboard
Route::get('/shop_dashboard', [ShopDashboardController::class, 'index'])->name('shop_dashboard')->middleware('auth');

// Subscriptions
Route::get('/subscriptions/new', [SubscriptionsController::class, 'create'])->name('subscriptions.new')->middleware('auth');
Route::post('/subscriptions', [SubscriptionsController::class, 'store'])->name('subscriptions.create')->middleware('auth');

// MyShop
Route::middleware('auth')->group(function () {
    Route::get('/myshop', [MyshopController::class, 'index'])->name('myshop');
    Route::get('/myshop/configure', [MyshopController::class, 'configure'])->name('configure_myshop');
    Route::patch('/myshop/configure', [MyshopController::class, 'configureSave'])->name('configure_myshop_save');
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
    Route::get('/myservice/business_card', [MyserviceController::class, 'businessCard'])->name('business_card_myservice');
    Route::post('/myservice/business_card', [MyserviceController::class, 'businessCardSave'])->name('business_card_myservice_save');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [AdminUsersController::class, 'destroy'])->name('users.destroy');
    Route::get('/shops', [AdminShopsController::class, 'index'])->name('shops.index');
    Route::delete('/shops/{shop}', [AdminShopsController::class, 'destroy'])->name('shops.destroy');
});

// Webhooks
Route::post('/webhooks/stripe', [StripeController::class, 'create'])->name('webhooks.stripe');

// Health check
Route::get('/up', function () {
    return response('OK', 200);
})->name('health_check');
