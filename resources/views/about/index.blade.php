@extends('layouts.app')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,700&family=Poppins:wght@400;500;600;700;800&display=swap');

  .about-page {
    --brand-teal: #2a9d8f;
    --brand-pink: #e84393;
    --brand-pink-bg: #fce4f0;
    --green: #2d6a4f;
    --green-light: #52b788;
    --gold: #f4a261;
    --cream: #fef9ef;
    --dark: #1b1b1b;
    --gray: #6b7280;
    font-family: 'Poppins', sans-serif;
    background: var(--cream);
    color: var(--dark);
    overflow-x: hidden;
  }

  .about-page * { margin: 0; padding: 0; box-sizing: border-box; }

  .about-page .brand-logo { display: inline-flex; align-items: center; gap: 10px; }
  .about-page .hero-brand-logo {
    width: 48px; height: 48px; display: block; flex-shrink: 0;
  }
  .about-page .logo-text-wrap { display: flex; flex-direction: column; line-height: 1.1; }
  .about-page .logo-name {
    font-family: 'Nunito', sans-serif; font-weight: 800; font-style: italic;
    font-size: 22px; color: var(--brand-teal); letter-spacing: -0.3px;
  }
  .about-page .logo-tagline { font-size: 11px; color: #888; font-weight: 500; letter-spacing: 0.2px; }

  .about-page .hero {
    background: linear-gradient(135deg, #0d4f47 0%, #1a7a6e 55%, #2a9d8f 100%);
    color: white; padding: 64px 40px 80px; position: relative; overflow: hidden; text-align: center;
  }
  .about-page .hero::before {
    content: ''; position: absolute; top: -100px; right: -80px; width: 340px; height: 340px;
    border-radius: 50%; background: rgba(42,157,143,0.2);
  }
  .about-page .hero::after {
    content: ''; position: absolute; bottom: -70px; left: -60px; width: 260px; height: 260px;
    border-radius: 50%; background: rgba(232,67,147,0.12);
  }
  .about-page .hero-logo-wrap { display: flex; justify-content: center; margin-bottom: 32px; animation: fadeDown 0.7s ease both; }
  .about-page .hero-logo-card {
    background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);
    border-radius: 20px; padding: 14px 24px;
  }
  .about-page .hero-logo-card .logo-name { color: white; }
  .about-page .hero-logo-card .logo-tagline { color: rgba(255,255,255,0.65); }
  .about-page .hero h1 {
    font-family: 'Nunito', sans-serif; font-size: clamp(30px, 5vw, 54px); font-weight: 800;
    line-height: 1.1; margin-bottom: 18px; animation: fadeDown 0.8s 0.1s ease both;
  }
  .about-page .hero h1 em { color: #f9c74f; font-style: normal; }
  .about-page .hero p {
    font-size: 16px; color: rgba(255,255,255,0.82); max-width: 540px; margin: 0 auto 36px;
    line-height: 1.75; animation: fadeDown 0.8s 0.2s ease both;
  }
  .about-page .hero-cta {
    display: inline-block; background: var(--brand-pink); color: white; padding: 14px 36px;
    border-radius: 50px; font-weight: 700; font-size: 15px; text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s; animation: fadeDown 0.8s 0.3s ease both; letter-spacing: 0.3px;
  }
  .about-page .hero-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(232,67,147,0.45); }

  .about-page .stats { display: flex; flex-wrap: wrap; background: #f3f4f6; border-bottom: 1px solid #d9dde3; }
  .about-page .stat { padding: 26px 36px; text-align: center; border-right: 1px solid #d9dde3; flex: 1; min-width: 130px; }
  .about-page .stat:last-child { border-right: none; }
  .about-page .stat-num { font-family: 'Nunito', sans-serif; font-size: 30px; font-weight: 800; color: var(--brand-teal); }
  .about-page .stat-label { font-size: 12px; color: var(--gray); margin-top: 3px; font-weight: 500; }

  .about-page section { padding: 68px 40px; }
  .about-page .section-tag {
    display: inline-block; background: rgba(42,157,143,0.1); color: var(--brand-teal); font-size: 11px;
    font-weight: 700; letter-spacing: 1.8px; text-transform: uppercase; padding: 6px 14px; border-radius: 50px;
    margin-bottom: 14px;
  }
  .about-page h2 {
    font-family: 'Nunito', sans-serif; font-size: clamp(24px, 3.5vw, 38px); font-weight: 800;
    line-height: 1.15; margin-bottom: 14px; color: var(--dark);
  }
  .about-page h2 span { color: var(--brand-teal); }

  .about-page .offers-section { background: var(--cream); }
  .about-page .offers-inner { max-width: 1100px; margin: 0 auto; }
  .about-page .lead { font-size: 16px; color: var(--gray); max-width: 560px; line-height: 1.75; margin-bottom: 48px; }
  .about-page .offer-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 22px; }
  .about-page .offer-card {
    background: white; border-radius: 18px; padding: 30px 26px; border: 1.5px solid #e8f5f3;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s; position: relative; overflow: hidden;
  }
  .about-page .offer-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
    background: linear-gradient(90deg, var(--brand-pink), var(--brand-teal));
    transform: scaleX(0); transform-origin: left; transition: transform 0.3s;
  }
  .about-page .offer-card:hover { transform: translateY(-4px); box-shadow: 0 14px 36px rgba(0,0,0,0.07); border-color: #b2dfdb; }
  .about-page .offer-card:hover::before { transform: scaleX(1); }
  .about-page .offer-icon { font-size: 30px; margin-bottom: 14px; }
  .about-page .offer-card h3 { font-size: 17px; font-weight: 700; margin-bottom: 9px; color: var(--dark); }
  .about-page .offer-card p { font-size: 13.5px; color: var(--gray); line-height: 1.65; }

  .about-page .why-section { background: linear-gradient(135deg, #0d4f47, #1a7a6e); color: white; }
  .about-page .why-section h2 { color: white; }
  .about-page .why-section .section-tag { background: rgba(255,255,255,0.15); color: white; }
  .about-page .why-inner { max-width: 1100px; margin: 0 auto; }
  .about-page .why-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 44px; }
  .about-page .why-item {
    display: flex; gap: 14px; align-items: flex-start; background: rgba(255,255,255,0.07);
    border-radius: 14px; padding: 22px 20px; border: 1px solid rgba(255,255,255,0.12); transition: background 0.2s;
  }
  .about-page .why-item:hover { background: rgba(255,255,255,0.13); }
  .about-page .why-check {
    width: 34px; height: 34px; flex-shrink: 0; background: var(--brand-pink); border-radius: 50%;
    display: flex; align-items: center; justify-content: center; font-size: 15px; color: white; font-weight: 800;
  }
  .about-page .why-item h4 { font-size: 15px; font-weight: 700; margin-bottom: 5px; }
  .about-page .why-item p { font-size: 13px; color: rgba(255,255,255,0.72); line-height: 1.6; }

  .about-page .contact-section { background: var(--cream); text-align: center; }
  .about-page .contact-inner { max-width: 660px; margin: 0 auto; }
  .about-page .contact-inner > p { font-size: 16px; color: var(--gray); margin-bottom: 36px; line-height: 1.75; }
  .about-page .contact-cards { display: flex; flex-wrap: wrap; gap: 18px; justify-content: center; margin-bottom: 36px; }
  .about-page .contact-card {
    background: white; border: 1.5px solid #e5e7eb; border-radius: 16px; padding: 22px 28px;
    display: flex; flex-direction: column; align-items: center; gap: 7px; min-width: 190px; transition: box-shadow 0.2s;
  }
  .about-page .contact-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.07); }
  .about-page .contact-card .ci { font-size: 26px; }
  .about-page .contact-card h4 {
    font-size: 11px; font-weight: 700; color: var(--gray); letter-spacing: 0.8px; text-transform: uppercase;
  }
  .about-page .contact-card a, .about-page .contact-card span {
    font-size: 14px; font-weight: 700; color: var(--brand-teal); text-decoration: none;
  }
  .about-page .contact-card a:hover { text-decoration: underline; }
  .about-page .big-cta {
    display: inline-flex; align-items: center; gap: 8px; background: var(--brand-teal); color: white;
    padding: 15px 40px; border-radius: 50px; font-size: 16px; font-weight: 700; text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .about-page .big-cta:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(42,157,143,0.4); }

  @keyframes fadeDown {
    from { opacity: 0; transform: translateY(-16px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 1024px) {
    .about-page .offer-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
  }

  @media (max-width: 768px) {
    .about-page section { padding: 48px 18px; }
    .about-page h1 { font-size: 32px; }
    .about-page h2 { font-size: 26px; }
    .about-page .lead { font-size: 15px; margin-bottom: 36px; }
    .about-page .offer-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; }
    .about-page .offer-card { padding: 20px 18px; }
    .about-page .offer-icon { font-size: 26px; }
    .about-page .offer-card h3 { font-size: 15px; }
    .about-page .offer-card p { font-size: 12px; }
    .about-page .why-grid { grid-template-columns: 1fr; gap: 14px; margin-top: 32px; }
    .about-page .why-item { padding: 16px; gap: 10px; }
    .about-page .why-check { width: 30px; height: 30px; font-size: 13px; }
    .about-page .why-item h4 { font-size: 14px; }
    .about-page .why-item p { font-size: 12px; }
    .about-page .contact-cards { gap: 12px; }
    .about-page .contact-card { min-width: 160px; padding: 16px 20px; }
    .about-page .contact-card .ci { font-size: 22px; }
    .about-page .contact-card h4 { font-size: 10px; }
    .about-page .big-cta { padding: 12px 32px; font-size: 14px; }
  }

  @media (max-width: 640px) {
    .about-page section { padding: 36px 16px; }
    .about-page h1 { font-size: 26px; }
    .about-page h2 { font-size: 22px; }
    .about-page .lead { font-size: 14px; margin-bottom: 28px; }
    .about-page .offer-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .about-page .offer-card { padding: 16px 14px; }
    .about-page .offer-icon { font-size: 24px; margin-bottom: 10px; }
    .about-page .offer-card h3 { font-size: 13px; margin-bottom: 6px; }
    .about-page .offer-card p { font-size: 11px; }
    .about-page .why-grid { grid-template-columns: 1fr; gap: 12px; margin-top: 24px; }
    .about-page .why-item { padding: 14px; gap: 10px; }
    .about-page .why-check { width: 28px; height: 28px; font-size: 12px; }
    .about-page .why-item h4 { font-size: 13px; margin-bottom: 4px; }
    .about-page .why-item p { font-size: 11px; }
    .about-page .contact-section { background: var(--cream); }
    .about-page .contact-inner > p { font-size: 14px; margin-bottom: 24px; }
    .about-page .contact-cards { flex-direction: column; gap: 10px; }
    .about-page .contact-card { min-width: 100%; }
    .about-page .big-cta { padding: 10px 26px; font-size: 13px; }
  }

  @media (max-width: 480px) {
    .about-page section { padding: 28px 12px; }
    .about-page h1 { font-size: 22px; margin-bottom: 16px; }
    .about-page h2 { font-size: 18px; margin-bottom: 20px; }
    .about-page .offer-grid { grid-template-columns: 1fr; gaps: 10px; }
    .about-page .why-grid { gap: 10px; }
    .about-page .contact-card h4 { font-size: 9px; }
    .about-page .hero { padding: 36px 12px 48px; }
  }
</style>

<div class="about-page">
  <div class="hero">
    <div class="hero-logo-wrap">
      <div class="hero-logo-card">
        <div class="brand-logo">
          @include('shared.brand_logo', ['className' => 'hero-brand-logo', 'title' => 'AajchaOffer logo'])
          <div class="logo-text-wrap">
            <span class="logo-name">AajchaOffer</span>
            <span class="logo-tagline">Aajcha bhav, aajcha offer</span>
          </div>
        </div>
      </div>
    </div>

    <h1>{{ __('ui.about_hero_title_1') }}<br><em>{{ __('ui.about_hero_title_2') }}</em></h1>
    <p>{{ __('ui.about_hero_intro') }}</p>
    <a class="hero-cta" href="mailto:contact@aajchaoffer.com">{{ __('ui.get_in_touch') }} →</a>
  </div>

  <div class="stats">
    <div class="stat"><div class="stat-num">7+</div><div class="stat-label">{{ __('ui.about_core_sections') }}</div></div>
    <div class="stat"><div class="stat-num">∞</div><div class="stat-label">{{ __('ui.about_cities_supported') }}</div></div>
    <div class="stat"><div class="stat-num">4</div><div class="stat-label">{{ __('ui.about_user_roles') }}</div></div>
    <div class="stat"><div class="stat-num">₹0</div><div class="stat-label">{{ __('ui.about_to_browse_read') }}</div></div>
  </div>

  <section class="offers-section">
    <div class="offers-inner">
      <div class="section-tag">{{ __('ui.about_what_we_offer') }}</div>
      <h2>{{ __('ui.about_everything_your') }} <span>{{ __('ui.about_community_needs') }}</span></h2>
      <p class="lead">{{ __('ui.about_offer_lead') }}</p>
      <div class="offer-grid">
        <div class="offer-card">
          <div class="offer-icon">🌾</div>
          <h3>{{ __('ui.about_card_live_market_rates') }}</h3>
          <p>{{ __('ui.about_card_live_market_rates_desc') }}</p>
        </div>
        <div class="offer-card">
          <div class="offer-icon">📰</div>
          <h3>{{ __('ui.about_card_local_news_updates') }}</h3>
          <p>{{ __('ui.about_card_local_news_updates_desc') }}</p>
        </div>
        <div class="offer-card">
          <div class="offer-icon">💼</div>
          <h3>{{ __('ui.about_card_jobs_board') }}</h3>
          <p>{{ __('ui.about_card_jobs_board_desc') }}</p>
        </div>
        <div class="offer-card">
          <div class="offer-icon">🏡</div>
          <h3>{{ __('ui.about_card_rentals_buy_sell') }}</h3>
          <p>{{ __('ui.about_card_rentals_buy_sell_desc') }}</p>
        </div>
        <div class="offer-card">
          <div class="offer-icon">🌿</div>
          <h3>{{ __('ui.about_card_farming_hub') }}</h3>
          <p>{{ __('ui.about_card_farming_hub_desc') }}</p>
        </div>
        <div class="offer-card">
          <div class="offer-icon">🏪</div>
          <h3>{{ __('ui.about_card_digital_shop_pages') }}</h3>
          <p>{{ __('ui.about_card_digital_shop_pages_desc') }}</p>
        </div>
      </div>
    </div>
  </section>

  <section class="why-section">
    <div class="why-inner">
      <div class="section-tag">{{ __('ui.about_why_aajchaoffer') }}</div>
      <h2>{{ __('ui.about_built_for_bharat') }}</h2>
      <div class="why-grid">
        <div class="why-item">
          <div class="why-check">✓</div>
          <div><h4>{{ __('ui.about_why_truly_affordable') }}</h4><p>{{ __('ui.about_why_truly_affordable_desc') }}</p></div>
        </div>
        <div class="why-item">
          <div class="why-check">✓</div>
          <div><h4>{{ __('ui.about_why_no_login') }}</h4><p>{{ __('ui.about_why_no_login_desc') }}</p></div>
        </div>
        <div class="why-item">
          <div class="why-check">✓</div>
          <div><h4>{{ __('ui.about_why_city_aware') }}</h4><p>{{ __('ui.about_why_city_aware_desc') }}</p></div>
        </div>
        <div class="why-item">
          <div class="why-check">✓</div>
          <div><h4>{{ __('ui.about_why_digitize_business') }}</h4><p>{{ __('ui.about_why_digitize_business_desc') }}</p></div>
        </div>
        <div class="why-item">
          <div class="why-check">✓</div>
          <div><h4>{{ __('ui.about_why_govt_data') }}</h4><p>{{ __('ui.about_why_govt_data_desc') }}</p></div>
        </div>
        <div class="why-item">
          <div class="why-check">✓</div>
          <div><h4>{{ __('ui.about_why_farmer_first') }}</h4><p>{{ __('ui.about_why_farmer_first_desc') }}</p></div>
        </div>
      </div>
    </div>
  </section>

  <section class="contact-section" id="contact">
    <div class="contact-inner">
      <div class="section-tag">{{ __('ui.about_lets_connect') }}</div>
      <h2>{{ __('ui.about_ready_take_business') }} <span>{{ __('ui.about_digital_q') }}</span></h2>
      <p>{{ __('ui.about_contact_intro') }}</p>
      <div class="contact-cards">
        <div class="contact-card">
          <div class="ci">✉️</div>
          <h4>{{ __('ui.about_email_us') }}</h4>
          <a href="mailto:contact@aajchaoffer.com">contact@aajchaoffer.com</a>
        </div>
        <div class="contact-card">
          <div class="ci">🌐</div>
          <h4>{{ __('ui.about_platform') }}</h4>
          <span>AajchaOffer.com</span>
        </div>
        <div class="contact-card">
          <div class="ci">📍</div>
          <h4>{{ __('ui.about_serving') }}</h4>
          <span>{{ __('ui.about_serving_value') }}</span>
        </div>
      </div>
      <a class="big-cta" href="mailto:contact@aajchaoffer.com">{{ __('ui.about_contact_cta') }} →</a>
    </div>
  </section>
</div>
@endsection
