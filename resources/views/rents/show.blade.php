@extends('layouts.app')

@section('content')
@php
  $callDigits = preg_replace('/\D+/', '', (string) ($rent->contact_number ?? ''));
@endphp
<div class="panel" style="max-width:1000px;margin:0 auto;">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:12px">
    <div>
      <h2 style="margin:0 0 4px">{{ $rent->title }}</h2>
      <div style="font-size:13px;color:#5d7698">📍 {{ $rent->city?->name ?: 'City not set' }} · {{ ucfirst($rent->subcategory ?: 'general') }}</div>
    </div>
    <div style="text-align:right">
      <div style="font-size:28px;font-weight:900;color:#2f4e74">{{ $rent->price ? '₹' . number_format((float) $rent->price, 0) . '/mo' : 'Contact' }}</div>
      <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#eef5ff;border:1px solid #d8e5f8;color:#365982;font-size:12px;font-weight:700">{{ strtoupper($rent->status) }}</span>
    </div>
  </div>

  @if(($rent->status ?? '') === 'pending')
    <div style="margin-bottom:12px;padding:10px;border:1px solid #f0d79a;background:#fff7e4;border-radius:8px;font-size:12px;color:#7b5b1d">
      This listing is under review. It becomes public after superadmin approval.
    </div>
  @endif

  @if(is_array($rent->photos) && count($rent->photos))
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;margin-bottom:12px">
      @foreach($rent->photos as $photoPath)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($photoPath) }}" alt="Rental Photo" style="width:100%;height:160px;object-fit:cover;border-radius:10px;border:1px solid #dbe7f8">
      @endforeach
    </div>
  @endif

  <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;align-items:start">
    <div style="background:#fff;border:1px solid #dbe7f8;border-radius:10px;padding:12px">
      <h3 style="margin:0 0 8px;color:#2f4e74">Description</h3>
      <div style="color:#355272;line-height:1.7">{!! simple_format($rent->description ?: 'No description provided.') !!}</div>
    </div>
    <div style="background:#fff;border:1px solid #dbe7f8;border-radius:10px;padding:12px">
      <h3 style="margin:0 0 8px;color:#2f4e74">Details</h3>
      <div style="font-size:13px;color:#4f6d8f;line-height:1.8">
        <div><strong>City:</strong> {{ $rent->city?->name ?: '—' }}</div>
        <div><strong>Location:</strong> {{ $rent->location ?: '—' }}</div>
        <div>
          <strong>Contact:</strong>
          @if($callDigits !== '')
            <a href="tel:{{ $callDigits }}">{{ $rent->contact_number }}</a>
          @else
            {{ $rent->contact_number ?: '—' }}
          @endif
        </div>
      </div>
    </div>
  </div>

  <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap">
    <a href="{{ route('rents.index') }}" class="button">← Back to Rentals</a>
    @if($callDigits !== '')
      <a href="tel:{{ $callDigits }}" class="button">📞 Call Landlord</a>
    @endif
    @auth
      @if(auth()->user()->id === $rent->user_id || auth()->user()->isSuperadmin())
        <a href="{{ route('rents.edit', $rent->id) }}" class="button">Edit</a>
        <form action="{{ route('rents.destroy', $rent->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="button danger">Delete</button>
        </form>
      @endif
    @endauth
  </div>
</div>
@endsection
