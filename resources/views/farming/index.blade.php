@extends('layouts.app')

@section('content')
<style>
  :root {
    --farm-soil:#3d2b1f;
    --farm-leaf:#1a5e20;
    --farm-leaf2:#2e7d32;
    --farm-wheat:#c8960c;
    --farm-earth:#f5f0e8;
    --farm-earth2:#ede8da;
    --farm-text:#1c1208;
    --farm-muted:#6b5f50;
    --farm-border:rgba(61,43,31,.14);
    --farm-card:#ffffff;
    --farm-shadow:0 4px 18px rgba(61,43,31,.10);
  }

  .farm-wrap{max-width:none;margin:0;padding:16px 18px 30px;color:var(--farm-text)}
  .farm-hero{background:linear-gradient(120deg,#1a5e20 0%, #2e7d32 60%, #43a047 100%);border-radius:14px;padding:22px 24px;color:#fff;box-shadow:var(--farm-shadow);margin-bottom:16px}
  .farm-hero h1{margin:0 0 8px;font-size:2rem;line-height:1.15;font-weight:800}
  .farm-hero p{margin:0;color:rgba(255,255,255,.9);max-width:760px;font-size:.95rem;line-height:1.6}
  .farm-hero-grid{display:grid;grid-template-columns:1.5fr 1fr;gap:14px;align-items:end;margin-top:14px}
  .farm-search{display:flex;background:rgba(255,255,255,.13);border:1px solid rgba(255,255,255,.28);border-radius:10px;overflow:hidden}
  .farm-search input{flex:1;background:transparent;border:none;color:#fff;padding:10px 12px;outline:none}
  .farm-search input::placeholder{color:rgba(255,255,255,.75)}
  .farm-search button{background:#fdd835;border:none;color:#2b1d12;font-weight:800;padding:0 16px;cursor:pointer}
  .farm-city-select{width:100%;background:rgba(255,255,255,.13);border:1px solid rgba(255,255,255,.28);border-radius:10px;color:#fff;padding:10px 12px;outline:none}
  .farm-city-select option{color:#222}

  .farm-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px}
  .farm-stat{background:var(--farm-card);border:1px solid var(--farm-border);border-radius:12px;padding:12px 14px;box-shadow:var(--farm-shadow)}
  .farm-stat .n{font-family:Georgia,serif;font-size:1.35rem;font-weight:800;color:var(--farm-leaf2)}
  .farm-stat .l{font-size:.75rem;color:var(--farm-muted);text-transform:uppercase;letter-spacing:.7px}

  .farm-main{display:grid;grid-template-columns:2fr 1fr;gap:14px;align-items:start}
  .farm-main > div,.farm-main > aside{min-width:0}
  .farm-card{background:var(--farm-card);border:1px solid var(--farm-border);border-radius:12px;box-shadow:var(--farm-shadow)}
  .farm-card-h{padding:12px 14px;border-bottom:1px solid var(--farm-border);display:flex;justify-content:space-between;align-items:center}
  .farm-card-h h3{margin:0;font-size:1rem;display:flex;align-items:center;gap:6px}
  .farm-card-b{padding:12px 14px}
  .farm-api-badge{display:inline-flex;align-items:center;gap:5px;background:rgba(26,94,32,.09);color:#1b5e20;border:1px solid rgba(26,94,32,.25);padding:2px 8px;border-radius:999px;font-size:.65rem;letter-spacing:.5px;text-transform:uppercase}

  .farm-articles{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
  .farm-article{border:1px solid var(--farm-border);border-radius:10px;padding:11px;background:#fff}
  .farm-article h4{margin:0 0 6px;font-size:.95rem;line-height:1.4}
  .farm-article h4 a{text-decoration:none;color:var(--farm-text)}
  .farm-article h4 a:hover{color:var(--farm-leaf2)}
  .farm-article p{margin:0 0 7px;color:var(--farm-muted);font-size:.82rem;line-height:1.55}
  .farm-meta{font-size:.72rem;color:var(--farm-muted);display:flex;justify-content:space-between;gap:8px}

  .farm-scheme-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
  .farm-scheme{display:block;border:1px solid var(--farm-border);border-radius:10px;padding:10px;background:#fff;text-decoration:none;color:inherit;transition:all .2s}
  .farm-scheme:hover{border-color:rgba(26,94,32,.35);transform:translateY(-2px);box-shadow:0 8px 18px rgba(61,43,31,.12)}
  .farm-scheme h5{margin:0 0 5px;font-size:.88rem}
  .farm-scheme p{margin:0;color:var(--farm-muted);font-size:.76rem;line-height:1.5}
  .farm-scheme .go{margin-top:6px;font-size:.72rem;color:var(--farm-leaf2);font-weight:700}

  .farm-table-wrap{overflow:auto;border:1px solid var(--farm-border);border-radius:10px}
  .farm-table{width:100%;border-collapse:collapse;font-size:.8rem;min-width:640px}
  .farm-table th{background:var(--farm-soil);color:#fff;padding:8px 9px;text-align:left;font-size:.68rem;letter-spacing:.6px;text-transform:uppercase}
  .farm-table td{padding:8px 9px;border-bottom:1px solid var(--farm-border)}

  .farm-job-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
  .farm-job{border:1px solid var(--farm-border);border-radius:10px;padding:10px}
  .farm-job h5{margin:0 0 5px;font-size:.9rem}
  .farm-job p{margin:0 0 6px;color:var(--farm-muted);font-size:.77rem;line-height:1.55}
  .farm-job a{text-decoration:none;color:var(--farm-leaf2);font-weight:700;font-size:.78rem}

  .farm-news-item{padding:9px 0;border-bottom:1px solid var(--farm-border)}
  .farm-news-item:last-child{border-bottom:none}
  .farm-news-item a{text-decoration:none;color:var(--farm-text);font-size:.86rem;font-weight:700;line-height:1.45}
  .farm-news-item a:hover{color:var(--farm-leaf2)}
  .farm-news-meta{font-size:.72rem;color:var(--farm-muted);margin-top:4px}

  .farm-kpi{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--farm-border);font-size:.8rem}
  .farm-kpi:last-child{border-bottom:none}
  .farm-kpi strong{color:var(--farm-leaf2)}

  .farm-calendar{display:grid;grid-template-columns:100px repeat(12,1fr);gap:4px;align-items:center}
  .farm-cal-head{font-size:.66rem;text-transform:uppercase;color:var(--farm-muted);text-align:center}
  .farm-cal-crop{font-size:.74rem;font-weight:700}
  .farm-cal-box{height:20px;border-radius:4px;background:#e8e2d4}
  .farm-cal-box.on{background:#a5d6a7}
  .farm-cal-box.harvest{background:#fbc02d}

  .farm-weather{background:linear-gradient(135deg,#3d2b1f,#5c3d2e);border-radius:11px;padding:11px;color:#fff;margin-bottom:10px}
  .farm-weather .row{display:flex;justify-content:space-between;gap:8px;font-size:.82rem;margin-top:6px}
  .farm-weather .row span{min-width:0}
  .farm-weather .row strong{margin-left:auto;text-align:right;white-space:nowrap;min-width:max-content}
  .farm-news-live{margin-top:10px;border:1px dashed var(--farm-border);border-radius:10px;padding:8px 10px;background:#fff}
  .farm-news-live h5{margin:0 0 6px;font-size:.78rem;display:flex;align-items:center;justify-content:space-between;gap:8px}
  .farm-news-live .item{padding:7px 0;border-bottom:1px solid var(--farm-border)}
  .farm-news-live .item:last-child{border-bottom:none}
  .farm-news-live a{text-decoration:none;color:var(--farm-text);font-size:.78rem;font-weight:700;line-height:1.4}
  .farm-news-live small{display:block;color:var(--farm-muted);font-size:.66rem;margin-top:2px}

  @media(max-width:1100px){
    .farm-main{grid-template-columns:1fr}
    .farm-stats{grid-template-columns:repeat(2,1fr)}
    .farm-scheme-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  }

  @media(max-width:960px){
    .farm-stats{grid-template-columns:repeat(2,1fr);gap:12px}
    .farm-articles{grid-template-columns:1fr}
    .farm-job-list{grid-template-columns:1fr}
  }

  @media(max-width:768px){
    .farm-wrap{padding:12px 10px 20px}
    .farm-hero{padding:14px 12px}
    .farm-hero h1{font-size:clamp(1.25rem,6vw,1.8rem)}
    .farm-hero p{font-size:.9rem;line-height:1.5}
    .farm-hero-grid{grid-template-columns:1fr;gap:10px}
    .farm-search input, .farm-city-select{font-size:14px;padding:9px 10px}
    .farm-search button{padding:0 12px;font-size:12px}
    .farm-stats{grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:12px}
    .farm-stat{padding:10px 12px}
    .farm-stat .n{font-size:1.2rem}
    .farm-stat .l{font-size:.7rem}
    .farm-articles,.farm-scheme-grid,.farm-job-list{grid-template-columns:1fr}
    .farm-card-h h3{font-size:.88rem;font-size:clamp(.82rem,2.5vw,.95rem)}
    .farm-article h4{font-size:.9rem;margin-bottom:5px}
    .farm-article p{font-size:.78rem}
    .farm-scheme h5{font-size:.82rem}
    .farm-scheme p{font-size:.72rem}
    .farm-card#calendar .farm-card-b{overflow-x:auto;-webkit-overflow-scrolling:touch}
    .farm-calendar{grid-template-columns:80px repeat(12,minmax(28px,1fr));min-width:560px}
    .farm-cal-head{font-size:.6rem}
    .farm-cal-crop{font-size:.7rem}
    .farm-table{font-size:.75rem;min-width:640px}
    .farm-table td, .farm-table th{padding:6px 7px}
  }

  @media(max-width:640px){
    .farm-wrap{padding:10px 8px 16px}
    .farm-hero{padding:12px 10px;margin-bottom:12px}
    .farm-hero h1{font-size:clamp(1.1rem,5vw,1.5rem);margin-bottom:6px}
    .farm-hero p{font-size:.85rem;max-width:100%}
    .farm-stats{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-bottom:10px}
    .farm-stat{padding:8px 10px}
    .farm-stat .n{font-size:1rem}
    .farm-stat .l{font-size:.65rem;letter-spacing:.5px}
    .farm-card{border-radius:10px}
    .farm-card-h{padding:10px 12px;font-size:.85rem}
    .farm-card-b{padding:10px}
    .farm-article{padding:9px}
    .farm-article h4{font-size:.85rem}
    .farm-article p{font-size:.75rem;line-height:1.4}
    .farm-meta{font-size:.68rem}
    .farm-scheme{padding:8px}
    .farm-scheme:hover{transform:translateY(-1px)}
    .farm-job{padding:8px}
    .farm-job h5{font-size:.85rem}
    .farm-job p{font-size:.73rem}
    .farm-scheme-grid{gap:8px}
  }

  @media(max-width:480px){
    .farm-wrap{padding:8px 6px 12px}
    .farm-hero{padding:10px 8px}
    .farm-hero h1{font-size:clamp(.95rem,4vw,1.25rem)}
    .farm-hero p{font-size:.8rem}
    .farm-search, .farm-city-select{font-size:13px}
    .farm-search input{padding:8px}
    .farm-stats{grid-template-columns:1fr 1fr;gap:6px;margin-bottom:8px}
    .farm-stat{padding:6px 8px}
    .farm-stat .n{font-size:.95rem}
    .farm-stat .l{font-size:.6rem}
    .farm-card-h{padding:8px 10px}
    .farm-card-b{padding:8px}
    .farm-article{padding:7px}
    .farm-articles{gap:8px}
    .farm-scheme-grid{grid-template-columns:1fr;gap:6px}
    .farm-scheme{padding:7px}
    .farm-weather{padding:9px}
    .farm-weather .row{font-size:.74rem;gap:6px}
    .farm-weather .row strong{font-size:.74rem}
  }

  @media(max-width:360px){
    .farm-wrap{padding:6px 4px 10px}
    .farm-hero{padding:8px 6px}
    .farm-hero h1{font-size:clamp(.85rem,3vw,1.1rem);margin-bottom:4px}
    .farm-hero p{font-size:.75rem;margin:0}
    .farm-stats{gap:4px}
    .farm-card-h, .farm-card-b{padding:6px}
  }
</style>

@php
  $schemes = [
    ['name' => 'PM-KISAN', 'desc' => __('ui.scheme_pm_kisan_desc'), 'link' => 'https://pmkisan.gov.in'],
    ['name' => 'PM-KUSUM', 'desc' => __('ui.scheme_pm_kusum_desc'), 'link' => 'https://mnre.gov.in'],
    ['name' => 'PMFBY', 'desc' => __('ui.scheme_pmfby_desc'), 'link' => 'https://pmfby.gov.in'],
    ['name' => __('ui.kisan_credit_card'), 'desc' => __('ui.scheme_kcc_desc'), 'link' => 'https://www.nabard.org'],
    ['name' => 'e-NAM', 'desc' => __('ui.scheme_enam_desc'), 'link' => 'https://enam.gov.in'],
    ['name' => __('ui.national_bee_mission'), 'desc' => __('ui.scheme_nbm_desc'), 'link' => 'https://nbb.gov.in'],
  ];

  $cropRows = [
    [__('ui.crop_wheat'), [0,0,0,0,0,0,0,0,0,1,1,2]],
    [__('ui.crop_soybean'), [0,0,0,0,0,1,1,2,2,0,0,0]],
    [__('ui.crop_cotton'), [0,0,0,0,1,1,1,0,2,0,0,0]],
    [__('ui.crop_onion'), [1,2,0,0,0,0,1,1,1,0,2,0]],
  ];

  $monthLabels = ['J','F','M','A','M','J','J','A','S','O','N','D'];
@endphp

<div class="farm-wrap">
  <section class="farm-hero">
    <h1>🌾 {{ __('ui.smart_farming') }} — {{ $selectedCityName ? city_display_name($selectedCityName) : __('ui.all_cities') }}</h1>
    <p>{{ __('ui.farming_intro') }}</p>
    <div class="farm-hero-grid">
      <form class="farm-search" method="GET" action="{{ route('farming.index') }}">
        <input type="hidden" name="city_id" value="{{ $selectedCityId ?: '' }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('ui.farming_search_placeholder') }}">
        <button type="submit">{{ __('ui.search') }}</button>
      </form>
      <form method="GET" action="{{ route('farming.index') }}">
        <input type="hidden" name="q" value="{{ $q }}">
        <select name="city_id" class="farm-city-select" onchange="this.form.submit()">
          <option value="">📍 {{ __('ui.all_cities') }}</option>
          @foreach($cities as $city)
            <option value="{{ $city->id }}" {{ (int) ($selectedCityId ?? 0) === (int) $city->id ? 'selected' : '' }}>📍 {{ city_display_name($city->name) }}</option>
          @endforeach
        </select>
      </form>
    </div>
  </section>

  <section class="farm-stats">
    <div class="farm-stat"><div class="n">{{ $farmings->total() }}</div><div class="l">{{ __('ui.filtered_articles') }}</div></div>
    <div class="farm-stat"><div class="n">{{ $cityArticles }}</div><div class="l">{{ __('ui.city_articles_count', ['city' => $selectedCityName ? city_display_name($selectedCityName) : __('ui.all_cities')]) }}</div></div>
    <div class="farm-stat"><div class="n">{{ $marketRows->count() }}</div><div class="l">{{ __('ui.mandi_rows') }}</div></div>
    <div class="farm-stat"><div class="n">{{ $agriJobs->count() }}</div><div class="l">{{ __('ui.agri_jobs') }}</div></div>
  </section>

  <section class="farm-main">
    <div>
      <div class="farm-card" id="articles">
        <div class="farm-card-h">
          <h3>📰 {{ __('ui.city_farming_articles') }}</h3>
          <a href="{{ route('farming.create') }}" class="toggle-btn">+ {{ __('ui.share_blog') }}</a>
        </div>
        <div class="farm-card-b">
          <div class="farm-api-badge" style="margin-bottom:10px">{{ __('ui.dynamic_citywise_blogs') }}</div>
          <div class="farm-articles">
            @forelse($farmings as $f)
              <article class="farm-article">
                <h4><a href="{{ route('farming.show', $f) }}">{{ $f->title }}</a></h4>
                <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $f->content), 170) }}</p>
                <div class="farm-meta">
                  <span>✍️ {{ $f->author_name ?: __('ui.farmer') }} · 📍 {{ $f->city?->name ?: __('ui.city_not_set') }}</span>
                  <span>{{ $f->created_at?->format('d M Y') }}</span>
                </div>
              </article>
            @empty
              <p style="margin:0;color:var(--farm-muted)">{{ __('ui.no_farming_articles_city_filter') }}</p>
            @endforelse
          </div>
          @if($farmings->count())
            <div style="margin-top:12px">{{ $farmings->links('pagination::simple-bootstrap-5') }}</div>
          @endif
        </div>
      </div>

      <div class="farm-card" id="schemes" style="margin-top:14px">
        <div class="farm-card-h"><h3>🏛️ {{ __('ui.govt_schemes') }}</h3></div>
        <div class="farm-card-b">
          <div class="farm-scheme-grid">
            @foreach($schemes as $scheme)
              <a class="farm-scheme" href="{{ $scheme['link'] }}" target="_blank" rel="noopener">
                <h5>{{ $scheme['name'] }}</h5>
                <p>{{ $scheme['desc'] }}</p>
                <div class="go">{{ __('ui.open_official_site') }} →</div>
              </a>
            @endforeach
          </div>
        </div>
      </div>

      <div class="farm-card" id="mandi" style="margin-top:14px">
        <div class="farm-card-h">
          <h3>💰 {{ __('ui.mandi_prices') }} ({{ $selectedCityName ? city_display_name($selectedCityName) : __('ui.latest') }})</h3>
          <span class="farm-api-badge" id="farmMandiSource">{{ __('ui.db_live_api') }}</span>
        </div>
        <div class="farm-card-b">
          <div class="farm-table-wrap">
            <table class="farm-table">
              <thead>
                <tr>
                  <th>{{ __('ui.commodity') }}</th><th>{{ __('ui.city_label') }}</th><th>{{ __('ui.district') }}</th><th>{{ __('ui.modal') }}</th><th>{{ __('ui.min') }}</th><th>{{ __('ui.max') }}</th><th>{{ __('ui.date') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($marketRows as $row)
                  <tr>
                    <td>{{ $row->commodity ?: __('ui.dash') }}</td>
                    <td>{{ $row->city ?: ($row->city_rel?->name ?: __('ui.dash')) }}</td>
                    <td>{{ $row->district ?: __('ui.dash') }}</td>
                    <td>₹{{ $row->modal_price ? number_format((float) $row->modal_price, 0) : __('ui.dash') }}</td>
                    <td>₹{{ $row->min_price ? number_format((float) $row->min_price, 0) : __('ui.dash') }}</td>
                    <td>₹{{ $row->max_price ? number_format((float) $row->max_price, 0) : __('ui.dash') }}</td>
                    <td>{{ $row->price_date?->format('d M Y') ?: __('ui.dash') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="7">{{ __('ui.no_mandi_data_city') }}</td></tr>
                @endforelse
              </tbody>
              <tbody id="farmMandiLiveBody" style="display:none"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="farm-card" id="jobs" style="margin-top:14px">
        <div class="farm-card-h"><h3>💼 {{ __('ui.agriculture_jobs_city_wise') }}</h3></div>
        <div class="farm-card-b">
          <div class="farm-job-list">
            @forelse($agriJobs as $job)
              <div class="farm-job">
                <h5>{{ $job->title }}</h5>
                <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $job->description), 120) }}</p>
                <div class="farm-meta" style="margin-bottom:4px">
                  <span>📍 {{ $job->city?->name ?: ($job->location ?: __('ui.city_not_set')) }}</span>
                  <span>{{ $job->category ?: __('ui.general') }}</span>
                </div>
                <a href="{{ route('jobs.show', $job) }}">{{ __('ui.view_job') }} →</a>
              </div>
            @empty
              <p style="margin:0;color:var(--farm-muted)">{{ __('ui.no_agriculture_jobs_city') }}</p>
            @endforelse
          </div>
        </div>
      </div>

      <div class="farm-card" id="calendar" style="margin-top:14px">
        <div class="farm-card-h"><h3>📅 {{ __('ui.crop_calendar') }}</h3></div>
        <div class="farm-card-b">
          <div class="farm-calendar" style="margin-bottom:8px">
            <div></div>
            @foreach($monthLabels as $m)
              <div class="farm-cal-head">{{ $m }}</div>
            @endforeach
            @foreach($cropRows as $crop)
              <div class="farm-cal-crop">{{ $crop[0] }}</div>
              @foreach($crop[1] as $stage)
                <div class="farm-cal-box {{ $stage === 1 ? 'on' : ($stage === 2 ? 'harvest' : '') }}"></div>
              @endforeach
            @endforeach
          </div>
          <div style="font-size:.76rem;color:var(--farm-muted)">{{ __('ui.crop_calendar_legend') }}</div>
        </div>
      </div>
    </div>

    <aside>
      <div class="farm-card" id="weather">
        <div class="farm-card-h"><h3>🌦️ {{ __('ui.farm_weather') }}</h3></div>
        <div class="farm-card-b">
          <div class="farm-weather">
            <div style="font-size:.72rem;opacity:.86">{{ $selectedCityName ? city_display_name($selectedCityName) : __('ui.selected_city') }}</div>
            <div style="font-size:1.4rem;font-weight:800" id="farmTemp">--°C</div>
            <div style="font-size:.82rem;opacity:.92" id="farmCond">{{ __('ui.loading_condition') }}</div>
            <div class="row"><span>{{ __('ui.humidity') }}</span><strong id="farmHum">--%</strong></div>
            <div class="row"><span>{{ __('ui.wind') }}</span><strong id="farmWind">-- km/h</strong></div>
            <div class="row"><span>{{ __('ui.rain_chance') }}</span><strong id="farmRain">--%</strong></div>
          </div>
          <div style="font-size:.74rem;color:var(--farm-muted)">{{ __('ui.auto_city_coordinates') }}</div>
        </div>
      </div>

      <div class="farm-card" style="margin-top:12px">
        <div class="farm-card-h"><h3>📊 {{ __('ui.commodity_snapshot') }}</h3></div>
        <div class="farm-card-b">
          @forelse($topCommodities as $commodity)
            <div class="farm-kpi">
              <span>{{ $commodity['name'] }}</span>
              <strong>₹{{ $commodity['modal_price'] ? number_format((float) $commodity['modal_price'], 0) : __('ui.dash') }}</strong>
            </div>
          @empty
            <div style="font-size:.78rem;color:var(--farm-muted)">{{ __('ui.no_commodity_summary') }}</div>
          @endforelse
        </div>
      </div>

      <div class="farm-card" style="margin-top:12px">
        <div class="farm-card-h">
          <h3>📰 {{ __('ui.city_agriculture_news') }}</h3>
          <span class="farm-api-badge">Google News RSS</span>
        </div>
        <div class="farm-card-b">
          @forelse($cityNews as $news)
            <article class="farm-news-item">
              <a href="{{ $news['link'] }}" target="_blank" rel="noopener">{{ $news['title'] }}</a>
              <div class="farm-news-meta">{{ $news['source'] ?: __('ui.news') }} · {{ $news['pubDate'] ?: __('ui.latest') }}</div>
            </article>
          @empty
            <div style="font-size:.78rem;color:var(--farm-muted)">{{ __('ui.city_agri_news_unavailable') }}</div>
          @endforelse

          <div class="farm-news-live" id="hnNewsBox">
            <h5>
              <span>⚡ {{ __('ui.live_farming_headlines') }}</span>
              <span class="farm-api-badge" style="font-size:.6rem;padding:1px 6px">Hacker News Algolia</span>
            </h5>
            <div id="hnNewsItems" style="font-size:.75rem;color:var(--farm-muted)">{{ __('ui.loading_live_headlines') }}</div>
          </div>
        </div>
      </div>
    </aside>
  </section>
</div>

<script>
  (function(){
    const lat = @json((float) ($selectedCity?->latitude ?? 18.5204));
    const lng = @json((float) ($selectedCity?->longitude ?? 73.8567));
    const agmarknetState = @json((string) ($selectedCity?->agmarknet_state ?? 'Maharashtra'));
    const agmarknetDistrict = @json((string) ($selectedCity?->agmarknet_district ?? ''));
    const cityName = @json((string) ($selectedCityName ?? 'Maharashtra'));
    const FARM_LIVE_MANDI_URL = @json(route('farming.live_mandi'));
    const farmI18n = {
      clear: @json(__('ui.weather_clear')),
      mostlyClear: @json(__('ui.weather_mostly_clear')),
      partlyCloudy: @json(__('ui.weather_partly_cloudy')),
      overcast: @json(__('ui.weather_overcast')),
      fog: @json(__('ui.weather_fog')),
      drizzle: @json(__('ui.weather_drizzle')),
      lightRain: @json(__('ui.weather_light_rain')),
      rain: @json(__('ui.weather_rain')),
      heavyRain: @json(__('ui.weather_heavy_rain')),
      showers: @json(__('ui.weather_showers')),
      heavyShowers: @json(__('ui.weather_heavy_showers')),
      thunderstorm: @json(__('ui.weather_thunderstorm')),
      weatherUpdate: @json(__('ui.weather_update')),
      weatherUnavailable: @json(__('ui.weather_unavailable')),
      agmarknetLive: @json(__('ui.agmarknet_live_badge')),
      newsFallback: @json(__('ui.news')),
      noLiveHeadlines: @json(__('ui.no_live_headlines')),
      liveNewsUnavailable: @json(__('ui.live_news_unavailable')),
    };

    async function loadFarmWeather(){
      try {
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m,precipitation_probability&timezone=Asia%2FKolkata`;
        const response = await fetch(url);
        const data = await response.json();
        const current = data.current || {};

        const condMap = {
          0:farmI18n.clear,1:farmI18n.mostlyClear,2:farmI18n.partlyCloudy,3:farmI18n.overcast,45:farmI18n.fog,51:farmI18n.drizzle,53:farmI18n.drizzle,61:farmI18n.lightRain,63:farmI18n.rain,65:farmI18n.heavyRain,80:farmI18n.showers,81:farmI18n.showers,82:farmI18n.heavyShowers,95:farmI18n.thunderstorm
        };

        document.getElementById('farmTemp').textContent = `${Math.round(current.temperature_2m ?? 0)}°C`;
        document.getElementById('farmCond').textContent = condMap[current.weather_code] || farmI18n.weatherUpdate;
        document.getElementById('farmHum').textContent = `${current.relative_humidity_2m ?? '--'}%`;
        document.getElementById('farmWind').textContent = `${current.wind_speed_10m ?? '--'} km/h`;
        document.getElementById('farmRain').textContent = `${current.precipitation_probability ?? '--'}%`;
      } catch (e) {
        document.getElementById('farmCond').textContent = farmI18n.weatherUnavailable;
      }
    }

    async function loadLiveMandi(){
      try {
        const params = new URLSearchParams({
          state: agmarknetState || 'Maharashtra',
          limit: '14',
        });
        if (agmarknetDistrict) {
          params.set('district', agmarknetDistrict);
        }

        const res = await fetch(`${FARM_LIVE_MANDI_URL}?${params.toString()}`, {
          headers: { 'Accept': 'application/json' },
        });
        if (!res.ok) {
          throw new Error(`Live mandi request failed with status ${res.status}`);
        }
        const data = await res.json();
        const records = Array.isArray(data.records) ? data.records : [];

        if (!records.length) return;

        const body = document.getElementById('farmMandiLiveBody');
        if (!body) return;

        const rows = records.slice(0, 12).map((rec) => {
          const commodity = rec.Commodity || rec.commodity || @json(__('ui.dash'));
          const city = rec.Market || rec.market || rec.District || @json(__('ui.dash'));
          const district = rec.District || rec.district || @json(__('ui.dash'));
          const modal = rec.Modal_Price || rec.modal_price || @json(__('ui.dash'));
          const min = rec.Min_Price || rec.min_price || @json(__('ui.dash'));
          const max = rec.Max_Price || rec.max_price || @json(__('ui.dash'));
          const date = rec.Arrival_Date || rec.arrival_date || @json(__('ui.dash'));

          return `<tr><td>${commodity}</td><td>${city}</td><td>${district}</td><td>₹${modal}</td><td>₹${min}</td><td>₹${max}</td><td>${date}</td></tr>`;
        });

        body.innerHTML = rows.join('');
        body.style.display = '';

        const initialBodies = body.parentElement.querySelectorAll('tbody:not(#farmMandiLiveBody)');
        initialBodies.forEach((tb) => {
          tb.style.display = 'none';
        });

        const source = document.getElementById('farmMandiSource');
        if (source) {
          source.textContent = farmI18n.agmarknetLive;
        }
      } catch (e) {
      }
    }

    async function loadLiveHnNews(){
      const container = document.getElementById('hnNewsItems');
      if (!container) return;

      try {
        const q = encodeURIComponent(`${cityName} farming agriculture mandi`);
        const res = await fetch(`https://hn.algolia.com/api/v1/search?query=${q}&tags=story&hitsPerPage=5`);
        const data = await res.json();
        const hits = (data.hits || []).filter((h) => h.title && h.url);

        if (!hits.length) {
          container.textContent = farmI18n.noLiveHeadlines;
          return;
        }

        container.innerHTML = hits.slice(0, 5).map((h) => {
          const domain = (() => {
            try { return new URL(h.url).hostname.replace('www.', ''); } catch (_) { return farmI18n.newsFallback; }
          })();
          return `<div class="item"><a href="${h.url}" target="_blank" rel="noopener">${h.title}</a><small>${domain}</small></div>`;
        }).join('');
      } catch (e) {
        container.textContent = farmI18n.liveNewsUnavailable;
      }
    }

    loadFarmWeather();
    loadLiveMandi();
    loadLiveHnNews();
  })();
</script>
@endsection
