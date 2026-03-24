@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>{{ __('ui.change_your_password') }}</h2>

  <form action="{{ route('password.update') }}" method="POST" class="devise-form">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="field">
      <label for="email">{{ __('ui.email') }}</label>
      <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" autofocus autocomplete="email">
    </div>

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
