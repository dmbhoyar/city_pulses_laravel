<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share city list to all views for the navbar selector
        \View::composer('*', function ($view) {
            $cities = \App\Models\City::orderBy('name')->pluck('name');
            $view->with('navCities', $cities);
            if (auth()->check()) {
                $view->with('currentUser', auth()->user());
            }
        });
    }
}
