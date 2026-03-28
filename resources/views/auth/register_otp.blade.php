@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>{{ __('ui.sign_up') }}</h2>
  <p>Enter the OTP sent to your email or mobile to complete registration.</p>
  <form action="{{ route('register.otp.verify') }}" method="POST" class="devise-form">
    @csrf
    <div class="field">
      <label for="otp_code">OTP</label>
      <input type="text" id="otp_code" name="otp_code" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="Enter 6-digit OTP" required>
      @error('otp_code')<div style="color:#c34141;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
    </div>
    <div class="actions" style="display:flex;gap:10px;align-items:center;">
      <button type="submit" class="button primary">Verify & Complete Registration</button>
      <form action="{{ route('register.otp.resend') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="button secondary" style="background:#eee;color:#333;">Resend OTP</button>
      </form>
    </div>
    @if(session('notice'))
      <div style="color:#2d7a2d;font-size:13px;margin-top:8px">{{ session('notice') }}</div>
    @endif
  </form>
  <div class="devise-links">
    <a href="{{ route('register') }}">Back to Registration</a>
  </div>
</div>
@endsection
