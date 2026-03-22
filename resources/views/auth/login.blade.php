@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>Log in</h2>

  <form action="{{ route('login') }}" method="POST" class="devise-form">
    @csrf
    @php $finalRedirectTo = old('redirect_to', $redirectTo ?? ''); @endphp
    @if(!empty($finalRedirectTo))
      <input type="hidden" name="redirect_to" value="{{ $finalRedirectTo }}">
    @endif
    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="email">
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" autocomplete="current-password">
    </div>

    <div class="field">
      <label><input type="checkbox" name="remember"> Remember me</label>
    </div>

    <div class="actions">
      <button type="submit" class="button primary">Log in</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('password.request') }}">Forgot your password?</a>
    &nbsp;|&nbsp;
    <a href="{{ route('register', array_filter(['seller' => ($lockSellerRole ?? false) ? 1 : null, 'redirect_to' => $finalRedirectTo])) }}">Sign up</a>
  </div>
</div>
@endsection
