<?php

namespace App\Http\Controllers;

use App\Services\EmailOtpService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ShortsPlayAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        /** @var User|null $authenticatedUser */
        $authenticatedUser = $request->user();
        if ($authenticatedUser) {
            return response()->json([
                'code' => 'already_authenticated',
                'message' => 'Already logged in. Entering ShortsPlay.',
                'already_authenticated' => true,
                'user' => $this->mapUser($authenticatedUser),
            ]);
        }

        $validated = $request->validate([
            'email' => 'nullable|string|max:255',
            'login_id' => 'nullable|string|max:255',
            'password' => 'required|string',
            'mode' => 'nullable|in:user,admin',
        ]);

        $loginId = trim((string) ($validated['login_id'] ?? $validated['email'] ?? ''));
        if ($loginId === '') {
            return response()->json([
                'code' => 'missing_login_id',
                'message' => 'Email or mobile is required.',
            ], 422);
        }

        $credentials = [
            'password' => $validated['password'],
        ];

        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $loginId;
        } else {
            $credentials['mobile_number'] = $loginId;
        }

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'code' => 'invalid_credentials',
                'message' => 'Invalid email/mobile or password.',
            ], 422);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();
        $selectedMode = $validated['mode'] ?? 'user';

        if ($selectedMode === 'admin' && !$user->isSuperadmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'code' => 'admin_mode_forbidden',
                'message' => 'Admin access is allowed only for superadmin users.',
            ], 403);
        }

        $targetShortsRole = $user->isSuperadmin()
            ? 'admin'
            : ($selectedMode === 'admin' ? 'admin' : 'viewer');

        if ($user->shorts_role !== $targetShortsRole) {
            $user->shorts_role = $targetShortsRole;
            $user->save();
        }

        return response()->json([
            'message' => 'Logged in successfully.',
            'user' => $this->mapUser($user->fresh()),
        ]);
    }

    public function register(Request $request, EmailOtpService $otpService): JsonResponse
    {
        /** @var User|null $authenticatedUser */
        $authenticatedUser = $request->user();
        if ($authenticatedUser) {
            return response()->json([
                'code' => 'already_authenticated',
                'message' => 'You are already logged in. Log out before creating another account.',
                'already_authenticated' => true,
                'user' => $this->mapUser($authenticatedUser),
            ], 409);
        }

        $otpCode = trim((string) $request->input('otp_code', ''));

        // Step 1: Initial registration details (no OTP)
        if ($otpCode === '') {
            $validated = $request->validate([
                'username' => 'required|string|max:100',
                'email' => 'nullable|email|max:255',
                'mobile_number' => 'required|regex:/^[0-9]{10}$/',
                'password' => 'required|string|min:8',
            ]);

            // Check uniqueness
            if ($validated['email'] && User::where('email', $validated['email'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already registered.',
                ], 422);
            }
            if (User::where('mobile_number', $validated['mobile_number'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mobile number is already registered.',
                ], 422);
            }

            // Store registration data in session
            $request->session()->put('shortsplay_registration', [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'mobile_number' => $validated['mobile_number'],
                'password' => $validated['password'],
            ]);

            // Send OTP to email if provided and not registered, else to mobile
            $otpRecipient = $validated['email'] ?: $validated['mobile_number'];
            $otpService->sendOtp($otpRecipient, 'shorts_register');

            return response()->json([
                'success' => true,
                'requires_otp' => true,
                'message' => filter_var($otpRecipient, FILTER_VALIDATE_EMAIL)
                    ? 'OTP sent to your email. Enter OTP to complete registration.'
                    : 'OTP sent to your mobile number. Enter OTP to complete registration.',
            ], 202);
        }

        // Step 2: OTP verification
        $regData = $request->session()->get('shortsplay_registration');
        if (!$regData) {
            return response()->json([
                'success' => false,
                'message' => 'Registration session expired. Please fill your details again.',
            ], 440);
        }

        $otpRecipient = $regData['email'] ?: $regData['mobile_number'];
        $meta = [];
        if (!$otpService->verifyOtp($otpRecipient, 'shorts_register', $otpCode, $meta)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $firstName = ltrim(trim($regData['username']), '@');
        if ($firstName === '') {
            $firstName = 'player';
        }

        $user = User::create([
            'first_name' => substr($firstName, 0, 100),
            'last_name' => '',
            'email' => $regData['email'],
            'mobile_number' => $regData['mobile_number'],
            'role' => 'user',
            'password' => Hash::make($regData['password']),
            'shorts_role' => 'viewer',
            'ruby_points' => 0,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('shortsplay_registration');

        return response()->json([
            'message' => 'Registered successfully.',
            'user' => $this->mapUser($user),
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        return response()->json([
            'user' => $user ? $this->mapUser($user) : null,
        ]);
    }

    public function forgotPassword(Request $request, EmailOtpService $otpService): JsonResponse
    {
        $validated = $request->validate([
            'mobile_number' => 'required|regex:/^[0-9]{10}$/',
        ]);

        $mobileNumber = trim((string) $validated['mobile_number']);
        if (User::where('mobile_number', $mobileNumber)->exists()) {
            $otpService->sendOtp($mobileNumber, 'shorts_password_reset');
        }

        return response()->json([
            'success' => true,
            'message' => 'If account exists, OTP has been sent to mobile number.',
        ]);
    }

    public function resetPassword(Request $request, EmailOtpService $otpService): JsonResponse
    {
        $validated = $request->validate([
            'mobile_number' => 'required|regex:/^[0-9]{10}$/',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $mobileNumber = (string) $validated['mobile_number'];
        $meta = [];
        if (!$otpService->verifyOtp($mobileNumber, 'shorts_password_reset', (string) $validated['otp_code'], $meta)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        $user = User::where('mobile_number', $mobileNumber)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $user->password = Hash::make((string) $validated['password']);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password reset successful.']);
    }

    public function updateProfile(Request $request, EmailOtpService $otpService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|regex:/^[0-9]{10}$/|unique:users,mobile_number,' . $user->id,
            'otp_code' => 'nullable|string|size:6',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            if (empty($validated['current_password']) || !Hash::check((string) $validated['current_password'], (string) $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password incorrect.'], 422);
            }
            $user->password = Hash::make((string) $validated['password']);
        }

        $newMobile = trim((string) ($validated['mobile_number'] ?? ''));
        if ($newMobile !== '' && $newMobile !== (string) ($user->mobile_number ?? '')) {
            $otpCode = trim((string) ($validated['otp_code'] ?? ''));
            if ($otpCode === '') {
                $otpService->sendOtp($newMobile, 'shorts_mobile_change', ['new_mobile' => $newMobile]);
                return response()->json([
                    'success' => true,
                    'requires_otp' => true,
                    'message' => 'OTP sent to your mobile number to verify mobile number change.',
                ], 202);
            }

            $meta = [];
            if (!$otpService->verifyOtp($newMobile, 'shorts_mobile_change', $otpCode, $meta)) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
            }

            if (($meta['new_mobile'] ?? '') !== $newMobile) {
                return response()->json(['success' => false, 'message' => 'Mobile number does not match OTP request.'], 422);
            }

            $user->mobile_number = $newMobile;
        }

        if (array_key_exists('first_name', $validated) && $validated['first_name'] !== null) {
            $user->first_name = (string) $validated['first_name'];
        }
        if (array_key_exists('last_name', $validated) && $validated['last_name'] !== null) {
            $user->last_name = (string) $validated['last_name'];
        }
        if (array_key_exists('email', $validated) && $validated['email'] !== null) {
            $user->email = (string) $validated['email'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => $this->mapUser($user->fresh()),
        ]);
    }

    private function mapUser(User $user): array
    {
        $display = trim((string) $user->first_name) !== ''
            ? $user->first_name
            : explode('@', (string) $user->email)[0];

        return [
            'id' => $user->id,
            'name' => $display,
            'email' => $user->email,
            'role' => $user->role,
            'shorts_role' => $user->shorts_role,
            'is_admin' => $user->isSuperadmin(),
            'ruby_points' => (int) $user->ruby_points,
        ];
    }
}
