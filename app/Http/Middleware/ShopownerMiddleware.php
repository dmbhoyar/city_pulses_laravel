<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShopownerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('alert', 'Not authorized');
        }

        $user = auth()->user();

        if ($user->isSuperadmin() || $user->isShopowner()) {
            return $next($request);
        }

        if (strtolower((string) $user->role) === 'normal') {
            $user->role = 'shopowner';
            $user->save();
            return $next($request);
        }

        return redirect()->route('home')->with('alert', 'Not authorized');
    }
}
