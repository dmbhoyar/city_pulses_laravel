@extends('layouts.app')

@section('content')
<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2 style="margin:0">Buy &amp; Sell</h2>
    @auth
      <a href="{{ route('buy.new') }}" class="toggle-btn">New Buy Record</a>
    @endauth
  </div>
</div>

<div class="grid" style="margin-top:12px">
  @foreach($listings as $buy)
    <div class="card">
      <h3><a href="{{ route('buy.show', $buy->id) }}">{{ $buy->title }}</a></h3>
      <span>City: {{ $buy->city?->name }}</span>
    </div>
  @endforeach
</div>
@endsection
