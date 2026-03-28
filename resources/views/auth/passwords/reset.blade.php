@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>{{ __('ui.change_your_password') }}</h2>
  @php($isOtpMode = (bool) ($otpMode ?? false))
  @if($isOtpMode)
    <p>Use the OTP sent to your mobile number and set a new password.</p>
  @endif

  <form action="{{ route('password.update') }}" method="POST" class="devise-form">
    @csrf
    @if(!$isOtpMode)
      <input type="hidden" name="token" value="{{ $token }}">
    @endif

    @if($isOtpMode)
      <div class="field">
        <label for="mobile_number">{{ __('ui.mobile_number') }}</label>
        <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $mobile_number ?? '') }}" autofocus autocomplete="tel" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" placeholder="Enter 10-digit mobile number">
      </div>
    @else
      <div class="field">
        <label for="email">{{ __('ui.email') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" autofocus autocomplete="email">
      </div>
    @endif

    @if($isOtpMode)
      <div class="field">
        <label for="otp_code">Mobile OTP</label>
        <input type="text" id="otp_code" name="otp_code" value="{{ old('otp_code') }}" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="Enter 6-digit OTP">
      </div>
    @endif

    <div class="field">
      <label for="password">{{ __('ui.new_password') }}</label>
      <input type="password" id="password" name="password" autocomplete="new-password">
    </div>

    <div class="field">
      <label for="password_confirmation">{{ __('ui.confirm_new_password') }}</label>
      <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
    </div>

    <div class="actions">
      <button type="submit" class="button primary">{{ __('ui.change_my_password') }}</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('login') }}">{{ __('ui.log_in') }}</a>
    &nbsp;|&nbsp;
    <a href="{{ route('register') }}">{{ __('ui.sign_up') }}</a>
  </div>
</div>
@endsection
