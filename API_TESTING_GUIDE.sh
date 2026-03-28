#!/bin/bash
# ShortsPlay API Testing Guide
# Use this script to test all ShortsPlay endpoints
# Replace {token} with actual Sanctum bearer token

BASE_URL="http://localhost"
AUTH_HEADER="Authorization: Bearer {token}"

echo "ShortsPlay API Testing Guide"
echo "============================"
echo ""

# ===== VIDEO MANAGEMENT =====
echo "📹 VIDEO MANAGEMENT ENDPOINTS"
echo ""

echo "1. Get Feed (all approved videos)"
echo "curl -X GET $BASE_URL/api/shorts/feed \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "2. Upload New Video (Creator)"
echo "curl -X POST $BASE_URL/api/shorts/upload \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{"
echo "    \"title\": \"Amazing Yoga Flow\","
echo "    \"source_url\": \"https://youtube.com/shorts/jNQXAC9IVRw\","
echo "    \"video_type\": \"yt\","
echo "    \"duration\": \"0:58\""
echo "  }'"
echo ""

echo "3. Get My Videos (Creator)"
echo "curl -X GET $BASE_URL/api/shorts/my-videos \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "4. Get Pending Videos (Admin)"
echo "curl -X GET $BASE_URL/api/shorts/pending \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "5. Approve Video (Admin)"
echo "curl -X POST $BASE_URL/api/shorts/{video-id}/approve \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "6. Reject Video (Admin)"
echo "curl -X POST $BASE_URL/api/shorts/{video-id}/reject \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{"
echo "    \"admin_notes\": \"Violates content policy - explicit language\""
echo "  }'"
echo ""

# ===== ENGAGEMENT =====
echo "❤️  ENGAGEMENT ENDPOINTS"
echo ""

echo "7. Like a Video"
echo "curl -X POST $BASE_URL/api/shorts/{video-id}/like \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "8. Comment on Video"
echo "curl -X POST $BASE_URL/api/shorts/{video-id}/comment \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{"
echo "    \"comment_text\": \"This is amazing! 🔥\""
echo "  }'"
echo ""

echo "9. Get Comments for Video"
echo "curl -X GET $BASE_URL/api/shorts/{video-id}/comments \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "10. Share Video"
echo "curl -X POST $BASE_URL/api/shorts/{video-id}/share \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{"
echo "    \"platform\": \"WhatsApp\""
echo "  }'"
echo ""

echo "11. Subscribe to Creator"
echo "curl -X POST $BASE_URL/api/shorts/{video-id}/subscribe \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

# ===== RUBY POINTS & LEADERBOARD =====
echo "💎 RUBY POINTS & LEADERBOARD ENDPOINTS"
echo ""

echo "12. Get My Stats"
echo "curl -X GET $BASE_URL/api/ruby/my-stats \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "13. Get User Stats"
echo "curl -X GET $BASE_URL/api/ruby/user/{user-id}/stats \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "14. Get Tier Information"
echo "curl -X GET $BASE_URL/api/ruby/tiers \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "15. Get Leaderboard"
echo "curl -X GET $BASE_URL/api/ruby/leaderboard?limit=20 \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

echo "16. Get Earnings Breakdown"
echo "curl -X GET $BASE_URL/api/ruby/user/{user-id}/earnings \\"
echo "  -H '$AUTH_HEADER' \\"
echo "  -H 'Accept: application/json'"
echo ""

# ===== EXAMPLE RESPONSES =====
echo "📋 EXAMPLE RESPONSES"
echo ""

echo "Upload Video Response:"
echo "{
  \"success\": true,
  \"message\": \"Video submitted for admin review\",
  \"data\": {
    \"id\": 1,
    \"title\": \"Amazing Yoga Flow\",
    \"embed_id\": \"jNQXAC9IVRw\",
    \"video_type\": \"yt\",
    \"creator\": {
      \"id\": 5,
      \"name\": \"John Yoga\",
      \"username\": \"@john\",
      \"ruby_points\": 0
    },
    \"status\": \"pending\",
    \"total_ruby_points\": 0
  }
}"
echo ""

echo "Like Video Response:"
echo "{
  \"success\": true,
  \"liked\": true,
  \"likes_count\": 42,
  \"points_awarded\": 1,
  \"creator_points\": 128,
  \"new_tier\": null
}"
echo ""

echo "Ruby Stats Response:"
echo "{
  \"success\": true,
  \"data\": {
    \"ruby_points\": 5250,
    \"current_tier\": {
      \"name\": \"silver\",
      \"display_name\": \"Silver Ruby\",
      \"tag\": \"Silver Ruby Taker\",
      \"emoji\": \"🥈\",
      \"threshold\": 5000,
      \"points\": 5250,
      \"achieved_at\": \"2026-03-25T10:30:00Z\"
    },
    \"progress_to_next\": {
      \"current_tier\": \"silver\",
      \"next_tier\": \"gold\",
      \"next_tier_display\": \"Gold Ruby\",
      \"current_points\": 5250,
      \"next_threshold\": 10000,
      \"points_needed\": 4750,
      \"percentage\": 5
    }
  }
}"
echo ""

echo "Tier Information Response:"
echo "{
  \"success\": true,
  \"data\": [
    {
      \"name\": \"silver\",
      \"display_name\": \"Silver Ruby\",
      \"tag\": \"Silver Ruby Taker\",
      \"emoji\": \"🥈\",
      \"base_threshold\": 5000,
      \"adjusted_threshold\": 5000,
      \"first_achiever\": null
    },
    {
      \"name\": \"gold\",
      \"display_name\": \"Gold Ruby\",
      \"tag\": \"Golden Ruby Holder\",
      \"emoji\": \"🥇\",
      \"base_threshold\": 10000,
      \"adjusted_threshold\": 10001,
      \"first_achiever\": {
        \"id\": 3,
        \"name\": \"Sarah Creator\",
        \"achieved_at\": \"2026-03-24T14:20:00Z\"
      }
    }
  ]
}"
echo ""

echo "Leaderboard Response:"
echo "{
  \"success\": true,
  \"data\": [
    {
      \"id\": 2,
      \"name\": \"@chef_hacks\",
      \"ruby_points\": 12840,
      \"tier\": {
        \"name\": \"diamond\",
        \"display_name\": \"Diamond Ruby\",
        \"tag\": \"Diamond Ruby Holder\",
        \"emoji\": \"💎\"
      },
      \"avatar\": null
    },
    {
      \"id\": 5,
      \"name\": \"@yoga_vibes\",
      \"ruby_points\": 7320,
      \"tier\": {
        \"name\": \"silver\",
        \"display_name\": \"Silver Ruby\",
        \"tag\": \"Silver Ruby Taker\",
        \"emoji\": \"🥈\"
      },
      \"avatar\": null
    }
  ]
}"
echo ""

# ===== TESTING WORKFLOW =====
echo "🧪 TESTING WORKFLOW"
echo ""

echo "Step 1: Create Test Accounts"
echo "- Create user1 as 'viewer'"
echo "- Create user2 as 'creator'"
echo "- Create admin as 'admin'"
echo ""

echo "Step 2: Get Auth Tokens"
echo "Use Sanctum to generate tokens for each user"
echo ""

echo "Step 3: Upload Video (as creator)"
echo "POST /api/shorts/upload with YouTube/Instagram link"
echo "Should return status: 'pending'"
echo ""

echo "Step 4: Review Video (as admin)"
echo "GET /api/shorts/pending to see pending videos"
echo "POST /api/shorts/{id}/approve to approve"
echo ""

echo "Step 5: View in Feed (as viewer)"
echo "GET /api/shorts/feed should show approved video"
echo ""

echo "Step 6: Engage (as viewer)"
echo "POST /api/shorts/{id}/like - should award 1 point"
echo "POST /api/shorts/{id}/comment - should award 2 points"
echo "POST /api/shorts/{id}/share - should award 3 points"
echo "POST /api/shorts/{id}/subscribe - should award 5 points"
echo ""

echo "Step 7: Check Stats (for creator)"
echo "GET /api/ruby/my-stats"
echo "Creator's ruby_points should increase"
echo ""

echo "Step 8: Check Tier Progression"
echo "Continue engaging until creator reaches tier thresholds"
echo "Monitor tier achievements"
echo ""

# ===== TROUBLESHOOTING =====
echo "🔧 TROUBLESHOOTING"
echo ""

echo "If you get 401 Unauthorized:"
echo "- Check token is valid"
echo "- Check token includes 'Bearer' prefix"
echo "- Verify user is authenticated"
echo ""

echo "If you get 403 Forbidden:"
echo "- Check user has correct role"
echo "- Verify admin only endpoints used by admin"
echo "- Check creator endpoints used by creator"
echo ""

echo "If videos not appearing in feed:"
echo "- Verify status = 'approved'"
echo "- Check video soft_deletes (deleted_at IS NULL)"
echo "- Confirm admin approved the video"
echo ""

echo "If points not updating:"
echo "- Check database records inserted"
echo "- Verify RubyPointsService called"
echo "- Check creator_id is correct"
echo ""

echo "==============================="
echo "For more info, see SHORTSPLAY_GUIDE.md"
echo "==============================="
