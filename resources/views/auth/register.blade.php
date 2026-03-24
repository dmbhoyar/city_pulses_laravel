@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>{{ __('ui.sign_up') }}</h2>

  <form action="{{ route('register') }}" method="POST" class="devise-form">
    @csrf
    @php $finalRedirectTo = old('redirect_to', $redirectTo ?? ''); @endphp
    @if(!empty($finalRedirectTo))
      <input type="hidden" name="redirect_to" value="{{ $finalRedirectTo }}">
    @endif

    <div class="field">
      <label for="first_name">{{ __('ui.first_name') }}</label>
      <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name">
    </div>

    <div class="field">
      <label for="last_name">{{ __('ui.last_name') }}</label>
      <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name">
    </div>

    <div class="field">
      <label for="mobile_number">{{ __('ui.mobile_number') }}</label>
      <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" autocomplete="tel">
    </div>

    <div class="field">
      <label for="role">{{ __('ui.register_as') }}</label>
      @if(($lockSellerRole ?? false) === true)
        <input type="text" value="{{ __('ui.seller') }}" readonly>
        <input type="hidden" name="role" value="seller">
      @else
        <select id="role" name="role" class="form-control">
          <option value="normal" {{ old('role','normal') === 'normal' ? 'selected' : '' }}>{{ __('ui.normal') }}</option>
          <option value="seller" {{ old('role') === 'seller' ? 'selected' : '' }}>{{ __('ui.seller') }}</option>
          <option value="shopowner" {{ old('role') === 'shopowner' ? 'selected' : '' }}>{{ __('ui.shop_owner') }}</option>
          <option value="shopworker" {{ old('role') === 'shopworker' ? 'selected' : '' }}>{{ __('ui.shop_worker') }}</option>
          <option value="service_provider" {{ old('role') === 'service_provider' ? 'selected' : '' }}>{{ __('ui.service_provider') }}</option>
        </select>
      @endif
    </div>

    <div class="field">
      <label for="email">{{ __('ui.email') }}</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="email">
    </div>

    <div class="field">
      <label for="password">{{ __('ui.password') }}</label>
      <p><em>({{ __('ui.password_min_characters') }})</em></p>
      <div class="password-input-wrap">
        <input type="password" id="password" name="password" autocomplete="new-password">
        <button type="button" class="password-toggle" data-target="password" data-show-label="{{ __('ui.show_password') }}" data-hide-label="{{ __('ui.hide_password') }}" aria-label="{{ __('ui.show_password') }}" title="{{ __('ui.show_password') }}">
          <span aria-hidden="true">&#128065;</span>
        </button>
      </div>
    </div>

    <div class="field">
      <label for="password_confirmation">{{ __('ui.password_confirmation') }}</label>
      <div class="password-input-wrap">
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
        <button type="button" class="password-toggle" data-target="password_confirmation" data-show-label="{{ __('ui.show_password') }}" data-hide-label="{{ __('ui.hide_password') }}" aria-label="{{ __('ui.show_password') }}" title="{{ __('ui.show_password') }}">
          <span aria-hidden="true">&#128065;</span>
        </button>
      </div>
    </div>

    <div class="actions">
      <button type="submit" class="button primary">{{ __('ui.sign_up') }}</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('login') }}">{{ __('ui.already_have_account_log_in') }}</a>
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
