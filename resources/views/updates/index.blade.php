@extends('layouts.app')

@section('content')
<div class="panel max-w-lg mx-auto">
  <div class="panel-heading text-center">Updates & News Headlines</div>
  <div class="panel-body">
    <div class="mb-4 text-center">
      @auth
        @if(auth()->user()->isSuperadmin())
          <a href="{{ route('updates.create') }}" class="btn-primary">New Update</a>
        @endif
      @endauth
    </div>
    <ul class="space-y-6">
      @foreach($updates as $update)
        <li class="card">
          <div class="card-body">
            <h3 class="card-title">
              <a href="{{ route('updates.show', $update->id) }}" class="link">{{ $update->title }}</a>
            </h3>
            <div class="text-muted text-sm mb-2">
              <strong>City:</strong> {{ $update->city?->name ?? 'All' }} &bull; {{ $update->created_at->format('d F Y') }}
            </div>
            <p class="mb-0">{{ truncate_text(strip_tags($update->content), 220) }}</p>
          </div>
          <div class="card-footer">
            <a href="{{ route('updates.show', $update->id) }}" class="btn-link">Read more</a>
          </div>
        </li>
      @endforeach
    </ul>
  </div>
</div>
@endsection
