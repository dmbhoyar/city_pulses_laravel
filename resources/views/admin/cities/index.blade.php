@extends('layouts.app')

@section('content')
@include('admin.partials.ui')
<div class="panel admin-wrap">
  <div class="admin-head">
    <h1>Cities Management</h1>
    <a href="{{ route('admin.dashboard') }}" class="button">Back to Admin</a>
  </div>

  <div class="admin-card">
    <div class="admin-toolbar">
      <h3 style="margin:0">Add New City</h3>
      <form method="GET" action="{{ route('admin.cities.index') }}" class="admin-search">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search city, Agmarknet district, market...">
        <button type="submit" class="button">Search</button>
      </form>
    </div>

    <form action="{{ route('admin.cities.store') }}" method="POST">
      @csrf
      <div class="admin-form-grid">
        <div><label>City Name</label><input type="text" name="name" required></div>
        <div><label>Agmarknet District</label><input type="text" name="agmarknet_district"></div>
        <div><label>Agmarknet Market</label><input type="text" name="agmarknet_market"></div>
        <div><label>Agmarknet State</label><input type="text" name="agmarknet_state"></div>
        <div><label>Latitude</label><input type="number" step="0.000001" name="latitude"></div>
        <div><label>Longitude</label><input type="number" step="0.000001" name="longitude"></div>
      </div>
      <div class="admin-actions"><button type="submit" class="button">Add City</button></div>
    </form>
  </div>

  <div class="admin-card">
    <h3>All Cities</h3>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>City</th>
            <th>Agmarknet District / Market</th>
            <th>Agmarknet State</th>
            <th>Coordinates</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cities as $city)
            <tr>
              <td>
                <strong>{{ $city->name }}</strong>
                <div class="admin-muted">ID #{{ $city->id }}</div>
              </td>
              <td>
                <div>{{ $city->agmarknet_district ?: '—' }}</div>
                <div class="admin-muted">{{ $city->agmarknet_market ?: '—' }}</div>
              </td>
              <td>{{ $city->agmarknet_state ?: '—' }}</td>
              <td>{{ $city->latitude ?: '—' }}, {{ $city->longitude ?: '—' }}</td>
              <td>
                <form action="{{ route('admin.cities.update', $city) }}" method="POST" class="admin-inline">
                  @csrf
                  @method('PATCH')
                  <div class="row">
                    <input type="text" name="name" value="{{ $city->name }}" required>
                    <input type="text" name="agmarknet_district" value="{{ $city->agmarknet_district }}" placeholder="Agmarknet District">
                    <input type="text" name="agmarknet_market" value="{{ $city->agmarknet_market }}" placeholder="Agmarknet Market">
                  </div>
                  <div class="row">
                    <input type="text" name="agmarknet_state" value="{{ $city->agmarknet_state }}" placeholder="Agmarknet State">
                    <input type="number" step="0.000001" name="latitude" value="{{ $city->latitude }}" placeholder="Latitude">
                    <input type="number" step="0.000001" name="longitude" value="{{ $city->longitude }}" placeholder="Longitude">
                  </div>
                  <div class="admin-actions">
                    <button type="submit" class="button">Update</button>
                </form>
                    <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" onsubmit="return confirm('Delete this city?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="button danger">Delete</button>
                    </form>
                  </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5">No cities found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="margin-top:10px">{{ $cities->links() }}</div>
  </div>
</div>
@endsection
