@extends('layouts.app')

@section('title', __('ui.services'))

@section('content')
<style>
  .svc-container{display:flex;flex-direction:column;gap:16px;}
  .svc-header{border:1px solid #d7deec;border-radius:16px;background:linear-gradient(135deg,#eef4ff,#ffffff);padding:16px 18px;}
  .svc-header h1{margin:0;color:#1f3658;font-size:34px;line-height:1.15;font-weight:800;}
  .svc-header p{margin:8px 0 0;color:#4d6484;font-size:14px;}
  .svc-empty{border:1px dashed #c9d2e4;border-radius:16px;background:#fff;padding:40px;text-align:center;color:#5f7393;}
  .svc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:14px;}
  .svc-card{display:block;border-radius:18px;background:linear-gradient(135deg,#2d4468,#385781);padding:16px;border:1px solid #46648f;box-shadow:0 12px 24px rgba(22,36,61,.24);text-decoration:none;color:#fff;transition:all .3s;}
  .svc-card:hover{transform:translateY(-4px);box-shadow:0 16px 32px rgba(22,36,61,.32);}
  .svc-card-head{display:flex;align-items:flex-start;gap:12px;}
  .svc-card-avatar{width:62px;height:62px;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center;color:#2b466f;font-size:28px;flex-shrink:0;}
  .svc-card-info{min-width:0;}
  .svc-card-info h2{margin:0;font-size:29px;line-height:1.1;font-weight:800;letter-spacing:.2px;color:#fff;}
  .svc-card-info p{margin:4px 0 0;font-size:11px;font-weight:700;letter-spacing:.8px;opacity:.9;text-transform:uppercase;}
  .svc-card-desc{margin:8px 0 0;font-size:12px;line-height:1.35;opacity:.92;color:#d9e7ff;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
  .svc-card-tags{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;}
  .svc-tag{padding:4px 10px;border:1px solid rgba(255,255,255,.28);border-radius:999px;font-size:11px;font-weight:700;background:rgba(255,255,255,.08);}
  .svc-card-details{margin-top:12px;border:1px solid rgba(255,255,255,.22);border-radius:12px;padding:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px;}
  .svc-detail-item{border:1px solid rgba(255,255,255,.26);border-radius:10px;padding:7px 8px;background:rgba(255,255,255,.06);}
  .svc-detail-label{font-size:10px;letter-spacing:.8px;text-transform:uppercase;color:#d7e8ff;font-weight:700;}
  .svc-detail-value{font-size:12px;color:#fff;font-weight:700;margin-top:2px;}
  .svc-card-cta{margin-top:10px;font-size:12px;font-weight:700;color:#d7ebff;}

  @media(max-width:1024px){
    .svc-grid{grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;}
  }

  @media(max-width:768px){
    .svc-grid{grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;}
    .svc-header h1{font-size:28px;}
    .svc-card{padding:14px;}
    .svc-card-avatar{width:56px;height:56px;font-size:24px;}
    .svc-card-info h2{font-size:24px;}
    .svc-card-desc{font-size:11px;line-height:1.3;}
    .svc-card-details{padding:8px;gap:6px;}
    .svc-detail-item{padding:6px;}
    .svc-detail-label{font-size:9px;}
    .svc-detail-value{font-size:11px;}
  }

  @media(max-width:640px){
    .svc-grid{grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;}
    .svc-header h1{font-size:24px;}
    .svc-header p{font-size:13px;}
    .svc-card{padding:12px;}
    .svc-card-head{gap:10px;}
    .svc-card-avatar{width:50px;height:50px;font-size:22px;}
    .svc-card-info h2{font-size:20px;line-height:1;}
    .svc-card-info p{font-size:10px;}
    .svc-card-desc{font-size:10px;-webkit-line-clamp:1;}
    .svc-tag{padding:3px 8px;font-size:10px;}
    .svc-card-details{margin-top:8px;padding:6px;gap:4px;}
    .svc-detail-item{padding:5px;}
    .svc-detail-label{font-size:8px;}
    .svc-detail-value{font-size:10px;margin-top:1px;}
    .svc-card-cta{font-size:11px;margin-top:8px;}
  }

  @media(max-width:480px){
    .svc-grid{grid-template-columns:1fr;gap:8px;}
    .svc-header h1{font-size:20px;}
    .svc-card{padding:10px;}
    .svc-card-avatar{width:48px;height:48px;font-size:20px;}
    .svc-card-info h2{font-size:18px;}
    .svc-card-details{grid-template-columns:1fr;gap:4px;}
  }
</style>

<section class="svc-container">
  <div class="svc-header">
    <h1>{{ __('ui.public_service_directory') }}</h1>
    <p>{{ __('ui.service_providers_for', ['city' => $city?->name ?? __('ui.selected_city')]) }} {{ __('ui.only_active_subscribed_cards') }}</p>
  </div>

  @if ($services->isEmpty())
    <div class="svc-empty">
      {{ __('ui.no_active_service_cards_city', ['city' => $city?->name ?? __('ui.this_city')]) }}
    </div>
  @else
    <div class="svc-grid">
      @foreach($services as $service)
        @php
          $cfg = is_array($service->page_config) ? $service->page_config : [];
          $templateContent = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];
          $heroTitle = trim((string) ($templateContent['hero_title'] ?? '')) ?: ($service->name ?: __('ui.service_provider'));
          $heroDescription = trim((string) ($templateContent['hero_description'] ?? ''));
          $providerName = trim((string) ($cfg['provider_name'] ?? '')) ?: ($service->name ?: __('ui.provider'));
          $providerContact = trim((string) ($cfg['provider_contact'] ?? ''));
          $experience = trim((string) ($cfg['experience_years'] ?? ''));
          $serviceArea = $service->city?->name ?: ($city?->name ?: __('ui.city_area'));
        @endphp

        <a href="{{ route('services.show', $service->public_page_slug) }}" class="svc-card">
          <div class="svc-card-head">
            <div class="svc-card-avatar">👨‍💼</div>
            <div class="svc-card-info">
              <h2>{{ $providerName }}</h2>
              <p>{{ __('ui.founder_and_expert') }}</p>
              @if($heroDescription !== '')
                <p class="svc-card-desc">{{ \Illuminate\Support\Str::limit($heroDescription, 62) }}</p>
              @endif
            </div>
          </div>

          <div class="svc-card-tags">
            <span class="svc-tag">{{ $experience !== '' ? $experience : __('ui.not_available') }}</span>
            <span class="svc-tag">{{ $serviceArea }}</span>
            <span class="svc-tag">{{ __('ui.issued_on_date', ['date' => now()->format('d M Y')]) }}</span>
          </div>

          <div class="svc-card-details">
            <div class="svc-detail-item">
              <div class="svc-detail-label">{{ __('ui.id_card') }}</div>
              <div class="svc-detail-value">{{ \Illuminate\Support\Str::limit($heroTitle, 26) }}</div>
            </div>
            <div class="svc-detail-item">
              <div class="svc-detail-label">{{ __('ui.contact') }}</div>
              <div class="svc-detail-value">{{ $providerContact !== '' ? $providerContact : __('ui.not_available') }}</div>
            </div>
          </div>

          <div class="svc-card-cta">{{ __('ui.tap_card_open_details_reviews') }} →</div>
        </a>
      @endforeach
    </div>

    <div style="margin-top:4px;">
      {{ $services->links() }}
    </div>
  @endif
</section>
@endsection
