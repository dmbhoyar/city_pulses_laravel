<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackRubyVisitPoints
{
    private const DAILY_VISIT_POINTS = 5;
    private const UNIQUE_PAGE_POINTS = 1;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();
        if (!$user || !$request->isMethod('GET')) {
            return $response;
        }

        $path = '/' . ltrim($request->path(), '/');
        if ($this->shouldIgnorePath($path)) {
            return $response;
        }

        $today = now()->toDateString();
        $awarded = 0;

        $dailyInserted = DB::table('user_daily_visit_points')->insertOrIgnore([
            'user_id' => $user->id,
            'visit_date' => $today,
            'points_awarded' => self::DAILY_VISIT_POINTS,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if ($dailyInserted > 0) {
            $awarded += self::DAILY_VISIT_POINTS;
        }

        $pageInserted = DB::table('user_page_visit_points')->insertOrIgnore([
            'user_id' => $user->id,
            'visit_date' => $today,
            'page_key' => substr($path, 0, 191),
            'points_awarded' => self::UNIQUE_PAGE_POINTS,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if ($pageInserted > 0) {
            $awarded += self::UNIQUE_PAGE_POINTS;
        }

        if ($awarded > 0) {
            DB::table('users')->where('id', $user->id)->increment('ruby_points', $awarded);
        }

        return $response;
    }

    private function shouldIgnorePath(string $path): bool
    {
        if (str_starts_with($path, '/api/')) return true;
        if (str_starts_with($path, '/shortsplay/auth/')) return true;
        if (str_starts_with($path, '/password/')) return true;
        if (in_array($path, ['/login', '/register', '/logout'], true)) return true;

        return false;
    }
}
