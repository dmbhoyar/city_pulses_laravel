@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>Forgot your password?</h2>

  <form action="{{ route('password.email') }}" method="POST" class="devise-form">
    @csrf
    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="email">
    </div>

    <div class="actions">
      <button type="submit" class="button primary">Send me reset password instructions</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('login') }}">Log in</a>
    &nbsp;|&nbsp;
    <a href="{{ route('register') }}">Sign up</a>
  </div>
</div>
@endsection
