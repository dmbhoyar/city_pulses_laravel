@extends('layouts.app')

@section('content')
<div class="panel">
  <h1 style="margin-top:0">{{ $job->title }}</h1>
  <div style="display:flex;gap:16px;flex-wrap:wrap">
    <div style="flex:1;min-width:220px">
      <p><strong>Company:</strong> {{ $job->company ?: '—' }}</p>
      <p><strong>Location:</strong> {{ $job->location ?: '—' }}</p>
      <p><strong>Category:</strong> {{ $job->category ?: '—' }}</p>
    </div>
    <div style="flex:2;min-width:200px">
      {!! simple_format($job->description) !!}
    </div>
  </div>

  <div style="margin-top:12px">
    @auth
      @if(auth()->user()->isSuperadmin())
        <a href="{{ route('jobs.edit', $job->id) }}" class="button">Edit</a>
        <form action="{{ route('jobs.destroy', $job->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="button danger">Delete</button>
        </form>
      @endif
    @endauth
    <a href="{{ route('jobs.apply', $job->id) }}" class="toggle-btn">Apply</a>
  </div>
</div>
@endsection
