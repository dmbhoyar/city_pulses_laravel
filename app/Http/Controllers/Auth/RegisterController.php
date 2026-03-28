<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function resendOtp(Request $request, EmailOtpService $otpService)
    {
        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register')->with('error', 'Please fill registration form first.');
        }
        $otpRecipient = !empty($pending['email']) ? $pending['email'] : $pending['mobile_number'];
        try {
            $otpService->sendOtp($otpRecipient, 'register');
            return back()->with('notice', 'OTP resent successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend registration OTP: ' . $e->getMessage());
            return back()->withErrors(['otp_code' => 'Failed to resend OTP. Please try again.']);
        }
    }
    public function showRegistrationForm()
    {
        $lockSellerRole = request()->boolean('seller');
        $redirectTo = (string) request()->query('redirect_to', '');

        return view('auth.register', compact('lockSellerRole', 'redirectTo'));
    }

    public function register(Request $request, EmailOtpService $otpService)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'email'         => 'required|string|email|max:255|unique:users',
            'mobile_number' => 'required|regex:/^[0-9]{10}$/|unique:users,mobile_number',
            'role'          => 'required|in:user,shopowner,shopworker,service_provider,seller',
            'password'      => 'required|string|min:8|confirmed',
            'redirect_to'   => 'nullable|string|max:2048',
        ]);

        // Store registration data in session (except password)
        $registrationData = $validated;
        $registrationData['password'] = bcrypt($validated['password']);
        session(['pending_registration' => $registrationData]);

        // Decide OTP channel: prefer email if free, else mobile
        $otpRecipient = null;
        if (!empty($validated['email'])) {
            $otpRecipient = $validated['email'];
        } elseif (!empty($validated['mobile_number'])) {
            $otpRecipient = $validated['mobile_number'];
        }
        if ($otpRecipient) {
            $otpService->sendOtp($otpRecipient, 'register');
        }

        // Redirect to OTP verification page
        return redirect()->route('register.otp.form')->with('notice', 'OTP sent to your email or mobile. Enter OTP to complete registration.');
    }

    public function showOtpForm()
    {
        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register')->with('error', 'Please fill registration form first.');
        }
        return view('auth.register_otp');
    }

    public function verifyOtp(Request $request, EmailOtpService $otpService)
    {
        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register')->with('error', 'Please fill registration form first.');
        }
        $request->validate(['otp_code' => 'required|string|size:6']);
        $otpRecipient = !empty($pending['email']) ? $pending['email'] : $pending['mobile_number'];
        $meta = [];
        if (!$otpService->verifyOtp($otpRecipient, 'register', $request->otp_code, $meta)) {
            return back()->withErrors(['otp_code' => 'Invalid or expired OTP.']);
        }
        // Create user
        $user = User::create([
            'first_name'    => $pending['first_name'],
            'last_name'     => $pending['last_name'] ?? '',
            'email'         => $pending['email'],
            'mobile_number' => $pending['mobile_number'],
            'password'      => $pending['password'],
            'role'          => $pending['role'],
        ]);
        event(new Registered($user));
        Auth::login($user);
        session()->forget('pending_registration');
        $redirectTo = trim((string) ($pending['redirect_to'] ?? ''));
        if ($redirectTo !== '') {
            return redirect()->to($redirectTo)->with('notice', 'Registration successful!');
        }
        return redirect()->route('home')->with('notice', 'Registration successful!');
    }
}
