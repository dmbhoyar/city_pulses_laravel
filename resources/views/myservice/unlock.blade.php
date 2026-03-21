@extends('layouts.app')

@php
  $configureRoute = $configureRoute ?? 'configure_myservice';
  $unlockTitle = $unlockTitle ?? 'Unlock Astro Dynamic Template';
@endphp

@section('title', $unlockTitle)

@section('content')
<style>
  .unlock-page{max-width:520px;margin:18px auto;padding:0 14px 40px}
  .unlock-page-back{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#35527a;text-decoration:none;margin-bottom:14px}
  .unlock-page-back:hover{color:#1e3a5f}

  /* Status banners */
  .unlock-state{border-radius:14px;padding:24px 20px;text-align:center;margin-bottom:18px}
  .unlock-state.approved{background:linear-gradient(135deg,#d1fae5 0%,#a7f3d0 100%);border:1.5px solid #6ee7b7}
  .unlock-state.pending{background:linear-gradient(135deg,#fef9c3 0%,#fde68a 100%);border:1.5px solid #fcd34d}
  .unlock-state.rejected{background:linear-gradient(135deg,#fee2e2 0%,#fecaca 100%);border:1.5px solid #f87171}
  .unlock-state-icon{font-size:40px;margin-bottom:8px}
  .unlock-state h2{margin:0 0 6px;font-size:20px}
  .unlock-state.approved h2{color:#065f46}
  .unlock-state.pending h2{color:#78350f}
  .unlock-state.rejected h2{color:#991b1b}
  .unlock-state p{margin:0;font-size:13px;line-height:1.6}
  .unlock-state.approved p{color:#065f46}
  .unlock-state.pending p{color:#78350f}
  .unlock-state.rejected p{color:#991b1b}
  .unlock-state .state-meta{
    display:inline-flex;align-items:center;gap:6px;
    margin-top:12px;padding:6px 14px;border-radius:999px;
    font-size:12px;font-weight:600;letter-spacing:.04em
  }
  .unlock-state.approved .state-meta{background:#059669;color:#fff}
  .unlock-state.pending .state-meta{background:#d97706;color:#fff}
  .unlock-state.rejected .state-meta{background:#dc2626;color:#fff}

  /* Template card preview */
  .unlock-preview-card{background:#0a0414;border-radius:14px;padding:18px;margin-bottom:18px;display:flex;gap:16px;align-items:center}
  .unlock-preview-thumb{width:72px;height:54px;flex-shrink:0;border-radius:8px;background:linear-gradient(135deg,#1a0533 0%,#3b0d6e 100%);display:flex;flex-direction:column;gap:4px;padding:7px}
  .utp-bar{height:5px;border-radius:3px;background:rgba(168,85,247,.6)}
  .utp-bar.accent{background:#a855f7;width:60%}
  .utp-row{display:flex;gap:4px}
  .utp-box{flex:1;height:8px;border-radius:3px;background:rgba(168,85,247,.25)}
  .unlock-preview-info{flex:1}
  .unlock-preview-info h3{margin:0 0 4px;font-size:15px;color:#e9d5ff;font-weight:700}
  .unlock-preview-info p{margin:0;font-size:12px;color:rgba(232,224,255,.65);line-height:1.5}

  /* Price badge */
  .unlock-price-badge{text-align:center;margin-bottom:20px}
  .unlock-price-badge .price{font-size:32px;font-weight:800;color:#2f4e74;letter-spacing:-.5px}
  .unlock-price-badge .price-note{font-size:12px;color:#7a95b0;margin-top:4px}

  /* Form card */
  .unlock-form-card{background:#fff;border:1px solid #dbe7f8;border-radius:14px;padding:20px}
  .unlock-form-card h3{margin:0 0 14px;font-size:15px;color:#2f4e74;border-bottom:1px solid #eaf0fb;padding-bottom:8px}
  .uf-row{margin-bottom:14px}
  .uf-row label{display:block;font-size:12px;font-weight:600;color:#4a6580;margin-bottom:5px}
  .uf-row input[type=text],.uf-row input[type=file],.uf-row textarea{
    width:100%;box-sizing:border-box;padding:9px 11px;border:1px solid #c8d8ee;
    border-radius:8px;font-size:13px;background:#f9fbff;color:#1e3a5f;
    transition:border-color .15s
  }
  .uf-row input:focus,.uf-row textarea:focus{border-color:#4f85c5;outline:none;background:#fff}
  .uf-row textarea{resize:vertical;min-height:70px}
  .uf-divider{display:flex;align-items:center;gap:10px;margin:16px 0}
  .uf-divider::before,.uf-divider::after{content:'';flex:1;height:1px;background:#dbe7f8}
  .uf-divider span{font-size:11px;color:#9ab2cc;white-space:nowrap}
  .uf-note{font-size:11px;color:#7a95b0;margin-top:6px}
  .uf-submit-btn{
    display:block;width:100%;padding:13px;
    background:#f5c518;border:2px solid #e0b10f;color:#2d1c00;
    font-size:15px;font-weight:700;border-radius:10px;cursor:pointer;
    transition:background .15s,transform .1s;text-align:center
  }
  .uf-submit-btn:hover:not(:disabled){background:#e6b800;transform:translateY(-1px)}
  .uf-submit-btn:disabled{opacity:.5;cursor:not-allowed}

  /* QR section */
  .unlock-qr-section{background:#f0f5ff;border:1px solid #c8d8ee;border-radius:12px;padding:16px;margin-bottom:16px;text-align:center}
  .unlock-qr-section img{width:min(80vw,240px);height:auto;display:block;margin:0 auto 10px;border-radius:8px;border:2px solid #dbe7f8}
  .unlock-qr-section .qr-label{font-size:12px;color:#4a6580;font-weight:600}
  .unlock-qr-section .qr-amount{font-size:20px;font-weight:800;color:#2f4e74;margin:4px 0}
  .unlock-qr-section .qr-unavail{font-size:13px;color:#a05b00;background:#fff7e4;border:1px solid #f0d79a;border-radius:8px;padding:10px}

  /* Warning box */
  .unlock-warn{background:#fff7e4;border:1px solid #f0d79a;border-radius:10px;padding:14px 16px;margin-bottom:16px;font-size:13px;color:#7b5b1d;line-height:1.6}
  .unlock-warn a.button{display:inline-block;margin-top:8px;padding:6px 14px;font-size:12px;background:#f5c518;border:1px solid #e0b10f;color:#2d1c00;border-radius:8px;text-decoration:none;font-weight:600}

  /* SLA note */
  .unlock-sla{text-align:center;font-size:12px;color:#9ab2cc;margin-top:14px;line-height:1.5}

  /* Rejected reason box */
  .unlock-rejection-box{background:#fff5f5;border:1px solid #fca5a5;border-radius:10px;padding:14px 16px;margin-bottom:16px;font-size:13px;color:#991b1b}
  .unlock-rejection-box strong{display:block;margin-bottom:4px}

  @media(max-width:480px){
    .unlock-page{padding:0 8px 40px}
    .unlock-form-card{padding:14px}
    .unlock-state{padding:18px 14px}
    .unlock-state-icon{font-size:32px}
  }
</style>

<div class="unlock-page">
  <a href="{{ route($configureRoute) }}" class="unlock-page-back">← Back to Configure</a>

  {{-- Template preview strip --}}
  <div class="unlock-preview-card">
    <div class="unlock-preview-thumb">
      <div class="utp-bar accent"></div>
      <div class="utp-row"><div class="utp-box"></div><div class="utp-box"></div><div class="utp-box"></div></div>
      <div class="utp-row"><div class="utp-box"></div><div class="utp-box"></div></div>
    </div>
    <div class="unlock-preview-info">
      <h3>Astro Dynamic Template</h3>
      <p>Cosmic premium dark landing page with luxury hero, dramatic cards, and a high-visual brand feel.</p>
    </div>
  </div>

  {{-- ═══ STATE: APPROVED ═══ --}}
  @if($astroUnlocked)
    <div class="unlock-state approved">
      <div class="unlock-state-icon">🎉</div>
      <h2>Template Unlocked!</h2>
      <p>Your Astro Dynamic template has been approved and is ready to use. Go to Configure to set it as your active template.</p>
      <div class="state-meta">✓ Approved &amp; Active</div>
    </div>
    <a href="{{ route($configureRoute) }}" class="uf-submit-btn" style="display:block;text-decoration:none;text-align:center;margin-top:10px">Go to Configure →</a>

  {{-- ═══ STATE: PENDING ═══ --}}
  @elseif($hasPendingAstroUnlockRequest)
    <div class="unlock-state pending">
      <div class="unlock-state-icon">⏳</div>
      <h2>Request Under Review</h2>
      <p>
        Your unlock request was submitted
        @if($latestAstroUnlockRequest->created_at)
          {{ $latestAstroUnlockRequest->created_at->diffForHumans() }}
        @endif
        and is currently being reviewed by admin. Please check back after {{ $unlockSlaHours }} hours.
      </p>
      <div class="state-meta">⏳ Pending Admin Review</div>
    </div>

    <div class="unlock-form-card" style="text-align:center;padding:24px 20px">
      <div style="font-size:13px;color:#4a6580;line-height:1.7">
        <strong style="display:block;margin-bottom:6px;color:#2f4e74">What happens next?</strong>
        Admin will review your payment proof and approve within <strong>{{ $unlockSlaHours }} hours</strong>.<br>
        Once approved, the Astro Dynamic template will be available in your Configure page.
      </div>
      <a href="{{ route($configureRoute) }}" class="uf-submit-btn" style="display:block;text-decoration:none;margin-top:18px">Back to Configure</a>
    </div>

  @else
    {{-- ═══ STATE: REJECTED — show info + allow resubmit ═══ --}}
    @if($isRejected && $latestAstroUnlockRequest)
      <div class="unlock-state rejected">
        <div class="unlock-state-icon">✕</div>
        <h2>Request Rejected</h2>
        <p>
          Your previous unlock request was rejected
          @if($latestAstroUnlockRequest->created_at)
            (submitted {{ $latestAstroUnlockRequest->created_at->diffForHumans() }})
          @endif
          . You can submit a new request with correct payment proof below.
        </p>
        <div class="state-meta">✕ Rejected</div>
      </div>
      @if($latestAstroUnlockRequest->admin_note)
        <div class="unlock-rejection-box">
          <strong>Admin note:</strong>
          {{ $latestAstroUnlockRequest->admin_note }}
        </div>
      @endif
    @endif

    {{-- ═══ STATE: NO SUBSCRIPTION WARNING ═══ --}}
    @if(!$hasActiveSubscription)
      <div class="unlock-warn">
        <strong>Subscription required first.</strong><br>
        Activate a <strong>Yearly Base Plan (₹{{ number_format($yearlyBasePrice, 0) }})</strong> before unlocking Astro Dynamic Template.
        <br><a href="{{ route('subscriptions.new') }}" class="button">Activate Subscription →</a>
      </div>
    @endif

    {{-- Price badge --}}
    <div class="unlock-price-badge">
      <div class="price">₹{{ number_format($astroUnlockPrice, 0) }}</div>
      <div class="price-note">One-time unlock fee · lifetime access to Astro Dynamic</div>
    </div>

    {{-- QR payment section --}}
    @php $hasPaymentCode = (string) $paymentQrUrl !== '' || (string) $paymentBarcodeUrl !== ''; @endphp
    <div class="unlock-qr-section">
      @if($hasPaymentCode)
        <img
          src="{{ $paymentQrUrl ?: $paymentBarcodeUrl }}"
          alt="Payment QR"
          onerror="this.style.display='none';document.getElementById('qr-unavail').style.display='block'">
        <div id="qr-unavail" style="display:none" class="qr-unavail">Payment QR not available right now. Please contact admin.</div>
      @else
        <div class="qr-unavail">Payment QR not available right now. Please contact admin.</div>
      @endif
      <div class="qr-label" style="margin-top:8px">Scan &amp; pay</div>
      <div class="qr-amount">₹{{ number_format($astroUnlockPrice, 0) }}</div>
      <div style="font-size:11px;color:#7a95b0;margin-top:4px">Then fill below form with transaction proof</div>
    </div>

    {{-- Submission form --}}
    <div class="unlock-form-card">
      <h3>Submit Payment Proof</h3>

      @if(session('notice'))
        <div style="background:#e6f9ee;border:1px solid #6ee7a0;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#065f46">
          {{ session('notice') }}
        </div>
      @endif

      @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #f87171;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#991b1b">
          @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
      @endif

      <form action="{{ route('subscriptions.template_unlock_request') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="template_key" value="astro_dynamic">
        <input type="hidden" name="amount" value="{{ number_format($astroUnlockPrice, 2, '.', '') }}">

        <div class="uf-row">
          <label for="payment_transaction_id">Transaction ID <span style="color:#9ab2cc;font-weight:400">(optional)</span></label>
          <input type="text" id="payment_transaction_id" name="payment_transaction_id" value="{{ old('payment_transaction_id') }}" placeholder="UPI / bank transaction reference">
        </div>

        <div class="uf-divider"><span>or</span></div>

        <div class="uf-row">
          <label for="payment_screenshot">Payment Screenshot <span style="color:#9ab2cc;font-weight:400">(optional)</span></label>
          <input type="file" id="payment_screenshot" name="payment_screenshot" accept="image/*">
        </div>

        <div class="uf-note" style="margin-bottom:14px">⚠ At least one of transaction ID or screenshot is required.</div>

        <div class="uf-row">
          <label for="comment">Additional Comment <span style="color:#9ab2cc;font-weight:400">(optional)</span></label>
          <textarea id="comment" name="comment" placeholder="Any note for admin…">{{ old('comment') }}</textarea>
        </div>

        <button
          type="submit"
          class="uf-submit-btn"
          {{ !$hasActiveSubscription ? 'disabled' : '' }}>
          Submit Unlock Request
        </button>
      </form>

      <div class="unlock-sla">🕐 Admin reviews within <strong>{{ $unlockSlaHours }} hours</strong>. You will see the status here once approved.</div>
    </div>
  @endif
</div>
@endsection
