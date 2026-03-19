<!DOCTYPE html>
<html>
<head>
  <title>AajchaOffer</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/application.css') }}">
  <style>
    .global-top{position:sticky;top:0;z-index:120;background:rgba(255,248,240,.96);backdrop-filter:blur(14px);border-bottom:1px solid #F0E8DC;padding:.62rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:.7rem}
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
    @media(max-width:700px){
      .global-copy small{max-width:150px}
      .global-time{display:none}
      .global-city-form{display:none}
      .mobile-city-btn{display:inline-flex}
    }
  </style>
</head>
<body>

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
      <div class="panel-title {{ request()->routeIs('home') ? 'active' : '' }}">{{ request()->routeIs('about') ? 'About Us' : 'Home' }}</div>
      <ul class="panel-list">
        @php $ctrl = request()->route()?->getActionMethod() ? class_basename(request()->route()->getController()) : ''; @endphp
        @if(request()->routeIs('about'))
          <li><a href="{{ route('about') }}" class="panel-link">Our Story</a></li>
          <li><a href="{{ route('about') }}#contact" class="panel-link">Contact</a></li>
        @elseif(str_contains($ctrl, 'Home'))
          <li><a href="{{ route('home') }}" class="panel-link">Today's Pulses</a></li>
          <li><a href="{{ route('offers') }}" class="panel-link">Offers &amp; Benefits</a></li>
        @elseif(str_contains($ctrl, 'Updates'))
          <li><a href="{{ route('updates.index') }}" class="panel-link">News</a></li>
          <li><a href="{{ route('updates.index') }}" class="panel-link">Alerts</a></li>
        @elseif(str_contains($ctrl, 'Jobs'))
          <li><a href="{{ route('jobs.index') }}" class="panel-link">Shops</a></li>
          <li><a href="{{ route('jobs.index') }}" class="panel-link">Government</a></li>
        @elseif(str_contains($ctrl, 'Farming'))
          <li><a href="{{ route('farming.index') }}" class="panel-link">Crops</a></li>
          <li><a href="{{ route('farming.index') }}" class="panel-link">Markets</a></li>
        @elseif(str_contains($ctrl, 'Rents'))
          <li><a href="{{ route('rents.index') }}" class="panel-link">Houses</a></li>
          <li><a href="{{ route('rents.index') }}" class="panel-link">Flats</a></li>
        @elseif(str_contains($ctrl, 'Buy'))
          <li><a href="{{ route('buy.index') }}" class="panel-link">Electronics</a></li>
          <li><a href="{{ route('buy.index') }}" class="panel-link">Cars</a></li>
        @elseif(str_contains($ctrl, 'Services'))
          <li><a href="{{ route('services.index') }}" class="panel-link">Plumbing</a></li>
          <li><a href="{{ route('services.index') }}" class="panel-link">Events</a></li>
        @else
          <li><a href="{{ route('home') }}" class="panel-link">Today's Pulses</a></li>
          <li><a href="{{ route('offers') }}" class="panel-link">Offers &amp; Benefits</a></li>
        @endif
      </ul>
      <div class="panel-details">
        <h4>Recommended</h4>
        <ul class="recommendations">
          <li>See today's top stories</li>
          <li>Nearby events</li>
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
      $currentCityId = session('city_id');
      $currentCityName = $currentCityId ? \App\Models\City::find($currentCityId)?->name : null;
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
        items: [['N',"News","{{ route('updates.index') }}"],['A',"Alerts","{{ route('updates.index') }}"]],
        rec: ["Subscribe to alerts","Manage notifications"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#0b5ed7" xmlns="http://www.w3.org/2000/svg"><path d="M3 5h18v2H3zM3 11h12v2H3zM3 17h18v2H3z"/></svg>',
        color: '#0b5ed7'
      },
      jobs: {
        title: "Jobs",
        items: [['S',"Shops","{{ route('jobs.index') }}"],['G',"Government","{{ route('jobs.index') }}"]],
        rec: ["Post a job","Manage applications"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#1761a0" xmlns="http://www.w3.org/2000/svg"><path d="M6 7h12v2H6zM6 11h12v6H6z"/></svg>',
        color: '#1761a0'
      },
      farming: {
        title: "Farming",
        items: [['F',"Crops","{{ route('farming.index') }}"],['M',"Markets","{{ route('farming.index') }}"]],
        rec: ["Farming tips","Local suppliers"],
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
        title: "Buy",
        items: [['B',"Electronics","{{ route('buy.index') }}"],['C',"Cars","{{ route('buy.index') }}"]],
        rec: ["Create listing","Popular categories"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#6f42c1" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18v2H3zM7 10h10v8H7z"/></svg>',
        color: '#6f42c1'
      },
      services: {
        title: "Services",
        items: [['S',"Plumbing","{{ route('services.index') }}"],['E',"Events","{{ route('services.index') }}"]],
        rec: ["Top rated","Request a service"],
        icon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="#0d6efd" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v2H4zM4 10h16v8H4z"/></svg>',
        color: '#0d6efd'
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
      panelList.innerHTML = def.items.map((i,idx)=>{
        const badge = `<span class="badge">${i[0]}</span>`;
        const label = i[2] ? `<a class="label" href="${i[2]}">${i[1]}</a>` : `<span class="label">${i[1]}</span>`;
        return `<li class="${idx===0?'active-item':''}">${badge}${label}</li>`;
      }).join('');
      recList.innerHTML = def.rec.map(r=>`<li>${r}</li>`).join('');
    }

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

    const initial = document.querySelector('.icon-item.selected');
    const initialKey = initial&&initial.dataset.key ? initial.dataset.key : 'home';
    renderFor(initialKey);
  });
</script>

</body>
</html>
