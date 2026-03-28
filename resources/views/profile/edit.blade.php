@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>My Profile</h2>
  <p>Update your details. Mobile number change requires OTP verification on your mobile number.</p>

  <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="devise-form">
    @csrf

    <div class="field">
      <label for="first_name">{{ __('ui.first_name') }}</label>
      <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
    </div>

    <div class="field">
      <label for="last_name">{{ __('ui.last_name') }}</label>
      <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
    </div>

    <div class="field">
      <label for="email">{{ __('ui.email') }}</label>
      <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>

    <div class="field">
      <label for="mobile_number">{{ __('ui.mobile_number') }}</label>
      <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" placeholder="Enter 10-digit mobile number">
      <p><em>Change mobile and submit once to receive OTP on mobile, then submit again with OTP.</em></p>
    </div>

    <div class="field">
      <label for="otp_code">Mobile OTP (for mobile change)</label>
      <input type="text" id="otp_code" name="otp_code" value="{{ old('otp_code') }}" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="Enter 6-digit OTP">
    </div>

    <div class="field">
      <label for="avatar">Profile Photo</label>
      <input type="file" id="avatar" name="avatar" accept="image/*">
      @if(!empty($user->avatar_url))
        <p><img src="{{ $user->avatar_url }}" alt="Current avatar" style="max-width:72px;max-height:72px;border-radius:50%;border:1px solid #ddd;"></p>
      @endif
    </div>

    <hr>

    <div class="field">
      <label for="current_password">Current Password (required to change password)</label>
      <input type="password" id="current_password" name="current_password" autocomplete="current-password">
    </div>

    <div class="field">
      <label for="password">New Password</label>
      <input type="password" id="password" name="password" autocomplete="new-password">
    </div>

    <div class="field">
      <label for="password_confirmation">Confirm New Password</label>
      <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
    </div>

    <div class="actions">
      <button type="submit" class="button primary">Save Profile</button>
    </div>
  </form>
</div>
@endsection
