<!DOCTYPE html>
<html lang="en">
<head>
@php
  $cfg = is_array($shop->page_config ?? null) ? $shop->page_config : [];
  $tc = $cfg['template_content'] ?? [];
  $owner = $shop->user ?? auth()->user();
  $providerName = trim((string)($tc['provider_name'] ?? optional($owner)->full_name ?? 'Service Provider'));
  $providerTitle = trim((string)($tc['provider_title'] ?? 'Founder & Lead Service Expert'));
  $providerEmail = trim((string)($tc['provider_email'] ?? optional($owner)->email ?? ''));
  $providerContact = trim((string)($tc['provider_contact'] ?? $shop->phone ?? optional($owner)->mobile_number ?? ''));
  $providerContactDigits = preg_replace('/\D+/', '', $providerContact);
  $providerCallUrl = $providerContactDigits ? ('tel:' . $providerContactDigits) : '';
  $providerWhatsappUrl = $providerContactDigits ? ('https://wa.me/' . $providerContactDigits) : '';
  $providerAge = trim((string)($tc['provider_age'] ?? ''));
  $providerPhoto = trim((string)($tc['provider_photo'] ?? ''));
  $providerPhotoUrl = '';
  if ($providerPhoto !== '') {
      $providerPhotoUrl = \Illuminate\Support\Str::startsWith($providerPhoto, ['http://', 'https://', 'data:', '/'])
          ? $providerPhoto
          : \Illuminate\Support\Facades\Storage::url($providerPhoto);
  }
  $providerBio = trim((string)($tc['provider_bio'] ?? 'Experienced local professional dedicated to reliable and customer-friendly service.'));
  $providerExperience = trim((string)($tc['provider_experience'] ?? '5+ Years Experience'));
  $publicCardUrl = route('shops.idcard.public', ['publicSlug' => $shop->public_page_slug]);
  $pageUrlEncoded = urlencode($publicCardUrl);
  $shareText = 'Check my service ID card: ' . $publicCardUrl;
  $shareTextEncoded = urlencode($shareText);
  $shareWhatsappUrl = "https://wa.me/?text={$shareTextEncoded}";
  $shareFacebookUrl = "https://www.facebook.com/sharer/sharer.php?u={$pageUrlEncoded}";
  $shareXUrl = "https://twitter.com/intent/tweet?url={$pageUrlEncoded}&text={$shareTextEncoded}";
  $shareTelegramUrl = "https://t.me/share/url?url={$pageUrlEncoded}&text={$shareTextEncoded}";
  $ownerCanManageCard = auth()->check() && auth()->id() === $shop->user_id && auth()->user()->isServiceProvider();
@endphp

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $providerName }} | ID Card</title>
<meta name="description" content="Service provider ID card for {{ $providerName }}">
<meta property="og:title" content="{{ $providerName }} | ID Card">
<meta property="og:description" content="View service provider ID card for {{ $providerName }}.">
<meta property="og:url" content="{{ $publicCardUrl }}">
<meta property="og:type" content="website">
<link rel="canonical" href="{{ $publicCardUrl }}">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'DM Sans',sans-serif;background:linear-gradient(180deg,#f7f8fc 0%,#eef4fb 100%);color:#18243a}
  .id-shell{min-height:100vh;padding:28px 20px;display:grid;gap:16px;justify-items:center}
  .id-wrap{width:min(100%,560px);display:grid;gap:14px;justify-items:start}
  .id-actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center;align-content:flex-start;justify-content:flex-start;width:100%}
  .id-actions .a-btn{display:inline-flex;flex:0 0 auto;align-items:center;justify-content:center;gap:7px;min-height:42px;padding:10px 14px;border-radius:12px;border:1px solid #d6e3f5;background:#fff;color:#2f4e74;font-weight:700;font-size:13px;line-height:1;white-space:nowrap;text-decoration:none;cursor:pointer;box-shadow:0 8px 24px rgba(47,78,116,.08);writing-mode:horizontal-tb}
  .id-actions .a-btn.primary{background:linear-gradient(135deg,#2f4e74,#4a90d9);border-color:transparent;color:#fff}
  .provider-card{position:relative;background:linear-gradient(145deg,#182a46,#314f77 55%,#4a90d9);border:1px solid rgba(74,144,217,.35);border-radius:22px;padding:18px 18px 16px;color:#fff;box-shadow:0 18px 42px rgba(26,26,46,.18);overflow:hidden;width:100%;display:flex;flex-direction:column;gap:12px}
  .provider-card::after{content:'';position:absolute;width:200px;height:200px;border-radius:50%;right:-80px;top:-70px;background:radial-gradient(circle,rgba(255,255,255,.25) 0%,rgba(255,255,255,0) 70%)}
  .provider-top{display:grid;grid-template-columns:84px 1fr;gap:14px;align-items:center;position:relative;z-index:1}
  .provider-photo{width:84px;height:84px;border-radius:22px;object-fit:cover;background:linear-gradient(135deg,#d6e4fb,#bdd5f5);display:block;border:3px solid rgba(255,255,255,.5)}
  .provider-fallback{width:84px;height:84px;border-radius:22px;background:linear-gradient(135deg,#6bb3ff,#8f71ff);color:#fff;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;border:3px solid rgba(255,255,255,.45)}
  .provider-main{min-width:0}
  .provider-name{font-family:'Playfair Display',serif;font-size:20px;line-height:1.06;margin-bottom:4px;text-shadow:0 4px 16px rgba(0,0,0,.18)}
  .provider-role{font-size:11px;color:#d6ebff;font-weight:700;margin-bottom:8px;letter-spacing:.08em;text-transform:uppercase}
  .provider-bio{font-size:12px;color:rgba(255,255,255,.86);line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .provider-chip-row{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px;position:relative;z-index:1}
  .provider-chip{display:inline-flex;align-items:center;padding:5px 9px;border-radius:999px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.26);font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase}
  .provider-details{margin-top:12px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;position:relative;z-index:1}
  .provider-detail{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.24);border-radius:12px;padding:9px 10px;min-width:0}
  .provider-detail strong{display:block;color:#d7e7ff;font-size:11px;letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px}
  .provider-detail span,.provider-detail a{font-size:12px;color:#fff;text-decoration:none;word-break:break-word;display:block;line-height:1.35}
  .provider-detail.contact{grid-column:span 2}
  .provider-detail.email,.provider-detail.area{grid-column:span 2}
  .contact-number{font-weight:700;color:#fff;display:block;margin-bottom:8px}
  .contact-actions{display:flex;gap:6px;flex-wrap:wrap}
  .contact-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;border:1px solid transparent}
  .contact-btn .ci{width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px}
  .contact-btn.call{background:#eef5ff;color:#2f4e74;border-color:#c7d9f3}
  .contact-btn.call .ci{background:#2f4e74;color:#fff}
  .contact-btn.wa{background:#eafaf1;color:#0f7a43;border-color:#b9e9ce}
  .contact-btn.wa .ci{background:#17a851;color:#fff}
  .card-caption{font-size:13px;color:#7186a8;text-align:center;width:100%}
  @media (max-width: 900px){ .provider-details{grid-template-columns:repeat(2,minmax(0,1fr))}.provider-detail.email,.provider-detail.area,.provider-detail.contact{grid-column:1/-1} }
  @media (max-width: 560px){ .id-actions .a-btn{min-height:40px;padding:9px 12px;font-size:12px}.provider-top{grid-template-columns:72px 1fr}.provider-photo,.provider-fallback{width:72px;height:72px;border-radius:18px}.provider-name{font-size:18px}.provider-details{grid-template-columns:1fr}.provider-detail.email,.provider-detail.area,.provider-detail.contact{grid-column:auto} }
  @media print {
    body{background:#fff !important}
    .id-actions,.card-caption{display:none !important}
    .id-shell{padding:0 !important;min-height:auto !important}
    .provider-card{box-shadow:none !important;border-radius:18px !important;min-height:auto !important}
    @page{size:auto;margin:8mm}
  }
</style>
</head>
<body>

<div class="id-shell">
  <div class="id-wrap">
    @if($ownerCanManageCard)
      <div class="id-actions">
        <button type="button" class="a-btn primary" id="id-print-card">⬇ Download PDF</button>
        <a href="{{ $shareWhatsappUrl }}" target="_blank" rel="noopener" class="a-btn">🟢 WhatsApp</a>
        <a href="{{ $shareFacebookUrl }}" target="_blank" rel="noopener" class="a-btn">📘 Facebook</a>
        <a href="{{ $shareXUrl }}" target="_blank" rel="noopener" class="a-btn">✖ X</a>
        <a href="{{ $shareTelegramUrl }}" target="_blank" rel="noopener" class="a-btn">📨 Telegram</a>
        <button type="button" class="a-btn" id="id-copy-link" data-url="{{ $publicCardUrl }}">🔗 Copy Link</button>
      </div>
    @endif

    <div class="provider-card" id="printable-id-card">
      <div class="provider-top">
        @if($providerPhotoUrl)
          <img src="{{ $providerPhotoUrl }}" alt="{{ $providerName }}" class="provider-photo">
        @else
          <div class="provider-fallback">{{ strtoupper(substr($providerName, 0, 1)) }}</div>
        @endif
        <div class="provider-main">
          <div class="provider-name">{{ $providerName }}</div>
          <div class="provider-role">{{ $providerTitle }}</div>
          <div class="provider-bio">{{ $providerBio }}</div>
        </div>
      </div>
      <div class="provider-chip-row">
        @if($providerExperience)<span class="provider-chip">{{ $providerExperience }}</span>@endif
        @if($shop->address)<span class="provider-chip">{{ $shop->address }}</span>@endif
        <span class="provider-chip">Issued {{ now()->format('d M Y') }}</span>
      </div>
      <div class="provider-details">
        @if($providerAge)<div class="provider-detail"><strong>Age</strong><span>{{ $providerAge }}</span></div>@endif
        @if($providerEmail)<div class="provider-detail email"><strong>Email</strong><a href="mailto:{{ $providerEmail }}">{{ $providerEmail }}</a></div>@endif
        @if($providerExperience)<div class="provider-detail"><strong>Experience</strong><span>{{ $providerExperience }}</span></div>@endif
        @if($shop->address)<div class="provider-detail area"><strong>Service Area</strong><span>{{ $shop->address }}</span></div>@endif
        @if($providerContact)
          <div class="provider-detail contact">
            <strong>Contact</strong>
            <span class="contact-number">{{ $providerContact }}</span>
            <div class="contact-actions">
              @if($providerCallUrl)
                <a href="{{ $providerCallUrl }}" class="contact-btn call"><span class="ci">📞</span>Call</a>
              @endif
              @if($providerWhatsappUrl)
                <a href="{{ $providerWhatsappUrl }}" target="_blank" rel="noopener" class="contact-btn wa"><span class="ci">💬</span>WhatsApp</a>
              @endif
            </div>
          </div>
        @endif
      </div>
    </div>

    @unless($ownerCanManageCard)
      <div class="card-caption">Public ID card view</div>
    @endunless
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const copyBtn = document.getElementById('id-copy-link');
  const printBtn = document.getElementById('id-print-card');

  if (copyBtn) {
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
  }

  if (printBtn) {
    printBtn.addEventListener('click', function(){
      const card = document.getElementById('printable-id-card');
      if (!card) return;

      const printWindow = window.open('', '_blank', 'width=900,height=700');
      if (!printWindow) {
        window.print();
        return;
      }

      printWindow.document.write(`
        <html>
          <head>
            <title>ID Card</title>
            <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>${document.querySelector('style').innerHTML}
              body{display:grid;place-items:center;min-height:100vh;padding:20px;background:#fff !important}
              .id-actions,.card-caption{display:none !important}
            </style>
          </head>
          <body>
            ${card.outerHTML}
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.focus();
      setTimeout(() => {
        printWindow.print();
        printWindow.close();
      }, 250);
    });
  }
});
</script>
</body>
</html>
