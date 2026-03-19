@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin:0">Shop Dashboard</h1>
  @if(isset($shop) && $shop)
    <h2 style="margin-top:8px">{{ $shop->name }}</h2>
    <div class="card">
      <p><strong>Total revenue:</strong> {{ number_to_currency($revenue_total ?? 0) }}</p>
    </div>

    <h3 style="margin-top:12px">Offers</h3>
    @if(isset($offers) && count($offers))
      <div class="grid">
        @foreach($offers as $o)
          <div class="card"><strong>{{ $o->title }}</strong><div>{{ truncate_text($o->content, 120) }}</div></div>
        @endforeach
      </div>
    @else
      <div class="card">No active offers.</div>
    @endif

    <div style="margin-top:12px">
      <a href="{{ route('configure_myshop') }}" class="toggle-btn">Configure Shop</a>
      <a href="{{ route('subscriptions.new') }}" class="button">Subscription</a>
    </div>
  @else
    <p>No shop found for your account.</p>
  @endif
</div>
@endsection
