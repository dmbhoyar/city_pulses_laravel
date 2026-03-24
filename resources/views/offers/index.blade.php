@extends('layouts.app')

@section('content')
<section style="display:flex;flex-direction:column;gap:16px;">
  <div style="border:1px solid #3f5f8f;background:linear-gradient(135deg,#2c4468,#3f5f8f);border-radius:16px;padding:16px;box-shadow:0 12px 24px rgba(20,34,58,.22);color:#fff;">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
      <div>
        <p style="margin:0;font-size:11px;letter-spacing:.9px;text-transform:uppercase;color:#d8e8ff;font-weight:700;">{{ __('ui.home_section') }}</p>
        <h1 style="margin:4px 0 0;font-size:30px;line-height:1.1;color:#fff;font-weight:800;">{{ __('ui.offers_benefits') }}</h1>
        <p style="margin:8px 0 0;font-size:13px;color:#e2ecff;max-width:760px;">{{ __('ui.offers_intro') }}</p>
      </div>
      <div style="border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.14);border-radius:12px;padding:10px 12px;min-width:210px;">
        <p style="margin:0;color:#fff;font-size:12px;"><strong style="font-weight:800;">{{ __('ui.date') }}:</strong> {{ $today->format('d M Y') }}</p>
        <p style="margin:6px 0 0;color:#fff;font-size:12px;"><strong style="font-weight:800;">{{ __('ui.showing') }}:</strong> {{ isset($city) ? city_display_name($city->name) : __('ui.all_cities') }}</p>
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
            $ownerLabel = str_contains($ownerRole, 'shop') ? __('ui.shop_owner') : __('ui.service_provider');
            $photoPath = trim((string) ($o->photo_path ?? ''));
            $isAbsolutePhoto = str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://') || str_starts_with($photoPath, '/storage/');
            $photoUrl = $photoPath === '' ? '' : ($isAbsolutePhoto ? $photoPath : asset('storage/' . ltrim($photoPath, '/')));
          @endphp

          <article class="offer-card" style="overflow:hidden;border:1px solid #d6deeb;border-radius:12px;background:#fff;box-shadow:0 4px 10px rgba(25,40,68,.06);">
            <div style="background:{{ $gradient }};padding:10px 12px;color:#fff;">
              <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:flex-start;">
                <div>
                  <p style="margin:0;font-size:11px;letter-spacing:.8px;text-transform:uppercase;font-weight:700;color:#deecff;">{{ __('ui.offer_benefit') }}</p>
                  <h2 style="margin:4px 0 0;font-size:16px;font-weight:800;line-height:1.2;">{{ $o->title }}</h2>
                </div>
                <span style="padding:5px 9px;border:1px solid rgba(255,255,255,.35);border-radius:999px;background:rgba(255,255,255,.14);font-size:11px;font-weight:700;">{{ $ownerLabel }}</span>
              </div>
            </div>

            <div style="padding:10px 12px;">
              @if($photoUrl !== '')
                <div class="offer-image-wrap" style="width:100%;margin:0 0 9px;max-height:180px;overflow:hidden;border-radius:10px;border:1px solid #d9e4f6;background:#edf3ff;">
                  <img src="{{ $photoUrl }}" alt="{{ __('ui.offer_photo_alt') }}" style="display:block;width:100%;height:100%;max-height:180px;object-fit:cover;">
                </div>
              @endif

              <div style="display:flex;flex-wrap:wrap;gap:8px 12px;color:#5f7494;font-size:12px;">
                <span>{{ optional($o->created_at)->format('d M Y') }}</span>
                @if($o->city)
                  <span>• {{ city_display_name($o->city->name) }}</span>
                @endif
                @if($o->shop)
                  <span>• {{ $o->shop->name ?: __('ui.profile') }}</span>
                @endif
              </div>

              @if(trim((string) $o->content) !== '')
                <p style="margin:8px 0 0;font-size:12px;color:#2f4a70;line-height:1.45;">{{ truncate_text(strip_tags($o->content ?? ''), 180) }}</p>
              @endif

              @if($o->shop || $o->source_url)
                <div class="offer-actions" style="margin-top:10px;display:flex;gap:7px;flex-wrap:wrap;">
                  @if($o->shop)
                    <a href="{{ route('shops.public', ['publicSlug' => $o->shop->public_page_slug]) }}" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 11px;border-radius:10px;background:#2f4f77;color:#fff;font-size:12px;font-weight:800;text-decoration:none;">{{ __('ui.open_public_page') }}</a>
                    <a href="{{ route('shops.idcard.public', ['publicSlug' => $o->shop->public_page_slug]) }}" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 11px;border-radius:10px;border:1px solid #c7d5ea;background:#f7fbff;color:#2b4569;font-size:12px;font-weight:800;text-decoration:none;">{{ __('ui.view_id_card') }}</a>
                  @endif
                  @if($o->source_url)
                    <a href="{{ $o->source_url }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 11px;border-radius:10px;border:1px solid #d3ddea;background:#fff;color:#2f4a71;font-size:12px;font-weight:700;text-decoration:none;">{{ __('ui.offer_link') }}</a>
                  @endif
                </div>
              @endif
            </div>
          </article>
        @endforeach
      @else
        <div style="border:1px dashed #c7d4e8;border-radius:14px;background:#fff;padding:22px;text-align:center;color:#5f7697;font-size:14px;font-weight:600;">
          {{ __('ui.no_current_offers_for_city', ['city' => isset($city) ? city_display_name($city->name) : __('ui.selected_city')]) }}
        </div>
      @endif
    </section>

    <aside class="offers-events" style="min-width:0;">
      <section style="border:1px solid #d7deec;border-radius:14px;background:#fff;padding:14px;">
        <h3 style="margin:0;color:#243d61;font-size:20px;font-weight:800;">{{ __('ui.todays_events') }}</h3>
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
          <p style="margin:8px 0 0;color:#637b9d;font-size:13px;">{{ __('ui.no_events_listed_today') }}</p>
        @endif
      </section>
    </aside>
  </div>
</section>

<style>
  .offers-section { padding: 0; }
  .offers-layout { display: grid; grid-template-columns: minmax(0, 2.2fr) minmax(260px, 1fr); gap: 12px; align-items: start; }
  
  @media (max-width: 1200px) {
    .offers-layout {
      grid-template-columns: 1fr;
      gap: 14px;
    }
    .offers-events {
      order: -1;
    }
  }

  @media (max-width: 1000px) {
    .offers-list { gap: 12px; }
  }

  @media (max-width: 768px) {
    .offers-layout { gap: 12px; }
    // Main header adjustments
    h1 { font-size: clamp(24px, 5vw, 30px) !important; }
    .offer-card { border-radius: 12px !important; }
    .offer-image-wrap { max-height: 160px !important; }
    .offer-image-wrap img { max-height: 160px !important; }
    // Typography
    h2 { font-size: 15px !important; }
    p { font-size: 12px !important; }
    // Actions
    .offer-actions a { 
      width: auto; 
      padding: 7px 10px !important; 
      font-size: 11px !important;
    }
  }

  @media (max-width: 640px) {
    section { gap: 10px !important; }
    .offers-layout { gap: 10px; }
    
    // Header card
    div[style*="border:1px solid #3f5f8f"] { 
      padding: 12px !important;
      border-radius: 12px !important;
    }
    
    // Offer cards
    .offer-card { 
      border-radius: 10px !important;
      box-shadow: 0 2px 6px rgba(25,40,68,.08) !important;
    }
    
    // Offer header (gradient section)
    div[style*="background:linear-gradient"] { 
      padding: 9px 10px !important; 
    }
    
    // Offer body
    div[style*="padding:10px 12px"] { 
      padding: 9px 10px !important; 
    }
    
    .offer-image-wrap { 
      max-height: 140px !important;
      margin: 0 0 8px !important;
      border-radius: 8px !important;
    }
    
    .offer-image-wrap img { 
      max-height: 140px !important; 
    }
    
    h2 { font-size: 14px !important; line-height: 1.2 !important; }
    p { font-size: 11px !important; line-height: 1.4 !important; }
    
    .offer-actions { 
      margin-top: 8px !important;
      gap: 5px 8px !important;
      flex-wrap: wrap;
    }
    
    .offer-actions a { 
      padding: 6px 9px !important; 
      font-size: 10px !important;
      border-radius: 8px !important;
    }
    
    // Events sidebar
    section[style*="border:1px solid #d7deec"] { 
      padding: 12px !important;
      border-radius: 12px !important;
    }
    
    section h3 { font-size: 18px !important; }
  }

  @media (max-width: 480px) {
    section { gap: 8px !important; }
    .offers-layout { gap: 8px; }
    
    // Main header
    div[style*="border:1px solid #3f5f8f"] { 
      padding: 10px !important;
      flex-direction: column !important;
      gap: 10px !important;
    }
    
    h1 { font-size: clamp(20px, 4vw, 24px) !important; margin: 2px 0 0 !important; }
    p { font-size: 10px !important; }
    
    // Offer cards
    .offer-card { border: 1px solid #dbe3f0 !important; }
    
    div[style*="background:linear-gradient"] { 
      padding: 8px 9px !important;
      flex-direction: column !important;
    }
    
    // Offer header row
    div[style*="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap"] {
      flex-direction: column !important;
      gap: 6px !important;
    }
    
    h2 { font-size: 13px !important; margin: 2px 0 0 !important; }
    
    div[style*="padding:8px 11px"] { 
      padding: 4px 7px !important;
      font-size: 9px !important;
    }
    
    div[style*="padding:10px 12px"] { 
      padding: 8px 9px !important; 
    }
    
    .offer-image-wrap { 
      max-height: 120px !important;
      margin: 0 0 7px !important;
      border-radius: 6px !important;
    }
    
    .offer-image-wrap img { max-height: 120px !important; }
    
    .offer-actions { 
      margin-top: 7px !important;
      gap: 4px 6px !important;
    }
    
    .offer-actions a { 
      flex: 1;
      padding: 5px 8px !important; 
      font-size: 9px !important;
      border-radius: 6px !important;
      text-align: center;
    }
    
    // Events
    section[style*="border:1px solid #d7deec"] { 
      padding: 10px !important;
      border-radius: 10px !important;
    }
    
    section h3 { font-size: 16px !important; margin: 0 !important; }
    
    article[style*="border:1px solid #d9e3f3"] { 
      border-radius: 8px !important;
      padding: 8px !important;
    }
  }

  @media (max-width: 360px) {
    h1 { font-size: clamp(18px, 3vw, 20px) !important; }
    h2 { font-size: 12px !important; }
    .offer-actions a { font-size: 8px !important; padding: 4px 6px !important; }
  }
</style>
@endsection
