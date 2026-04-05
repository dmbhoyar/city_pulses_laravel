# ShortsPlay Implementation Guide

## Overview
ShortsPlay is a YouTube Shorts and Google Drive video aggregation platform with a Ruby Points gamification system. Content creators can upload videos, viewers can engage with them (like, comment, share, subscribe), and creators earn Ruby Points which unlock tier badges.

## Features Implemented

### 1. **Video Upload & Management**
- Creators can paste YouTube Shorts or Google Drive video links
- Source URLs are kept private from viewers
- Videos must be approved by admin before appearing in feed
- Admins can approve or reject submissions with notes

### 2. **Viewer Engagement**
- **Like Videos**: 1 Ruby Point awarded to creator
- **Comment**: 2 Ruby Points awarded to creator
- **Share**: 3 Ruby Points awarded to creator
- **Subscribe to Creator**: 5 Ruby Points awarded to creator

### 3. **Ruby Points System**
Ruby Points are accumulated through viewer engagement:
- Each like = 1 point
- Each comment = 2 points
- Each share = 3 points
- Each subscription = 5 points

### 4. **Ruby Tier Badges**
Four tier levels based on accumulated points:

| Tier | Emoji | Points | Tag |
|------|-------|--------|-----|
| Silver Ruby | 🥈 | 5,000 | Silver Ruby Taker |
| Gold Ruby | 🥇 | 10,000 | Golden Ruby Holder |
| Diamond Ruby | 💎 | 15,000 | Diamond Ruby Holder |
| Red Ruby | 🔴 | 20,000 | Red Ruby Achiever |

### 5. **Dynamic Tier Thresholds**
When a user first achieves a tier, subsequent users need +1 extra point to earn that tier:
- If first user gets Silver at 5000 pts, next users need 5001 pts
- Similar logic applies to all tiers
- This is tracked in the `ruby_tier_thresholds` table

### 6. **Legal & Policies Section**
Added comprehensive Legal tab with:
- Terms of Service
- Privacy Policy
- Content Policy
- Ruby Points Policy
- Disclaimer
- Contact & Support information

## Database Schema

### Tables Created

#### `short_videos`
```sql
- id
- creator_id (FK: users)
- title
- source_url (kept private)
- embed_id (extracted ID)
- video_type ('yt' or 'drive')
- status ('pending', 'approved', 'rejected')
- admin_notes
- approved_at
- rejected_at
- timestamps
- soft deletes
```

#### `video_likes`
```sql
- id
- short_video_id (FK: short_videos)
- user_id (FK: users)
- unique: (short_video_id, user_id)
```

#### `video_comments`
```sql
- id
- short_video_id (FK: short_videos)
- user_id (FK: users)
- comment_text
- timestamps
```

#### `video_shares`
```sql
- id
- short_video_id (FK: short_videos)
- user_id (FK: users)
- platform (WhatsApp, Telegram, Facebook, etc.)
```

#### `short_video_subscriptions`
```sql
- id
- short_video_id (FK: short_videos)
- user_id (FK: users)
- creator_id (FK: users)
- unique: (user_id, creator_id)
```

#### `ruby_tiers`
```sql
- id
- user_id (FK: users)
- tier_name ('silver', 'gold', 'diamond', 'red')
- ruby_points
- achieved_at
- unique: (user_id, tier_name)
```

#### `ruby_tier_thresholds`
```sql
- id
- tier_name ('silver', 'gold', 'diamond', 'red')
- threshold_points
- first_achiever_id (FK: users)
- first_achieved_at
```

#### `users` (columns added)
```sql
- ruby_points (unsigned bigint, default 0)
- shorts_role ('viewer', 'creator', 'admin')
```

## API Endpoints

### Video Management
```
GET    /api/shorts/feed                    - Get approved videos feed
POST   /api/shorts/upload                  - Upload new short video
GET    /api/shorts/my-videos               - Get creator's videos
GET    /api/shorts/pending                 - Get pending videos (admin)
POST   /api/shorts/{video}/approve         - Approve video (admin)
POST   /api/shorts/{video}/reject          - Reject video (admin)
```

### Engagement
```
POST   /api/shorts/{video}/like            - Like a video
POST   /api/shorts/{video}/comment         - Add comment
GET    /api/shorts/{video}/comments        - Get video comments
POST   /api/shorts/{video}/share           - Share video
POST   /api/shorts/{video}/subscribe       - Subscribe to creator
```

### Ruby Points & Leaderboard
```
GET    /api/ruby/my-stats                  - Get authenticated user's stats
GET    /api/ruby/user/{user}/stats         - Get specific user's stats
GET    /api/ruby/tiers                     - Get tier information
GET    /api/ruby/leaderboard               - Get leaderboard
GET    /api/ruby/user/{user}/earnings      - Get user's earnings breakdown
```

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Tier Thresholds
```bash
php artisan db:seed --class=RubyTierThresholdsSeeder
```

### 3. Access ShortsPlay
Navigate to `/shortsplay` (requires Sanctum authentication)

## Models & Relationships

### ShortVideo Model
```php
// Relationships
$video->creator()           // Creator user
$video->likes()             // VideoLike records
$video->comments()          // VideoComment records
$video->shares()            // VideoShare records
$video->subscriptions()     // Subscriptions for this video
```

### User Model (Extended)
```php
// ShortsPlay relationships added
$user->shortVideos()              // Videos created
$user->videoLikes()               // Likes given
$user->videoComments()            // Comments made
$user->videoShares()              // Shares made
$user->shortVideoSubscriptions()  // Subscriptions made
$user->creatorSubscriptions()     // Subscriptions received
$user->rubyTiers()                // Tier achievements
```

## Services

### RubyPointsService
Handles all Ruby Points calculations:

```php
// Calculate points for a video
$service->calculateCreatorPoints(ShortVideo $video)

// Calculate total points for a user
$service->calculateUserTotalPoints(User $user)

// Award points for engagement
$service->awardPointsForEngagement(ShortVideo $video, string $type)

// Check and award new tier
$service->checkAndAwardNewTier(User $user)

// Get adjusted threshold (with first achiever bonus)
$service->getAdjustedThreshold(string $tierName)

// Get user's current tier
$service->getUserCurrentTier(User $user)

// Get progress to next tier
$service->getProgressToNextTier(User $user)

// Get leaderboard
$service->getLeaderboard(int $limit = 20)
```

## Frontend

The frontend is a responsive mobile-first web app built with vanilla HTML/CSS/JS.

### Sections
1. **Feed** - Browse all approved videos
2. **Upload** - Submit new videos (creators only)
3. **Ruby System** - View tier information and leaderboard
4. **Profile** - View personal stats and tier progress
5. **Legal** - Terms, privacy, policies
6. **Admin** - Video moderation (admin only)

### Features
- Fullscreen reels player with swipe navigation
- Real-time engagement (like, comment, share)
- Ruby points visualization
- Tier progress tracking
- Leaderboard ranking

## Usage Examples

### Upload a Video (API)
```bash
curl -X POST http://localhost/api/shorts/upload \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Amazing Yoga Flow",
    "source_url": "https://youtube.com/shorts/jNQXAC9IVRw",
    "video_type": "yt",
    "duration": "0:58"
  }'
```

### Like a Video (API)
```bash
curl -X POST http://localhost/api/shorts/{video_id}/like \
  -H "Authorization: Bearer {token}"
```

### Get User Stats (API)
```bash
curl -X GET http://localhost/api/ruby/my-stats \
  -H "Authorization: Bearer {token}"
```

## Admin Functions

### Approve Video
```bash
curl -X POST http://localhost/api/shorts/{video_id}/approve \
  -H "Authorization: Bearer {admin_token}"
```

### Reject Video
```bash
curl -X POST http://localhost/api/shorts/{video_id}/reject \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "admin_notes": "Violates content policy"
  }'
```

## Points Calculation Formula

For a creator across all videos:
```
Total Ruby Points = 
  (Likes × 1) + 
  (Comments × 2) + 
  (Shares × 3) + 
  (Unique Subscribers × 5)
```

## Tier Achievement Logic

When calculating user's tier:
1. Get all accumulated ruby points
2. For each tier (checked highest to lowest):
   - Get adjusted threshold (base + 1 if first achiever exists)
   - If user's points >= threshold:
     - Check if RubyTier record exists
     - If not, create it and mark as achieved
3. Check if this is first ever achievement of tier
4. If yes, update RubyTierThreshold record with first achiever

## Security Considerations

1. **Source URL Privacy**: Original YouTube/Google Drive URLs are stored encrypted and never shown to viewers
2. **Engagement Validation**: Only authenticated users can engage
3. **Duplicate Prevention**: Unique constraints prevent duplicate likes/subscriptions
4. **Admin Only Actions**: Video approval/rejection requires admin role
5. **Soft Deletes**: Videos can be soft-deleted instead of permanently removed

## Future Enhancements

1. **Video Analytics**: Track views, engagement rate, watch time
2. **Creator Earnings**: Actual monetary rewards based on Ruby Points
3. **Badge System**: Additional badges for milestones
4. **Search & Discovery**: Search videos, trending section
5. **Notifications**: Real-time notifications for engagement
6. **Reporting**: User reporting system for inappropriate content
7. **Blocking**: Users can block other users
8. **Collections**: Save favorite videos
9. **Hashtags**: Searchable video tags
10. **Direct Messaging**: Creator-to-follower messaging

## Troubleshooting

### Videos not appearing in feed
- Check if status is 'approved'
- Verify admin has approved the video
- Check soft_deletes - ensure deleted_at is NULL

### Ruby Points not updating
- Verify RubyPointsService is being called after engagement
- Check if creator_id is set correctly
- Verify unique constraints aren't preventing duplicates

### Tier not achieved despite sufficient points
- Check RubyTierThreshold records exist
- Verify getAdjustedThreshold() is calculating correctly
- Check that checkAndAwardNewTier() is being called

## Support

For issues, feature requests, or questions, check:
- App logs: `storage/logs/laravel.log`
- Database queries: Enable query logging in config
- API responses: Use Postman to test endpoints

## Version
- **Current Version**: 1.0
- **Last Updated**: March 25, 2026
- **Framework**: Laravel 10
- **Frontend**: Vanilla HTML/CSS/JS
- **Authentication**: Laravel Sanctum
