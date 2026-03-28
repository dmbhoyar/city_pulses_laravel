<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request, EmailOtpService $otpService)
    {
        $request->validate(['mobile_number' => 'required|regex:/^[0-9]{10}$/']);

        $mobileNumber = trim((string) $request->mobile_number);

        if (User::where('mobile_number', $mobileNumber)->exists()) {
            $otpService->sendOtp($mobileNumber, 'password_reset');
        }

        return redirect()->route('password.otp.form', ['mobile_number' => $mobileNumber])
            ->with('notice', 'If your mobile number exists, an OTP has been sent.');
    }
}
