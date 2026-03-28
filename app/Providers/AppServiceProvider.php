<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Only run this if not running in console and DB is available
        if (!$this->app->runningInConsole()) {
            try {
                if (\Schema::hasTable('cities')) {
                    \View::composer('*', function ($view) {
                        $cities = \App\Models\City::orderBy('name')->pluck('name');
                        $view->with('navCities', $cities);
                        if (auth()->check()) {
                            $view->with('currentUser', auth()->user());
                        }
                    });
                }
            } catch (\Exception $e) {
                // Prevent boot failure if DB is not ready
            }
        }
    }
}
