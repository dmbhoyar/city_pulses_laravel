@extends('layouts.app')

@section('content')
<h1>Farming Guides & Updates</h1>

@if(count($farmings))
  @foreach($farmings as $f)
    <div class="card">
      <h3><a href="{{ route('farming.show', $f->id) }}">{{ $f->title }}</a></h3>
      <div>{{ truncate_text($f->content, 200) }}</div>
      <div><small>{{ $f->city?->name }}</small></div>
    </div>
  @endforeach
@else
  <p>No farming articles yet.</p>
@endif

@auth
  <a href="{{ route('farming.create') }}" class="toggle-btn">Add Farming Note</a>
@endauth
@endsection
