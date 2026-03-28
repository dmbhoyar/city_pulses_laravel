<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        if (!isset($user->tags)) $user->tags = [];
        if (!isset($user->ruby_points)) $user->ruby_points = 0;

        // Stats for ShortsPlay-style profile
        $rubyService = app(\App\Services\RubyPointsService::class);
        $currentTier = $rubyService->getUserCurrentTier($user);
        $progress = $rubyService->getProgressToNextTier($user);
        $tiers = [];
        foreach (\App\Models\RubyTier::TIERS as $tierName => $threshold) {
            $achieved = $currentTier && $currentTier['name'] === $tierName;
            $tiers[] = [
                'name' => $tierName,
                'display_name' => \App\Models\RubyTier::TIER_NAMES[$tierName],
                'emoji' => \App\Models\RubyTier::TIER_EMOJIS[$tierName],
                'threshold' => $threshold,
                'achieved' => $achieved,
                'locked' => !$achieved && (!$currentTier || $currentTier['name'] !== $tierName),
            ];
        }

        // ShortsPlay stats
        $subscribers = $user->creatorSubscriptions()->count();
        $likes = $user->shortVideos()->withCount('likes')->get()->sum('likes_count');
        $shares = $user->shortVideos()->withCount('shares')->get()->sum('shares_count');

        return view('profile.show', [
            'user' => $user,
            'ruby_points' => $user->ruby_points,
            'current_tier' => $currentTier,
            'progress' => $progress,
            'tiers' => $tiers,
            'subscribers' => $subscribers,
            'likes' => $likes,
            'shares' => $shares,
        ]);
    }
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request, EmailOtpService $otpService): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|regex:/^[0-9]{10}$/|unique:users,mobile_number,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'otp_code' => 'nullable|string|size:6',
        ]);

        if (!empty($validated['password'])) {
            if (empty($validated['current_password']) || !Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $user->password = Hash::make($validated['password']);
        }

        $newMobile = trim((string) ($validated['mobile_number'] ?? ''));
        $mobileChanged = $newMobile !== '' && $newMobile !== (string) ($user->mobile_number ?? '');

        if ($mobileChanged) {
            $otpCode = (string) ($validated['otp_code'] ?? '');
            if ($otpCode === '') {
                $otpService->sendOtp($newMobile, 'mobile_change', ['new_mobile' => $newMobile]);
                return back()->with('notice', 'OTP sent to your mobile number. Enter OTP to verify new mobile number.')->withInput();
            }

            $meta = [];
            if (!$otpService->verifyOtp($newMobile, 'mobile_change', $otpCode, $meta)) {
                return back()->withErrors(['otp_code' => 'Invalid or expired OTP for mobile change.'])->withInput();
            }

            $verifiedMobile = trim((string) ($meta['new_mobile'] ?? ''));
            if ($verifiedMobile === '' || $verifiedMobile !== $newMobile) {
                return back()->withErrors(['mobile_number' => 'Mobile number does not match OTP request.'])->withInput();
            }

            $user->mobile_number = $verifiedMobile;
        }

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'] ?? '';
        $user->email = $validated['email'];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = '/storage/' . $path;
        }

        $user->save();

        return back()->with('notice', 'Profile updated successfully.');
    }
}
