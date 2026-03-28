<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShortVideoController;
use App\Http\Controllers\Api\VideoEngagementController;
use App\Http\Controllers\Api\RubyPointsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ShortsPlay Routes
Route::middleware('auth:sanctum')->group(function () {
    // Short Videos
    Route::get('/shorts/feed', [ShortVideoController::class, 'getFeed']);
    Route::get('/shorts/my-videos', [ShortVideoController::class, 'myVideos']);
    Route::post('/shorts/upload', [ShortVideoController::class, 'store']);
    Route::get('/shorts/pending', [ShortVideoController::class, 'getPending']);
    Route::post('/shorts/{video}/approve', [ShortVideoController::class, 'approve']);
    Route::post('/shorts/{video}/reject', [ShortVideoController::class, 'reject']);

    // Video Engagement
    Route::post('/shorts/{video}/like', [VideoEngagementController::class, 'like']);
    Route::post('/shorts/{video}/comment', [VideoEngagementController::class, 'comment']);
    Route::get('/shorts/{video}/comments', [VideoEngagementController::class, 'getComments']);
    Route::post('/shorts/{video}/share', [VideoEngagementController::class, 'share']);
    Route::post('/shorts/{video}/subscribe', [VideoEngagementController::class, 'subscribe']);

    // Ruby Points
    Route::get('/ruby/my-stats', [RubyPointsController::class, 'getMyStats']);
    Route::get('/ruby/user/{user}/stats', [RubyPointsController::class, 'getUserStats']);
    Route::get('/ruby/tiers', [RubyPointsController::class, 'getTiers']);
    Route::get('/ruby/leaderboard', [RubyPointsController::class, 'getLeaderboard']);
    Route::get('/ruby/user/{user}/earnings', [RubyPointsController::class, 'getEarningsBreakdown']);
});

