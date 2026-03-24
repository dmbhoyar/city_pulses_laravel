@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>{{ __('ui.log_in') }}</h2>

  <form action="{{ route('login') }}" method="POST" class="devise-form">
    @csrf
    @php $finalRedirectTo = old('redirect_to', $redirectTo ?? ''); @endphp
    @if(!empty($finalRedirectTo))
      <input type="hidden" name="redirect_to" value="{{ $finalRedirectTo }}">
    @endif
    <div class="field">
      <label for="email">{{ __('ui.email') }}</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="email">
    </div>

    <div class="field">
      <label for="password">{{ __('ui.password') }}</label>
      <div class="password-input-wrap">
        <input type="password" id="password" name="password" autocomplete="current-password">
        <button type="button" class="password-toggle" data-target="password" data-show-label="{{ __('ui.show_password') }}" data-hide-label="{{ __('ui.hide_password') }}" aria-label="{{ __('ui.show_password') }}" title="{{ __('ui.show_password') }}">
          <span aria-hidden="true">&#128065;</span>
        </button>
      </div>
    </div>

    <div class="field">
      <label><input type="checkbox" name="remember"> {{ __('ui.remember_me') }}</label>
    </div>

    <div class="actions">
      <button type="submit" class="button primary">{{ __('ui.log_in') }}</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('password.request') }}">{{ __('ui.forgot_your_password') }}</a>
    &nbsp;|&nbsp;
    <a href="{{ route('register', array_filter(['seller' => ($lockSellerRole ?? false) ? 1 : null, 'redirect_to' => $finalRedirectTo])) }}">{{ __('ui.sign_up') }}</a>
  </div>
</div>

<style>
  .password-input-wrap {
    position: relative;
  }

  .password-input-wrap input {
    padding-right: 2.5rem;
  }

  .password-toggle {
    position: absolute;
    top: 50%;
    right: 0.5rem;
    transform: translateY(-50%);
    border: 0;
    background: transparent;
    cursor: pointer;
    line-height: 1;
    padding: 0.2rem;
  }
</style>

<script>
  (function () {
    document.querySelectorAll('.password-toggle').forEach(function (button) {
      button.addEventListener('click', function () {
        var targetId = button.getAttribute('data-target');
        var input = document.getElementById(targetId);
        if (!input) return;

        var isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        var showLabel = button.getAttribute('data-show-label') || 'Show password';
        var hideLabel = button.getAttribute('data-hide-label') || 'Hide password';
        var nextLabel = isHidden ? hideLabel : showLabel;
        button.setAttribute('aria-label', nextLabel);
        button.setAttribute('title', nextLabel);
      });
    });
  })();
</script>
@endsection
