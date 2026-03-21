@extends('layouts.app')

@section('content')
<section style="display:flex;flex-direction:column;gap:16px;">
  <div style="border:1px solid #3f5f8f;background:linear-gradient(135deg,#2c4468,#3f5f8f);border-radius:16px;padding:16px;box-shadow:0 12px 24px rgba(20,34,58,.22);color:#fff;">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
      <div>
        <p style="margin:0;font-size:11px;letter-spacing:.9px;text-transform:uppercase;color:#d8e8ff;font-weight:700;">Home Section</p>
        <h1 style="margin:4px 0 0;font-size:30px;line-height:1.1;color:#fff;font-weight:800;">Offers & Benefits</h1>
        <p style="margin:8px 0 0;font-size:13px;color:#e2ecff;max-width:760px;">Public offers from shop owners and service providers. Offers are automatically shown for your selected city and shops serving this city.</p>
      </div>
      <div style="border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.14);border-radius:12px;padding:10px 12px;min-width:210px;">
        <p style="margin:0;color:#fff;font-size:12px;"><strong style="font-weight:800;">Date:</strong> {{ $today->format('d M Y') }}</p>
        <p style="margin:6px 0 0;color:#fff;font-size:12px;"><strong style="font-weight:800;">Showing:</strong> {{ isset($city) ? $city->name : 'All Cities' }}</p>
      </div>
    </div>
  </div>

  <div class="offers-layout" style="display:grid;grid-template-columns:minmax(0,2.2fr) minmax(260px,1fr);gap:12px;align-items:start;">
    <section class="offers-list" style="display:flex;flex-direction:column;gap:10px;min-width:0;">
      @if(isset($offers) && count($offers))
        @foreach($offers as $index => $o)
          @php
            $headerGradients = [
                'linear-gradient(120deg,#2f4f77,#496da2)',
                'linear-gradient(120deg,#266b59,#2e8d77)',
                'linear-gradient(120deg,#8a4d20,#bd6b2a)',
                'linear-gradient(120deg,#7a2f6f,#a64495)',
            ];
            $gradient = $headerGradients[$index % count($headerGradients)];
            $ownerRole = strtolower((string) optional(optional($o->shop)->user)->role);
            $ownerLabel = str_contains($ownerRole, 'shop') ? 'Shop Owner' : 'Service Provider';
            $photoPath = trim((string) ($o->photo_path ?? ''));
            $isAbsolutePhoto = str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://') || str_starts_with($photoPath, '/storage/');
            $photoUrl = $photoPath === '' ? '' : ($isAbsolutePhoto ? $photoPath : asset('storage/' . ltrim($photoPath, '/')));
          @endphp

          <article class="offer-card" style="overflow:hidden;border:1px solid #d6deeb;border-radius:12px;background:#fff;box-shadow:0 4px 10px rgba(25,40,68,.06);">
            <div style="background:{{ $gradient }};padding:10px 12px;color:#fff;">
              <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:flex-start;">
                <div>
                  <p style="margin:0;font-size:11px;letter-spacing:.8px;text-transform:uppercase;font-weight:700;color:#deecff;">Offer & Benefit</p>
                  <h2 style="margin:4px 0 0;font-size:16px;font-weight:800;line-height:1.2;">{{ $o->title }}</h2>
                </div>
                <span style="padding:5px 9px;border:1px solid rgba(255,255,255,.35);border-radius:999px;background:rgba(255,255,255,.14);font-size:11px;font-weight:700;">{{ $ownerLabel }}</span>
              </div>
            </div>

            <div style="padding:10px 12px;">
              @if($photoUrl !== '')
                <div class="offer-image-wrap" style="width:100%;margin:0 0 9px;max-height:180px;overflow:hidden;border-radius:10px;border:1px solid #d9e4f6;background:#edf3ff;">
                  <img src="{{ $photoUrl }}" alt="Offer photo" style="display:block;width:100%;height:100%;max-height:180px;object-fit:cover;">
                </div>
              @endif

              <div style="display:flex;flex-wrap:wrap;gap:8px 12px;color:#5f7494;font-size:12px;">
                <span>{{ optional($o->created_at)->format('d M Y') }}</span>
                @if($o->city)
                  <span>• {{ $o->city->name }}</span>
                @endif
                @if($o->shop)
                  <span>• {{ $o->shop->name ?: 'Profile' }}</span>
                @endif
              </div>

              @if(trim((string) $o->content) !== '')
                <p style="margin:8px 0 0;font-size:12px;color:#2f4a70;line-height:1.45;">{{ truncate_text(strip_tags($o->content ?? ''), 180) }}</p>
              @endif

              @if($o->shop || $o->source_url)
                <div class="offer-actions" style="margin-top:10px;display:flex;gap:7px;flex-wrap:wrap;">
                  @if($o->shop)
                    <a href="{{ route('shops.public', ['publicSlug' => $o->shop->public_page_slug]) }}" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 11px;border-radius:10px;background:#2f4f77;color:#fff;font-size:12px;font-weight:800;text-decoration:none;">Open Public Page</a>
                    <a href="{{ route('shops.idcard.public', ['publicSlug' => $o->shop->public_page_slug]) }}" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 11px;border-radius:10px;border:1px solid #c7d5ea;background:#f7fbff;color:#2b4569;font-size:12px;font-weight:800;text-decoration:none;">View ID Card</a>
                  @endif
                  @if($o->source_url)
                    <a href="{{ $o->source_url }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 11px;border-radius:10px;border:1px solid #d3ddea;background:#fff;color:#2f4a71;font-size:12px;font-weight:700;text-decoration:none;">Offer Link</a>
                  @endif
                </div>
              @endif
            </div>
          </article>
        @endforeach
      @else
        <div style="border:1px dashed #c7d4e8;border-radius:14px;background:#fff;padding:22px;text-align:center;color:#5f7697;font-size:14px;font-weight:600;">
          No current offers for {{ isset($city) ? $city->name : 'the selected city' }}.
        </div>
      @endif
    </section>

    <aside class="offers-events" style="min-width:0;">
      <section style="border:1px solid #d7deec;border-radius:14px;background:#fff;padding:14px;">
        <h3 style="margin:0;color:#243d61;font-size:20px;font-weight:800;">Today's Events</h3>
        @if(isset($events) && count($events))
          <div style="margin-top:10px;display:flex;flex-direction:column;gap:8px;">
            @foreach($events as $e)
              <article style="border:1px solid #d9e3f3;border-radius:10px;background:#f7faff;padding:9px 10px;">
                <p style="margin:0;color:#1f3658;font-size:13px;font-weight:800;">{{ $e->title }}</p>
                <p style="margin:4px 0 0;color:#607695;font-size:11px;">{{ optional($e->created_at)->format('h:i A') }}</p>
              </article>
            @endforeach
          </div>
        @else
          <p style="margin:8px 0 0;color:#637b9d;font-size:13px;">No events listed for today.</p>
        @endif
      </section>
    </aside>
  </div>
</section>

<style>
  @media (max-width: 1100px) {
    .offers-layout {
      grid-template-columns: 1fr !important;
    }

    .offers-events {
      order: -1;
    }
  }

  @media (max-width: 640px) {
    .offer-card {
      border-radius: 10px !important;
    }

    .offer-image-wrap {
      max-height: 140px !important;
    }

    .offer-image-wrap img {
      max-height: 140px !important;
    }

    .offer-actions a {
      width: 100%;
    }
  }
</style>
@endsection
