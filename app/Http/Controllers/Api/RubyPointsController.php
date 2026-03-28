<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RubyTier;
use App\Services\RubyPointsService;
use Illuminate\Http\Request;

class RubyPointsController extends Controller
{
    protected $rubyService;

    public function __construct(RubyPointsService $rubyService)
    {
        $this->rubyService = $rubyService;
    }

    /**
     * Get user's ruby points and tier info
     */
    public function getUserStats(Request $request, User $user)
    {
        $currentTier = $this->rubyService->getUserCurrentTier($user);
        $progress = $this->rubyService->getProgressToNextTier($user);

        return response()->json([
            'success' => true,
            'data' => [
                'ruby_points' => $user->ruby_points,
                'current_tier' => $currentTier,
                'progress_to_next' => $progress,
            ],
        ]);
    }

    /**
     * Get my stats (authenticated user)
     */
    public function getMyStats(Request $request)
    {
        $user = auth()->user();
        return $this->getUserStats($request, $user);
    }

    /**
     * Get tier information
     */
    public function getTiers(Request $request)
    {
        $tiers = [];
        
        foreach (RubyPointsService::TIER_THRESHOLDS as $tierName => $baseThreshold) {
            $adjustedThreshold = $this->rubyService->getAdjustedThreshold($tierName);
            
            // Get first achiever from database
            $thresholdRecord = \App\Models\RubyTierThreshold::where('tier_name', $tierName)->first();
            $firstAchiever = null;
            
            if ($thresholdRecord && $thresholdRecord->first_achiever) {
                $firstAchiever = [
                    'id' => $thresholdRecord->first_achiever->id,
                    'name' => $thresholdRecord->first_achiever->first_name . ' ' . $thresholdRecord->first_achiever->last_name,
                    'achieved_at' => $thresholdRecord->first_achieved_at,
                ];
            }

            $tiers[] = [
                'name' => $tierName,
                'display_name' => RubyTier::TIER_NAMES[$tierName],
                'tag' => RubyTier::TIER_TAGS[$tierName],
                'emoji' => RubyTier::TIER_EMOJIS[$tierName],
                'base_threshold' => $baseThreshold,
                'adjusted_threshold' => $adjustedThreshold,
                'first_achiever' => $firstAchiever,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $tiers,
        ]);
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(Request $request)
    {
        $limit = $request->get('limit', 20);
        $leaderboard = $this->rubyService->getLeaderboard($limit);

        return response()->json([
            'success' => true,
            'data' => $leaderboard,
        ]);
    }

    /**
     * Get earning breakdown for a creator
     */
    public function getEarningsBreakdown(Request $request, User $user)
    {
        // Get all approved videos for the user
        $videos = $user->shortVideos()->approved()->with(['likes', 'comments', 'shares', 'subscriptions'])->get();

        $breakdown = [
            'total_likes' => 0,
            'likes_points' => 0,
            'total_comments' => 0,
            'comments_points' => 0,
            'total_shares' => 0,
            'shares_points' => 0,
            'total_subscriptions' => 0,
            'subscriptions_points' => 0,
            'total_points' => 0,
        ];

        foreach ($videos as $video) {
            $likes = $video->likes()->count();
            $comments = $video->comments()->count();
            $shares = $video->shares()->count();
            $subscriptions = $video->subscriptions()->distinct('user_id')->count();

            $breakdown['total_likes'] += $likes;
            $breakdown['likes_points'] += $likes * RubyPointsService::POINTS['like'];
            $breakdown['total_comments'] += $comments;
            $breakdown['comments_points'] += $comments * RubyPointsService::POINTS['comment'];
            $breakdown['total_shares'] += $shares;
            $breakdown['shares_points'] += $shares * RubyPointsService::POINTS['share'];
            $breakdown['total_subscriptions'] += $subscriptions;
            $breakdown['subscriptions_points'] += $subscriptions * RubyPointsService::POINTS['subscribe'];
        }

        $breakdown['total_points'] = $breakdown['likes_points'] + $breakdown['comments_points'] 
            + $breakdown['shares_points'] + $breakdown['subscriptions_points'];

        return response()->json([
            'success' => true,
            'data' => $breakdown,
        ]);
    }
}
