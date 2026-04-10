@extends('layouts.app')

@section('content')
@include('admin.partials.ui')
<style>
.offers-admin { max-width:1200px; margin:0 auto; padding:20px; }
.offers-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.offers-header h1 { margin:0; font-size:1.6rem; font-weight:700; color:#1e293b; }
.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 18px; border-radius:8px; font-size:.875rem; font-weight:600; border:none; cursor:pointer; text-decoration:none; transition:all .15s; }
.btn-primary { background:#6366f1; color:#fff; }
.btn-primary:hover { background:#4f46e5; }
.btn-danger { background:#ef4444; color:#fff; }
.btn-danger:hover { background:#dc2626; }
.btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; }
.btn-outline:hover { border-color:#6366f1; color:#6366f1; }
.btn-success { background:#10b981; color:#fff; }
.btn-success:hover { background:#059669; }
.btn-sm { padding:5px 12px; font-size:.8rem; }
.badge { display:inline-flex; align-items:center; justify-content:center; min-width:22px; height:22px; padding:0 6px; border-radius:11px; font-size:.72rem; font-weight:700; line-height:1; }
.badge-danger { background:#fee2e2; color:#dc2626; }

/* Card */
.admin-card { background:#fff; border-radius:14px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:24px; margin-bottom:24px; border:1px solid #f1f5f9; }
.card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
.card-header h3 { margin:0; font-size:1.1rem; font-weight:700; color:#1e293b; }

/* Form grid */
.form-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:14px; }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-group label { font-size:.8rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; }
.form-group input, .form-group select, .form-group textarea {
  padding:9px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:.9rem; color:#1e293b;
  transition:border-color .15s; background:#fff; width:100%; box-sizing:border-box;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
  border-color:#6366f1; outline:none; box-shadow:0 0 0 3px rgba(99,102,241,.1);
}
.form-group textarea { resize:vertical; min-height:80px; }
.form-full { grid-column:1/-1; }
.form-actions { display:flex; gap:10px; margin-top:8px; }

/* Photo preview */
.photo-preview { width:80px; height:60px; object-fit:cover; border-radius:8px; border:2px solid #e5e7eb; }
.photo-wrap { display:flex; align-items:center; gap:12px; }

/* Table */
.table-wrap { overflow-x:auto; border-radius:10px; border:1px solid #f1f5f9; }
table.offers-table { width:100%; border-collapse:collapse; font-size:.88rem; }
table.offers-table thead tr { background:#f8fafc; }
table.offers-table th { padding:11px 14px; text-align:left; font-size:.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e5e7eb; white-space:nowrap; }
table.offers-table td { padding:14px; border-bottom:1px solid #f1f5f9; vertical-align:top; }
table.offers-table tbody tr:last-child td { border-bottom:none; }
table.offers-table tbody tr:hover { background:#fafbff; }
.offer-thumb { width:60px; height:46px; object-fit:cover; border-radius:7px; border:1.5px solid #e5e7eb; flex-shrink:0; }
.offer-thumb-placeholder { width:60px; height:46px; border-radius:7px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; }
.offer-cell { display:flex; align-items:flex-start; gap:10px; }
.offer-info strong { display:block; font-weight:600; color:#1e293b; }
.offer-info small { color:#94a3b8; font-size:.78rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; max-width:260px; }
.points-pill { display:inline-flex; align-items:center; gap:4px; background:#fef3c7; color:#92400e; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:700; }
.city-chip { display:inline-block; background:#ede9fe; color:#5b21b6; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600; }

/* Inline edit form */
.inline-edit { display:none; background:#f8fafc; border-radius:10px; padding:16px; border:1.5px solid #e5e7eb; margin-top:8px; }
.inline-edit.open { display:block; }
.inline-edit .form-grid { grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:10px; }
.inline-edit .form-group input, .inline-edit .form-group select, .inline-edit .form-group textarea {
  padding:7px 10px; font-size:.85rem;
}
.action-btns { display:flex; gap:6px; flex-wrap:wrap; }

/* Search bar */
.search-bar { display:flex; gap:8px; }
.search-bar input { padding:8px 14px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:.88rem; min-width:220px; }
.search-bar input:focus { border-color:#6366f1; outline:none; }

/* Type toggle */
.type-toggle { display:flex; gap:0; border-radius:10px; overflow:hidden; border:1.5px solid #e2e8f0; width:fit-content; }
.type-toggle label { display:flex; align-items:center; gap:6px; padding:8px 16px; font-size:.82rem; font-weight:600; cursor:pointer; background:#f8fafc; color:#6b7280; transition:all .15s; }
.type-toggle input[type=radio] { display:none; }
.type-toggle input[type=radio]:checked + label { background:#6366f1; color:#fff; }
.type-toggle label:not(:last-child) { border-right:1.5px solid #e2e8f0; }
.type-badge-coupon  { display:inline-flex; align-items:center; gap:4px; background:#fef3c7; color:#92400e; padding:3px 9px; border-radius:20px; font-size:.73rem; font-weight:700; }
.type-badge-product { display:inline-flex; align-items:center; gap:4px; background:#dbeafe; color:#1e40af; padding:3px 9px; border-radius:20px; font-size:.73rem; font-weight:700; }

/* Redemptions banner */
.redemptions-banner { display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border-radius:12px; padding:16px 22px; margin-bottom:24px; }
.redemptions-banner-left { display:flex; align-items:center; gap:14px; }
.redemptions-banner-left .icon { font-size:2rem; }
.redemptions-banner-left h3 { margin:0; font-size:1rem; font-weight:700; }
.redemptions-banner-left p { margin:2px 0 0; font-size:.83rem; opacity:.85; }

/* Notice / Alert */
.flash-notice { background:#d1fae5; color:#065f46; border-radius:8px; padding:10px 16px; margin-bottom:16px; font-size:.9rem; display:flex; align-items:center; gap:8px; }
.flash-alert  { background:#fee2e2; color:#991b1b; border-radius:8px; padding:10px 16px; margin-bottom:16px; font-size:.9rem; display:flex; align-items:center; gap:8px; }
</style>

<div class="offers-admin">
  <div class="offers-header">
    <h1>Offers Management</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">← Back to Admin</a>
  </div>

  @if(session('notice'))
    <div class="flash-notice">✅ {{ session('notice') }}</div>
  @endif
  @if(session('alert'))
    <div class="flash-alert">⚠️ {{ session('alert') }}</div>
  @endif

  {{-- Redemptions Banner --}}
  <div class="redemptions-banner">
    <div class="redemptions-banner-left">
      <span class="icon">📦</span>
      <div>
        <h3>Offer Redemptions</h3>
        <p>{{ $pendingCount }} pending redemption{{ $pendingCount != 1 ? 's' : '' }} awaiting your review</p>
      </div>
    </div>
    <a href="{{ route('admin.offers.redemptions') }}" class="btn btn-outline" style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.4);">
      Manage Redemptions
      @if($pendingCount > 0)
        <span class="badge badge-danger" style="background:#ef4444;color:#fff;">{{ $pendingCount }}</span>
      @endif
    </a>
  </div>

  {{-- Add New Offer --}}
  <div class="admin-card">
    <div class="card-header">
      <h3>➕ Add New Offer</h3>
      <form method="GET" action="{{ route('admin.offers.index') }}" class="search-bar">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search offers...">
        <button type="submit" class="btn btn-outline btn-sm">Search</button>
        @if($q)<a href="{{ route('admin.offers.index') }}" class="btn btn-outline btn-sm">Clear</a>@endif
      </form>
    </div>

    <form action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Title *</label>
          <input type="text" name="title" required placeholder="Offer title">
        </div>
        <div class="form-group">
          <label>City</label>
          <select name="city_id">
            <option value="">All cities</option>
            @foreach($cities as $city)
              <option value="{{ $city->id }}">{{ $city->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Link Shop / Service</label>
          <select name="shop_id">
            <option value="">No linked page</option>
            @foreach($shops as $shop)
              <option value="{{ $shop->id }}">{{ $shop->name }}{{ $shop->user ? ' — '.$shop->user->email : '' }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Source URL</label>
          <input type="url" name="source_url" placeholder="https://...">
        </div>
        <div class="form-group">
          <label>Published At</label>
          <input type="datetime-local" name="published_at">
        </div>
        <div class="form-group">
          <label>💎 Ruby Points Required</label>
          <input type="number" name="points_required" min="0" max="99999" placeholder="200" value="200">
        </div>
        <div class="form-group">
          <label>Offer Type</label>
          <div class="type-toggle">
            <input type="radio" name="offer_category" id="oc_coupon_new" value="coupon" checked>
            <label for="oc_coupon_new">🎟 Coupon / Code</label>
            <input type="radio" name="offer_category" id="oc_product_new" value="product">
            <label for="oc_product_new">📦 Product / Delivery</label>
          </div>
          <div style="font-size:.73rem;color:#94a3b8;margin-top:4px;">Coupon: reveals a code. Product: Flipkart-style order tracking.</div>
        </div>
        <div class="form-group">
          <label>Offer Photo</label>
          <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this,'new-preview')">
          <img id="new-preview" class="photo-preview" style="display:none;margin-top:6px">
        </div>
        <div class="form-group form-full">
          <label>Offer Content</label>
          <textarea name="content" rows="3" placeholder="Describe the offer..."></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Add Offer</button>
      </div>
    </form>
  </div>

  {{-- All Offers Table --}}
  <div class="admin-card">
    <div class="card-header">
      <h3>All Offers
        @if($q)<span style="font-weight:400;font-size:.9rem;color:#6b7280;"> — results for "{{ $q }}"</span>@endif
      </h3>
      <span style="color:#6b7280;font-size:.85rem;">{{ $offers->total() }} offer{{ $offers->total() != 1 ? 's' : '' }}</span>
    </div>

    <div class="table-wrap">
      <table class="offers-table">
        <thead>
          <tr>
            <th>Offer</th>
            <th>Type</th>
            <th>Points</th>
            <th>City</th>
            <th>Linked Shop</th>
            <th>Published</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($offers as $offer)
            <tr>
              <td>
                <div class="offer-cell">
                  @if($offer->photo_path)
                    <img src="{{ Storage::disk('public')->url($offer->photo_path) }}" class="offer-thumb" alt="">
                  @else
                    <div class="offer-thumb-placeholder">🎁</div>
                  @endif
                  <div class="offer-info">
                    <strong>{{ $offer->title }}</strong>
                    <small>{{ \Illuminate\Support\Str::limit((string) $offer->content, 100) }}</small>
                  </div>
                </div>
              </td>
              <td>
                @if($offer->offer_category === 'product')
                  <span class="type-badge-product">📦 Product</span>
                @else
                  <span class="type-badge-coupon">🎟 Coupon</span>
                @endif
              </td>
              <td><span class="points-pill">💎 {{ $offer->points_required }}</span></td>
              <td>
                @if($offer->city)
                  <span class="city-chip">{{ $offer->city->name }}</span>
                @else
                  <span style="color:#94a3b8">All</span>
                @endif
              </td>
              <td>
                @if($offer->shop)
                  <div style="font-weight:600;color:#374151;">{{ $offer->shop->name }}</div>
                  <div style="font-size:.78rem;color:#94a3b8;">{{ $offer->shop->user?->email }}</div>
                @else
                  <span style="color:#94a3b8">—</span>
                @endif
              </td>
              <td style="white-space:nowrap;color:#6b7280;">
                {{ optional($offer->published_at)->format('d M Y') ?: '—' }}
              </td>
              <td>
                <div class="action-btns">
                  <button type="button" class="btn btn-outline btn-sm" onclick="toggleEdit('edit-{{ $offer->id }}')">Edit</button>
                  <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" onsubmit="return confirm('Delete this offer?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                  </form>
                </div>

                {{-- Inline Edit Form --}}
                <div id="edit-{{ $offer->id }}" class="inline-edit">
                  <form action="{{ route('admin.offers.update', $offer) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PATCH')
                    <div class="form-grid">
                      <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" value="{{ $offer->title }}" required>
                      </div>
                      <div class="form-group">
                        <label>City</label>
                        <select name="city_id">
                          <option value="">All cities</option>
                          @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ (int)$offer->city_id === (int)$city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Linked Shop</label>
                        <select name="shop_id">
                          <option value="">No linked page</option>
                          @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ (int)$offer->shop_id === (int)$shop->id ? 'selected' : '' }}>{{ $shop->name }}{{ $shop->user ? ' — '.$shop->user->email : '' }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Source URL</label>
                        <input type="url" name="source_url" value="{{ $offer->source_url }}">
                      </div>
                      <div class="form-group">
                        <label>Published At</label>
                        <input type="datetime-local" name="published_at" value="{{ optional($offer->published_at)->format('Y-m-d\TH:i') }}">
                      </div>
                      <div class="form-group">
                        <label>💎 Ruby Points</label>
                        <input type="number" name="points_required" min="0" max="99999" value="{{ $offer->getRawOriginal('points_required') ?? 200 }}">
                      </div>
                      <div class="form-group">
                        <label>Offer Type</label>
                        <div class="type-toggle">
                          <input type="radio" name="offer_category" id="oc_coupon_{{ $offer->id }}" value="coupon" {{ ($offer->offer_category ?? 'coupon') === 'coupon' ? 'checked' : '' }}>
                          <label for="oc_coupon_{{ $offer->id }}">🎟 Coupon</label>
                          <input type="radio" name="offer_category" id="oc_product_{{ $offer->id }}" value="product" {{ ($offer->offer_category ?? '') === 'product' ? 'checked' : '' }}>
                          <label for="oc_product_{{ $offer->id }}">📦 Product</label>
                        </div>
                      </div>
                      <div class="form-group">
                        <label>Change Photo</label>
                        <div class="photo-wrap">
                          @if($offer->photo_path)
                            <img src="{{ Storage::disk('public')->url($offer->photo_path) }}" class="photo-preview" id="preview-{{ $offer->id }}">
                          @else
                            <img class="photo-preview" id="preview-{{ $offer->id }}" style="display:none">
                          @endif
                          <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this,'preview-{{ $offer->id }}')">
                        </div>
                      </div>
                      <div class="form-group form-full">
                        <label>Content</label>
                        <textarea name="content" rows="2">{{ $offer->content }}</textarea>
                      </div>
                    </div>
                    <div class="form-actions" style="margin-top:10px;">
                      <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                      <button type="button" class="btn btn-outline btn-sm" onclick="toggleEdit('edit-{{ $offer->id }}')">Cancel</button>
                    </div>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">No offers found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="margin-top:14px;">{{ $offers->links() }}</div>
  </div>
</div>

<script>
function toggleEdit(id) {
  const el = document.getElementById(id);
  el.classList.toggle('open');
}
function previewPhoto(input, previewId) {
  const preview = document.getElementById(previewId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
@endsection
