@extends('layouts.app')

@section('content')
@include('admin.partials.ui')
<div class="panel admin-wrap">
  <div class="admin-head">
    <h1>Admin Dashboard</h1>
  </div>

  <div class="admin-grid">
    <div class="admin-stat"><small>Users</small><strong>{{ $users_count }}</strong></div>
    <div class="admin-stat"><small>Shops</small><strong>{{ $shops_count }}</strong></div>
    <div class="admin-stat"><small>Cities</small><strong>{{ $cities_count }}</strong></div>
    <div class="admin-stat"><small>Jobs</small><strong>{{ $jobs_count }}</strong></div>
    <div class="admin-stat"><small>Offers</small><strong>{{ $offers_count }}</strong></div>
    <div class="admin-stat"><small>Subscriptions</small><strong>{{ $subscriptions_count }}</strong></div>
    <div class="admin-stat"><small>Pending Unlocks</small><strong>{{ $unlock_requests_pending_count }}</strong></div>
  </div>

  <div class="admin-card">
    <h3>Management Modules</h3>
    <div class="admin-actions">
      <a href="{{ route('admin.users.index') }}" class="button">Users</a>
      <a href="{{ route('admin.shops.index') }}" class="button">Shops</a>
      <a href="{{ route('admin.cities.index') }}" class="button">Cities</a>
      <a href="{{ route('admin.jobs.index') }}" class="button">Jobs</a>
      <a href="{{ route('admin.offers.index') }}" class="button">Offers</a>
      <a href="{{ route('admin.subscriptions.index') }}" class="button">Subscriptions & Unlocks</a>
      <a href="{{ route('admin.settings.index') }}" class="button">Settings</a>
    </div>
  </div>

  <div class="admin-card">
    <h3>Super Admin Recommendations</h3>
    <ul style="margin:0 0 0 18px;line-height:1.8">
      <li>Review pending template unlock requests at least twice daily.</li>
      <li>Keep city list clean and standardized to avoid duplicate market data.</li>
      <li>Audit role assignments monthly and keep only required admin accounts.</li>
      <li>Update expired jobs/offers to maintain platform quality.</li>
    </ul>
  </div>
</div>
@endsection
