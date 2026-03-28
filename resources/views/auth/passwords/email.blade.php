@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>{{ __('ui.forgot_your_password') }}</h2>
  <p>Enter your mobile number to receive OTP and reset your password.</p>

  <form action="{{ route('password.email') }}" method="POST" class="devise-form">
    @csrf
    <div class="field">
      <label for="mobile_number">{{ __('ui.mobile_number') }}</label>
      <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" autofocus autocomplete="tel" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" placeholder="Enter 10-digit mobile number">
    </div>

    <div class="actions">
      <button type="submit" class="button primary">Send OTP</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('login') }}">{{ __('ui.log_in') }}</a>
    &nbsp;|&nbsp;
    <a href="{{ route('register') }}">{{ __('ui.sign_up') }}</a>
  </div>
</div>
@endsection
