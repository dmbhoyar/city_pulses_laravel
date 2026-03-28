<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.passwords.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function showOtpResetForm(Request $request)
    {
        return view('auth.passwords.reset', [
            'token' => null,
            'mobile_number' => (string) $request->query('mobile_number', ''),
            'otpMode' => true,
        ]);
    }

    public function reset(Request $request, EmailOtpService $otpService)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
            'token'    => 'nullable|string',
            'otp_code' => 'nullable|string|size:6',
        ]);

        $otpCode = trim((string) $request->get('otp_code', ''));
        if ($otpCode !== '') {
            $request->validate([
                'mobile_number' => 'required|regex:/^[0-9]{10}$/',
            ]);

            $mobileNumber = trim((string) $request->get('mobile_number', ''));
            $meta = [];
            if (!$otpService->verifyOtp($mobileNumber, 'password_reset', $otpCode, $meta)) {
                return back()->withErrors(['otp_code' => 'Invalid or expired OTP.'])->withInput($request->except(['password', 'password_confirmation']));
            }

            $user = User::where('mobile_number', $mobileNumber)->first();
            if (!$user) {
                return back()->withErrors(['mobile_number' => 'User not found.']);
            }

            $user->forceFill([
                'password' => Hash::make((string) $request->password),
                'remember_token' => Str::random(60),
            ])->save();
            event(new PasswordReset($user));

            return redirect()->route('login')->with('notice', 'Password reset successful.');
        }

        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                    ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('notice', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
