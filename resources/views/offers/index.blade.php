@extends('layouts.app')

@section('content')
<div class="offers-page">
  <div class="offers-header">
    <h2>Offers & Benefits</h2>
    <p>Today's date: <strong>{{ $today->format('d M Y') }}</strong></p>
    <div class="city-info">
      Showing offers for: <strong>{{ isset($city) ? $city->name : 'All Cities' }}</strong>
    </div>
  </div>

  <div class="offers-content">
    <section class="offers-list">
      <h3>Offers for {{ isset($city) ? $city->name : 'All Cities' }}</h3>
      @if(isset($offers) && count($offers))
        <ul>
          @foreach($offers as $o)
            <li>
              <strong>{{ $o->title }}</strong>
              <div class="meta">{{ $o->created_at->format('d M Y') }} — {{ $o->city?->name }}</div>
              <div class="body">{{ truncate_text(strip_tags($o->body ?? $o->description ?? ''), 180) }}</div>
            </li>
          @endforeach
        </ul>
      @else
        <p>No current offers.</p>
      @endif
    </section>

    <aside class="offers-side">
      <section class="todays-events">
        <h4>Today's Events</h4>
        @if(isset($events) && count($events))
          <ul>
            @foreach($events as $e)
              <li>
                <strong>{{ $e->title }}</strong>
                <div class="meta">{{ $e->created_at->format('h:i A') }}</div>
              </li>
            @endforeach
          </ul>
        @else
          <p>No events listed for today.</p>
        @endif
      </section>
    </aside>
  </div>
</div>
@endsection
