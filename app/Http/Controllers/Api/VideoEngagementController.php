<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Models\VideoLike;
use App\Models\VideoComment;
use App\Models\VideoShare;
use App\Models\ShortVideoSubscription;
use App\Services\RubyPointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoEngagementController extends Controller
{
    protected $rubyService;

    public function __construct(RubyPointsService $rubyService)
    {
        $this->rubyService = $rubyService;
    }

    /**
     * Like a video
     */
    public function like(Request $request, ShortVideo $video)
    {
        if (strtolower((string) $video->status) !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved videos are eligible'], 422);
        }

        $user = auth()->user();

        // Check if already liked
        $liked = VideoLike::where('short_video_id', $video->id)
            ->where('user_id', $user->id)
            ->first();

        if ($liked) {
            $liked->delete();
            if ($video->creator_id !== $user->id) {
                $this->rubyService->deductPointsForEngagement($video, 'like');
            }
            return response()->json([
                'success' => true,
                'liked' => false,
                'likes_count' => $video->likes()->count(),
            ]);
        }

        VideoLike::create([
            'short_video_id' => $video->id,
            'user_id' => $user->id,
        ]);

        // Award points to creator
        $points = $video->creator_id !== $user->id
            ? $this->rubyService->awardPointsForEngagement($video, 'like')
            : 0;
        $newTier = $this->rubyService->checkAndAwardNewTier($video->creator);

        return response()->json([
            'success' => true,
            'liked' => true,
            'likes_count' => $video->likes()->count(),
            'points_awarded' => $points,
            'creator_points' => $video->creator->fresh()->ruby_points,
            'new_tier' => $newTier,
        ]);
    }

    /**
     * Add a comment
     */
    public function comment(Request $request, ShortVideo $video)
    {
        if (strtolower((string) $video->status) !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved videos are eligible'], 422);
        }

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'comment_text' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment = VideoComment::create([
            'short_video_id' => $video->id,
            'user_id' => $user->id,
            'comment_text' => $request->comment_text,
        ]);

        // Award points to creator
        $points = $video->creator_id !== $user->id
            ? $this->rubyService->awardPointsForEngagement($video, 'comment')
            : 0;
        $newTier = $this->rubyService->checkAndAwardNewTier($video->creator);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $comment->id,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'username' => '@' . $user->first_name,
                ],
                'text' => $comment->comment_text,
                'created_at' => $comment->created_at,
            ],
            'comments_count' => $video->comments()->count(),
            'points_awarded' => $points,
            'creator_points' => $video->creator->fresh()->ruby_points,
            'new_tier' => $newTier,
        ], 201);
    }

    /**
     * Get comments for a video
     */
    public function getComments(Request $request, ShortVideo $video)
    {
        $comments = VideoComment::where('short_video_id', $video->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->first_name . ' ' . $comment->user->last_name,
                        'username' => '@' . $comment->user->first_name,
                    ],
                    'text' => $comment->comment_text,
                    'created_at' => $comment->created_at,
                ];
            }),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Share a video
     */
    public function share(Request $request, ShortVideo $video)
    {
        if (strtolower((string) $video->status) !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved videos are eligible'], 422);
        }

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        VideoShare::create([
            'short_video_id' => $video->id,
            'user_id' => $user->id,
            'platform' => $request->platform,
        ]);

        // Share intent should not directly award points. Use referral/open tracking instead.
        $points = 0;
        $newTier = null;

        return response()->json([
            'success' => true,
            'message' => 'Video shared',
            'shares_count' => $video->shares()->count(),
            'points_awarded' => $points,
            'creator_points' => $video->creator->fresh()->ruby_points,
            'new_tier' => $newTier,
        ]);
    }

    /**
     * Subscribe to creator
     */
    public function subscribe(Request $request, ShortVideo $video)
    {
        if (strtolower((string) $video->status) !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved creators are eligible'], 422);
        }

        $user = auth()->user();
        $creator = $video->creator;

        if ($user->id === $creator->id) {
            return response()->json(['success' => false, 'message' => 'Cannot subscribe to yourself'], 422);
        }

        // Check if already subscribed
        $subscribed = ShortVideoSubscription::where('user_id', $user->id)
            ->where('creator_id', $creator->id)
            ->first();

        if ($subscribed) {
            return response()->json([
                'success' => false,
                'message' => 'Already subscribed',
            ]);
        }

        ShortVideoSubscription::create([
            'short_video_id' => $video->id,
            'user_id' => $user->id,
            'creator_id' => $creator->id,
        ]);

        // Award points to creator for first legal subscribe.
        $points = $this->rubyService->awardPointsForEngagement($video, 'subscribe');
        $newTier = $this->rubyService->checkAndAwardNewTier($creator);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed to creator',
            'subscribers_count' => $video->subscriptions()->distinct('user_id')->count(),
            'points_awarded' => $points,
            'creator_points' => $creator->fresh()->ruby_points,
            'new_tier' => $newTier,
        ]);
    }
}
