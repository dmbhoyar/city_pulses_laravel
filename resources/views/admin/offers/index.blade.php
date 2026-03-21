@extends('layouts.app')

@section('content')
@include('admin.partials.ui')
<div class="panel admin-wrap">
  <div class="admin-head">
    <h1>Offers Management</h1>
    <a href="{{ route('admin.dashboard') }}" class="button">Back to Admin</a>
  </div>

  <div class="admin-card">
    <div class="admin-toolbar">
      <h3 style="margin:0">Add New Offer</h3>
      <form method="GET" action="{{ route('admin.offers.index') }}" class="admin-search">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search by offer title/content...">
        <button type="submit" class="button">Search</button>
      </form>
    </div>

    <form action="{{ route('admin.offers.store') }}" method="POST">
      @csrf
      <div class="admin-form-grid">
        <div><label>Title</label><input type="text" name="title" required></div>
        <div>
          <label>City</label>
          <select name="city_id">
            <option value="">All cities</option>
            @foreach($cities as $city)
              <option value="{{ $city->id }}">{{ $city->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label>Link Shop / Service</label>
          <select name="shop_id">
            <option value="">No linked page</option>
            @foreach($shops as $shop)
              <option value="{{ $shop->id }}">{{ $shop->name }}{{ $shop->user ? ' — ' . $shop->user->email : '' }}</option>
            @endforeach
          </select>
        </div>
        <div><label>Source URL</label><input type="url" name="source_url" placeholder="https://..."></div>
        <div><label>Published At</label><input type="datetime-local" name="published_at"></div>
      </div>
      <div class="admin-form-grid" style="grid-template-columns:1fr">
        <div><label>Offer Content</label><textarea name="content" rows="3" placeholder="Offer details..."></textarea></div>
      </div>
      <div class="admin-actions"><button type="submit" class="button">Add Offer</button></div>
    </form>
  </div>

  <div class="admin-card">
    <h3>All Offers</h3>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Offer</th>
            <th>City</th>
            <th>Published</th>
            <th>Linked Page</th>
            <th>Source</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($offers as $offer)
            <tr>
              <td>
                <strong>{{ $offer->title }}</strong>
                <div class="admin-muted" style="max-width:360px">{{ \Illuminate\Support\Str::limit((string) $offer->content, 120) }}</div>
              </td>
              <td>{{ $offer->city?->name ?: 'All' }}</td>
              <td>{{ optional($offer->published_at)->format('d M Y H:i') ?: '—' }}</td>
              <td>
                @if($offer->shop)
                  <div>{{ $offer->shop->name }}</div>
                  <div class="admin-muted">{{ $offer->shop->user?->email ?: 'Linked page' }}</div>
                @else
                  —
                @endif
              </td>
              <td>
                @if($offer->source_url)
                  <a href="{{ $offer->source_url }}" target="_blank" rel="noopener">Open</a>
                @else
                  —
                @endif
              </td>
              <td>
                <form action="{{ route('admin.offers.update', $offer) }}" method="POST" class="admin-inline">
                  @csrf
                  @method('PATCH')
                  <div class="row2">
                    <input type="text" name="title" value="{{ $offer->title }}" required>
                    <select name="city_id">
                      <option value="">All cities</option>
                      @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ (int) $offer->city_id === (int) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <select name="shop_id">
                    <option value="">No linked page</option>
                    @foreach($shops as $shop)
                      <option value="{{ $shop->id }}" {{ (int) $offer->shop_id === (int) $shop->id ? 'selected' : '' }}>{{ $shop->name }}{{ $shop->user ? ' — ' . $shop->user->email : '' }}</option>
                    @endforeach
                  </select>
                  <div class="row2">
                    <input type="url" name="source_url" value="{{ $offer->source_url }}" placeholder="Source URL">
                    <input type="datetime-local" name="published_at" value="{{ optional($offer->published_at)->format('Y-m-d\TH:i') }}">
                  </div>
                  <textarea name="content" rows="2" placeholder="Offer content">{{ $offer->content }}</textarea>
                  <div class="admin-actions">
                    <button type="submit" class="button">Update</button>
                </form>
                    <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" onsubmit="return confirm('Delete this offer?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="button danger">Delete</button>
                    </form>
                  </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6">No offers found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="margin-top:10px">{{ $offers->links() }}</div>
  </div>
</div>
@endsection
