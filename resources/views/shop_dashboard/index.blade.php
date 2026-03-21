@extends('layouts.app')

@section('content')
@php
  $isServiceProvider = auth()->check() && auth()->user()->isServiceProvider();
  $entityTitle = $isServiceProvider ? 'Service Dashboard' : 'Shop Dashboard';
  $entityName = $isServiceProvider ? 'service' : 'shop';
  $configureRoute = $isServiceProvider ? route('configure_myservice') : route('configure_myshop');
  $configureLabel = $isServiceProvider ? 'Configure Service' : 'Configure Shop';
@endphp
<div class="panel">
  <h1 style="margin:0">{{ $entityTitle }}</h1>
  @if(isset($shop) && $shop)
    <h2 style="margin-top:8px">{{ $shop->name }}</h2>
    <div class="card">
      <p><strong>Total revenue:</strong> {{ number_to_currency($revenueTotal ?? 0) }}</p>
      <p style="margin-top:6px"><strong>Subscription:</strong>
        @if(!empty($activeSubscription))
          Active ({{ $activeSubscription->plan_key ?: 'yearly_base' }}) · valid till {{ optional($activeSubscription->expires_at)->format('d M Y') }}
        @else
          Not active
        @endif
      </p>
      <p style="margin-top:6px"><strong>Astro Dynamic Unlock:</strong>
        @if(!empty($astroUnlockRequest))
          {{ ucfirst($astroUnlockRequest->status) }}
        @else
          Not requested
        @endif
      </p>
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
      <a href="{{ $configureRoute }}" class="toggle-btn">{{ $configureLabel }}</a>
      <a href="{{ route('subscriptions.new') }}" class="button">Subscription</a>
    </div>
  @else
    <p>No {{ $entityName }} found for your account.</p>
  @endif
</div>
@endsection
