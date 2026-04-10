@extends('layouts.app')

@section('title', 'My Orders & Redemptions · CityPulse')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box}
#myOrders{
  --r:#e11d7a;--b:#6366f1;--b2:#4f46e5;--g:#10b981;--y:#f59e0b;
  --dark:#0f0a1e;--dark2:#1a1033;--dark3:#241847;--mid:#4b3b7c;
  --muted:#9f8fc0;--border:rgba(255,255,255,.12);--surface:rgba(255,255,255,.06);
  background:var(--dark);color:#fff;font-family:'Inter',sans-serif;
  margin:-22px -22px -22px -22px;min-height:100vh;padding-bottom:5rem;position:relative;
}
#myOrders *{box-sizing:border-box}
#myOrders a{text-decoration:none;color:inherit}

/* animated bg */
#myOrders .bg-orbs{position:absolute;inset:0;pointer-events:none;z-index:0;overflow:hidden}
#myOrders .orb{position:absolute;border-radius:50%;filter:blur(90px);opacity:.25;animation:orb-d 22s ease-in-out infinite}
#myOrders .orb-1{width:500px;height:500px;background:var(--r);top:-150px;left:-150px}
#myOrders .orb-2{width:450px;height:450px;background:var(--b);bottom:-100px;right:-100px;animation-delay:8s}
@keyframes orb-d{0%,100%{transform:translate(0,0)}50%{transform:translate(40px,50px)}}

/* hero */
#myOrders .hero{position:relative;z-index:1;padding:2.5rem 1.5rem 1.5rem;text-align:center}
#myOrders .hero-back{display:inline-flex;align-items:center;gap:.4rem;color:var(--muted);font-size:.8rem;font-weight:600;padding:.4rem .9rem;border:1px solid var(--border);border-radius:100px;margin-bottom:1.2rem;transition:all .2s}
#myOrders .hero-back:hover{border-color:rgba(255,255,255,.3);color:#fff}
#myOrders .hero-title{font-family:'Syne',sans-serif;font-size:clamp(1.8rem,5vw,3rem);font-weight:800;letter-spacing:-1px;background:linear-gradient(135deg,#fff,#f9a8d4,var(--y));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:.5rem}
#myOrders .hero-sub{font-size:.88rem;color:var(--muted);margin-bottom:1.5rem}
#myOrders .pts-pill{display:inline-flex;align-items:center;gap:.6rem;background:rgba(225,29,122,.18);border:1px solid rgba(225,29,122,.35);padding:.5rem 1.2rem;border-radius:100px;font-size:.82rem;font-weight:700;margin-bottom:1.5rem}

/* filter tabs */
#myOrders .tabs{display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap;margin-bottom:.5rem;z-index:1;position:relative}
#myOrders .tab{padding:.5rem 1.2rem;border-radius:100px;border:1.5px solid var(--border);background:var(--surface);color:var(--muted);font-size:.8rem;font-weight:600;cursor:pointer;transition:all .2s}
#myOrders .tab:hover{border-color:rgba(255,255,255,.3);color:#fff}
#myOrders .tab.on{background:linear-gradient(135deg,var(--r),var(--b2));border-color:transparent;color:#fff;box-shadow:0 4px 14px rgba(225,29,122,.3)}
#myOrders .tab .cnt{font-size:.68rem;background:rgba(255,255,255,.18);padding:1px 6px;border-radius:100px;font-weight:700;margin-left:3px}

/* wrap */
#myOrders .wrap{max-width:860px;margin:0 auto;padding:0 1.2rem;position:relative;z-index:1}

/* order card */
#myOrders .order-card{background:var(--dark2);border:1.5px solid var(--border);border-radius:20px;overflow:hidden;margin-bottom:1.2rem;transition:border-color .2s}
#myOrders .order-card:hover{border-color:rgba(99,102,241,.4)}

/* card head */
#myOrders .card-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:8px}
#myOrders .order-num{font-size:.72rem;font-weight:700;color:var(--muted);letter-spacing:.05em}
#myOrders .order-date{font-size:.75rem;color:var(--muted)}

/* status badges */
#myOrders .sb{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:100px;font-size:.75rem;font-weight:700}
#myOrders .sb-pending    {background:rgba(245,158,11,.18);color:#fcd34d;border:1px solid rgba(245,158,11,.3)}
#myOrders .sb-approved   {background:rgba(99,102,241,.18);color:#a5b4fc;border:1px solid rgba(99,102,241,.3)}
#myOrders .sb-on_the_way {background:rgba(139,92,246,.18);color:#c4b5fd;border:1px solid rgba(139,92,246,.3)}
#myOrders .sb-delivered  {background:rgba(16,185,129,.18);color:#6ee7b7;border:1px solid rgba(16,185,129,.3)}
#myOrders .sb-rejected   {background:rgba(239,68,68,.18);color:#fca5a5;border:1px solid rgba(239,68,68,.3)}
#myOrders .sb-coupon     {background:rgba(245,158,11,.12);color:#fcd34d;border:1px solid rgba(245,158,11,.25)}

/* card body */
#myOrders .card-body{display:flex;align-items:flex-start;gap:14px;padding:16px 18px;flex-wrap:wrap}
#myOrders .offer-thumb{width:70px;height:56px;object-fit:cover;border-radius:12px;border:1.5px solid var(--border);flex-shrink:0}
#myOrders .offer-thumb-ph{width:70px;height:56px;border-radius:12px;background:linear-gradient(135deg,var(--dark3),var(--mid));display:flex;align-items:center;justify-content:center;font-size:1.8rem;flex-shrink:0}
#myOrders .offer-info{flex:1;min-width:160px}
#myOrders .offer-title{font-weight:700;font-size:.95rem;margin-bottom:4px}
#myOrders .offer-store{font-size:.78rem;color:var(--muted)}
#myOrders .pts-spent{display:inline-flex;align-items:center;gap:4px;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);padding:3px 10px;border-radius:100px;font-size:.73rem;font-weight:700;color:var(--y);margin-top:6px}

/* stepper (product tracking) */
#myOrders .stepper{display:flex;align-items:flex-start;padding:14px 18px 18px;overflow-x:auto;gap:0}
#myOrders .step{display:flex;align-items:flex-start;flex:1;min-width:70px}
#myOrders .step-inner{display:flex;flex-direction:column;align-items:center;gap:5px}
#myOrders .dot{width:32px;height:32px;border-radius:50%;border:2.5px solid rgba(255,255,255,.15);background:var(--dark3);display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;transition:all .3s}
#myOrders .dot.done  {background:var(--g);border-color:var(--g);box-shadow:0 0 12px rgba(16,185,129,.4)}
#myOrders .dot.active{background:var(--b);border-color:var(--b);box-shadow:0 0 14px rgba(99,102,241,.5);animation:pulse-ring .8s ease-in-out infinite alternate}
@keyframes pulse-ring{from{box-shadow:0 0 8px rgba(99,102,241,.4)}to{box-shadow:0 0 18px rgba(99,102,241,.7)}}
#myOrders .dot.skip  {background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.4)}
#myOrders .step-label{font-size:.65rem;font-weight:600;color:var(--muted);text-align:center;white-space:nowrap;transition:color .3s}
#myOrders .step-label.done   {color:var(--g)}
#myOrders .step-label.active {color:#a5b4fc}
#myOrders .step-label.skip   {color:#f87171}
#myOrders .step-line{flex:1;height:3px;background:rgba(255,255,255,.1);margin-top:14px;transition:background .3s}
#myOrders .step-line.done{background:var(--g)}

/* admin notes */
#myOrders .admin-note{margin:0 18px 14px;background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.25);border-radius:10px;padding:8px 12px;font-size:.78rem;color:#a5b4fc;display:flex;align-items:flex-start;gap:6px}

/* coupon code box */
#myOrders .coupon-box{margin:0 18px 16px}
#myOrders .coupon-code{background:rgba(16,185,129,.1);border:1.5px dashed rgba(16,185,129,.4);border-radius:12px;padding:10px 16px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
#myOrders .coupon-val{font-size:1.1rem;font-weight:800;color:#6ee7b7;letter-spacing:2px}
#myOrders .copy-btn{display:flex;align-items:center;gap:4px;padding:5px 14px;border-radius:100px;background:rgba(16,185,129,.2);border:1px solid rgba(16,185,129,.35);color:#6ee7b7;font-size:.75rem;font-weight:700;cursor:pointer;transition:all .2s}
#myOrders .copy-btn:hover{background:rgba(16,185,129,.35)}
#myOrders .copied-tip{font-size:.72rem;color:var(--g);margin-top:4px;display:none}

/* rejected card */
#myOrders .rejected-note{margin:0 18px 14px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:10px;padding:10px 14px;font-size:.8rem;color:#fca5a5}

/* delivery address */
#myOrders .addr-box{margin:0 18px 14px;background:rgba(255,255,255,.04);border:1.5px solid rgba(255,255,255,.1);border-radius:12px;overflow:hidden}
#myOrders .addr-head{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.08);cursor:pointer;user-select:none}
#myOrders .addr-head span{font-size:.75rem;font-weight:700;color:#a5b4fc;display:flex;align-items:center;gap:5px}
#myOrders .addr-toggle{font-size:.7rem;color:var(--muted);transition:transform .2s}
#myOrders .addr-body{padding:12px 14px;display:none}
#myOrders .addr-body.open{display:block}
#myOrders .addr-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:8px}
#myOrders .af{display:flex;flex-direction:column;gap:2px}
#myOrders .af-label{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)}
#myOrders .af-val{font-size:.82rem;font-weight:600;color:rgba(255,255,255,.85)}
#myOrders .af-full{grid-column:1/-1}

/* empty */
#myOrders .empty{text-align:center;padding:4rem 1.5rem;color:var(--muted)}
#myOrders .empty .ico{font-size:3.5rem;margin-bottom:1rem}
#myOrders .empty h3{font-size:1.1rem;color:rgba(255,255,255,.7);margin-bottom:.5rem}
#myOrders .empty p{font-size:.85rem;line-height:1.6}
#myOrders .btn-go{display:inline-flex;align-items:center;gap:.4rem;margin-top:1.2rem;padding:.65rem 1.6rem;border-radius:100px;background:linear-gradient(135deg,var(--r),var(--b2));color:#fff;font-size:.85rem;font-weight:700;border:none;cursor:pointer;text-decoration:none}

/* toast */
#moToast{position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%) translateY(60px);z-index:9999;background:#fff;color:var(--dark);padding:.65rem 1.4rem;border-radius:100px;font-size:.82rem;font-weight:700;box-shadow:0 8px 32px rgba(0,0,0,.3);opacity:0;transition:all .3s;white-space:nowrap;display:flex;align-items:center;gap:.5rem}
#moToast.show{transform:translateX(-50%) translateY(0);opacity:1}
#moToast.ok{background:var(--g);color:#fff}
</style>

<div id="myOrders">
  <div class="bg-orbs"><div class="orb orb-1"></div><div class="orb orb-2"></div></div>
  <div id="moToast"></div>

  <div class="hero">
    <a href="{{ route('offers') }}" class="hero-back">← Back to Offers</a>
    <h1 class="hero-title">📦 My Orders &amp; Redemptions</h1>
    <p class="hero-sub">Track your redeemed offers and product deliveries</p>
    @auth
      <div class="pts-pill">💎 <strong>{{ number_format(Auth::user()->ruby_points ?? 0) }}</strong> Ruby Points</div>
    @endauth

    <div class="tabs">
      <div class="tab on" data-filter="all">🗂 All <span class="cnt">{{ $redemptions->total() }}</span></div>
      <div class="tab" data-filter="product">📦 Products <span class="cnt">{{ $productCount }}</span></div>
      <div class="tab" data-filter="coupon">🎟 Coupons <span class="cnt">{{ $couponCount }}</span></div>
      <div class="tab" data-filter="pending">⏳ Pending <span class="cnt">{{ $pendingCount }}</span></div>
      <div class="tab" data-filter="delivered">🎉 Delivered <span class="cnt">{{ $deliveredCount }}</span></div>
    </div>
  </div>

  <div class="wrap">
    @if($redemptions->count())
      @foreach($redemptions as $r)
        @php
          $isProduct = $r->offer && $r->offer->offer_category === 'product';
          $steps     = ['pending','approved','on_the_way','delivered'];
          $stepIcons = ['pending'=>'⏳','approved'=>'✅','on_the_way'=>'🚚','delivered'=>'🎉'];
          $stepLabels= ['pending'=>'Ordered','approved'=>'Confirmed','on_the_way'=>'Shipped','delivered'=>'Delivered'];
          $curIdx    = array_search($r->status, $steps);
          $isRejected= $r->status === 'rejected';
          $filterKey = $isProduct ? 'product' : 'coupon';
          $statusKey = in_array($r->status, ['pending','approved','on_the_way']) ? 'pending' : $r->status;
        @endphp
        <div class="order-card" data-filter="{{ $filterKey }}" data-status="{{ $statusKey }}">

          {{-- Head --}}
          <div class="card-head">
            <div>
              <div class="order-num">ORDER #{{ str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</div>
              <div class="order-date">{{ $r->redeemed_at?->format('d M Y, h:i A') ?? '—' }}</div>
            </div>
            @if($isProduct)
              @if($isRejected)
                <span class="sb sb-rejected">❌ Rejected</span>
              @else
                <span class="sb sb-{{ $r->status }}">
                  {{ $stepIcons[$r->status] ?? '' }} {{ \App\Models\CouponRedemption::STATUS_LABELS[$r->status] ?? ucfirst($r->status) }}
                </span>
              @endif
            @else
              <span class="sb sb-coupon">🎟 Coupon Redeemed</span>
            @endif
          </div>

          {{-- Body --}}
          <div class="card-body">
            @if($r->offer?->photo_path)
              <img src="{{ Storage::disk('public')->url($r->offer->photo_path) }}" class="offer-thumb" alt="">
            @else
              <div class="offer-thumb-ph">{{ $isProduct ? '📦' : '🎟' }}</div>
            @endif
            <div class="offer-info">
              <div class="offer-title">{{ $r->offer?->title ?? $r->title }}</div>
              <div class="offer-store">
                @if($r->store) 🏪 {{ $r->store }} @endif
                @if($r->offer?->source_url)
                  &nbsp;·&nbsp;<a href="{{ $r->offer->source_url }}" target="_blank" rel="noopener" style="color:#a5b4fc;font-size:.75rem;">View offer ↗</a>
                @endif
              </div>
              <div class="pts-spent">💎 {{ $r->offer?->getRawOriginal('points_required') ?? 200 }} pts spent</div>
            </div>
          </div>

          {{-- Product: stepper --}}
          @if($isProduct && !$isRejected)
            <div class="stepper">
              @foreach($steps as $idx => $step)
                @php
                  if ($curIdx === false) { $dc=''; $lc=''; $lnc=''; }
                  elseif ($idx < $curIdx) { $dc='done'; $lc='done'; $lnc='done'; }
                  elseif ($idx === $curIdx) { $dc='active'; $lc='active'; $lnc=''; }
                  else { $dc=''; $lc=''; $lnc=''; }
                @endphp
                <div class="step">
                  <div class="step-inner">
                    <div class="dot {{ $dc }}">
                      @if($dc==='done') ✓
                      @else {{ $stepIcons[$step] }}
                      @endif
                    </div>
                    <div class="step-label {{ $lc }}">{{ $stepLabels[$step] }}</div>
                  </div>
                  @if($idx < count($steps)-1)
                    <div class="step-line {{ $lnc }}"></div>
                  @endif
                </div>
              @endforeach
            </div>
          @endif

          {{-- Delivery address (collapsible) --}}
          @if($isProduct && $r->hasDeliveryAddress())
            <div class="addr-box">
              <div class="addr-head" onclick="toggleAddr({{ $r->id }})">
                <span>📍 Delivery Address</span>
                <span class="addr-toggle" id="addr-arrow-{{ $r->id }}">▼</span>
              </div>
              <div class="addr-body" id="addr-body-{{ $r->id }}">
                <div class="addr-row">
                  @if($r->delivery_name)
                    <div class="af"><div class="af-label">Name</div><div class="af-val">{{ $r->delivery_name }}</div></div>
                  @endif
                  @if($r->delivery_phone)
                    <div class="af"><div class="af-label">Phone</div><div class="af-val">{{ $r->delivery_phone }}</div></div>
                  @endif
                  @if($r->delivery_pincode)
                    <div class="af"><div class="af-label">PIN Code</div><div class="af-val">{{ $r->delivery_pincode }}</div></div>
                  @endif
                  @if($r->delivery_state)
                    <div class="af"><div class="af-label">State</div><div class="af-val">{{ $r->delivery_state }}</div></div>
                  @endif
                  <div class="af af-full"><div class="af-label">Full Address</div><div class="af-val">{{ $r->delivery_address_string }}</div></div>
                </div>
              </div>
            </div>
          @endif

          {{-- Product: rejected --}}
          @if($isProduct && $isRejected)
            <div class="rejected-note">
              ❌ <strong>Order rejected.</strong>
              @if($r->admin_notes) {{ $r->admin_notes }} @else Please contact support for more details. @endif
            </div>
          @endif

          {{-- Product: delivered --}}
          @if($isProduct && $r->status === 'delivered')
            <div class="admin-note" style="background:rgba(16,185,129,.1);border-color:rgba(16,185,129,.3);color:#6ee7b7;">
              🎉 <span>Your order was delivered{{ $r->processed_at ? ' on '.$r->processed_at->format('d M Y') : '' }}!</span>
            </div>
          @endif

          {{-- Admin notes (non-rejected products) --}}
          @if($isProduct && $r->admin_notes && !$isRejected && $r->status !== 'delivered')
            <div class="admin-note">
              📋 <span>{{ $r->admin_notes }}</span>
            </div>
          @endif

          {{-- Coupon: show code --}}
          @if(!$isProduct)
            <div class="coupon-box">
              @php
                $code = null;
                if (str_starts_with($r->coupon_code, 'coupon_') && $r->coupon) {
                    $code = $r->coupon->discount_text;
                }
              @endphp
              @if($code)
                <div class="coupon-code">
                  <span class="coupon-val" id="code-{{ $r->id }}">{{ $code }}</span>
                  <button class="copy-btn" onclick="copyCode('{{ $code }}','{{ $r->id }}')">📋 Copy Code</button>
                </div>
                <div class="copied-tip" id="copied-{{ $r->id }}">✓ Copied to clipboard!</div>
              @else
                <div class="coupon-code">
                  <span style="color:var(--muted);font-size:.82rem;">✓ Offer successfully redeemed</span>
                </div>
              @endif
            </div>
          @endif

        </div>
      @endforeach

      <div style="margin-top:16px;">{{ $redemptions->links() }}</div>
    @else
      <div class="empty">
        <div class="ico">📭</div>
        <h3>No orders yet</h3>
        <p>You haven't redeemed any offers yet.<br>Browse offers and spend your Ruby Points!</p>
        <a href="{{ route('offers') }}" class="btn-go">Browse Offers →</a>
      </div>
    @endif
  </div>
</div>

<script>
// Filter tabs
const cards = document.querySelectorAll('.order-card');
document.querySelectorAll('.tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('on'));
    this.classList.add('on');
    const f = this.dataset.filter;
    cards.forEach(card => {
      if (f === 'all') { card.style.display=''; return; }
      const fk = card.dataset.filter;
      const sk = card.dataset.status;
      card.style.display = (f === fk || f === sk) ? '' : 'none';
    });
  });
});

// Copy coupon code
function copyCode(code, id) {
  navigator.clipboard?.writeText(code).then(() => {
    const tip = document.getElementById('copied-' + id);
    if (tip) { tip.style.display='block'; setTimeout(()=>tip.style.display='none', 2500); }
    moToast('✓ Code copied: ' + code, 'ok');
  });
}

// Toggle address accordion
function toggleAddr(id) {
  const body  = document.getElementById('addr-body-' + id);
  const arrow = document.getElementById('addr-arrow-' + id);
  const open  = body.classList.toggle('open');
  if (arrow) arrow.style.transform = open ? 'rotate(180deg)' : '';
}

// Toast
function moToast(msg, type) {
  const t = document.getElementById('moToast');
  t.textContent = msg;
  t.className = 'show ' + (type || '');
  clearTimeout(t._t);
  t._t = setTimeout(() => t.className = '', 3000);
}
</script>
@endsection
