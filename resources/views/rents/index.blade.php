@extends('layouts.app')

@section('content')
<style>
  :root {
    --gold:#D4A017;--gold-light:#F0C040;--dark:#0D0D0D;--dark2:#141414;--dark3:#1C1C1C;--dark4:#252525;
    --card-bg:#181818;--text:#F5F0E8;--text-muted:#9A9080;--text-dim:#5A5248;
    --accent-green:#2EC87A;--accent-red:#E84040;--accent-blue:#4A8FE8;
    --border:rgba(212,160,23,0.15);--border-hover:rgba(212,160,23,0.4);--radius:14px;
  }
  *{box-sizing:border-box;margin:0;padding:0}

  /* HERO */
  .hero{padding:3rem 2rem;text-align:center;position:relative;overflow:hidden;border-bottom:1px solid var(--border)}
  .hero::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:600px;height:300px;background:radial-gradient(ellipse,rgba(212,160,23,0.12) 0%,transparent 70%);pointer-events:none}
  .hero h1{font-size:clamp(2rem,5vw,3.5rem);font-weight:700;line-height:1.05;letter-spacing:-1px;margin-bottom:1rem}
  .hero h1 span{color:var(--gold)}
  .hero p{color:var(--text-muted);font-size:1rem;max-width:520px;margin:0 auto 2rem;line-height:1.6}
  .hero-search{display:flex;max-width:580px;margin:0 auto;background:var(--dark3);border:1px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s}
  .hero-search:focus-within{border-color:var(--gold)}
  .hero-search select{background:var(--dark4);border:none;color:var(--text-muted);font-size:13px;padding:0 16px;border-right:1px solid var(--border);cursor:pointer;outline:none;min-width:120px}
  .hero-search input{flex:1;background:transparent;border:none;color:var(--text);font-size:14px;padding:12px 16px;outline:none}
  .hero-search input::placeholder{color:var(--text-dim)}
  .hero-search .search-btn{background:var(--gold);border:none;color:var(--dark);padding:0 24px;font-weight:700;font-size:13px;cursor:pointer;transition:background .2s}
  .hero-search .search-btn:hover{background:var(--gold-light)}

  /* STATS */
  .stats-strip{display:flex;justify-content:center;border-bottom:1px solid var(--border);background:var(--dark2);overflow-x:auto}
  .stat-item{padding:1rem 2rem;border-right:1px solid var(--border);text-align:center;white-space:nowrap}
  .stat-item:last-child{border-right:none}
  .stat-num{font-size:1.5rem;font-weight:700;color:var(--gold);letter-spacing:-1px}
  .stat-label{font-size:12px;color:var(--text-muted);margin-top:2px}

  /* SECTION */
  .section{padding:2rem;max-width:1280px;margin:0 auto}
  .section-heading{font-size:1.3rem;color:#27344A;font-weight:800;letter-spacing:.2px;text-shadow:0 1px 0 rgba(255,255,255,0.45)}

  /* CATEGORY TABS */
  .cat-tabs{display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none}
  .cat-tabs::-webkit-scrollbar{display:none}
  .cat-tab{display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;border:1px solid var(--border);background:var(--dark3);cursor:pointer;white-space:nowrap;font-size:13px;font-weight:500;color:var(--text-muted);transition:all .2s}
  .cat-tab:hover{border-color:var(--border-hover);color:var(--text)}
  .cat-tab.active{background:rgba(212,160,23,0.12);border-color:var(--gold);color:var(--gold)}

  /* FILTER */
  .filter-bar{display:flex;gap:10px;align-items:center;margin:1rem 0;flex-wrap:wrap}
  .filter-chip{padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--dark3);color:var(--text-muted);font-size:12px;cursor:pointer;transition:all .2s}
  .filter-chip:hover,.filter-chip.active{border-color:var(--gold);color:var(--gold);background:rgba(212,160,23,0.06)}

  /* LISTINGS GRID */
  .listings-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;margin-top:1rem}
  .listing-card{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:all .25s;cursor:pointer;position:relative;animation:fadeInUp .4s ease both}
  .listing-card:hover{border-color:var(--border-hover);transform:translateY(-3px);box-shadow:0 12px 40px rgba(0,0,0,0.4)}
  .card-img-wrapper{position:relative;height:180px;background:linear-gradient(135deg,var(--dark3),var(--dark4));display:flex;align-items:center;justify-content:center;font-size:3rem;overflow:hidden}
  .card-img-wrapper img{width:100%;height:100%;object-fit:cover;display:block}
  .card-badge{position:absolute;top:10px;left:10px;padding:4px 10px;border-radius:5px;font-size:10px;font-weight:700;letter-spacing:.5px}
  .badge-rent{background:rgba(74,143,232,0.9);color:#fff}
  .badge-new{background:rgba(46,200,122,0.9);color:#fff}
  .badge-used{background:rgba(232,64,64,0.85);color:#fff}
  .card-fav{position:absolute;top:10px;right:10px;width:30px;height:30px;border-radius:50%;background:rgba(0,0,0,0.6);border:none;color:var(--text-muted);font-size:14px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s}
  .card-fav:hover,.card-fav.active{color:#E84040;background:rgba(232,64,64,0.15)}
  .card-body{padding:14px 16px}
  .card-title{font-size:14px;font-weight:600;color:var(--text);margin-bottom:6px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .card-price{font-size:1.2rem;font-weight:700;color:var(--gold);margin-bottom:8px}
  .card-price span{font-size:12px;color:var(--text-muted);font-weight:400}
  .card-meta{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--text-dim);padding-top:8px;border-top:1px solid var(--border)}

  /* NO RESULTS */
  .no-results{text-align:center;padding:3rem;color:var(--text-muted);display:none;grid-column:1/-1}
  .no-results.show{display:block}

  .btn-primary{background:var(--gold);border:none;color:var(--dark);padding:10px 20px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:700;transition:all .2s;font-family:inherit}
  .btn-primary:hover{background:var(--gold-light);transform:translateY(-1px)}
  .btn-outline{background:transparent;border:1px solid var(--border-hover);color:var(--gold);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;font-family:inherit;transition:all .2s}
  .btn-outline:hover{background:rgba(212,160,23,0.08)}

  /* DETAIL PANEL */
  .detail-panel{display:none;position:fixed;right:0;top:0;bottom:0;width:min(480px,100%);z-index:150;background:var(--dark2);border-left:1px solid var(--border);overflow-y:auto;transform:translateX(100%);transition:transform .3s}
  .detail-panel.open{display:block;transform:translateX(0)}
  .panel-hdr{padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;position:sticky;top:0;background:var(--dark2);z-index:5}
  .panel-cls{background:none;border:none;color:var(--text-muted);font-size:20px;cursor:pointer;padding:4px}
  .panel-body{padding:1.5rem}
  .panel-media{width:100%;height:240px;background:var(--dark4);border-radius:12px;margin-bottom:1.5rem;overflow:hidden;display:flex;align-items:center;justify-content:center}
  .panel-media img{width:100%;height:100%;object-fit:cover;display:none}
  .panel-placeholder{width:100%;height:240px;display:flex;align-items:center;justify-content:center;font-size:5rem;background:var(--dark4);border-radius:12px;margin-bottom:1.5rem}
  .panel-price{font-size:1.8rem;font-weight:700;color:var(--gold);margin-bottom:6px}
  .panel-title{font-size:1.05rem;font-weight:600;margin-bottom:12px}
  .panel-desc{color:var(--text-muted);font-size:13px;line-height:1.6;margin-bottom:1.5rem}
  .panel-specs{background:var(--dark3);border-radius:10px;padding:1rem;margin-bottom:1.5rem}
  .spec-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:12px}
  .spec-row:last-child{border-bottom:none}
  .spec-k{color:var(--text-muted)}
  .spec-v{font-weight:500}
  .panel-actions{display:flex;gap:10px}
  .panel-actions .btn-primary{flex:1;padding:12px}

  .toast{position:fixed;bottom:2rem;right:2rem;z-index:300;background:var(--dark3);border:1px solid var(--accent-green);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text);transform:translateY(100px);opacity:0;transition:all .3s;max-width:300px}
  .toast.show{transform:translateY(0);opacity:1}

  @keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  .listing-card:nth-child(1){animation-delay:.05s}.listing-card:nth-child(2){animation-delay:.1s}.listing-card:nth-child(3){animation-delay:.15s}.listing-card:nth-child(4){animation-delay:.2s}.listing-card:nth-child(5){animation-delay:.25s}.listing-card:nth-child(6){animation-delay:.3s}

  @media(max-width:1024px){
    .listings-grid{grid-template-columns:repeat(auto-fill,minmax(200px,1fr))}
  }

  @media(max-width:768px){
    .form-row{grid-template-columns:1fr}
    .listings-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px}
    .listing-card{animation-delay:0 !important}
    .detail-panel{width:100%}
    .hero-search{flex-direction:column}
    .hero-search select,.hero-search .search-btn{border-right:none;border-bottom:1px solid var(--border)}
    .section{padding:1.25rem}
    .section-heading{font-size:1.15rem}
    .card-price{font-size:1rem}
    .card-title{font-size:13px}
  }

  @media(max-width:640px){
    .listings-grid{grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px}
    .card-img-wrapper{height:140px}
    .card-body{padding:10px 12px}
    .card-badge{padding:3px 8px;font-size:9px}
    .card-meta{font-size:11px}
  }

  @media(max-width:480px){
    .listings-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
    .card-img-wrapper{height:120px}
    .card-title{font-size:12px;line-height:1.2}
    .card-price{font-size:.95rem;margin-bottom:4px}
    .card-body{padding:8px 10px}
    .filter-bar{flex-direction:column;gap:6px}
    .filter-chip{width:100%;justify-content:center}
  }

  @media(max-width:360px){
    .listings-grid{grid-template-columns:1fr}
    .card-img-wrapper{height:100px}
  }
</style>

<div class="hero">
  <h1>{{ __('ui.rent_title_prefix') }} <span>{{ __('ui.rent_title_suffix') }}</span></h1>
  <p>{{ __('ui.rent_subtitle') }}</p>
  <div class="hero-search">
    <select id="searchCategory" onchange="applyFilters()"><option value="">{{ __('ui.all_types') }}</option><option value="house">🏠 {{ __('ui.houses') }}</option><option value="flat">🏢 {{ __('ui.flats') }}</option><option value="shop">🏪 {{ __('ui.shops') }}</option><option value="office">💼 {{ __('ui.offices') }}</option><option value="land">🌾 {{ __('ui.land') }}</option></select>
    <input type="text" id="searchInput" placeholder="{{ __('ui.search_properties') }}" oninput="applyFilters()">
    <button class="search-btn" onclick="applyFilters()">{{ __('ui.search') }}</button>
  </div>
</div>

<div class="stats-strip">
  <div class="stat-item"><div class="stat-num" id="totalListings">{{ $listings->total() }}</div><div class="stat-label">{{ __('ui.active_properties') }}</div></div>
  <div class="stat-item"><div class="stat-num" id="cityListings">{{ $cityActive }}</div><div class="stat-label">{{ $selectedCityName ? city_display_name($selectedCityName) : __('ui.all_cities') }}</div></div>
</div>

<div class="section">
  @php
    $rentPostUrl = \Illuminate\Support\Facades\Route::has('rents.new')
      ? route('rents.new')
      : route('rents.create');
  @endphp

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:1rem">
    <h2 class="section-heading">{{ __('ui.browse_properties') }}</h2>
    @auth
      @if(auth()->user()->isSeller() || auth()->user()->isSuperadmin())
        <a class="btn-primary" href="{{ $rentPostUrl }}">+ {{ __('ui.post_rental') }}</a>
      @else
        <a class="btn-primary" href="{{ route('register', ['role' => 'seller', 'redirect_to' => $rentPostUrl]) }}">+ {{ __('ui.post_rental') }}</a>
      @endif
    @else
      <a class="btn-primary" href="{{ route('login', ['redirect_to' => $rentPostUrl]) }}">+ {{ __('ui.post_rental') }}</a>
    @endauth
  </div>

  <div class="cat-tabs">
    <div class="cat-tab active" onclick="filterByCat('',this)" data-cat="">🔥 {{ __('ui.all') }}</div>
    <div class="cat-tab" onclick="filterByCat('house',this)" data-cat="house">🏠 {{ __('ui.houses') }}</div>
    <div class="cat-tab" onclick="filterByCat('flat',this)" data-cat="flat">🏢 {{ __('ui.flats') }}</div>
    <div class="cat-tab" onclick="filterByCat('shop',this)" data-cat="shop">🏪 {{ __('ui.shops') }}</div>
    <div class="cat-tab" onclick="filterByCat('office',this)" data-cat="office">💼 {{ __('ui.offices') }}</div>
    <div class="cat-tab" onclick="filterByCat('land',this)" data-cat="land">🌾 {{ __('ui.land') }}</div>
  </div>

  <form method="GET" action="{{ route('rents.index') }}" class="filter-bar" id="filterForm" style="margin-top:1rem">
    <input type="hidden" id="subcategoryFilter" name="subcategory" value="{{ $subcategory ?? '' }}">
    <input type="hidden" id="cityIdFilter" name="city_id" value="{{ $selectedCityId ?? '' }}">
    <input type="hidden" id="searchFilter" name="q" value="{{ $search ?? '' }}">
    <select name="city_id_select" id="citySelect" onchange="document.getElementById('cityIdFilter').value=this.value;document.getElementById('filterForm').submit()" class="filter-chip" style="margin-left:auto">
      <option value="">{{ __('ui.all_cities') }}</option>
      @foreach($cities as $city)
        <option value="{{ $city->id }}" {{ (int)($selectedCityId ?? 0) === (int)$city->id ? 'selected' : '' }}>📍 {{ city_display_name($city->name) }}</option>
      @endforeach
    </select>
  </form>

  {{-- Listings Grid --}}
  <div class="listings-grid" id="listingsGrid">
    @forelse($listings as $item)
      @php
        $firstPhotoPath = is_array($item->photos ?? null) && count($item->photos) ? $item->photos[0] : null;
        $firstPhotoUrl = $firstPhotoPath ? Storage::url($firstPhotoPath) : null;
        $callDigits = preg_replace('/\D+/', '', (string) ($item->contact_number ?? ''));
        $panelPayload = json_encode([
          'title' => $item->title,
          'price' => $item->price,
          'description' => $item->description,
          'city' => $item->city?->name ?: __('ui.city_not_set'),
          'subcategory' => $item->subcategory,
          'photo' => $firstPhotoUrl,
          'call' => $callDigits,
          'url' => route('rents.show', $item),
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
      @endphp
      <div class="listing-card" onclick='openPanel({!! $panelPayload !!})'>
        <div class="card-img-wrapper">
          @if($firstPhotoUrl)
            <img src="{{ $firstPhotoUrl }}" alt="{{ $item->title }}">
          @else
            @php
              $emojis = [
                'house' => '🏠', 'flat' => '🏢', 'apartment' => '🏢',
                'shop' => '🏪', 'office' => '💼', 'land' => '🌾'
              ];
              $emoji = $emojis[$item->subcategory] ?? '🏠';
            @endphp
            {{ $emoji }}
          @endif
        </div>
        <div class="card-badge badge-rent">{{ __('ui.rent_badge') }}</div>
        <button class="card-fav" onclick="event.stopPropagation()">♥</button>
        <div class="card-body">
          <div class="card-title">{{ $item->title }}</div>
          <div class="card-price">{{ $item->price ? '₹' . number_format((float)$item->price, 0) . '/mo' : __('ui.contact') }} <span>{{ __('ui.monthly_rent') }}</span></div>
          <div class="card-meta">
            <span>📍 {{ $item->city?->name ?: __('ui.city_not_set') }}</span>
            <span>⭐ {{ __('ui.verified') }}</span>
          </div>
        </div>
      </div>
    @empty
      <div class="no-results show" style="grid-column:1/-1">
        <div style="font-size:3rem;margin-bottom:1rem">🔍</div>
        <p>{{ __('ui.no_properties') }} <button class="btn-outline" onclick="window.location.href='{{ route('rents.index') }}'">{{ __('ui.view_all') }}</button></p>
      </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($listings->count())
    <div style="margin-top:2rem;text-align:center">
      {{ $listings->links('pagination::simple-bootstrap-5') }}
    </div>
  @endif
</div>

<!-- DETAIL PANEL -->
<div class="detail-panel" id="detailPanel">
  <div class="panel-hdr">
    <button class="panel-cls" onclick="closePanel()">✕</button>
    <span style="font-size:14px;color:var(--text-muted)">{{ __('ui.property_detail') }}</span>
  </div>
  <div class="panel-body">
    <div class="panel-media">
      <img id="panelPhoto" alt="{{ __('ui.rental_photo_alt') }}">
      <div class="panel-placeholder" id="panelIcon">🏠</div>
    </div>
    <div class="panel-price" id="panelPrice"></div>
    <div class="panel-title" id="panelTitle"></div>
    <div class="panel-desc" id="panelDesc"></div>
    <div class="panel-specs" id="panelSpecs"></div>
    <div class="panel-actions" style="flex-wrap:wrap">
      <a class="btn-primary" id="panelCallLink" href="#" style="text-decoration:none;text-align:center;flex:1">📞 {{ __('ui.call_landlord') }}</a>
      <a class="btn-outline" id="panelViewLink" href="#" style="text-decoration:none;text-align:center">{{ __('ui.view_details') }}</a>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span>✅</span><span id="toastMsg"></span></div>

<script>
const rentsI18n = {
  rentalProperty: @json(__('ui.rental_property')),
  contact: @json(__('ui.contact')),
  noDescription: @json(__('ui.no_description_available')),
  location: @json(__('ui.location')),
  type: @json(__('ui.type')),
  general: @json(__('ui.general')),
  cityNotSet: @json(__('ui.city_not_set')),
};

function applyFilters(){
  const search = document.getElementById('searchInput').value;
  const cat = document.getElementById('searchCategory').value;
  const form = document.getElementById('filterForm');
  document.getElementById('searchFilter').value = search;
  if(cat) document.getElementById('subcategoryFilter').value = cat;
  form.submit();
}

function filterByCat(cat, el){
  document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('subcategoryFilter').value = cat;
  document.getElementById('filterForm').submit();
}

function openPanel(payload){
  const iconMap = {house:'🏠',flat:'🏢',apartment:'🏢',shop:'🏪',office:'💼',land:'🌾'};
  const panelPhoto = document.getElementById('panelPhoto');
  const panelIcon = document.getElementById('panelIcon');
  const panelCallLink = document.getElementById('panelCallLink');
  const panelViewLink = document.getElementById('panelViewLink');

  document.getElementById('panelTitle').textContent = payload.title || rentsI18n.rentalProperty;
  document.getElementById('panelPrice').textContent = payload.price ? '₹' + Number(payload.price).toLocaleString('en-IN') + '/mo' : rentsI18n.contact;
  document.getElementById('panelDesc').textContent = payload.description || rentsI18n.noDescription;
  document.getElementById('panelSpecs').innerHTML = `<div class="spec-row"><span class="spec-k">${rentsI18n.location}</span><span class="spec-v">${payload.city || rentsI18n.cityNotSet}</span></div><div class="spec-row"><span class="spec-k">${rentsI18n.type}</span><span class="spec-v">${(payload.subcategory || rentsI18n.general).toString()}</span></div>`;

  if(payload.photo){
    panelPhoto.src = payload.photo;
    panelPhoto.style.display = 'block';
    panelIcon.style.display = 'none';
  } else {
    panelPhoto.style.display = 'none';
    panelIcon.style.display = 'flex';
    panelIcon.textContent = iconMap[payload.subcategory] || '🏠';
  }

  if(payload.call){
    panelCallLink.href = `tel:${payload.call}`;
    panelCallLink.style.pointerEvents = 'auto';
    panelCallLink.style.opacity = '1';
  } else {
    panelCallLink.href = '#';
    panelCallLink.style.pointerEvents = 'none';
    panelCallLink.style.opacity = '0.6';
  }

  panelViewLink.href = payload.url || '#';
  document.getElementById('detailPanel').classList.add('open');
}

function closePanel(){
  document.getElementById('detailPanel').classList.remove('open');
}

function showToast(msg){
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
@endsection
