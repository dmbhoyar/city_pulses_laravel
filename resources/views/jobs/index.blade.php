@extends('layouts.app')

@section('content')
<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1 style="margin:0">Jobs</h1>
    <div>
      @auth
        @if(auth()->user()->isShopowner() || auth()->user()->isSuperadmin())
          <a href="{{ route('jobs.create') }}" class="toggle-btn">Post Job</a>
        @endif
      @endauth
    </div>
  </div>

  <div class="jobs-search" style="margin-top:12px">
    <form action="{{ route('jobs.index') }}" method="GET">
      <div style="display:flex;gap:8px">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search jobs..." class="search-input">
        <button type="submit" class="button">Search</button>
      </div>
    </form>
  </div>
</div>

<div class="grid" style="margin-top:12px">
  @foreach($jobs as $job)
    <div class="card">
      <h3><a href="{{ route('jobs.show', $job->id) }}">{{ $job->title }}</a></h3>
      <p>{{ $job->company }} — {{ $job->location }}</p>
      <div style="margin-top:8px">
        <a href="{{ route('jobs.show', $job->id) }}" class="button">View</a>
        <a href="{{ route('jobs.apply', $job->id) }}" class="button">Apply</a>
      </div>
    </div>
  @endforeach
</div>
@endsection
