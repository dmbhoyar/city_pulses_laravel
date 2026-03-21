<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ServiceProviderMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('alert', 'Not authorized');
        }

        $user = auth()->user();

        if ($user->isSuperadmin() || $user->isServiceProvider()) {
            return $next($request);
        }

        if (strtolower((string) $user->role) === 'normal') {
            $user->role = 'service_provider';
            $user->save();
            return $next($request);
        }

        return redirect()->route('home')->with('alert', 'Not authorized');
    }
}
