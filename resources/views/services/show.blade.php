@extends('layouts.app')

@section('content')
<div class="panel">
  <h2>{{ $service->title }}</h2>
  <p><strong>City:</strong> {{ $service->city?->name }}</p>
  <div>{!! simple_format($service->description) !!}</div>
  @auth
    @if(auth()->user()->id === $service->user_id || auth()->user()->isSuperadmin())
      <a href="{{ route('services.edit', $service->id) }}" class="button">Edit</a>
      <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="button danger">Delete</button>
      </form>
    @endif
  @endauth
</div>
@endsection
