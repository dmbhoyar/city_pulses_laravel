@extends('layouts.app')

@section('content')
<div class="panel max-w-lg mx-auto">
  <div class="panel-heading">
    <h1 class="mb-0">{{ $update->title }}</h1>
  </div>
  <div class="panel-body">
    <div class="text-muted text-sm mb-3">
      <strong>City:</strong> {{ $update->city?->name ?? 'All' }} &bull; {{ $update->created_at->format('d F Y') }}
    </div>
    <div class="prose">
      {!! simple_format($update->content) !!}
    </div>
  </div>
  <div class="panel-footer">
    @auth
      @if(auth()->user()->isSuperadmin())
        <a href="{{ route('updates.edit', $update->id) }}" class="btn-secondary">Edit</a>
        <form action="{{ route('updates.destroy', $update->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-danger ml-2">Delete</button>
        </form>
      @endif
    @endauth
    <a href="{{ route('updates.index') }}" class="btn-link ml-2">Back</a>
  </div>
</div>
@endsection
