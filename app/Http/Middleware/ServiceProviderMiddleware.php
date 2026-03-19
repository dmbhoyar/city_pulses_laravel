<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ServiceProviderMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isServiceProvider()) {
            return redirect()->route('home')->with('alert', 'Not authorized');
        }
        return $next($request);
    }
}
