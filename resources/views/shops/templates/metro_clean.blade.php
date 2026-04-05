<!DOCTYPE html>
<html lang="en">
<head>
@php
  $cfg = is_array($shop->page_config ?? null) ? $shop->page_config : [];
  $tc  = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];
  $services = is_array($cfg['services'] ?? null) ? array_values(array_filter($cfg['services'], fn($s)=>!empty($s['name']))) : [];
  $owner = $shop->user ?? null;

  $brandName     = trim((string)($tc['footer_brand']     ?? $shop->name ?? 'My Business'));
  $heroBadge     = trim((string)($tc['hero_badge']       ?? 'Trusted Local Service'));
  $heroTitle     = trim((string)($tc['hero_title']       ?? $brandName));
  $heroDesc      = trim((string)($tc['hero_description'] ?? 'Professional service delivered fast and reliably.'));
  $primaryCta    = trim((string)($tc['primary_cta']      ?? 'Book Now'));
  $secondaryCta  = trim((string)($tc['secondary_cta']    ?? 'Learn More'));
  $footerTagline = trim((string)($tc['footer_tagline']   ?? 'Trusted · Professional · Local'));
  $aboutTitle    = trim((string)($tc['about_title']      ?? 'About Us'));
  $aboutText     = trim((string)($tc['about_text']       ?? ($shop->description ?? 'We are a trusted local business dedicated to quality and customer satisfaction.')));
  $contactHeading= trim((string)($tc['contact_heading']  ?? 'Get In Touch'));
  $servicesLabel = trim((string)($tc['services_label']   ?? 'Our Services'));
  $servicesTitle = trim((string)($tc['services_title']   ?? 'What We Offer'));
  $servicesSub   = trim((string)($tc['services_subtitle']?? 'Choose from our popular services.'));
  $whyTitle      = trim((string)($tc['why_title']        ?? 'Why Choose Us'));
  $whySub        = trim((string)($tc['why_subtitle']     ?? 'Transparent pricing and expert team.'));
  $ctaTitle      = trim((string)($tc['cta_title']        ?? 'Ready to Get Started?'));
  $ctaDesc       = trim((string)($tc['cta_description']  ?? 'Contact us now for quick assistance.'));
  $ctaButton     = trim((string)($tc['cta_button']       ?? 'Contact Now'));

  $accent = trim((string)($tc['metro_accent'] ?? 'blue'));
  $accentColors = [
    'blue'   => ['bg'=>'#2563eb','light'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1e40af','dark'=>'#1e3a8a'],
    'green'  => ['bg'=>'#16a34a','light'=>'#f0fdf4','border'=>'#bbf7d0','text'=>'#15803d','dark'=>'#14532d'],
    'indigo' => ['bg'=>'#4f46e5','light'=>'#eef2ff','border'=>'#c7d2fe','text'=>'#4338ca','dark'=>'#312e81'],
  ];
  $col = $accentColors[$accent] ?? $accentColors['blue'];

  $providerName    = trim((string)($tc['provider_name']    ?? optional($owner)->full_name ?? 'Business Owner'));
  $providerTitle   = trim((string)($tc['provider_title']   ?? 'Founder'));
  $providerEmail   = trim((string)($tc['provider_email']   ?? optional($owner)->email ?? ''));
  $providerContact = trim((string)($tc['provider_contact'] ?? $shop->phone ?? optional($owner)->mobile_number ?? ''));
  $providerBio     = trim((string)($tc['provider_bio']     ?? 'Experienced professional dedicated to quality service.'));
  $providerExp     = trim((string)($tc['provider_experience'] ?? '5+ Years'));
  $providerPhone   = preg_replace('/\D+/', '', $providerContact);
  $callUrl         = $providerPhone ? 'tel:'.$providerPhone : '';
  $waUrl           = $providerPhone ? 'https://wa.me/'.$providerPhone : '';
  $providerPhoto   = trim((string)($tc['provider_photo'] ?? ''));
  $photoUrl = '';
  if ($providerPhoto !== '') {
    $photoUrl = \Illuminate\Support\Str::startsWith($providerPhoto, ['http','data:','/'])
      ? $providerPhoto : \Illuminate\Support\Facades\Storage::url($providerPhoto);
  }

  if (empty($services)) {
    $services = [['icon'=>'🛠️','name'=>$shop->name ?: 'General Service','description'=>$shop->description ?: 'Professional local service.','price'=>'Contact for pricing']];
  }

  $currentUrl = $shop->id ? route('shops.public', ['publicSlug' => $shop->public_page_slug]) : '#';
  $shareText = urlencode('Check out '.$brandName.' on AajchaOffer: '.$currentUrl);
  $ownerCanManage = auth()->check() && $shop->id && auth()->id() === $shop->user_id;

  $pageTitle = $brandName . ' | ' . $providerName;
  $pageDesc  = $heroDesc;
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDesc }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDesc }}">
<meta property="og:type" content="website">
@if($shop->id)<link rel="canonical" href="{{ $currentUrl }}">@endif
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --accent:{{ $col['bg'] }};
  --accent-light:{{ $col['light'] }};
  --accent-border:{{ $col['border'] }};
  --accent-text:{{ $col['text'] }};
  --accent-dark:{{ $col['dark'] }};
  --gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;--gray-400:#9ca3af;
  --gray-600:#4b5563;--gray-700:#374151;--gray-800:#1f2937;--gray-900:#111827;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:var(--gray-50);color:var(--gray-800);line-height:1.6;overflow-x:hidden}
a{color:inherit;text-decoration:none}
img{max-width:100%;display:block}
.mc-shell{min-height:100vh;background:#fff}

/* ── Owner bar ── */
.mc-owner-bar{background:#fff;border-bottom:1px solid var(--gray-200);padding:8px 24px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.mc-owner-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border-radius:8px;border:1px solid var(--gray-200);background:#fff;color:var(--gray-700);font-size:13px;font-weight:600;cursor:pointer;transition:all .18s}
.mc-owner-btn:hover{border-color:var(--accent);color:var(--accent)}
.mc-owner-btn.primary{background:var(--accent);border-color:var(--accent);color:#fff}
.mc-owner-btn.primary:hover{opacity:.9}

/* ── Header ── */
.mc-header{background:#fff;border-bottom:1px solid var(--gray-200);padding:0 5%;position:sticky;top:0;z-index:100;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.mc-header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:64px;gap:16px}
.mc-logo{font-size:18px;font-weight:800;color:var(--gray-900);letter-spacing:-.02em}
.mc-logo span{color:var(--accent)}
.mc-nav{display:flex;gap:4px}
.mc-nav a{padding:7px 14px;border-radius:8px;font-size:14px;font-weight:500;color:var(--gray-600);transition:all .18s}
.mc-nav a:hover{background:var(--accent-light);color:var(--accent-text)}
.mc-nav-cta{background:var(--accent) !important;color:#fff !important;font-weight:700 !important}
.mc-nav-cta:hover{opacity:.9 !important;background:var(--accent) !important}
.mc-mobile-menu-btn{display:none;border:none;background:none;padding:6px;cursor:pointer;color:var(--gray-700);font-size:22px;line-height:1}

/* ── Hero ── */
.mc-hero{background:#fff;padding:80px 5% 70px;border-bottom:1px solid var(--gray-200)}
.mc-hero-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 420px;gap:60px;align-items:center}
.mc-hero-badge{display:inline-flex;align-items:center;gap:6px;background:var(--accent-light);border:1px solid var(--accent-border);color:var(--accent-text);font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:5px 12px;border-radius:999px;margin-bottom:20px}
.mc-hero-badge i{width:6px;height:6px;border-radius:50%;background:var(--accent);display:inline-block}
.mc-hero h1{font-size:clamp(32px,4.5vw,52px);font-weight:800;color:var(--gray-900);line-height:1.1;letter-spacing:-.02em;margin-bottom:18px}
.mc-hero p{font-size:18px;color:var(--gray-600);line-height:1.7;max-width:500px;margin-bottom:32px}
.mc-hero-btns{display:flex;gap:12px;flex-wrap:wrap}
.mc-btn-primary{padding:14px 28px;background:var(--accent);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s;box-shadow:0 4px 14px rgba(0,0,0,.15)}
.mc-btn-primary:hover{filter:brightness(1.08);box-shadow:0 6px 20px rgba(0,0,0,.2)}
.mc-btn-secondary{padding:14px 24px;background:#fff;color:var(--gray-700);border:1.5px solid var(--gray-200);border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s}
.mc-btn-secondary:hover{border-color:var(--accent);color:var(--accent)}
.mc-hero-card{background:var(--gray-50);border:1px solid var(--gray-200);border-radius:20px;padding:28px;display:flex;flex-direction:column;gap:14px}
.mc-hero-stat{display:flex;align-items:center;gap:12px;padding:14px;background:#fff;border-radius:12px;border:1px solid var(--gray-200)}
.mc-hero-stat-icon{width:40px;height:40px;border-radius:10px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.mc-hero-stat strong{display:block;font-size:15px;font-weight:700;color:var(--gray-900)}
.mc-hero-stat small{font-size:13px;color:var(--gray-600)}

/* ── Section layout ── */
.mc-section{padding:72px 5%}
.mc-section-inner{max-width:1200px;margin:0 auto}
.mc-section.alt{background:var(--gray-50)}
.mc-section-label{font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:var(--accent);font-weight:700;margin-bottom:8px}
.mc-section-title{font-size:clamp(24px,3vw,36px);font-weight:800;color:var(--gray-900);line-height:1.2;letter-spacing:-.01em;margin-bottom:12px}
.mc-section-sub{font-size:16px;color:var(--gray-600);max-width:520px;line-height:1.7;margin-bottom:40px}

/* ── Services grid ── */
.mc-services-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
.mc-service-card{background:#fff;border:1.5px solid var(--gray-200);border-radius:14px;padding:24px;transition:all .22s;cursor:pointer;position:relative;overflow:hidden}
.mc-service-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--accent);transform:scaleX(0);transform-origin:left;transition:transform .25s}
.mc-service-card:hover::before{transform:scaleX(1)}
.mc-service-card:hover{border-color:var(--accent-border);box-shadow:0 8px 24px rgba(0,0,0,.08);transform:translateY(-2px)}
.mc-svc-icon{width:48px;height:48px;border-radius:12px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:14px}
.mc-svc-name{font-size:16px;font-weight:700;color:var(--gray-900);margin-bottom:6px}
.mc-svc-desc{font-size:14px;color:var(--gray-600);line-height:1.6;margin-bottom:12px}
.mc-svc-price{display:inline-flex;align-items:center;padding:4px 10px;background:var(--accent-light);border:1px solid var(--accent-border);border-radius:999px;font-size:12px;font-weight:700;color:var(--accent-text)}

/* ── About + Provider ── */
.mc-about-grid{display:grid;grid-template-columns:1fr 380px;gap:48px;align-items:start}
.mc-about-text{font-size:16px;color:var(--gray-600);line-height:1.8;margin-bottom:24px}
.mc-checklist{list-style:none;display:flex;flex-direction:column;gap:10px;margin-bottom:28px}
.mc-checklist li{display:flex;align-items:center;gap:10px;font-size:15px;color:var(--gray-700)}
.mc-checklist li::before{content:'✓';width:22px;height:22px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0}
.mc-profile-card{background:#fff;border:1.5px solid var(--gray-200);border-radius:20px;padding:28px;box-shadow:0 4px 24px rgba(0,0,0,.07)}
.mc-profile-top{display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--gray-200)}
.mc-profile-photo{width:72px;height:72px;border-radius:16px;object-fit:cover;background:var(--accent-light);border:3px solid var(--accent-border);flex-shrink:0}
.mc-profile-fallback{width:72px;height:72px;border-radius:16px;background:linear-gradient(135deg,var(--accent-light),var(--accent-border));display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:var(--accent-text);flex-shrink:0}
.mc-profile-name{font-size:17px;font-weight:800;color:var(--gray-900);margin-bottom:2px}
.mc-profile-role{font-size:12px;color:var(--accent-text);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}
.mc-profile-bio{font-size:13px;color:var(--gray-600);line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.mc-profile-details{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px}
.mc-profile-detail{background:var(--gray-50);border:1px solid var(--gray-200);border-radius:10px;padding:10px 12px}
.mc-profile-detail strong{display:block;font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:var(--gray-400);margin-bottom:4px}
.mc-profile-detail span{font-size:13px;color:var(--gray-800);font-weight:600;word-break:break-word}
.mc-profile-actions{display:flex;gap:8px}
.mc-contact-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;border:1.5px solid transparent;transition:all .18s}
.mc-contact-btn.call{background:var(--accent-light);color:var(--accent-text);border-color:var(--accent-border)}
.mc-contact-btn.call:hover{background:var(--accent);color:#fff}
.mc-contact-btn.wa{background:#ecfdf5;color:#15803d;border-color:#bbf7d0}
.mc-contact-btn.wa:hover{background:#16a34a;color:#fff}

/* ── Why us ── */
.mc-why-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
.mc-why-card{background:#fff;border:1.5px solid var(--gray-200);border-radius:14px;padding:22px;transition:border-color .2s}
.mc-why-card:hover{border-color:var(--accent-border)}
.mc-why-icon{width:44px;height:44px;border-radius:12px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px}
.mc-why-title{font-size:14px;font-weight:700;color:var(--gray-900);margin-bottom:6px}
.mc-why-desc{font-size:13px;color:var(--gray-600);line-height:1.6}

/* ── CTA ── */
.mc-cta{background:var(--accent);padding:72px 5%;text-align:center}
.mc-cta-inner{max-width:640px;margin:0 auto}
.mc-cta h2{font-size:clamp(26px,3.5vw,40px);font-weight:800;color:#fff;line-height:1.2;margin-bottom:14px;letter-spacing:-.01em}
.mc-cta p{font-size:17px;color:rgba(255,255,255,.85);line-height:1.7;margin-bottom:28px}
.mc-cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.mc-cta-btn-white{padding:14px 28px;background:#fff;color:var(--accent);border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;box-shadow:0 4px 14px rgba(0,0,0,.15)}
.mc-cta-btn-white:hover{box-shadow:0 6px 20px rgba(0,0,0,.25);transform:translateY(-1px)}
.mc-cta-btn-outline{padding:14px 24px;background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,.5);border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .2s}
.mc-cta-btn-outline:hover{background:rgba(255,255,255,.1);border-color:#fff}

/* ── Contact section ── */
.mc-contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start}
.mc-contact-info{display:flex;flex-direction:column;gap:16px}
.mc-contact-item{display:flex;align-items:flex-start;gap:14px}
.mc-contact-icon{width:44px;height:44px;border-radius:12px;background:var(--accent-light);border:1.5px solid var(--accent-border);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.mc-contact-label{font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:var(--gray-400);font-weight:600;margin-bottom:3px}
.mc-contact-value{font-size:15px;color:var(--gray-800);font-weight:600}
.mc-share-btns{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}
.mc-share-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;border:1.5px solid var(--gray-200);background:#fff;color:var(--gray-700);font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.mc-share-btn:hover{border-color:var(--accent);color:var(--accent)}

/* ── Footer ── */
.mc-footer{background:var(--gray-900);color:rgba(255,255,255,.7);padding:32px 5%;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:13px}
.mc-footer-brand{font-weight:700;color:#fff;font-size:15px}
.mc-footer a{color:rgba(255,255,255,.7);text-decoration:none}
.mc-footer a:hover{color:#fff}

@media(max-width:900px){
  .mc-hero-inner,.mc-about-grid,.mc-contact-grid{grid-template-columns:1fr}
  .mc-hero-card{display:none}
}
@media(max-width:600px){
  .mc-hero{padding:50px 5% 44px}
  .mc-section{padding:48px 5%}
  .mc-nav{display:none}
  .mc-profile-details{grid-template-columns:1fr}
}
</style>
</head>
<body>
<div class="mc-shell">

@if($ownerCanManage)
<div class="mc-owner-bar">
  <a href="{{ route('shops.show', $shop) }}" class="mc-owner-btn primary">✏️ Edit Page</a>
  <a href="{{ $callUrl ?: '#' }}" class="mc-owner-btn">📞 Test Call</a>
  <span style="margin-left:auto;font-size:12px;color:#9ca3af">Owner View</span>
</div>
@endif

{{-- Header --}}
<header class="mc-header">
  <div class="mc-header-inner">
    <div class="mc-logo">{{ $brandName }}</div>
    <nav class="mc-nav">
      <a href="#services">Services</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>
      @if($callUrl)<a href="{{ $callUrl }}" class="mc-nav-cta">📞 Call Now</a>@endif
    </nav>
  </div>
</header>

{{-- Hero --}}
<section class="mc-hero">
  <div class="mc-hero-inner">
    <div>
      <div class="mc-hero-badge"><i></i> {{ $heroBadge }}</div>
      <h1>{{ $heroTitle }}</h1>
      <p>{{ $heroDesc }}</p>
      <div class="mc-hero-btns">
        @if($callUrl)<a href="{{ $callUrl }}" class="mc-btn-primary">📞 {{ $primaryCta }}</a>@else<span class="mc-btn-primary" style="cursor:default">{{ $primaryCta }}</span>@endif
        @if($waUrl)<a href="{{ $waUrl }}" class="mc-btn-secondary" target="_blank" rel="noopener">💬 WhatsApp</a>@else<a href="#services" class="mc-btn-secondary">{{ $secondaryCta }}</a>@endif
      </div>
    </div>
    <div class="mc-hero-card">
      <div class="mc-hero-stat">
        <div class="mc-hero-stat-icon">⭐</div>
        <div><strong>4.9 / 5.0 Rating</strong><small>Based on customer reviews</small></div>
      </div>
      <div class="mc-hero-stat">
        <div class="mc-hero-stat-icon">🏆</div>
        <div><strong>{{ $providerExp }}</strong><small>Professional Experience</small></div>
      </div>
      <div class="mc-hero-stat">
        <div class="mc-hero-stat-icon">⚡</div>
        <div><strong>Fast Response</strong><small>Same-day service available</small></div>
      </div>
      <div class="mc-hero-stat">
        <div class="mc-hero-stat-icon">🛡️</div>
        <div><strong>100% Trusted</strong><small>Verified by AajchaOffer</small></div>
      </div>
    </div>
  </div>
</section>

{{-- Services --}}
<section class="mc-section alt" id="services">
  <div class="mc-section-inner">
    <div class="mc-section-label">{{ $servicesLabel }}</div>
    <div class="mc-section-title">{{ $servicesTitle }}</div>
    <div class="mc-section-sub">{{ $servicesSub }}</div>
    <div class="mc-services-grid">
      @foreach($services as $svc)
      <div class="mc-service-card">
        <div class="mc-svc-icon">{{ $svc['icon'] ?? '🛠️' }}</div>
        <div class="mc-svc-name">{{ $svc['name'] }}</div>
        @if(!empty($svc['description']))<div class="mc-svc-desc">{{ $svc['description'] }}</div>@endif
        @if(!empty($svc['price']))<div class="mc-svc-price">{{ $svc['price'] }}</div>@endif
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- About + Provider --}}
<section class="mc-section" id="about">
  <div class="mc-section-inner">
    <div class="mc-about-grid">
      <div>
        <div class="mc-section-label">Who We Are</div>
        <div class="mc-section-title">{{ $aboutTitle }}</div>
        <p class="mc-about-text">{{ $aboutText }}</p>
        <ul class="mc-checklist">
          <li>Experienced and certified professionals</li>
          <li>Transparent pricing with no hidden charges</li>
          <li>Fast response and on-time service delivery</li>
          <li>Trusted by hundreds of local customers</li>
        </ul>
        @if($callUrl || $waUrl)
        <div class="mc-hero-btns">
          @if($callUrl)<a href="{{ $callUrl }}" class="mc-btn-primary">📞 {{ $primaryCta }}</a>@endif
          @if($waUrl)<a href="{{ $waUrl }}" class="mc-btn-secondary" target="_blank" rel="noopener">💬 WhatsApp</a>@endif
        </div>
        @endif
      </div>
      <div class="mc-profile-card">
        <div class="mc-profile-top">
          @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $providerName }}" class="mc-profile-photo">
          @else
            <div class="mc-profile-fallback">{{ mb_substr($providerName,0,1) }}</div>
          @endif
          <div>
            <div class="mc-profile-name">{{ $providerName }}</div>
            <div class="mc-profile-role">{{ $providerTitle }}</div>
            <div class="mc-profile-bio">{{ $providerBio }}</div>
          </div>
        </div>
        <div class="mc-profile-details">
          @if($providerExp)<div class="mc-profile-detail"><strong>Experience</strong><span>{{ $providerExp }}</span></div>@endif
          @if($providerEmail)<div class="mc-profile-detail"><strong>Email</strong><span>{{ $providerEmail }}</span></div>@endif
          @if($shop->address)<div class="mc-profile-detail" style="grid-column:span 2"><strong>Location</strong><span>{{ $shop->address }}</span></div>@endif
        </div>
        <div class="mc-profile-actions">
          @if($callUrl)<a href="{{ $callUrl }}" class="mc-contact-btn call">📞 Call</a>@endif
          @if($waUrl)<a href="{{ $waUrl }}" class="mc-contact-btn wa" target="_blank">💬 WhatsApp</a>@endif
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Why Us --}}
<section class="mc-section alt">
  <div class="mc-section-inner">
    <div class="mc-section-label">Our Strengths</div>
    <div class="mc-section-title">{{ $whyTitle }}</div>
    <div class="mc-section-sub">{{ $whySub }}</div>
    <div class="mc-why-grid">
      <div class="mc-why-card"><div class="mc-why-icon">⚡</div><div class="mc-why-title">Fast Response</div><div class="mc-why-desc">Quick turnaround and same-day availability for urgent needs.</div></div>
      <div class="mc-why-card"><div class="mc-why-icon">🏆</div><div class="mc-why-title">Expert Team</div><div class="mc-why-desc">Certified professionals with years of field experience.</div></div>
      <div class="mc-why-card"><div class="mc-why-icon">💰</div><div class="mc-why-title">Fair Pricing</div><div class="mc-why-desc">Transparent rates with no surprise charges after the job.</div></div>
      <div class="mc-why-card"><div class="mc-why-icon">🛡️</div><div class="mc-why-title">Quality Guaranteed</div><div class="mc-why-desc">Every job backed by our satisfaction guarantee promise.</div></div>
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="mc-cta">
  <div class="mc-cta-inner">
    <h2>{{ $ctaTitle }}</h2>
    <p>{{ $ctaDesc }}</p>
    <div class="mc-cta-btns">
      @if($callUrl)<a href="{{ $callUrl }}" class="mc-cta-btn-white">📞 {{ $ctaButton }}</a>@else<span class="mc-cta-btn-white">{{ $ctaButton }}</span>@endif
      @if($waUrl)<a href="{{ $waUrl }}" class="mc-cta-btn-outline" target="_blank">💬 WhatsApp Us</a>@endif
    </div>
  </div>
</section>

{{-- Contact --}}
<section class="mc-section" id="contact">
  <div class="mc-section-inner">
    <div class="mc-section-label">{{ $contactHeading }}</div>
    <div class="mc-section-title">Connect With Us</div>
    <div class="mc-contact-grid">
      <div class="mc-contact-info">
        @if($providerContact)
        <div class="mc-contact-item">
          <div class="mc-contact-icon">📞</div>
          <div><div class="mc-contact-label">Phone</div><div class="mc-contact-value">{{ $providerContact }}</div></div>
        </div>
        @endif
        @if($providerEmail)
        <div class="mc-contact-item">
          <div class="mc-contact-icon">📧</div>
          <div><div class="mc-contact-label">Email</div><div class="mc-contact-value">{{ $providerEmail }}</div></div>
        </div>
        @endif
        @if($shop->address)
        <div class="mc-contact-item">
          <div class="mc-contact-icon">📍</div>
          <div><div class="mc-contact-label">Address</div><div class="mc-contact-value">{{ $shop->address }}</div></div>
        </div>
        @endif
        @if($shop->id)
        <div>
          <div class="mc-contact-label" style="margin-bottom:8px">Share This Page</div>
          <div class="mc-share-btns">
            <a href="https://wa.me/?text={{ $shareText }}" class="mc-share-btn" target="_blank" rel="noopener">💬 WhatsApp</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($currentUrl) }}" class="mc-share-btn" target="_blank" rel="noopener">Facebook</a>
          </div>
        </div>
        @endif
      </div>
      <div>
        @if($callUrl || $waUrl)
        <div style="background:var(--accent-light);border:1.5px solid var(--accent-border);border-radius:20px;padding:28px;text-align:center">
          <div style="font-size:48px;margin-bottom:14px">📞</div>
          <h3 style="font-size:20px;font-weight:800;color:var(--gray-900);margin-bottom:8px">Call Us Directly</h3>
          <p style="color:var(--gray-600);font-size:14px;margin-bottom:20px">We're available to help you right now.</p>
          <div style="display:flex;flex-direction:column;gap:10px">
            @if($callUrl)<a href="{{ $callUrl }}" class="mc-btn-primary" style="justify-content:center">📞 {{ $providerContact }}</a>@endif
            @if($waUrl)<a href="{{ $waUrl }}" class="mc-btn-secondary" target="_blank" rel="noopener" style="justify-content:center">💬 Chat on WhatsApp</a>@endif
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- Footer --}}
<footer class="mc-footer">
  <div class="mc-footer-brand">{{ $brandName }}</div>
  <div>{{ $footerTagline }}</div>
  <div>Powered by <a href="{{ url('/') }}">AajchaOffer</a></div>
</footer>

</div>
</body>
</html>
