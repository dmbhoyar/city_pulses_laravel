@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin:0">My Shop</h1>
  @if(isset($shop) && $shop)
    <h2 style="margin-top:8px">{{ $shop->name }}</h2>
    <div class="card">{!! simple_format($shop->description) !!}</div>
    <div style="display:flex;gap:8px;margin-top:8px">
      <a href="{{ route('configure_myshop') }}" class="toggle-btn">Configure</a>
      <a href="{{ route('workers_myshop') }}" class="button">Workers</a>
      <a href="{{ route('myshop_offer_new') }}" class="button">Offers</a>
    </div>
  @else
    <p>You don't have a shop yet. <a href="{{ route('configure_myshop') }}" class="toggle-btn">Create one</a></p>
  @endif
</div>
@endsection
