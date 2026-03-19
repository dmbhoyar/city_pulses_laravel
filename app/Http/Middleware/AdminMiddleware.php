<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isSuperadmin()) {
            return redirect()->route('home')->with('alert', 'Not authorized');
        }
        return $next($request);
    }
}
