@extends('layouts.app')

@section('content')
<div class="panel">
  <h1>Admin Dashboard</h1>
  <ul>
    <li>Users: {{ $users_count }}</li>
    <li>Shops: {{ $shops_count }}</li>
    <li>Listings: {{ $listings_count }}</li>
  </ul>
  <div style="margin-top:12px">
    <a href="{{ route('admin.users.index') }}" class="button">Manage Users</a>
    <a href="{{ route('admin.shops.index') }}" class="button">Manage Shops</a>
  </div>
</div>
@endsection
