@extends('layouts.app')

@section('content')
@include('admin.partials.ui')
<style>
/* ── Layout ── */
.redemp-wrap { max-width:1260px; margin:0 auto; padding:20px; }
.redemp-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.redemp-header h1 { margin:0; font-size:1.55rem; font-weight:800; color:#1e293b; }

/* ── Buttons ── */
.btn { display:inline-flex; align-items:center; gap:5px; padding:8px 16px; border-radius:8px; font-size:.875rem; font-weight:600; border:none; cursor:pointer; text-decoration:none; transition:all .15s; }
.btn-back   { background:#f1f5f9; color:#374151; border:1.5px solid #e2e8f0; }
.btn-back:hover { border-color:#6366f1; color:#6366f1; }

/* ── Flash ── */
.flash { border-radius:9px; padding:11px 16px; margin-bottom:18px; font-size:.88rem; font-weight:600; display:flex; align-items:center; gap:8px; }
.flash-ok  { background:#d1fae5; color:#065f46; }
.flash-err { background:#fee2e2; color:#991b1b; }

/* ── Stat Cards ── */
.stat-strip { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:14px; margin-bottom:26px; }
.stat-card { background:#fff; border-radius:13px; padding:16px 18px; border:1.5px solid #f1f5f9; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.stat-card .stat-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:5px; }
.stat-card .stat-num   { font-size:1.8rem; font-weight:800; line-height:1; }
.stat-card.pending    .stat-num { color:#f59e0b; }
.stat-card.approved   .stat-num { color:#3b82f6; }
.stat-card.on_the_way .stat-num { color:#8b5cf6; }
.stat-card.delivered  .stat-num { color:#10b981; }
.stat-card.rejected   .stat-num { color:#ef4444; }

/* ── Filters ── */
.filter-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; background:#fff; border-radius:11px; padding:14px 18px; border:1.5px solid #f1f5f9; box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:22px; }
.filter-chip { padding:6px 16px; border-radius:20px; font-size:.82rem; font-weight:600; border:1.5px solid #e2e8f0; background:#f8fafc; color:#374151; cursor:pointer; text-decoration:none; transition:all .15s; }
.filter-chip:hover { border-color:#6366f1; color:#6366f1; }
.filter-chip.active { border-color:transparent; color:#fff; }
.filter-chip.fc-all       .active-bg, .filter-chip.fc-all.active       { background:#6366f1; }
.filter-chip.fc-pending   .active-bg, .filter-chip.fc-pending.active   { background:#f59e0b; }
.filter-chip.fc-approved  .active-bg, .filter-chip.fc-approved.active  { background:#3b82f6; }
.filter-chip.fc-on_the_way .active-bg, .filter-chip.fc-on_the_way.active { background:#8b5cf6; }
.filter-chip.fc-delivered .active-bg, .filter-chip.fc-delivered.active { background:#10b981; }
.filter-chip.fc-rejected  .active-bg, .filter-chip.fc-rejected.active  { background:#ef4444; }
.filter-chip.active { border-color:transparent; }
.filter-chip.fc-all.active       { background:#6366f1; }
.filter-chip.fc-pending.active   { background:#f59e0b; }
.filter-chip.fc-approved.active  { background:#3b82f6; }
.filter-chip.fc-on_the_way.active { background:#8b5cf6; }
.filter-chip.fc-delivered.active { background:#10b981; }
.filter-chip.fc-rejected.active  { background:#ef4444; }
.filter-search { display:flex; gap:8px; margin-left:auto; }
.filter-search input { padding:7px 13px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; min-width:200px; }
.filter-search input:focus { border-color:#6366f1; outline:none; }

/* ── Order Cards ── */
.order-list { display:flex; flex-direction:column; gap:16px; }
.order-card { background:#fff; border-radius:14px; border:1.5px solid #f1f5f9; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden; transition:box-shadow .2s; }
.order-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }

.order-head { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #f1f5f9; flex-wrap:wrap; gap:8px; }
.order-id { font-size:.75rem; font-weight:700; color:#94a3b8; letter-spacing:.05em; }
.order-date { font-size:.78rem; color:#94a3b8; }

/* Status badge */
.status-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:.78rem; font-weight:700; }
.sb-pending    { background:#fef3c7; color:#92400e; }
.sb-approved   { background:#dbeafe; color:#1e40af; }
.sb-on_the_way { background:#ede9fe; color:#4c1d95; }
.sb-delivered  { background:#d1fae5; color:#065f46; }
.sb-rejected   { background:#fee2e2; color:#991b1b; }

.order-body { display:flex; align-items:flex-start; gap:18px; padding:16px 20px; flex-wrap:wrap; }
.order-thumb { width:72px; height:56px; object-fit:cover; border-radius:9px; border:1.5px solid #e5e7eb; flex-shrink:0; }
.order-thumb-placeholder { width:72px; height:56px; border-radius:9px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:1.8rem; flex-shrink:0; }
.order-details { flex:1; min-width:200px; }
.order-details .offer-title { font-weight:700; color:#1e293b; font-size:.97rem; margin-bottom:4px; }
.order-details .offer-store { font-size:.82rem; color:#6b7280; }
.order-points { display:inline-flex; align-items:center; gap:4px; background:#fef3c7; color:#92400e; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:700; margin-top:6px; }

.order-user { display:flex; align-items:center; gap:10px; min-width:180px; }
.user-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#8b5cf6); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:.95rem; flex-shrink:0; }
.user-info .user-name { font-weight:600; color:#1e293b; font-size:.88rem; }
.user-info .user-email { font-size:.76rem; color:#94a3b8; }

/* Progress stepper */
.stepper { display:flex; align-items:center; gap:0; margin:14px 20px 0; padding-bottom:16px; overflow-x:auto; }
.step { display:flex; align-items:center; flex:1; min-width:80px; }
.step-dot { width:30px; height:30px; border-radius:50%; border:2.5px solid #e2e8f0; background:#fff; display:flex; align-items:center; justify-content:center; font-size:.85rem; flex-shrink:0; position:relative; z-index:1; }
.step-dot.done  { background:#10b981; border-color:#10b981; color:#fff; }
.step-dot.active{ background:#6366f1; border-color:#6366f1; color:#fff; box-shadow:0 0 0 4px rgba(99,102,241,.2); }
.step-dot.skip  { background:#fee2e2; border-color:#ef4444; color:#ef4444; }
.step-line { flex:1; height:3px; background:#e2e8f0; margin:0 -1px; }
.step-line.done  { background:#10b981; }
.step-line.active{ background:linear-gradient(90deg,#10b981,#e2e8f0); }
.step-label { font-size:.7rem; font-weight:600; color:#94a3b8; text-align:center; margin-top:4px; white-space:nowrap; }
.step-label.active { color:#6366f1; }
.step-label.done   { color:#10b981; }
.step-label.skip   { color:#ef4444; }
.step-wrap { display:flex; flex-direction:column; align-items:center; }
.step-with-label { display:flex; flex-direction:column; align-items:center; }

/* Status Actions */
.order-foot { display:flex; align-items:center; gap:10px; padding:12px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; flex-wrap:wrap; }
.status-select { padding:7px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; background:#fff; color:#374151; font-weight:600; cursor:pointer; }
.status-select:focus { border-color:#6366f1; outline:none; }
.btn-update { background:#6366f1; color:#fff; padding:7px 18px; border-radius:8px; font-size:.85rem; font-weight:700; border:none; cursor:pointer; transition:background .15s; }
.btn-update:hover { background:#4f46e5; }
.notes-input { flex:1; min-width:160px; padding:7px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.82rem; color:#374151; }
.notes-input:focus { border-color:#6366f1; outline:none; }
.toast-msg { display:none; font-size:.82rem; font-weight:600; color:#10b981; }

/* Delivery address panel */
.addr-panel { background:#f8fafc; border:1.5px solid #e5e7eb; border-radius:10px; padding:14px 16px; margin:0 20px 14px; }
.addr-panel-title { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
.addr-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:10px; }
.addr-field { display:flex; flex-direction:column; gap:3px; }
.addr-field .af-label { font-size:.68rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.05em; }
.addr-field .af-val   { font-size:.85rem; font-weight:600; color:#1e293b; }
.addr-full { grid-column:1/-1; }
.no-addr { font-size:.8rem; color:#94a3b8; font-style:italic; }

/* Empty state */
.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state .empty-icon { font-size:3.5rem; margin-bottom:12px; }
.empty-state h3 { margin:0 0 6px; font-size:1.1rem; color:#64748b; }
.empty-state p { margin:0; font-size:.88rem; }

/* Pagination */
.page-links { margin-top:20px; }
</style>

<div class="redemp-wrap">
  <div class="redemp-header">
    <h1>📦 Offer Redemptions</h1>
    <a href="{{ route('admin.offers.index') }}" class="btn btn-back">← Back to Offers</a>
  </div>

  @if(session('notice'))
    <div class="flash flash-ok">✅ {{ session('notice') }}</div>
  @endif

  {{-- Stats strip --}}
  @php
    $allCount = $counts->sum();
    $statDefs = [
      'pending'    => ['label'=>'Pending',    'emoji'=>'⏳'],
      'approved'   => ['label'=>'Approved',   'emoji'=>'✅'],
      'on_the_way' => ['label'=>'On the Way', 'emoji'=>'🚚'],
      'delivered'  => ['label'=>'Delivered',  'emoji'=>'🎉'],
      'rejected'   => ['label'=>'Rejected',   'emoji'=>'❌'],
    ];
  @endphp
  <div class="stat-strip">
    <div class="stat-card" style="border-color:#e0e7ff;">
      <div class="stat-label">All Orders</div>
      <div class="stat-num" style="color:#6366f1;">{{ $allCount }}</div>
    </div>
    @foreach($statDefs as $key => $def)
      <div class="stat-card {{ $key }}">
        <div class="stat-label">{{ $def['emoji'] }} {{ $def['label'] }}</div>
        <div class="stat-num">{{ $counts[$key] ?? 0 }}</div>
      </div>
    @endforeach
  </div>

  {{-- Filter row --}}
  <div class="filter-row">
    @php
      $chips = ['' => 'All'] + array_combine(array_keys($statDefs), array_column($statDefs, 'label'));
    @endphp
    @foreach($chips as $val => $label)
      <a href="{{ route('admin.offers.redemptions', array_merge(request()->except('status','page'), ['status'=>$val])) }}"
         class="filter-chip fc-{{ $val ?: 'all' }} {{ $statusFilter === $val ? 'active' : '' }}">
        {{ $val === '' ? '🗂 ' : '' }}{{ $label }}
        @if($val !== '' && ($counts[$val] ?? 0) > 0)
          <span style="background:rgba(0,0,0,.12);border-radius:10px;padding:1px 6px;font-size:.7rem;">{{ $counts[$val] }}</span>
        @endif
      </a>
    @endforeach

    <form method="GET" action="{{ route('admin.offers.redemptions') }}" class="filter-search">
      @if($statusFilter)<input type="hidden" name="status" value="{{ $statusFilter }}">@endif
      <input type="text" name="q" value="{{ $q }}" placeholder="Search user or offer...">
      <button type="submit" class="btn" style="background:#6366f1;color:#fff;padding:7px 14px;font-size:.82rem;">Search</button>
      @if($q)<a href="{{ route('admin.offers.redemptions', ['status'=>$statusFilter]) }}" class="btn btn-back btn-sm" style="padding:7px 10px;font-size:.82rem;">✕</a>@endif
    </form>
  </div>

  {{-- Orders list --}}
  @if($redemptions->count())
    <div class="order-list">
      @foreach($redemptions as $r)
        @php
          $steps = ['pending','approved','on_the_way','delivered'];
          $currentIdx = array_search($r->status, $steps);
          $isRejected = $r->status === 'rejected';
        @endphp
        <div class="order-card" id="order-{{ $r->id }}">
          {{-- Head --}}
          <div class="order-head">
            <div>
              <div class="order-id">ORDER #{{ str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</div>
              <div class="order-date">Redeemed {{ $r->redeemed_at?->diffForHumans() ?? '—' }}</div>
            </div>
            <span class="status-badge sb-{{ $r->status }}" id="badge-{{ $r->id }}">
              @php $icons=['pending'=>'⏳','approved'=>'✅','on_the_way'=>'🚚','delivered'=>'🎉','rejected'=>'❌']; @endphp
              {{ $icons[$r->status] ?? '' }} {{ \App\Models\CouponRedemption::STATUS_LABELS[$r->status] ?? $r->status }}
            </span>
          </div>

          {{-- Body --}}
          <div class="order-body">
            {{-- Offer image + details --}}
            <div style="display:flex;align-items:flex-start;gap:14px;flex:2;min-width:220px;">
              @if($r->offer?->photo_path)
                <img src="{{ Storage::disk('public')->url($r->offer->photo_path) }}" class="order-thumb" alt="">
              @else
                <div class="order-thumb-placeholder">🎁</div>
              @endif
              <div class="order-details">
                <div class="offer-title">{{ $r->offer?->title ?? $r->title }}</div>
                <div class="offer-store">
                  @if($r->store) <span>🏪 {{ $r->store }}</span> @endif
                  @if($r->offer?->source_url)
                    <a href="{{ $r->offer->source_url }}" target="_blank" rel="noopener" style="color:#6366f1;font-size:.76rem;margin-left:6px;">View Offer ↗</a>
                  @endif
                </div>
                <span class="order-points">💎 {{ $r->offer?->getRawOriginal('points_required') ?? 200 }} pts spent</span>
                @if($r->admin_notes)
                  <div style="margin-top:6px;background:#f8fafc;border-radius:7px;padding:6px 10px;font-size:.8rem;color:#6b7280;border-left:3px solid #6366f1;">
                    <strong>Admin note:</strong> {{ $r->admin_notes }}
                  </div>
                @endif
              </div>
            </div>

            {{-- User info --}}
            <div class="order-user">
              @php $userName = trim(($r->user?->first_name ?? '') . ' ' . ($r->user?->last_name ?? '')) ?: 'Unknown'; @endphp
              <div class="user-avatar">{{ strtoupper(substr($userName, 0, 1)) }}</div>
              <div class="user-info">
                <div class="user-name">{{ $userName }}</div>
                <div class="user-email">{{ $r->user?->email ?? '—' }}</div>
                @if($r->processed_at)
                  <div style="font-size:.73rem;color:#94a3b8;margin-top:2px;">Updated {{ $r->processed_at->diffForHumans() }}</div>
                @endif
              </div>
            </div>
          </div>

          {{-- Delivery address (product orders) --}}
          @if($r->offer?->offer_category === 'product')
            <div class="addr-panel">
              <div class="addr-panel-title">📍 Delivery Address</div>
              @if($r->hasDeliveryAddress())
                <div class="addr-grid">
                  @if($r->delivery_name)
                    <div class="addr-field">
                      <span class="af-label">Name</span>
                      <span class="af-val">{{ $r->delivery_name }}</span>
                    </div>
                  @endif
                  @if($r->delivery_phone)
                    <div class="addr-field">
                      <span class="af-label">Phone</span>
                      <span class="af-val">{{ $r->delivery_phone }}</span>
                    </div>
                  @endif
                  @if($r->delivery_pincode)
                    <div class="addr-field">
                      <span class="af-label">PIN Code</span>
                      <span class="af-val">{{ $r->delivery_pincode }}</span>
                    </div>
                  @endif
                  <div class="addr-field addr-full">
                    <span class="af-label">Full Address</span>
                    <span class="af-val">{{ $r->delivery_address_string }}</span>
                  </div>
                </div>
              @else
                <span class="no-addr">No delivery address provided</span>
              @endif
            </div>
          @endif

          {{-- Progress stepper (only for non-rejected) --}}
          @if(!$isRejected)
            <div class="stepper">
              @foreach($steps as $idx => $step)
                @php
                  if ($currentIdx === false) { $dotClass = ''; $lineClass = ''; $lblClass = ''; }
                  elseif ($idx < $currentIdx) { $dotClass='done'; $lineClass='done'; $lblClass='done'; }
                  elseif ($idx === $currentIdx) { $dotClass='active'; $lineClass='active'; $lblClass='active'; }
                  else { $dotClass=''; $lineClass=''; $lblClass=''; }
                  $stepIcons=['pending'=>'⏳','approved'=>'✅','on_the_way'=>'🚚','delivered'=>'🎉'];
                @endphp
                <div class="step">
                  <div class="step-with-label">
                    <div class="step-dot {{ $dotClass }}">
                      @if($dotClass === 'done') ✓
                      @elseif($dotClass === 'active') {{ $stepIcons[$step] }}
                      @else {{ $stepIcons[$step] }}
                      @endif
                    </div>
                    <div class="step-label {{ $lblClass }}">
                      {{ \App\Models\CouponRedemption::STATUS_LABELS[$step] }}
                    </div>
                  </div>
                  @if($idx < count($steps)-1)
                    <div class="step-line {{ $lineClass }}"></div>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <div style="padding:10px 20px 14px;display:flex;align-items:center;gap:8px;">
              <span style="background:#fee2e2;color:#991b1b;padding:4px 12px;border-radius:20px;font-size:.8rem;font-weight:700;">❌ This order was rejected</span>
            </div>
          @endif

          {{-- Footer: status update controls --}}
          @if($r->status !== 'delivered')
            <div class="order-foot">
              <form class="status-form" data-id="{{ $r->id }}"
                action="{{ route('admin.offers.redemptions.status', $r) }}"
                method="POST" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;width:100%;">
                @csrf @method('PATCH')
                <select name="status" class="status-select" onchange="highlightStatus(this)">
                  @foreach(\App\Models\CouponRedemption::STATUS_LABELS as $val => $lbl)
                    <option value="{{ $val }}" {{ $r->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                  @endforeach
                </select>
                <input type="text" name="admin_notes" class="notes-input"
                  placeholder="Add a note (optional)..."
                  value="{{ $r->admin_notes }}">
                <button type="submit" class="btn-update">Update Status</button>
                <span class="toast-msg" id="toast-{{ $r->id }}">✅ Updated!</span>
              </form>
            </div>
          @else
            <div class="order-foot">
              <span style="color:#10b981;font-weight:700;font-size:.85rem;">🎉 Order fulfilled successfully</span>
            </div>
          @endif
        </div>
      @endforeach
    </div>

    <div class="page-links">{{ $redemptions->links() }}</div>
  @else
    <div class="empty-state">
      <div class="empty-icon">📭</div>
      <h3>No redemptions found</h3>
      <p>{{ $statusFilter ? 'No orders with status "'.(\App\Models\CouponRedemption::STATUS_LABELS[$statusFilter] ?? $statusFilter).'".' : 'Users have not redeemed any offers yet.' }}</p>
    </div>
  @endif
</div>

<script>
// AJAX status update
document.querySelectorAll('.status-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const id   = this.dataset.id;
    const data = new FormData(this);
    const btn  = this.querySelector('.btn-update');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    fetch(this.action, {
      method: 'POST',
      body: data,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        // Update badge
        const badge = document.getElementById('badge-' + id);
        if (badge) {
          const icons = {pending:'⏳',approved:'✅',on_the_way:'🚚',delivered:'🎉',rejected:'❌'};
          badge.textContent = (icons[res.status] || '') + ' ' + res.label;
          badge.className = 'status-badge sb-' + res.status;
        }
        // Show toast
        const toast = document.getElementById('toast-' + id);
        if (toast) { toast.style.display='inline'; setTimeout(()=>toast.style.display='none', 3000); }
        // If delivered or rejected, reload to refresh stepper
        if (res.status === 'delivered' || res.status === 'rejected') {
          setTimeout(() => location.reload(), 800);
        }
      }
    })
    .catch(() => alert('Error updating status. Please try again.'))
    .finally(() => { btn.disabled = false; btn.textContent = 'Update Status'; });
  });
});

function highlightStatus(sel) {
  const colors = {pending:'#fef3c7',approved:'#dbeafe',on_the_way:'#ede9fe',delivered:'#d1fae5',rejected:'#fee2e2'};
  sel.style.background = colors[sel.value] || '#fff';
}

// Init colors on load
document.querySelectorAll('.status-select').forEach(highlightStatus);
</script>
@endsection
