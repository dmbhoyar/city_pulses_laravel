@extends('layouts.app')

@section('content')
<div class="panel" style="max-width:980px;margin:0 auto;">
  <div class="card" style="border:1px solid rgba(61,43,31,.14);border-radius:12px;overflow:hidden;">
    <div style="background:linear-gradient(120deg,#1a5e20,#2e7d32);padding:16px 18px;color:#fff;">
      <div style="font-size:.78rem;opacity:.9;letter-spacing:.5px;text-transform:uppercase;">Farmer Blog</div>
      <h1 style="margin:4px 0 6px;font-size:1.45rem;line-height:1.3;">{{ $farming->title }}</h1>
      <div style="font-size:.8rem;opacity:.92;">✍️ {{ $farming->author_name ?: 'Farmer' }} · 📍 {{ $farming->city?->name ?: 'City not set' }} · {{ $farming->created_at?->format('d M Y, h:i A') }}</div>
    </div>

    <div style="padding:16px 18px;">
      <div style="line-height:1.75;color:#3d2b1f;font-size:.93rem;">{!! $farming->content !!}</div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
        <a href="{{ route('farming.index') }}" class="toggle-btn">Back to Farming</a>
        @auth
          @if(auth()->user()->isSuperadmin())
            <a href="{{ route('farming.edit', $farming) }}" class="button">Edit</a>
            <form action="{{ route('farming.destroy', $farming) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this farming record?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="button danger">Delete</button>
            </form>
          @endif
        @endauth
      </div>
    </div>
  </div>
</div>
@endsection
