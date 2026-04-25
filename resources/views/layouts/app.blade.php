<!DOCTYPE html>
<html>
<head>
  @php
    $seoTitle = trim($__env->yieldContent('title')) ?: 'AajchaOffer - Aajcha Bhav, Aajche Offers, Todays Rate';
    $seoDescription = trim($__env->yieldContent('meta_description')) ?: 'AajchaOffer gives daily city updates for aajcha bhav, aajche offers, local market prices, jobs, farming, rents, buy & sell, and services.';
    $seoKeywords = trim($__env->yieldContent('meta_keywords')) ?: 'aajcha offer, aajcha bhav, aajche offers, todays rate, city pulses rate, local offers, market bhav';
    $canonicalUrl = url()->current();
  @endphp
  <title>{{ $seoTitle }}</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="{{ $seoDescription }}">
  <meta name="keywords" content="{{ $seoKeywords }}">
  <meta name="robots" content="index,follow,max-image-preview:large">
  <link rel="canonical" href="{{ $canonicalUrl }}">
  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ $seoTitle }}">
  <meta property="og:description" content="{{ $seoDescription }}">
  <meta property="og:url" content="{{ $canonicalUrl }}">
  <meta property="og:site_name" content="AajchaOffer">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $seoTitle }}">
  <meta name="twitter:description" content="{{ $seoDescription }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script type="application/ld+json">
    {!! json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'WebSite',
      'name' => 'AajchaOffer',
      'url' => url('/'),
      'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => url('/') . '?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
      ],
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
  </script>
  <script type="application/ld+json">
    {!! json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'Organization',
      'name' => 'AajchaOffer',
      'url' => url('/'),
      'logo' => asset('images/icons/about.svg'),
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
  </script>
  @php
    $appCssPath = public_path('css/application.css');
    $appCssVersion = is_file($appCssPath) ? (string) @filemtime($appCssPath) : (string) time();
  @endphp
  <link rel="stylesheet" href="{{ asset('css/application.css') }}?v={{ $appCssVersion }}">
  <style>
      @stack('styles')
    .global-top{position:sticky;top:0;z-index:120;background:rgba(255,248,240,.96);backdrop-filter:blur(14px);border-bottom:1px solid #F0E8DC;padding:.62rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:.7rem}
      /* Panel active item */
      .panel-list li.active-item{background:linear-gradient(90deg,#eef5ff,#f4f8ff);border-left:3px solid #2f4e74;padding-left:5px}
      .panel-list li.active-item .badge{background:#2f4e74 !important;color:#fff !important}
      .panel-list li.active-item .label,.panel-list li.active-item .label a{color:#2f4e74;font-weight:700}
    /* Mobile workspace sidebar: ensure panel content fills nicely */
    @media(max-width:900px){
      .panel-search{margin-bottom:.5rem}
      .panel-list li{padding:11px 8px}
      .panel-list .badge{width:28px;height:28px;font-size:.8rem;margin-right:9px}
      .panel-list .label,.panel-link{font-size:.88rem}
      .mobile-sidebar-head strong{font-size:.9rem}
    }
    .global-brand{display:flex;align-items:center;gap:.6rem;min-width:0}
    .global-badge{display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .global-logo{width:36px;height:36px;min-width:36px;min-height:36px;display:block;flex:0 0 auto}
    .global-copy strong{display:block;font-size:1.04rem;line-height:1.1}
    .global-wordmark{font-weight:800;letter-spacing:.1px}
    .global-wordmark .aajcha{color:#2e4a6f}
    .global-wordmark .offer{background:linear-gradient(135deg,#C368CA,#8E5BD6);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    .global-copy small{display:block;font-size:.74rem;color:#6B7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px}
    .global-right{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;justify-content:flex-end}
    .global-lang{display:flex;gap:2px;background:#F0E8DC;border-radius:18px;padding:2px}
    .global-lang button{border:0;background:transparent;color:#6B7280;font-size:.73rem;font-weight:700;padding:.2rem .55rem;border-radius:14px;cursor:pointer}
    .global-lang button.on{background:#FF6B00;color:#fff}
    .global-city-form{margin:0}
    .global-city{border:1.5px solid #FF6B00;border-radius:18px;padding:.3rem .58rem;background:#fff;color:#FF6B00;font-weight:700;font-size:.8rem;max-width:170px}
    .workspace-toggle-btn{display:inline-flex;align-items:center;justify-content:center;border:1px solid #FF6B00;border-radius:18px;padding:.4rem .9rem;background:#fff;color:#FF6B00;font-size:.78rem;font-weight:700;text-decoration:none;white-space:nowrap;margin-left:.5rem}
    .workspace-toggle-btn:hover{background:#FF6B0020}
    .mobile-city-btn{display:none;border:1.5px solid #FF6B00;border-radius:18px;padding:.3rem .62rem;background:#fff;color:#FF6B00;font-weight:700;font-size:.8rem;cursor:pointer}
    .mobile-city-modal{position:fixed;inset:0;background:rgba(18,26,40,.45);z-index:300;display:none;align-items:flex-start;justify-content:center;padding-top:76px}
    .mobile-city-modal.open{display:flex}
    .mobile-city-sheet{width:min(92vw,420px);max-height:74vh;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 24px 56px rgba(18,26,40,.3)}
    .mobile-city-head{display:flex;align-items:center;gap:8px;padding:10px;border-bottom:1px solid #e9eef8}
    .mobile-city-head input{flex:1;border:1px solid #d6e3f7;border-radius:8px;padding:8px 10px;font-size:14px}
    .mobile-city-close{border:1px solid #d5def0;background:#fff;color:#48648a;border-radius:8px;width:34px;height:34px;cursor:pointer}
    .mobile-city-list{max-height:58vh;overflow:auto;padding:8px}
    .mobile-city-item{width:100%;display:flex;align-items:center;justify-content:space-between;gap:8px;border:1px solid #e3ebf8;background:#fff;border-radius:8px;padding:9px 10px;margin-bottom:7px;font-size:14px;color:#31496c;text-align:left;cursor:pointer}
    .mobile-city-item.active{border-color:#FF6B00;background:#fff7ef;color:#c75f00;font-weight:700}
    .global-time{font-size:.72rem;color:#6B7280}
    .mobile-menu-btn{display:none;border:1px solid #d7dff0;background:#fff;border-radius:9px;width:34px;height:34px;align-items:center;justify-content:center;color:#365078;font-size:1.05rem;font-weight:700;cursor:pointer;line-height:1}
    .mobile-sidebar-overlay{display:none}
    .mobile-sidebar-head{display:none}
    .mobile-sidebar-brand{display:flex;align-items:center;gap:.5rem}
    .mobile-sidebar-brand .mobile-logo{width:26px;height:26px;display:block}
    .mobile-sidebar-brand .mobile-title{font-weight:700;font-size:.95rem;color:#334a68}
    .sidebar-panel{background:#f3f4f6 !important;border-right:1px solid #d9dde3 !important}
    .about-ruby-icon{display:flex;align-items:center;justify-content:flex-start;margin:4px 0 8px 0}
    .about-ruby-icon svg{display:block}
    .guide-bot{position:fixed;right:16px;bottom:16px;z-index:420;display:flex;flex-direction:column;align-items:flex-end;gap:10px}
    .guide-toggle{
      width:56px;height:56px;border:none;border-radius:50%;
      background:transparent;color:#fff;cursor:pointer;position:relative;
      box-shadow:none;display:flex;align-items:center;justify-content:center;
      transition:transform .18s ease;
      animation:guideFloat 2.2s ease-in-out infinite;
    }
    .guide-toggle:hover{transform:translateY(-2px)}
    .guide-toggle:focus-visible{outline:3px solid rgba(255,255,255,.72);outline-offset:2px}
    .guide-toggle-icon{width:52px;height:52px;display:block;filter:drop-shadow(0 5px 10px rgba(20,34,58,.28)) drop-shadow(0 0 10px rgba(255,153,0,.25))}
    .guide-toggle-dot{display:none}
    @keyframes guideFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-3px)}}
    .guide-card{width:min(92vw,360px);background:#fff;border:1px solid #d7e2f4;border-radius:14px;box-shadow:0 18px 42px rgba(27,44,77,.24);padding:12px;display:none}
    .guide-card.open{display:block}
    .guide-card::before{content:'';display:block;height:4px;border-radius:10px;background:linear-gradient(90deg,#2f9c8e,#63c1b7,#8e5bd6);margin:-12px -12px 10px}
    .guide-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:6px}
    .guide-head strong{font-size:14px;color:#2d4669}
    .guide-close{border:1px solid #d4dff2;background:#fff;color:#54709b;border-radius:8px;width:30px;height:30px;cursor:pointer}
    .guide-copy{font-size:12.5px;line-height:1.5;color:#5a7293;margin:0 0 10px}
    .guide-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px}
    .guide-list li{display:flex;align-items:center;justify-content:space-between;gap:10px;background:linear-gradient(180deg,#f9fbff 0%,#f3f8ff 100%);border:1px solid #dce8fa;border-radius:9px;padding:8px 9px}
    .guide-list span{font-size:12px;color:#35537a;font-weight:600;line-height:1.35}
    .guide-focus-btn{border:1px solid #c8d8f3;background:#fff;color:#2f5c97;border-radius:8px;font-size:11px;font-weight:700;padding:5px 9px;cursor:pointer;white-space:nowrap}
    .guide-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:10px}
    .guide-secondary,.guide-primary{border-radius:8px;padding:7px 11px;font-size:12px;font-weight:700;cursor:pointer}
    .guide-secondary{border:1px solid #d3dff2;background:#fff;color:#4a678f}
    .guide-primary{border:none;background:#2f9c8e;color:#fff}
    .guide-focus{outline:3px solid rgba(255,107,0,.6)!important;outline-offset:3px;border-radius:10px;animation:guidePulse 1.2s ease 2}
    @keyframes guidePulse{0%{box-shadow:0 0 0 0 rgba(255,107,0,.45)}100%{box-shadow:0 0 0 14px rgba(255,107,0,0)}}
    @media(max-width:700px){
      .global-logo{width:30px;height:30px;min-width:30px;min-height:30px}
      .global-copy small{display:none}
      .global-time{display:none}
      .global-city-form{display:none}
      .mobile-city-btn{display:inline-flex}
      .guide-bot{right:10px;bottom:10px}
      .guide-toggle{width:48px;height:48px}
      .guide-toggle-icon{width:44px;height:44px}
      .guide-card{width:min(95vw,330px);padding:10px}
      .guide-card::before{margin:-10px -10px 10px}
      .guide-list li{padding:7px 8px}
      /* Compact header on small screens */
      .global-top{padding:.45rem .6rem;gap:.35rem}
      .global-copy strong{font-size:.88rem}
      .global-lang{gap:1px;padding:2px 3px}
      .global-lang button{padding:.16rem .32rem;font-size:.66rem}
      .workspace-toggle-btn{padding:.28rem .55rem;font-size:.68rem;margin-left:.1rem;white-space:nowrap}
      .mobile-city-btn{padding:.22rem .38rem;font-size:.72rem}
    }
    @media(max-width:480px){
      .global-lang{display:none}
      .global-copy strong{font-size:.82rem}
      .workspace-toggle-btn{padding:.25rem .42rem;font-size:.65rem}
    }
    @media(max-width:360px){
      .global-wordmark{display:none}
    }
  </style>
@stack('scripts')
</head>
@php
  $routeShopParam = request()->route('shop');
  $routeShopId = is_object($routeShopParam)
    ? ($routeShopParam->id ?? null)
    : (is_numeric($routeShopParam) ? (int) $routeShopParam : null);
  $ownsRouteShop = auth()->check() && $routeShopId
    ? auth()->user()->shops()->where('id', $routeShopId)->exists()
    : false;
  $isOwnedServicePage = request()->routeIs('shops.show') && $ownsRouteShop && auth()->check() && auth()->user()->isServiceProvider();
  $isOwnedShopPage = request()->routeIs('shops.show') && $ownsRouteShop && auth()->check() && auth()->user()->isShopowner();

  $isMyServiceBody = request()->routeIs('myservice')
      || request()->routeIs('configure_myservice*')
      || request()->routeIs('workers_myservice')
      || request()->routeIs('create_worker_myservice')
      || request()->routeIs('update_worker_myservice')
      || request()->routeIs('worker_experience_myservice')
      || request()->routeIs('myservice_offer_*')
      || request()->routeIs('myservice_requests*')
      || request()->routeIs('myservice_experience')
    || request()->routeIs('myservice_idcard')
    || $isOwnedServicePage
    || (request()->routeIs('shop_dashboard') && auth()->check() && auth()->user()->isServiceProvider())
    || (request()->routeIs('subscriptions.*') && auth()->check() && auth()->user()->isServiceProvider());
  $isMyShopBody = request()->routeIs('myshop')
      || request()->routeIs('configure_myshop*')
      || request()->routeIs('myshop_requests*')
      || request()->routeIs('workers_myshop')
      || request()->routeIs('create_worker_myshop')
      || request()->routeIs('update_worker_myshop')
      || request()->routeIs('worker_experience_myshop')
      || request()->routeIs('myshop_offer_*')
      || request()->routeIs('myshop_experience')
    || request()->routeIs('myshop_idcard')
    || $isOwnedShopPage
    || (request()->routeIs('shop_dashboard') && auth()->check() && auth()->user()->isShopowner())
    || (request()->routeIs('subscriptions.*') && auth()->check() && auth()->user()->isShopowner());
  $isAdminBody = request()->routeIs('admin.*') && auth()->check() && auth()->user()->isSuperadmin();
  $isUserSubmissionsBody = request()->routeIs('user_submissions.*');
  $isInvoicesBody = request()->routeIs('invoices.*');
  $invoicesGroup = $isInvoicesBody && auth()->check()
    ? (auth()->user()->isServiceProvider() ? 'myservice' : 'myshop')
    : '';
  $bodyRouteGroup = $isAdminBody ? 'admin' : ($isMyServiceBody ? 'myservice' : ($isMyShopBody ? 'myshop' : ($isUserSubmissionsBody ? 'user_submissions' : ($invoicesGroup ?: ''))));
  $isWorkspaceRoute = $isAdminBody || $isMyServiceBody || $isMyShopBody || $isInvoicesBody;
  $hasWorkspaceRole = auth()->check() && (auth()->user()->isSuperadmin() || auth()->user()->isShopowner() || auth()->user()->isServiceProvider());
  $workspaceEntryUrl = auth()->check()
    ? (auth()->user()->isSuperadmin()
        ? route('admin.dashboard')
        : (auth()->user()->isShopowner()
            ? route('myshop')
            : (auth()->user()->isServiceProvider() ? route('myservice') : route('home'))))
    : route('home');
@endphp
<body data-user-role="{{ auth()->check() ? auth()->user()->role : 'guest' }}" data-path="{{ request()->path() }}" data-route-group="{{ $bodyRouteGroup }}">

<div class="layout" id="main-layout">
  {{-- Hide global main nav when user is inside a workspace route --}}
  @unless($isWorkspaceRoute)
  <aside class="iconbar" aria-hidden="false">
    <nav>
      <ul>
        <li class="icon-item {{ request()->routeIs('home') ? 'selected' : '' }}" data-key="home">
          <a href="{{ route('home') }}" class="icon-square" title="Home">
            <img src="{{ asset('images/icons/home.png') }}" alt="Home" width="24" height="24">
          </a>
          <span class="icon-name">{{ __('ui.home') }}</span>
        </li>
        <li class="icon-item {{ request()->routeIs('updates.*') ? 'selected' : '' }}" data-key="updates">
          <a href="{{ route('updates.index') }}" class="icon-square" title="Updates">
            <img src="{{ asset('images/icons/updates.png') }}" alt="Updates" width="24" height="24">
          </a>
          <span class="icon-name">{{ __('ui.updates') }}</span>
        </li>
        <li class="icon-item {{ request()->routeIs('jobs.*') ? 'selected' : '' }}" data-key="jobs">
          <a href="{{ route('jobs.index') }}" class="icon-square" title="Jobs">
            <img src="{{ asset('images/icons/jobs.jpeg') }}" alt="Jobs" width="24" height="24">
          </a>
          <span class="icon-name">{{ __('ui.jobs') }}</span>
        </li>
        <li class="icon-item {{ request()->routeIs('farming.*') ? 'selected' : '' }}" data-key="farming">
          <a href="{{ route('farming.index') }}" class="icon-square" title="Farming">
            <img src="{{ asset('images/icons/farming.png') }}" alt="Farming" width="24" height="24">
          </a>
          <span class="icon-name">{{ __('ui.farming') }}</span>
        </li>
        <li class="icon-item {{ request()->routeIs('rents.*') ? 'selected' : '' }}" data-key="rents">
          <a href="{{ route('rents.index') }}" class="icon-square" title="Rents">
            <img src="{{ asset('images/icons/rent.png') }}" alt="Rents" width="24" height="24">
          </a>
          <span class="icon-name">{{ __('ui.rents') }}</span>
        </li>
        <li class="icon-item {{ request()->routeIs('buy.*') ? 'selected' : '' }}" data-key="buy">
          <a href="{{ route('buy.index') }}" class="icon-square" title="Buy &amp; Sell">
            <img src="{{ asset('images/icons/buy.png') }}" alt="Buy & Sell" width="24" height="24">
          </a>
          <span class="icon-name">{{ __('ui.buy') }}</span>
        </li>
        <li class="icon-item {{ request()->routeIs('services.*') ? 'selected' : '' }}" data-key="services">
          <a href="{{ route('services.index') }}" class="icon-square" title="Services">
            <img src="{{ asset('images/icons/services.png') }}" alt="Services" width="24" height="24">
          </a>
          <span class="icon-name">{{ __('ui.services') }}</span>
        </li>
        <li class="icon-item {{ request()->routeIs('shortsplay') ? 'selected' : '' }}" data-key="shortsplay">
          <a href="{{ route('shortsplay') }}" class="icon-square" title="ShortsPlay">
            <svg viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M4 8l4-4h8l4 4-8 12L4 8z" fill="#e23b57"/>
              <path d="M8 4l4 4 4-4" fill="none" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M4 8h16" fill="none" stroke="#b91f3b" stroke-width="1.1"/>
            </svg>
          </a>
          <span class="icon-name">SHORTSPLAY</span>
        </li>
        <li class="icon-item {{ request()->routeIs('about') ? 'selected' : '' }}" data-key="about">
          <a href="{{ route('about') }}" class="icon-square" title="About Us">
            <img src="{{ asset('images/icons/about.svg') }}" alt="About Us" width="24" height="24">
          </a>
          <span class="icon-name">{{ __('ui.about_us') }}</span>
        </li>
      </ul>
    </nav>
    <div class="iconbar-auth-placeholder" style="height:86px"></div>
    <div class="collapse">
      <button class="collapse-btn" id="collapse-btn">›</button>
    </div>
  </aside>
  @endunless

  <aside class="sidebar-panel">
    <div class="sidebar-brand">
      @include('shared.brand_logo', ['className' => 'sidebar-brand-logo', 'title' => 'AajchaOffer logo'])
      <div class="sidebar-brand-copy">
        <strong><span class="aajcha">Aajcha</span><span class="offer">Offer</span></strong>
        <small>Aajcha bhav, aajcha offer</small>
      </div>
    </div>

    <div class="mobile-sidebar-head">
      <div class="mobile-sidebar-brand">
        @include('shared.brand_logo', ['className' => 'mobile-logo', 'title' => 'AajchaOffer logo'])
        <span class="mobile-title">{{ __('ui.menu') }}</span>
      </div>
      <button type="button" id="mobile-menu-close" aria-label="{{ __('ui.close_menu') }}">✕</button>
    </div>
    <div class="panel-search">
      <input type="text" placeholder="{{ __('ui.search_menu') }}" />
    </div>

    <div class="mobile-sections" aria-label="{{ __('ui.all_sections') }}" @if($isWorkspaceRoute) style="display:none" @endif>
      <a href="{{ route('home') }}" class="mobile-sec-link {{ request()->routeIs('home') ? 'active' : '' }}">🏠 {{ __('ui.home') }}</a>
      <a href="{{ route('updates.index') }}" class="mobile-sec-link {{ request()->routeIs('updates.*') ? 'active' : '' }}">📰 {{ __('ui.updates') }}</a>
      <a href="{{ route('jobs.index') }}" class="mobile-sec-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">💼 {{ __('ui.jobs') }}</a>
      <a href="{{ route('farming.index') }}" class="mobile-sec-link {{ request()->routeIs('farming.*') ? 'active' : '' }}">🌾 {{ __('ui.farming') }}</a>
      <a href="{{ route('rents.index') }}" class="mobile-sec-link {{ request()->routeIs('rents.*') ? 'active' : '' }}">🏘️ {{ __('ui.rents') }}</a>
      <a href="{{ route('buy.index') }}" class="mobile-sec-link {{ request()->routeIs('buy.*') ? 'active' : '' }}">🛒 {{ __('ui.buy') }}</a>
      <a href="{{ route('services.index') }}" class="mobile-sec-link {{ request()->routeIs('services.*') ? 'active' : '' }}">🛠️ {{ __('ui.services') }}</a>
      <a href="{{ route('shortsplay') }}" class="mobile-sec-link {{ request()->routeIs('shortsplay') ? 'active' : '' }}">💎 ShortsPlay</a>
      <a href="{{ route('about') }}" class="mobile-sec-link {{ request()->routeIs('about') ? 'active' : '' }}">ℹ️ {{ __('ui.about_us') }}</a>
    </div>

    <div class="sidebar-content">
      @php
        $ctrl = request()->route()?->getActionMethod() ? class_basename(request()->route()->getController()) : '';
        $panelRouteShopParam = request()->route('shop');
        $panelRouteShopId = is_object($panelRouteShopParam)
          ? ($panelRouteShopParam->id ?? null)
          : (is_numeric($panelRouteShopParam) ? (int) $panelRouteShopParam : null);
        $panelOwnsRouteShop = auth()->check() && $panelRouteShopId
          ? auth()->user()->shops()->where('id', $panelRouteShopId)->exists()
          : false;
        $panelIsOwnedServicePage = request()->routeIs('shops.show') && $panelOwnsRouteShop && auth()->check() && auth()->user()->isServiceProvider();
        $panelIsOwnedShopPage = request()->routeIs('shops.show') && $panelOwnsRouteShop && auth()->check() && auth()->user()->isShopowner();
        $panelIsSuperadmin = auth()->check() && auth()->user()->isSuperadmin();

        $isMyService = !$panelIsSuperadmin && (request()->routeIs('myservice')
          || request()->routeIs('configure_myservice*')
          || request()->routeIs('workers_myservice')
          || request()->routeIs('create_worker_myservice')
          || request()->routeIs('update_worker_myservice')
          || request()->routeIs('worker_experience_myservice')
          || request()->routeIs('myservice_offer_*')
          || request()->routeIs('myservice_requests*')
          || request()->routeIs('myservice_experience')
          || request()->routeIs('myservice_idcard')
          || $panelIsOwnedServicePage
          || ($isInvoicesBody && auth()->check() && auth()->user()->isServiceProvider())
          || (request()->routeIs('shop_dashboard') && auth()->check() && auth()->user()->isServiceProvider())
          || (request()->routeIs('subscriptions.*') && auth()->check() && auth()->user()->isServiceProvider()));
        $isMyShop = !$panelIsSuperadmin && (request()->routeIs('myshop')
          || request()->routeIs('configure_myshop*')
          || request()->routeIs('myshop_requests*')
          || request()->routeIs('workers_myshop')
          || request()->routeIs('create_worker_myshop')
          || request()->routeIs('update_worker_myshop')
          || request()->routeIs('worker_experience_myshop')
          || request()->routeIs('myshop_offer_*')
          || request()->routeIs('myshop_experience')
          || request()->routeIs('myshop_idcard')
          || $panelIsOwnedShopPage
          || ($isInvoicesBody && auth()->check() && auth()->user()->isShopowner())
          || (request()->routeIs('shop_dashboard') && auth()->check() && auth()->user()->isShopowner())
          || (request()->routeIs('subscriptions.*') && auth()->check() && auth()->user()->isShopowner()));

        $myServiceShop = auth()->check() ? auth()->user()->shops()->first() : null;
        $myServicePageUrl = $myServiceShop
          ? route('shops.public', ['publicSlug' => $myServiceShop->public_page_slug])
          : route('myservice');
        $myServiceUrlEncoded = urlencode($myServicePageUrl);
        $myServiceShareText = 'Check my service page: ' . $myServicePageUrl;
        $myServiceShareTextEncoded = urlencode($myServiceShareText);
        $myServiceWhatsappShareUrl = "https://wa.me/?text={$myServiceShareTextEncoded}";
        $myServiceFacebookShareUrl = "https://www.facebook.com/sharer/sharer.php?u={$myServiceUrlEncoded}";
        $myServiceXShareUrl = "https://twitter.com/intent/tweet?url={$myServiceUrlEncoded}&text={$myServiceShareTextEncoded}";
        $myServiceTelegramShareUrl = "https://t.me/share/url?url={$myServiceUrlEncoded}&text={$myServiceShareTextEncoded}";
      @endphp
      @if(request()->routeIs('about'))
        <div class="about-ruby-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 8l4-4h8l4 4-8 12L4 8z" fill="#e23b57"/>
            <path d="M8 4l4 4 4-4" fill="none" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 8h16" fill="none" stroke="#b91f3b" stroke-width="1.1"/>
          </svg>
        </div>
      @endif
      <div class="panel-title {{ request()->routeIs('home') || $isMyService || $isMyShop || $panelIsSuperadmin ? 'active' : '' }}">
        {{ request()->routeIs('about') ? __('ui.about_us') : ($panelIsSuperadmin ? __('ui.admin_panel') : ($isMyService ? __('ui.my_service') : ($isMyShop ? __('ui.my_shop') : __('ui.home')))) }}
      </div>
      <ul class="panel-list">
        @if(request()->routeIs('about'))
          <li><a href="{{ route('about') }}" class="panel-link">{{ __('ui.our_story') }}</a></li>
          <li><a href="{{ route('about') }}#contact" class="panel-link">{{ __('ui.contact') }}</a></li>
        @elseif($panelIsSuperadmin)
          <li><a href="{{ route('admin.dashboard') }}" class="panel-link">{{ __('ui.dashboard') }}</a></li>
          <li><a href="{{ route('admin.users.index') }}" class="panel-link">{{ __('ui.users') }}</a></li>
          <li><a href="{{ route('admin.shops.index') }}" class="panel-link">{{ __('ui.shops') }}</a></li>
          <li><a href="{{ route('admin.subscriptions.index') }}" class="panel-link">{{ __('ui.subscriptions') }}</a></li>
          <li><a href="{{ route('admin.settings.index') }}" class="panel-link">{{ __('ui.settings') }}</a></li>
        @elseif($isMyService)
          <li><a href="{{ route('shop_dashboard') }}" class="panel-link">{{ __('ui.dashboard') }}</a></li>
          <li><a href="{{ route('configure_myservice') }}" class="panel-link">{{ __('ui.configure_service') }}</a></li>
          <li><a href="{{ route('workers_myservice') }}" class="panel-link">{{ __('ui.workers') }}</a></li>
          <li><a href="{{ route('myservice_requests') }}" class="panel-link">{{ __('ui.client_requests') }}</a></li>
          <li><a href="{{ route('myservice_offer_new') }}" class="panel-link">{{ __('ui.offers') }}</a></li>
          <li><a href="{{ route('invoices.index') }}" class="panel-link">📄 {{ __('ui.my_invoices') }}</a></li>
          <li><a href="{{ route('invoices.settings') }}" class="panel-link">⚙ {{ __('ui.invoice_settings') }}</a></li>
          <li><a href="{{ route('myservice_experience') }}" class="panel-link">{{ __('ui.experience_letter') }}</a></li>
          <li><a href="{{ route('myservice_idcard') }}" class="panel-link">{{ __('ui.id_card') }}</a></li>
          <li><a href="{{ route('subscriptions.new') }}" class="panel-link">{{ __('ui.subscription') }}</a></li>
          <li><a href="{{ $myServicePageUrl }}" class="panel-link" target="_blank" rel="noopener">{{ __('ui.my_page') }} ↗</a></li>
        @elseif($isMyShop)
          <li><a href="{{ route('myshop') }}" class="panel-link">{{ __('ui.shop_home') }}</a></li>
          <li><a href="{{ route('configure_myshop') }}" class="panel-link">{{ __('ui.configure_shop') }}</a></li>
          <li><a href="{{ route('workers_myshop') }}" class="panel-link">{{ __('ui.workers') }}</a></li>
          <li><a href="{{ route('myshop_requests') }}" class="panel-link">{{ __('ui.client_requests') }}</a></li>
          <li><a href="{{ route('myshop_offer_new') }}" class="panel-link">{{ __('ui.offers') }}</a></li>
          <li><a href="{{ route('invoices.index') }}" class="panel-link">📄 {{ __('ui.my_invoices') }}</a></li>
          <li><a href="{{ route('invoices.settings') }}" class="panel-link">⚙ {{ __('ui.invoice_settings') }}</a></li>
          <li><a href="{{ route('myshop_experience') }}" class="panel-link">{{ __('ui.experience_letter') }}</a></li>
          <li><a href="{{ route('myshop_idcard') }}" class="panel-link">{{ __('ui.id_card') }}</a></li>
          <li><a href="{{ route('subscriptions.new') }}" class="panel-link">{{ __('ui.subscription') }}</a></li>
        @elseif(str_contains($ctrl, 'Home'))
          <li><a href="{{ route('home') }}" class="panel-link">{{ __('ui.todays_pulses') }}</a></li>
          <li><a href="{{ route('offers') }}" class="panel-link">{{ __('ui.offers_benefits') }}</a></li>
        @elseif(str_contains($ctrl, 'Updates'))
          <li><a href="{{ route('updates.index') }}" class="panel-link">{{ __('ui.front_page') }}</a></li>
          <li><a href="{{ route('user_submissions.index') }}" class="panel-link">{{ __('ui.send_news') }}</a></li>
          <li><a href="{{ route('updates.index') }}#news" class="panel-link">{{ __('ui.latest_news') }}</a></li>
          <li><a href="{{ route('updates.index') }}#events" class="panel-link">{{ __('ui.events') }}</a></li>
          <li><a href="{{ route('updates.index') }}#jobs" class="panel-link">{{ __('ui.jobs_feed') }}</a></li>
          <li><a href="{{ route('updates.index') }}#markets" class="panel-link">{{ __('ui.markets') }}</a></li>
          <li><a href="{{ route('updates.index') }}#opinion" class="panel-link">{{ __('ui.opinion') }}</a></li>
          @auth
            @if(auth()->user()->isSuperadmin())
              <li><a href="{{ route('updates.create') }}" class="panel-link">{{ __('ui.publish_update') }}</a></li>
            @endif
          @endauth
        @elseif(str_contains($ctrl, 'Jobs'))
          <li><a href="{{ route('jobs.index') }}" class="panel-link">{{ __('ui.all_jobs') }}</a></li>
          <li><a href="{{ route('jobs.index', ['category' => 'IT']) }}" class="panel-link">{{ __('ui.it_jobs') }}</a></li>
          <li><a href="{{ route('jobs.index', ['category' => 'Government']) }}" class="panel-link">{{ __('ui.government_jobs') }}</a></li>
          <li><a href="{{ route('jobs.index', ['category' => 'Sales']) }}" class="panel-link">{{ __('ui.sales_jobs') }}</a></li>
          @auth
            @if(auth()->user()->isSuperadmin())
              <li><a href="{{ route('jobs.create') }}" class="panel-link">{{ __('ui.add_job') }}</a></li>
            @endif
          @endauth
        @elseif(str_contains($ctrl, 'Farming'))
          <li><a href="{{ route('farming.index') }}#articles" class="panel-link">{{ __('ui.city_articles') }}</a></li>
          <li><a href="{{ route('farming.index') }}#mandi" class="panel-link">{{ __('ui.mandi_prices') }}</a></li>
          <li><a href="{{ route('farming.index') }}#schemes" class="panel-link">{{ __('ui.govt_schemes') }}</a></li>
          <li><a href="{{ route('farming.index') }}#jobs" class="panel-link">{{ __('ui.agri_jobs') }}</a></li>
          <li><a href="{{ route('farming.index') }}#calendar" class="panel-link">{{ __('ui.crop_calendar') }}</a></li>
          <li><a href="{{ route('farming.index') }}#weather" class="panel-link">{{ __('ui.weather') }}</a></li>
          <li><a href="{{ route('farming.create') }}" class="panel-link">{{ __('ui.share_blog') }}</a></li>
        @elseif(str_contains($ctrl, 'Rents'))
          <li><a href="{{ route('rents.index') }}" class="panel-link">🏠 {{ __('ui.houses') }}</a></li>
          <li><a href="{{ route('rents.index', ['subcategory' => 'flat']) }}" class="panel-link">🏢 {{ __('ui.flats') }}</a></li>
          <li><a href="{{ route('rents.index', ['subcategory' => 'shop']) }}" class="panel-link">🏪 {{ __('ui.shops') }}</a></li>
          <li><a href="{{ route('rents.index', ['subcategory' => 'office']) }}" class="panel-link">💼 {{ __('ui.offices') }}</a></li>
          <li><a href="{{ route('rents.index', ['subcategory' => 'land']) }}" class="panel-link">🌾 {{ __('ui.land') }}</a></li>
        @elseif(str_contains($ctrl, 'Buy'))
          <li><a href="{{ route('buy.index') }}" class="panel-link">🛒 {{ __('ui.all_items') }}</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'vehicles']) }}" class="panel-link">🚗 {{ __('ui.vehicles') }}</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'electronics']) }}" class="panel-link">💻 {{ __('ui.electronics') }}</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'mobile']) }}" class="panel-link">📱 {{ __('ui.mobile_phones') }}</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'bikes']) }}" class="panel-link">🏍️ {{ __('ui.bikes') }}</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'farm']) }}" class="panel-link">🚜 {{ __('ui.farm_equip') }}</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'land']) }}" class="panel-link">🌾 {{ __('ui.land') }}</a></li>
        @elseif(str_contains($ctrl, 'Services'))
          <li><a href="{{ route('services.index') }}" class="panel-link">{{ __('ui.service_id_cards') }}</a></li>
        @elseif(request()->routeIs('user_submissions.*'))
          <li><a href="{{ route('user_submissions.index') }}" class="panel-link">📰 {{ __('ui.browse_stories') }}</a></li>
          @auth
            <li><a href="{{ route('user_submissions.index', ['tab' => 'submit']) }}" class="panel-link">✍️ {{ __('ui.submit_story') }}</a></li>
            <li><a href="{{ route('user_submissions.index', ['tab' => 'mine']) }}" class="panel-link">📁 {{ __('ui.my_stories') }}</a></li>
          @endauth
        @else
          <li><a href="{{ route('home') }}" class="panel-link">{{ __('ui.todays_pulses') }}</a></li>
          <li><a href="{{ route('offers') }}" class="panel-link">{{ __('ui.offers_benefits') }}</a></li>
        @endif
      </ul>
      <div class="panel-details">
        <h4>{{ $panelIsSuperadmin || $isMyService || $isMyShop ? __('ui.workspace') : (request()->routeIs('user_submissions.*') ? __('ui.community_stories') : __('ui.recommended')) }}</h4>
        <ul class="recommendations">
          @if($panelIsSuperadmin)
            <li>{{ __('ui.rec_manage_platform') }}</li>
            <li>{{ __('ui.rec_review_subscriptions') }}</li>
          @elseif($isMyService)
            @php
              $sideShop = auth()->check() ? auth()->user()->shops()->first() : null;
              $sideWorkersCount = $sideShop ? \App\Models\User::where('shop_id', $sideShop->id)->count() : 0;
              $sideOffersCount = $sideShop && $sideShop->city_id
                ? \App\Models\Update::offers()->where('city_id', $sideShop->city_id)->count()
                : 0;
            @endphp
            <li>{{ __('ui.total_workers') }}: {{ $sideWorkersCount }}</li>
            <li>{{ __('ui.active_city_offers') }}: {{ $sideOffersCount }}</li>
          @elseif($isMyShop)
            @php
              $sideShop = auth()->check() ? auth()->user()->shops()->first() : null;
              $sideWorkersCount = $sideShop ? \App\Models\User::where('shop_id', $sideShop->id)->count() : 0;
            @endphp
            <li>{{ __('ui.total_workers') }}: {{ $sideWorkersCount }}</li>
            <li>{{ __('ui.rec_update_shop') }}</li>
          @elseif(str_contains($ctrl, 'Services'))
            <li>{{ __('ui.rec_verified_cards') }}</li>
            <li>{{ __('ui.rec_open_card') }}</li>
          @elseif(str_contains($ctrl, 'Jobs'))
            <li>{{ __('ui.rec_city_jobs') }}</li>
            <li>{{ __('ui.rec_superadmin_jobs') }}</li>
          @elseif(request()->routeIs('user_submissions.*'))
            <li>{{ __('ui.earn_10_points') }}</li>
            <li>{{ __('ui.browse_stories') }}</li>
          @elseif(str_contains($ctrl, 'Updates'))
            <li>{{ __('ui.rec_updates_sections') }}</li>
            <li>{{ __('ui.rec_city_filter') }}</li>
          @elseif(str_contains($ctrl, 'Farming'))
            <li>{{ __('ui.rec_farming_citywise') }}</li>
            <li>{{ __('ui.rec_farming_blog') }}</li>
            <li>{{ __('ui.rec_farming_planning') }}</li>
          @else
            <li>{{ __('ui.rec_top_stories') }}</li>
            <li>{{ __('ui.rec_nearby_events') }}</li>
          @endif
        </ul>
      </div>
    </div>

    @include('shared.sidebar_auth')
  </aside>

  <div class="mobile-sidebar-overlay" id="mobile-sidebar-overlay" aria-hidden="true"></div>

  <!-- Main area -->
  <div class="main">
    <!-- Unified top header (all pages) -->
    @php
      $defaultCity = \App\Models\City::query()->whereRaw('LOWER(name) = ?', ['washim'])->first();
      $currentCityId = session('city_id') ?: $defaultCity?->id;
      $currentCity = $currentCityId ? \App\Models\City::find($currentCityId) : null;
      $currentCityName = $currentCity?->name;
      $currentCityLabel = city_display_name($currentCityName);
      $cityOptions = \App\Models\City::orderBy('name')->get(['id', 'name']);
    @endphp
    <header class="global-top">
      <div class="global-brand">
        <button type="button" class="mobile-menu-btn" id="mobile-menu-btn" aria-label="{{ __('ui.open_menu') }}">☰</button>
        <div class="global-badge">
          @include('shared.brand_logo', ['className' => 'global-logo', 'title' => 'AajchaOffer logo'])
        </div>
        <div class="global-copy">
          <strong class="global-wordmark"><span class="aajcha">Aajcha</span><span class="offer">Offer</span></strong>
          <small>Aajcha bhav, aajcha offer</small>
        </div>
      </div>

      <div class="global-right">
        @php($activeLocale = app()->getLocale())
        <form action="{{ route('set_language') }}" method="POST" class="global-lang" aria-label="Language switcher">
          @csrf
          <button type="submit" name="locale" value="en" class="{{ $activeLocale === 'en' ? 'on' : '' }}">EN</button>
          <button type="submit" name="locale" value="mr" class="{{ $activeLocale === 'mr' ? 'on' : '' }}">मर</button>
          <button type="submit" name="locale" value="hi" class="{{ $activeLocale === 'hi' ? 'on' : '' }}">हि</button>
        </form>

        <form action="{{ route('set_city') }}" method="POST" class="global-city-form">
          @csrf
          <select name="city_id" class="global-city" onchange="this.form.submit()">
            <option value="">{{ __('ui.select_city') }}</option>
            @foreach($cityOptions as $c)
              <option value="{{ $c->id }}" {{ (int)$currentCityId === (int)$c->id || (!$currentCityId && $currentCityName === $c->name) ? 'selected' : '' }}>
                📍 {{ city_display_name($c->name) }}
              </option>
            @endforeach
          </select>
        </form>

        <button type="button" class="mobile-city-btn" id="mobile-city-btn">📍 {{ $currentCityLabel ?: __('ui.select_city') }}</button>

        @if($hasWorkspaceRole)
          <a href="{{ $isWorkspaceRoute ? route('home') : $workspaceEntryUrl }}" class="workspace-toggle-btn">
            {{ $isWorkspaceRoute ? 'Switch to public view' : 'Open workspace' }}
          </a>
        @endif
        <div class="global-time" id="globalTime">--:--</div>
      </div>
    </header>

    <div class="mobile-city-modal" id="mobile-city-modal" aria-hidden="true">
      <div class="mobile-city-sheet" role="dialog" aria-label="{{ __('ui.select_city') }}" onclick="event.stopPropagation()">
        <div class="mobile-city-head">
          <input type="text" id="mobile-city-search" placeholder="{{ __('ui.search_city') }}">
          <button type="button" class="mobile-city-close" id="mobile-city-close" aria-label="{{ __('ui.close') }}">✕</button>
        </div>
        <div class="mobile-city-list" id="mobile-city-list"></div>
        <form id="mobile-city-form" action="{{ route('set_city') }}" method="POST" style="display:none">
          @csrf
          <input type="hidden" name="city_id" id="mobile-city-id" value="">
        </form>
      </div>
    </div>

    {{-- Flash messages --}}
    @if(session('notice'))
      <div style="background:#d4edda;color:#155724;padding:10px 22px;border-bottom:1px solid #c3e6cb;">
        {{ session('notice') }}
      </div>
    @endif
    @if(session('alert'))
      <div style="background:#f8d7da;color:#721c24;padding:10px 22px;border-bottom:1px solid #f5c6cb;">
        {{ session('alert') }}
      </div>
    @endif
    @if($errors->any())
      <div style="background:#f8d7da;color:#721c24;padding:10px 22px;border-bottom:1px solid #f5c6cb;">
        <ul style="margin:0;padding-left:20px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Page Content -->
    <div class="content">
      @yield('content')
    </div>

    @include('shared.footer')
  </div>
</div>

<div class="guide-bot" id="guide-bot">
  <div class="guide-card" id="guide-card" role="dialog" aria-label="{{ __('ui.quick_guide') }}">
    <div class="guide-head">
      <strong>{{ __('ui.welcome_title') }}</strong>
      <button type="button" class="guide-close" id="guide-close" aria-label="{{ __('ui.close') }}">✕</button>
    </div>
    <p class="guide-copy">{{ __('ui.welcome_copy') }}</p>
    <ul class="guide-list">
      <li>
        <span>{{ __('ui.guide_city') }}</span>
        <button type="button" class="guide-focus-btn" data-focus="#mobile-city-btn, .global-city">{{ __('ui.show') }}</button>
      </li>
      <li>
        <span>{{ __('ui.guide_tabs') }}</span>
        <button type="button" class="guide-focus-btn" data-focus=".iconbar, .mobile-menu-btn">{{ __('ui.show') }}</button>
      </li>
      <li>
        <span>{{ __('ui.guide_side') }}</span>
        <button type="button" class="guide-focus-btn" data-focus=".sidebar-panel, .mobile-sections">{{ __('ui.show') }}</button>
      </li>
      @if($hasWorkspaceRole)
      <li>
        <span>{{ __('ui.guide_switch') }}</span>
        <button type="button" class="guide-focus-btn" data-focus=".workspace-toggle-btn">{{ __('ui.show') }}</button>
      </li>
      @endif
    </ul>
    <div class="guide-actions">
      <button type="button" class="guide-secondary" id="guide-later">{{ __('ui.later') }}</button>
      <button type="button" class="guide-primary" id="guide-done">{{ __('ui.got_it') }}</button>
    </div>
  </div>
  <button type="button" class="guide-toggle" id="guide-toggle" aria-label="{{ __('ui.open_quick_guide') }}">
    <img class="guide-toggle-icon" src="{{ asset('images/icons/guide-robot.svg') }}" alt="{{ __('ui.guide_robot') }}" width="36" height="36">
    <span class="guide-toggle-dot one" aria-hidden="true"></span>
    <span class="guide-toggle-dot two" aria-hidden="true"></span>
    <span class="guide-toggle-dot three" aria-hidden="true"></span>
  </button>
</div>

<script>
/* ── Mobile sidebar: standalone, isolated, runs first ── */
(function(){
  function _sp(){ return document.querySelector('.sidebar-panel'); }
  function _ov(){ return document.getElementById('mobile-sidebar-overlay'); }
  function openSidebar(){
    document.body.classList.add('mobile-sidebar-open');
    var sp = _sp(); if(sp){ sp.style.transform='translateX(0)'; sp.style.visibility='visible'; }
    var ov = _ov(); if(ov){ ov.style.display='block'; ov.style.opacity='1'; ov.style.pointerEvents='auto'; }
  }
  function closeSidebar(){
    document.body.classList.remove('mobile-sidebar-open');
    var sp = _sp(); if(sp){ sp.style.transform=''; sp.style.visibility=''; }
    var ov = _ov(); if(ov){ ov.style.display=''; ov.style.opacity=''; ov.style.pointerEvents=''; }
  }
  document.addEventListener('DOMContentLoaded', function(){
    var btn  = document.getElementById('mobile-menu-btn');
    var cls  = document.getElementById('mobile-menu-close');
    var ov   = _ov();
    if(btn) btn.addEventListener('click', openSidebar);
    if(cls) cls.addEventListener('click', closeSidebar);
    if(ov)  ov.addEventListener('click',  closeSidebar);
  });
})();
</script>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const t = document.getElementById('globalTime');
    if (t) {
      const tick = () => {
        const d = new Date();
        t.textContent = `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
      };
      tick();
      setInterval(tick, 60 * 1000);
    }
  });

  document.addEventListener('DOMContentLoaded', function(){
    const mobileCityBtn = document.getElementById('mobile-city-btn');
    const mobileCityModal = document.getElementById('mobile-city-modal');
    const mobileCityClose = document.getElementById('mobile-city-close');
    const mobileCitySearch = document.getElementById('mobile-city-search');
    const mobileCityList = document.getElementById('mobile-city-list');
    const mobileCityId = document.getElementById('mobile-city-id');
    const mobileCityForm = document.getElementById('mobile-city-form');

    if (!mobileCityBtn || !mobileCityModal || !mobileCityList || !mobileCityId || !mobileCityForm) return;

    const cities = @json($cityOptions->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'label' => city_display_name($c->name)])->values());
    const selectedId = {{ (int)($currentCityId ?? 0) }};

    function renderCities(query = '') {
      const q = String(query || '').toLowerCase().trim();
      const filtered = cities.filter(c => !q || c.name.toLowerCase().includes(q) || String(c.label || '').toLowerCase().includes(q));
      mobileCityList.innerHTML = filtered.map(c => {
        const active = Number(c.id) === Number(selectedId) ? 'active' : '';
        return `<button type="button" class="mobile-city-item ${active}" data-id="${c.id}" data-name="${String(c.name).replace(/"/g, '&quot;')}"><span>📍 ${String(c.label || c.name)}</span><span>›</span></button>`;
      }).join('') || '<div style="padding:10px;color:#64748b">{{ __('ui.no_city_found') }}</div>';

      mobileCityList.querySelectorAll('.mobile-city-item').forEach(btn => {
        btn.addEventListener('click', function(){
          mobileCityId.value = this.getAttribute('data-id') || '';
          mobileCityForm.submit();
        });
      });
    }

    mobileCityBtn.addEventListener('click', function(){
      mobileCityModal.classList.add('open');
      renderCities('');
      if (mobileCitySearch) {
        mobileCitySearch.value = '';
        setTimeout(() => mobileCitySearch.focus(), 80);
      }
    });

    if (mobileCityClose) {
      mobileCityClose.addEventListener('click', function(){
        mobileCityModal.classList.remove('open');
      });
    }

    mobileCityModal.addEventListener('click', function(){
      mobileCityModal.classList.remove('open');
    });

    if (mobileCitySearch) {
      mobileCitySearch.addEventListener('input', function(){
        renderCities(this.value || '');
      });
    }
  });

  document.addEventListener('DOMContentLoaded', function(){
    const myServicePageUrl = @json($myServicePageUrl);
    const myServiceWhatsappShareUrl = @json($myServiceWhatsappShareUrl);
    const myServiceFacebookShareUrl = @json($myServiceFacebookShareUrl);
    const myServiceXShareUrl = @json($myServiceXShareUrl);
    const myServiceTelegramShareUrl = @json($myServiceTelegramShareUrl);

    const mapping = {
      home: {
        title: @json(__('ui.home')),
        items: [['U',@json(__('ui.todays_pulses')),"{{ route('home') }}"],['O',@json(__('ui.offers_benefits')),"{{ route('offers') }}"]],
        rec: [@json(__('ui.rec_top_stories')),@json(__('ui.rec_nearby_events')),@json(__('ui.rec_city_offers'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#a10b0b" xmlns="http://www.w3.org/2000/svg"><path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5z"/></svg>',
        color: '#a10b0b'
      },
      updates: {
        title: @json(__('ui.updates')),
        items: [
          ['📰',@json(__('ui.front_page')),"{{ route('updates.index') }}"],
          ['N',@json(__('ui.latest_news')),"{{ route('updates.index') }}#news"],
          ['E',@json(__('ui.events')),"{{ route('updates.index') }}#events"],
          ['J',@json(__('ui.jobs_feed')),"{{ route('updates.index') }}#jobs"],
          ['M',@json(__('ui.markets')),"{{ route('updates.index') }}#markets"],
          ['O',@json(__('ui.opinion')),"{{ route('updates.index') }}#opinion"],
          ['S',@json(__('ui.send_news')),"{{ route('user_submissions.index') }}"],
          @auth
            @if(auth()->user()->isSuperadmin())
              ['+',@json(__('ui.publish_update')),"{{ route('updates.create') }}"],
            @endif
          @endauth
        ],
        rec: [@json(__('ui.rec_updates_sections')),@json(__('ui.rec_city_filter')),@json(__('ui.rec_download_newspaper'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#0b5ed7" xmlns="http://www.w3.org/2000/svg"><path d="M3 5h18v2H3zM3 11h12v2H3zM3 17h18v2H3z"/></svg>',
        color: '#0b5ed7'
      },
      jobs: {
        title: @json(__('ui.jobs')),
        items: [
          ['💼',@json(__('ui.all_jobs')),"{{ route('jobs.index') }}"],
          ['IT',@json(__('ui.it_jobs')),"{{ route('jobs.index', ['category' => 'IT']) }}"],
          ['G',@json(__('ui.government_jobs')),"{{ route('jobs.index', ['category' => 'Government']) }}"],
          ['S',@json(__('ui.sales_jobs')),"{{ route('jobs.index', ['category' => 'Sales']) }}"],
          @auth
            @if(auth()->user()->isSuperadmin())
              ['+',@json(__('ui.add_job')),"{{ route('jobs.create') }}"],
            @endif
          @endauth
        ],
        rec: [@json(__('ui.rec_city_jobs')),@json(__('ui.rec_shop_service_jobs')),@json(__('ui.rec_superadmin_jobs'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#1761a0" xmlns="http://www.w3.org/2000/svg"><path d="M6 7h12v2H6zM6 11h12v6H6z"/></svg>',
        color: '#1761a0'
      },
      farming: {
        title: @json(__('ui.farming')),
        items: [
          ['🌾',@json(__('ui.city_articles')),"{{ route('farming.index') }}#articles"],
          ['💰',@json(__('ui.mandi_prices')),"{{ route('farming.index') }}#mandi"],
          ['🏛',@json(__('ui.govt_schemes')),"{{ route('farming.index') }}#schemes"],
          ['💼',@json(__('ui.agri_jobs')),"{{ route('farming.index') }}#jobs"],
          ['📅',@json(__('ui.crop_calendar')),"{{ route('farming.index') }}#calendar"],
          ['🌦',@json(__('ui.weather')),"{{ route('farming.index') }}#weather"],
          ['+',@json(__('ui.share_blog')),"{{ route('farming.create') }}"],
        ],
        rec: [@json(__('ui.rec_farming_citywise')),@json(__('ui.rec_farming_blog_short')),@json(__('ui.rec_farming_live_api'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#2e7d32" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3 7h-6l3-7zM6 10h10v8H6z"/></svg>',
        color: '#2e7d32'
      },
      rents: {
        title: @json(__('ui.rents')),
        items: [['R',@json(__('ui.houses')),"{{ route('rents.index') }}"],['F',@json(__('ui.flats')),"{{ route('rents.index') }}"]],
        rec: [@json(__('ui.rec_new_listings')),@json(__('ui.rec_saved_searches'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#ff8f00" xmlns="http://www.w3.org/2000/svg"><path d="M12 3l9 7h-3v8h-12v-8H3l9-7z"/></svg>',
        color: '#ff8f00'
      },
      buy: {
        title: @json(__('ui.buy_sell')),
        items: [
          ['🛒',@json(__('ui.all_items')),"{{ route('buy.index') }}"],
          ['🚗',@json(__('ui.vehicles')),"{{ route('buy.index', ['subcategory' => 'vehicles']) }}"],
          ['🏍',@json(__('ui.bikes')),"{{ route('buy.index', ['subcategory' => 'bikes']) }}"],
          ['📱',@json(__('ui.mobile_phones')),"{{ route('buy.index', ['subcategory' => 'mobile']) }}"],
          ['💻',@json(__('ui.electronics')),"{{ route('buy.index', ['subcategory' => 'electronics']) }}"],
          ['🚜',@json(__('ui.farm_equip')),"{{ route('buy.index', ['subcategory' => 'farm']) }}"],
          ['🌾',@json(__('ui.land')),"{{ route('buy.index', ['subcategory' => 'land']) }}"],
        ],
        rec: [@json(__('ui.rec_post_listing')),@json(__('ui.rec_filter_city_category'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#6f42c1" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18v2H3zM7 10h10v8H7z"/></svg>',
        color: '#6f42c1'
      },
      services: {
        title: @json(__('ui.services')),
        items: [['S',@json(__('ui.service_id_cards')),"{{ route('services.index') }}"]],
        rec: [@json(__('ui.rec_top_rated')),@json(__('ui.rec_verified_profiles'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#0d6efd" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v2H4zM4 10h16v8H4z"/></svg>',
        color: '#0d6efd'
      },
      shortsplay: {
        title: 'ShortsPlay',
        items: [
          ['💎','Open ShortsPlay',"{{ route('shortsplay') }}"],
        ],
        rec: ['Watch short videos', 'Earn ruby points', 'Track your leaderboard rank'],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg"><path d="M4 8l4-4h8l4 4-8 12L4 8z" fill="#e23b57"/><path d="M8 4l4 4 4-4" fill="none" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 8h16" fill="none" stroke="#b91f3b" stroke-width="1.1"/></svg>',
        color: '#e23b57'
      },
      user_submissions: {
        title: @json(__('ui.community_stories')),
        items: [
          ['📰', @json(__('ui.browse_stories')),  "{{ route('user_submissions.index') }}"],
          ['✍️', @json(__('ui.submit_story')),    "{{ route('user_submissions.index', ['tab' => 'submit']) }}"],
          ['📁', @json(__('ui.my_stories')),      "{{ route('user_submissions.index', ['tab' => 'mine']) }}"],
        ],
        rec: [@json(__('ui.earn_10_points')), @json(__('ui.browse_stories'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#b91c1c" xmlns="http://www.w3.org/2000/svg"><path d="M4 4h16v2H4zM4 8h12v2H4zM4 12h16v2H4zM4 16h10v2H4z"/></svg>',
        color: '#b91c1c'
      },
      admin: {
        title: @json(__('ui.admin_panel')),
        items: [
          ['🏠',@json(__('ui.dashboard')),      "{{ route('admin.dashboard') }}"],
          ['👤',@json(__('ui.users')),          "{{ route('admin.users.index') }}"],
          ['🏬',@json(__('ui.shops')),          "{{ route('admin.shops.index') }}"],
          ['🏙',@json(__('ui.cities')),         "{{ route('admin.cities.index') }}"],
          ['💼',@json(__('ui.jobs')),           "{{ route('admin.jobs.index') }}"],
          ['🏷',@json(__('ui.offers')),         "{{ route('admin.offers.index') }}"],
          ['💳',@json(__('ui.subscriptions')),  "{{ route('admin.subscriptions.index') }}"],
          ['⚙',@json(__('ui.settings')),       "{{ route('admin.settings.index') }}"],
        ],
        rec: [@json(__('ui.rec_manage_modules')),@json(__('ui.rec_unlock_queue')),@json(__('ui.rec_keep_data_clean'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#2f4e74" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l9 4v6c0 5-3.5 9.5-9 10-5.5-.5-9-5-9-10V6l9-4zm0 3.2L6 7.7v4.2c0 3.9 2.6 7.3 6 7.9 3.4-.6 6-4 6-7.9V7.7l-6-2.5z"/></svg>',
        color: '#2f4e74'
      },
      myservice: {
        title: @json(__('ui.my_service')),
        items: [
          ['📊',@json(__('ui.dashboard')),       "{{ route('shop_dashboard') }}"],
          ['⚙',@json(__('ui.configure')),       "{{ route('configure_myservice') }}"],
          ['👥',@json(__('ui.workers')),         "{{ route('workers_myservice') }}"],
          ['📞',@json(__('ui.client_requests')), "{{ route('myservice_requests') }}"],
          ['🏷',@json(__('ui.add_offer')),       "{{ route('myservice_offer_new') }}"],
          ['📄',@json(__('ui.my_invoices')),      "{{ route('invoices.index') }}"],
          ['⚙',@json(__('ui.invoice_settings')),"{{ route('invoices.settings') }}"],
          ['📋',@json(__('ui.experience')),      "{{ route('myservice_experience') }}"],
          ['🪪',@json(__('ui.id_card')),         "{{ route('myservice_idcard') }}"],
          ['⭐',@json(__('ui.subscription')),    "{{ route('subscriptions.new') }}"],
          ['🌐',@json(__('ui.my_page') . ' ↗'),  myServicePageUrl, '_blank'],
        ],
        rec: [@json(__('ui.rec_manage_service_team')),@json(__('ui.rec_handle_requests')),@json(__('ui.rec_share_id_card'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#2f4e74" xmlns="http://www.w3.org/2000/svg"><path d="M19 3H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 12H7v-2h8v2zm0-4H7v-2h8v2zm5-5H4V6h16v1z"/></svg>',
        color: '#2f4e74'
      },
      myshop: {
        title: @json(__('ui.my_shop')),
        items: [
          ['🏠',@json(__('ui.shop_home')),       "{{ route('myshop') }}"],
          ['⚙',@json(__('ui.configure')),       "{{ route('configure_myshop') }}"],
          ['👥',@json(__('ui.workers')),         "{{ route('workers_myshop') }}"],
          ['📞',@json(__('ui.client_requests')), "{{ route('myshop_requests') }}"],
          ['🏷',@json(__('ui.add_offer')),       "{{ route('myshop_offer_new') }}"],
          ['📄',@json(__('ui.my_invoices')),      "{{ route('invoices.index') }}"],
          ['⚙',@json(__('ui.invoice_settings')),"{{ route('invoices.settings') }}"],
          ['📋',@json(__('ui.experience')),      "{{ route('myshop_experience') }}"],
          ['🪪',@json(__('ui.id_card')),         "{{ route('myshop_idcard') }}"],
          ['⭐',@json(__('ui.subscription')),    "{{ route('subscriptions.new') }}"],
        ],
        rec: [@json(__('ui.rec_manage_shop_team')),@json(__('ui.rec_handle_requests')),@json(__('ui.rec_share_id_card'))],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#2f4e74" xmlns="http://www.w3.org/2000/svg"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 12H7v-2h8v2zm0-4H7v-2h8v2zm5-5H4V6h16v1z"/></svg>',
        color: '#253a5a'
      }
    };

    const iconItems = document.querySelectorAll('.icon-item');
    const panelTitle = document.querySelector('.sidebar-panel .panel-title');
    const panelList = document.querySelector('.sidebar-panel .panel-list');
    const recList = document.querySelector('.sidebar-panel .recommendations');
    const collapseBtn = document.getElementById('collapse-btn');
    const layout = document.getElementById('main-layout');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const mobileOverlay = document.getElementById('mobile-sidebar-overlay');

    function closeMobileSidebar(){
      document.body.classList.remove('mobile-sidebar-open');
    }

    function openMobileSidebar(){
      document.body.classList.add('mobile-sidebar-open');
    }

    function renderFor(key){
      const def = mapping[key];
      if(!def) return;
      panelTitle.innerHTML = (def.icon||'') + `<span class="panel-title-text">${def.title}</span>`;
      panelTitle.classList.add('active');
      panelTitle.style.borderLeftColor = def.color||'#a10b0b';
      const currentUrl = new URL(window.location.href);
      panelList.innerHTML = def.items.map((i)=>{
        const badge = `<span class="badge">${i[0]}</span>`;
        let label = `<span class="label">${i[1]}</span>`;
        if (i[2]) {
          if (String(i[2]).startsWith('action:')) {
            const action = String(i[2]).replace('action:', '');
            label = `<a class="label panel-action-link" href="#" data-action="${action}" data-url="${myServicePageUrl}">${i[1]}</a>`;
          } else {
            let isExternal = false;
            try {
              const targetUrl = new URL(i[2], window.location.origin);
              isExternal = targetUrl.origin !== window.location.origin;
            } catch(e) {}
            const openBlank = isExternal || i[3] === '_blank';
            label = `<a class="label" href="${i[2]}" ${openBlank ? 'target="_blank" rel="noopener"' : ''}>${i[1]}</a>`;
          }
        }
        let isActive = false;
        try {
          if (i[2]) {
            const targetUrl = new URL(i[2], window.location.origin);
            isActive = currentUrl.pathname === targetUrl.pathname
              && ((targetUrl.hash || '') === '' ? (currentUrl.hash || '') === '' : currentUrl.hash === targetUrl.hash);
          }
        } catch(e){}
        return `<li class="${isActive ? 'active-item' : ''}">${badge}${label}</li>`;
      }).join('');
      recList.innerHTML = def.rec.map(r=>`<li>${r}</li>`).join('');
    }

    panelList.addEventListener('click', async function(e){
      const actionLink = e.target.closest('.panel-action-link');
      if (!actionLink) return;
      e.preventDefault();

      const action = actionLink.getAttribute('data-action');
      const url = actionLink.getAttribute('data-url') || myServicePageUrl;

      if (action === 'copy') {
        try {
          await navigator.clipboard.writeText(url);
          const prev = actionLink.textContent;
          actionLink.textContent = @json(__('ui.copied'));
          setTimeout(() => actionLink.textContent = prev, 1300);
        } catch (err) {
          window.prompt(@json(__('ui.copy_this_link')), url);
        }
      }

      if (action === 'pdf') {
        const printWin = window.open(url, '_blank');
        if (printWin) {
          setTimeout(() => {
            try { printWin.print(); } catch (err) {}
          }, 900);
        }
      }
    });

    iconItems.forEach(function(li){
      li.addEventListener('click', function(e){
        const key = li.dataset.key;
        if(!key) return;
        document.querySelectorAll('.icon-item').forEach(n=>n.classList.remove('selected'));
        li.classList.add('selected');
        renderFor(key);
        layout.classList.remove('collapsed');
        closeMobileSidebar();
      });
    });

    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', openMobileSidebar);
    }
    if (mobileMenuClose) {
      mobileMenuClose.addEventListener('click', closeMobileSidebar);
    }
    if (mobileOverlay) {
      mobileOverlay.addEventListener('click', closeMobileSidebar);
    }

    document.querySelectorAll('.sidebar-panel a').forEach(function(link){
      link.addEventListener('click', closeMobileSidebar);
    });

    if(collapseBtn){
      collapseBtn.addEventListener('click', function(){
        layout.classList.toggle('collapsed');
        collapseBtn.textContent = layout.classList.contains('collapsed') ? '<' : '›';
      });
    }

    // Highlight correct icon for workspace routes
    const routeGroup = document.body.dataset.routeGroup || '';
    if (routeGroup && mapping[routeGroup]) {
      document.querySelectorAll('.icon-item').forEach(n => n.classList.remove('selected'));
    }
    const initial = document.querySelector('.icon-item.selected');
    const initialKey = routeGroup && mapping[routeGroup] ? routeGroup
      : (initial && initial.dataset.key ? initial.dataset.key : 'home');
    try { renderFor(initialKey); } catch(e) { console.warn('renderFor error:', e); }
  });

  document.addEventListener('DOMContentLoaded', function(){
    const guideCard = document.getElementById('guide-card');
    const guideToggle = document.getElementById('guide-toggle');
    const guideClose = document.getElementById('guide-close');
    const guideLater = document.getElementById('guide-later');
    const guideDone = document.getElementById('guide-done');
    const focusButtons = document.querySelectorAll('.guide-focus-btn');

    if (!guideCard || !guideToggle) return;

    const userId = @json(auth()->check() ? auth()->id() : null);
    const storageKey = userId ? `aajchaoffer_guide_seen_v1_user_${userId}` : 'aajchaoffer_guide_seen_v1_guest';
    const openGuide = () => guideCard.classList.add('open');
    const closeGuide = () => guideCard.classList.remove('open');

    const routeGroup = document.body.dataset.routeGroup || '';
    if (routeGroup) return;

    const findVisibleTarget = (selectorCsv) => {
      const selectors = String(selectorCsv || '').split(',').map(s => s.trim()).filter(Boolean);
      for (const selector of selectors) {
        const candidate = document.querySelector(selector);
        if (!candidate) continue;
        const style = window.getComputedStyle(candidate);
        const rect = candidate.getBoundingClientRect();
        const isVisible = style.display !== 'none'
          && style.visibility !== 'hidden'
          && style.opacity !== '0'
          && rect.width > 0
          && rect.height > 0
          && candidate.offsetParent !== null;
        if (!isVisible) continue;
        return candidate;
      }
      return null;
    };

    const focusElement = (element) => {
      document.querySelectorAll('.guide-focus').forEach(el => el.classList.remove('guide-focus'));
      element.classList.add('guide-focus');
      element.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
      window.setTimeout(() => element.classList.remove('guide-focus'), 2200);
    };

    guideToggle.addEventListener('click', function(){
      guideCard.classList.contains('open') ? closeGuide() : openGuide();
    });

    const dismissGuide = () => {
      localStorage.setItem(storageKey, '1');
      closeGuide();
    };

    if (guideClose) {
      guideClose.addEventListener('click', dismissGuide);
    }

    if (guideLater) {
      guideLater.addEventListener('click', dismissGuide);
    }

    if (guideDone) {
      guideDone.addEventListener('click', function(){
        dismissGuide();
      });
    }

    focusButtons.forEach(function(btn){
      btn.addEventListener('click', function(){
        const target = findVisibleTarget(this.getAttribute('data-focus'));
        if (target) focusElement(target);
      });
    });

    const isLandingPage = ['/offers', '/home', '/'].includes(window.location.pathname);
    if (!localStorage.getItem(storageKey) && isLandingPage) {
      window.setTimeout(openGuide, 650);
    }
  });

  document.addEventListener('DOMContentLoaded', function(){
    const role = document.body.getAttribute('data-user-role') || '';
    const path = '/' + (document.body.getAttribute('data-path') || '').replace(/^\/+/, '');
    const isServiceFlow = role === 'service_provider' && (path.startsWith('/myservice') || path.startsWith('/shop_dashboard') || path.startsWith('/subscriptions'));
    if (!isServiceFlow) return;

    const replacements = [
      [/Shop Owner/gi, 'Service Provider'],
      [/Shop Logo/gi, 'Service Logo'],
      [/Shop Type/gi, 'Service Type'],
      [/Configure Shop/gi, 'Configure Service'],
      [/My Shop/gi, 'My Service'],
      [/Shop Dashboard/gi, 'Service Dashboard'],
      [/shop owner/gi, 'service provider'],
      [/shop/gi, 'service'],
      [/Shop/g, 'Service'],
    ];

    const swapText = (input) => {
      let output = input;
      replacements.forEach(([pattern, replacement]) => {
        output = output.replace(pattern, replacement);
      });
      return output;
    };

    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
      acceptNode(node) {
        const parent = node.parentElement;
        if (!parent) return NodeFilter.FILTER_REJECT;
        const tag = parent.tagName;
        if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'NOSCRIPT') return NodeFilter.FILTER_REJECT;
        if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
        return NodeFilter.FILTER_ACCEPT;
      }
    });

    const textNodes = [];
    while (walker.nextNode()) {
      textNodes.push(walker.currentNode);
    }
    textNodes.forEach((node) => {
      const next = swapText(node.nodeValue);
      if (next !== node.nodeValue) {
        node.nodeValue = next;
      }
    });

    document.querySelectorAll('input[placeholder], textarea[placeholder]').forEach((el) => {
      const placeholder = el.getAttribute('placeholder') || '';
      const updated = swapText(placeholder);
      if (updated !== placeholder) {
        el.setAttribute('placeholder', updated);
      }
    });
  });
</script>

@auth
<script>
(function(){
  // Reading-time reward: +2 pts after 10 continuous minutes on page (once per page per day)
  var READ_MS   = 10 * 60 * 1000; // 10 minutes
  var pageKey   = location.pathname;
  var csrf      = document.querySelector('meta[name="csrf-token"]')?.content || '';
  var awarded   = false;
  var elapsed   = 0;
  var lastTick  = Date.now();
  var hidden    = false;

  // Pause timer when tab is hidden
  document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
      elapsed += Date.now() - lastTick;
      hidden = true;
    } else {
      lastTick = Date.now();
      hidden = false;
    }
  });

  var ticker = setInterval(function() {
    if (awarded) { clearInterval(ticker); return; }
    if (!hidden) elapsed += Date.now() - lastTick;
    lastTick = Date.now();

    if (elapsed >= READ_MS) {
      clearInterval(ticker);
      fetch('/points/page-reading', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf},
        body: JSON.stringify({page_key: pageKey})
      }).then(function(r){ return r.json(); }).then(function(d){
        if (d.ok) {
          awarded = true;
          // Update points display if on offers page
          if (typeof cpSetPts === 'function' && d.total != null) cpSetPts(d.total);
          // Show a subtle toast if cp2Toast exists, else browser notification
          if (typeof cpToast === 'function') {
            cpToast('+2 Ruby Points for reading! 💎', 'ok');
          } else {
            var n = document.createElement('div');
            n.textContent = '💎 +2 Ruby Points for reading!';
            n.style.cssText = 'position:fixed;bottom:1.2rem;right:1.2rem;background:#10b981;color:#fff;padding:.55rem 1.1rem;border-radius:100px;font-size:.8rem;font-weight:700;z-index:99999;box-shadow:0 4px 16px rgba(0,0,0,.2);animation:fadeInUp .3s ease';
            document.body.appendChild(n);
            setTimeout(function(){ n.remove(); }, 3500);
          }
        }
      }).catch(function(){});
    }
  }, 5000); // check every 5s
})();
</script>
@endauth

</body>
</html>
