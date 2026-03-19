@extends('layouts.app')

@section('content')
<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2 style="margin:0">Services</h2>
    @auth
      <a href="{{ route('services.create') }}" class="toggle-btn">New Service Record</a>
    @endauth
  </div>
</div>

<div class="grid" style="margin-top:12px">
  @foreach($services as $service)
    <div class="card">
      <h3><a href="{{ route('services.show', $service->id) }}">{{ $service->title }}</a></h3>
      <span>City: {{ $service->city?->name }}</span>
    </div>
  @endforeach
</div>
@endsection
