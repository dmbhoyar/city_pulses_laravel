<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $allowed = ['en', 'mr', 'hi'];
        $defaultLocale = (string) config('app.locale', 'mr');
        $locale = (string) $request->session()->get('locale', $defaultLocale);

        if (!in_array($locale, $allowed, true)) {
            $locale = $defaultLocale;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
