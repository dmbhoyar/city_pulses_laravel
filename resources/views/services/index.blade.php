@extends('layouts.app')

@section('title', 'Services')

@section('content')
<section style="display:flex;flex-direction:column;gap:16px;">
  <div style="border:1px solid #d7deec;border-radius:16px;background:linear-gradient(135deg,#eef4ff,#ffffff);padding:16px 18px;">
    <h1 style="margin:0;color:#1f3658;font-size:34px;line-height:1.15;font-weight:800;">Public Service Directory</h1>
    <p style="margin:8px 0 0;color:#4d6484;font-size:14px;">Showing service providers for <strong>{{ $city?->name ?? 'selected city' }}</strong>. Only active and subscribed provider ID cards are listed.</p>
  </div>

  @if ($services->isEmpty())
    <div style="border:1px dashed #c9d2e4;border-radius:16px;background:#fff;padding:40px;text-align:center;color:#5f7393;">
      No active service provider cards available for {{ $city?->name ?? 'this city' }}.
    </div>
  @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:14px;">
      @foreach($services as $service)
        @php
          $cfg = is_array($service->page_config) ? $service->page_config : [];
          $templateContent = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];
          $heroTitle = trim((string) ($templateContent['hero_title'] ?? '')) ?: ($service->name ?: 'Service Provider');
          $heroDescription = trim((string) ($templateContent['hero_description'] ?? ''));
          $providerName = trim((string) ($cfg['provider_name'] ?? '')) ?: ($service->name ?: 'Provider');
          $providerContact = trim((string) ($cfg['provider_contact'] ?? ''));
          $experience = trim((string) ($cfg['experience_years'] ?? ''));
          $serviceArea = $service->city?->name ?: ($city?->name ?: 'City Area');
        @endphp

        <a href="{{ route('services.show', $service->public_page_slug) }}" style="display:block;border-radius:18px;background:linear-gradient(135deg,#2d4468,#385781);padding:16px;border:1px solid #46648f;box-shadow:0 12px 24px rgba(22,36,61,.24);text-decoration:none;color:#fff;">
          <div style="display:flex;align-items:flex-start;gap:12px;">
            <div style="width:62px;height:62px;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center;color:#2b466f;font-size:28px;flex-shrink:0;">👨‍💼</div>
            <div style="min-width:0;">
              <h2 style="margin:0;font-size:29px;line-height:1.1;font-weight:800;letter-spacing:.2px;color:#fff;">{{ $providerName }}</h2>
              <p style="margin:4px 0 0;font-size:11px;font-weight:700;letter-spacing:.8px;opacity:.9;text-transform:uppercase;">Founder and Expert</p>
              @if($heroDescription !== '')
                <p style="margin:8px 0 0;font-size:12px;line-height:1.35;opacity:.92;color:#d9e7ff;">{{ \Illuminate\Support\Str::limit($heroDescription, 62) }}</p>
              @endif
            </div>
          </div>

          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
            <span style="padding:4px 10px;border:1px solid rgba(255,255,255,.28);border-radius:999px;font-size:11px;font-weight:700;background:rgba(255,255,255,.08);">{{ $experience !== '' ? $experience : 'N/A' }}</span>
            <span style="padding:4px 10px;border:1px solid rgba(255,255,255,.28);border-radius:999px;font-size:11px;font-weight:700;background:rgba(255,255,255,.08);">{{ $serviceArea }}</span>
            <span style="padding:4px 10px;border:1px solid rgba(255,255,255,.28);border-radius:999px;font-size:11px;font-weight:700;background:rgba(255,255,255,.08);">Issued {{ now()->format('d M Y') }}</span>
          </div>

          <div style="margin-top:12px;border:1px solid rgba(255,255,255,.22);border-radius:12px;padding:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div style="border:1px solid rgba(255,255,255,.26);border-radius:10px;padding:7px 8px;background:rgba(255,255,255,.06);">
              <div style="font-size:10px;letter-spacing:.8px;text-transform:uppercase;color:#d7e8ff;font-weight:700;">ID CARD</div>
              <div style="font-size:12px;color:#fff;font-weight:700;margin-top:2px;">{{ \Illuminate\Support\Str::limit($heroTitle, 26) }}</div>
            </div>
            <div style="border:1px solid rgba(255,255,255,.26);border-radius:10px;padding:7px 8px;background:rgba(255,255,255,.06);">
              <div style="font-size:10px;letter-spacing:.8px;text-transform:uppercase;color:#d7e8ff;font-weight:700;">CONTACT</div>
              <div style="font-size:12px;color:#fff;font-weight:700;margin-top:2px;">{{ $providerContact !== '' ? $providerContact : 'N/A' }}</div>
            </div>
          </div>

          <div style="margin-top:10px;font-size:12px;font-weight:700;color:#d7ebff;">Tap card to open details & reviews →</div>
        </a>
      @endforeach
    </div>

    <div style="margin-top:4px;">
      {{ $services->links() }}
    </div>
  @endif
</section>
@endsection
