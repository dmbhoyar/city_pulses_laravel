@extends('layouts.app')

@section('content')
@php
  $publicPageUrl = $shop->id
    ? route('shops.idcard.public', ['publicSlug' => $shop->public_page_slug])
    : route('myservice');
  $pageUrlEncoded = urlencode($publicPageUrl);
  $shareText = 'Check my service business card: ' . $publicPageUrl;
  $shareTextEncoded = urlencode($shareText);
  $shareWhatsappUrl = "https://wa.me/?text={$shareTextEncoded}";
  $shareFacebookUrl = "https://www.facebook.com/sharer/sharer.php?u={$pageUrlEncoded}";
  $shareXUrl = "https://twitter.com/intent/tweet?url={$pageUrlEncoded}&text={$shareTextEncoded}";
  $shareTelegramUrl = "https://t.me/share/url?url={$pageUrlEncoded}&text={$shareTextEncoded}";
  $cfg = is_array($shop->page_config ?? null) ? $shop->page_config : [];
  $tc = $cfg['template_content'] ?? [];
  $owner = $shop->user ?? auth()->user();
  $providerName = trim((string)($tc['provider_name'] ?? optional($owner)->full_name ?? 'Service Provider'));
  $providerTitle = trim((string)($tc['provider_title'] ?? 'Founder & Lead Service Expert'));
  $providerPhoto = trim((string)($tc['provider_photo'] ?? ''));
  $providerPhotoUrl = '';
  if ($providerPhoto !== '') {
      $providerPhotoUrl = \Illuminate\Support\Str::startsWith($providerPhoto, ['http://', 'https://', 'data:', '/'])
          ? $providerPhoto
          : \Illuminate\Support\Facades\Storage::url($providerPhoto);
  }
  $providerContact = trim((string)($tc['provider_contact'] ?? $shop->phone ?? optional($owner)->mobile_number ?? ''));
@endphp
<style>
  .bc-preview{position:relative;max-width:560px;aspect-ratio:1.58/1;border-radius:22px;padding:18px 18px 16px;color:#fff;background:linear-gradient(145deg,#182a46,#314f77 55%,#4a90d9);border:1px solid rgba(74,144,217,.35);box-shadow:0 18px 42px rgba(26,26,46,.18);overflow:hidden;display:flex;flex-direction:column;justify-content:space-between}
  .bc-preview::after{content:'';position:absolute;width:200px;height:200px;border-radius:50%;right:-80px;top:-70px;background:radial-gradient(circle,rgba(255,255,255,.25) 0%,rgba(255,255,255,0) 70%)}
  .bc-head{display:grid;grid-template-columns:84px 1fr;gap:14px;align-items:center;position:relative;z-index:1}
  .bc-photo,.bc-fallback{width:84px;height:84px;border-radius:22px;border:3px solid rgba(255,255,255,.45)}
  .bc-photo{object-fit:cover;background:linear-gradient(135deg,#d6e4fb,#bdd5f5)}
  .bc-fallback{display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#6bb3ff,#8f71ff);font-size:32px;font-weight:800}
  .bc-name{font-family:'Playfair Display',serif;font-size:20px;line-height:1.05;margin-bottom:4px}
  .bc-role{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#d6ebff;font-weight:700}
  .bc-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;position:relative;z-index:1}
  .bc-box{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.24);border-radius:12px;padding:9px 10px;min-width:0}
  .bc-box strong{display:block;color:#d7e7ff;font-size:11px;letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px}
  .bc-box span{display:block;font-size:12px;line-height:1.35;word-break:break-word}
  .bc-box.full{grid-column:1/-1}
  @media (max-width: 700px){.bc-preview{max-width:100%;aspect-ratio:auto}.bc-meta{grid-template-columns:1fr}}
</style>
<div class="panel">
  <h1 style="margin:0">Service Business Card</h1>
  <p style="margin-top:8px">Configure your details, preview the card, and share instantly.</p>

  <form action="{{ route('business_card_myservice_save') }}" method="POST" style="margin-top:12px">
    @csrf
    <div class="form-row">
      <label for="services_list">Services (one per line)</label>
      <textarea id="services_list" name="services_list" rows="5">{{ old('services_list', implode("\n", $shop->page_config['services_list'] ?? [])) }}</textarea>
    </div>
    <div class="form-row">
      <button type="submit" class="toggle-btn">Save</button>
      <button type="button" class="button" onclick="window.print()">Download PDF</button>
    </div>
  </form>

  <div class="card" style="margin-top:12px">
    <h3 style="margin:0 0 8px">Preview</h3>
    <div class="bc-preview">
      <div class="bc-head">
        @if($providerPhotoUrl)
          <img src="{{ $providerPhotoUrl }}" alt="{{ $providerName }}" class="bc-photo">
        @else
          <div class="bc-fallback">{{ strtoupper(substr($providerName, 0, 1)) }}</div>
        @endif
        <div>
          <div class="bc-name">{{ $providerName }}</div>
          <div class="bc-role">{{ $providerTitle }}</div>
        </div>
      </div>
      <div class="bc-meta">
        <div class="bc-box"><strong>Service</strong><span>{{ $shop->name ?? 'My Service' }}</span></div>
        <div class="bc-box"><strong>Contact</strong><span>{{ $providerContact ?: 'Not set' }}</span></div>
        <div class="bc-box full"><strong>Area</strong><span>{{ $shop->address ?? 'Address not set' }}</span></div>
        @if(!empty($shop->page_config['services_list']))
          <div class="bc-box full"><strong>Services</strong><span>{{ implode(' • ', array_slice($shop->page_config['services_list'], 0, 4)) }}</span></div>
        @endif
      </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px">
      <a class="button" target="_blank" href="{{ $shareWhatsappUrl }}">WhatsApp</a>
      <a class="button" target="_blank" href="{{ $shareFacebookUrl }}">Facebook</a>
      <a class="button" target="_blank" href="{{ $shareXUrl }}">X</a>
      <a class="button" target="_blank" href="{{ $shareTelegramUrl }}">Telegram</a>
      <button type="button" class="button" id="bc-copy-link" data-url="{{ $publicPageUrl }}">Copy Link</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const copyBtn = document.getElementById('bc-copy-link');
  if (!copyBtn) return;
  copyBtn.addEventListener('click', async function(){
    const url = copyBtn.getAttribute('data-url') || window.location.href;
    try {
      await navigator.clipboard.writeText(url);
      const prev = copyBtn.textContent;
      copyBtn.textContent = '✓ Copied';
      setTimeout(() => copyBtn.textContent = prev, 1200);
    } catch (err) {
      window.prompt('Copy this link:', url);
    }
  });
});
</script>
@endsection
