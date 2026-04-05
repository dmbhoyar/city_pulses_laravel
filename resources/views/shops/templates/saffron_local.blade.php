<!DOCTYPE html>
<html lang="en">
<head>
@php
  $cfg = is_array($shop->page_config ?? null) ? $shop->page_config : [];
  $tc  = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];
  $services = is_array($cfg['services'] ?? null) ? array_values(array_filter($cfg['services'], fn($s)=>!empty($s['name']))) : [];
  $owner = $shop->user ?? null;

  $brandName     = trim((string)($tc['footer_brand']     ?? $shop->name ?? 'माझे दुकान'));
  $heroBadge     = trim((string)($tc['hero_badge']       ?? 'विश्वासार्ह स्थानिक दुकान'));
  $heroTitle     = trim((string)($tc['hero_title']       ?? $brandName));
  $heroDesc      = trim((string)($tc['hero_description'] ?? 'दर्जेदार उत्पादने आणि सेवा — आपल्या परिसरात सहज उपलब्ध.'));
  $primaryCta    = trim((string)($tc['primary_cta']      ?? 'संपर्क करा'));
  $secondaryCta  = trim((string)($tc['secondary_cta']    ?? 'अधिक जाणून घ्या'));
  $footerTagline = trim((string)($tc['footer_tagline']   ?? 'विश्वासार्ह · स्थानिक · दर्जेदार'));
  $servicesLabel = trim((string)($tc['services_label']   ?? 'आमच्या ऑफर्स'));
  $servicesTitle = trim((string)($tc['services_title']   ?? 'उत्पादने आणि सेवा'));
  $servicesSub   = trim((string)($tc['services_subtitle']?? 'आमच्या निवडक ऑफर्समधून निवडा.'));
  $whyTitle      = trim((string)($tc['why_title']        ?? 'आमचेच का निवडावे?'));
  $whySub        = trim((string)($tc['why_subtitle']     ?? 'दर्जा, विश्वास आणि सर्वोत्तम किंमत — आमचे वचन.'));
  $ctaTitle      = trim((string)($tc['cta_title']        ?? 'आजच भेट द्या'));
  $ctaDesc       = trim((string)($tc['cta_description']  ?? 'आत्ताच संपर्क करा आणि जलद मदत मिळवा.'));
  $ctaButton     = trim((string)($tc['cta_button']       ?? 'आत्ता संपर्क करा'));
  $openingHours  = trim((string)($tc['opening_hours']    ?? ''));
  $localityNote  = trim((string)($tc['locality_note']    ?? ''));
  $specialOffer  = trim((string)($tc['special_offer']    ?? ''));

  $providerName    = trim((string)($tc['provider_name']    ?? optional($owner)->full_name ?? 'दुकान मालक'));
  $providerTitle   = trim((string)($tc['provider_title']   ?? 'संस्थापक'));
  $providerEmail   = trim((string)($tc['provider_email']   ?? optional($owner)->email ?? ''));
  $providerContact = trim((string)($tc['provider_contact'] ?? $shop->phone ?? optional($owner)->mobile_number ?? ''));
  $providerBio     = trim((string)($tc['provider_bio']     ?? 'आमचे दुकान तुमच्या परिसरातील एक विश्वासार्ह नाव आहे.'));
  $providerExp     = trim((string)($tc['provider_experience'] ?? ''));
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
    $services = [['icon'=>'🛍️','name'=>$shop->name ?: 'सामान्य सेवा','description'=>$shop->description ?: 'व्यावसायिक स्थानिक सेवा.','price'=>'किंमतीसाठी संपर्क करा']];
  }

  $currentUrl = $shop->id ? route('shops.public', ['publicSlug' => $shop->public_page_slug]) : '#';
  $shareText  = urlencode($brandName . ' — ' . $currentUrl);
  $ownerCanManage = auth()->check() && $shop->id && auth()->id() === $shop->user_id;
  $pageTitle = $brandName . ' | ' . $providerName;
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $heroDesc }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:type" content="website">
@if($shop->id)<link rel="canonical" href="{{ $currentUrl }}">@endif
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --saf:#ea580c;--saf-d:#c2410c;--saf-l:#fff7ed;--saf-ll:#fffbf5;
  --saf-b:#fed7aa;--saf-bb:#fdba74;--amber:#f59e0b;
  --cream:#fffbf5;--brown:#78350f;--brown-l:#92400e;
  --gray-100:#f3f4f6;--gray-200:#e5e7eb;--gray-600:#4b5563;--gray-800:#1f2937;--gray-900:#111827;
  --shadow-sm:0 1px 4px rgba(234,88,12,.08);
  --shadow:0 4px 20px rgba(234,88,12,.12);
  --shadow-lg:0 12px 40px rgba(234,88,12,.18);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Poppins',sans-serif;background:var(--cream);color:var(--gray-800);line-height:1.6;overflow-x:hidden}
a{color:inherit;text-decoration:none}
img{max-width:100%;display:block}
.sf-shell{min-height:100vh}

/* ── Owner bar ── */
.sf-owner-bar{background:#fff;border-bottom:2px solid var(--saf-b);padding:8px 24px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.sf-owner-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border-radius:8px;border:1.5px solid var(--saf-b);background:#fff;color:var(--brown);font-size:13px;font-weight:600;cursor:pointer;transition:all .18s}
.sf-owner-btn:hover,.sf-owner-btn.primary{background:var(--saf);border-color:var(--saf);color:#fff}

/* ── Special Offer Banner ── */
.sf-offer-banner{background:linear-gradient(90deg,var(--amber),var(--saf));color:#fff;text-align:center;padding:10px 20px;font-size:14px;font-weight:600;letter-spacing:.01em}

/* ── Header ── */
.sf-header{background:var(--saf);padding:0 5%;position:sticky;top:0;z-index:100;box-shadow:0 3px 14px rgba(234,88,12,.3)}
.sf-header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:66px;gap:16px}
.sf-logo{font-size:19px;font-weight:800;color:#fff;letter-spacing:-.01em}
.sf-nav{display:flex;gap:4px}
.sf-nav a{padding:7px 14px;border-radius:8px;font-size:14px;font-weight:600;color:rgba(255,255,255,.85);transition:all .18s}
.sf-nav a:hover{background:rgba(255,255,255,.15);color:#fff}
.sf-nav-cta{background:#fff !important;color:var(--saf) !important;font-weight:700 !important}
.sf-nav-cta:hover{opacity:.9 !important}

/* ── Hero ── */
.sf-hero{background:linear-gradient(135deg,var(--cream) 0%,#ffe4cc 55%,var(--saf-b) 100%);padding:80px 5% 70px;position:relative;overflow:hidden}
.sf-hero::before{content:'';position:absolute;top:-60px;right:-80px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(234,88,12,.1),transparent 70%);pointer-events:none}
.sf-hero-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 380px;gap:60px;align-items:center;position:relative;z-index:1}
.sf-hero-tag{display:inline-flex;align-items:center;gap:6px;background:var(--saf);color:#fff;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:5px 14px;border-radius:999px;margin-bottom:18px}
.sf-hero h1{font-size:clamp(30px,4.5vw,52px);font-weight:800;color:var(--brown);line-height:1.1;letter-spacing:-.02em;margin-bottom:16px}
.sf-hero p{font-size:17px;color:var(--brown-l);line-height:1.7;max-width:480px;margin-bottom:28px}
.sf-hero-btns{display:flex;gap:12px;flex-wrap:wrap}
.sf-btn-primary{padding:14px 28px;background:var(--saf);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s;box-shadow:var(--shadow)}
.sf-btn-primary:hover{background:var(--saf-d);box-shadow:var(--shadow-lg);transform:translateY(-1px)}
.sf-btn-secondary{padding:14px 24px;background:#fff;color:var(--saf);border:2px solid var(--saf-bb);border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s}
.sf-btn-secondary:hover{background:var(--saf-l);border-color:var(--saf)}
.sf-hero-info{background:#fff;border:2px solid var(--saf-b);border-radius:20px;padding:28px;box-shadow:var(--shadow)}
.sf-info-item{display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid var(--saf-b)}
.sf-info-item:last-child{border-bottom:none;padding-bottom:0}
.sf-info-icon{width:38px;height:38px;border-radius:10px;background:var(--saf-l);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.sf-info-label{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--saf);font-weight:700;margin-bottom:2px}
.sf-info-value{font-size:13px;color:var(--gray-800);font-weight:600;line-height:1.4}

/* ── Section layout ── */
.sf-section{padding:72px 5%}
.sf-section-inner{max-width:1200px;margin:0 auto}
.sf-section.alt{background:#fff}
.sf-section-tag{font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--saf);font-weight:700;margin-bottom:8px}
.sf-section-title{font-size:clamp(22px,3vw,36px);font-weight:800;color:var(--brown);line-height:1.2;letter-spacing:-.01em;margin-bottom:10px}
.sf-section-sub{font-size:15px;color:var(--brown-l);max-width:500px;line-height:1.7;margin-bottom:36px}

/* ── Services / Products ── */
.sf-services-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px}
.sf-svc-card{background:#fff;border:2px solid var(--saf-b);border-radius:16px;padding:22px;transition:all .22s;cursor:pointer;position:relative;overflow:hidden}
.sf-svc-card::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,transparent,rgba(234,88,12,.03));pointer-events:none}
.sf-svc-card:hover{border-color:var(--saf);box-shadow:var(--shadow-lg);transform:translateY(-3px)}
.sf-svc-icon{width:50px;height:50px;border-radius:14px;background:linear-gradient(135deg,var(--saf-l),var(--saf-b));display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:14px}
.sf-svc-name{font-size:16px;font-weight:700;color:var(--brown);margin-bottom:6px}
.sf-svc-desc{font-size:13px;color:var(--gray-600);line-height:1.6;margin-bottom:12px}
.sf-svc-price{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:var(--saf);color:#fff;border-radius:999px;font-size:12px;font-weight:700}

/* ── Owner card ── */
.sf-owner-grid{display:grid;grid-template-columns:360px 1fr;gap:48px;align-items:start}
.sf-owner-card{background:linear-gradient(145deg,var(--saf),var(--saf-d) 70%,#7c2d12);border-radius:24px;padding:32px;color:#fff;box-shadow:var(--shadow-lg);position:relative;overflow:hidden}
.sf-owner-card::before{content:'';position:absolute;width:300px;height:300px;border-radius:50%;top:-100px;right:-100px;background:radial-gradient(circle,rgba(255,255,255,.15),transparent 70%)}
.sf-owner-top{display:flex;align-items:center;gap:16px;margin-bottom:20px;position:relative;z-index:1}
.sf-owner-photo{width:80px;height:80px;border-radius:20px;object-fit:cover;border:3px solid rgba(255,255,255,.5);flex-shrink:0}
.sf-owner-fallback{width:80px;height:80px;border-radius:20px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;color:#fff;flex-shrink:0}
.sf-owner-name{font-size:20px;font-weight:800;color:#fff;margin-bottom:2px}
.sf-owner-role{font-size:11px;color:rgba(255,255,255,.8);font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px}
.sf-owner-bio{font-size:13px;color:rgba(255,255,255,.9);line-height:1.6;margin-bottom:20px;position:relative;z-index:1}
.sf-owner-chips{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;position:relative;z-index:1}
.sf-owner-chip{display:inline-flex;align-items:center;padding:5px 11px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:999px;font-size:11px;font-weight:700;color:#fff}
.sf-owner-btns{display:flex;gap:8px;position:relative;z-index:1}
.sf-owner-btn-white{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;background:#fff;color:var(--saf);border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;transition:all .18s}
.sf-owner-btn-white:hover{background:var(--saf-l);color:var(--saf-d)}
.sf-owner-btn-ghost{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;background:rgba(255,255,255,.15);color:#fff;border-radius:10px;border:1.5px solid rgba(255,255,255,.35);font-size:13px;font-weight:700;text-decoration:none;transition:all .18s}
.sf-owner-btn-ghost:hover{background:rgba(255,255,255,.25)}

.sf-owner-right{display:flex;flex-direction:column;gap:16px}
.sf-owner-detail{background:var(--saf-ll);border:2px solid var(--saf-b);border-radius:14px;padding:18px}
.sf-owner-detail-label{font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--saf);font-weight:700;margin-bottom:6px}
.sf-owner-detail-value{font-size:15px;color:var(--brown);font-weight:600}

/* ── Why us ── */
.sf-why-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px}
.sf-why-card{background:var(--saf-ll);border:2px solid var(--saf-b);border-radius:14px;padding:20px;transition:all .2s}
.sf-why-card:hover{border-color:var(--saf);background:#fff;box-shadow:var(--shadow)}
.sf-why-icon{width:44px;height:44px;border-radius:12px;background:var(--saf);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px}
.sf-why-title{font-size:14px;font-weight:700;color:var(--brown);margin-bottom:6px}
.sf-why-desc{font-size:13px;color:var(--brown-l);line-height:1.6}

/* ── CTA ── */
.sf-cta{background:linear-gradient(135deg,var(--brown),var(--saf-d) 50%,var(--saf));padding:72px 5%;text-align:center;position:relative;overflow:hidden}
.sf-cta::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='2' cy='2' r='1.5' fill='rgba(255,255,255,0.05)'/%3E%3C/svg%3E") repeat;pointer-events:none}
.sf-cta-inner{max-width:640px;margin:0 auto;position:relative;z-index:1}
.sf-cta h2{font-size:clamp(24px,3.5vw,40px);font-weight:800;color:#fff;line-height:1.2;margin-bottom:12px}
.sf-cta p{font-size:16px;color:rgba(255,255,255,.87);line-height:1.7;margin-bottom:28px}
.sf-cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.sf-cta-btn-white{padding:14px 28px;background:#fff;color:var(--saf);border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;box-shadow:0 4px 14px rgba(0,0,0,.2)}
.sf-cta-btn-white:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.25)}
.sf-cta-btn-outline{padding:14px 24px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.5);border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .2s}
.sf-cta-btn-outline:hover{background:rgba(255,255,255,.12);border-color:#fff}

/* ── Footer ── */
.sf-footer{background:var(--brown);color:rgba(255,255,255,.75);padding:32px 5%;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:13px}
.sf-footer-brand{font-weight:800;color:#fff;font-size:16px}
.sf-footer a{color:rgba(255,255,255,.75)}
.sf-footer a:hover{color:#fff}

@media(max-width:900px){
  .sf-hero-inner,.sf-owner-grid{grid-template-columns:1fr}
  .sf-hero-info{display:none}
}
@media(max-width:600px){
  .sf-hero{padding:48px 5% 40px}
  .sf-section{padding:48px 5%}
  .sf-nav{display:none}
}
</style>
</head>
<body>
<div class="sf-shell">

@if($ownerCanManage)
<div class="sf-owner-bar">
  <a href="{{ route('shops.show', $shop) }}" class="sf-owner-btn primary">✏️ Edit Page</a>
  <span style="margin-left:auto;font-size:12px;color:#9ca3af">Owner View</span>
</div>
@endif

@if($specialOffer)
<div class="sf-offer-banner">{{ $specialOffer }}</div>
@endif

{{-- Header --}}
<header class="sf-header">
  <div class="sf-header-inner">
    <div class="sf-logo">{{ $brandName }}</div>
    <nav class="sf-nav">
      <a href="#services">Products</a>
      <a href="#owner">About</a>
      <a href="#contact">Contact</a>
      @if($callUrl)<a href="{{ $callUrl }}" class="sf-nav-cta">📞 Call Now</a>@endif
    </nav>
  </div>
</header>

{{-- Hero --}}
<section class="sf-hero">
  <div class="sf-hero-inner">
    <div>
      <div class="sf-hero-tag">{{ $heroBadge }}</div>
      <h1>{{ $heroTitle }}</h1>
      <p>{{ $heroDesc }}</p>
      <div class="sf-hero-btns">
        @if($callUrl)<a href="{{ $callUrl }}" class="sf-btn-primary">📞 {{ $primaryCta }}</a>@else<span class="sf-btn-primary" style="cursor:default">{{ $primaryCta }}</span>@endif
        @if($waUrl)<a href="{{ $waUrl }}" class="sf-btn-secondary" target="_blank" rel="noopener">💬 WhatsApp</a>@else<a href="#services" class="sf-btn-secondary">{{ $secondaryCta }}</a>@endif
      </div>
    </div>
    <div class="sf-hero-info">
      @if($shop->address)
      <div class="sf-info-item">
        <div class="sf-info-icon">📍</div>
        <div><div class="sf-info-label">Address</div><div class="sf-info-value">{{ $shop->address }}</div></div>
      </div>
      @endif
      @if($openingHours)
      <div class="sf-info-item">
        <div class="sf-info-icon">🕐</div>
        <div><div class="sf-info-label">Opening Hours</div><div class="sf-info-value">{{ $openingHours }}</div></div>
      </div>
      @endif
      @if($localityNote)
      <div class="sf-info-item">
        <div class="sf-info-icon">🗺️</div>
        <div><div class="sf-info-label">Service Area</div><div class="sf-info-value">{{ $localityNote }}</div></div>
      </div>
      @endif
      @if($providerContact)
      <div class="sf-info-item">
        <div class="sf-info-icon">📞</div>
        <div><div class="sf-info-label">Phone</div><div class="sf-info-value">{{ $providerContact }}</div></div>
      </div>
      @endif
    </div>
  </div>
</section>

{{-- Services / Products --}}
<section class="sf-section alt" id="services">
  <div class="sf-section-inner">
    <div class="sf-section-tag">{{ $servicesLabel }}</div>
    <div class="sf-section-title">{{ $servicesTitle }}</div>
    <div class="sf-section-sub">{{ $servicesSub }}</div>
    <div class="sf-services-grid">
      @foreach($services as $svc)
      <div class="sf-svc-card">
        <div class="sf-svc-icon">{{ $svc['icon'] ?? '🛍️' }}</div>
        <div class="sf-svc-name">{{ $svc['name'] }}</div>
        @if(!empty($svc['description']))<div class="sf-svc-desc">{{ $svc['description'] }}</div>@endif
        @if(!empty($svc['price']))<div class="sf-svc-price">{{ $svc['price'] }}</div>@endif
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Owner --}}
<section class="sf-section" id="owner">
  <div class="sf-section-inner">
    <div class="sf-section-tag">आमच्याबद्दल</div>
    <div class="sf-section-title">{{ $providerName }}</div>
    <div class="sf-owner-grid">
      <div class="sf-owner-card">
        <div class="sf-owner-top">
          @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $providerName }}" class="sf-owner-photo">
          @else
            <div class="sf-owner-fallback">{{ mb_substr($providerName,0,1) }}</div>
          @endif
          <div>
            <div class="sf-owner-name">{{ $providerName }}</div>
            <div class="sf-owner-role">{{ $providerTitle }}</div>
          </div>
        </div>
        <div class="sf-owner-bio">{{ $providerBio }}</div>
        <div class="sf-owner-chips">
          @if($providerExp)<span class="sf-owner-chip">⭐ {{ $providerExp }}</span>@endif
          <span class="sf-owner-chip">✓ Verified</span>
          <span class="sf-owner-chip">🛡️ Trusted</span>
        </div>
        <div class="sf-owner-btns">
          @if($callUrl)<a href="{{ $callUrl }}" class="sf-owner-btn-white">📞 Call</a>@endif
          @if($waUrl)<a href="{{ $waUrl }}" class="sf-owner-btn-ghost" target="_blank">💬 WhatsApp</a>@endif
        </div>
      </div>
      <div class="sf-owner-right">
        @if($providerContact)
        <div class="sf-owner-detail">
          <div class="sf-owner-detail-label">📞 Contact</div>
          <div class="sf-owner-detail-value">{{ $providerContact }}</div>
        </div>
        @endif
        @if($providerEmail)
        <div class="sf-owner-detail">
          <div class="sf-owner-detail-label">📧 Email</div>
          <div class="sf-owner-detail-value">{{ $providerEmail }}</div>
        </div>
        @endif
        @if($shop->address)
        <div class="sf-owner-detail">
          <div class="sf-owner-detail-label">📍 Address</div>
          <div class="sf-owner-detail-value">{{ $shop->address }}</div>
        </div>
        @endif
        @if($openingHours)
        <div class="sf-owner-detail">
          <div class="sf-owner-detail-label">🕐 Hours</div>
          <div class="sf-owner-detail-value">{{ $openingHours }}</div>
        </div>
        @endif
        @if($localityNote)
        <div class="sf-owner-detail">
          <div class="sf-owner-detail-label">🗺️ Service Area</div>
          <div class="sf-owner-detail-value">{{ $localityNote }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- Why Us --}}
<section class="sf-section alt" id="why">
  <div class="sf-section-inner">
    <div class="sf-section-tag">आमची खासियत</div>
    <div class="sf-section-title">{{ $whyTitle }}</div>
    <div class="sf-section-sub">{{ $whySub }}</div>
    <div class="sf-why-grid">
      <div class="sf-why-card"><div class="sf-why-icon">⭐</div><div class="sf-why-title">दर्जेदार उत्पादने</div><div class="sf-why-desc">उत्तम गुणवत्ता आणि विश्वसनीय ब्रँड्सची निवड.</div></div>
      <div class="sf-why-card"><div class="sf-why-icon">💰</div><div class="sf-why-title">परवडणाऱ्या किंमती</div><div class="sf-why-desc">स्पर्धात्मक दर — कुठलेही छुपे शुल्क नाही.</div></div>
      <div class="sf-why-card"><div class="sf-why-icon">⚡</div><div class="sf-why-title">जलद सेवा</div><div class="sf-why-desc">त्वरित प्रतिसाद आणि वेळेत वितरण.</div></div>
      <div class="sf-why-card"><div class="sf-why-icon">🤝</div><div class="sf-why-title">विश्वासार्ह नाव</div><div class="sf-why-desc">वर्षानुवर्षे परिसरातील ग्राहकांचा विश्वास.</div></div>
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="sf-cta" id="contact">
  <div class="sf-cta-inner">
    <h2>{{ $ctaTitle }}</h2>
    <p>{{ $ctaDesc }}</p>
    <div class="sf-cta-btns">
      @if($callUrl)<a href="{{ $callUrl }}" class="sf-cta-btn-white">📞 {{ $ctaButton }}</a>@else<span class="sf-cta-btn-white">{{ $ctaButton }}</span>@endif
      @if($waUrl)<a href="{{ $waUrl }}" class="sf-cta-btn-outline" target="_blank" rel="noopener">💬 WhatsApp</a>@endif
    </div>
    @if($shop->id)
    <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
      <a href="https://wa.me/?text={{ $shareText }}" class="sf-cta-btn-outline" target="_blank" rel="noopener" style="font-size:12px;padding:8px 16px">📤 Share Page</a>
    </div>
    @endif
  </div>
</section>

{{-- Footer --}}
<footer class="sf-footer">
  <div class="sf-footer-brand">{{ $brandName }}</div>
  <div>{{ $footerTagline }}</div>
  <div>Powered by <a href="{{ url('/') }}">AajchaOffer</a></div>
</footer>

</div>
</body>
</html>
