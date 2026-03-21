@extends('layouts.app')

@section('content')
@php
  $dashboardTitle = $dashboardTitle ?? 'My Service';
  $defaultEntityName = $defaultEntityName ?? 'My Service';
  $configureRoute = $configureRoute ?? 'configure_myservice';
  $configureLabel = $configureLabel ?? 'Configure Service Page';
  $workersRoute = $workersRoute ?? 'workers_myservice';
  $offerNewRoute = $offerNewRoute ?? 'myservice_offer_new';
  $experienceRoute = $experienceRoute ?? 'myservice_experience';
  $idCardRoute = $idCardRoute ?? 'myservice_idcard';
  $requestsRoute = $requestsRoute ?? 'myservice_requests';
  $showSubscriptionButton = $showSubscriptionButton ?? true;
  $servicesHeading = $servicesHeading ?? 'Services Offered';
  $emptyServicesTitle = $emptyServicesTitle ?? 'No services added yet';
  $emptyServicesText = $emptyServicesText ?? 'Add the services you offer in';
@endphp
<style>
  .myservice-hub .hero{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap}
  .myservice-hub .hero .meta{color:#5d779a;font-size:13px}
  .myservice-hub .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-top:12px}
  .myservice-hub .stat-card{background:#fff;border:1px solid #dbe7f8;border-radius:10px;padding:11px}
  .myservice-hub .stat-card .k{font-size:12px;color:#6a82a3}
  .myservice-hub .stat-card .v{font-size:24px;font-weight:800;color:#2f4e74;line-height:1.1;margin-top:4px}
  .myservice-hub .actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
  .myservice-hub .split{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px}
  .myservice-hub .list{list-style:none;margin:0;padding:0}
  .myservice-hub .list li{padding:9px 0;border-bottom:1px solid #e8eef9}
  .myservice-hub .list li:last-child{border-bottom:0}
  .myservice-hub .title{font-weight:700;color:#2c486b}
  .myservice-hub .sub{font-size:12px;color:#6d84a5;margin-top:2px}
  .req-pill{display:inline-flex;align-items:center;gap:5px;padding:2px 9px;border-radius:999px;font-size:11px;font-weight:700;text-transform:capitalize}
  .req-pill.new{background:#e8f2ff;color:#2f6fbe}
  .req-pill.confirmed{background:#fff4db;color:#9a6500}
  .req-pill.pending{background:#fff7e6;color:#a16207}
  .req-pill.completed{background:#e8f7ee;color:#1f8a49}
  .req-pill.cancelled{background:#feecec;color:#c53d3d}
  .req-pill.callback{background:#f3ecff;color:#7e4dc2}
  .req-pill.called{background:#e7faf1;color:#0f7a43}
  /* Services section */
  .svc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-top:10px}
  .svc-biz-card{background:#fff;border:1px solid #dbe7f8;border-radius:12px;padding:16px;display:flex;flex-direction:column;gap:8px;text-decoration:none;color:inherit;transition:box-shadow .2s,transform .15s;position:relative;overflow:hidden}
  .svc-biz-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#2f4e74,#4a90d9)}
  .svc-biz-card:hover{box-shadow:0 6px 20px rgba(47,78,116,.15);transform:translateY(-2px)}
  .svc-biz-card .sbc-icon{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#2f4e74,#4a90d9);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700}
  .svc-biz-card .sbc-name{font-size:15px;font-weight:700;color:#2f4e74}
  .svc-biz-card .sbc-desc{font-size:12px;color:#6d84a5;flex:1}
  .svc-biz-card .sbc-link{font-size:11px;color:#4a90d9;margin-top:4px}
  .city-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:6px}
  .city-tag{background:#eef4ff;color:#2f4e74;font-size:11px;padding:2px 8px;border-radius:12px;border:1px solid #c5d9f5}
  @media (max-width: 860px){.myservice-hub .split{grid-template-columns:1fr}}
</style>

<div class="panel myservice-hub">
  <div class="hero">
    <div>
      <h1 style="margin:0">{{ $dashboardTitle }}</h1>
      <h2 style="margin:6px 0 0">{{ $shop->name ?: $defaultEntityName }}</h2>
      <div class="meta">{{ $shop->address ?: 'Add address in configuration for better local visibility' }}</div>
      @if($serviceCities->count())
        <div class="city-tags">
          @foreach($serviceCities as $cn)<span class="city-tag">📍 {{ $cn }}</span>@endforeach
        </div>
      @endif
    </div>
    <a href="{{ route($configureRoute) }}" class="toggle-btn">{{ $configureLabel }}</a>
  </div>

  <div class="stats">
    <div class="stat-card"><div class="k">Total Workers</div><div class="v">{{ $workersCount }}</div></div>
    <div class="stat-card"><div class="k">Active Offers</div><div class="v">{{ $offersCount }}</div></div>
    <div class="stat-card"><div class="k">Services</div><div class="v">{{ count($services) }}</div></div>
    <div class="stat-card"><div class="k">Template</div><div class="v" style="font-size:18px">{{ $shop->template ?: 'Not set' }}</div></div>
    <div class="stat-card"><div class="k">Client Requests</div><div class="v">{{ $requestsCount ?? 0 }}</div></div>
    <div class="stat-card"><div class="k">Pending Requests</div><div class="v">{{ $requestsPendingCount ?? 0 }}</div></div>
  </div>

  <div class="actions">
    <a href="{{ route($workersRoute) }}" class="button">Workers</a>
    <a href="{{ route($offerNewRoute) }}" class="button">Add Offer</a>
    <a href="{{ route($experienceRoute) }}" class="button">Experience Letter</a>
    <a href="{{ route($idCardRoute) }}" class="button">ID Card</a>
    <a href="{{ route($requestsRoute) }}" class="button">Client Requests</a>
    @if($showSubscriptionButton)
      <a href="{{ route('subscriptions.new') }}" class="button">Subscription</a>
    @endif
    @if($shop->id)
      <a href="{{ route('shops.public', ['publicSlug' => $shop->public_page_slug]) }}" target="_blank" class="button">View Public Page ↗</a>
    @endif
  </div>

  {{-- ─── Services Offered Section ─────────────────────────────── --}}
  @if(count($services))
  <div class="card" style="margin-top:12px">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:6px">
      <h3 style="margin:0">{{ $servicesHeading }}</h3>
      <a href="{{ route($configureRoute) }}" style="font-size:12px;color:#4a90d9">Edit services →</a>
    </div>
    <div class="svc-grid">
      @foreach($services as $svc)
        @php $href = !empty($svc['url']) ? $svc['url'] : ($shop->id ? route('shops.public', ['publicSlug' => $shop->public_page_slug]) : '#'); @endphp
        <a href="{{ $href }}" class="svc-biz-card" {{ !empty($svc['url']) ? 'target="_blank"' : '' }}>
          <div class="sbc-icon">{{ strtoupper(substr($svc['name'] ?? 'S', 0, 1)) }}</div>
          <div class="sbc-name">{{ $svc['name'] }}</div>
          @if(!empty($svc['description']))
            <div class="sbc-desc">{{ $svc['description'] }}</div>
          @endif
          @if(!empty($svc['url']))
            <div class="sbc-link">🔗 {{ parse_url($svc['url'], PHP_URL_HOST) ?: $svc['url'] }}</div>
          @else
            <div class="sbc-link" style="color:#86a0be">View service page ↗</div>
          @endif
        </a>
      @endforeach
    </div>
  </div>
  @else
  <div class="card" style="margin-top:12px;text-align:center;color:#86a0be;padding:20px">
    <div style="font-size:32px;margin-bottom:6px">🛠️</div>
    <div style="font-weight:600;color:#4a90d9">{{ $emptyServicesTitle }}</div>
    <div style="font-size:13px;margin-top:4px">{{ $emptyServicesText }} <a href="{{ route($configureRoute) }}" style="color:#4a90d9">{{ $configureLabel }}</a> — they’ll appear here as business cards.</div>
  </div>
  @endif

  {{-- ─── Workers + Offers split ──────────────────────────────────── --}}
  <div class="split">
    <div class="card" style="margin:0">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px">
        <h3 style="margin:0">Recent Client Requests</h3>
        <a href="{{ route($requestsRoute) }}" style="font-size:12px;color:#4a90d9">Manage all →</a>
      </div>
      @if(($recentClientRequests ?? collect())->count())
        <ul class="list">
          @foreach($recentClientRequests as $req)
            <li>
              <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap">
                <div class="title">{{ $req->customer_name ?: $req->phone }}</div>
                <span class="req-pill {{ $req->status }}">{{ $req->status }}</span>
              </div>
              <div class="sub">{{ $req->phone }} · {{ $req->service_name ?: 'General Request' }} · {{ $req->source ?: 'public_form' }}</div>
            </li>
          @endforeach
        </ul>
      @else
        <p style="margin:0">No client requests yet. New leads from your public page will appear here.</p>
      @endif
    </div>

    <div class="card" style="margin:0">
      <h3 style="margin:0 0 8px">Recent Workers</h3>
      @if($workers->count())
        <ul class="list">
          @foreach($workers as $worker)
            <li>
              <div class="title">{{ $worker->full_name }}</div>
              <div class="sub">{{ $worker->email }}{{ $worker->mobile_number ? ' · ' . $worker->mobile_number : '' }}</div>
            </li>
          @endforeach
        </ul>
      @else
        <p style="margin:0">No workers yet. Start by adding your first worker.</p>
      @endif
    </div>

    <div class="card" style="margin:0">
      <h3 style="margin:0 0 8px">Recent Offers</h3>
      @if($offers->count())
        <ul class="list">
          @foreach($offers as $offer)
            <li>
              <div class="title">{{ $offer->title }}</div>
              <div class="sub">{{ \Illuminate\Support\Str::limit(strip_tags($offer->content ?? ''), 90) }}</div>
            </li>
          @endforeach
        </ul>
      @else
        <p style="margin:0">No offers yet. Create an offer to boost visibility.</p>
      @endif
    </div>
  </div>
</div>
@endsection
