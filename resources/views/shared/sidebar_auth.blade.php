<div class="sidebar-user" role="navigation" aria-label="User">
  @auth
    <div class="user-top">
      <div class="user-avatar">{{ strtoupper(substr(auth()->user()->first_name ?: auth()->user()->email, 0, 1)) }}</div>
      <div class="user-info">
        <div class="user-name">{{ auth()->user()->full_name }}</div>
        <div class="user-email">{{ auth()->user()->email }}</div>
      </div>
    </div>
    <div class="user-actions">
      @if (Route::has('profile.edit'))
        <a href="{{ route('profile.edit') }}" class="button">Profile</a>
      @endif
      <form action="{{ route('logout') }}" method="POST" class="logout-form" style="display:inline">
        @csrf
        <button type="submit" class="button primary">Logout</button>
      </form>
    </div>
    <div style="margin-top:8px">
      @if(auth()->user()->isServiceProvider())
        <a href="{{ route('myservice') }}" class="button">My Service</a>
      @endif
      @if(auth()->user()->isShopowner())
        <a href="{{ route('myshop') }}" class="button">My Shop</a>
      @endif
    </div>
  @else
    <div class="auth-links">
      <a href="{{ route('login') }}" class="button">Login</a>
      <a href="{{ route('register') }}" class="button primary">Sign Up</a>
    </div>
  @endauth
</div>
