@extends('layouts.app')

@section('content')
@php
  $dashboardRoute = $dashboardRoute ?? 'myservice';
  $configureSaveRoute = $configureSaveRoute ?? 'configure_myservice_save';
  $unlockRoute = $unlockRoute ?? 'myservice.unlock';
  $entityLabel = $entityLabel ?? 'Service';
  $entityPluralLabel = $entityPluralLabel ?? 'Services';
  $providerProfileLabel = $providerProfileLabel ?? 'Service Provider Profile';
@endphp
<style>
  .cfg-section{background:#fff;border:1px solid #dbe7f8;border-radius:10px;padding:16px;margin-top:12px}
  .cfg-section,.cfg-section *{box-sizing:border-box}
  .cfg-section{overflow:hidden}
  .cfg-section .form-row{min-width:0}
  .cfg-section input[type="text"],
  .cfg-section input[type="email"],
  .cfg-section input[type="url"],
  .cfg-section input[type="file"],
  .cfg-section textarea,
  .cfg-section select{width:100%;max-width:100%;min-width:0}
  .cfg-section textarea{resize:vertical}
  .cfg-section h3{margin:0 0 10px;font-size:15px;color:#2f4e74;border-bottom:1px solid #e8eef9;padding-bottom:6px}
  .cfg-section h3 span{font-size:11px;font-weight:400;color:#86a0be;margin-left:6px}
  .template-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px}
  .tpl-card{border:2px solid #dbe7f8;border-radius:12px;padding:12px;cursor:pointer;transition:border-color .2s,background .2s,transform .2s;text-align:left;background:#fafcff}
  .tpl-card.active{border-color:#2f4e74;background:#eef4ff}
  .tpl-card:hover{transform:translateY(-1px)}
  .tpl-card.locked{border-color:#f2d27a;background:#fffaf0}
  .tpl-card h4{margin:0 0 4px;font-size:14px;color:#2f4e74}
  .tpl-card p{margin:0;font-size:12px;color:#6d84a5}
  .tpl-chip{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:3px 8px;border-radius:999px;margin-bottom:8px}
  .tpl-chip.free{background:#e8f7ee;color:#1f8a49;border:1px solid #ccefd9}
  .tpl-chip.paid{background:#fff2d8;color:#9a6500;border:1px solid #f4d49a}
  .tpl-chip.unlocked{background:#e6f9ee;color:#166534;border:1px solid #6ee7a0}
  .unlock-box{margin-top:12px;border:1px solid #f2d27a;background:#fffaf0;border-radius:10px;padding:14px}
  .unlock-box h4{margin:0 0 8px;color:#8c5a00}
  .unlock-box p{margin:0 0 10px;color:#7b6a3d;font-size:13px;line-height:1.6}
  .unlock-modal{position:fixed;inset:0;z-index:1400;display:flex;align-items:center;justify-content:center;padding:16px;overflow-y:auto}
  .unlock-modal-backdrop{position:absolute;inset:0;background:rgba(8,14,26,.58)}
  .unlock-modal-dialog{position:relative;width:min(760px,calc(100vw - 24px));max-height:calc(100vh - 32px);overflow:auto;background:#fff;border-radius:12px;box-shadow:0 18px 48px rgba(11,22,40,.28);border:1px solid #dbe7f8;padding:18px 18px 16px}
  .unlock-modal-close{position:absolute;top:10px;right:10px;border:1px solid #d0dff4;background:#fff;border-radius:8px;width:32px;height:32px;font-size:20px;line-height:1;cursor:pointer;color:#35527a}
  .unlock-form{display:grid;gap:10px}
  .unlock-grid{display:grid;grid-template-columns:1fr;gap:10px}
  .unlock-box .form-row{margin:0}
  .unlock-box input[type="text"],.unlock-box input[type="file"],.unlock-box textarea{width:100%;box-sizing:border-box}
  .unlock-box input[type="file"]{padding:8px;background:#fff;border:1px solid #d9e4f4;border-radius:8px}
  .unlock-pay-row{display:flex;gap:14px;align-items:center;flex-wrap:wrap}
  .unlock-qr-img{width:180px;height:180px;object-fit:cover;border:1px solid #ecd7a2;border-radius:8px;background:#fff}
  .unlock-pay-note{font-size:12px;color:#7b6a3d;line-height:1.65;max-width:360px}
  .unlock-pay-unavailable{padding:10px 12px;border:1px solid #f0d79a;background:#fff3df;border-radius:8px;color:#8c5a00;font-size:12px;font-weight:600}
  @media (min-width: 980px){.unlock-grid.two-col{grid-template-columns:1fr 1fr}}
  @media (max-width: 680px){
    .unlock-modal{padding:0;align-items:flex-end;justify-content:stretch}
    .unlock-modal-dialog{width:100vw;max-height:90vh;padding:12px 12px 10px;border-radius:14px 14px 0 0;border-bottom:none}
    .unlock-modal-close{top:6px;right:6px;width:30px;height:30px}
    .unlock-box{padding:10px}
    .unlock-pay-row{align-items:flex-start;flex-direction:column}
    .unlock-qr-img{width:min(84vw,280px);height:min(84vw,280px);max-width:100%}
    .unlock-pay-note{max-width:100%}
    .unlock-grid.two-col{grid-template-columns:1fr}
  }
  .tpl-thumb{height:130px;border-radius:10px;overflow:hidden;border:1px solid #dbe7f8;margin-bottom:10px;position:relative;background:#fff}
  .tpl-thumb.dynamic{background:linear-gradient(135deg,#1a0a3b,#7b2ff7)}
  .tpl-thumb.dynamic::before{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.08),transparent)}
  .tpl-thumb.dynamic .mini-top{height:24px;background:rgba(255,255,255,.12)}
  .tpl-thumb.dynamic .mini-hero{padding:14px}
  .tpl-thumb.dynamic .mini-pill{width:74px;height:10px;border-radius:999px;background:rgba(255,255,255,.28);margin-bottom:10px}
  .tpl-thumb.dynamic .mini-title{width:90px;height:14px;border-radius:6px;background:#fff;margin-bottom:8px}
  .tpl-thumb.dynamic .mini-sub{width:120px;height:9px;border-radius:5px;background:rgba(255,255,255,.5);margin-bottom:12px}
  .tpl-thumb.dynamic .mini-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px}
  .tpl-thumb.dynamic .mini-box{height:28px;border-radius:8px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.18)}
  .tpl-thumb.astro{background:linear-gradient(135deg,#05000f,#4b1f7a 48%,#c9860a 120%)}
  .tpl-thumb.astro::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 20% 20%,rgba(255,255,255,.12),transparent 32%),radial-gradient(circle at 80% 70%,rgba(245,197,24,.16),transparent 28%)}
  .tpl-thumb.astro .mini-top{height:22px;background:rgba(0,0,0,.24);display:flex;gap:4px;align-items:center;padding:0 8px}
  .tpl-thumb.astro .mini-dot{width:18px;height:6px;border-radius:999px;background:rgba(245,197,24,.5)}
  .tpl-thumb.astro .mini-hero{padding:12px;color:#fff}
  .tpl-thumb.astro .mini-title{width:110px;height:15px;border-radius:6px;background:linear-gradient(90deg,#fff,#f5c518);margin-bottom:8px}
  .tpl-thumb.astro .mini-sub{width:95px;height:8px;border-radius:5px;background:rgba(255,220,150,.5);margin-bottom:12px}
  .tpl-thumb.astro .mini-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:6px}
  .tpl-thumb.astro .mini-box{height:24px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(245,197,24,.2)}
  .tpl-preview{border-radius:10px;padding:12px;margin-top:10px;font-size:12px;color:#567;background:#f3f7ff;border:1px solid #dbe7f8;min-height:210px;display:grid;grid-template-columns:minmax(220px,320px) 1fr;gap:14px;align-items:start}
  .tpl-preview-visual{border-radius:10px;overflow:hidden;border:1px solid #dbe7f8;background:#fff;min-height:180px}
  .tpl-preview-copy h4{margin:0 0 6px;color:#2f4e74;font-size:15px}
  .tpl-preview-copy p{margin:0;color:#6d84a5;line-height:1.6}
  .tpl-thumb.metro{background:linear-gradient(135deg,#1e3a5f,#2563eb)}
  .tpl-thumb.metro .mini-top{height:22px;background:rgba(0,0,0,.22);display:flex;align-items:center;padding:0 8px;gap:4px}
  .tpl-thumb.metro .mini-dot{width:16px;height:5px;border-radius:999px;background:rgba(255,255,255,.45)}
  .tpl-thumb.metro .mini-hero{padding:10px}
  .tpl-thumb.metro .mini-title{width:90px;height:12px;border-radius:5px;background:#fff;margin-bottom:7px}
  .tpl-thumb.metro .mini-sub{width:108px;height:8px;border-radius:4px;background:rgba(255,255,255,.5);margin-bottom:10px}
  .tpl-thumb.metro .mini-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:5px}
  .tpl-thumb.metro .mini-box{height:26px;border-radius:7px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.2)}
  .tpl-thumb.saffron-local{background:linear-gradient(135deg,#fffbf5,#ffe4cc 55%,#fdba74)}
  .tpl-thumb.saffron-local .mini-top{height:22px;background:#ea580c;display:flex;align-items:center;padding:0 8px;gap:4px}
  .tpl-thumb.saffron-local .mini-dot{width:16px;height:5px;border-radius:999px;background:rgba(255,255,255,.5)}
  .tpl-thumb.saffron-local .mini-hero{padding:10px}
  .tpl-thumb.saffron-local .mini-title{width:90px;height:12px;border-radius:5px;background:#ea580c;margin-bottom:7px}
  .tpl-thumb.saffron-local .mini-sub{width:108px;height:8px;border-radius:4px;background:rgba(234,88,12,.35);margin-bottom:10px}
  .tpl-thumb.saffron-local .mini-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:5px}
  .tpl-thumb.saffron-local .mini-box{height:26px;border-radius:7px;background:#fff;border:1px solid #f0d9c8}
  .tpl-card .tpl-demo-link{display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:11px;font-weight:600;color:#2f4e74;text-decoration:none;padding:4px 10px;border-radius:6px;border:1px solid #dbe7f8;background:#f0f6ff;transition:background .15s}
  .tpl-card .tpl-demo-link:hover{background:#dce9ff}
  .tpl-specific-fields{display:none;margin-top:12px;padding:12px;background:#f8fbff;border:1px solid #dbe7f8;border-radius:8px}
  .tpl-specific-fields.active{display:block}
  .city-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:6px;max-height:220px;overflow-y:auto;border:1px solid #e2ecf9;border-radius:6px;padding:8px;background:#fafcff}
  .city-item label{display:flex;align-items:center;gap:5px;font-size:13px;color:#3a5478;cursor:pointer;padding:3px 4px;border-radius:4px}
  .city-item label:hover{background:#eef4ff}
  .svc-row{display:flex;gap:8px;align-items:flex-start;border:1px solid #e8eef9;border-radius:6px;padding:10px;margin-bottom:8px;background:#fafcff}
  .svc-row .svc-body{flex:1;display:grid;gap:6px}
  .svc-row .svc-body input,.svc-row .svc-body textarea{width:100%;box-sizing:border-box}
  .svc-row .svc-body textarea{resize:vertical;min-height:52px}
  .svc-remove{color:#e55;background:none;border:1px solid #e8d;border-radius:4px;padding:2px 7px;cursor:pointer;font-size:18px;line-height:1}
  .pf-row{display:flex;gap:8px;align-items:flex-start;border:1px solid #e8eef9;border-radius:5px;padding:8px;margin-bottom:6px;background:#fafcff}
  .pf-row .pf-body{flex:1;display:grid;grid-template-columns:1fr 2fr 120px;gap:6px;align-items:center}
  .pf-row .pf-body input,.pf-row .pf-body select{width:100%;box-sizing:border-box}
  .pf-actions{display:flex;flex-direction:column;gap:3px;width:34px}
  .pf-actions button{padding:2px 5px;font-size:11px}
  .cfg-grid{display:grid;gap:10px}
  .cfg-grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}
  .cfg-grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}
  .cfg-grid.split{grid-template-columns:minmax(0,1fr) minmax(0,2fr)}
  .cfg-grid > *{min-width:0}
  .svc-inline-grid{display:grid;grid-template-columns:90px minmax(0,1fr) 150px;gap:6px}
  .svc-inline-grid > *{min-width:0}
  .grp-item-row{display:grid;grid-template-columns:70px minmax(0,1fr) 130px;gap:5px;margin-bottom:5px;align-items:start}
  .grp-item-row > *{min-width:0}
  .grp-item-desc{grid-column:1/-1;display:flex;gap:5px;align-items:center}
  .grp-item-desc textarea{flex:1;min-width:0}
  @media (max-width: 760px){
    .cfg-grid.two,.cfg-grid.three,.cfg-grid.split,.svc-inline-grid,.pf-row .pf-body{grid-template-columns:1fr}
    .svc-row,.pf-row{flex-direction:column}
    .svc-remove{align-self:flex-end}
    .pf-actions{flex-direction:row;width:auto}
    .grp-item-desc{flex-direction:column;align-items:stretch}
    .grp-item-desc .grp-item-remove{align-self:flex-end}
    .cfg-section{padding:12px}
  }
</style>

<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
    <h1 style="margin:0">Configure {{ $entityLabel }}</h1>
    <a href="{{ route($dashboardRoute) }}" class="button">← Back to Dashboard</a>
  </div>

  <form action="{{ route($configureSaveRoute) }}" method="POST" id="cfg-form" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    {{-- ─── Basic Info ──────────────────────────────────── --}}
    <div class="cfg-section">
      <h3>Basic Information</h3>
      <div class="form-row">
        <label for="name">{{ $entityLabel }} Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $shop->name ?? '') }}" placeholder="e.g. SparkFix Electricals">
      </div>
      <div class="form-row">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3" placeholder="Briefly describe what you offer…">{{ old('description', $shop->description ?? '') }}</textarea>
      </div>
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone" value="{{ old('phone', $shop->phone ?? '') }}" placeholder="+91 …">
        </div>
        <div class="form-row" style="margin:0">
          <label for="address">Address / Area</label>
          <input type="text" id="address" name="address" value="{{ old('address', $shop->address ?? '') }}" placeholder="Area, City">
        </div>
      </div>
      <div class="form-row">
        <label for="public_slug">Public Page URL</label>
        <input type="text" id="public_slug" name="public_slug" value="{{ old('public_slug', $pageConfig['public_slug'] ?? $shop->public_page_slug) }}" placeholder="e.g. dhananjay-plumbing">
        <small style="display:block;margin-top:6px;color:#7d92ae">Choose your own unique page link. Use letters, numbers, and hyphens only.</small>
        <small style="display:block;margin-top:4px;color:#4a90d9">Preview: {{ url('/') }}/<span id="public-slug-preview">{{ old('public_slug', $pageConfig['public_slug'] ?? $shop->public_page_slug) }}</span></small>
        @error('public_slug')
          <div style="margin-top:6px;color:#d64545;font-size:12px">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- ─── Service Cities ──────────────────────────────── --}}
    <div class="cfg-section">
      <h3>{{ $entityLabel }} Cities <span>Select all cities where you provide this {{ strtolower($entityLabel) }}</span></h3>
      @php $savedCities = old('service_cities', $serviceCities ?? []); @endphp
      @if($cities->count())
        <div class="city-grid">
          @foreach($cities as $city)
            <div class="city-item">
              <label>
                <input type="checkbox" name="service_cities[]" value="{{ $city->id }}"
                       {{ in_array($city->id, (array)$savedCities) ? 'checked' : '' }}>
                {{ city_display_name($city->name) }}
              </label>
            </div>
          @endforeach
        </div>
      @else
        <p style="margin:0;color:#86a0be">No cities configured yet.</p>
      @endif
    </div>

    {{-- ─── Template ────────────────────────────────────── --}}
    <div class="cfg-section">
      <h3>Website Template <span>How your public {{ strtolower($entityLabel) }} page looks</span></h3>
      @php $activeTemplate = old('template', $shop->template ?? 'dynamic_service'); @endphp
      @if(!$astroUnlocked)
        @php $activeTemplate = $activeTemplate === 'astro_dynamic' ? 'dynamic_service' : $activeTemplate; @endphp
      @endif
      <input type="hidden" id="cfg-template" name="template" value="{{ $activeTemplate }}">
      <div class="template-cards">
        <button type="button" class="tpl-card {{ $activeTemplate === 'dynamic_service' ? 'active' : '' }}" data-template="dynamic_service"
                data-desc="Dynamic premium landing page with switcher, hero, services grid, testimonials, CTA and footer.">
          <span class="tpl-chip free">Included · Free</span>
          <div class="tpl-thumb dynamic">
            <div class="mini-top"></div>
            <div class="mini-hero">
              <div class="mini-pill"></div>
              <div class="mini-title"></div>
              <div class="mini-sub"></div>
              <div class="mini-grid">
                <div class="mini-box"></div>
                <div class="mini-box"></div>
                <div class="mini-box"></div>
              </div>
            </div>
          </div>
          <h4>Dynamic Template</h4>
          <p>Premium dynamic template (included free in your subscription plan).</p>
          <a href="{{ route('template.demo', 'dynamic_service') }}" target="_blank" class="tpl-demo-link" onclick="event.stopPropagation()">👁 Preview</a>
        </button>
        <button type="button" class="tpl-card {{ $activeTemplate === 'astro_dynamic' ? 'active' : '' }} {{ !$astroUnlocked ? 'locked' : '' }}" data-template="astro_dynamic" data-locked="{{ $astroUnlocked ? '0' : '1' }}"
          data-desc="Premium cosmic dark template with dramatic hero, luxury cards, and a richer high-visual landing page.">
          @if($astroUnlocked)
            <span class="tpl-chip unlocked">✓ Unlocked</span>
          @else
            <span class="tpl-chip paid">Premium · ₹{{ number_format($astroUnlockPrice, 0) }} Unlock</span>
          @endif
          <div class="tpl-thumb astro">
            <div class="mini-top"><span class="mini-dot"></span><span class="mini-dot"></span><span class="mini-dot"></span></div>
            <div class="mini-hero">
              <div class="mini-title"></div>
              <div class="mini-sub"></div>
              <div class="mini-grid">
                <div class="mini-box"></div>
                <div class="mini-box"></div>
                <div class="mini-box"></div>
                <div class="mini-box"></div>
              </div>
            </div>
          </div>
          <h4>Astro Dynamic Template</h4>
          @if($astroUnlocked)
            <p style="color:#166534;font-weight:500">Unlocked — cosmic premium dark template ready to use.</p>
          @else
            <p>Cosmic premium landing page in a single dark luxury style. Paid unlock required.</p>
          @endif
          <a href="{{ route('template.demo', 'astro_dynamic') }}" target="_blank" class="tpl-demo-link" onclick="event.stopPropagation()">👁 Preview</a>
        </button>
        <button type="button" class="tpl-card {{ $activeTemplate === 'metro_clean' ? 'active' : '' }}" data-template="metro_clean"
          data-desc="Clean professional blue template with bold header, services grid, about section, and contact panel.">
          <span class="tpl-chip free">Included · Free</span>
          <div class="tpl-thumb metro">
            <div class="mini-top"><span class="mini-dot"></span><span class="mini-dot"></span></div>
            <div class="mini-hero">
              <div class="mini-title"></div>
              <div class="mini-sub"></div>
              <div class="mini-grid">
                <div class="mini-box"></div>
                <div class="mini-box"></div>
                <div class="mini-box"></div>
              </div>
            </div>
          </div>
          <h4>Metro Clean Template</h4>
          <p>Clean modern blue layout — ideal for consultants, clinics, and professional services.</p>
          <a href="{{ route('template.demo', 'metro_clean') }}" target="_blank" class="tpl-demo-link" onclick="event.stopPropagation()">👁 Preview</a>
        </button>
        <button type="button" class="tpl-card {{ $activeTemplate === 'saffron_local' ? 'active' : '' }}" data-template="saffron_local"
          data-desc="Warm saffron-orange local business template with offer banner, opening hours, and WhatsApp CTA.">
          <span class="tpl-chip free">Included · Free</span>
          <div class="tpl-thumb saffron-local">
            <div class="mini-top"><span class="mini-dot"></span><span class="mini-dot"></span></div>
            <div class="mini-hero">
              <div class="mini-title"></div>
              <div class="mini-sub"></div>
              <div class="mini-grid">
                <div class="mini-box"></div>
                <div class="mini-box"></div>
                <div class="mini-box"></div>
              </div>
            </div>
          </div>
          <h4>Saffron Local Template</h4>
          <p>Warm orange style for local shops — with offer banners, opening hours, and WhatsApp contact.</p>
          <a href="{{ route('template.demo', 'saffron_local') }}" target="_blank" class="tpl-demo-link" onclick="event.stopPropagation()">👁 Preview</a>
        </button>
      </div>
      @error('template')
        <div style="margin-top:8px;color:#d64545;font-size:12px">{{ $message }}</div>
      @enderror
      <div id="template-lock-message" style="margin-top:8px;color:#d64545;font-size:12px;display:none">You have to unlock this template first. Click Unlock option and submit payment proof.</div>
      @if(!$astroUnlocked)
        <div style="margin-top:10px">
          <a href="{{ route($unlockRoute) }}" class="button" style="background:#f5c518;border-color:#e0b10f;color:#2d1c00;text-decoration:none;display:inline-block">Unlock Astro Dynamic →</a>
        </div>
      @endif
      @if(!$astroUnlocked)
      <div class="unlock-modal" id="astro-unlock-modal" style="display:none" aria-hidden="true">
        <div class="unlock-modal-backdrop" data-close-astro-unlock></div>
        <div class="unlock-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="astro-unlock-title">
          <button type="button" class="unlock-modal-close" aria-label="Close" data-close-astro-unlock>&times;</button>
          <div class="unlock-box" id="astro-unlock-box" style="margin-top:0">
            <h4 id="astro-unlock-title">Unlock Astro Dynamic (₹{{ number_format($astroUnlockPrice, 0) }})</h4>
            <p>
              UPI/QR Payment: <strong>Scan your barcode/QR and complete ₹{{ number_format($astroUnlockPrice, 0) }} payment</strong>. Submit transaction ID or payment screenshot (one is mandatory).
              Approval is completed within {{ $unlockSlaHours }} hours by super admin.
            </p>

            @if(!$hasActiveSubscription)
              <div style="margin-bottom:10px;padding:10px;border:1px solid #f0d79a;background:#fff7e4;border-radius:8px;font-size:12px;color:#7b5b1d">
                Activate <strong>Yearly Base Plan (₹{{ number_format($yearlyBasePrice, 0) }})</strong> first to submit unlock request for Astro Dynamic.
                <a href="{{ route('subscriptions.new') }}" class="button" style="margin-left:8px">Go to Subscription</a>
              </div>
            @endif

            @if($latestAstroUnlockRequest)
              <div style="margin-bottom:10px;font-size:12px;color:#6d5a2a">
                Latest request status: <strong>{{ ucfirst($latestAstroUnlockRequest->status) }}</strong>
                @if($latestAstroUnlockRequest->created_at)
                  · submitted {{ $latestAstroUnlockRequest->created_at->diffForHumans() }}
                @endif
              </div>
            @endif

            @if($hasPendingAstroUnlockRequest)
              <div style="margin-bottom:10px;padding:10px;border:1px solid #f0d79a;background:#fff7e4;border-radius:8px;font-size:12px;color:#7b5b1d">
                Your request is submitted and currently under review by admin. You cannot submit another request until current review is completed.
              </div>
            @endif

            <div class="unlock-form">
              <input type="hidden" name="template_key" value="astro_dynamic" form="astro-unlock-request-form">
              <input type="hidden" name="amount" value="{{ number_format($astroUnlockPrice, 2, '.', '') }}" form="astro-unlock-request-form">

              <div class="form-row">
                <label>Payment QR / Barcode (Pay ₹{{ number_format($astroUnlockPrice, 0) }})</label>
                @php $hasPaymentCode = (string) $paymentQrUrl !== '' || (string) $paymentBarcodeUrl !== ''; @endphp
                @if($hasPaymentCode)
                  <div class="unlock-pay-row">
                    <img src="{{ $paymentQrUrl ?: $paymentBarcodeUrl }}" alt="Payment QR" class="unlock-qr-img" onerror="this.style.display='none'; this.nextElementSibling && (this.nextElementSibling.style.display='block');">
                    <div class="unlock-pay-unavailable" style="display:none">Payment QR/Barcode unavailable right now. Please contact admin.</div>
                    <div class="unlock-pay-note">
                      Scan this code and pay <strong>₹{{ number_format($astroUnlockPrice, 0) }}</strong>, then submit transaction ID or screenshot below.
                    </div>
                  </div>
                @else
                  <div class="unlock-pay-unavailable">Payment QR/Barcode unavailable right now. Please contact admin.</div>
                @endif
              </div>

              <div class="unlock-grid two-col">
                <div class="form-row">
                  <label for="unlock_transaction_id">Transaction ID (optional)</label>
                  <input id="unlock_transaction_id" type="text" name="payment_transaction_id" value="{{ old('payment_transaction_id') }}" placeholder="UPI/Bank transaction reference" form="astro-unlock-request-form">
                </div>
                <div class="form-row">
                  <label for="unlock_screenshot">Payment Screenshot (optional)</label>
                  <input id="unlock_screenshot" type="file" name="payment_screenshot" accept="image/*" form="astro-unlock-request-form">
                </div>
              </div>

              <div class="form-row">
                <label for="unlock_comment">Additional Comment (optional)</label>
                <textarea id="unlock_comment" name="comment" rows="2" placeholder="Anything you want admin to know..." form="astro-unlock-request-form">{{ old('comment') }}</textarea>
              </div>

              <div style="font-size:12px;color:#7b6a3d">Either transaction ID or screenshot is required.</div>
              <div>
                <button type="submit" class="button" style="background:#f5c518;border-color:#e0b10f;color:#2d1c00" form="astro-unlock-request-form" {{ !$hasActiveSubscription || $hasPendingAstroUnlockRequest ? 'disabled' : '' }}>Submit Unlock Request</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- ─── Dynamic Template Content ────────────────────── --}}
    <div class="cfg-section">
      <h3>Template Content <span>These fields control your public website text</span></h3>
      @php $tc = old('tc', $templateContent ?? []); @endphp
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="tc_hero_badge">Hero Badge</label>
          <input id="tc_hero_badge" type="text" name="tc[hero_badge]" value="{{ $tc['hero_badge'] ?? '' }}" placeholder="Trusted Since 2010 · 5000+ Customers">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_hero_title">Hero Title</label>
          <input id="tc_hero_title" type="text" name="tc[hero_title]" value="{{ $tc['hero_title'] ?? '' }}" placeholder="Professional Service For Your Needs">
        </div>
      </div>
      <div class="form-row">
        <label for="tc_hero_description">Hero Description</label>
        <textarea id="tc_hero_description" name="tc[hero_description]" rows="3" placeholder="Short intro for your service page">{{ $tc['hero_description'] ?? '' }}</textarea>
      </div>
      <div class="cfg-grid three">
        <div class="form-row" style="margin:0">
          <label for="tc_primary_cta">Primary CTA Button</label>
          <input id="tc_primary_cta" type="text" name="tc[primary_cta]" value="{{ $tc['primary_cta'] ?? '' }}" placeholder="Book Now">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_secondary_cta">Secondary CTA Button</label>
          <input id="tc_secondary_cta" type="text" name="tc[secondary_cta]" value="{{ $tc['secondary_cta'] ?? '' }}" placeholder="Get Free Quote">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_services_label">Services Label</label>
          <input id="tc_services_label" type="text" name="tc[services_label]" value="{{ $tc['services_label'] ?? '' }}" placeholder="Our Services">
        </div>
      </div>
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="tc_services_title">Services Section Title</label>
          <input id="tc_services_title" type="text" name="tc[services_title]" value="{{ $tc['services_title'] ?? '' }}" placeholder="Services We Offer">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_services_subtitle">Services Section Subtitle</label>
          <input id="tc_services_subtitle" type="text" name="tc[services_subtitle]" value="{{ $tc['services_subtitle'] ?? '' }}" placeholder="Choose from our most popular services">
        </div>
      </div>
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="tc_why_title">Why-Us Title</label>
          <input id="tc_why_title" type="text" name="tc[why_title]" value="{{ $tc['why_title'] ?? '' }}" placeholder="Why Choose Us">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_why_subtitle">Why-Us Subtitle</label>
          <input id="tc_why_subtitle" type="text" name="tc[why_subtitle]" value="{{ $tc['why_subtitle'] ?? '' }}" placeholder="Quality work and transparent pricing">
        </div>
      </div>
      <div class="cfg-grid three">
        <div class="form-row" style="margin:0">
          <label for="tc_cta_title">Bottom CTA Title</label>
          <input id="tc_cta_title" type="text" name="tc[cta_title]" value="{{ $tc['cta_title'] ?? '' }}" placeholder="Need Help Today?">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_cta_description">Bottom CTA Description</label>
          <input id="tc_cta_description" type="text" name="tc[cta_description]" value="{{ $tc['cta_description'] ?? '' }}" placeholder="Contact us now">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_cta_button">Bottom CTA Button</label>
          <input id="tc_cta_button" type="text" name="tc[cta_button]" value="{{ $tc['cta_button'] ?? '' }}" placeholder="Contact Now">
        </div>
      </div>
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="tc_footer_brand">Footer Brand</label>
          <input id="tc_footer_brand" type="text" name="tc[footer_brand]" value="{{ $tc['footer_brand'] ?? '' }}" placeholder="Your brand name">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_footer_tagline">Footer Tagline</label>
          <input id="tc_footer_tagline" type="text" name="tc[footer_tagline]" value="{{ $tc['footer_tagline'] ?? '' }}" placeholder="Trusted · Fast · Professional">
        </div>
      </div>

      {{-- Metro Clean specific fields --}}
      <div class="tpl-specific-fields {{ $activeTemplate === 'metro_clean' ? 'active' : '' }}" id="tpl-fields-metro_clean">
        <h3 style="margin:0 0 10px;font-size:14px;color:#1e3a5f;border-bottom:1px solid #dbe7f8;padding-bottom:6px">Metro Clean — Extra Fields</h3>
        <div class="cfg-grid three">
          <div class="form-row" style="margin:0">
            <label for="tc_metro_accent">Accent Color</label>
            <select id="tc_metro_accent" name="tc[metro_accent]">
              <option value="blue" {{ ($tc['metro_accent'] ?? 'blue') === 'blue' ? 'selected' : '' }}>Blue (default)</option>
              <option value="green" {{ ($tc['metro_accent'] ?? '') === 'green' ? 'selected' : '' }}>Green</option>
              <option value="red" {{ ($tc['metro_accent'] ?? '') === 'red' ? 'selected' : '' }}>Red</option>
            </select>
          </div>
          <div class="form-row" style="margin:0">
            <label for="tc_about_title">About Section Title</label>
            <input id="tc_about_title" type="text" name="tc[about_title]" value="{{ $tc['about_title'] ?? '' }}" placeholder="About Our Business">
          </div>
          <div class="form-row" style="margin:0">
            <label for="tc_contact_heading">Contact Section Heading</label>
            <input id="tc_contact_heading" type="text" name="tc[contact_heading]" value="{{ $tc['contact_heading'] ?? '' }}" placeholder="Get In Touch">
          </div>
        </div>
        <div class="form-row">
          <label for="tc_about_text">About Section Text</label>
          <textarea id="tc_about_text" name="tc[about_text]" rows="3" placeholder="Tell visitors about your business history and mission…">{{ $tc['about_text'] ?? '' }}</textarea>
        </div>
      </div>

      {{-- Saffron Local specific fields --}}
      <div class="tpl-specific-fields {{ $activeTemplate === 'saffron_local' ? 'active' : '' }}" id="tpl-fields-saffron_local">
        <h3 style="margin:0 0 10px;font-size:14px;color:#92400e;border-bottom:1px solid #f0d9c8;padding-bottom:6px">Saffron Local — Extra Fields</h3>
        <div class="cfg-grid two">
          <div class="form-row" style="margin:0">
            <label for="tc_opening_hours">Opening Hours</label>
            <input id="tc_opening_hours" type="text" name="tc[opening_hours]" value="{{ $tc['opening_hours'] ?? '' }}" placeholder="Mon–Sat: 9 AM – 8 PM  |  Sun: 10 AM – 5 PM">
          </div>
          <div class="form-row" style="margin:0">
            <label for="tc_locality_note">Locality / Area Note</label>
            <input id="tc_locality_note" type="text" name="tc[locality_note]" value="{{ $tc['locality_note'] ?? '' }}" placeholder="Serving Washim, Mangrulpir and nearby areas">
          </div>
        </div>
        <div class="form-row">
          <label for="tc_special_offer">Special Offer Banner <small style="color:#86a0be;font-weight:400">(shown at the top — leave blank to hide)</small></label>
          <input id="tc_special_offer" type="text" name="tc[special_offer]" value="{{ $tc['special_offer'] ?? '' }}" placeholder="🎉 Grand Opening Offer: 20% off on all services this month!">
        </div>
      </div>
    </div>

    <div class="cfg-section">
      <h3>{{ $providerProfileLabel }} <span>This is managed from Subscription page now</span></h3>
      <p style="margin:0;color:#6d84a5;font-size:13px;line-height:1.7">
        To update {{ strtolower($providerProfileLabel) }} details (name, contact, bio, photo), go to
        <a href="{{ route('subscriptions.new') }}">Subscription</a>.
      </p>
    </div>

    {{-- ─── Services Offered ────────────────────────────── --}}
    <div class="cfg-section">
      <h3>{{ $entityPluralLabel }} Offered <span>Each item will appear as a business card on your public page</span></h3>
      <datalist id="service-icon-options">
        <option value="📚" label="Education"></option>
        <option value="💼" label="Career"></option>
        <option value="❤️" label="Love & Marriage"></option>
        <option value="🏠" label="Home / Vastu"></option>
        <option value="💎" label="Gemstone"></option>
        <option value="🪔" label="Puja / Ritual"></option>
        <option value="🧿" label="Protection / Nazar"></option>
        <option value="💰" label="Finance"></option>
        <option value="🧘" label="Health / Wellness"></option>
        <option value="👶" label="Child / Family"></option>
        <option value="🚰" label="Plumbing"></option>
        <option value="⚡" label="Electrical"></option>
        <option value="🎨" label="Painting"></option>
        <option value="🧹" label="Cleaning"></option>
        <option value="🛠️" label="General Service"></option>
        <option value="✨" label="Premium / Featured"></option>
      </datalist>
      <div id="services-container">
        @php $svcs = old('services', $services ?? []); @endphp
        @foreach($svcs as $i => $svc)
          <div class="svc-row">
            <div class="svc-body">
              <div class="svc-inline-grid">
                <input type="text" name="services[{{ $i }}][icon]" value="{{ $svc['icon'] ?? '🛠️' }}" placeholder="Icon" list="service-icon-options">
                <input type="text" name="services[{{ $i }}][name]" value="{{ $svc['name'] ?? '' }}" placeholder="Service name (e.g. Plumbing Repair)">
                <input type="text" name="services[{{ $i }}][price]" value="{{ $svc['price'] ?? '' }}" placeholder="Price (e.g. From ₹499)">
              </div>
              <textarea name="services[{{ $i }}][description]" placeholder="Short description…">{{ $svc['description'] ?? '' }}</textarea>
              <input type="url" name="services[{{ $i }}][url]" value="{{ $svc['url'] ?? '' }}" placeholder="Web page URL (optional)">
            </div>
            <button type="button" class="svc-remove">✕</button>
          </div>
        @endforeach
      </div>
      <button type="button" id="add-service" class="button" style="margin-top:6px">+ Add Service</button>
    </div>

      {{-- ─── Astro: Service Sections ─────────────────────── --}}
      @if($astroUnlocked)
      <div class="cfg-section" id="astro-groups-section" style="{{ $activeTemplate !== 'astro_dynamic' ? 'display:none' : '' }}">
        <h3>Service Sections <span>Astro Template only — group your services under separate headings</span></h3>
        <p style="font-size:12px;color:#667;margin-bottom:12px">Each section has its own eyebrow label, heading and subtitle with service cards underneath. Leave untouched to use auto-split defaults.</p>
        <div id="svc-groups-container">
          @php $savedGroups = old('service_groups', $tc['service_groups'] ?? []); @endphp
          @foreach($savedGroups as $gi => $grp)
            <div class="svc-group-row" style="border:1px solid #dbe7f8;border-radius:10px;padding:12px;margin-bottom:10px">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <strong style="font-size:13px;color:#2d4a7a">Section {{ $gi + 1 }}</strong>
                <button type="button" class="grp-remove" style="background:#fee;border:1px solid #fbb;color:#c33;border-radius:6px;padding:2px 8px;font-size:12px;cursor:pointer">✕ Remove</button>
              </div>
              <div class="cfg-grid split" style="margin-bottom:6px">
                <input type="text" name="service_groups[{{ $gi }}][eyebrow]" value="{{ $grp['eyebrow'] ?? '' }}" placeholder="Eyebrow label (e.g. 01 · Astrology)">
                <input type="text" name="service_groups[{{ $gi }}][title]"   value="{{ $grp['title'] ?? '' }}"   placeholder="Section heading *">
              </div>
              <input type="text" name="service_groups[{{ $gi }}][subtitle]" value="{{ $grp['subtitle'] ?? '' }}" placeholder="Subtitle / description" style="width:100%;margin-bottom:8px">
              <div class="grp-items-container" style="padding-left:10px;border-left:3px solid #e8eef8">
                @foreach($grp['items'] ?? [] as $ii => $item)
                  <div class="grp-item-row">
                    <input type="text"  name="service_groups[{{ $gi }}][items][{{ $ii }}][icon]"        value="{{ $item['icon'] ?? '✨' }}"     placeholder="Icon" list="service-icon-options">
                    <input type="text"  name="service_groups[{{ $gi }}][items][{{ $ii }}][name]"        value="{{ $item['name'] ?? '' }}"       placeholder="Service name *">
                    <input type="text"  name="service_groups[{{ $gi }}][items][{{ $ii }}][price]"       value="{{ $item['price'] ?? '' }}"      placeholder="Price">
                    <div class="grp-item-desc">
                      <textarea name="service_groups[{{ $gi }}][items][{{ $ii }}][description]" placeholder="Short description…" rows="1" style="flex:1">{{ $item['description'] ?? '' }}</textarea>
                      <button type="button" class="grp-item-remove" style="background:#fee;border:1px solid #fbb;color:#c33;border-radius:6px;padding:2px 6px;font-size:11px;cursor:pointer;white-space:nowrap">✕</button>
                    </div>
                  </div>
                @endforeach
              </div>
              <button type="button" class="add-grp-item" style="margin-top:6px;font-size:12px;padding:4px 10px" class="button">+ Add Service to Section</button>
            </div>
          @endforeach
        </div>
        <button type="button" id="add-group" class="button" style="margin-top:6px">+ Add Section</button>
      </div>
      @endif

    {{-- ─── Custom Page Fields ─────────────────────────── --}}
    <div class="cfg-section">
      <h3>Custom Page Fields <span>Additional info shown on your public page</span></h3>
      <div id="pf-container">
        @php
          $pfFields = [];
          if (!empty($shop->page_config['fields'])) $pfFields = $shop->page_config['fields'];
        @endphp
        @foreach($pfFields as $fi => $pf)
          <div class="pf-row">
            <div class="pf-body">
              <input type="text" name="pf[{{ $fi }}][title]" value="{{ $pf['title'] ?? '' }}" placeholder="Title">
              <input type="text" name="pf[{{ $fi }}][value]" value="{{ $pf['value'] ?? '' }}" placeholder="Value">
              <div style="display:flex;gap:6px;align-items:center">
                <label style="font-size:12px;display:flex;gap:3px;align-items:center;white-space:nowrap">
                  <input type="checkbox" name="pf[{{ $fi }}][bold]" value="1" {{ !empty($pf['bold']) ? 'checked' : '' }}> Bold
                </label>
                <select name="pf[{{ $fi }}][align]" style="font-size:12px">
                  <option value="left"   {{ ($pf['align']??'') === 'left'   ? 'selected' : '' }}>Left</option>
                  <option value="center" {{ ($pf['align']??'') === 'center' ? 'selected' : '' }}>Center</option>
                  <option value="right"  {{ ($pf['align']??'') === 'right'  ? 'selected' : '' }}>Right</option>
                </select>
              </div>
            </div>
            <div class="pf-actions">
              <button type="button" class="pf-up button">↑</button>
              <button type="button" class="pf-down button">↓</button>
              <button type="button" class="pf-del button" style="color:#e55">✕</button>
            </div>
          </div>
        @endforeach
      </div>
      <button type="button" id="add-pf" class="button" style="margin-top:6px">+ Add Field</button>
    </div>

    {{-- ─── Submit ───────────────────────────────────────── --}}
    @if(!$hasActiveSubscription)
      <div style="margin-top:10px;padding:10px;border:1px solid #f0d79a;background:#fff7e4;border-radius:8px;font-size:12px;color:#7b5b1d">
        Activate your yearly base subscription first to save configuration.
        <a href="{{ route('subscriptions.new') }}" class="button" style="margin-left:8px">Go to Subscription</a>
      </div>
    @endif
    <div style="display:flex;gap:10px;margin-top:14px">
      <button type="submit" class="toggle-btn" {{ !$hasActiveSubscription ? 'disabled' : '' }}>Save Configuration</button>
      @if($shop->id)
        <a href="{{ route('shops.public', ['publicSlug' => $shop->public_page_slug]) }}" target="_blank" class="button">View Public Page ↗</a>
      @endif
    </div>
  </form>

  @if(!$astroUnlocked)
    <form id="astro-unlock-request-form" action="{{ route('subscriptions.template_unlock_request') }}" method="POST" enctype="multipart/form-data" style="display:none">
      @csrf
    </form>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){

  const publicSlugInput = document.getElementById('public_slug');
  const publicSlugPreview = document.getElementById('public-slug-preview');
  function normalizeSlug(value){
    return String(value || '')
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  }
  if (publicSlugInput && publicSlugPreview) {
    const syncPublicSlug = () => {
      const slug = normalizeSlug(publicSlugInput.value);
      publicSlugInput.value = slug;
      publicSlugPreview.textContent = slug || '{{ $shop->public_page_slug }}';
    };
    publicSlugInput.addEventListener('input', syncPublicSlug);
    syncPublicSlug();
  }

  /* ── Template picker ─────────────────────────────────── */
  const tplInput   = document.getElementById('cfg-template');
  const unlockModal = document.getElementById('astro-unlock-modal');
  const templateLockMessage = document.getElementById('template-lock-message');
  function openUnlockModal(){
    if (!unlockModal) return;
    unlockModal.style.display = 'flex';
    unlockModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    unlockModal.scrollTop = 0;
    const dialog = unlockModal.querySelector('.unlock-modal-dialog');
    if (dialog) dialog.scrollTop = 0;
  }
  function closeUnlockModal(){
    if (!unlockModal) return;
    unlockModal.style.display = 'none';
    unlockModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }
  document.querySelectorAll('[data-open-astro-unlock]').forEach(function(btn){
    btn.addEventListener('click', openUnlockModal);
  });
  document.querySelectorAll('[data-close-astro-unlock]').forEach(function(btn){
    btn.addEventListener('click', closeUnlockModal);
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeUnlockModal();
  });
  function syncTemplatePreview(card){
    if (!card) return;
    const tpl = card.dataset.template;
    tplInput.value = tpl;
    // Show/hide template-specific field panels
    document.querySelectorAll('.tpl-specific-fields').forEach(function(panel){
      panel.classList.toggle('active', panel.id === 'tpl-fields-' + tpl);
    });
  }
  const unlockPageUrl = '{{ route($unlockRoute) }}';
  document.querySelectorAll('.tpl-card').forEach(function(card){
    card.addEventListener('click', function(){
      const isLocked = card.dataset.locked === '1';
      document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('active'));
      card.classList.add('active');
      syncTemplatePreview(card);
      if (isLocked) {
        window.location.href = unlockPageUrl;
      }
    });
  });
  syncTemplatePreview(document.querySelector('.tpl-card.active') || document.querySelector('.tpl-card[data-template="dynamic_service"]'));

  const cfgForm = document.getElementById('cfg-form');
  const astroCard = document.querySelector('.tpl-card[data-template="astro_dynamic"]');
  if (cfgForm && tplInput && astroCard) {
    cfgForm.addEventListener('submit', function(e){
      if (tplInput.value === 'astro_dynamic' && astroCard.dataset.locked === '1') {
        e.preventDefault();
        window.location.href = unlockPageUrl;
      }
    });
  }

  /* ── Services offered ─────────────────────────────────── */
  let svcIdx = {{ count($services ?? []) }};
  const svcContainer = document.getElementById('services-container');

  function addSvcRow(n, icon, name, price, desc, url){
    const row = document.createElement('div');
    row.className = 'svc-row';
    row.innerHTML = `
      <div class="svc-body">
        <div class="svc-inline-grid">
          <input type="text" name="services[${n}][icon]" value="${esc(icon)}" placeholder="Icon" list="service-icon-options">
          <input type="text" name="services[${n}][name]" value="${esc(name)}" placeholder="Service name (e.g. Plumbing Repair)">
          <input type="text" name="services[${n}][price]" value="${esc(price)}" placeholder="Price (e.g. From ₹499)">
        </div>
        <textarea name="services[${n}][description]" placeholder="Short description…">${esc(desc)}</textarea>
        <input type="url" name="services[${n}][url]" value="${esc(url)}" placeholder="Web page URL (optional)">
      </div>
      <button type="button" class="svc-remove">✕</button>`;
    row.querySelector('.svc-remove').addEventListener('click', () => row.remove());
    svcContainer.appendChild(row);
  }

  // Attach remove to existing rows
  document.querySelectorAll('.svc-row .svc-remove').forEach(function(btn){
    btn.addEventListener('click', () => btn.closest('.svc-row').remove());
  });

  document.getElementById('add-service').addEventListener('click', function(){
    addSvcRow(svcIdx++, '🛠️', '', '', '', '');
  });

    /* ── Service Groups (astro_dynamic) ──────────────────── */
    @if($astroUnlocked)
    const grpSection     = document.getElementById('astro-groups-section');
    const grpContainer   = document.getElementById('svc-groups-container');
    let   grpIdx         = {{ count($savedGroups ?? old('service_groups', $tc['service_groups'] ?? [])) }};

    function getGrpItemCount(grpEl){
      return grpEl.querySelectorAll('.grp-item-row').length;
    }

    function addGrpItemRow(grpEl, gi, ii, icon, name, price, desc){
      const c   = grpEl.querySelector('.grp-items-container');
      const row = document.createElement('div');
      row.className = 'grp-item-row';
      row.innerHTML = `
        <input type="text"  name="service_groups[${gi}][items][${ii}][icon]"        value="${esc(icon)}"  placeholder="Icon" list="service-icon-options">
        <input type="text"  name="service_groups[${gi}][items][${ii}][name]"        value="${esc(name)}"  placeholder="Service name *">
        <input type="text"  name="service_groups[${gi}][items][${ii}][price]"       value="${esc(price)}" placeholder="Price">
        <div class="grp-item-desc">
          <textarea name="service_groups[${gi}][items][${ii}][description]" rows="1" placeholder="Short description…" style="flex:1">${esc(desc)}</textarea>
          <button type="button" class="grp-item-remove" style="background:#fee;border:1px solid #fbb;color:#c33;border-radius:6px;padding:2px 6px;font-size:11px;cursor:pointer;white-space:nowrap">✕</button>
        </div>`;
      row.querySelector('.grp-item-remove').addEventListener('click', () => row.remove());
      c.appendChild(row);
    }

    function addGrpRow(gi, data){
      data = data || {};
      const grp = document.createElement('div');
      grp.className = 'svc-group-row';
      grp.style.cssText = 'border:1px solid #dbe7f8;border-radius:10px;padding:12px;margin-bottom:10px';
      grp.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
          <strong style="font-size:13px;color:#2d4a7a">Section ${gi + 1}</strong>
          <button type="button" class="grp-remove" style="background:#fee;border:1px solid #fbb;color:#c33;border-radius:6px;padding:2px 8px;font-size:12px;cursor:pointer">✕ Remove</button>
        </div>
        <div class="cfg-grid split" style="margin-bottom:6px">
          <input type="text" name="service_groups[${gi}][eyebrow]"  value="${esc(data.eyebrow||'')}"  placeholder="Eyebrow label (e.g. 01 · Astrology)">
          <input type="text" name="service_groups[${gi}][title]"    value="${esc(data.title||'')}"    placeholder="Section heading *">
        </div>
        <input type="text" name="service_groups[${gi}][subtitle]"   value="${esc(data.subtitle||'')}" placeholder="Subtitle / description" style="width:100%;margin-bottom:8px">
        <div class="grp-items-container" style="padding-left:10px;border-left:3px solid #e8eef8"></div>
        <button type="button" class="add-grp-item button" style="margin-top:6px;font-size:12px;padding:4px 10px">+ Add Service to Section</button>`;
      grp.querySelector('.grp-remove').addEventListener('click', () => { grp.remove(); reindexGroups(); });
      grp.querySelector('.add-grp-item').addEventListener('click', function(){
        addGrpItemRow(grp, gi, getGrpItemCount(grp), '✨', '', '', '');
      });
      grpContainer.appendChild(grp);
      // Populate existing items
      if (Array.isArray(data.items)) {
        data.items.forEach(function(item, ii){
          addGrpItemRow(grp, gi, ii, item.icon||'✨', item.name||'', item.price||'', item.description||'');
        });
      }
    }

    function reindexGroups(){
      grpContainer.querySelectorAll('.svc-group-row').forEach(function(grp, gi){
        grp.querySelector('strong').textContent = 'Section ' + (gi + 1);
        grp.querySelectorAll('[name]').forEach(function(el){
          el.name = el.name.replace(/service_groups\[\d+\]/, 'service_groups[' + gi + ']');
        });
      });
      grpIdx = grpContainer.querySelectorAll('.svc-group-row').length;
    }

    // Attach events to existing rows rendered by Blade
    document.querySelectorAll('.svc-group-row').forEach(function(grp, gi){
      grp.querySelector('.grp-remove') && grp.querySelector('.grp-remove').addEventListener('click', () => { grp.remove(); reindexGroups(); });
      const addItemBtn = grp.querySelector('.add-grp-item');
      addItemBtn && addItemBtn.addEventListener('click', function(){
        addGrpItemRow(grp, gi, getGrpItemCount(grp), '✨', '', '', '');
      });
      grp.querySelectorAll('.grp-item-remove').forEach(function(btn){
        btn.addEventListener('click', () => btn.closest('.grp-item-row').remove());
      });
    });

    document.getElementById('add-group').addEventListener('click', function(){
      addGrpRow(grpIdx++);
    });

    // Show/hide groups section based on template selection
    function syncGrpSection(){
      const tpl = document.getElementById('cfg-template').value;
      if (grpSection) grpSection.style.display = (tpl === 'astro_dynamic') ? '' : 'none';
    }
    document.querySelectorAll('.tpl-card').forEach(function(btn){
      btn.addEventListener('click', function(){ setTimeout(syncGrpSection, 50); });
    });
    syncGrpSection();
    @endif

  /* ── Custom page fields ───────────────────────────────── */
  let pfIdx = {{ count($pfFields ?? []) }};
  const pfContainer = document.getElementById('pf-container');

  function addPfRow(n, title, val, bold, align){
    const row = document.createElement('div');
    row.className = 'pf-row';
    row.innerHTML = `
      <div class="pf-body">
        <input type="text" name="pf[${n}][title]" value="${esc(title)}" placeholder="Title">
        <input type="text" name="pf[${n}][value]" value="${esc(val)}"   placeholder="Value">
        <div style="display:flex;gap:6px;align-items:center">
          <label style="font-size:12px;display:flex;gap:3px;align-items:center;white-space:nowrap">
            <input type="checkbox" name="pf[${n}][bold]" value="1" ${bold ? 'checked' : ''}> Bold
          </label>
          <select name="pf[${n}][align]" style="font-size:12px">
            <option value="left"   ${align==='left'   ? 'selected' : ''}>Left</option>
            <option value="center" ${align==='center' ? 'selected' : ''}>Center</option>
            <option value="right"  ${align==='right'  ? 'selected' : ''}>Right</option>
          </select>
        </div>
      </div>
      <div class="pf-actions">
        <button type="button" class="pf-up  button">↑</button>
        <button type="button" class="pf-down button">↓</button>
        <button type="button" class="pf-del  button" style="color:#e55">✕</button>
      </div>`;
    attachPfEvents(row);
    pfContainer.appendChild(row);
  }

  function attachPfEvents(row){
    row.querySelector('.pf-del').addEventListener('click', () => row.remove());
    row.querySelector('.pf-up').addEventListener('click', function(){
      if (row.previousElementSibling) pfContainer.insertBefore(row, row.previousElementSibling);
      reindexPf();
    });
    row.querySelector('.pf-down').addEventListener('click', function(){
      if (row.nextElementSibling) pfContainer.insertBefore(row.nextElementSibling, row);
      reindexPf();
    });
  }

  function reindexPf(){
    pfContainer.querySelectorAll('.pf-row').forEach(function(row, i){
      row.querySelectorAll('[name]').forEach(function(el){
        el.name = el.name.replace(/pf\[\d+\]/, 'pf[' + i + ']');
      });
    });
  }

  // Attach events to existing rows
  document.querySelectorAll('.pf-row').forEach(attachPfEvents);

  document.getElementById('add-pf').addEventListener('click', function(){
    addPfRow(pfIdx++, '', '', false, 'left');
  });

  function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
});
</script>
@endsection
