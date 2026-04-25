@extends('layouts.app')

@section('title', ($isService ? 'Service' : 'Shop') . ' Dashboard')

@section('content')
@php
  $label       = $isService ? 'Service' : 'Shop';
  $configRoute = $isService ? route('configure_myservice') : route('configure_myshop');
  $offerRoute  = $isService ? route('myservice_offer_new') : route('myshop_offer_new');
  $reqRoute    = $isService ? route('myservice_requests')  : route('myshop_requests');
  $workRoute   = $isService ? route('workers_myservice')   : route('workers_myshop');
  $expRoute    = $isService ? route('myservice_experience'): route('myshop_experience');
  $idcRoute    = $isService ? route('myservice_idcard')    : route('myshop_idcard');
@endphp
<style>
.db-wrap{max-width:1000px;margin:0 auto;padding:1.1rem 1rem 3rem}
.db-top{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.4rem}
.db-title-block h1{font-size:1.5rem;font-weight:800;color:#1a1208;margin:0 0 .15rem}
.db-title-block p{font-size:.85rem;color:#6b7280;margin:0}
.db-header-actions{display:flex;gap:.5rem;flex-wrap:wrap}
.db-btn{display:inline-flex;align-items:center;gap:.35rem;padding:.5rem 1rem;border-radius:8px;font-size:.82rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;white-space:nowrap}
.db-btn-primary{background:#1a1208;color:#fff}
.db-btn-primary:hover{background:#b91c1c}
.db-btn-outline{background:#fff;color:#374151;border:1px solid #d1d5db}
.db-btn-outline:hover{background:#f9fafb;border-color:#9ca3af}
.db-btn-green{background:#16a34a;color:#fff}
.db-btn-green:hover{background:#15803d}

/* Alert / setup banner */
.db-alert{display:flex;align-items:center;gap:.75rem;padding:.8rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:.84rem}
.db-alert-warn{background:#fffbeb;border:1px solid #fde68a;color:#92400e}
.db-alert-info{background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af}
.db-alert-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534}

/* Stats row */
.db-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;margin-bottom:1.2rem}
.db-stat{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1rem 1.1rem;position:relative;overflow:hidden}
.db-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.db-stat.blue::before{background:linear-gradient(90deg,#3b82f6,#60a5fa)}
.db-stat.green::before{background:linear-gradient(90deg,#16a34a,#4ade80)}
.db-stat.amber::before{background:linear-gradient(90deg,#f59e0b,#fcd34d)}
.db-stat.purple::before{background:linear-gradient(90deg,#8b5cf6,#c4b5fd)}
.db-stat.rose::before{background:linear-gradient(90deg,#e11d48,#fb7185)}
.db-stat-icon{font-size:1.5rem;margin-bottom:.3rem;display:block}
.db-stat-val{font-size:1.7rem;font-weight:800;color:#111827;line-height:1}
.db-stat-label{font-size:.72rem;color:#6b7280;font-weight:600;margin-top:.25rem;text-transform:uppercase;letter-spacing:.5px}
.db-stat-sub{font-size:.7rem;color:#9ca3af;margin-top:.15rem}

/* Quick actions */
.db-actions{display:grid;grid-template-columns:repeat(4,1fr);gap:.6rem;margin-bottom:1.2rem}
.db-action{display:flex;flex-direction:column;align-items:center;gap:.4rem;padding:.85rem .5rem;background:#fff;border:1px solid #e5e7eb;border-radius:12px;text-decoration:none;color:#374151;font-size:.74rem;font-weight:700;text-align:center;transition:all .15s}
.db-action:hover{border-color:#1a1208;background:#f9fafb;color:#1a1208;transform:translateY(-1px)}
.db-action-icon{font-size:1.5rem;line-height:1}

/* Cards */
.db-row{display:grid;grid-template-columns:1fr 1fr;gap:.9rem;margin-bottom:1rem}
.db-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1rem 1.1rem}
.db-card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem}
.db-card-title{font-size:.8rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px}
.db-card-link{font-size:.75rem;color:#2563eb;text-decoration:none;font-weight:600}
.db-card-link:hover{text-decoration:underline}

/* Invoice table */
.db-inv-list{display:flex;flex-direction:column;gap:.45rem}
.db-inv-row{display:flex;align-items:center;justify-content:space-between;padding:.5rem .65rem;background:#f9fafb;border-radius:8px;gap:.5rem;text-decoration:none;color:inherit}
.db-inv-row:hover{background:#f3f4f6}
.db-inv-num{font-size:.75rem;font-weight:700;color:#1a1208}
.db-inv-client{font-size:.75rem;color:#6b7280;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;padding:0 .5rem}
.db-inv-amt{font-size:.78rem;font-weight:700;color:#111827;white-space:nowrap}
.db-badge{display:inline-block;padding:.15rem .45rem;border-radius:100px;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
.db-badge-draft{background:#f3f4f6;color:#6b7280}
.db-badge-sent{background:#dbeafe;color:#1d4ed8}
.db-badge-paid{background:#dcfce7;color:#16a34a}
.db-badge-cancelled{background:#fee2e2;color:#dc2626}
.db-badge-overdue{background:#fef3c7;color:#b45309}

/* Progress bar */
.db-progress-wrap{margin:.5rem 0}
.db-progress-bar{height:8px;background:#e5e7eb;border-radius:100px;overflow:hidden}
.db-progress-fill{height:100%;background:linear-gradient(90deg,#16a34a,#4ade80);border-radius:100px;transition:width .4s ease}
.db-progress-label{display:flex;justify-content:space-between;font-size:.72rem;color:#6b7280;margin-bottom:.3rem}

/* Offers grid */
.db-offers{display:flex;flex-direction:column;gap:.4rem}
.db-offer-row{padding:.5rem .65rem;background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;font-size:.78rem}
.db-offer-title{font-weight:700;color:#c2410c;margin-bottom:.1rem}
.db-offer-desc{color:#78350f;font-size:.72rem;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}

/* Sub card */
.db-sub-active{background:linear-gradient(135deg,#1a1208,#374151);color:#fff;border-radius:10px;padding:.85rem 1rem;margin-bottom:.5rem}
.db-sub-active .db-sub-plan{font-size:1rem;font-weight:800}
.db-sub-active .db-sub-exp{font-size:.75rem;color:rgba(255,255,255,.7);margin-top:.25rem}

.db-empty{text-align:center;padding:1.5rem 1rem;color:#9ca3af;font-size:.8rem}
.db-empty-icon{font-size:2rem;display:block;margin-bottom:.4rem}

@media(max-width:700px){
  .db-stats{grid-template-columns:1fr 1fr}
  .db-actions{grid-template-columns:repeat(3,1fr)}
  .db-row{grid-template-columns:1fr}
  .db-top{flex-direction:column}
  .db-header-actions{width:100%}
  .db-header-actions .db-btn{flex:1;justify-content:center}
}
@media(max-width:460px){
  .db-stats{grid-template-columns:1fr 1fr}
  .db-actions{grid-template-columns:repeat(2,1fr)}
  .db-stat-val{font-size:1.4rem}
}
</style>

<div class="db-wrap">

  {{-- Header --}}
  <div class="db-top">
    <div class="db-title-block">
      <h1>{{ $isService ? '🛠️' : '🏪' }} {{ $label }} Dashboard</h1>
      <p>{{ $shop ? $shop->name : 'No '.$label.' configured yet' }}
        @if($shop && $shop->city) &nbsp;·&nbsp; 📍 {{ $shop->city->name }} @endif
      </p>
    </div>
    <div class="db-header-actions">
      <a href="{{ $offerRoute }}" class="db-btn db-btn-green">🏷 Add Offer</a>
      <a href="{{ route('invoices.create') }}" class="db-btn db-btn-primary">📄 New Invoice</a>
      <a href="{{ $configRoute }}" class="db-btn db-btn-outline">⚙ Configure</a>
    </div>
  </div>

  {{-- Profile completeness alert --}}
  @if($profilePct < 60)
    <div class="db-alert db-alert-warn">
      <span style="font-size:1.2rem">⚠️</span>
      <div>
        <strong>Business profile {{ $profilePct }}% complete.</strong>
        Your logo, bank details, and payment QR are missing from invoices.
        <a href="{{ route('invoices.settings') }}" style="color:#b45309;font-weight:700;margin-left:.4rem">Complete now →</a>
      </div>
    </div>
  @elseif($profilePct < 100)
    <div class="db-alert db-alert-info">
      <span style="font-size:1.2rem">ℹ️</span>
      <div>Profile {{ $profilePct }}% done — a few more fields will make your invoices look professional.
        <a href="{{ route('invoices.settings') }}" style="color:#1d4ed8;font-weight:700;margin-left:.4rem">Finish setup →</a>
      </div>
    </div>
  @else
    <div class="db-alert db-alert-ok">
      <span style="font-size:1.2rem">✓</span>
      <strong>Business profile complete.</strong> Your invoices look professional!
    </div>
  @endif

  {{-- Stats row --}}
  <div class="db-stats">
    <div class="db-stat blue">
      <span class="db-stat-icon">📄</span>
      <div class="db-stat-val">{{ $invoiceStats['total'] }}</div>
      <div class="db-stat-label">Total Invoices</div>
      <div class="db-stat-sub">{{ $invoiceStats['paid'] }} paid · {{ $invoiceStats['sent'] }} sent</div>
    </div>
    <div class="db-stat green">
      <span class="db-stat-icon">💰</span>
      <div class="db-stat-val">₹{{ number_format($invoiceStats['earned'], 0) }}</div>
      <div class="db-stat-label">Total Earned</div>
      <div class="db-stat-sub">From paid invoices</div>
    </div>
    <div class="db-stat amber">
      <span class="db-stat-icon">👥</span>
      <div class="db-stat-val">{{ $workersCount }}</div>
      <div class="db-stat-label">Team Members</div>
      <div class="db-stat-sub"><a href="{{ $workRoute }}" style="color:#b45309;text-decoration:none">Manage →</a></div>
    </div>
    <div class="db-stat purple">
      <span class="db-stat-icon">🏷</span>
      <div class="db-stat-val">{{ $offers->count() }}</div>
      <div class="db-stat-label">Active Offers</div>
      <div class="db-stat-sub">In your city</div>
    </div>
  </div>

  {{-- Quick Actions --}}
  <div class="db-actions">
    <a href="{{ $configRoute }}" class="db-action">
      <span class="db-action-icon">⚙️</span>Configure {{ $label }}
    </a>
    <a href="{{ $workRoute }}" class="db-action">
      <span class="db-action-icon">👥</span>Manage Workers
    </a>
    <a href="{{ $reqRoute }}" class="db-action">
      <span class="db-action-icon">📞</span>Client Requests
    </a>
    <a href="{{ $offerRoute }}" class="db-action">
      <span class="db-action-icon">🏷️</span>Add Offer
    </a>
    <a href="{{ route('invoices.index') }}" class="db-action">
      <span class="db-action-icon">📄</span>My Invoices
    </a>
    <a href="{{ route('invoices.settings') }}" class="db-action">
      <span class="db-action-icon">⚙️</span>Invoice Settings
    </a>
    <a href="{{ $expRoute }}" class="db-action">
      <span class="db-action-icon">📋</span>Experience Letters
    </a>
    <a href="{{ $idcRoute }}" class="db-action">
      <span class="db-action-icon">🪪</span>ID Cards
    </a>
  </div>

  {{-- Main content grid --}}
  <div class="db-row">

    {{-- Recent Invoices --}}
    <div class="db-card">
      <div class="db-card-head">
        <span class="db-card-title">📄 Recent Invoices</span>
        <a href="{{ route('invoices.index') }}" class="db-card-link">View all →</a>
      </div>
      @if($recentInvoices->isEmpty())
        <div class="db-empty">
          <span class="db-empty-icon">📄</span>
          No invoices yet.<br>
          <a href="{{ route('invoices.create') }}" style="color:#2563eb;font-weight:700">Create your first invoice →</a>
        </div>
      @else
        <div class="db-inv-list">
          @foreach($recentInvoices as $inv)
            @php
              $statusClass = match($inv->status) {
                'paid'      => 'db-badge-paid',
                'sent'      => 'db-badge-sent',
                'cancelled' => 'db-badge-cancelled',
                default     => $inv->isOverdue() ? 'db-badge-overdue' : 'db-badge-draft',
              };
            @endphp
            <a href="{{ route('invoices.show', $inv) }}" class="db-inv-row">
              <span class="db-inv-num">{{ $inv->invoice_number }}</span>
              <span class="db-inv-client">{{ $inv->client_name }}</span>
              <span class="db-inv-amt">₹{{ number_format($inv->total, 0) }}</span>
              <span class="db-badge {{ $statusClass }}">{{ $inv->status }}</span>
            </a>
          @endforeach
        </div>

        {{-- Invoice mini stats --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.4rem;margin-top:.85rem">
          <div style="text-align:center;padding:.5rem;background:#f9fafb;border-radius:8px">
            <div style="font-size:1rem;font-weight:800;color:#6b7280">{{ $invoiceStats['draft'] }}</div>
            <div style="font-size:.65rem;color:#9ca3af;font-weight:600">DRAFT</div>
          </div>
          <div style="text-align:center;padding:.5rem;background:#dbeafe;border-radius:8px">
            <div style="font-size:1rem;font-weight:800;color:#1d4ed8">{{ $invoiceStats['sent'] }}</div>
            <div style="font-size:.65rem;color:#1d4ed8;font-weight:600">SENT</div>
          </div>
          <div style="text-align:center;padding:.5rem;background:#dcfce7;border-radius:8px">
            <div style="font-size:1rem;font-weight:800;color:#16a34a">{{ $invoiceStats['paid'] }}</div>
            <div style="font-size:.65rem;color:#16a34a;font-weight:600">PAID</div>
          </div>
        </div>
      @endif
    </div>

    {{-- Right column --}}
    <div style="display:flex;flex-direction:column;gap:.9rem">

      {{-- Subscription --}}
      <div class="db-card">
        <div class="db-card-head">
          <span class="db-card-title">⭐ Subscription</span>
          <a href="{{ route('subscriptions.new') }}" class="db-card-link">Upgrade →</a>
        </div>
        @if($activeSubscription)
          <div class="db-sub-active">
            <div class="db-sub-plan">✓ {{ strtoupper($activeSubscription->plan_key ?: 'Active Plan') }}</div>
            <div class="db-sub-exp">Expires {{ optional($activeSubscription->expires_at)->format('d M Y') }}</div>
          </div>
        @else
          <div class="db-empty" style="padding:.8rem">
            <span class="db-empty-icon">⭐</span>
            <div>No active subscription</div>
            <a href="{{ route('subscriptions.new') }}" class="db-btn db-btn-primary" style="display:inline-flex;margin-top:.5rem;font-size:.78rem;padding:.4rem .9rem">Get Premium →</a>
          </div>
        @endif

        {{-- Astro Dynamic --}}
        <div style="margin-top:.6rem;padding:.6rem .75rem;background:#faf5ff;border:1px solid #e9d5ff;border-radius:8px;font-size:.78rem">
          <div style="font-weight:700;color:#7c3aed;margin-bottom:.2rem">🌟 Astro Dynamic Template</div>
          @if($astroUnlockRequest)
            <span style="color:#6b7280">Status: <strong>{{ ucfirst($astroUnlockRequest->status) }}</strong></span>
          @else
            <span style="color:#6b7280">Not requested · </span>
            <a href="{{ route('subscriptions.new') }}" style="color:#7c3aed;font-weight:700">Request unlock →</a>
          @endif
        </div>
      </div>

      {{-- Profile Progress --}}
      <div class="db-card">
        <div class="db-card-head">
          <span class="db-card-title">📊 Profile Setup</span>
          <a href="{{ route('invoices.settings') }}" class="db-card-link">Edit →</a>
        </div>
        <div class="db-progress-wrap">
          <div class="db-progress-label">
            <span>Business Profile</span>
            <span style="font-weight:700;color:{{ $profilePct >= 80 ? '#16a34a' : ($profilePct >= 50 ? '#b45309' : '#dc2626') }}">{{ $profilePct }}%</span>
          </div>
          <div class="db-progress-bar">
            <div class="db-progress-fill" style="width:{{ $profilePct }}%;background:{{ $profilePct >= 80 ? 'linear-gradient(90deg,#16a34a,#4ade80)' : ($profilePct >= 50 ? 'linear-gradient(90deg,#f59e0b,#fcd34d)' : 'linear-gradient(90deg,#e11d48,#fb7185)') }}"></div>
          </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.6rem">
          @foreach(['Logo' => $profile->business_logo, 'GSTIN' => $profile->gstin, 'Bank' => $profile->bank_name, 'QR Code' => $profile->payment_qr, 'Signature' => $profile->signature] as $item => $val)
            <span style="font-size:.67rem;padding:.18rem .45rem;border-radius:100px;{{ $val ? 'background:#dcfce7;color:#16a34a' : 'background:#fee2e2;color:#dc2626' }};font-weight:700">
              {{ $val ? '✓' : '○' }} {{ $item }}
            </span>
          @endforeach
        </div>
      </div>

      {{-- City Offers --}}
      @if($offers->isNotEmpty())
        <div class="db-card">
          <div class="db-card-head">
            <span class="db-card-title">🏷 Your City Offers</span>
            <a href="{{ $offerRoute }}" class="db-card-link">Add offer →</a>
          </div>
          <div class="db-offers">
            @foreach($offers->take(3) as $o)
              <div class="db-offer-row">
                <div class="db-offer-title">{{ $o->title }}</div>
                <div class="db-offer-desc">{{ Str::limit($o->content, 80) }}</div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

    </div>{{-- end right col --}}
  </div>{{-- end db-row --}}

</div>
@endsection
