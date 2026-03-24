@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>{{ __('ui.forgot_your_password') }}</h2>

  <form action="{{ route('password.email') }}" method="POST" class="devise-form">
    @csrf
    <div class="field">
      <label for="email">{{ __('ui.email') }}</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="email">
    </div>

    <div class="actions">
      <button type="submit" class="button primary">{{ __('ui.send_reset_password_instructions') }}</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('login') }}">{{ __('ui.log_in') }}</a>
    &nbsp;|&nbsp;
    <a href="{{ route('register') }}">{{ __('ui.sign_up') }}</a>
  </div>
</div>
@endsection
