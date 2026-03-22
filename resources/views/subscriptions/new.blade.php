@extends('layouts.app')

@section('content')
<style>
  .sub-form-card,.sub-profile-card{overflow:hidden}
  .sub-form-card,.sub-form-card *,.sub-profile-card,.sub-profile-card *{box-sizing:border-box}
  .sub-state{border-radius:14px;padding:22px 18px;text-align:center;margin-top:12px}
  .sub-state.active{background:linear-gradient(135deg,#d1fae5 0%,#a7f3d0 100%);border:1.5px solid #6ee7b7}
  .sub-state.pending{background:linear-gradient(135deg,#fef9c3 0%,#fde68a 100%);border:1.5px solid #fcd34d}
  .sub-state.rejected{background:linear-gradient(135deg,#fee2e2 0%,#fecaca 100%);border:1.5px solid #f87171}
  .sub-state .icon{font-size:36px;margin-bottom:8px}
  .sub-state h2{margin:0 0 6px;font-size:22px}
  .sub-state p{margin:0;font-size:13px;line-height:1.6}
  .sub-state.active h2,.sub-state.active p{color:#065f46}
  .sub-state.pending h2,.sub-state.pending p{color:#78350f}
  .sub-state.rejected h2,.sub-state.rejected p{color:#991b1b}
  .sub-meta{display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:6px 14px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.04em}
  .sub-state.active .sub-meta{background:#059669;color:#fff}
  .sub-state.pending .sub-meta{background:#d97706;color:#fff}
  .sub-state.rejected .sub-meta{background:#dc2626;color:#fff}
  .sub-info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-top:14px;text-align:left}
  .sub-info-item{background:rgba(255,255,255,.45);border:1px solid rgba(255,255,255,.6);border-radius:10px;padding:10px 12px}
  .sub-info-item strong{display:block;font-size:11px;letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px;opacity:.8}
  .sub-info-item span{font-size:14px;font-weight:700}
  .sub-plan-card{margin-top:12px;padding:16px;border:1px solid #dbe7f8;border-radius:12px;background:#fff}
  .sub-request-wrap{max-width:720px;margin:12px auto 0}
  .sub-price-badge{text-align:center;margin:6px 0 16px}
  .sub-price-badge .price{font-size:40px;font-weight:900;color:#2f4e74;letter-spacing:-.5px;line-height:1}
  .sub-price-badge .note{font-size:12px;color:#7a95b0;margin-top:6px}
  .sub-qr-card{background:#f0f5ff;border:1px solid #c8d8ee;border-radius:12px;padding:16px;margin-bottom:14px;text-align:center}
  .sub-qr-card .qr-row{display:flex;justify-content:center;gap:12px;flex-wrap:wrap}
  .sub-qr-card .qr-box{border:1px solid #dbe7f8;border-radius:10px;padding:10px;background:#f9fcff;text-align:center}
  .sub-qr-card img{width:min(76vw,220px);height:auto;object-fit:cover;border-radius:6px}
  .sub-qr-card .qr-amount{font-size:26px;font-weight:900;color:#2f4e74;margin:6px 0 2px}
  .sub-qr-card .qr-help{font-size:12px;color:#7a95b0}
  .sub-form-card{background:#fff;border:1px solid #dbe7f8;border-radius:12px;padding:16px}
  .sub-form-card h3{margin:0 0 10px;font-size:20px;color:#2f4e74}
  .sub-form-card p{margin:0 0 12px;color:#4d647f;font-size:13px;line-height:1.6}
  .sub-form-card ul{margin:0 0 14px 18px;color:#4d647f;line-height:1.8;font-size:13px}
  .sub-form-grid{display:grid;gap:10px}
  .sub-field label{display:block;font-size:12px;font-weight:600;color:#4a6580;margin-bottom:4px}
  .sub-field input,.sub-field textarea{width:100%;padding:9px 11px;border:1px solid #c8d8ee;border-radius:8px;font-size:13px;background:#f9fbff;color:#1e3a5f}
  .sub-field input,.sub-field textarea,.sub-field select{max-width:100%;min-width:0}
  .sub-field textarea{resize:vertical;min-height:74px}
  .sub-field input:focus,.sub-field textarea:focus{outline:none;border-color:#4f85c5;background:#fff}
  .sub-submit-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .sub-submit{padding:12px 18px;background:#f5c518;border:2px solid #e0b10f;color:#2d1c00;font-size:14px;font-weight:800;border-radius:10px;cursor:pointer}
  .sub-submit:disabled{opacity:.55;cursor:not-allowed}
  .sub-proof-note{font-size:11px;color:#7a95b0}
  .sub-profile-card{margin-top:12px;background:#fff;border:1px solid #dbe7f8;border-radius:12px;padding:16px}
  .sub-profile-card h3{margin:0 0 8px;color:#2f4e74}
  .sub-profile-note{font-size:12px;color:#5d6f86;line-height:1.7;margin:0 0 12px}
  .sub-profile-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
  .sub-profile-grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}
  .sub-profile-grid > *{min-width:0}
  .sub-profile-photo{display:flex;align-items:center;gap:10px;margin-top:8px}
  .sub-profile-photo img{width:56px;height:56px;border-radius:12px;object-fit:cover;border:1px solid #dbe7f8}
  @media (max-width: 760px){
    .sub-profile-grid,.sub-profile-grid.two{grid-template-columns:1fr}
  }

  .template-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:10px}
  .tpl-card{border:2px solid #dbe7f8;border-radius:12px;padding:12px;cursor:pointer;transition:border-color .2s,background .2s,transform .2s;text-align:left;background:#fafcff}
  .tpl-card.active{border-color:#2f4e74;background:#eef4ff}
  .tpl-card:hover{transform:translateY(-1px)}
  .tpl-card.locked{border-color:#f2d27a;background:#fffaf0}
  .tpl-card h4{margin:0 0 4px;font-size:20px;color:#2f4e74}
  .tpl-card p{margin:0;font-size:13px;color:#6d84a5;line-height:1.6}
  .tpl-chip{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:3px 8px;border-radius:999px;margin-bottom:8px}
  .tpl-chip.free{background:#e8f7ee;color:#1f8a49;border:1px solid #ccefd9}
  .tpl-chip.paid{background:#fff2d8;color:#9a6500;border:1px solid #f4d49a}
  .tpl-chip.unlocked{background:#e6f9ee;color:#166534;border:1px solid #6ee7a0}
  .tpl-thumb{height:102px;border-radius:10px;overflow:hidden;border:1px solid #dbe7f8;margin-bottom:10px;position:relative;background:#fff}
  .tpl-thumb.dynamic{background:linear-gradient(135deg,#1a0a3b,#7b2ff7)}
  .tpl-thumb.dynamic .mini-top{height:20px;background:rgba(255,255,255,.12)}
  .tpl-thumb.dynamic .mini-hero{padding:10px}.tpl-thumb.dynamic .mini-pill{width:58px;height:8px;border-radius:999px;background:rgba(255,255,255,.28);margin-bottom:8px}.tpl-thumb.dynamic .mini-title{width:84px;height:12px;border-radius:6px;background:#fff;margin-bottom:6px}.tpl-thumb.dynamic .mini-sub{width:110px;height:8px;border-radius:5px;background:rgba(255,255,255,.5);margin-bottom:10px}.tpl-thumb.dynamic .mini-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px}.tpl-thumb.dynamic .mini-box{height:24px;border-radius:8px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.18)}
  .tpl-thumb.astro{background:linear-gradient(135deg,#05000f,#4b1f7a 48%,#c9860a 120%)}
  .tpl-thumb.astro .mini-top{height:20px;background:rgba(0,0,0,.24);display:flex;gap:4px;align-items:center;padding:0 8px}.tpl-thumb.astro .mini-dot{width:16px;height:6px;border-radius:999px;background:rgba(245,197,24,.5)}.tpl-thumb.astro .mini-hero{padding:10px}.tpl-thumb.astro .mini-title{width:105px;height:13px;border-radius:6px;background:linear-gradient(90deg,#fff,#f5c518);margin-bottom:7px}.tpl-thumb.astro .mini-sub{width:95px;height:8px;border-radius:5px;background:rgba(255,220,150,.5);margin-bottom:10px}.tpl-thumb.astro .mini-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:6px}.tpl-thumb.astro .mini-box{height:22px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(245,197,24,.2)}
  .tpl-sample-wrap{margin-top:10px;border:1px solid #dbe7f8;border-radius:12px;overflow:hidden;background:#fff}
  .tpl-sample-head{padding:10px 12px;background:#f8fbff;border-bottom:1px solid #e4edf9;font-size:12px;display:flex;justify-content:space-between;align-items:center}
  .tpl-sample-head strong{color:#2f4e74}
  .tpl-sample-head span{color:#6d84a5}
  .tpl-sample{display:none}
  .tpl-sample.active{display:block}
  .tpl-sample-scroll{overflow-x:auto}
  /* dynamic sample */
  .sample-dyn{font-family:inherit}
  .sample-dyn-top{display:flex;gap:12px;padding:10px 16px;background:#1a0a3b;font-size:11px;color:rgba(255,255,255,.7)}
  .sample-dyn-hero{background:linear-gradient(135deg,#1a0a3b,#7b2ff7);padding:24px 18px 20px;color:#fff}
  .sample-dyn-badge{display:inline-block;background:rgba(255,255,255,.15);border-radius:999px;padding:3px 12px;font-size:11px;margin-bottom:10px}
  .sample-dyn-title{font-size:22px;font-weight:800;line-height:1.2;margin-bottom:8px}
  .sample-dyn-sub{font-size:12px;opacity:.8;line-height:1.6;margin-bottom:14px}
  .sample-dyn-actions{display:flex;gap:8px;flex-wrap:wrap}
  .sample-dyn-actions span{padding:7px 16px;border-radius:8px;font-size:12px;font-weight:600;background:rgba(255,255,255,.15);cursor:default}
  .sample-dyn-actions span:first-child{background:#fff;color:#2f4e74}
  .sample-dyn-body{background:#f4f8ff;padding:14px 16px}
  .sample-dyn-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px}
  .sample-dyn-card{background:#fff;border:1px solid #dbe7f8;border-radius:10px;padding:14px}
  .sample-dyn-card h5{margin:0 0 4px;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#6d84a5}
  .sample-dyn-card h4{margin:0 0 6px;font-size:14px;color:#2f4e74}
  .sample-dyn-card p{margin:0;font-size:12px;color:#4d647f;line-height:1.6}
  .sample-dyn-card.tall{grid-row:span 2}
  .sample-dyn-mini-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:10px}
  .sample-dyn-mini-grid .mini{background:#f0f5ff;border-radius:8px;padding:8px;font-size:11px}
  .sample-dyn-mini-grid .mini strong{display:block;color:#2f4e74;margin-bottom:2px}
  .sample-dyn-mini-grid .mini span{color:#6d84a5}
  .sample-dyn-cta{background:linear-gradient(135deg,#1a0a3b,#7b2ff7);border-radius:10px;padding:18px;color:#fff;text-align:center;margin-top:12px}
  .sample-dyn-cta h4{margin:0 0 6px;font-size:16px}
  .sample-dyn-cta p{margin:0 0 12px;font-size:12px;opacity:.85}
  .sample-dyn-footer{display:flex;gap:16px;padding:10px 16px;background:#1a0a3b;font-size:11px;color:rgba(255,255,255,.5);margin-top:10px;border-radius:0 0 12px 12px}
  /* astro sample */
  .sample-astro-outer{background:#0a0414}
  .sample-astro-top{display:flex;gap:12px;padding:10px 16px;background:rgba(0,0,0,.3);font-size:11px;color:rgba(212,184,255,.7)}
  .sample-astro-hero{padding:28px 18px 22px;background:linear-gradient(160deg,#0a0414 0%,#2e0a5e 55%,#7c3a04 100%);color:#fff}
  .sample-astro-badge{display:inline-block;background:rgba(245,197,24,.15);border:1px solid rgba(245,197,24,.3);border-radius:999px;padding:3px 12px;font-size:11px;color:#f5c518;margin-bottom:10px}
  .sample-astro-title{font-size:22px;font-weight:800;line-height:1.2;margin-bottom:8px;background:linear-gradient(90deg,#fff,#f5c518);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
  .sample-astro-sub{font-size:12px;color:rgba(232,224,255,.75);line-height:1.6;margin-bottom:14px}
  .sample-astro-actions{display:flex;gap:8px;flex-wrap:wrap}
  .sample-astro-actions span{padding:7px 16px;border-radius:8px;font-size:12px;font-weight:600;background:rgba(255,255,255,.08);color:rgba(232,224,255,.9);cursor:default}
  .sample-astro-actions span:first-child{background:linear-gradient(90deg,#f5c518,#e0a000);color:#1a0a00}
  .sample-astro-body{background:#0a0414;padding:14px 16px}
  .sample-astro-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:12px}
  .sample-astro-card{background:rgba(255,255,255,.04);border:1px solid rgba(245,197,24,.15);border-radius:10px;padding:12px}
  .sample-astro-card strong{display:block;font-size:13px;color:#e9d5ff;margin-bottom:3px}
  .sample-astro-card span{font-size:11px;color:rgba(232,224,255,.5)}
  .sample-astro-cta{background:linear-gradient(160deg,#2e0a5e,#7c3a04);border-radius:10px;padding:18px;text-align:center;margin-top:12px;border:1px solid rgba(245,197,24,.2)}
  .sample-astro-cta h4{margin:0 0 6px;font-size:16px;color:#f5c518}
  .sample-astro-cta p{margin:0 0 12px;font-size:12px;color:rgba(232,224,255,.8)}
  .sample-astro-footer{display:flex;gap:16px;padding:10px 16px;background:rgba(0,0,0,.4);font-size:11px;color:rgba(212,184,255,.4);margin-top:10px;border-radius:0 0 12px 12px}
</style>
<div class="panel">
  @php
    $entityLabel = $entityLabel ?? 'Service';
    $dashboardLabel = $dashboardLabel ?? 'MyService';
    $websiteLabel = $websiteLabel ?? 'Public service website';
    $profileLabel = $entityLabel === 'Shop' ? 'Shop Owner Profile' : 'Service Provider Profile';
    $profileTc = $shop->page_config['template_content'] ?? [];
    $profileDefaultName = auth()->user()->full_name ?: ($entityLabel === 'Shop' ? 'Shop Owner' : 'Service Provider');
    $profileDefaultTitle = $entityLabel === 'Shop' ? 'Founder & Lead Shop Expert' : 'Founder & Lead Service Expert';
    $profileDefaultContact = $shop->phone ?: (auth()->user()->mobile_number ?? '');
    $profileDefaultBio = $entityLabel === 'Shop'
      ? 'Trusted local shop owner focused on reliable products and customer-friendly support.'
      : 'Experienced local professional dedicated to reliable and customer-friendly service.';
  @endphp

  <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap">
    <h1 style="margin:0">Subscription Plans</h1>
    <a href="{{ route($dashboardRoute) }}" class="button">← Back to Dashboard</a>
  </div>

  @if($hasActiveSubscription)
    <div class="sub-state active">
      <div class="icon">🎉</div>
      <h2>Subscription Active</h2>
      <p>Your yearly base plan is active and your {{ strtolower($entityLabel) }} profile is fully enabled.</p>
      <div class="sub-meta">✓ Active Plan</div>

      @if($activeSubscription)
        <div class="sub-info-grid">
          <div class="sub-info-item">
            <strong>Plan</strong>
            <span>{{ ucfirst(str_replace('_', ' ', $activeSubscription->plan_key ?: 'yearly_base')) }}</span>
          </div>
          <div class="sub-info-item">
            <strong>Amount</strong>
            <span>₹{{ number_format((float) $activeSubscription->amount, 0) }}</span>
          </div>
          <div class="sub-info-item">
            <strong>Started</strong>
            <span>{{ optional($activeSubscription->starts_at)->format('d M Y') ?: '—' }}</span>
          </div>
          <div class="sub-info-item">
            <strong>Expires</strong>
            <span>{{ optional($activeSubscription->expires_at)->format('d M Y') ?: '—' }}</span>
          </div>
        </div>
      @endif
    </div>
  @elseif($hasPendingSubscriptionRequest)
    <div class="sub-state pending">
      <div class="icon">⏳</div>
      <h2>Request Under Review</h2>
      <p>
        Your subscription request is currently under admin review.
        @if($latestSubscriptionRequest && $latestSubscriptionRequest->created_at)
          Submitted {{ $latestSubscriptionRequest->created_at->diffForHumans() }}.
        @endif
      </p>
      <div class="sub-meta">⏳ Pending Admin Review</div>
    </div>
  @elseif($latestSubscriptionRequest && in_array($latestSubscriptionRequest->status, ['rejected', 'failed', 'cancelled'], true))
    <div class="sub-state rejected">
      <div class="icon">✕</div>
      <h2>Request {{ ucfirst($latestSubscriptionRequest->status) }}</h2>
      <p>You can submit a new subscription request with corrected payment proof.</p>
      <div class="sub-meta">✕ {{ strtoupper($latestSubscriptionRequest->status) }}</div>
      @if($latestSubscriptionRequest->admin_notes)
        <div class="sub-plan-card" style="margin-top:12px;border-color:#f5bcbc;background:#fff5f5;color:#8c2c2c;text-align:left">
          <strong>Admin note:</strong> {{ $latestSubscriptionRequest->admin_notes }}
        </div>
      @endif
    </div>
  @endif

  @if(!$hasActiveSubscription && !$hasPendingSubscriptionRequest)
  <div class="sub-request-wrap">
    <div class="sub-price-badge">
      <div class="price">₹{{ number_format((float) $plans[0]['amount'], 0) }}</div>
      <div class="note">One-year access · submit proof for admin verification</div>
    </div>

    <div class="sub-qr-card">
      @if($paymentQrUrl || $paymentBarcodeUrl)
        <div class="qr-row">
          @if($paymentQrUrl)
            <div class="qr-box">
              <img src="{{ $paymentQrUrl }}" alt="Payment QR">
              <div style="font-size:12px;color:#4d647f;margin-top:6px">Scan QR to pay</div>
            </div>
          @endif
          @if($paymentBarcodeUrl)
            <div class="qr-box">
              <img src="{{ $paymentBarcodeUrl }}" alt="Payment Barcode">
              <div style="font-size:12px;color:#4d647f;margin-top:6px">Barcode / UPI info</div>
            </div>
          @endif
        </div>
      @else
        <div style="font-size:13px;color:#a05b00;background:#fff7e4;border:1px solid #f0d79a;border-radius:8px;padding:10px">Payment QR not available right now. Please contact admin.</div>
      @endif
      <div class="qr-amount">₹{{ number_format((float) $plans[0]['amount'], 0) }}</div>
      <div class="qr-help">Pay first, then submit payment proof below</div>
    </div>

    <div class="sub-form-card">
      <h3>Request Yearly Base Plan</h3>
      <p>{{ $websiteLabel }} with custom URL, Dynamic Service Template access, and {{ $dashboardLabel }} dashboard tools.</p>
      <ul>
        <li>{{ $websiteLabel }} with custom URL</li>
        <li>Dynamic Service Template unlocked</li>
        <li>{{ $dashboardLabel }} dashboard and profile tools</li>
      </ul>

      <form method="POST" action="{{ route('subscriptions.create') }}" enctype="multipart/form-data" class="sub-form-grid">
        @csrf
        <input type="hidden" name="plan_key" value="yearly_base">

        <div class="sub-field">
          <label for="payment_transaction_id">Transaction ID (optional if screenshot uploaded)</label>
          <input id="payment_transaction_id" type="text" name="payment_transaction_id" value="{{ old('payment_transaction_id') }}" placeholder="UPI / bank transaction reference">
          @error('payment_transaction_id')
            <div style="margin-top:4px;color:#d64545;font-size:12px">{{ $message }}</div>
          @enderror
        </div>

        <div class="sub-field">
          <label for="payment_screenshot">Payment Screenshot (optional if transaction ID provided)</label>
          <input id="payment_screenshot" type="file" name="payment_screenshot" accept="image/*">
          @error('payment_screenshot')
            <div style="margin-top:4px;color:#d64545;font-size:12px">{{ $message }}</div>
          @enderror
        </div>

        <div class="sub-field">
          <label for="comment">Comment (optional)</label>
          <textarea id="comment" name="comment" rows="3" placeholder="Any note for admin review">{{ old('comment') }}</textarea>
          @error('comment')
            <div style="margin-top:4px;color:#d64545;font-size:12px">{{ $message }}</div>
          @enderror
        </div>

        <div class="sub-submit-row">
          <button type="submit" class="sub-submit" {{ $hasActiveSubscription || $hasPendingSubscriptionRequest ? 'disabled' : '' }}>Submit Subscription Request</button>
          <span class="sub-proof-note">At least one proof is required: transaction ID or screenshot.</span>
        </div>
      </form>
    </div>
  </div>
  @endif

  <div class="sub-profile-card">
    <h3>{{ $profileLabel }}</h3>
    <p class="sub-profile-note">This profile appears on your public page. It is now managed from this Subscription section.</p>

    @if(!$hasActiveSubscription)
      <div style="margin-bottom:10px;padding:10px;border:1px solid #f0d79a;background:#fff7e4;border-radius:8px;font-size:12px;color:#7b5b1d">
        Activate your yearly base subscription first to save profile details.
      </div>
    @endif

    <form method="POST" action="{{ route('subscriptions.profile.update') }}" enctype="multipart/form-data" class="sub-form-grid">
      @csrf
      @method('PATCH')

      <div class="sub-profile-grid">
        <div class="sub-field">
          <label for="profile_provider_name">Provider Name</label>
          <input id="profile_provider_name" type="text" name="profile[provider_name]" value="{{ old('profile.provider_name', $profileTc['provider_name'] ?? $profileDefaultName) }}" placeholder="Your full name">
        </div>
        <div class="sub-field">
          <label for="profile_provider_age">Age</label>
          <input id="profile_provider_age" type="text" name="profile[provider_age]" value="{{ old('profile.provider_age', $profileTc['provider_age'] ?? '') }}" placeholder="e.g. 32">
        </div>
        <div class="sub-field">
          <label for="profile_provider_title">Professional Title</label>
          <input id="profile_provider_title" type="text" name="profile[provider_title]" value="{{ old('profile.provider_title', $profileTc['provider_title'] ?? $profileDefaultTitle) }}" placeholder="Founder & Lead Expert">
        </div>
      </div>

      <div class="sub-profile-grid">
        <div class="sub-field">
          <label for="profile_provider_email">Email</label>
          <input id="profile_provider_email" type="email" name="profile[provider_email]" value="{{ old('profile.provider_email', $profileTc['provider_email'] ?? (auth()->user()->email ?? '')) }}" placeholder="name@example.com">
        </div>
        <div class="sub-field">
          <label for="profile_provider_contact">Contact Number</label>
          <input id="profile_provider_contact" type="text" name="profile[provider_contact]" value="{{ old('profile.provider_contact', $profileTc['provider_contact'] ?? $profileDefaultContact) }}" placeholder="+91 ...">
        </div>
        <div class="sub-field">
          <label for="profile_provider_experience">Experience Tag</label>
          <input id="profile_provider_experience" type="text" name="profile[provider_experience]" value="{{ old('profile.provider_experience', $profileTc['provider_experience'] ?? '5+ Years Experience') }}" placeholder="10+ Years Experience">
        </div>
      </div>

      <div class="sub-profile-grid two">
        <div class="sub-field">
          <label for="profile_provider_photo_file">Provider Photo</label>
          <input id="profile_provider_photo_file" type="file" name="profile[provider_photo_file]" accept="image/*">
          <input type="hidden" name="profile[provider_photo_existing]" value="{{ old('profile.provider_photo_existing', $profileTc['provider_photo'] ?? '') }}">
          <div class="sub-proof-note">Upload JPG/PNG/WEBP (max 2MB).</div>
          @php
            $profilePhoto = (string) ($profileTc['provider_photo'] ?? '');
            $profilePhotoUrl = $profilePhoto === ''
              ? ''
              : (\Illuminate\Support\Str::startsWith($profilePhoto, ['http://', 'https://', 'data:', '/']) ? $profilePhoto : \Illuminate\Support\Facades\Storage::url($profilePhoto));
          @endphp
          @if($profilePhotoUrl !== '')
            <div class="sub-profile-photo">
              <img src="{{ $profilePhotoUrl }}" alt="Current profile photo">
              <span class="sub-proof-note">Current photo</span>
            </div>
          @endif
        </div>
        <div class="sub-field">
          <label for="profile_provider_bio">Provider Bio</label>
          <textarea id="profile_provider_bio" name="profile[provider_bio]" rows="4" placeholder="Describe the provider, expertise, trust, and background">{{ old('profile.provider_bio', $profileTc['provider_bio'] ?? $profileDefaultBio) }}</textarea>
        </div>
      </div>

      @if($errors->has('profile.provider_name') || $errors->has('profile.provider_age') || $errors->has('profile.provider_title') || $errors->has('profile.provider_email') || $errors->has('profile.provider_contact') || $errors->has('profile.provider_experience') || $errors->has('profile.provider_bio') || $errors->has('profile.provider_photo_file'))
        <div style="color:#d64545;font-size:12px">
          {{ $errors->first('profile.provider_name') ?: $errors->first('profile.provider_age') ?: $errors->first('profile.provider_title') ?: $errors->first('profile.provider_email') ?: $errors->first('profile.provider_contact') ?: $errors->first('profile.provider_experience') ?: $errors->first('profile.provider_bio') ?: $errors->first('profile.provider_photo_file') }}
        </div>
      @endif

      <div class="sub-submit-row">
        <button type="submit" class="sub-submit" {{ !$hasActiveSubscription ? 'disabled' : '' }}>Save {{ $profileLabel }}</button>
      </div>
    </form>
  </div>

  <div class="card" style="margin-top:12px;padding:16px;border:1px solid #f3e5b2;background:#fffdf6">
    <h3 style="margin-top:0">Paid Template Add-on</h3>
    <p style="margin:0;color:#5d5332;line-height:1.7">
      Astro Dynamic Template is a premium add-on priced at <strong>₹{{ number_format((float) $astroUnlockPrice, 0) }}</strong>.
      You can view sample in configuration, and submit unlock request with payment proof from your profile configuration page.
    </p>
  </div>

  @php
    $activeTemplate = old('template', $shop->template ?: 'dynamic_service');
    $tc = $shop->page_config['template_content'] ?? [];
    $configuredServices = is_array($shop->page_config['services'] ?? null) ? $shop->page_config['services'] : [];
    $serviceGroups = is_array($tc['service_groups'] ?? null) ? $tc['service_groups'] : [];

    $serviceName = $shop->name ?: 'Your Service Name';
    $heroTitle = trim((string)($tc['hero_title'] ?? 'Professional Service For Your Needs'));
    $heroDesc = trim((string)($tc['hero_description'] ?? 'Fast, reliable, and affordable service from experienced professionals.'));
    $providerName = trim((string)($tc['provider_name'] ?? (auth()->user()->full_name ?: 'Service Provider')));
    $providerContact = trim((string)($tc['provider_contact'] ?? ($shop->phone ?: (auth()->user()->mobile_number ?? 'Not set'))));

    $heroBadge = trim((string)($tc['hero_badge'] ?? 'Trusted Local Service'));
    $primaryCta = trim((string)($tc['primary_cta'] ?? 'Book Service'));
    $secondaryCta = trim((string)($tc['secondary_cta'] ?? 'Get Free Quote'));
    $servicesLabel = trim((string)($tc['services_label'] ?? 'Our Services'));
    $servicesTitle = trim((string)($tc['services_title'] ?? 'Services We Offer'));
    $servicesSubtitle = trim((string)($tc['services_subtitle'] ?? 'Choose from our most popular services.'));
    $ctaTitle = trim((string)($tc['cta_title'] ?? 'Need Help Today?'));
    $ctaDescription = trim((string)($tc['cta_description'] ?? 'Contact now and get quick support from a trusted local expert.'));
    $ctaButton = trim((string)($tc['cta_button'] ?? 'Contact Now'));
    $footerBrand = trim((string)($tc['footer_brand'] ?? ($shop->name ?: 'My Service Brand')));
    $footerTagline = trim((string)($tc['footer_tagline'] ?? 'Trusted · Fast · Professional'));

    $dynamicPreviewItems = array_slice(array_values(array_filter(array_map(function($svc){
      $name = trim((string)($svc['name'] ?? ''));
      if ($name === '') return null;
      return [
        'name' => $name,
        'desc' => trim((string)($svc['description'] ?? 'Professional service')),
      ];
    }, $configuredServices))), 0, 4);

    if (empty($dynamicPreviewItems)) {
      $dynamicPreviewItems = [
        ['name' => 'Plumbing Repair', 'desc' => 'Fast leak fixing'],
        ['name' => 'Home Wiring', 'desc' => 'Safe installation'],
        ['name' => 'AC Service', 'desc' => 'Cleaning & repair'],
        ['name' => 'Emergency', 'desc' => 'Quick response'],
      ];
    }

    $astroPreviewItems = [];
    foreach ($serviceGroups as $group) {
      foreach ((array)($group['items'] ?? []) as $item) {
        $itemName = trim((string)($item['name'] ?? ''));
        if ($itemName === '') continue;
        $astroPreviewItems[] = [
          'name' => $itemName,
          'desc' => trim((string)($item['description'] ?? 'Premium service item')),
        ];
        if (count($astroPreviewItems) >= 4) break 2;
      }
    }
    if (empty($astroPreviewItems)) {
      $astroPreviewItems = $dynamicPreviewItems;
    }
  @endphp

  <div class="card" style="margin-top:12px;padding:16px;border:1px solid #dbe7f8">
    <h3 style="margin:0 0 10px">Template Preview & Selection</h3>
    <p style="margin:0 0 12px;color:#4d647f">Preview templates with your current site details and set the one you want.</p>

    <div class="template-cards">
      <button type="button" class="tpl-card {{ $activeTemplate === 'dynamic_service' ? 'active' : '' }}" data-template="dynamic_service" data-locked="0">
        <span class="tpl-chip free">Included · Free</span>
        <div class="tpl-thumb dynamic">
          <div class="mini-top"></div>
          <div class="mini-hero"><div class="mini-pill"></div><div class="mini-title"></div><div class="mini-sub"></div><div class="mini-grid"><div class="mini-box"></div><div class="mini-box"></div><div class="mini-box"></div></div></div>
        </div>
        <h4>Dynamic Service Template</h4>
        <p>Premium dynamic template included in your yearly subscription.</p>
      </button>

      <button type="button" class="tpl-card {{ $activeTemplate === 'astro_dynamic' ? 'active' : '' }} {{ !$astroUnlocked ? 'locked' : '' }}" data-template="astro_dynamic" data-locked="{{ $astroUnlocked ? '0' : '1' }}">
        @if($astroUnlocked)
          <span class="tpl-chip unlocked">✓ Unlocked</span>
        @else
          <span class="tpl-chip paid">Premium · ₹{{ number_format((float) $astroUnlockPrice, 0) }} Unlock</span>
        @endif
        <div class="tpl-thumb astro">
          <div class="mini-top"><span class="mini-dot"></span><span class="mini-dot"></span><span class="mini-dot"></span></div>
          <div class="mini-hero"><div class="mini-title"></div><div class="mini-sub"></div><div class="mini-grid"><div class="mini-box"></div><div class="mini-box"></div><div class="mini-box"></div><div class="mini-box"></div></div></div>
        </div>
        <h4>Astro Dynamic Template</h4>
        @if($astroUnlocked)
          <p style="color:#166534;font-weight:500">Unlocked — cosmic premium dark template ready to use.</p>
        @else
          <p>Cosmic premium landing style. Unlock is required before saving this template.</p>
        @endif
      </button>
    </div>

    @error('template')
      <div style="margin-top:8px;color:#d64545;font-size:12px">{{ $message }}</div>
    @enderror
    <div id="subscription-template-lock-message" style="margin-top:8px;color:#d64545;font-size:12px;display:none">You have to unlock this template first. Click Unlock option and submit payment proof.</div>

    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:10px">
      <form method="POST" action="{{ route('subscriptions.template.update') }}" id="subscription-template-form" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        @csrf
        @method('PATCH')
        <input type="hidden" name="template" id="subscription-template-input" value="{{ $activeTemplate }}">
        <button type="submit" class="toggle-btn">Save Selected Template</button>
      </form>
      @if(!$astroUnlocked)
        <a href="{{ route($unlockRoute) }}" class="button" style="background:#f5c518;border-color:#e0b10f;color:#2d1c00;text-decoration:none">Unlock Astro Dynamic →</a>
      @endif
    </div>

    <div class="tpl-sample-wrap">
      <div class="tpl-sample-head"><strong>Sample View</strong><span>Visual preview of each template</span></div>

      {{-- Dynamic Service Sample --}}
      <div class="tpl-sample {{ $activeTemplate === 'dynamic_service' ? 'active' : '' }}" data-sample="dynamic_service">
        <div class="tpl-sample-scroll">
        <div class="sample-dyn">
          <div class="sample-dyn-top"><span>Home</span><span>Services</span><span>Contact</span></div>
          <div class="sample-dyn-hero">
            <div class="sample-dyn-badge">{{ $heroBadge }}</div>
            <div class="sample-dyn-title">{{ $heroTitle }}</div>
            <div class="sample-dyn-sub">{{ $heroDesc }}</div>
            <div class="sample-dyn-actions"><span>{{ $primaryCta }}</span><span>{{ $secondaryCta }}</span></div>
          </div>
          <div class="sample-dyn-body">
            <div class="sample-dyn-grid">
              <div class="sample-dyn-card tall">
                <h5>{{ $servicesLabel }}</h5>
                <h4>{{ $servicesTitle }}</h4>
                <p>{{ $servicesSubtitle }}</p>
                <div class="sample-dyn-mini-grid">
                  @foreach($dynamicPreviewItems as $item)
                    <div class="mini"><strong>{{ $item['name'] }}</strong><span>{{ $item['desc'] }}</span></div>
                  @endforeach
                </div>
              </div>
              <div class="sample-dyn-card">
                <h5>Provider Card</h5>
                <h4>{{ $providerName }}</h4>
                <p>Contact: {{ $providerContact }}</p>
              </div>
            </div>
            <div class="sample-dyn-cta">
              <h4>{{ $ctaTitle }}</h4>
              <p>{{ $ctaDescription }}</p>
              <div class="sample-dyn-actions" style="justify-content:center"><span>{{ $ctaButton }}</span><span>{{ $secondaryCta }}</span></div>
            </div>
            <div class="sample-dyn-footer"><span>{{ $footerBrand }}</span><span>{{ $footerTagline }}</span><span>© 2026</span></div>
          </div>
        </div>
        </div>
      </div>

      {{-- Astro Dynamic Sample --}}
      <div class="tpl-sample {{ $activeTemplate === 'astro_dynamic' ? 'active' : '' }}" data-sample="astro_dynamic">
        <div class="tpl-sample-scroll">
        <div class="sample-astro-outer">
          <div class="sample-astro-top"><span>Home</span><span>Services</span><span>Consult</span></div>
          <div class="sample-astro-hero">
            <div class="sample-astro-badge">✦ Premium Cosmic Style</div>
            <div class="sample-astro-title">{{ $heroTitle }}</div>
            <div class="sample-astro-sub">{{ $heroDesc }}</div>
            <div class="sample-astro-actions"><span>{{ $primaryCta }}</span><span>{{ $secondaryCta }}</span></div>
          </div>
          <div class="sample-astro-body">
            <div class="sample-astro-grid">
              @foreach($astroPreviewItems as $item)
                <div class="sample-astro-card"><strong>{{ $item['name'] }}</strong><span>{{ $item['desc'] }}</span></div>
              @endforeach
            </div>
            <div class="sample-astro-cta">
              <h4>{{ $ctaTitle }}</h4>
              <p>{{ $ctaDescription }}</p>
              <div class="sample-astro-actions" style="justify-content:center"><span>{{ $ctaButton }}</span><span>{{ $secondaryCta }}</span></div>
            </div>
            <div class="sample-astro-footer"><span>{{ $footerBrand }}</span><span>{{ $footerTagline }}</span><span>© 2026</span></div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const templateInput = document.getElementById('subscription-template-input');
  const templateForm  = document.getElementById('subscription-template-form');
  const unlockPageUrl = '{{ route($unlockRoute) }}';
  const sampleViews   = document.querySelectorAll('.tpl-sample');

  function syncTemplateSelection(card){
    if (!card || !templateInput) return;
    templateInput.value = card.dataset.template;
    sampleViews.forEach(function(view){
      view.classList.toggle('active', view.dataset.sample === card.dataset.template);
    });
  }

  document.querySelectorAll('.tpl-card').forEach(function(card){
    card.addEventListener('click', function(){
      const isLocked = card.dataset.locked === '1';
      document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('active'));
      card.classList.add('active');
      syncTemplateSelection(card);
      if (isLocked) {
        window.location.href = unlockPageUrl;
      }
    });
  });

  if (templateForm && templateInput) {
    templateForm.addEventListener('submit', function(e){
      const astroCard = document.querySelector('.tpl-card[data-template="astro_dynamic"]');
      if (templateInput.value === 'astro_dynamic' && astroCard && astroCard.dataset.locked === '1') {
        e.preventDefault();
        window.location.href = unlockPageUrl;
      }
    });
  }
});
</script>
@endsection
