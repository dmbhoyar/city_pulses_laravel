<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RubyTier;
use App\Models\RubyTierThreshold;
use App\Models\ShortVideo;
use App\Models\ShortVideoSubscription;
use App\Models\User;
use App\Models\CommentLike;
use App\Models\VideoComment;
use App\Models\VideoLike;
use App\Models\VideoShare;
use App\Models\VideoView;
use App\Services\RubyPointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShortVideoController extends Controller
{
    protected $rubyService;

    public function __construct(RubyPointsService $rubyService)
    {
        $this->rubyService = $rubyService;
    }

    /**
     * Get approved videos for feed
     */
    public function getFeed(Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min($perPage, 30));
        $viewer = auth()->user();

        // Inspired by modern short-video ranking:
        // mix engagement quality + recency + exploration randomness.
        $rankedVideos = ShortVideo::where('status', 'approved')
            ->with('creator')
            ->withCount(['likes', 'comments', 'shares'])
            ->get()
            ->map(function (ShortVideo $video) {
                $publishedAt = $video->approved_at ?: $video->created_at;
                $ageHours = max(1, (int) $publishedAt->diffInHours(now()));

                $engagementScore =
                    ($video->likes_count * 1.0)
                    + ($video->comments_count * 1.6)
                    + ($video->shares_count * 2.2);

                $engagementBoost = log(2 + $engagementScore);
                $recencyBoost = 1 / (1 + ($ageHours / 18));
                $explorationBoost = mt_rand(0, 1000) / 1000;

                $video->feed_rank =
                    ($engagementBoost * 0.55)
                    + ($recencyBoost * 0.30)
                    + ($explorationBoost * 0.15);

                return $video;
            })
            ->sortByDesc('feed_rank')
            ->values();

        $total = $rankedVideos->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min($page, $lastPage);
        $videos = $rankedVideos->forPage($currentPage, $perPage)->values();

        return response()->json([
            'success' => true,
            'data' => $videos->map(function (ShortVideo $video) use ($viewer) {
                return $this->formatVideoResponse($video, $viewer);
            }),
            'pagination' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Submit a new video
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'source_url' => 'required|string|url',
            'video_type' => 'nullable|in:yt,drive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $resolvedType = $request->video_type;
        if (!$resolvedType) {
            if ($this->extractYoutubeId($request->source_url)) {
                $resolvedType = 'yt';
            } elseif ($this->extractGoogleDriveId($request->source_url)) {
                $resolvedType = 'drive';
            }
        }

        if (!$resolvedType) {
            return response()->json([
                'success' => false,
                'message' => 'Only YouTube Shorts and Google Drive video links are allowed.',
            ], 422);
        }

        // Extract embed ID from URL
        $embedId = match ($resolvedType) {
            'yt' => $this->extractYoutubeId($request->source_url),
            'drive' => $this->extractGoogleDriveId($request->source_url),
        };

        if (!$embedId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid video URL format',
            ], 422);
        }

        $title = trim((string) $request->get('title', ''));
        if ($title === '') {
            $title = match ($resolvedType) {
                'yt' => 'YouTube Short Submission',
                'drive' => 'Google Drive Video Submission',
            };
        }

        // For admin, videos go live immediately
        $status = $user->isSuperadmin() ? 'approved' : 'pending';
        $approvedAt = $status === 'approved' ? now() : null;

        $video = ShortVideo::create([
            'creator_id' => $user->id,
            'title' => $title,
            'description' => trim((string) $request->get('description', '')) ?: null,
            'source_url' => $request->source_url,
            'embed_id' => $embedId,
            'video_type' => $resolvedType,
            'duration' => $request->get('duration'),
            'status' => $status,
            'approved_at' => $approvedAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => $status === 'approved'
                ? 'Video uploaded and live!'
                : 'Video submitted for admin review',
            'data' => $this->formatVideoResponse($video),
        ], 201);
    }

    /**
     * Get creator's videos
     */
    public function myVideos(Request $request)
    {
        $user = auth()->user();

        $videos = ShortVideo::where('creator_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $videos->map(function ($video) use ($user) {
                return $this->formatVideoResponse($video, $user);
            }),
        ]);
    }

    /**
     * Get admin pending videos
     */
    public function getPending(Request $request)
    {
        $user = auth()->user();

        if (!$this->canModerate($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $status = strtolower((string) $request->get('status', 'pending'));
        if (!in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $videos = ShortVideo::query()
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->with('creator')
            ->orderByDesc('approved_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'status' => $status,
            'counts' => [
                'pending' => ShortVideo::where('status', 'pending')->count(),
                'approved' => ShortVideo::where('status', 'approved')->count(),
                'rejected' => ShortVideo::where('status', 'rejected')->count(),
            ],
            'data' => $videos->map(function ($video) use ($user) {
                return $this->formatVideoResponse($video, $user);
            }),
        ]);
    }

    /**
     * Approve a video
     */
    public function approve(Request $request, ShortVideo $video)
    {
        $user = auth()->user();

        if (!$this->canModerate($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $video->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video approved and live',
            'data' => $this->formatVideoResponse($video, $user),
        ]);
    }

    /**
     * Reject a video
     */
    public function reject(Request $request, ShortVideo $video)
    {
        $user = auth()->user();

        if (!$this->canModerate($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $video->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => $request->get('admin_notes'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video rejected',
        ]);
    }

    /**
     * Superadmin-only hard delete for moderation cleanup.
     */
    public function destroy(Request $request, ShortVideo $video)
    {
        $user = auth()->user();

        if (!$this->canDelete($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only superadmin can delete videos.',
            ], 403);
        }

        $video->delete();

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully.',
        ]);
    }

    // ─────────────────────────────────────────────
    //  ENGAGEMENT
    // ─────────────────────────────────────────────

    /** POST /shortsplay/data/{video}/like — toggle like */
    public function toggleLike(Request $request, ShortVideo $video): JsonResponse
    {
        if (!$this->canRewardOnVideo($video)) {
            return response()->json(['success' => false, 'message' => 'Only approved videos can be liked'], 422);
        }

        $user = auth()->user();
        $pts = 0;
        $viewerPts = 0;

        $existing = VideoLike::where('short_video_id', $video->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            if ($video->creator_id !== $user->id) {
                $pts = $this->rubyService->deductPointsForEngagement($video, 'like');
            }
            $liked = false;
        } else {
            VideoLike::create([
                'short_video_id' => $video->id,
                'user_id' => $user->id,
            ]);
            // Award ruby points to the creator (not self-likes)
            if ($video->creator_id !== $user->id) {
                $pts = $this->rubyService->awardPointsForEngagement($video, 'like');
                $viewerPts = $this->rubyService->awardViewerPoints($user, 'like', 'video', $video->id);
            } else {
                $pts = 0;
            }
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes' => $video->likes()->count(),
            'points_awarded' => $liked ? ($pts ?? 0) : 0,
            'points_deducted' => !$liked ? ($pts ?? 0) : 0,
            'viewer_points_awarded' => $liked ? ($viewerPts ?? 0) : 0,
        ]);
    }

    /** GET /shortsplay/data/{video}/comments */
    public function getComments(Request $request, ShortVideo $video): JsonResponse
    {
        $userId = auth()->id();

        // Load only top-level comments (no parent)
        $comments = VideoComment::where('short_video_id', $video->id)
            ->whereNull('parent_id')
            ->with(['user:id,first_name,last_name', 'replies.user:id,first_name,last_name', 'replies.likes'])
            ->withCount('likes')
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($c) use ($userId) {
                return $this->formatComment($c, $userId);
            });

        return response()->json([
            'success' => true,
            'data' => $comments,
            'total' => $comments->count(),
        ]);
    }

    private function formatComment(VideoComment $c, int $userId): array
    {
        $replies = $c->relationLoaded('replies') ? $c->replies->map(function ($r) use ($userId) {
            return [
                'id'         => $r->id,
                'text'       => $r->comment_text,
                'pinned'     => false,
                'parent_id'  => $r->parent_id,
                'likes'      => $r->likes_count ?? $r->likes->count(),
                'is_liked'   => $r->likes->contains('user_id', $userId),
                'user'       => [
                    'id'   => $r->user->id,
                    'name' => '@' . $r->user->first_name,
                ],
                'created_at' => $r->created_at,
                'replies'    => [],
            ];
        }) : collect([]);

        return [
            'id'         => $c->id,
            'text'       => $c->comment_text,
            'pinned'     => (bool) $c->pinned,
            'parent_id'  => $c->parent_id,
            'likes'      => $c->likes_count ?? 0,
            'is_liked'   => CommentLike::where('comment_id', $c->id)->where('user_id', $userId)->exists(),
            'user'       => [
                'id'   => $c->user->id,
                'name' => '@' . $c->user->first_name,
            ],
            'created_at' => $c->created_at,
            'replies'    => $replies->values(),
        ];
    }

    /** POST /shortsplay/data/{video}/comment */
    public function postComment(Request $request, ShortVideo $video): JsonResponse
    {
        if (!$this->canRewardOnVideo($video)) {
            return response()->json(['success' => false, 'message' => 'Comments are allowed on approved videos only'], 422);
        }

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'text'      => 'required|string|max:500',
            'parent_id' => 'nullable|integer|exists:video_comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Validate parent comment belongs to this video
        $parentId = $request->parent_id ?? null;
        if ($parentId) {
            $parent = VideoComment::find($parentId);
            if (!$parent || $parent->short_video_id !== $video->id) {
                return response()->json(['success' => false, 'message' => 'Invalid parent comment'], 422);
            }
        }

        $comment = VideoComment::create([
            'short_video_id' => $video->id,
            'user_id'        => $user->id,
            'parent_id'      => $parentId,
            'comment_text'   => $request->text,
        ]);

        // Award ruby points to creator (only for top-level comments)
        $pts = 0;
        $viewerPts = 0;
        if (!$parentId && $video->creator_id !== $user->id) {
            $pts = $this->rubyService->awardPointsForEngagement($video, 'comment');
            $viewerPts = $this->rubyService->awardViewerPoints($user, 'comment', 'video', $video->id);
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id'         => $comment->id,
                'text'       => $comment->comment_text,
                'pinned'     => false,
                'parent_id'  => $comment->parent_id,
                'likes'      => 0,
                'is_liked'   => false,
                'user'       => ['id' => $user->id, 'name' => '@' . $user->first_name],
                'created_at' => $comment->created_at,
                'replies'    => [],
            ],
            'total'          => $video->comments()->whereNull('parent_id')->count(),
            'points_awarded' => $pts,
            'viewer_points_awarded' => $viewerPts,
        ], 201);
    }

    /** POST /shortsplay/data/comments/{comment}/pin */
    public function pinComment(Request $request, VideoComment $comment): JsonResponse
    {
        $user  = auth()->user();
        $video = $comment->video;

        // Only the video creator or an admin may pin
        if ($video->creator_id !== $user->id && !$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorised'], 403);
        }

        // Toggle: if already pinned, just unpin
        if ($comment->pinned) {
            $comment->update(['pinned' => false]);
            return response()->json(['success' => true, 'pinned' => false]);
        }

        // Unpin previous pinned comment on this video
        VideoComment::where('short_video_id', $video->id)
            ->where('pinned', true)
            ->update(['pinned' => false]);

        $comment->update(['pinned' => true]);

        return response()->json(['success' => true, 'pinned' => true]);
    }

    /** POST /shortsplay/data/comments/{comment}/like */
    public function likeComment(Request $request, VideoComment $comment): JsonResponse
    {
        $userId = auth()->id();

        $existing = CommentLike::where('comment_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            CommentLike::create(['comment_id' => $comment->id, 'user_id' => $userId]);
            $liked = true;
        }

        $total = CommentLike::where('comment_id', $comment->id)->count();

        return response()->json(['success' => true, 'liked' => $liked, 'likes' => $total]);
    }

    /** POST /shortsplay/data/{video}/share */
    public function recordShare(Request $request, ShortVideo $video): JsonResponse
    {
        if (!$this->canRewardOnVideo($video)) {
            return response()->json(['success' => false, 'message' => 'Only approved videos can be shared'], 422);
        }

        $user = auth()->user();

        $platform = $request->get('platform', 'other');

        VideoShare::create([
            'short_video_id' => $video->id,
            'user_id' => $user->id,
            'platform' => $platform,
        ]);

        return response()->json([
            'success' => true,
            'shares' => $video->shares()->where('platform', 'referral_visit')->count(),
            'points_awarded' => 0,
            'message' => 'Share intent recorded. Ruby points are awarded when another user opens your shared link.',
        ]);
    }

    /**
     * POST /shortsplay/data/referral-visit
     * Award share points only when a different user opens a shared link.
     */
    public function recordReferralVisit(Request $request): JsonResponse
    {
        $viewer = auth()->user();

        $validator = Validator::make($request->all(), [
            'video_id' => 'required|integer|exists:short_videos,id',
            'ref' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $video = ShortVideo::with('creator')->findOrFail((int) $request->video_id);
        if (!$this->canRewardOnVideo($video)) {
            return response()->json(['success' => false, 'message' => 'Video not eligible for points'], 422);
        }
        $referrerId = $request->filled('ref') ? (int) $request->ref : null;

        // Ignore self-visits and creator self-visits.
        if ($video->creator_id === $viewer->id || ($referrerId && $referrerId === $viewer->id)) {
            return response()->json([
                'success' => true,
                'awarded' => false,
                'shares' => VideoShare::where('short_video_id', $video->id)->where('platform', 'referral_visit')->count(),
                'points_awarded' => 0,
            ]);
        }

        $alreadyRewarded = VideoShare::where('short_video_id', $video->id)
            ->where('user_id', $viewer->id)
            ->where('platform', 'referral_visit')
            ->exists();

        if ($alreadyRewarded) {
            return response()->json([
                'success' => true,
                'awarded' => false,
                'shares' => VideoShare::where('short_video_id', $video->id)->where('platform', 'referral_visit')->count(),
                'points_awarded' => 0,
            ]);
        }

        VideoShare::create([
            'short_video_id' => $video->id,
            'user_id' => $viewer->id,
            'platform' => 'referral_visit',
        ]);

        $pts = $this->rubyService->awardPointsForEngagement($video, 'share');

        return response()->json([
            'success' => true,
            'awarded' => true,
            'shares' => VideoShare::where('short_video_id', $video->id)->where('platform', 'referral_visit')->count(),
            'points_awarded' => $pts,
        ]);
    }

    /** POST /shortsplay/data/creator/{creator}/subscribe — toggle follow */
    public function toggleSubscribe(Request $request, User $creator): JsonResponse
    {
        $user = auth()->user();
        $pts = 0;
        $viewerPts = 0;
        $deductedPts = 0;

        if ($user->id === $creator->id) {
            return response()->json(['success' => false, 'message' => 'Cannot subscribe to yourself'], 422);
        }

        $existing = ShortVideoSubscription::where('user_id', $user->id)
            ->where('creator_id', $creator->id)
            ->first();

        if ($existing) {
            $existing->delete();
            // Remove follow points when unfollow happens, only if creator has approved content.
            if (ShortVideo::where('creator_id', $creator->id)->where('status', 'approved')->exists()) {
                $creator->ruby_points = max(0, (int) $creator->ruby_points - RubyPointsService::POINTS['subscribe']);
                $creator->save();
                $deductedPts = RubyPointsService::POINTS['subscribe'];
            }
            $subscribed = false;
        } else {
            // Use any approved video from creator as context (or skip if none)
            $contextVideo = ShortVideo::where('creator_id', $creator->id)->where('status', 'approved')->first();
            ShortVideoSubscription::create([
                'short_video_id' => $contextVideo?->id ?? ShortVideo::where('creator_id', $creator->id)->first()?->id ?? 1,
                'user_id' => $user->id,
                'creator_id' => $creator->id,
            ]);
            // Award subscribe points to creator
            $pts = 0;
            if ($contextVideo) {
                $pts = $this->rubyService->awardPointsForEngagement($contextVideo, 'subscribe');
                $viewerPts = $this->rubyService->awardViewerPoints($user, 'follow', 'creator', $creator->id);
            } else {
                // No approved video => no creator reward for follow.
                $pts = 0;
            }
            $subscribed = true;
        }

        $creator->refresh();
        $subscriberCount = ShortVideoSubscription::where('creator_id', $creator->id)->count();

        return response()->json([
            'success' => true,
            'subscribed' => $subscribed,
            'subscriber_count' => $subscriberCount,
            'points_awarded' => $subscribed ? $pts : 0,
            'points_deducted' => !$subscribed ? $deductedPts : 0,
            'viewer_points_awarded' => $subscribed ? $viewerPts : 0,
        ]);
    }

    /** POST /shortsplay/data/{video}/view */
    public function recordView(Request $request, ShortVideo $video): JsonResponse
    {
        if (!$this->canRewardOnVideo($video)) {
            return response()->json(['success' => false, 'message' => 'Only approved videos can be viewed'], 422);
        }

        $viewer = auth()->user();
        $viewerPts = 0;

        $view = VideoView::firstOrCreate([
            'short_video_id' => $video->id,
            'user_id' => $viewer->id,
        ]);

        if ($view->wasRecentlyCreated && $video->creator_id !== $viewer->id) {
            $viewerPts = $this->rubyService->awardViewerPoints($viewer, 'view', 'video', $video->id);
        }

        return response()->json([
            'success' => true,
            'viewed' => true,
            'unique_view' => (bool) $view->wasRecentlyCreated,
            'viewer_points_awarded' => $viewerPts,
            'views' => VideoView::where('short_video_id', $video->id)->count(),
        ]);
    }

    /** GET /shortsplay/data/creator/{creator}/profile */
    public function creatorProfile(Request $request, User $creator): JsonResponse
    {
        $viewer = auth()->user();

        $approvedVideoIds = ShortVideo::where('creator_id', $creator->id)
            ->where('status', 'approved')
            ->pluck('id');

        $postsCount = $approvedVideoIds->count();
        $followersCount = ShortVideoSubscription::where('creator_id', $creator->id)->distinct('user_id')->count('user_id');
        $followingCount = ShortVideoSubscription::where('user_id', $creator->id)->distinct('creator_id')->count('creator_id');
        $likesCount = $postsCount ? VideoLike::whereIn('short_video_id', $approvedVideoIds)->count() : 0;
        $commentsCount = $postsCount ? VideoComment::whereIn('short_video_id', $approvedVideoIds)->count() : 0;
        $sharesCount = $postsCount ? VideoShare::whereIn('short_video_id', $approvedVideoIds)->where('platform', 'referral_visit')->count() : 0;

        $recentVideos = ShortVideo::where('creator_id', $creator->id)
            ->where('status', 'approved')
            ->withCount(['likes', 'comments'])
            ->orderByDesc('approved_at')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get()
            ->map(function (ShortVideo $video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'video_type' => $video->video_type,
                    'embed_id' => $video->embed_id,
                    'likes' => $video->likes_count ?? 0,
                    'comments' => $video->comments_count ?? 0,
                ];
            })
            ->values();

        $isFollowing = $viewer && $viewer->id !== $creator->id
            ? ShortVideoSubscription::where('user_id', $viewer->id)->where('creator_id', $creator->id)->exists()
            : false;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $creator->id,
                    'name' => trim(($creator->first_name ?? '') . ' ' . ($creator->last_name ?? '')),
                    'username' => '@' . ($creator->first_name ?: 'creator'),
                    'role' => $creator->role,
                    'ruby_points' => (int) ($creator->ruby_points ?? 0),
                    'is_following' => $isFollowing,
                ],
                'stats' => [
                    'posts' => $postsCount,
                    'followers' => $followersCount,
                    'following' => $followingCount,
                    'likes' => $likesCount,
                    'comments' => $commentsCount,
                    'shares' => $sharesCount,
                ],
                'videos' => $recentVideos,
            ],
        ]);
    }

    /** GET /shortsplay/data/users/search?q=... */
    public function searchUsers(Request $request): JsonResponse
    {
        $viewer = auth()->user();
        $q = trim((string) $request->get('q', ''));
        $limit = max(1, min((int) $request->get('limit', 20), 30));

        $query = User::query()
            ->where('id', '!=', $viewer->id)
            ->where(function ($inner) {
                $inner->whereNotNull('first_name')->orWhereNotNull('last_name');
            });

        if ($q !== '') {
            $query->where(function ($inner) use ($q) {
                $inner->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$q}%"])
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query
            ->orderByDesc('ruby_points')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'email', 'ruby_points'])
            ->map(function (User $user) use ($viewer) {
                $followers = ShortVideoSubscription::where('creator_id', $user->id)->distinct('user_id')->count('user_id');
                $posts = ShortVideo::where('creator_id', $user->id)->where('status', 'approved')->count();
                $isFollowing = ShortVideoSubscription::where('user_id', $viewer->id)
                    ->where('creator_id', $user->id)
                    ->exists();

                return [
                    'id' => $user->id,
                    'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                    'username' => '@' . ($user->first_name ?: strtok((string) $user->email, '@')),
                    'ruby_points' => (int) ($user->ruby_points ?? 0),
                    'followers' => $followers,
                    'posts' => $posts,
                    'is_following' => $isFollowing,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $users,
            'query' => $q,
        ]);
    }

    // ─────────────────────────────────────────────
    //  RUBY
    // ─────────────────────────────────────────────

    /** GET /shortsplay/data/ruby/stats */
    public function myRubyStats(Request $request): JsonResponse
    {
        $user = auth()->user();
        $currentTier = $this->rubyService->getUserCurrentTier($user);
        $progress = $this->rubyService->getProgressToNextTier($user);
        $subscriberCount = ShortVideoSubscription::where('creator_id', $user->id)->count();

        // Count total engagement on my videos
        $myVideoIds = ShortVideo::where('creator_id', $user->id)->pluck('id');
        $totalLikes = VideoLike::whereIn('short_video_id', $myVideoIds)->count();
        $totalShares = VideoShare::whereIn('short_video_id', $myVideoIds)->where('platform', 'referral_visit')->count();
        $totalComments = VideoComment::whereIn('short_video_id', $myVideoIds)->count();

        $achievedTiers = collect(RubyPointsService::TIER_THRESHOLDS)
            ->map(function ($baseThreshold, $tierName) {
                return [
                    'name' => $tierName,
                    'threshold' => $this->rubyService->getAdjustedThreshold($tierName),
                ];
            })
            ->filter(fn ($tier) => $user->ruby_points >= $tier['threshold'])
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'ruby_points' => $user->ruby_points,
                'current_tier' => $currentTier,
                'progress_to_next' => $progress,
                'achieved_tiers' => $achievedTiers,
                'stats' => [
                    'subscribers' => $subscriberCount,
                    'likes' => $totalLikes,
                    'comments' => $totalComments,
                    'shares' => $totalShares,
                ],
            ],
        ]);
    }

    /** GET /shortsplay/data/ruby/leaderboard */
    public function leaderboard(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 20), 50);
        $data = $this->rubyService->getLeaderboard($limit);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /** GET /shortsplay/data/ruby/tiers */
    public function tierInfo(Request $request): JsonResponse
    {
        $tiers = [];
        foreach (RubyPointsService::TIER_THRESHOLDS as $tierName => $base) {
            $adjusted = $this->rubyService->getAdjustedThreshold($tierName);
            $thresholdRecord = RubyTierThreshold::where('tier_name', $tierName)->with('firstAchiever:id,first_name,last_name,ruby_points')->first();

            $tiers[] = [
                'name' => $tierName,
                'display_name' => RubyTier::TIER_NAMES[$tierName],
                'tag' => RubyTier::TIER_TAGS[$tierName],
                'emoji' => RubyTier::TIER_EMOJIS[$tierName],
                'base_threshold' => $base,
                'adjusted_threshold' => $adjusted,
                'first_achiever' => $thresholdRecord?->firstAchiever ? [
                    'id' => $thresholdRecord->firstAchiever->id,
                    'name' => '@' . $thresholdRecord->firstAchiever->first_name,
                    'ruby_points' => $thresholdRecord->firstAchiever->ruby_points,
                ] : null,
            ];
        }

        return response()->json(['success' => true, 'data' => $tiers]);
    }

    private function canModerate($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->isSuperadmin()
            || strtolower((string) $user->role) === 'admin'
            || strtolower((string) $user->shorts_role) === 'admin';
    }

    private function canDelete($user): bool
    {
        return $user && $user->isSuperadmin();
    }

    private function canRewardOnVideo(ShortVideo $video): bool
    {
        return strtolower((string) $video->status) === 'approved';
    }

    // Helper methods
    private function extractYoutubeId($url)
    {
        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:shorts\/|watch\?v=|embed\/))([a-zA-Z0-9_-]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    private function extractGoogleDriveId($url)
    {
        if (preg_match('/drive\.google\.com\/file\/d\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('/drive\.google\.com\/(?:open|uc)\?id=([A-Za-z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function formatVideoResponse(ShortVideo $video, ?User $viewer = null)
    {
        $isLiked = $viewer ? VideoLike::where('short_video_id', $video->id)->where('user_id', $viewer->id)->exists() : false;
        $isSubscribed = $viewer ? ShortVideoSubscription::where('user_id', $viewer->id)->where('creator_id', $video->creator_id)->exists() : false;

        return [
            'id' => $video->id,
            'title' => $video->title,
            'description' => $video->description,
            'embed_id' => $video->embed_id,
            'video_type' => $video->video_type,
            'creator' => [
                'id' => $video->creator->id,
                'name' => trim($video->creator->first_name . ' ' . $video->creator->last_name),
                'username' => '@' . $video->creator->first_name,
                'ruby_points' => $video->creator->ruby_points,
            ],
            'duration' => $video->duration,
            'likes' => $video->likes()->count(),
            'comments' => $video->comments()->count(),
            'shares' => $video->shares()->where('platform', 'referral_visit')->count(),
            'views' => VideoView::where('short_video_id', $video->id)->count(),
            'subscribers' => ShortVideoSubscription::where('creator_id', $video->creator_id)->count(),
            'is_liked' => $isLiked,
            'is_subscribed' => $isSubscribed,
            'total_ruby_points' => $this->rubyService->calculateCreatorPoints($video),
            'status' => $video->status,
            'created_at' => $video->created_at,
            'approved_at' => $video->approved_at,
        ];
    }
}
