@extends('layouts.app')

@section('content')
<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1 style="margin:0">Listings</h1>
    <div>
      <a href="{{ route('listings.index') }}" class="button">All</a>
      <a href="{{ route('listings.index', ['category' => 'sell']) }}" class="button">Buy/Sell</a>
      <a href="{{ route('listings.index', ['category' => 'rent']) }}" class="button">Rentals</a>
      <a href="{{ route('listings.index', ['category' => 'service']) }}" class="button">Services</a>
    </div>
  </div>

  <div class="grid" style="margin-top:12px">
    @foreach($listings as $l)
      <div class="card">
        <h3><a href="{{ route('listings.show', $l->id) }}">{{ $l->title }}</a></h3>
        <p>{{ truncate_text($l->description, 150) }}</p>
        <p><strong>Price:</strong> {{ $l->price ? number_to_currency($l->price) : 'Contact' }}</p>
        <p><strong>Contact:</strong> {{ $l->contact_number ?: '—' }}</p>
        <div style="margin-top:8px">
          <a href="{{ route('listings.show', $l->id) }}" class="button">View</a>
          @auth
            @if(auth()->user()->id === $l->user_id || auth()->user()->isSuperadmin())
              <a href="{{ route('listings.edit', $l->id) }}" class="button">Edit</a>
            @endif
          @endauth
        </div>
      </div>
    @endforeach
  </div>

  <div style="margin-top:12px">
    @auth
      <a href="{{ route('listings.create') }}" class="toggle-btn">Add Listing</a>
    @endauth
  </div>
</div>
@endsection
