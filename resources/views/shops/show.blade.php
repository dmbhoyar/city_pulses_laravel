@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin-top:0">{{ $shop->name }}</h1>
  <div style="display:flex;gap:16px;flex-wrap:wrap">
    <div style="flex:1;min-width:220px">
      <p><strong>Address:</strong> {{ $shop->address }}</p>
      <p><strong>Phone:</strong> {{ $shop->phone }}</p>
    </div>
    <div style="flex:2;min-width:200px">
      {!! simple_format($shop->description) !!}
    </div>
  </div>

  @auth
    @if($shop->user_id === auth()->user()->id || auth()->user()->isSuperadmin())
      <div style="margin-top:12px">
        <a href="{{ route('shops.edit', $shop->id) }}" class="toggle-btn">Edit Shop</a>
      </div>
    @endif
  @endauth
</div>

<div class="panel">
  <h2 style="margin-top:0">Listings</h2>
  @if($shop->listings->count())
    <div class="grid">
      @foreach($shop->listings as $l)
        <div class="card">
          <h4><a href="{{ route('listings.show', $l->id) }}">{{ $l->title }}</a></h4>
          <div>{{ $l->category }} — {{ $l->price ? number_to_currency($l->price) : 'Contact' }}</div>
        </div>
      @endforeach
    </div>
  @else
    <p>No listings yet.</p>
  @endif
</div>
@endsection
