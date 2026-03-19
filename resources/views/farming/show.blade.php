@extends('layouts.app')

@section('content')
<h1>{{ $farming->title }}</h1>
<div><small>{{ $farming->city?->name }} — {{ $farming->created_at->format('d M Y') }}</small></div>
<div>{!! simple_format($farming->content) !!}</div>

@auth
  <a href="{{ route('farming.index') }}" class="toggle-btn">Back</a>
  @if(auth()->user()->isSuperadmin())
    <a href="{{ route('farming.edit', $farming->id) }}" class="button">Edit</a>
    <form action="{{ route('farming.destroy', $farming->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="button danger">Delete</button>
    </form>
  @endif
@endauth
@endsection
