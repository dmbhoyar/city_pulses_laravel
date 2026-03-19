@extends('layouts.app')

@section('content')
<div class="panel">
  <h2>{{ $buy->title }}</h2>
  <p><strong>City:</strong> {{ $buy->city?->name }}</p>
  <div>{!! simple_format($buy->description) !!}</div>
  @auth
    @if(auth()->user()->id === $buy->user_id || auth()->user()->isSuperadmin())
      <a href="{{ route('buy.edit', $buy->id) }}" class="button">Edit</a>
      <form action="{{ route('buy.destroy', $buy->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="button danger">Delete</button>
      </form>
    @endif
  @endauth
</div>
@endsection
