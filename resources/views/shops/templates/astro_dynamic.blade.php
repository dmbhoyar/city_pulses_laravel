@php
  $cfg = $shop->page_config ?? [];
  $tc  = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];
  $services = is_array($cfg['services'] ?? null)
    ? array_values(array_filter($cfg['services'], fn($s) => !empty($s['name'])))
    : [];

  /* ── Brand / Hero ── */
  $brandName       = trim((string)($tc['footer_brand']     ?? $shop->name ?? 'Nakshtea Astro'));
  $heroTitle       = trim((string)($tc['hero_title']       ?? $brandName));
  $heroBadge       = trim((string)($tc['hero_badge']       ?? 'Vedic Astrology · Numerology · Vastu · Remedies'));
  $heroTagline     = trim((string)($tc['footer_tagline']   ?? 'Ancient Wisdom. Modern Guidance.'));
  $heroDescription = trim((string)($tc['hero_description'] ?? 'Personalised birth chart readings, powerful remedies, and live expert guidance — in Hindi & English, 24/7.'));
  $primaryCta      = trim((string)($tc['primary_cta']      ?? 'Talk to Astrologer'));
  $secondaryCta    = trim((string)($tc['secondary_cta']    ?? 'Horoscope 2026'));
  $ctaTitle        = trim((string)($tc['cta_title']        ?? 'Get Your Personalised Reading Today'));
  $ctaDesc         = trim((string)($tc['cta_description']  ?? 'Accurate, confidential guidance on any life question — in Hindi & English, available 24/7'));
  $ctaButton       = trim((string)($tc['cta_button']       ?? 'Get Callback'));

  /* ── Hero Stats ── */
  $stats = is_array($tc['hero_stats'] ?? null) && count($tc['hero_stats'])
    ? $tc['hero_stats']
    : [
        ['value'=>'50K+', 'label'=>'Happy Clients'],
        ['value'=>'25',   'label'=>'Years Experience'],
        ['value'=>'4.9★', 'label'=>'App Rating'],
        ['value'=>'24/7', 'label'=>'Available'],
      ];

  /* ── Provider ── */
  $providerName    = trim((string)($tc['provider_name']    ?? optional($shop->user)->full_name   ?? 'Astro Expert'));
  $providerContact = trim((string)($tc['provider_contact'] ?? $shop->phone                       ?? optional($shop->user)->mobile_number ?? ''));
  $providerEmail   = trim((string)($tc['provider_email']   ?? optional($shop->user)->email       ?? ''));
  $providerBio     = trim((string)($tc['provider_bio']     ?? 'Our expert astrologers are available 24/7 for consultations via phone, WhatsApp, or video call in Hindi and English.'));

  /* ── Why Us ── */
  $whyTitle    = trim((string)($tc['why_title']    ?? 'Trusted by 50,000+ Clients'));
  $whySubtitle = trim((string)($tc['why_subtitle'] ?? 'Authentic, accurate, and always available — the promise'));
  $defaultWhy  = [
    ['ico'=>'🎓','title'=>'Expert Astrologers',     'desc'=>'All astrologers are certified Jyotish Acharyas with 15+ years of practice and thousands of readings.'],
    ['ico'=>'🔒','title'=>'100% Confidential',      'desc'=>'Your personal details and life questions are completely private and never shared with anyone.'],
    ['ico'=>'⚡','title'=>'Instant Reports',          'desc'=>'Digital reports delivered within 24 hours. Live consultations available within 60 minutes of booking.'],
    ['ico'=>'🌍','title'=>'Hindi & English',          'desc'=>'Full consultation available in Hindi and English to make guidance accessible to every client.'],
    ['ico'=>'💎','title'=>'Certified Gemstones',      'desc'=>'All gemstones are lab-certified, naturally energised, and sourced from trusted mines globally.'],
    ['ico'=>'📱','title'=>'Easy Online Booking',      'desc'=>'Book a consultation, order a report, or get remedies — all from your phone in under 2 minutes.'],
    ['ico'=>'🛡️','title'=>'Satisfaction Guarantee',  'desc'=>'Not satisfied with your reading? We offer a free follow-up session — no questions asked.'],
    ['ico'=>'✨','title'=>'Authentic Vedic Methods',  'desc'=>'Classical Parashari, Jaimini, Lal Kitab, and KP systems — not generic computer-generated reports.'],
  ];
  $whyItems = is_array($tc['why_items'] ?? null) && count($tc['why_items']) ? $tc['why_items'] : $defaultWhy;

  /* ── Testimonials ── */
  $defaultTestimonials = [
    ['text'=>'"The career reading was accurate to the month. My job change happened exactly when predicted. The remedies gave me true confidence to move forward."','name'=>'Rahul K.','loc'=>'Mumbai · Career Reading','initials'=>'RK'],
    ['text'=>'"The Kundali Milan was incredibly detailed. Found out about a Manglik dosha and got proper remedies. Got married within 8 months of the consultation!"','name'=>'Priya S.','loc'=>'Delhi · Match Making','initials'=>'PS'],
    ['text'=>'"Got my gemstone prescribed and within 3 months my business turned profitable. Everything the astrologer predicted came true. Highly recommend!"','name'=>'Ankit M.','loc'=>'Pune · Business + Gemstone','initials'=>'AM'],
    ['text'=>'"Sade Sati was destroying my life and I didn\'t know why. The Saturn Transit report opened my eyes and the remedies brought immediate relief."','name'=>'Sunita J.','loc'=>'Ahmedabad · Saturn Transit','initials'=>'SJ'],
    ['text'=>'"The Vastu correction for my office took just a few changes but the difference was extraordinary. More clients, better energy, and team harmony improved."','name'=>'Vikram R.','loc'=>'Bangalore · Vastu Shastra','initials'=>'VR'],
    ['text'=>'"My name change suggestion from numerology analysis was spot on. Business revenue doubled in 6 months. This is not coincidence — it is science!"','name'=>'Nitin K.','loc'=>'Surat · Numerology + Name Change','initials'=>'NK'],
  ];
  $testimonials = is_array($tc['testimonials'] ?? null) && count($tc['testimonials']) ? $tc['testimonials'] : $defaultTestimonials;

  /* ── Service Groups ──────────────────────────────────────────────────────
     Structure in $tc['service_groups']:
       [ { eyebrow, title, subtitle, items:[{icon,name,description,price}] } ]
     Fallback: auto-split flat $services into up to 2 groups using
     $tc['astrology_*'] and $tc['services_*'] as headings.
  ──────────────────────────────────────────────────────────────────────── */
  $defaultFlatItems = [
    ['icon'=>'📅','name'=>'Horoscope Reading',  'description'=>'Year-ahead forecasts with monthly predictions.',      'price'=>'From ₹499'],
    ['icon'=>'🔢','name'=>'Numerology',           'description'=>'Name and destiny number analysis.',                  'price'=>'From ₹499'],
    ['icon'=>'🏡','name'=>'Vastu Guidance',       'description'=>'Home and office Vastu correction support.',          'price'=>'From ₹499'],
    ['icon'=>'🤝','name'=>'Match Making',          'description'=>'Detailed compatibility and kundali matching.',       'price'=>'From ₹499'],
  ];
  $flatServices = count($services) ? array_map(fn($s) => [
    'icon'        => trim((string)($s['icon']        ?? '✨')),
    'name'        => trim((string)($s['name']        ?? 'Service')),
    'description' => trim((string)($s['description'] ?? '')),
    'price'       => trim((string)($s['price']       ?? 'From ₹499')),
  ], $services) : $defaultFlatItems;

  /* Load configured groups */
  $savedGroups = is_array($tc['service_groups'] ?? null) ? $tc['service_groups'] : [];
  $savedGroups = array_values(array_filter($savedGroups, fn($g) => !empty($g['title'] ?? '')));

  if (count($savedGroups)) {
    $serviceGroups = array_map(fn($g) => [
      'eyebrow'  => trim((string)($g['eyebrow']  ?? '✦ Services')),
      'title'    => trim((string)($g['title']    ?? 'Our Services')),
      'subtitle' => trim((string)($g['subtitle'] ?? '')),
      'items'    => is_array($g['items'] ?? null) ? array_values(array_filter(array_map(fn($i) => [
        'icon'        => trim((string)($i['icon']        ?? '✨')),
        'name'        => trim((string)($i['name']        ?? '')),
        'description' => trim((string)($i['description'] ?? '')),
        'price'       => trim((string)($i['price']       ?? 'From ₹499')),
      ], $g['items']), fn($i) => !empty($i['name']))) : [],
    ], $savedGroups);
  } else {
    /* Auto-split: first half → Astrology section, second half → Services section */
    $half = (int)ceil(count($flatServices) / 2);
    $serviceGroups = [];
    if ($half > 0) {
      $serviceGroups[] = [
        'eyebrow'  => trim((string)($tc['astrology_eyebrow'] ?? '01 · Astrology Consultation')),
        'title'    => trim((string)($tc['astrology_title']   ?? 'Expert Readings for Every Life Area')),
        'subtitle' => trim((string)($tc['astrology_subtitle']?? 'Personalised consultations — accurate, confidential, and deeply insightful')),
        'items'    => array_slice($flatServices, 0, $half),
      ];
    }
    $remaining = array_slice($flatServices, $half);
    if (count($remaining)) {
      $serviceGroups[] = [
        'eyebrow'  => trim((string)($tc['services_eyebrow']  ?? '02 · Premium Services')),
        'title'    => trim((string)($tc['services_title']    ?? 'Our Astrology Services')),
        'subtitle' => trim((string)($tc['services_subtitle'] ?? 'Deep reports, sacred rituals, transit analysis and specialist readings for every need')),
        'items'    => $remaining,
      ];
    }
  }

  /* Flat list of all items (for contact dropdown, footer) */
  $allItems = count($serviceGroups) ? array_merge(...array_column($serviceGroups, 'items')) : $flatServices;

  /* Products — always the same sacred items */
  $products = [
    ['ico'=>'💎','name'=>'Gemstone / Rashi Ratna','description'=>'Certified natural gemstones prescribed as per your Rashi and planetary positions for maximum benefit and positive results.','badge'=>'Lab Certified'],
    ['ico'=>'🏡','name'=>'Vastu Products',         'description'=>'Yantra, pyramids, crystals, copper plates, and sacred items for home and office Vastu correction and positive energy flow.','badge'=>'Energised & Blessed'],
    ['ico'=>'🛠️','name'=>'Vastu Tools',            'description'=>'Professional Vastu measurement tools, compass sets, divination instruments and assessment kits for Vastu practitioners.','badge'=>'Professional Grade'],
  ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $heroTitle }} – Vedic Astrology, Numerology &amp; Vastu</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   BASE RESET
══════════════════════════════════════════ */
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;width:100%;max-width:100%;overflow-x:hidden;}
:root{
  --gold:#f5c518;--gold2:#c97000;
  --bg:#03000d;--surface:rgba(255,255,255,.03);
  --border:rgba(245,197,24,.12);--text:#f0e8d0;
  --muted:rgba(240,220,160,.5);--nav-h:70px;
}
body{font-family:'Outfit',sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden;width:100%;max-width:100%;}

/* ══════════════════════════════════════════
   KEYFRAMES
══════════════════════════════════════════ */
@keyframes slowSpin{from{transform:translate(-50%,-50%) rotate(0deg)}to{transform:translate(-50%,-50%) rotate(360deg)}}
@keyframes fadeDown{from{opacity:0;transform:translateY(-14px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes pulseDot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.3;transform:scale(1.5)}}
@keyframes modalIn{from{opacity:0;transform:scale(.92) translateY(20px)}to{opacity:1;transform:scale(1) translateY(0)}}
@keyframes waPulse{0%,100%{box-shadow:0 4px 20px rgba(37,211,102,.4)}50%{box-shadow:0 4px 32px rgba(37,211,102,.7),0 0 0 8px rgba(37,211,102,.1)}}

/* ══════════════════════════════════════════
   COSMOS BACKGROUND
══════════════════════════════════════════ */
.cosmos{position:fixed;inset:0;z-index:0;pointer-events:none;
  background:
    radial-gradient(ellipse at 20% 30%,rgba(80,0,150,.35) 0%,transparent 50%),
    radial-gradient(ellipse at 80% 70%,rgba(10,0,80,.45) 0%,transparent 55%),
    radial-gradient(ellipse at 50% 5%,rgba(200,100,0,.09) 0%,transparent 40%),
    radial-gradient(ellipse at 10% 90%,rgba(60,0,120,.2) 0%,transparent 45%),
    var(--bg);
}
#starCanvas{position:absolute;inset:0;width:100%;height:100%;}

/* ══════════════════════════════════════════
   NAVBAR
══════════════════════════════════════════ */
.nav{position:fixed;top:0;left:0;right:0;z-index:500;height:var(--nav-h);
  display:flex;align-items:center;justify-content:space-between;padding:0 5%;
  background:rgba(3,0,13,0);border-bottom:1px solid transparent;
  transition:background .4s,border-color .4s,backdrop-filter .4s;}
.nav.scrolled{background:rgba(3,0,13,.92);border-color:rgba(245,197,24,.12);backdrop-filter:blur(18px);}
.nav-logo{font-family:'Cinzel',serif;font-size:20px;font-weight:700;color:var(--gold);
  letter-spacing:.1em;cursor:pointer;line-height:1;text-decoration:none;}
.nav-logo small{display:block;font-family:'Outfit',sans-serif;font-size:9px;
  letter-spacing:.22em;text-transform:uppercase;color:rgba(245,197,24,.38);font-weight:300;margin-top:2px;}
.nav-links{display:flex;gap:6px;align-items:center;}
.nav-links a{font-size:12px;color:rgba(255,220,150,.55);text-decoration:none;
  letter-spacing:.08em;text-transform:uppercase;cursor:pointer;font-weight:500;
  padding:6px 12px;border-radius:4px;transition:color .2s,background .2s;}
.nav-links a:hover{color:var(--gold);background:rgba(245,197,24,.06);}
.nav-links a.active{color:var(--gold);}
.nav-cta{padding:10px 22px;background:linear-gradient(135deg,var(--gold2),var(--gold));
  border:none;border-radius:4px;color:#1a0800;font-size:12px;font-weight:700;
  font-family:'Outfit',sans-serif;letter-spacing:.08em;text-transform:uppercase;
  cursor:pointer;transition:opacity .2s,transform .2s;
  box-shadow:0 0 18px rgba(245,197,24,.2);white-space:nowrap;}
.nav-cta:hover{opacity:.85;transform:translateY(-1px);}

/* Hamburger */
.hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;
  padding:8px;background:none;border:none;z-index:600;}
.hamburger span{display:block;width:24px;height:2px;background:var(--gold);
  border-radius:2px;transition:all .3s;}
.hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg);}
.hamburger.open span:nth-child(2){opacity:0;transform:scaleX(0);}
.hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}

/* Mobile Menu */
.mobile-menu{display:none;position:fixed;inset:var(--nav-h) 0 0 0;
  background:rgba(3,0,13,.97);z-index:490;flex-direction:column;
  padding:32px 5%;gap:4px;overflow-y:auto;overflow-x:hidden;border-top:1px solid var(--border);
  width:100%;max-width:100vw;
  transform:translate3d(100%,0,0);visibility:hidden;pointer-events:none;
  transition:transform .35s cubic-bezier(.4,0,.2,1),visibility .35s;}
.mobile-menu.open{transform:translate3d(0,0,0);visibility:visible;pointer-events:auto;}
.mobile-menu a{font-size:16px;color:rgba(255,220,150,.65);text-decoration:none;
  letter-spacing:.08em;text-transform:uppercase;padding:14px 16px;border-radius:8px;
  border-bottom:1px solid rgba(245,197,24,.06);transition:color .2s,background .2s;
  cursor:pointer;font-weight:500;}
.mobile-menu a:hover{color:var(--gold);background:rgba(245,197,24,.05);}
.mobile-menu .m-cta{margin-top:20px;padding:15px;
  background:linear-gradient(135deg,var(--gold2),var(--gold));
  border:none;border-radius:6px;color:#1a0800;font-size:15px;font-weight:700;
  font-family:'Outfit',sans-serif;letter-spacing:.08em;text-transform:uppercase;
  cursor:pointer;text-align:center;}
.mobile-menu .wa-m{margin-top:12px;padding:14px;background:rgba(37,211,102,.15);
  border:1px solid rgba(37,211,102,.3);border-radius:6px;color:#25d366;
  font-size:15px;font-weight:700;font-family:'Outfit',sans-serif;
  letter-spacing:.06em;text-transform:uppercase;cursor:pointer;text-align:center;
  display:flex;align-items:center;justify-content:center;gap:8px;}

/* ══════════════════════════════════════════
   HERO
══════════════════════════════════════════ */
.hero{position:relative;z-index:2;min-height:100vh;display:flex;flex-direction:column;
  align-items:center;justify-content:center;text-align:center;
  padding:calc(var(--nav-h) + 40px) 6% 80px;overflow:hidden;}
.hero,.section,.cta-section,footer,.nav{overflow-x:hidden;}
.hero-orbit{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
  width:680px;height:680px;border-radius:50%;
  border:1px solid rgba(245,197,24,.06);pointer-events:none;
  animation:slowSpin 80s linear infinite;}
.hero-orbit::after{content:'✦';position:absolute;top:-10px;left:50%;
  transform:translateX(-50%);font-size:14px;color:rgba(245,197,24,.4);}
.hero-orbit2{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
  width:440px;height:440px;border-radius:50%;
  border:1px solid rgba(245,197,24,.09);pointer-events:none;
  animation:slowSpin 50s linear infinite reverse;}
.hero-badge{display:inline-flex;align-items:center;gap:8px;
  background:rgba(245,197,24,.07);border:1px solid rgba(245,197,24,.22);
  border-radius:30px;padding:7px 20px;
  font-size:11px;letter-spacing:.16em;text-transform:uppercase;
  color:var(--gold);margin-bottom:30px;
  animation:fadeDown .8s ease forwards;}
.badge-dot{width:6px;height:6px;border-radius:50%;background:var(--gold);
  animation:pulseDot 2s ease-in-out infinite;}
.hero h1{font-family:'Cinzel',serif;
  font-size:clamp(46px,9vw,104px);font-weight:900;line-height:.92;
  background:linear-gradient(180deg,#fff 0%,var(--gold) 45%,var(--gold2) 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  margin-bottom:16px;letter-spacing:.04em;
  animation:fadeUp .8s .15s ease both;}
.hero-tagline{font-family:'Cormorant Garamond',serif;font-style:italic;
  font-size:clamp(18px,2.8vw,28px);color:rgba(240,220,160,.65);
  margin-bottom:24px;letter-spacing:.04em;
  animation:fadeUp .8s .25s ease both;}
.hero-desc{font-size:16px;color:var(--muted);line-height:1.85;
  max-width:560px;margin:0 auto 46px;
  animation:fadeUp .8s .35s ease both;}
.hero-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;
  margin-bottom:70px;animation:fadeUp .8s .45s ease both;}
.btn-gold{padding:15px 38px;
  background:linear-gradient(135deg,var(--gold2),var(--gold),var(--gold2));
  background-size:200%;border:none;border-radius:5px;
  color:#1a0800;font-size:14px;font-weight:700;
  font-family:'Outfit',sans-serif;letter-spacing:.1em;text-transform:uppercase;
  cursor:pointer;transition:background-position .4s,transform .2s,box-shadow .2s;
  box-shadow:0 4px 22px rgba(245,197,24,.22);}
.btn-gold:hover{background-position:100%;transform:translateY(-2px);
  box-shadow:0 8px 34px rgba(245,197,24,.38);}
.btn-ghost{padding:15px 36px;background:transparent;
  border:1.5px solid rgba(245,197,24,.35);border-radius:5px;
  color:var(--gold);font-size:14px;font-family:'Outfit',sans-serif;
  letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:all .2s;}
.btn-ghost:hover{border-color:var(--gold);background:rgba(245,197,24,.06);}
.hero-stats{display:flex;justify-content:center;gap:52px;flex-wrap:wrap;
  padding-top:38px;border-top:1px solid rgba(245,197,24,.1);
  animation:fadeUp .8s .55s ease both;}
.hstat-v{font-family:'Cinzel',serif;font-size:30px;font-weight:700;
  color:var(--gold);line-height:1;}
.hstat-v sup{font-size:16px;color:rgba(245,197,24,.55);}
.hstat-l{font-size:10px;letter-spacing:.14em;text-transform:uppercase;
  color:rgba(240,220,160,.38);margin-top:6px;}

/* ══════════════════════════════════════════
   SECTIONS COMMON
══════════════════════════════════════════ */
.section{position:relative;z-index:2;padding:80px 6%;scroll-margin-top:var(--nav-h);}
.section.alt{background:rgba(255,255,255,.017);
  border-top:1px solid rgba(245,197,24,.07);
  border-bottom:1px solid rgba(245,197,24,.07);}
.sec-head{text-align:center;margin-bottom:56px;}
.sec-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:10px;
  letter-spacing:.22em;text-transform:uppercase;
  color:var(--gold);margin-bottom:12px;font-weight:600;}
.sec-eyebrow::before,.sec-eyebrow::after{content:'✦';font-size:9px;opacity:.6;}
.sec-title{font-family:'Cinzel',serif;font-size:clamp(26px,4vw,46px);font-weight:700;
  color:var(--text);margin-bottom:12px;line-height:1.1;letter-spacing:.04em;}
.sec-sub{font-size:15px;color:var(--muted);max-width:480px;margin:0 auto;line-height:1.75;}
.divider{position:relative;z-index:2;display:flex;align-items:center;gap:16px;
  max-width:320px;margin:0 auto 72px;
  color:rgba(245,197,24,.35);font-size:13px;letter-spacing:.3em;}
.divider::before{content:'';flex:1;height:1px;
  background:linear-gradient(90deg,transparent,rgba(245,197,24,.28));}
.divider::after{content:'';flex:1;height:1px;
  background:linear-gradient(270deg,transparent,rgba(245,197,24,.28));}

/* ══════════════════════════════════════════
   SERVICE CARDS
══════════════════════════════════════════ */
.svc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;}
.svc-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;
  padding:26px 22px;cursor:pointer;position:relative;overflow:hidden;
  transition:border-color .25s,background .25s,transform .25s,box-shadow .25s;}
.svc-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,var(--gold2),var(--gold));
  transform:scaleX(0);transform-origin:left;transition:transform .3s;
  border-radius:0 0 14px 14px;}
.svc-card:hover{border-color:rgba(245,197,24,.35);background:rgba(245,197,24,.03);
  transform:translateY(-4px);
  box-shadow:0 14px 40px rgba(0,0,0,.45),0 0 0 1px rgba(245,197,24,.1);}
.svc-card:hover::after{transform:scaleX(1);}
.svc-card .ico{font-size:30px;margin-bottom:16px;display:block;}
.svc-card h3{font-family:'Cinzel',serif;font-size:13px;font-weight:600;
  color:var(--gold);margin-bottom:9px;letter-spacing:.05em;line-height:1.4;}
.svc-card p{font-size:13px;color:var(--muted);line-height:1.7;}
.card-cta{display:inline-block;margin-top:14px;font-size:11px;font-weight:700;
  color:var(--gold);letter-spacing:.1em;text-transform:uppercase;
  border-bottom:1px solid rgba(245,197,24,.3);padding-bottom:2px;
  opacity:0;transform:translateY(4px);transition:opacity .25s,transform .25s;}
.svc-card:hover .card-cta{opacity:1;transform:translateY(0);}
.svc-price{display:inline-block;margin-top:10px;background:rgba(245,197,24,.1);
  border:1px solid rgba(245,197,24,.22);border-radius:20px;
  padding:3px 12px;font-size:11px;color:rgba(245,197,24,.7);letter-spacing:.08em;}

/* ══════════════════════════════════════════
   PRODUCT CARDS
══════════════════════════════════════════ */
.prod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;}
.prod-card{background:linear-gradient(135deg,rgba(100,55,0,.28),rgba(50,15,0,.5));
  border:1px solid rgba(245,197,24,.18);border-radius:16px;padding:40px 28px;
  text-align:center;cursor:pointer;position:relative;overflow:hidden;
  transition:transform .22s,box-shadow .22s,border-color .22s;}
.prod-card::before{content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 50% 0%,rgba(245,197,24,.07),transparent 65%);
  opacity:0;transition:opacity .3s;}
.prod-card:hover{transform:translateY(-5px);border-color:rgba(245,197,24,.4);
  box-shadow:0 18px 50px rgba(0,0,0,.5),0 0 32px rgba(245,197,24,.1);}
.prod-card:hover::before{opacity:1;}
.prod-card .ico{font-size:48px;margin-bottom:18px;display:block;}
.prod-card h3{font-family:'Cinzel',serif;font-size:16px;font-weight:700;
  color:var(--gold);margin-bottom:10px;letter-spacing:.06em;}
.prod-card p{font-size:14px;color:var(--muted);line-height:1.7;margin-bottom:18px;}
.prod-badge{display:inline-block;background:rgba(245,197,24,.1);
  border:1px solid rgba(245,197,24,.22);border-radius:20px;padding:5px 16px;
  font-size:11px;color:rgba(245,197,24,.7);letter-spacing:.1em;text-transform:uppercase;}
.btn-buy{display:block;margin-top:16px;padding:11px 24px;
  background:linear-gradient(135deg,var(--gold2),var(--gold));
  border:none;border-radius:4px;color:#1a0800;font-size:12px;font-weight:700;
  font-family:'Outfit',sans-serif;letter-spacing:.08em;text-transform:uppercase;
  cursor:pointer;transition:opacity .2s;width:100%;}
.btn-buy:hover{opacity:.85;}

/* ══════════════════════════════════════════
   WHY US
══════════════════════════════════════════ */
.why-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;}
.why-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;
  padding:28px 22px;text-align:center;transition:border-color .2s,transform .2s;}
.why-card:hover{border-color:rgba(245,197,24,.3);transform:translateY(-3px);}
.why-ico{font-size:32px;margin-bottom:14px;display:block;}
.why-card h4{font-family:'Cinzel',serif;font-size:14px;font-weight:600;
  color:var(--gold);margin-bottom:8px;letter-spacing:.04em;}
.why-card p{font-size:13px;color:var(--muted);line-height:1.65;}

/* ══════════════════════════════════════════
   TESTIMONIALS
══════════════════════════════════════════ */
.testi-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:16px;}
.testi-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;
  padding:28px 24px;transition:border-color .2s,transform .2s;}
.testi-card:hover{border-color:rgba(245,197,24,.28);transform:translateY(-3px);}
.testi-stars{color:var(--gold);font-size:14px;letter-spacing:3px;margin-bottom:14px;}
.testi-text{font-family:'Cormorant Garamond',serif;font-size:16px;font-style:italic;
  color:rgba(240,220,160,.75);line-height:1.75;margin-bottom:20px;}
.testi-by{display:flex;align-items:center;gap:12px;}
.testi-av{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;
  justify-content:center;font-size:14px;font-weight:700;color:#1a0800;
  background:linear-gradient(135deg,var(--gold2),var(--gold));flex-shrink:0;}
.testi-name{font-size:14px;font-weight:600;color:var(--text);}
.testi-loc{font-size:12px;color:var(--muted);}

/* ══════════════════════════════════════════
   CTA BAND
══════════════════════════════════════════ */
.cta-section{position:relative;z-index:2;text-align:center;padding:90px 6%;
  border-top:1px solid rgba(245,197,24,.1);}
.cta-glow{position:absolute;top:0;left:50%;transform:translateX(-50%);
  width:700px;height:350px;
  background:radial-gradient(ellipse,rgba(200,100,0,.1) 0%,transparent 65%);
  pointer-events:none;}
.cta-section h2{font-family:'Cinzel',serif;font-size:clamp(28px,4.5vw,52px);
  font-weight:700;color:var(--gold);margin-bottom:14px;letter-spacing:.04em;
  position:relative;z-index:1;}
.cta-section p{font-size:16px;color:var(--muted);margin-bottom:40px;line-height:1.7;
  position:relative;z-index:1;max-width:500px;margin-left:auto;margin-right:auto;}
.cta-form{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;
  max-width:480px;margin:0 auto 20px;position:relative;z-index:1;}
.cta-input{flex:1;min-width:180px;padding:15px 20px;background:rgba(255,255,255,.05);
  border:1px solid rgba(245,197,24,.25);border-radius:5px;color:var(--text);
  font-size:14px;font-family:'Outfit',sans-serif;outline:none;transition:border-color .2s;}
.cta-input:focus{border-color:rgba(245,197,24,.6);}
.cta-input::placeholder{color:rgba(240,220,160,.3);}
.cta-note{font-size:12px;color:rgba(240,220,160,.35);letter-spacing:.06em;
  position:relative;z-index:1;}

/* ══════════════════════════════════════════
   CONTACT
══════════════════════════════════════════ */
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start;}
.contact-info h3{font-family:'Cinzel',serif;font-size:22px;color:var(--gold);
  margin-bottom:16px;letter-spacing:.06em;}
.contact-info p{font-size:15px;color:var(--muted);line-height:1.75;margin-bottom:28px;}
.contact-item{display:flex;gap:14px;align-items:flex-start;margin-bottom:20px;}
.c-ico{width:40px;height:40px;flex-shrink:0;border-radius:10px;
  background:rgba(245,197,24,.1);border:1px solid rgba(245,197,24,.2);
  display:flex;align-items:center;justify-content:center;font-size:18px;}
.c-label{font-size:12px;color:var(--muted);letter-spacing:.08em;
  text-transform:uppercase;margin-bottom:3px;}
.c-val{font-size:15px;color:var(--text);font-weight:500;}
.contact-form{background:rgba(255,255,255,.03);border:1px solid var(--border);
  border-radius:16px;padding:32px 28px;}
.contact-form h3{font-family:'Cinzel',serif;font-size:18px;color:var(--gold);
  margin-bottom:24px;letter-spacing:.06em;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;}
.form-field{margin-bottom:14px;}
.form-field label{display:block;font-size:11px;letter-spacing:.12em;text-transform:uppercase;
  color:rgba(245,197,24,.5);margin-bottom:6px;font-weight:600;}
.form-field input,.form-field select,.form-field textarea{
  width:100%;padding:13px 16px;background:rgba(255,255,255,.04);
  border:1px solid rgba(245,197,24,.15);border-radius:6px;
  color:var(--text);font-size:14px;font-family:'Outfit',sans-serif;
  outline:none;transition:border-color .2s;-webkit-appearance:none;}
.form-field input:focus,.form-field select:focus,.form-field textarea:focus{border-color:rgba(245,197,24,.5);}
.form-field input::placeholder,.form-field textarea::placeholder{color:rgba(240,220,160,.25);}
.form-field select option{background:#1a0a00;color:var(--text);}
.form-field textarea{resize:vertical;min-height:100px;}
.form-submit{width:100%;padding:14px;background:linear-gradient(135deg,var(--gold2),var(--gold));
  border:none;border-radius:6px;color:#1a0800;font-size:14px;font-weight:700;
  font-family:'Outfit',sans-serif;letter-spacing:.1em;text-transform:uppercase;
  cursor:pointer;transition:opacity .2s,transform .2s;
  box-shadow:0 4px 20px rgba(245,197,24,.2);}
.form-submit:hover{opacity:.88;transform:translateY(-2px);}

/* ══════════════════════════════════════════
   BOOKING MODAL
══════════════════════════════════════════ */
.modal-overlay{display:none;position:fixed;inset:0;z-index:900;
  background:rgba(0,0,0,.8);backdrop-filter:blur(8px);
  align-items:center;justify-content:center;padding:20px;}
.modal-overlay.open{display:flex;}
.modal{background:linear-gradient(135deg,#0d0520,#070118);
  border:1px solid rgba(245,197,24,.25);border-radius:20px;
  padding:40px 36px;max-width:480px;width:100%;position:relative;
  animation:modalIn .35s cubic-bezier(.16,1,.3,1);max-height:90vh;overflow-y:auto;}
.modal-close{position:absolute;top:16px;right:18px;width:32px;height:32px;
  border-radius:50%;background:rgba(255,255,255,.06);
  border:1px solid rgba(245,197,24,.15);color:var(--muted);font-size:18px;
  line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;
  transition:background .2s,color .2s;}
.modal-close:hover{background:rgba(245,197,24,.1);color:var(--gold);}
.modal-badge{font-size:36px;margin-bottom:14px;text-align:center;display:block;}
.modal h2{font-family:'Cinzel',serif;font-size:22px;color:var(--gold);
  margin-bottom:6px;letter-spacing:.06em;text-align:center;}
.modal-sub{font-size:13px;color:var(--muted);text-align:center;margin-bottom:28px;}
.modal-submit{width:100%;padding:14px;background:linear-gradient(135deg,var(--gold2),var(--gold));
  border:none;border-radius:6px;color:#1a0800;font-size:14px;font-weight:700;
  font-family:'Outfit',sans-serif;letter-spacing:.1em;text-transform:uppercase;
  cursor:pointer;transition:opacity .2s;box-shadow:0 4px 20px rgba(245,197,24,.25);}
.modal-submit:hover{opacity:.87;}
.modal-wa{display:flex;align-items:center;justify-content:center;gap:8px;
  width:100%;padding:13px;margin-top:10px;background:rgba(37,211,102,.12);
  border:1px solid rgba(37,211,102,.3);border-radius:6px;color:#25d366;
  font-size:14px;font-weight:700;font-family:'Outfit',sans-serif;
  letter-spacing:.06em;text-transform:uppercase;cursor:pointer;transition:background .2s;}
.modal-wa:hover{background:rgba(37,211,102,.2);}

/* ══════════════════════════════════════════
   TOAST
══════════════════════════════════════════ */
.toast{position:fixed;bottom:30px;left:50%;transform:translateX(-50%) translateY(20px);
  background:linear-gradient(135deg,#1a0800,#2d1400);
  border:1px solid rgba(245,197,24,.3);border-radius:8px;
  padding:14px 24px;color:var(--gold);font-size:14px;font-weight:600;
  z-index:999;opacity:0;transition:opacity .3s,transform .3s;
  white-space:nowrap;box-shadow:0 8px 32px rgba(0,0,0,.5);}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0);}

/* ══════════════════════════════════════════
   WHATSAPP FLOAT
══════════════════════════════════════════ */
.wa-float{position:fixed;bottom:28px;right:24px;z-index:400;
  width:58px;height:58px;border-radius:50%;
  background:linear-gradient(135deg,#128C7E,#25D366);
  display:flex;align-items:center;justify-content:center;cursor:pointer;
  box-shadow:0 4px 20px rgba(37,211,102,.4);
  animation:waPulse 2.5s ease-in-out infinite;
  border:2px solid rgba(255,255,255,.2);text-decoration:none;transition:transform .2s;}
.wa-float:hover{transform:scale(1.12);}
.wa-float svg{width:28px;height:28px;fill:#fff;}
.wa-tooltip{position:absolute;right:68px;top:50%;transform:translateY(-50%);
  background:#128C7E;color:#fff;font-size:12px;font-weight:600;white-space:nowrap;
  padding:6px 12px;border-radius:6px;opacity:0;pointer-events:none;
  transition:opacity .2s;letter-spacing:.04em;}
.wa-float:hover .wa-tooltip{opacity:1;}

/* ══════════════════════════════════════════
   BACK TO TOP
══════════════════════════════════════════ */
.back-top{position:fixed;bottom:28px;left:24px;z-index:400;
  width:44px;height:44px;border-radius:50%;
  background:rgba(245,197,24,.12);border:1px solid rgba(245,197,24,.3);
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:var(--gold);font-size:18px;
  transition:all .2s;opacity:0;pointer-events:none;}
.back-top.show{opacity:1;pointer-events:auto;}
.back-top:hover{background:rgba(245,197,24,.2);}

/* ══════════════════════════════════════════
   FOOTER
══════════════════════════════════════════ */
footer{position:relative;z-index:2;background:rgba(0,0,0,.55);
  border-top:1px solid rgba(245,197,24,.1);padding:48px 6% 28px;}
.footer-top{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:40px;margin-bottom:40px;}
.footer-brand{font-family:'Cinzel',serif;font-size:20px;font-weight:700;
  color:var(--gold);letter-spacing:.1em;margin-bottom:10px;}
.footer-tagline{font-family:'Cormorant Garamond',serif;font-style:italic;
  font-size:14px;color:rgba(240,220,160,.45);margin-bottom:18px;}
.footer-desc{font-size:13px;color:rgba(240,220,160,.38);line-height:1.7;}
.footer-col h4{font-family:'Cinzel',serif;font-size:12px;letter-spacing:.14em;
  text-transform:uppercase;color:rgba(245,197,24,.6);margin-bottom:16px;}
.footer-col a{display:block;font-size:13px;color:rgba(240,220,160,.45);
  text-decoration:none;margin-bottom:10px;cursor:pointer;transition:color .2s;}
.footer-col a:hover{color:var(--gold);}
.footer-bottom{display:flex;justify-content:space-between;align-items:center;
  flex-wrap:wrap;gap:12px;padding-top:24px;
  border-top:1px solid rgba(245,197,24,.07);}
.footer-copy{font-size:12px;color:rgba(240,220,160,.28);letter-spacing:.06em;}
.footer-social{display:flex;gap:12px;}
.soc-btn{width:36px;height:36px;border-radius:50%;background:rgba(245,197,24,.08);
  border:1px solid rgba(245,197,24,.15);display:flex;align-items:center;
  justify-content:center;font-size:15px;cursor:pointer;
  transition:background .2s,border-color .2s;}
.soc-btn:hover{background:rgba(245,197,24,.15);border-color:rgba(245,197,24,.35);}

/* ══════════════════════════════════════════
   SCROLL REVEAL
══════════════════════════════════════════ */
.reveal{opacity:0;transform:translateY(24px);transition:opacity .6s ease,transform .6s ease;}
.reveal.visible{opacity:1;transform:translateY(0);}
.reveal:nth-child(2){transition-delay:.08s}
.reveal:nth-child(3){transition-delay:.14s}
.reveal:nth-child(4){transition-delay:.2s}
.reveal:nth-child(5){transition-delay:.26s}
.reveal:nth-child(6){transition-delay:.32s}
.reveal:nth-child(7){transition-delay:.38s}
.reveal:nth-child(8){transition-delay:.44s}
.reveal:nth-child(9){transition-delay:.5s}
.reveal:nth-child(10){transition-delay:.56s}
.reveal:nth-child(n+11){transition-delay:.6s}

/* ══════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════ */
@media(max-width:960px){
  .footer-top{grid-template-columns:1fr 1fr;}
  .contact-grid{grid-template-columns:1fr;}
  .hero-orbit{width:560px;height:560px;}
  .hero-orbit2{width:360px;height:360px;}
  .hero{padding:calc(var(--nav-h) + 28px) 20px 56px;}
}
@media(max-width:768px){
  :root{--nav-h:62px;}
  .nav{padding:0 16px;}
  .nav-logo{font-size:16px;max-width:calc(100vw - 80px);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
  .nav-logo small{font-size:8px;letter-spacing:.16em;}
  .nav-links,.nav-cta{display:none;}
  .hamburger{display:flex;}
  .mobile-menu{display:flex;padding:18px 16px 22px;width:100%;max-width:100vw;overflow-x:hidden;}

  .hero{min-height:auto;align-items:stretch;text-align:left;padding:calc(var(--nav-h) + 22px) 16px 42px;}
  .hero > *{max-width:100%;}
  .hero-badge{display:flex;max-width:100%;white-space:normal;word-break:break-word;line-height:1.5;padding:7px 12px;margin-bottom:18px;}
  .hero h1{font-size:clamp(34px,12vw,56px);line-height:1.02;letter-spacing:.02em;overflow-wrap:anywhere;word-break:break-word;margin-bottom:12px;}
  .hero-tagline{font-size:clamp(16px,5.2vw,22px);margin-bottom:14px;}
  .hero-desc{max-width:100%;font-size:15px;line-height:1.75;margin-bottom:24px;}
  .hero-btns{width:100%;gap:10px;justify-content:flex-start;margin-bottom:28px;}
  .btn-gold,.btn-ghost{width:100%;max-width:100%;text-align:center;padding:13px 14px;font-size:13px;}
  .hero-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;width:100%;padding-top:18px;}
  .hero-stats > div{min-width:0;}
  .hstat-v{font-size:26px;}
  .hstat-l{font-size:9px;}
  .hero-orbit{width:460px;height:460px;opacity:.55;}
  .hero-orbit2{width:300px;height:300px;opacity:.75;}

  .section{padding:54px 16px;}
  .sec-head{margin-bottom:34px;}
  .sec-title{line-height:1.2;}
  .sec-sub{max-width:100%;font-size:14px;}
  .svc-grid{grid-template-columns:1fr;gap:12px;}
  .prod-grid,.why-grid,.testi-grid{grid-template-columns:1fr;gap:12px;}

  .cta-section{padding:58px 16px;}
  .cta-form{max-width:100%;}
  .cta-input{min-width:0;width:100%;}

  .form-row{grid-template-columns:1fr;}
  .contact-form{padding:20px 14px;}
  .footer-top{grid-template-columns:1fr;gap:20px;}
}
@media(max-width:480px){
  .hero{padding:calc(var(--nav-h) + 18px) 12px 34px;}
  .section,.cta-section,footer{padding-left:12px;padding-right:12px;}
  .hero-stats{grid-template-columns:1fr 1fr;gap:10px;}
  .mobile-menu a{font-size:14px;padding:12px 10px;}
  .mobile-menu{padding-left:12px;padding-right:12px;}
  .modal{padding:24px 14px;border-radius:14px;}
  .toast{left:12px;right:12px;transform:translateY(20px);white-space:normal;text-align:center;}
  .toast.show{transform:translateY(0);}
}
</style>
</head>
<body>

<!-- ════ COSMOS ════ -->
<div class="cosmos">
  <canvas id="starCanvas"></canvas>
</div>

<!-- ════ NAVBAR ════ -->
<nav class="nav" id="navbar">
  <a class="nav-logo" onclick="scrollToSection('hero')">
    {{ $brandName }}
    <small>{{ request()->getHost() }}</small>
  </a>
  <div class="nav-links">
    @foreach($serviceGroups as $gi => $grp)
      <a onclick="scrollToSection('svc-section-{{ $gi }}')">{{ Str::limit(explode('·', $grp['title'])[0], 12) }}</a>
    @endforeach
    <a onclick="scrollToSection('products')">Products</a>
    <a onclick="scrollToSection('why')">Why Us</a>
    <a onclick="scrollToSection('contact')">Contact</a>
  </div>
  <button class="nav-cta" onclick="openModal('{{ addslashes($primaryCta) }}','📞','Get expert guidance on any life question','General Consultation')">
    {{ $primaryCta }}
  </button>
  <button class="hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- ════ MOBILE MENU ════ -->
<div class="mobile-menu" id="mobileMenu">
  @foreach($serviceGroups as $gi => $grp)
    <a onclick="scrollToSection('svc-section-{{ $gi }}');toggleMenu()">✨ {{ $grp['title'] }}</a>
  @endforeach
  <a onclick="scrollToSection('products');toggleMenu()">💎 Products</a>
  <a onclick="scrollToSection('why');toggleMenu()">⭐ Why Us</a>
  <a onclick="scrollToSection('testimonials');toggleMenu()">💬 Reviews</a>
  <a onclick="scrollToSection('contact');toggleMenu()">📞 Contact</a>
  <button class="m-cta" onclick="openModal('{{ addslashes($primaryCta) }}','🔮','Connect with our expert today','General Consultation');toggleMenu()">
    Book Consultation
  </button>
  <button class="wa-m" onclick="openWhatsApp()">
    <svg viewBox="0 0 24 24" width="18" height="18" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.136.557 4.135 1.534 5.875L0 24l6.335-1.64A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.773 9.773 0 01-5.017-1.384l-.36-.214-3.729.965.998-3.62-.235-.374A9.757 9.757 0 012.182 12C2.182 6.58 6.58 2.182 12 2.182S21.818 6.58 21.818 12 17.42 21.818 12 21.818z"/></svg>
    WhatsApp Us
  </button>
</div>

<!-- ════ HERO ════ -->
<section class="hero" id="hero">
  <div class="hero-orbit"></div>
  <div class="hero-orbit2"></div>

  <div class="hero-badge">
    <span class="badge-dot"></span>
    {{ $heroBadge }}
  </div>

  <h1>{!! nl2br(e($heroTitle)) !!}</h1>
  <div class="hero-tagline">{{ $heroTagline }}</div>
  <p class="hero-desc">{{ $heroDescription }}</p>

  <div class="hero-btns">
    <button class="btn-gold" onclick="openModal('{{ addslashes($primaryCta) }}','🔮','Connect with our expert astrologer now','General Consultation')">
      {{ $primaryCta }}
    </button>
    <button class="btn-ghost" onclick="openModal('{{ addslashes($secondaryCta) }}','📅','Get your complete personalised forecast','{{ addslashes($secondaryCta) }}')">
      {{ $secondaryCta }}
    </button>
  </div>

  <div class="hero-stats">
    @foreach($stats as $st)
    <div>
      <div class="hstat-v">{{ $st['value'] ?? '' }}</div>
      <div class="hstat-l">{{ $st['label'] ?? '' }}</div>
    </div>
    @endforeach
  </div>
</section>

<div class="divider">✦ ✦ ✦</div>

<!-- ════ DYNAMIC SERVICE SECTIONS ════ -->
@foreach($serviceGroups as $gi => $grp)
<section class="section{{ $gi % 2 !== 0 ? ' alt' : '' }}" id="svc-section-{{ $gi }}">
  <div class="sec-head">
    <div class="sec-eyebrow">{{ $grp['eyebrow'] }}</div>
    <div class="sec-title">{{ $grp['title'] }}</div>
    @if(!empty($grp['subtitle']))<div class="sec-sub">{{ $grp['subtitle'] }}</div>@endif
  </div>
  <div class="svc-grid">
    @foreach($grp['items'] as $item)
    <div class="svc-card reveal" onclick="openModal('{{ addslashes($item['name']) }}','{{ addslashes($item['icon']) }}','{{ addslashes($item['description']) }}','{{ addslashes($item['name']) }}')">
      <span class="ico">{{ $item['icon'] }}</span>
      <h3>{{ $item['name'] }}</h3>
      <p>{{ $item['description'] }}</p>
      @if(!empty($item['price']))<span class="svc-price">{{ $item['price'] }}</span>@endif
      <span class="card-cta">Book Now →</span>
    </div>
    @endforeach
  </div>
</section>
@endforeach

<!-- ════ PRODUCTS ════ -->
<section class="section{{ count($serviceGroups) % 2 === 0 ? '' : ' alt' }}" id="products">
  <div class="sec-head">
    <div class="sec-eyebrow">Sacred Products</div>
    <div class="sec-title">Authentic Astrology Products</div>
    <div class="sec-sub">Certified gemstones, sacred Vastu items and professional tools — delivered to your door</div>
  </div>
  <div class="prod-grid">
    @foreach($products as $prod)
    <div class="prod-card reveal" onclick="openModal('{{ addslashes($prod['name']) }}','{{ $prod['ico'] }}','{{ addslashes($prod['description']) }}','{{ addslashes($prod['name']) }}')">
      <span class="ico">{{ $prod['ico'] }}</span>
      <h3>{{ $prod['name'] }}</h3>
      <p>{{ $prod['description'] }}</p>
      <span class="prod-badge">{{ $prod['badge'] }}</span>
      <button class="btn-buy">Order Now</button>
    </div>
    @endforeach
  </div>
</section>

<!-- ════ WHY US ════ -->
<section class="section alt" id="why">
  <div class="sec-head">
    <div class="sec-eyebrow">Why Choose Us</div>
    <div class="sec-title">{{ $whyTitle }}</div>
    <div class="sec-sub">{{ $whySubtitle }}</div>
  </div>
  <div class="why-grid">
    @foreach($whyItems as $w)
    <div class="why-card reveal">
      <span class="why-ico">{{ $w['ico'] ?? ($w['icon'] ?? '✨') }}</span>
      <h4>{{ $w['title'] ?? ($w['name'] ?? '') }}</h4>
      <p>{{ $w['desc'] ?? ($w['description'] ?? '') }}</p>
    </div>
    @endforeach
  </div>
</section>

<!-- ════ TESTIMONIALS ════ -->
<section class="section" id="testimonials">
  <div class="sec-head">
    <div class="sec-eyebrow">Client Words</div>
    <div class="sec-title">Real Stories, Real Results</div>
    <div class="sec-sub">Thousands of lives guided by the stars</div>
  </div>
  <div class="testi-grid">
    @foreach($testimonials as $t)
    <div class="testi-card reveal">
      <div class="testi-stars">★★★★★</div>
      <div class="testi-text">{{ $t['text'] ?? ($t['review'] ?? '') }}</div>
      <div class="testi-by">
        <div class="testi-av">{{ $t['initials'] ?? strtoupper(substr($t['name'] ?? 'U', 0, 2)) }}</div>
        <div>
          <div class="testi-name">{{ $t['name'] ?? '' }}</div>
          <div class="testi-loc">{{ $t['loc'] ?? ($t['location'] ?? '') }}</div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</section>

<!-- ════ CTA BAND ════ -->
<div class="cta-section">
  <div class="cta-glow"></div>
  <h2>{{ $ctaTitle }}</h2>
  <p>{{ $ctaDesc }}</p>
  <div class="cta-form">
    <input class="cta-input" type="tel" id="ctaPhone" placeholder="Enter your phone number"/>
    <button class="btn-gold" onclick="submitCTA()">{{ $ctaButton }}</button>
  </div>
  <div class="cta-note">✦ We call back within 30 minutes · No spam · 100% confidential ✦</div>
</div>

<!-- ════ CONTACT ════ -->
<section class="section alt" id="contact">
  <div class="sec-head">
    <div class="sec-eyebrow">Get in Touch</div>
    <div class="sec-title">Book a Consultation</div>
    <div class="sec-sub">Fill the form or reach us directly — we respond within 30 minutes</div>
  </div>
  <div class="contact-grid">
    <div class="contact-info">
      <h3>Reach Us Anytime</h3>
      <p>{{ $providerBio }}</p>
      @if($providerContact !== '')
      <div class="contact-item">
        <div class="c-ico">📞</div>
        <div><div class="c-label">Phone / WhatsApp</div><div class="c-val">{{ $providerContact }}</div></div>
      </div>
      @endif
      @if($providerEmail !== '')
      <div class="contact-item">
        <div class="c-ico">📧</div>
        <div><div class="c-label">Email</div><div class="c-val">{{ $providerEmail }}</div></div>
      </div>
      @endif
      <div class="contact-item">
        <div class="c-ico">🕐</div>
        <div><div class="c-label">Working Hours</div><div class="c-val">24/7 · 365 Days Available</div></div>
      </div>
      @if($shop->address)
      <div class="contact-item">
        <div class="c-ico">📍</div>
        <div><div class="c-label">Address</div><div class="c-val">{{ $shop->address }}</div></div>
      </div>
      @endif
      <div style="margin-top:24px;">
        <button class="btn-gold" style="margin-bottom:12px;width:100%;display:block;" onclick="openWhatsApp()">
          💬 Chat on WhatsApp
        </button>
        <button class="btn-ghost" style="width:100%;display:block;" onclick="openModal('Schedule a Call','📞','We will call you at your preferred time','General Consultation')">
          📞 Schedule a Call
        </button>
      </div>
    </div>
    <div class="contact-form">
      <h3>Quick Booking Form</h3>
      <div class="form-row">
        <div class="form-field">
          <label>Your Name</label>
          <input type="text" id="cf-name" placeholder="Full name"/>
        </div>
        <div class="form-field">
          <label>Phone Number</label>
          <input type="tel" id="cf-phone" placeholder="+91 XXXXX XXXXX"/>
        </div>
      </div>
      <div class="form-row">
        <div class="form-field">
          <label>Email</label>
          <input type="email" id="cf-email" placeholder="you@email.com"/>
        </div>
        <div class="form-field">
          <label>Date of Birth</label>
          <input type="date" id="cf-dob"/>
        </div>
      </div>
      <div class="form-field">
        <label>Service Required</label>
        <select id="cf-service">
          <option value="">Select a service…</option>
          @foreach($serviceGroups as $grp)
          <optgroup label="{{ $grp['title'] }}">
            @foreach($grp['items'] as $item)
            <option>{{ $item['name'] }}</option>
            @endforeach
          </optgroup>
          @endforeach
        </select>
      </div>
      <div class="form-field">
        <label>Your Question / Message</label>
        <textarea id="cf-msg" placeholder="Describe your question or what guidance you need…"></textarea>
      </div>
      <button class="form-submit" onclick="submitContactForm()">✦ Submit Booking Request</button>
    </div>
  </div>
</section>

<!-- ════ FOOTER ════ -->
<footer>
  <div class="footer-top">
    <div>
      <div class="footer-brand">✨ {{ $brandName }}</div>
      <div class="footer-tagline">{{ $heroTagline }}</div>
      <div class="footer-desc">{{ $heroDescription }}</div>
    </div>
    @if(count($serviceGroups) > 0)
    <div class="footer-col">
      <h4>{{ $serviceGroups[0]['title'] }}</h4>
      @foreach(array_slice($serviceGroups[0]['items'], 0, 5) as $item)
      <a onclick="openModal('{{ addslashes($item['name']) }}','{{ addslashes($item['icon']) }}','{{ addslashes($item['description']) }}','{{ addslashes($item['name']) }}')">{{ $item['name'] }}</a>
      @endforeach
    </div>
    @endif
    <div class="footer-col">
      <h4>Quick Links</h4>
      <a onclick="scrollToSection('hero')">Home</a>
      <a onclick="scrollToSection('products')">Gemstones</a>
      <a onclick="scrollToSection('testimonials')">Reviews</a>
      <a onclick="scrollToSection('contact')">Contact Us</a>
      <a onclick="openModal('Free Consultation','🌟','Get your free consultation','Free Consultation')">Free Consultation</a>
    </div>
    <div class="footer-col">
      <h4>Provider</h4>
      <a>{{ $providerName }}</a>
      @if($providerContact !== '')<a>{{ $providerContact }}</a>@endif
      @if($providerEmail !== '')<a>{{ $providerEmail }}</a>@endif
      @if($shop->address)<a>{{ Str::limit($shop->address, 40) }}</a>@endif
    </div>
  </div>
  <div class="footer-bottom">
    <div class="footer-copy">© {{ now()->year }} {{ $brandName }} · All Rights Reserved</div>
    <div class="footer-social">
      <div class="soc-btn" title="Facebook">📘</div>
      <div class="soc-btn" title="Instagram">📷</div>
      <div class="soc-btn" title="YouTube">▶️</div>
      <div class="soc-btn" title="WhatsApp" onclick="openWhatsApp()">💬</div>
    </div>
  </div>
</footer>

<!-- ════ BOOKING MODAL ════ -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
  <div class="modal" id="modal">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <span class="modal-badge" id="modalBadge">🔮</span>
    <h2 id="modalTitle">Book a Consultation</h2>
    <p class="modal-sub" id="modalSub">Connect with an expert astrologer today</p>
    <div class="form-field">
      <label>Your Name</label>
      <input type="text" id="m-name" placeholder="Full name"/>
    </div>
    <div class="form-field">
      <label>Phone Number</label>
      <input type="tel" id="m-phone" placeholder="+91 XXXXX XXXXX"/>
    </div>
    <div class="form-field">
      <label>Date of Birth</label>
      <input type="date" id="m-dob"/>
    </div>
    <div class="form-field">
      <label>Service</label>
      <input type="text" id="m-service" placeholder="Service name"/>
    </div>
    <div class="form-field">
      <label>Your Question</label>
      <textarea id="m-msg" placeholder="What would you like guidance on?" style="min-height:80px;"></textarea>
    </div>
    <button class="modal-submit" onclick="submitModal()">✦ Book Consultation Now</button>
    <button class="modal-wa" onclick="openWhatsApp()">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.136.557 4.135 1.534 5.875L0 24l6.335-1.64A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.773 9.773 0 01-5.017-1.384l-.36-.214-3.729.965.998-3.62-.235-.374A9.757 9.757 0 012.182 12C2.182 6.58 6.58 2.182 12 2.182S21.818 6.58 21.818 12 17.42 21.818 12 21.818z"/></svg>
      Or Chat on WhatsApp
    </button>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<!-- WhatsApp Float -->
<a class="wa-float" onclick="openWhatsApp()" title="WhatsApp">
  <div class="wa-tooltip">Chat on WhatsApp</div>
  <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.136.557 4.135 1.534 5.875L0 24l6.335-1.64A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.773 9.773 0 01-5.017-1.384l-.36-.214-3.729.965.998-3.62-.235-.374A9.757 9.757 0 012.182 12C2.182 6.58 6.58 2.182 12 2.182S21.818 6.58 21.818 12 17.42 21.818 12 21.818z"/></svg>
</a>

<!-- Back to Top -->
<button class="back-top" id="backTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" title="Back to top">↑</button>

<script>
/* ════════════════════════════
   STAR CANVAS
════════════════════════════ */
(function(){
  const c = document.getElementById('starCanvas');
  const ctx = c.getContext('2d');
  let stars = [];
  function resize(){
    c.width = window.innerWidth;
    c.height = window.innerHeight;
    init();
  }
  function init(){
    stars = [];
    const n = Math.floor((c.width * c.height) / 4000);
    for(let i = 0; i < n; i++){
      stars.push({
        x: Math.random() * c.width,
        y: Math.random() * c.height,
        r: Math.random() * 1.2 + .2,
        o: Math.random() * .8 + .1,
        s: Math.random() * .015 + .003,
        d: Math.random() > .5 ? 1 : -1,
        gold: Math.random() > .85
      });
    }
  }
  function draw(){
    ctx.clearRect(0, 0, c.width, c.height);
    stars.forEach(s => {
      s.o += s.s * s.d;
      if(s.o > 1 || s.o < .05) s.d *= -1;
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fillStyle = s.gold
        ? `rgba(245,197,24,${s.o})`
        : `rgba(255,255,255,${s.o})`;
      ctx.fill();
    });
    requestAnimationFrame(draw);
  }
  window.addEventListener('resize', resize);
  resize();
  draw();
})();

/* ════════════════════════════
   NAVBAR SCROLL
════════════════════════════ */
const navbar = document.getElementById('navbar');
const backTop = document.getElementById('backTop');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 50);
  backTop.classList.toggle('show', window.scrollY > 400);
  // scroll reveal
  document.querySelectorAll('.reveal').forEach(el => {
    if(el.getBoundingClientRect().top < window.innerHeight - 60)
      el.classList.add('visible');
  });
  // active nav link
  let cur = '';
  document.querySelectorAll('[id]').forEach(sec => {
    if(window.scrollY >= sec.offsetTop - 130) cur = sec.id;
  });
  document.querySelectorAll('.nav-links a').forEach(l => {
    const oc = l.getAttribute('onclick') || '';
    l.classList.toggle('active', cur && oc.includes(cur));
  });
}, { passive: true });

/* ════════════════════════════
   SMOOTH SCROLL
════════════════════════════ */
function scrollToSection(id){
  const el = document.getElementById(id);
  if(el) window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 70, behavior: 'smooth' });
}

/* ════════════════════════════
   MOBILE MENU
════════════════════════════ */
function toggleMenu(){
  const menu = document.getElementById('mobileMenu');
  const btn  = document.getElementById('hamburger');
  const open = menu.classList.toggle('open');
  btn.classList.toggle('open', open);
  document.body.style.overflow = open ? 'hidden' : '';
  document.documentElement.style.overflow = open ? 'hidden' : '';
}

window.addEventListener('resize', () => {
  if (window.innerWidth > 768) {
    const menu = document.getElementById('mobileMenu');
    const btn  = document.getElementById('hamburger');
    if (menu) menu.classList.remove('open');
    if (btn) btn.classList.remove('open');
    document.body.style.overflow = '';
    document.documentElement.style.overflow = '';
  }
});

/* ════════════════════════════
   MODAL
════════════════════════════ */
function openModal(title, badge, sub, service){
  document.getElementById('modalTitle').textContent = title;
  document.getElementById('modalBadge').textContent = badge;
  document.getElementById('modalSub').textContent   = sub;
  document.getElementById('m-service').value        = service || title;
  document.getElementById('modalOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeModal(){
  document.getElementById('modalOverlay').classList.remove('open');
  document.body.style.overflow = '';
}
function closeModalOutside(e){
  if(e.target === document.getElementById('modalOverlay')) closeModal();
}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });

/* ════════════════════════════
   TOAST
════════════════════════════ */
function showToast(msg){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3500);
}

/* ════════════════════════════
   FORM SUBMISSIONS
════════════════════════════ */
const clientRequestEndpoint = @json(route('client_requests.store'));
const clientRequestShopId = {{ (int) $shop->id }};
const clientRequestTemplate = @json((string) ($shop->template ?: 'dynamic_service'));
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
      shop_id: clientRequestShopId,
      template_key: clientRequestTemplate,
    }, payload || {})),
  });
  if(!response.ok){
    throw new Error('Failed to submit request');
  }
  return response.json();
}

async function submitModal(){
  const name  = document.getElementById('m-name').value.trim();
  const phone = document.getElementById('m-phone').value.trim();
  const dob   = document.getElementById('m-dob').value;
  const svc   = document.getElementById('m-service').value.trim();
  const msg   = document.getElementById('m-msg').value.trim();
  if(!name)  { showToast('⚠️ Please enter your name'); return; }
  if(!phone) { showToast('⚠️ Please enter your phone number'); return; }
  try{
    await postClientRequest({
      source: 'modal_booking',
      customer_name: name,
      phone: phone,
      dob: dob || null,
      service_name: svc || 'General Consultation',
      message: msg,
      status: 'new',
    });
    closeModal();
    showToast('✦ Booking confirmed! We will call you within 30 minutes.');
  }catch(_e){
    showToast('⚠️ Unable to submit request right now. Please try again.');
    return;
  }
  ['m-name','m-phone','m-dob','m-msg'].forEach(id => document.getElementById(id).value = '');
}
async function submitCTA(){
  const phone = document.getElementById('ctaPhone').value.trim();
  if(!phone) { showToast('⚠️ Please enter your phone number'); return; }
  try{
    await postClientRequest({
      source: 'hero_callback',
      phone: phone,
      status: 'callback',
      service_name: 'Quick Callback',
      message: 'Callback request from hero CTA',
    });
    showToast('✦ Thank you! Our astrologer will call you within 30 minutes.');
  }catch(_e){
    showToast('⚠️ Unable to submit callback right now. Please try again.');
    return;
  }
  document.getElementById('ctaPhone').value = '';
}
async function submitContactForm(){
  const name  = document.getElementById('cf-name').value.trim();
  const phone = document.getElementById('cf-phone').value.trim();
  const email = document.getElementById('cf-email').value.trim();
  const dob   = document.getElementById('cf-dob').value;
  const svc   = document.getElementById('cf-service').value;
  const msg   = document.getElementById('cf-msg').value.trim();
  if(!name)  { showToast('⚠️ Please enter your name'); return; }
  if(!phone) { showToast('⚠️ Please enter your phone number'); return; }
  if(!svc)   { showToast('⚠️ Please select a service'); return; }
  try{
    await postClientRequest({
      source: 'contact_form',
      customer_name: name,
      phone: phone,
      email: email || null,
      dob: dob || null,
      service_name: svc,
      message: msg,
      status: 'new',
    });
    showToast('✦ Booking request submitted! We will contact you shortly.');
  }catch(_e){
    showToast('⚠️ Unable to submit booking right now. Please try again.');
    return;
  }
  ['cf-name','cf-phone','cf-email','cf-dob','cf-msg'].forEach(id => {
    const el = document.getElementById(id);
    if(el) el.value = '';
  });
  document.getElementById('cf-service').value = '';
}

/* ════════════════════════════
   WHATSAPP
════════════════════════════ */
function openWhatsApp(){
  const base = '{{ $providerContact !== "" ? preg_replace('/\D+/', "", $providerContact) : "919876543210" }}';
  window.open(`https://wa.me/${base}?text=Hello!%20I%20would%20like%20to%20book%20a%20consultation.`, '_blank');
}

/* ════════════════════════════
   INIT REVEAL ON LOAD
════════════════════════════ */
window.addEventListener('load', () => {
  setTimeout(() => {
    document.querySelectorAll('.reveal').forEach(el => {
      if(el.getBoundingClientRect().top < window.innerHeight - 60)
        el.classList.add('visible');
    });
  }, 100);
});
</script>
</body>
</html>
