<!DOCTYPE html>
<html lang="en">
<head>
@php
  $cfg = is_array($shop->page_config ?? null) ? $shop->page_config : [];
  $tc = $cfg['template_content'] ?? [];
  $services = $cfg['services'] ?? [];
  $owner = $shop->user;
  $providerName = trim((string)($tc['provider_name'] ?? optional($owner)->full_name ?? 'सेवा प्रदाता'));
  $providerTitle = trim((string)($tc['provider_title'] ?? 'संस्थापक आणि प्रमुख सेवा तज्ञ'));
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
  $providerBio = trim((string)($tc['provider_bio'] ?? 'अनुभवी स्थानिक तज्ञ, विश्वासार्ह आणि ग्राहकाभिमुख सेवेसाठी समर्पित.'));
  $providerExperience = trim((string)($tc['provider_experience'] ?? '५+ वर्षांचा अनुभव'));
  $pageTitle = trim(($shop->name ?: 'सेवा') . ' | ' . $providerName);
  $pageDescription = trim(($shop->description ?: 'व्यावसायिक स्थानिक सेवा पेज') . ' — प्रदाता: ' . $providerName);
  $currentPageUrl = route('shops.public', ['publicSlug' => $shop->public_page_slug]);
  $ownerCanManagePage = auth()->check()
      && auth()->id() === $shop->user_id
      && (auth()->user()->isServiceProvider() || auth()->user()->isShopowner());
  $pageUrlEncoded = urlencode($currentPageUrl);
  $pageShareText = 'Check my page on AajchaOffer: ' . $currentPageUrl;
  $pageShareTextEncoded = urlencode($pageShareText);
  $shareWhatsappUrl = "https://wa.me/?text={$pageShareTextEncoded}";
  $shareFacebookUrl = "https://www.facebook.com/sharer/sharer.php?u={$pageUrlEncoded}";
  $shareXUrl = "https://twitter.com/intent/tweet?url={$pageUrlEncoded}&text={$pageShareTextEncoded}";
  $shareTelegramUrl = "https://t.me/share/url?url={$pageUrlEncoded}&text={$pageShareTextEncoded}";

  if (empty($services)) {
      $services = [[
          'icon' => '🛠️',
          'name' => $shop->name ?: 'General Service',
          'description' => $shop->description ?: 'Professional and reliable service for your area.',
          'price' => 'Contact for pricing',
          'url' => $currentPageUrl,
      ]];
  }

  $palette = [
      ['#1a0a3b', '#c97aff', '#f5c518'],
      ['#0c2340', '#2196f3', '#00e5ff'],
      ['#1a1200', '#ff6b00', '#ffd600'],
      ['#003050', '#00897b', '#80deea'],
      ['#1a0a14', '#e91e8c', '#ffb6c1'],
      ['#003320', '#00b96b', '#ffe066'],
  ];

  $serviceData = [];
  foreach ($services as $i => $svc) {
      $name = trim((string)($svc['name'] ?? 'Service ' . ($i + 1)));
      $key = \Illuminate\Support\Str::slug($name) ?: 'service-' . ($i + 1);
      [$color, $accent, $accent2] = $palette[$i % count($palette)];
      $serviceData[] = [
          'key' => $key,
          'name' => $name,
          'icon' => trim((string)($svc['icon'] ?? '🛠️')) ?: '🛠️',
          'description' => trim((string)($svc['description'] ?? $shop->description ?? '')),
          'price' => trim((string)($svc['price'] ?? 'Contact')),
          'url' => trim((string)($svc['url'] ?? $currentPageUrl)),
          'color' => $color,
          'accent' => $accent,
          'accent2' => $accent2,
      ];
  }
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $currentPageUrl }}">
<meta property="og:type" content="website">
<link rel="canonical" href="{{ $currentPageUrl }}">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --primary: #1a1a2e;
    --accent: #e94560;
    --accent2: #f5a623;
    --light: #f8f8f2;
    --gray: #6b7280;
    --card-bg: #ffffff;
    --section-bg: #f9f9f7;
    --border: #e5e7eb;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DM Sans', sans-serif; background: #f5f7fb; color: var(--primary); overflow-x: hidden; }
  a { color: inherit; }
  .page-shell{min-height:100vh;background:linear-gradient(180deg,#f7f8fc 0%,#eef4fb 100%)}
  .owner-actions{max-width:1280px;margin:0 auto;padding:18px 24px 0;display:flex;flex-wrap:wrap;gap:10px}
  .owner-actions .oa-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 14px;border-radius:10px;border:1px solid #d6e3f5;background:#fff;color:#2f4e74;font-weight:700;font-size:13px;text-decoration:none;cursor:pointer;box-shadow:0 8px 24px rgba(47,78,116,.08)}
  .owner-actions .oa-btn.primary{background:linear-gradient(135deg,#2f4e74,#4a90d9);border-color:transparent;color:#fff}
  .client-page{max-width:1280px;margin:18px auto 30px;background:#fff;border-radius:22px;overflow:hidden;box-shadow:0 22px 60px rgba(33,53,85,.12)}
  .switcher { background: var(--primary); padding: 10px 24px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; position: sticky; top: 0; z-index: 99; box-shadow: 0 2px 12px rgba(0,0,0,0.18); }
  .switcher span { color: #aaa; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; margin-right: 6px; white-space: nowrap; }
  .sw-btn { padding: 6px 16px; border-radius: 20px; border: 1.5px solid rgba(255,255,255,0.15); background: transparent; color: #ccc; font-size: 13px; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all .22s; white-space: nowrap; }
  .sw-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; font-weight: 600; }
  .hero { position: relative; padding: 86px 5% 64px; overflow: hidden; background:linear-gradient(135deg,#1a0a3b 0%, #2d1265 50%, #0e0625 100%); }
  .hero-pattern { position: absolute; inset: 0; opacity: .04; background-image: repeating-linear-gradient(45deg,#fff 0px,#fff 1px,transparent 1px,transparent 14px); z-index: 1; }
  .hero-content { position: relative; z-index: 2; max-width: 680px; }
  .hero-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); color: #fff; font-size: 12px; letter-spacing: .1em; text-transform: uppercase; padding: 5px 14px 5px 10px; border-radius: 30px; margin-bottom: 22px; }
  .hero-badge .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent2); }
  .hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(36px, 6vw, 66px); font-weight: 900; color: #fff; line-height: 1.08; margin-bottom: 18px; }
  .hero p { font-size: 17px; color: rgba(255,255,255,0.78); line-height: 1.7; margin-bottom: 34px; max-width: 480px; }
  .hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
  .btn-primary { padding: 14px 30px; background: var(--accent); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; text-decoration:none; }
  .btn-outline { padding: 14px 28px; background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,0.35); border-radius: 8px; font-size: 15px; text-decoration:none; }
  .hero-stats { display: flex; gap: 32px; margin-top: 48px; flex-wrap: wrap; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 28px; }
  .stat-num { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; color: #fff; }
  .stat-lbl { font-size: 12px; color: rgba(255,255,255,0.5); letter-spacing: .06em; text-transform: uppercase; margin-top: 2px; }
  section { padding: 72px 5%; }
  section.alt { background: var(--section-bg); }
  .section-label { font-size: 11px; letter-spacing: .14em; text-transform: uppercase; color: var(--accent); font-weight: 600; margin-bottom: 10px; }
  .section-title { font-family: 'Playfair Display', serif; font-size: clamp(26px, 3.5vw, 40px); font-weight: 700; margin-bottom: 14px; line-height: 1.18; }
  .section-sub { font-size: 16px; color: var(--gray); max-width: 520px; line-height: 1.7; margin-bottom: 40px; }
  .provider-section{display:block;max-width:580px}
  .provider-card{position:relative;background:linear-gradient(145deg,#182a46,#314f77 55%,#4a90d9);border:1px solid rgba(74,144,217,.35);border-radius:22px;padding:18px 18px 16px;color:#fff;box-shadow:0 18px 42px rgba(26,26,46,.18);overflow:hidden;max-width:560px;min-height:322px;display:flex;flex-direction:column;gap:12px}
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
  .contact-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;border:1px solid transparent;transition:transform .15s,box-shadow .15s}
  .contact-btn .ci{width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px}
  .contact-btn.call{background:#eef5ff;color:#2f4e74;border-color:#c7d9f3}
  .contact-btn.call .ci{background:#2f4e74;color:#fff}
  .contact-btn.wa{background:#eafaf1;color:#0f7a43;border-color:#b9e9ce}
  .contact-btn.wa .ci{background:#17a851;color:#fff}
  .contact-btn:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(20,39,71,.12)}
  .services-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
  .service-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; padding: 28px 24px; transition: transform .22s, box-shadow .22s, border-color .22s; cursor: pointer; position: relative; overflow: hidden; text-decoration:none; color:inherit }
  .service-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent); transform: scaleX(0); transform-origin: left; transition: transform .25s; }
  .service-card:hover::before { transform: scaleX(1); }
  .service-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.09); border-color: transparent; }
  .service-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 16px; background:#edf4ff }
  .service-card h3 { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
  .service-card p { font-size: 14px; color: var(--gray); line-height: 1.65; }
  .service-price { margin-top: 16px; font-size: 13px; font-weight: 600; color: var(--accent); }
  .why-grid,.testimonials-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px}
  .why-card,.testi-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:22px}
  .cta-section { padding: 80px 5%; text-align: center; background:linear-gradient(135deg,#2d1265,#7b2ff7) }
  .cta-section h2 { font-family: 'Playfair Display', serif; font-size: clamp(28px, 4vw, 48px); color: #fff; margin-bottom: 16px; }
  .cta-section p { color: rgba(255,255,255,0.7); font-size: 16px; margin-bottom: 36px; }
  .cta-inline-form{display:flex;gap:10px;justify-content:center;align-items:center;flex-wrap:wrap;margin-bottom:14px}
  .cta-inline-form input{height:44px;min-width:240px;padding:0 12px;border-radius:8px;border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.12);color:#fff;font-size:14px}
  .cta-inline-form input::placeholder{color:rgba(255,255,255,.7)}
  footer { background: var(--primary); color: rgba(255,255,255,0.55); padding: 32px 5%; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 13px; }
  .footer-brand { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #fff; }
  @media (max-width: 1024px){
    .provider-details{grid-template-columns:repeat(2,minmax(0,1fr))}
    .services-grid,.why-grid,.testimonials-grid{grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px}
  }

  @media (max-width: 900px){
    .provider-card{max-width:100%;min-height:auto}
    .provider-details{grid-template-columns:repeat(2,minmax(0,1fr))}
    .provider-detail.email,.provider-detail.area,.provider-detail.contact{grid-column:1/-1}
    .services-grid,.why-grid,.testimonials-grid{grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px}
  }

  @media (max-width: 768px){
    section{padding:40px 14px}
    .services-grid,.why-grid,.testimonials-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px}
    .service-card,.why-card,.testi-card{padding:16px}
    .hero h1{font-size:clamp(28px,7vw,36px)}
    .hero p{font-size:14px}
  }

  @media (max-width: 680px){
    .page-shell{padding:0}
    .client-page{margin:0;border-radius:0}
    .switcher{padding:10px 12px;gap:8px}
    .switcher span{width:100%;margin-right:0}
    .sw-btn{width:100%;text-align:left}
    .owner-actions{padding:14px 12px 0}
    .owner-actions .oa-btn{width:100%;justify-content:center}
    .hero{padding:58px 12px 42px}
    .hero-content{max-width:100%}
    .hero-badge{max-width:100%;white-space:normal;line-height:1.45}
    .hero h1{font-size:clamp(24px,8vw,32px);line-height:1.1;overflow-wrap:anywhere;word-break:break-word}
    .hero p{max-width:100%;font-size:14px}
    .hero-btns{gap:8px}
    .btn-primary,.btn-outline{width:100%;text-align:center;padding:12px 14px}
    .hero-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:28px;padding-top:18px}
    section{padding:36px 12px}
    .section-sub{max-width:100%;font-size:14px}
    .services-grid,.why-grid,.testimonials-grid{grid-template-columns:1fr;gap:12px}
    .service-card,.why-card,.testi-card{padding:14px 12px}
    .cta-section{padding:48px 12px}
    .cta-inline-form{flex-direction:column;align-items:stretch}
    .cta-inline-form input{min-width:0;width:100%}
  }

  @media (max-width: 560px){
    .provider-top{grid-template-columns:72px 1fr}
    .provider-photo,.provider-fallback{width:72px;height:72px;border-radius:18px}
    .provider-name{font-size:16px}
    .provider-details{grid-template-columns:1fr}
    .provider-detail.email,.provider-detail.area,.provider-detail.contact{grid-column:auto}
    .hero h1{font-size:clamp(20px,6vw,28px)}
  }

  @media (max-width: 480px){
    .hero{padding:40px 10px 32px}
    .hero h1{font-size:clamp(18px,5vw,24px)}
    .hero p{font-size:13px}
    .hero-stats{gap:10px;padding-top:14px}
    section{padding:24px 10px}
    .cta-section{padding:36px 10px}
    .cta-section h2{font-size:clamp(22px,5vw,32px)}
    .cta-section p{font-size:13px}
  }
  @media print {
    body{background:#fff}
    .owner-actions{display:none !important}
    .page-shell,.client-page{margin:0 !important;max-width:none !important;box-shadow:none !important;border-radius:0 !important}
    @page { size: A4; margin: 10mm; }
  }
</style>
</head>
<body>
<div class="page-shell">
  @if($ownerCanManagePage)
    <div class="owner-actions">
      <button type="button" class="oa-btn primary" id="page-download-pdf">⬇ Download PDF</button>
      <a href="{{ $shareWhatsappUrl }}" target="_blank" rel="noopener" class="oa-btn">🟢 WhatsApp</a>
      <a href="{{ $shareFacebookUrl }}" target="_blank" rel="noopener" class="oa-btn">📘 Facebook</a>
      <a href="{{ $shareXUrl }}" target="_blank" rel="noopener" class="oa-btn">✖ X</a>
      <a href="{{ $shareTelegramUrl }}" target="_blank" rel="noopener" class="oa-btn">📨 Telegram</a>
      <button type="button" class="oa-btn" id="page-copy-link" data-url="{{ $currentPageUrl }}">🔗 Copy Link</button>
    </div>
  @endif

  <div class="client-page">
    <div class="switcher">
      <span>Choose Service:</span>
      @foreach($serviceData as $index => $svc)
        <button class="sw-btn {{ $index === 0 ? 'active' : '' }}" data-key="{{ $svc['key'] }}">{{ $svc['icon'] }} {{ $svc['name'] }}</button>
      @endforeach
    </div>

    <div class="hero" id="hero">
      <div class="hero-pattern"></div>
      <div class="hero-content">
        <div class="hero-badge"><span class="dot"></span><span id="heroBadge"></span></div>
        <h1 id="heroTitle"></h1>
        <p id="heroDesc"></p>
        <div class="hero-btns">
          <a class="btn-primary" id="heroBtn1" href="#"></a>
          <a class="btn-outline" id="heroBtn2" href="#"></a>
        </div>
        <div class="hero-stats">
          <div><div class="stat-num" id="statServices"></div><div class="stat-lbl">Services</div></div>
          <div><div class="stat-num" id="statCities"></div><div class="stat-lbl">Cities</div></div>
          <div><div class="stat-num" id="statContact"></div><div class="stat-lbl">Contact</div></div>
        </div>
      </div>
    </div>

    <section class="alt">
      <div class="section-label">Service Provider</div>
      <div class="section-title">Meet {{ $providerName }}</div>
      <div class="section-sub">Know who is behind this service and how to contact them directly.</div>
      <div class="provider-section">
        <div class="provider-card">
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
      </div>
    </section>

    <section id="servicesSection">
      <div class="section-label" id="servicesLabel"></div>
      <div class="section-title" id="servicesTitle"></div>
      <div class="section-sub" id="servicesSub"></div>
      <div class="services-grid" id="servicesGrid"></div>
    </section>

    <section class="alt">
      <div class="section-label">Why Choose Us</div>
      <div class="section-title" id="whyTitle"></div>
      <div class="section-sub" id="whySub"></div>
      <div class="why-grid" id="whyGrid"></div>
    </section>

    <section>
      <div class="section-label">What Clients Say</div>
      <div class="section-title">Customer Feedback</div>
      <div class="testimonials-grid" id="testiGrid"></div>
    </section>

    <div class="cta-section" id="ctaSection">
      <h2 id="ctaTitle"></h2>
      <p id="ctaDesc"></p>
      <div class="cta-inline-form">
        <input type="tel" id="publicCallbackPhone" placeholder="Enter phone for callback">
        <button type="button" class="btn-outline" onclick="submitDynamicCallback()">Request Callback</button>
      </div>
      <a class="btn-primary" id="ctaBtn" href="{{ $providerContact ? 'tel:' . preg_replace('/\D+/', '', $providerContact) : '#' }}"></a>
    </div>

    <footer>
      <div class="footer-brand" id="footerBrand"></div>
      <div id="footerTagline"></div>
      <div>Powered by <a href="{{ url('/') }}">AajchaOffer</a> · © {{ now()->year }}</div>
    </footer>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const services = @json($serviceData);
  const tc = @json($tc);
  const phone = @json($providerContact ?: $shop->phone ?: '');
  const serviceCitiesCount = {{ is_array($cfg['service_cities'] ?? null) ? count($cfg['service_cities']) : 0 }};
  const clientRequestEndpoint = @json(route('client_requests.store'));
  const csrfToken = @json(csrf_token());

  async function postClientRequest(payload){
    const response = await fetch(clientRequestEndpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify(Object.assign({
        shop_id: {{ (int) $shop->id }},
        template_key: @json((string) ($shop->template ?: 'dynamic_service')),
      }, payload || {})),
    });
    if(!response.ok) throw new Error('request failed');
    return response.json();
  }

  window.submitDynamicCallback = async function(){
    const phoneInput = document.getElementById('publicCallbackPhone');
    const phone = (phoneInput && phoneInput.value || '').trim();
    if(!phone){
      alert('Please enter your phone number');
      return;
    }
    try{
      await postClientRequest({
        source: 'dynamic_cta_callback',
        phone: phone,
        status: 'callback',
        service_name: 'General Callback',
        message: 'Callback request from dynamic service CTA',
      });
      phoneInput.value = '';
      alert('Request submitted. We will contact you shortly.');
    }catch(_e){
      alert('Unable to submit request right now. Please try again.');
    }
  };

  function setService(key) {
    const current = services.find(s => s.key === key) || services[0];
    document.documentElement.style.setProperty('--primary', current.color || '#1a1a2e');
    document.documentElement.style.setProperty('--accent', current.accent || '#e94560');
    document.documentElement.style.setProperty('--accent2', current.accent2 || '#f5a623');
    document.getElementById('hero').style.background = `linear-gradient(135deg, ${current.color} 0%, ${current.accent} 55%, ${current.color} 100%)`;
    document.getElementById('ctaSection').style.background = `linear-gradient(135deg, ${current.color}, ${current.accent})`;

    document.getElementById('heroBadge').textContent = tc.hero_badge || 'विश्वासार्ह स्थानिक सेवा';
    document.getElementById('heroTitle').innerHTML = (tc.hero_title || (current.name + ' by {{ addslashes($providerName) }}')).replace(/\n/g, '<br>');
    document.getElementById('heroDesc').textContent = current.description || tc.hero_description || 'जलद, विश्वासार्ह आणि व्यावसायिक सेवा.';
    document.getElementById('heroBtn1').textContent = tc.primary_cta || 'आत्ता बुक करा';
    document.getElementById('heroBtn2').textContent = tc.secondary_cta || 'मोफत कोट घ्या';
    document.getElementById('heroBtn1').href = phone ? ('tel:' + String(phone).replace(/\D+/g,'')) : (current.url || '#');
    document.getElementById('heroBtn2').href = current.url || '#';

    document.getElementById('statServices').textContent = String(services.length) + '+';
    document.getElementById('statCities').textContent = String(serviceCitiesCount || 1) + '+';
    document.getElementById('statContact').textContent = phone ? 'Direct' : 'Online';

    document.getElementById('servicesLabel').textContent = tc.services_label || 'आमच्या सेवा';
    document.getElementById('servicesTitle').textContent = tc.services_title || 'आम्ही देत असलेल्या सेवा';
    document.getElementById('servicesSub').textContent = tc.services_subtitle || 'आमच्या लोकप्रिय सेवांमधून निवडा.';
    document.getElementById('servicesGrid').innerHTML = services.map(card => `
      <a class="service-card" href="${card.url || '#'}" target="_blank" rel="noopener">
        <div class="service-icon">${card.icon || '🛠️'}</div>
        <h3>${card.name}</h3>
        <p>${card.description || 'अनुभवी टीमकडून व्यावसायिक सेवा.'}</p>
        <div class="service-price">${card.price || 'किंमतीसाठी संपर्क करा'}</div>
      </a>
    `).join('');

    document.getElementById('whyTitle').textContent = tc.why_title || 'आम्हालाच का निवडाल?';
    document.getElementById('whySub').textContent = tc.why_subtitle || 'दर्जेदार काम, पारदर्शक किंमत आणि जलद प्रतिसाद.';
    document.getElementById('whyGrid').innerHTML = [
      ['⚡','Fast Response','Quick support and on-time service delivery.'],
      ['🏅','Experienced Team','Skilled professionals with practical experience.'],
      ['💰','Transparent Pricing','No hidden charges. Clear pricing before work starts.'],
      ['🤝','Trusted Service','Focused on long-term customer satisfaction.'],
    ].map(item => `<div class="why-card"><h4>${item[0]} ${item[1]}</h4><p style="margin-top:8px;color:#6b7280;line-height:1.7">${item[2]}</p></div>`).join('');

    document.getElementById('testiGrid').innerHTML = [
      'Excellent quality service and very professional behavior.',
      'Quick response and fair pricing. Highly recommended.',
      'Great experience from booking to completion.'
    ].map(text => `<div class="testi-card">★★★★★<p style="margin-top:12px;color:#4b5563;line-height:1.8">"${text}"</p></div>`).join('');

    document.getElementById('ctaTitle').textContent = tc.cta_title || 'आज मदत हवी आहे?';
    document.getElementById('ctaDesc').textContent = tc.cta_description || 'आत्ताच संपर्क करा, आम्ही लवकरच प्रतिसाद देऊ.';
    document.getElementById('ctaBtn').textContent = tc.cta_button || 'आत्ता संपर्क करा';
    document.getElementById('footerBrand').textContent = tc.footer_brand || @json($shop->name ?: 'माझी सेवा');
    document.getElementById('footerTagline').textContent = tc.footer_tagline || ('Provider: {{ addslashes($providerName) }}');

    document.querySelectorAll('.sw-btn').forEach(btn => btn.classList.toggle('active', btn.dataset.key === key));
  }

  document.querySelectorAll('.sw-btn').forEach(btn => btn.addEventListener('click', function(){ setService(btn.dataset.key); }));

  const pdfBtn = document.getElementById('page-download-pdf');
  const copyBtn = document.getElementById('page-copy-link');
  if (pdfBtn) pdfBtn.addEventListener('click', () => window.print());
  if (copyBtn) copyBtn.addEventListener('click', async function(){
    const url = copyBtn.getAttribute('data-url') || window.location.href;
    try { await navigator.clipboard.writeText(url); const prev = copyBtn.textContent; copyBtn.textContent = '✓ Copied'; setTimeout(() => copyBtn.textContent = prev, 1200); }
    catch (err) { window.prompt('Copy this link:', url); }
  });

  setService((services[0] && services[0].key) || 'service-1');
});
</script>
</body>
</html>
