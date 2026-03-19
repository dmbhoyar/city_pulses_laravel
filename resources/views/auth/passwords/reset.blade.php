@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>Change your password</h2>

  <form action="{{ route('password.update') }}" method="POST" class="devise-form">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" autofocus autocomplete="email">
    </div>

    <div class="field">
      <label for="password">New password</label>
      <input type="password" id="password" name="password" autocomplete="new-password">
    </div>

    <div class="field">
      <label for="password_confirmation">Confirm new password</label>
      <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
    </div>

    <div class="actions">
      <button type="submit" class="button primary">Change my password</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('login') }}">Log in</a>
    &nbsp;|&nbsp;
    <a href="{{ route('register') }}">Sign up</a>
  </div>
</div>
@endsection
