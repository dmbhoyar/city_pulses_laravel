@extends('layouts.app')

@section('content')
@php
  $requestsPageTitle = $requestsPageTitle ?? 'Client Requests';
  $backRoute = $backRoute ?? 'myservice';
  $indexRoute = $indexRoute ?? 'myservice_requests';
  $updateRoute = $updateRoute ?? 'myservice_requests_update';
@endphp
<style>
  .req-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-top:12px}
  .req-stat{background:#fff;border:1px solid #dbe7f8;border-radius:10px;padding:12px}
  .req-stat .k{font-size:12px;color:#6d84a5}
  .req-stat .v{font-size:24px;font-weight:800;color:#2f4e74;line-height:1.1;margin-top:4px}
  .req-filters{display:grid;grid-template-columns:1.2fr .8fr .8fr .8fr auto;gap:8px;align-items:end;margin:12px 0}
  .req-filters input,.req-filters select{width:100%}
  .req-table-wrap{overflow:auto;border:1px solid #dbe7f8;border-radius:10px;background:#fff}
  .req-table{width:100%;border-collapse:collapse;min-width:960px}
  .req-table th,.req-table td{padding:9px 10px;border-bottom:1px solid #e8eef9;vertical-align:top;font-size:12px}
  .req-table th{background:#f8fbff;color:#4b6b94;text-transform:uppercase;letter-spacing:.05em;font-size:11px}
  .req-badge{display:inline-flex;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;text-transform:capitalize}
  .req-badge.new{background:#e8f2ff;color:#2f6fbe}
  .req-badge.confirmed{background:#fff4db;color:#9a6500}
  .req-badge.pending{background:#fff7e6;color:#a16207}
  .req-badge.completed{background:#e8f7ee;color:#1f8a49}
  .req-badge.cancelled{background:#feecec;color:#c53d3d}
  .req-badge.callback{background:#f3ecff;color:#7e4dc2}
  .req-badge.called{background:#e7faf1;color:#0f7a43}
  .req-notes{width:100%;min-height:50px;resize:vertical}
  @media(max-width:960px){.req-filters{grid-template-columns:1fr 1fr}.req-filters .wide{grid-column:1/-1}}
</style>

<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap">
    <h1 style="margin:0">{{ $requestsPageTitle }}</h1>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <a href="{{ route($backRoute) }}" class="button">← Back Dashboard</a>
      @if($shop->id)
        <a href="{{ route('shops.public', ['publicSlug' => $shop->public_page_slug]) }}" target="_blank" class="button">View Public Page ↗</a>
      @endif
    </div>
  </div>

  <div class="req-grid">
    <div class="req-stat"><div class="k">Total Requests</div><div class="v">{{ $stats['total'] }}</div></div>
    <div class="req-stat"><div class="k">New</div><div class="v">{{ $stats['new'] }}</div></div>
    <div class="req-stat"><div class="k">Pending</div><div class="v">{{ $stats['pending'] }}</div></div>
    <div class="req-stat"><div class="k">Callbacks</div><div class="v">{{ $stats['callback'] }}</div></div>
    <div class="req-stat"><div class="k">Completed</div><div class="v">{{ $stats['completed'] }}</div></div>
  </div>

  <form method="GET" action="{{ route($indexRoute) }}" class="req-filters">
    <div class="wide">
      <label>Search</label>
      <input type="text" name="q" value="{{ $search }}" placeholder="Name, phone, service, source">
    </div>
    <div>
      <label>Status</label>
      <select name="status">
        <option value="">All</option>
        @foreach(['new','confirmed','pending','completed','cancelled','callback','called'] as $st)
          <option value="{{ $st }}" {{ $statusFilter === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label>Template</label>
      <select name="template">
        <option value="">All</option>
        @foreach($templates as $tpl)
          <option value="{{ $tpl }}" {{ $templateFilter === $tpl ? 'selected' : '' }}>{{ $tpl }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label>Source</label>
      <select name="source">
        <option value="">All</option>
        @foreach($sources as $src)
          <option value="{{ $src }}" {{ $sourceFilter === $src ? 'selected' : '' }}>{{ $src }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <button class="toggle-btn" type="submit">Apply</button>
    </div>
  </form>

  <div class="req-table-wrap">
    <table class="req-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Contact</th>
          <th>Service</th>
          <th>Template / Source</th>
          <th>Status</th>
          <th>Message + Notes</th>
          <th>Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $req)
          <tr>
            <td>{{ $req->id }}</td>
            <td>
              <div style="font-weight:700;color:#2f4e74">{{ $req->customer_name ?: 'Anonymous' }}</div>
              @if($req->dob)<div style="color:#6d84a5">DOB: {{ $req->dob->format('d M Y') }}</div>@endif
            </td>
            <td>
              <div>{{ $req->phone }}</div>
              @if($req->email)<div style="color:#6d84a5">{{ $req->email }}</div>@endif
            </td>
            <td>{{ $req->service_name ?: 'General Request' }}</td>
            <td>
              <div style="font-weight:600">{{ $req->template_key }}</div>
              <div style="color:#6d84a5">{{ $req->source ?: 'public_form' }}</div>
            </td>
            <td><span class="req-badge {{ $req->status }}">{{ $req->status }}</span></td>
            <td style="min-width:240px">
              @if($req->message)<div style="margin-bottom:6px">{{ \Illuminate\Support\Str::limit($req->message, 180) }}</div>@endif
              <form action="{{ route($updateRoute, ['id' => $req->id]) }}" method="POST">
                @csrf
                @method('PATCH')
                <textarea class="req-notes" name="admin_notes" placeholder="Internal notes...">{{ $req->admin_notes }}</textarea>
            </td>
            <td>
              <div>{{ $req->created_at?->format('d M Y, h:i A') }}</div>
              @if($req->contacted_at)<div style="color:#6d84a5">Called: {{ $req->contacted_at->format('d M, h:i A') }}</div>@endif
              @if($req->resolved_at)<div style="color:#6d84a5">Closed: {{ $req->resolved_at->format('d M, h:i A') }}</div>@endif
            </td>
            <td style="min-width:170px">
              <select name="status" style="width:100%;margin-bottom:6px">
                @foreach(['new','confirmed','pending','completed','cancelled','callback','called'] as $st)
                  <option value="{{ $st }}" {{ $req->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                @endforeach
              </select>
              <button type="submit" class="button" style="width:100%">Save</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" style="text-align:center;color:#6d84a5;padding:20px">No client requests found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:10px">{{ $requests->links() }}</div>
</div>
@endsection
