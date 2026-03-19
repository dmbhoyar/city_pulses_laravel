@extends('layouts.app')

@section('content')
<div class="panel">
  <h2>{{ $rent->title }}</h2>
  <p><strong>City:</strong> {{ $rent->city?->name }}</p>
  <div>{!! simple_format($rent->description) !!}</div>
  @auth
    @if(auth()->user()->isSuperadmin())
      <a href="{{ route('rents.edit', $rent->id) }}" class="button">Edit</a>
      <form action="{{ route('rents.destroy', $rent->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="button danger">Delete</button>
      </form>
    @endif
  @endauth
</div>
@endsection
