<?php

namespace App\Services;

use App\Models\RubyTier;
use App\Models\RubyTierThreshold;
use App\Models\ShortVideo;
use App\Models\User;
use App\Models\ViewerPointEvent;

class RubyPointsService
{
    const POINTS = [
        'like' => 1,
        'comment' => 2,
        'share' => 3,
        'subscribe' => 5,
    ];

    // Points for the viewer/actor who performs valid actions.
    const VIEWER_POINTS = [
        'view' => 1,
        'like' => 1,
        'comment' => 1,
        'follow' => 2,
    ];

    const TIER_THRESHOLDS = [
        'silver' => 5000,
        'gold' => 10000,
        'diamond' => 15000,
        'red' => 20000,
    ];

    /**
     * Calculate total ruby points for a video creator
     */
    public function calculateCreatorPoints(ShortVideo $video): int
    {
        $likes = $video->likes()->count() * self::POINTS['like'];
        $comments = $video->comments()->count() * self::POINTS['comment'];
        $shares = $video->shares()->count() * self::POINTS['share'];
        $subscriptions = $video->subscriptions()->distinct('user_id')->count() * self::POINTS['subscribe'];

        return $likes + $comments + $shares + $subscriptions;
    }

    /**
     * Calculate total ruby points for a creator across all videos
     */
    public function calculateUserTotalPoints(User $user): int
    {
        $videos = $user->shortVideos()->approved()->get();
        $totalPoints = 0;

        foreach ($videos as $video) {
            $totalPoints += $this->calculateCreatorPoints($video);
        }

        return $totalPoints;
    }

    /**
     * Award points when engagement happens
     * Returns the total points awarded to the creator
     */
    public function awardPointsForEngagement(ShortVideo $video, string $engagementType): int
    {
        $points = self::POINTS[$engagementType] ?? 0;
        
        if ($points > 0) {
            $creator = $video->creator;
            $creator->ruby_points += $points;
            $creator->save();

            // Check if user reached a new tier
            $this->checkAndAwardNewTier($creator);
        }

        return $points;
    }

    /**
     * Deduct points when engagement is removed (unlike/unsubscribe)
     * Returns the points deducted from the creator.
     */
    public function deductPointsForEngagement(ShortVideo $video, string $engagementType): int
    {
        $points = self::POINTS[$engagementType] ?? 0;

        if ($points > 0) {
            $creator = $video->creator;
            $creator->ruby_points = max(0, (int) $creator->ruby_points - $points);
            $creator->save();
        }

        return $points;
    }

    /**
     * Award viewer points once per unique legal action context.
     * Duplicate attempts return 0.
     */
    public function awardViewerPoints(User $viewer, string $eventType, string $subjectType, int $subjectId): int
    {
        $points = self::VIEWER_POINTS[$eventType] ?? 0;
        if ($points <= 0) {
            return 0;
        }

        $event = ViewerPointEvent::firstOrCreate(
            [
                'user_id' => $viewer->id,
                'event_type' => $eventType,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
            ],
            [
                'points' => $points,
            ]
        );

        if (!$event->wasRecentlyCreated) {
            return 0;
        }

        $viewer->ruby_points += $points;
        $viewer->save();
        $this->checkAndAwardNewTier($viewer);

        return $points;
    }

    /**
     * Check if user achieved a new tier
     */
    public function checkAndAwardNewTier(User $user): ?string
    {
        $currentPoints = $user->ruby_points;
        $newTier = null;

        foreach (self::TIER_THRESHOLDS as $tierName => $threshold) {
            $adjustedThreshold = $this->getAdjustedThreshold($tierName);
            
            // Check if user just reached this tier
            if ($currentPoints >= $adjustedThreshold) {
                $hasAchieved = RubyTier::where('user_id', $user->id)
                    ->where('tier_name', $tierName)
                    ->exists();

                if (!$hasAchieved) {
                    // Award the tier
                    RubyTier::create([
                        'user_id' => $user->id,
                        'tier_name' => $tierName,
                        'ruby_points' => $currentPoints,
                        'achieved_at' => now(),
                    ]);

                    // Check if this is the first ever achievement of this tier
                    $thresholdRecord = RubyTierThreshold::where('tier_name', $tierName)->first();
                    if (!$thresholdRecord) {
                        // Create threshold record for first time
                        RubyTierThreshold::create([
                            'tier_name' => $tierName,
                            'threshold_points' => $adjustedThreshold,
                            'first_achiever_id' => $user->id,
                            'first_achieved_at' => now(),
                        ]);
                    } elseif (!$thresholdRecord->first_achiever_id) {
                        // Update threshold record if not already set
                        $thresholdRecord->update([
                            'first_achiever_id' => $user->id,
                            'first_achieved_at' => now(),
                        ]);
                    }

                    $newTier = $tierName;
                }
            }
        }

        return $newTier;
    }

    /**
     * Get the adjusted threshold for a tier (increases by 1 for each first achiever)
     */
    public function getAdjustedThreshold(string $tierName): int
    {
        $baseThreshold = self::TIER_THRESHOLDS[$tierName];
        
        $thresholdRecord = RubyTierThreshold::where('tier_name', $tierName)->first();
        
        if ($thresholdRecord && $thresholdRecord->first_achiever_id) {
            // Someone already achieved it, add 1 to the threshold
            return $baseThreshold + 1;
        }

        return $baseThreshold;
    }

    /**
     * Get current tier of a user
     */
    public function getUserCurrentTier(User $user): ?array
    {
        $currentPoints = $user->ruby_points;

        // Check tiers in reverse order (highest to lowest)
        $tiers = ['red', 'diamond', 'gold', 'silver'];
        
        foreach ($tiers as $tierName) {
            $threshold = $this->getAdjustedThreshold($tierName);
            if ($currentPoints >= $threshold) {
                $tierRecord = RubyTier::where('user_id', $user->id)
                    ->where('tier_name', $tierName)
                    ->first();

                if ($tierRecord) {
                    return [
                        'name' => $tierName,
                        'display_name' => RubyTier::TIER_NAMES[$tierName] ?? ucfirst($tierName),
                        'tag' => RubyTier::TIER_TAGS[$tierName] ?? ucfirst($tierName),
                        'emoji' => RubyTier::TIER_EMOJIS[$tierName] ?? '💎',
                        'threshold' => $threshold,
                        'points' => $currentPoints,
                        'achieved_at' => $tierRecord->achieved_at,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Get progress to next tier
     */
    public function getProgressToNextTier(User $user): array
    {
        $currentPoints = $user->ruby_points;
        $tiers = ['silver', 'gold', 'diamond', 'red'];

        foreach ($tiers as $tierName) {
            $threshold = $this->getAdjustedThreshold($tierName);
            if ($currentPoints < $threshold) {
                $nextTierIdx = array_search($tierName, $tiers);
                $prevThreshold = $nextTierIdx > 0 ? $this->getAdjustedThreshold($tiers[$nextTierIdx - 1]) : 0;
                
                $progress = max(0, $currentPoints - $prevThreshold);
                $total = $threshold - $prevThreshold;
                $percentage = $total > 0 ? round(($progress / $total) * 100) : 0;

                return [
                    'current_tier' => $nextTierIdx > 0 ? $tiers[$nextTierIdx - 1] : null,
                    'next_tier' => $tierName,
                    'next_tier_display' => RubyTier::TIER_NAMES[$tierName] ?? ucfirst($tierName),
                    'current_points' => $currentPoints,
                    'next_threshold' => $threshold,
                    'points_needed' => max(0, $threshold - $currentPoints),
                    'percentage' => $percentage,
                ];
            }
        }

        // User maxed out
        return [
            'current_tier' => 'red',
            'next_tier' => null,
            'current_points' => $currentPoints,
            'percentage' => 100,
        ];
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(int $limit = 20): array
    {
        return User::where('ruby_points', '>', 0)
            ->orderByDesc('ruby_points')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                $currentTier = $this->getUserCurrentTier($user);
                return [
                    'id' => $user->id,
                    'name' => '@' . $user->first_name . ($user->last_name ? ' ' . $user->last_name : ''),
                    'ruby_points' => $user->ruby_points,
                    'tier' => $currentTier,
                    'avatar' => $user->avatar_url ?? null,
                ];
            })
            ->toArray();
    }
}
