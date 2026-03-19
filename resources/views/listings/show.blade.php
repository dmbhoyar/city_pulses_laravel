@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin-top:0">{{ $listing->title }}</h1>
  <div style="display:flex;gap:16px;flex-wrap:wrap">
    <div style="flex:1;min-width:220px">
      <p><strong>Category:</strong> {{ ucfirst($listing->category) }}</p>
      <p><strong>Price:</strong> {{ $listing->price ? number_to_currency($listing->price) : 'Contact owner' }}</p>
      <p><strong>Location:</strong> {{ $listing->location ?: '—' }}</p>
      <p><strong>Contact:</strong> {{ $listing->contact_number ?: '—' }}</p>
    </div>
    <div style="flex:2;min-width:200px">
      {!! simple_format($listing->description) !!}
    </div>
  </div>

  <div style="margin-top:12px">
    @auth
      @if(auth()->user()->id === $listing->user_id || auth()->user()->isSuperadmin())
        <a href="{{ route('listings.edit', $listing->id) }}" class="toggle-btn">Edit</a>
        <form action="{{ route('listings.destroy', $listing->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="button danger">Remove</button>
        </form>
      @else
        @if($listing->contact_number)
          <a href="tel:{{ $listing->contact_number }}" class="toggle-btn">Call Owner</a>
        @endif
      @endif
    @else
      @if($listing->contact_number)
        <a href="tel:{{ $listing->contact_number }}" class="toggle-btn">Call Owner</a>
      @endif
    @endauth
  </div>
</div>
@endsection
