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
  <link rel="stylesheet" href="{{ asset('css/application.css') }}">
  <style>
    .global-top{position:sticky;top:0;z-index:120;background:rgba(255,248,240,.96);backdrop-filter:blur(14px);border-bottom:1px solid #F0E8DC;padding:.62rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:.7rem}
      /* Panel active item */
      .panel-list li.active-item{background:linear-gradient(90deg,#eef5ff,#f4f8ff);border-left:3px solid #2f4e74;padding-left:5px}
      .panel-list li.active-item .badge{background:#2f4e74 !important;color:#fff !important}
      .panel-list li.active-item .label,.panel-list li.active-item .label a{color:#2f4e74;font-weight:700}
    .global-brand{display:flex;align-items:center;gap:.6rem;min-width:0}
    .global-badge{display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .global-logo{width:36px;height:36px;display:block}
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
      .global-copy small{max-width:150px}
      .global-time{display:none}
      .global-city-form{display:none}
      .mobile-city-btn{display:inline-flex}
      .guide-bot{right:10px;bottom:10px}
      .guide-toggle{width:50px;height:50px}
      .guide-toggle-icon{width:46px;height:46px}
      .guide-card{width:min(95vw,330px);padding:10px}
      .guide-card::before{margin:-10px -10px 10px}
      .guide-list li{padding:7px 8px}
    }
  </style>
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
    || $isOwnedServicePage;
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
    || $isOwnedShopPage;
  $isAdminBody = request()->routeIs('admin.*') && auth()->check() && auth()->user()->isSuperadmin();
  $bodyRouteGroup = $isAdminBody ? 'admin' : ($isMyServiceBody ? 'myservice' : ($isMyShopBody ? 'myshop' : ''));
@endphp
<body data-user-role="{{ auth()->check() ? auth()->user()->role : 'guest' }}" data-path="{{ request()->path() }}" data-route-group="{{ $bodyRouteGroup }}">

<div class="layout" id="main-layout">
  <!-- Left icon bar -->
  <aside class="iconbar" aria-hidden="false">
    <nav>
      <ul>
        <li class="icon-item {{ request()->routeIs('home') ? 'selected' : '' }}" data-key="home">
          <a href="{{ route('home') }}" class="icon-square" title="Home">
            <img src="{{ asset('images/icons/home.png') }}" alt="Home" width="24" height="24">
          </a>
        </li>
        <li class="icon-item {{ request()->routeIs('updates.*') ? 'selected' : '' }}" data-key="updates">
          <a href="{{ route('updates.index') }}" class="icon-square" title="Updates">
            <img src="{{ asset('images/icons/updates.png') }}" alt="Updates" width="24" height="24">
          </a>
        </li>
        <li class="icon-item {{ request()->routeIs('jobs.*') ? 'selected' : '' }}" data-key="jobs">
          <a href="{{ route('jobs.index') }}" class="icon-square" title="Jobs">
            <img src="{{ asset('images/icons/jobs.jpeg') }}" alt="Jobs" width="24" height="24">
          </a>
        </li>
        <li class="icon-item {{ request()->routeIs('farming.*') ? 'selected' : '' }}" data-key="farming">
          <a href="{{ route('farming.index') }}" class="icon-square" title="Farming">
            <img src="{{ asset('images/icons/farming.png') }}" alt="Farming" width="24" height="24">
          </a>
        </li>
        <li class="icon-item {{ request()->routeIs('rents.*') ? 'selected' : '' }}" data-key="rents">
          <a href="{{ route('rents.index') }}" class="icon-square" title="Rents">
            <img src="{{ asset('images/icons/rent.png') }}" alt="Rents" width="24" height="24">
          </a>
        </li>
        <li class="icon-item {{ request()->routeIs('buy.*') ? 'selected' : '' }}" data-key="buy">
          <a href="{{ route('buy.index') }}" class="icon-square" title="Buy &amp; Sell">
            <img src="{{ asset('images/icons/buy.png') }}" alt="Buy & Sell" width="24" height="24">
          </a>
        </li>
        <li class="icon-item {{ request()->routeIs('services.*') ? 'selected' : '' }}" data-key="services">
          <a href="{{ route('services.index') }}" class="icon-square" title="Services">
            <img src="{{ asset('images/icons/services.png') }}" alt="Services" width="24" height="24">
          </a>
        </li>
        <li class="icon-item {{ request()->routeIs('about') ? 'selected' : '' }}" data-key="about">
          <a href="{{ route('about') }}" class="icon-square" title="About Us">
            <img src="{{ asset('images/icons/about.svg') }}" alt="About Us" width="24" height="24">
          </a>
        </li>
      </ul>
    </nav>
    <div class="iconbar-auth-placeholder" style="height:86px"></div>
    <div class="collapse">
      <button class="collapse-btn" id="collapse-btn">›</button>
    </div>
  </aside>

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
        <span class="mobile-title">Menu</span>
      </div>
      <button type="button" id="mobile-menu-close" aria-label="Close menu">✕</button>
    </div>
    <div class="panel-search">
      <input type="text" placeholder="Search menu..." />
    </div>

    <div class="mobile-sections" aria-label="All sections">
      <a href="{{ route('home') }}" class="mobile-sec-link {{ request()->routeIs('home') ? 'active' : '' }}">🏠 Home</a>
      <a href="{{ route('updates.index') }}" class="mobile-sec-link {{ request()->routeIs('updates.*') ? 'active' : '' }}">📰 Updates</a>
      <a href="{{ route('jobs.index') }}" class="mobile-sec-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">💼 Jobs</a>
      <a href="{{ route('farming.index') }}" class="mobile-sec-link {{ request()->routeIs('farming.*') ? 'active' : '' }}">🌾 Farming</a>
      <a href="{{ route('rents.index') }}" class="mobile-sec-link {{ request()->routeIs('rents.*') ? 'active' : '' }}">🏘️ Rents</a>
      <a href="{{ route('buy.index') }}" class="mobile-sec-link {{ request()->routeIs('buy.*') ? 'active' : '' }}">🛒 Buy</a>
      <a href="{{ route('services.index') }}" class="mobile-sec-link {{ request()->routeIs('services.*') ? 'active' : '' }}">🛠️ Services</a>
      <a href="{{ route('about') }}" class="mobile-sec-link {{ request()->routeIs('about') ? 'active' : '' }}">ℹ️ About Us</a>
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
          || $panelIsOwnedServicePage);
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
          || $panelIsOwnedShopPage);

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
      <div class="panel-title {{ request()->routeIs('home') || $isMyService || $isMyShop || $panelIsSuperadmin ? 'active' : '' }}">
        {{ request()->routeIs('about') ? 'About Us' : ($panelIsSuperadmin ? 'Admin Panel' : ($isMyService ? 'My Service' : ($isMyShop ? 'My Shop' : 'Home'))) }}
      </div>
      <ul class="panel-list">
        @if(request()->routeIs('about'))
          <li><a href="{{ route('about') }}" class="panel-link">Our Story</a></li>
          <li><a href="{{ route('about') }}#contact" class="panel-link">Contact</a></li>
        @elseif($panelIsSuperadmin)
          <li><a href="{{ route('admin.dashboard') }}" class="panel-link">Dashboard</a></li>
          <li><a href="{{ route('admin.users.index') }}" class="panel-link">Users</a></li>
          <li><a href="{{ route('admin.shops.index') }}" class="panel-link">Shops</a></li>
          <li><a href="{{ route('admin.subscriptions.index') }}" class="panel-link">Subscriptions</a></li>
          <li><a href="{{ route('admin.settings.index') }}" class="panel-link">Settings</a></li>
        @elseif($isMyService)
          <li><a href="{{ $myServicePageUrl }}" class="panel-link">My Page</a></li>
          <li><a href="{{ route('configure_myservice') }}" class="panel-link">Configure Service</a></li>
          <li><a href="{{ route('workers_myservice') }}" class="panel-link">Workers</a></li>
          <li><a href="{{ route('myservice_offer_new') }}" class="panel-link">Offers</a></li>
          <li><a href="{{ route('myservice_requests') }}" class="panel-link">Client Requests</a></li>
          <li><a href="{{ route('myservice_experience') }}" class="panel-link">Experience Letter</a></li>
          <li><a href="{{ route('myservice_idcard') }}" class="panel-link">ID Card</a></li>
          <li><a href="{{ route('subscriptions.new') }}" class="panel-link">Subscription</a></li>
          <li><a href="{{ route('shop_dashboard') }}" class="panel-link">Dashboard</a></li>
        @elseif($isMyShop)
          <li><a href="{{ route('myshop') }}" class="panel-link">Shop Home</a></li>
          <li><a href="{{ route('configure_myshop') }}" class="panel-link">Configure Shop</a></li>
          <li><a href="{{ route('workers_myshop') }}" class="panel-link">Workers</a></li>
          <li><a href="{{ route('myshop_offer_new') }}" class="panel-link">Offers</a></li>
          <li><a href="{{ route('myshop_requests') }}" class="panel-link">Client Requests</a></li>
          <li><a href="{{ route('myshop_experience') }}" class="panel-link">Experience Letter</a></li>
          <li><a href="{{ route('myshop_idcard') }}" class="panel-link">ID Card</a></li>
          <li><a href="{{ route('subscriptions.new') }}" class="panel-link">Subscription</a></li>
        @elseif(str_contains($ctrl, 'Home'))
          <li><a href="{{ route('home') }}" class="panel-link">Today's Pulses</a></li>
          <li><a href="{{ route('offers') }}" class="panel-link">Offers &amp; Benefits</a></li>
        @elseif(str_contains($ctrl, 'Updates'))
          <li><a href="{{ route('updates.index') }}" class="panel-link">Front Page</a></li>
          <li><a href="{{ route('updates.index') }}#news" class="panel-link">Latest News</a></li>
          <li><a href="{{ route('updates.index') }}#events" class="panel-link">Events</a></li>
          <li><a href="{{ route('updates.index') }}#jobs" class="panel-link">Jobs Feed</a></li>
          <li><a href="{{ route('updates.index') }}#markets" class="panel-link">Markets</a></li>
          <li><a href="{{ route('updates.index') }}#opinion" class="panel-link">Opinion</a></li>
          @auth
            @if(auth()->user()->isSuperadmin())
              <li><a href="{{ route('updates.create') }}" class="panel-link">Publish Update</a></li>
            @endif
          @endauth
        @elseif(str_contains($ctrl, 'Jobs'))
          <li><a href="{{ route('jobs.index') }}" class="panel-link">All Jobs</a></li>
          <li><a href="{{ route('jobs.index', ['category' => 'IT']) }}" class="panel-link">IT Jobs</a></li>
          <li><a href="{{ route('jobs.index', ['category' => 'Government']) }}" class="panel-link">Government Jobs</a></li>
          <li><a href="{{ route('jobs.index', ['category' => 'Sales']) }}" class="panel-link">Sales Jobs</a></li>
          @auth
            @if(auth()->user()->isSuperadmin())
              <li><a href="{{ route('jobs.create') }}" class="panel-link">Add Job</a></li>
            @endif
          @endauth
        @elseif(str_contains($ctrl, 'Farming'))
          <li><a href="{{ route('farming.index') }}#articles" class="panel-link">City Articles</a></li>
          <li><a href="{{ route('farming.index') }}#mandi" class="panel-link">Mandi Prices</a></li>
          <li><a href="{{ route('farming.index') }}#schemes" class="panel-link">Govt Schemes</a></li>
          <li><a href="{{ route('farming.index') }}#jobs" class="panel-link">Agri Jobs</a></li>
          <li><a href="{{ route('farming.index') }}#calendar" class="panel-link">Crop Calendar</a></li>
          <li><a href="{{ route('farming.index') }}#weather" class="panel-link">Weather</a></li>
          <li><a href="{{ route('farming.create') }}" class="panel-link">Share Blog</a></li>
        @elseif(str_contains($ctrl, 'Rents'))
          <li><a href="{{ route('rents.index') }}" class="panel-link">🏠 Houses</a></li>
          <li><a href="{{ route('rents.index', ['subcategory' => 'flat']) }}" class="panel-link">🏢 Flats</a></li>
          <li><a href="{{ route('rents.index', ['subcategory' => 'shop']) }}" class="panel-link">🏪 Shops</a></li>
          <li><a href="{{ route('rents.index', ['subcategory' => 'office']) }}" class="panel-link">💼 Offices</a></li>
          <li><a href="{{ route('rents.index', ['subcategory' => 'land']) }}" class="panel-link">🌾 Land</a></li>
        @elseif(str_contains($ctrl, 'Buy'))
          <li><a href="{{ route('buy.index') }}" class="panel-link">🛒 All Items</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'vehicles']) }}" class="panel-link">🚗 Vehicles</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'electronics']) }}" class="panel-link">💻 Electronics</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'mobile']) }}" class="panel-link">📱 Mobile Phones</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'bikes']) }}" class="panel-link">🏍️ Bikes</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'farm']) }}" class="panel-link">🚜 Farm Equip</a></li>
          <li><a href="{{ route('buy.index', ['subcategory' => 'land']) }}" class="panel-link">🌾 Land</a></li>
        @elseif(str_contains($ctrl, 'Services'))
          <li><a href="{{ route('services.index') }}" class="panel-link">Service ID Cards</a></li>
        @else
          <li><a href="{{ route('home') }}" class="panel-link">Today's Pulses</a></li>
          <li><a href="{{ route('offers') }}" class="panel-link">Offers &amp; Benefits</a></li>
        @endif
      </ul>
      <div class="panel-details">
        <h4>{{ $panelIsSuperadmin || $isMyService || $isMyShop ? 'Workspace' : 'Recommended' }}</h4>
        <ul class="recommendations">
          @if($panelIsSuperadmin)
            <li>Manage platform users and business pages</li>
            <li>Review subscriptions and unlock requests</li>
          @elseif($isMyService)
            @php
              $sideShop = auth()->check() ? auth()->user()->shops()->first() : null;
              $sideWorkersCount = $sideShop ? \App\Models\User::where('shop_id', $sideShop->id)->count() : 0;
              $sideOffersCount = $sideShop && $sideShop->city_id
                ? \App\Models\Update::offers()->where('city_id', $sideShop->city_id)->count()
                : 0;
            @endphp
            <li>Total workers: {{ $sideWorkersCount }}</li>
            <li>Active city offers: {{ $sideOffersCount }}</li>
          @elseif($isMyShop)
            @php
              $sideShop = auth()->check() ? auth()->user()->shops()->first() : null;
              $sideWorkersCount = $sideShop ? \App\Models\User::where('shop_id', $sideShop->id)->count() : 0;
            @endphp
            <li>Total workers: {{ $sideWorkersCount }}</li>
            <li>Use Configure to update shop details</li>
          @elseif(str_contains($ctrl, 'Services'))
            <li>Verified service provider ID cards</li>
            <li>Open card to view details and reviews</li>
          @elseif(str_contains($ctrl, 'Jobs'))
            <li>City-based jobs include shop/service provider listings</li>
            <li>Only superadmin can add jobs from Jobs module</li>
          @elseif(str_contains($ctrl, 'Updates'))
            <li>Open newspaper sections from the same edition page</li>
            <li>Use city filter to switch the local edition</li>
          @elseif(str_contains($ctrl, 'Farming'))
            <li>All farming sections are city-wise with selected city context</li>
            <li>Anyone can share blog with rich editor (name + photos + formatting)</li>
            <li>Use mandi, weather and crop calendar for local planning</li>
          @else
            <li>See today's top stories</li>
            <li>Nearby events</li>
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
      $cityOptions = \App\Models\City::orderBy('name')->get(['id', 'name']);
    @endphp
    <header class="global-top">
      <div class="global-brand">
        <button type="button" class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Open menu">☰</button>
        <div class="global-badge">
          @include('shared.brand_logo', ['className' => 'global-logo', 'title' => 'AajchaOffer logo'])
        </div>
        <div class="global-copy">
          <strong class="global-wordmark"><span class="aajcha">Aajcha</span><span class="offer">Offer</span></strong>
          <small>Aajcha bhav, aajcha offer</small>
        </div>
      </div>

      <div class="global-right">
        <div class="global-lang" aria-label="Language switcher">
          <button type="button" class="on">EN</button>
          <button type="button">मर</button>
          <button type="button">हि</button>
        </div>

        <form action="{{ route('set_city') }}" method="POST" class="global-city-form">
          @csrf
          <select name="city_id" class="global-city" onchange="this.form.submit()">
            <option value="">Select city</option>
            @foreach($cityOptions as $c)
              <option value="{{ $c->id }}" {{ (int)$currentCityId === (int)$c->id || (!$currentCityId && $currentCityName === $c->name) ? 'selected' : '' }}>
                📍 {{ $c->name }}
              </option>
            @endforeach
          </select>
        </form>

        <button type="button" class="mobile-city-btn" id="mobile-city-btn">📍 {{ $currentCityName ?: 'Select city' }}</button>

        <div class="global-time" id="globalTime">--:--</div>
      </div>
    </header>

    <div class="mobile-city-modal" id="mobile-city-modal" aria-hidden="true">
      <div class="mobile-city-sheet" role="dialog" aria-label="Select city" onclick="event.stopPropagation()">
        <div class="mobile-city-head">
          <input type="text" id="mobile-city-search" placeholder="Search city...">
          <button type="button" class="mobile-city-close" id="mobile-city-close" aria-label="Close city picker">✕</button>
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
  <div class="guide-card" id="guide-card" role="dialog" aria-label="Quick guide">
    <div class="guide-head">
      <strong>Welcome to AajchaOffer 👋</strong>
      <button type="button" class="guide-close" id="guide-close" aria-label="Close guide">✕</button>
    </div>
    <p class="guide-copy">First time here? Use this guide to quickly learn where to select city and how to navigate tabs.</p>
    <ul class="guide-list">
      <li>
        <span>Select your city from the top-right city selector.</span>
        <button type="button" class="guide-focus-btn" data-focus="#mobile-city-btn, .global-city">Show</button>
      </li>
      <li>
        <span>Browse main tabs from left icons (Home, Updates, Jobs, Farming, Rents, Buy, Services, About).</span>
        <button type="button" class="guide-focus-btn" data-focus=".iconbar, .mobile-menu-btn">Show</button>
      </li>
      <li>
        <span>Use side menu links for section shortcuts and details.</span>
        <button type="button" class="guide-focus-btn" data-focus=".sidebar-panel, .mobile-sections">Show</button>
      </li>
    </ul>
    <div class="guide-actions">
      <button type="button" class="guide-secondary" id="guide-later">Later</button>
      <button type="button" class="guide-primary" id="guide-done">Got it</button>
    </div>
  </div>
  <button type="button" class="guide-toggle" id="guide-toggle" aria-label="Open quick guide">
    <img class="guide-toggle-icon" src="{{ asset('images/icons/guide-robot.svg') }}" alt="Guide robot" width="36" height="36">
    <span class="guide-toggle-dot one" aria-hidden="true"></span>
    <span class="guide-toggle-dot two" aria-hidden="true"></span>
    <span class="guide-toggle-dot three" aria-hidden="true"></span>
  </button>
</div>

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

    const cities = @json($cityOptions->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values());
    const selectedId = {{ (int)($currentCityId ?? 0) }};

    function renderCities(query = '') {
      const q = String(query || '').toLowerCase().trim();
      const filtered = cities.filter(c => !q || c.name.toLowerCase().includes(q));
      mobileCityList.innerHTML = filtered.map(c => {
        const active = Number(c.id) === Number(selectedId) ? 'active' : '';
        return `<button type="button" class="mobile-city-item ${active}" data-id="${c.id}" data-name="${String(c.name).replace(/"/g, '&quot;')}"><span>📍 ${c.name}</span><span>›</span></button>`;
      }).join('') || '<div style="padding:10px;color:#64748b">No city found.</div>';

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
        title: "Home",
        items: [['U',"Today's Pulses","{{ route('home') }}"],['O',"Offers & Benefits","{{ route('offers') }}"]],
        rec: ["See today's top stories","Nearby events","City offers & benefits"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#a10b0b" xmlns="http://www.w3.org/2000/svg"><path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5z"/></svg>',
        color: '#a10b0b'
      },
      updates: {
        title: "Updates",
        items: [
          ['📰',"Front Page","{{ route('updates.index') }}"],
          ['N',"Latest News","{{ route('updates.index') }}#news"],
          ['E',"Events","{{ route('updates.index') }}#events"],
          ['J',"Jobs Feed","{{ route('updates.index') }}#jobs"],
          ['M',"Markets","{{ route('updates.index') }}#markets"],
          ['O',"Opinion","{{ route('updates.index') }}#opinion"],
          @auth
            @if(auth()->user()->isSuperadmin())
              ['+',"Publish Update","{{ route('updates.create') }}"],
            @endif
          @endauth
        ],
        rec: ["Open each edition section from /updates","Use city filter for local edition","Download today’s newspaper view"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#0b5ed7" xmlns="http://www.w3.org/2000/svg"><path d="M3 5h18v2H3zM3 11h12v2H3zM3 17h18v2H3z"/></svg>',
        color: '#0b5ed7'
      },
      jobs: {
        title: "Jobs",
        items: [
          ['💼',"All Jobs","{{ route('jobs.index') }}"],
          ['IT',"IT Jobs","{{ route('jobs.index', ['category' => 'IT']) }}"],
          ['G',"Government","{{ route('jobs.index', ['category' => 'Government']) }}"],
          ['S',"Sales","{{ route('jobs.index', ['category' => 'Sales']) }}"],
          @auth
            @if(auth()->user()->isSuperadmin())
              ['+',"Add Job","{{ route('jobs.create') }}"],
            @endif
          @endauth
        ],
        rec: ["Jobs are listed city-wise","Shop/service provider job entries appear here","Only superadmin can add jobs here"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#1761a0" xmlns="http://www.w3.org/2000/svg"><path d="M6 7h12v2H6zM6 11h12v6H6z"/></svg>',
        color: '#1761a0'
      },
      farming: {
        title: "Farming",
        items: [
          ['🌾',"Articles","{{ route('farming.index') }}#articles"],
          ['💰',"Mandi","{{ route('farming.index') }}#mandi"],
          ['🏛',"Schemes","{{ route('farming.index') }}#schemes"],
          ['💼',"Agri Jobs","{{ route('farming.index') }}#jobs"],
          ['📅',"Calendar","{{ route('farming.index') }}#calendar"],
          ['🌦',"Weather","{{ route('farming.index') }}#weather"],
          ['+',"Share Blog","{{ route('farming.create') }}"],
        ],
        rec: ["City-wise farming data","Anyone can publish blog with rich editor","Live weather/mandi/news APIs"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#2e7d32" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3 7h-6l3-7zM6 12h12v8H6z"/></svg>',
        color: '#2e7d32'
      },
      rents: {
        title: "Rents",
        items: [['R',"Houses","{{ route('rents.index') }}"],['F',"Flats","{{ route('rents.index') }}"]],
        rec: ["New listings","Saved searches"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#ff8f00" xmlns="http://www.w3.org/2000/svg"><path d="M12 3l9 7h-3v8h-12v-8H3l9-7z"/></svg>',
        color: '#ff8f00'
      },
      buy: {
        title: "Buy & Sell",
        items: [
          ['🛒',"All Items","{{ route('buy.index') }}"],
          ['🚗',"Vehicles","{{ route('buy.index', ['subcategory' => 'vehicles']) }}"],
          ['🏍',"Bikes","{{ route('buy.index', ['subcategory' => 'bikes']) }}"],
          ['📱',"Mobile Phones","{{ route('buy.index', ['subcategory' => 'mobile']) }}"],
          ['💻',"Electronics","{{ route('buy.index', ['subcategory' => 'electronics']) }}"],
          ['🚜',"Farm Equip","{{ route('buy.index', ['subcategory' => 'farm']) }}"],
          ['🌾',"Land","{{ route('buy.index', ['subcategory' => 'land']) }}"],
        ],
        rec: ["Post listing with payment proof","Filter by city and category"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#6f42c1" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18v2H3zM7 10h10v8H7z"/></svg>',
        color: '#6f42c1'
      },
      services: {
        title: "Services",
        items: [['S',"Service ID Cards","{{ route('services.index') }}"]],
        rec: ["Top rated providers","Verified profiles"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#0d6efd" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v2H4zM4 10h16v8H4z"/></svg>',
        color: '#0d6efd'
      },
      admin: {
        title: "Admin Panel",
        items: [
          ['🏠',"Dashboard",      "{{ route('admin.dashboard') }}"],
          ['👤',"Users",          "{{ route('admin.users.index') }}"],
          ['🏬',"Shops",          "{{ route('admin.shops.index') }}"],
          ['🏙',"Cities",         "{{ route('admin.cities.index') }}"],
          ['💼',"Jobs",           "{{ route('admin.jobs.index') }}"],
          ['🏷',"Offers",         "{{ route('admin.offers.index') }}"],
          ['💳',"Subscriptions",  "{{ route('admin.subscriptions.index') }}"],
          ['⚙',"Settings",       "{{ route('admin.settings.index') }}"],
        ],
        rec: ["Manage platform modules","Review unlock/subscription queue","Keep data updated and clean"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#2f4e74" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l9 4v6c0 5-3.5 9.5-9 10-5.5-.5-9-5-9-10V6l9-4zm0 3.2L6 7.7v4.2c0 3.9 2.6 7.3 6 7.9 3.4-.6 6-4 6-7.9V7.7l-6-2.5z"/></svg>',
        color: '#2f4e74'
      },
      myservice: {
        title: "My Service",
        items: [
          ['🌐',"My Page",          myServicePageUrl],
          ['⚙',"Configure",       "{{ route('configure_myservice') }}"],
          ['👥',"Workers",         "{{ route('workers_myservice') }}"],
          ['🏷',"Add Offer",       "{{ route('myservice_offer_new') }}"],
          ['📞',"Client Requests", "{{ route('myservice_requests') }}"],
          ['📋',"Experience",      "{{ route('myservice_experience') }}"],
          ['🪪',"ID Card",         "{{ route('myservice_idcard') }}"],
          ['⭐',"Subscription",    "{{ route('subscriptions.new') }}"],
          ['📊',"Dashboard",       "{{ route('shop_dashboard') }}"],
        ],
        rec: ["Manage your service team","Handle client requests quickly","Share your ID card"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#2f4e74" xmlns="http://www.w3.org/2000/svg"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>',
        color: '#2f4e74'
      },
      myshop: {
        title: "My Shop",
        items: [
          ['🏠',"Shop Home",       "{{ route('myshop') }}"],
          ['⚙',"Configure",       "{{ route('configure_myshop') }}"],
          ['👥',"Workers",         "{{ route('workers_myshop') }}"],
          ['🏷',"Add Offer",       "{{ route('myshop_offer_new') }}"],
          ['📞',"Client Requests", "{{ route('myshop_requests') }}"],
          ['📋',"Experience",      "{{ route('myshop_experience') }}"],
          ['🪪',"ID Card",         "{{ route('myshop_idcard') }}"],
          ['⭐',"Subscription",    "{{ route('subscriptions.new') }}"],
          ['📊',"Dashboard",       "{{ route('shop_dashboard') }}"],
        ],
        rec: ["Manage your shop team","Handle client requests quickly","Share your ID card"],
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
            label = `<a class="label" href="${i[2]}" ${isExternal ? 'target="_blank" rel="noopener"' : ''}>${i[1]}</a>`;
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
          actionLink.textContent = 'Copied ✓';
          setTimeout(() => actionLink.textContent = prev, 1300);
        } catch (err) {
          window.prompt('Copy this link:', url);
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
    renderFor(initialKey);
  });

  document.addEventListener('DOMContentLoaded', function(){
    const storageKey = 'aajchaoffer_guide_seen_v1';
    const guideCard = document.getElementById('guide-card');
    const guideToggle = document.getElementById('guide-toggle');
    const guideClose = document.getElementById('guide-close');
    const guideLater = document.getElementById('guide-later');
    const guideDone = document.getElementById('guide-done');
    const focusButtons = document.querySelectorAll('.guide-focus-btn');

    if (!guideCard || !guideToggle) return;

    const openGuide = () => guideCard.classList.add('open');
    const closeGuide = () => guideCard.classList.remove('open');

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

    if (guideClose) {
      guideClose.addEventListener('click', closeGuide);
    }

    if (guideLater) {
      guideLater.addEventListener('click', closeGuide);
    }

    if (guideDone) {
      guideDone.addEventListener('click', function(){
        localStorage.setItem(storageKey, '1');
        closeGuide();
      });
    }

    focusButtons.forEach(function(btn){
      btn.addEventListener('click', function(){
        const target = findVisibleTarget(this.getAttribute('data-focus'));
        if (target) focusElement(target);
      });
    });

    if (!localStorage.getItem(storageKey)) {
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

</body>
</html>
