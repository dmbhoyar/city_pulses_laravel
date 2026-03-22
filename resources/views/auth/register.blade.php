@extends('layouts.app')

@section('content')
<div class="devise-panel">
  <h2>Sign up</h2>

  <form action="{{ route('register') }}" method="POST" class="devise-form">
    @csrf
    @php $finalRedirectTo = old('redirect_to', $redirectTo ?? ''); @endphp
    @if(!empty($finalRedirectTo))
      <input type="hidden" name="redirect_to" value="{{ $finalRedirectTo }}">
    @endif

    <div class="field">
      <label for="first_name">First name</label>
      <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name">
    </div>

    <div class="field">
      <label for="last_name">Last name</label>
      <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name">
    </div>

    <div class="field">
      <label for="mobile_number">Mobile number</label>
      <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" autocomplete="tel">
    </div>

    <div class="field">
      <label for="role">Register as</label>
      @if(($lockSellerRole ?? false) === true)
        <input type="text" value="Seller" readonly>
        <input type="hidden" name="role" value="seller">
      @else
        <select id="role" name="role" class="form-control">
          <option value="normal" {{ old('role','normal') === 'normal' ? 'selected' : '' }}>Normal</option>
          <option value="seller" {{ old('role') === 'seller' ? 'selected' : '' }}>Seller</option>
          <option value="shopowner" {{ old('role') === 'shopowner' ? 'selected' : '' }}>Shop Owner</option>
          <option value="shopworker" {{ old('role') === 'shopworker' ? 'selected' : '' }}>Shop Worker</option>
          <option value="service_provider" {{ old('role') === 'service_provider' ? 'selected' : '' }}>Service Provider</option>
        </select>
      @endif
    </div>

    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="email">
    </div>

    <div class="field">
      <label for="password">Password</label>
      <p><em>(8 characters minimum)</em></p>
      <input type="password" id="password" name="password" autocomplete="new-password">
    </div>

    <div class="field">
      <label for="password_confirmation">Password confirmation</label>
      <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
    </div>

    <div class="actions">
      <button type="submit" class="button primary">Sign up</button>
    </div>
  </form>

  <div class="devise-links">
    <a href="{{ route('login') }}">Already have an account? Log in</a>
  </div>
</div>
@endsection
