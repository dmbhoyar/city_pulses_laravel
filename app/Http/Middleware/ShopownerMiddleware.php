<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShopownerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isShopowner()) {
            return redirect()->route('home')->with('alert', 'Not authorized');
        }
        return $next($request);
    }
}
