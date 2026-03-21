<style>
  .sidebar-user{border-top:1px solid #e6edf8;padding:10px 12px;background:#fff}
  .su-card{display:flex;align-items:center;gap:10px;padding:8px 10px;background:linear-gradient(135deg,#2f4e74 0%,#3d6494 100%);border-radius:10px;margin-bottom:8px}
  .su-avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#FF6B00,#e05a00);color:#fff;font-weight:800;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid rgba(255,255,255,.3)}
  .su-info{min-width:0;flex:1}
  .su-name{font-weight:700;font-size:13px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .su-email{font-size:11px;color:rgba(255,255,255,.65);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .su-links{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:6px}
  .su-links a,.su-links button{flex:1;min-width:0;text-align:center;font-size:12px;padding:6px 8px;border-radius:7px;font-weight:600;border:1px solid #d6e3f5;background:#f4f8ff;color:#2f4e74;cursor:pointer;text-decoration:none;white-space:nowrap}
  .su-links a:hover,.su-links button:hover{background:#e6f0ff;border-color:#4a90d9}
  .su-links .ws-btn{background:linear-gradient(135deg,#2f4e74,#4a90d9);color:#fff;border-color:transparent}
  .su-links .ws-btn:hover{background:linear-gradient(135deg,#253a5a,#3a7bc8)}
  .su-logout{width:100%;padding:6px;border-radius:7px;background:#fff0f0;border:1px solid #f5c6c6;color:#c0392b;font-size:12px;font-weight:700;cursor:pointer}
  .su-logout:hover{background:#ffe0e0}
  .su-auth-links{display:flex;gap:6px}
  .su-auth-links a{flex:1;text-align:center;padding:8px;border-radius:8px;font-weight:700;font-size:13px;text-decoration:none}
  .su-auth-links .su-login{background:#f4f8ff;color:#2f4e74;border:1px solid #c5d9f5}
  .su-auth-links .su-signup{background:linear-gradient(135deg,#2f4e74,#4a90d9);color:#fff;border:none}
</style>

<div class="sidebar-user" role="navigation" aria-label="User">
  @auth
    @php
      $user = auth()->user();
      $role = strtolower((string) ($user->role ?? ''));
      $isSuperadmin = $user->isSuperadmin();
      $showMyService = !$isSuperadmin && $user->isServiceProvider();
      $showMyShop    = !$isSuperadmin && $user->isShopowner();
      $showDashboard = !$isSuperadmin && ($showMyService || $showMyShop);
    @endphp

    {{-- User identity card --}}
    <div class="su-card">
      <div class="su-avatar">{{ strtoupper(substr($user->first_name ?: $user->email, 0, 1)) }}</div>
      <div class="su-info">
        <div class="su-name">{{ $user->full_name }}</div>
        <div class="su-email">{{ $user->email }}</div>
      </div>
    </div>

    {{-- Quick workspace jump links --}}
    @if($showMyService || $showMyShop || $showDashboard)
    <div class="su-links">
      @if($showMyService)
        <a href="{{ route('myservice') }}" class="ws-btn">My Service</a>
      @endif
      @if($showMyShop)
        <a href="{{ route('myshop') }}" class="ws-btn">My Shop</a>
      @endif
      @if($showDashboard)
        <a href="{{ route('shop_dashboard') }}">📊 Dashboard</a>
      @endif
      @if(Route::has('profile.edit'))
        <a href="{{ route('profile.edit') }}">Profile</a>
      @endif
    </div>
    @endif

    {{-- Logout --}}
    <form action="{{ route('logout') }}" method="POST" style="margin:0">
      @csrf
      <button type="submit" class="su-logout">↩ Logout</button>
    </form>
  @else
    <div class="su-auth-links">
      <a href="{{ route('login') }}" class="su-login">Login</a>
      <a href="{{ route('register') }}" class="su-signup">Sign Up</a>
    </div>
  @endauth
</div>
