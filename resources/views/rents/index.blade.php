@extends('layouts.app')

@section('content')
<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2 style="margin:0">Rents</h2>
    @auth
      <a href="{{ route('rents.create') }}" class="toggle-btn">New Rent Record</a>
    @endauth
  </div>
</div>

<div class="grid" style="margin-top:12px">
  @foreach($rents as $rent)
    <div class="card">
      <h3><a href="{{ route('rents.show', $rent->id) }}">{{ $rent->title }}</a></h3>
      <span>City: {{ $rent->city?->name }}</span>
    </div>
  @endforeach
</div>
@endsection
