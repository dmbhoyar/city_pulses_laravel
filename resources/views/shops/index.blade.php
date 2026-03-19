@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin:0">Shops</h1>
  <div style="margin-top:12px">
    @auth
      <a href="{{ route('shops.create') }}" class="toggle-btn">Create Shop</a>
    @endauth
  </div>
</div>

<div class="grid">
  @foreach($shops as $s)
    <div class="card">
      <h3><a href="{{ route('shops.show', $s->id) }}">{{ $s->name }}</a></h3>
      <p>{{ $s->address }}</p>
      <p>{{ $s->phone }}</p>
      <div style="margin-top:8px">
        <a href="{{ route('shops.show', $s->id) }}" class="button">View</a>
      </div>
    </div>
  @endforeach
</div>
@endsection
