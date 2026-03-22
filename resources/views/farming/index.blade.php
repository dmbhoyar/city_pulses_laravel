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

  .farm-wrap{max-width:1280px;margin:0 auto;padding:16px 18px 30px;color:var(--farm-text)}
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
  @media(max-width:768px){
    .farm-hero-grid{grid-template-columns:1fr}
    .farm-articles,.farm-scheme-grid,.farm-job-list{grid-template-columns:1fr}
    .farm-stats{grid-template-columns:1fr}
    .farm-calendar{grid-template-columns:1fr repeat(6,1fr)}
    .farm-wrap{padding:12px 10px 24px}
    .farm-hero{padding:16px 14px}
    .farm-hero h1{font-size:1.45rem}
    .farm-card-h h3{font-size:.92rem}
  }
</style>

@php
  $schemes = [
    ['name' => 'PM-KISAN', 'desc' => '₹6,000/year support in 3 installments for eligible farmer families.', 'link' => 'https://pmkisan.gov.in'],
    ['name' => 'PM-KUSUM', 'desc' => 'Solar pump support with subsidy for irrigation and lower energy cost.', 'link' => 'https://mnre.gov.in'],
    ['name' => 'PMFBY', 'desc' => 'Crop insurance for weather damage, pests, and production loss.', 'link' => 'https://pmfby.gov.in'],
    ['name' => 'Kisan Credit Card', 'desc' => 'Working capital credit line for seasonal farm operations.', 'link' => 'https://www.nabard.org'],
    ['name' => 'e-NAM', 'desc' => 'National agri market access and better mandi price discovery.', 'link' => 'https://enam.gov.in'],
    ['name' => 'National Bee Mission', 'desc' => 'Support for beekeeping, equipment and training.', 'link' => 'https://nbb.gov.in'],
  ];

  $cropRows = [
    ['Wheat', [0,0,0,0,0,0,0,0,0,1,1,2]],
    ['Soybean', [0,0,0,0,0,1,1,2,2,0,0,0]],
    ['Cotton', [0,0,0,0,1,1,1,0,2,0,0,0]],
    ['Onion', [1,2,0,0,0,0,1,1,1,0,2,0]],
  ];

  $monthLabels = ['J','F','M','A','M','J','J','A','S','O','N','D'];
@endphp

<div class="farm-wrap">
  <section class="farm-hero">
    <h1>🌾 Smart Farming — {{ $selectedCityName ?: 'All Cities' }}</h1>
    <p>Reference-style farming module integrated with your project data: city-wise farming articles, mandi prices, agri jobs, local agri news, schemes, and seasonal planning.</p>
    <div class="farm-hero-grid">
      <form class="farm-search" method="GET" action="{{ route('farming.index') }}">
        <input type="hidden" name="city_id" value="{{ $selectedCityId ?: '' }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search farming topics, crops, irrigation, organic...">
        <button type="submit">Search</button>
      </form>
      <form method="GET" action="{{ route('farming.index') }}">
        <input type="hidden" name="q" value="{{ $q }}">
        <select name="city_id" class="farm-city-select" onchange="this.form.submit()">
          <option value="">📍 All Cities</option>
          @foreach($cities as $city)
            <option value="{{ $city->id }}" {{ (int) ($selectedCityId ?? 0) === (int) $city->id ? 'selected' : '' }}>📍 {{ $city->name }}</option>
          @endforeach
        </select>
      </form>
    </div>
  </section>

  <section class="farm-stats">
    <div class="farm-stat"><div class="n">{{ $farmings->total() }}</div><div class="l">Filtered Articles</div></div>
    <div class="farm-stat"><div class="n">{{ $cityArticles }}</div><div class="l">{{ $selectedCityName ?: 'All City' }} Articles</div></div>
    <div class="farm-stat"><div class="n">{{ $marketRows->count() }}</div><div class="l">Mandi Rows</div></div>
    <div class="farm-stat"><div class="n">{{ $agriJobs->count() }}</div><div class="l">Agri Jobs</div></div>
  </section>

  <section class="farm-main">
    <div>
      <div class="farm-card" id="articles">
        <div class="farm-card-h">
          <h3>📰 City Farming Articles</h3>
          <a href="{{ route('farming.create') }}" class="toggle-btn">+ Share Blog</a>
        </div>
        <div class="farm-card-b">
          <div class="farm-api-badge" style="margin-bottom:10px">Dynamic · City-wise blogs by users</div>
          <div class="farm-articles">
            @forelse($farmings as $f)
              <article class="farm-article">
                <h4><a href="{{ route('farming.show', $f) }}">{{ $f->title }}</a></h4>
                <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $f->content), 170) }}</p>
                <div class="farm-meta">
                  <span>✍️ {{ $f->author_name ?: 'Farmer' }} · 📍 {{ $f->city?->name ?: 'City not set' }}</span>
                  <span>{{ $f->created_at?->format('d M Y') }}</span>
                </div>
              </article>
            @empty
              <p style="margin:0;color:var(--farm-muted)">No farming articles found for this city/filter.</p>
            @endforelse
          </div>
          @if($farmings->count())
            <div style="margin-top:12px">{{ $farmings->links('pagination::simple-bootstrap-5') }}</div>
          @endif
        </div>
      </div>

      <div class="farm-card" id="schemes" style="margin-top:14px">
        <div class="farm-card-h"><h3>🏛️ Government Schemes</h3></div>
        <div class="farm-card-b">
          <div class="farm-scheme-grid">
            @foreach($schemes as $scheme)
              <a class="farm-scheme" href="{{ $scheme['link'] }}" target="_blank" rel="noopener">
                <h5>{{ $scheme['name'] }}</h5>
                <p>{{ $scheme['desc'] }}</p>
                <div class="go">Open Official Site →</div>
              </a>
            @endforeach
          </div>
        </div>
      </div>

      <div class="farm-card" id="mandi" style="margin-top:14px">
        <div class="farm-card-h">
          <h3>💰 Mandi Prices ({{ $selectedCityName ?: 'Latest' }})</h3>
          <span class="farm-api-badge" id="farmMandiSource">DB + Live API</span>
        </div>
        <div class="farm-card-b">
          <div class="farm-table-wrap">
            <table class="farm-table">
              <thead>
                <tr>
                  <th>Commodity</th><th>City</th><th>District</th><th>Modal</th><th>Min</th><th>Max</th><th>Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse($marketRows as $row)
                  <tr>
                    <td>{{ $row->commodity ?: '—' }}</td>
                    <td>{{ $row->city ?: ($row->city_rel?->name ?: '—') }}</td>
                    <td>{{ $row->district ?: '—' }}</td>
                    <td>₹{{ $row->modal_price ? number_format((float) $row->modal_price, 0) : '—' }}</td>
                    <td>₹{{ $row->min_price ? number_format((float) $row->min_price, 0) : '—' }}</td>
                    <td>₹{{ $row->max_price ? number_format((float) $row->max_price, 0) : '—' }}</td>
                    <td>{{ $row->price_date?->format('d M Y') ?: '—' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="7">No mandi data available for this city right now.</td></tr>
                @endforelse
              </tbody>
              <tbody id="farmMandiLiveBody" style="display:none"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="farm-card" id="jobs" style="margin-top:14px">
        <div class="farm-card-h"><h3>💼 Agriculture Jobs (City Wise)</h3></div>
        <div class="farm-card-b">
          <div class="farm-job-list">
            @forelse($agriJobs as $job)
              <div class="farm-job">
                <h5>{{ $job->title }}</h5>
                <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $job->description), 120) }}</p>
                <div class="farm-meta" style="margin-bottom:4px">
                  <span>📍 {{ $job->city?->name ?: ($job->location ?: 'City not set') }}</span>
                  <span>{{ $job->category ?: 'General' }}</span>
                </div>
                <a href="{{ route('jobs.show', $job) }}">View Job →</a>
              </div>
            @empty
              <p style="margin:0;color:var(--farm-muted)">No agriculture jobs found for this city yet.</p>
            @endforelse
          </div>
        </div>
      </div>

      <div class="farm-card" id="calendar" style="margin-top:14px">
        <div class="farm-card-h"><h3>📅 Seasonal Crop Calendar</h3></div>
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
          <div style="font-size:.76rem;color:var(--farm-muted)">Green = sowing/growth, Yellow = harvest window. Adjust by local rainfall and soil condition.</div>
        </div>
      </div>
    </div>

    <aside>
      <div class="farm-card" id="weather">
        <div class="farm-card-h"><h3>🌦️ Farm Weather</h3></div>
        <div class="farm-card-b">
          <div class="farm-weather">
            <div style="font-size:.72rem;opacity:.86">{{ $selectedCityName ?: 'Selected City' }}</div>
            <div style="font-size:1.4rem;font-weight:800" id="farmTemp">--°C</div>
            <div style="font-size:.82rem;opacity:.92" id="farmCond">Loading condition...</div>
            <div class="row"><span>Humidity</span><strong id="farmHum">--%</strong></div>
            <div class="row"><span>Wind</span><strong id="farmWind">-- km/h</strong></div>
            <div class="row"><span>Rain chance</span><strong id="farmRain">--%</strong></div>
          </div>
          <div style="font-size:.74rem;color:var(--farm-muted)">Auto city coordinates from your city settings.</div>
        </div>
      </div>

      <div class="farm-card" style="margin-top:12px">
        <div class="farm-card-h"><h3>📊 Commodity Snapshot</h3></div>
        <div class="farm-card-b">
          @forelse($topCommodities as $commodity)
            <div class="farm-kpi">
              <span>{{ $commodity['name'] }}</span>
              <strong>₹{{ $commodity['modal_price'] ? number_format((float) $commodity['modal_price'], 0) : '—' }}</strong>
            </div>
          @empty
            <div style="font-size:.78rem;color:var(--farm-muted)">No commodity summary available.</div>
          @endforelse
        </div>
      </div>

      <div class="farm-card" style="margin-top:12px">
        <div class="farm-card-h">
          <h3>📰 City Agriculture News</h3>
          <span class="farm-api-badge">Google News RSS</span>
        </div>
        <div class="farm-card-b">
          @forelse($cityNews as $news)
            <article class="farm-news-item">
              <a href="{{ $news['link'] }}" target="_blank" rel="noopener">{{ $news['title'] }}</a>
              <div class="farm-news-meta">{{ $news['source'] ?: 'News' }} · {{ $news['pubDate'] ?: 'Latest' }}</div>
            </article>
          @empty
            <div style="font-size:.78rem;color:var(--farm-muted)">City-specific agriculture news not available right now.</div>
          @endforelse

          <div class="farm-news-live" id="hnNewsBox">
            <h5>
              <span>⚡ Live Farming Headlines</span>
              <span class="farm-api-badge" style="font-size:.6rem;padding:1px 6px">Hacker News Algolia</span>
            </h5>
            <div id="hnNewsItems" style="font-size:.75rem;color:var(--farm-muted)">Loading live headlines...</div>
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
    const AGMARKNET_KEY = '579b464db66ec23bdd000001cdd3946e44ce4aab0ddc33ad780ea6de';

    async function loadFarmWeather(){
      try {
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m,precipitation_probability&timezone=Asia%2FKolkata`;
        const response = await fetch(url);
        const data = await response.json();
        const current = data.current || {};

        const condMap = {
          0:'Clear',1:'Mostly Clear',2:'Partly Cloudy',3:'Overcast',45:'Fog',51:'Drizzle',53:'Drizzle',61:'Light Rain',63:'Rain',65:'Heavy Rain',80:'Showers',81:'Showers',82:'Heavy Showers',95:'Thunderstorm'
        };

        document.getElementById('farmTemp').textContent = `${Math.round(current.temperature_2m ?? 0)}°C`;
        document.getElementById('farmCond').textContent = condMap[current.weather_code] || 'Weather update';
        document.getElementById('farmHum').textContent = `${current.relative_humidity_2m ?? '--'}%`;
        document.getElementById('farmWind').textContent = `${current.wind_speed_10m ?? '--'} km/h`;
        document.getElementById('farmRain').textContent = `${current.precipitation_probability ?? '--'}%`;
      } catch (e) {
        document.getElementById('farmCond').textContent = 'Weather unavailable';
      }
    }

    async function loadLiveMandi(){
      try {
        let url = `https://api.data.gov.in/resource/9ef4c6348b5524a09a98c1dc8b3f6b0b?api-key=${AGMARKNET_KEY}&format=json&limit=14`;

        if (agmarknetState) {
          url += `&filters[State.keyword]=${encodeURIComponent(agmarknetState)}`;
        }

        if (agmarknetDistrict) {
          url += `&filters[District.keyword]=${encodeURIComponent(agmarknetDistrict)}`;
        }

        const res = await fetch(url);
        const data = await res.json();
        const records = Array.isArray(data.records) ? data.records : [];

        if (!records.length) return;

        const body = document.getElementById('farmMandiLiveBody');
        if (!body) return;

        const rows = records.slice(0, 12).map((rec) => {
          const commodity = rec.Commodity || rec.commodity || '—';
          const city = rec.Market || rec.market || rec.District || '—';
          const district = rec.District || rec.district || '—';
          const modal = rec.Modal_Price || rec.modal_price || '—';
          const min = rec.Min_Price || rec.min_price || '—';
          const max = rec.Max_Price || rec.max_price || '—';
          const date = rec.Arrival_Date || rec.arrival_date || '—';

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
          source.textContent = 'data.gov.in · Agmarknet Live';
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
          container.textContent = 'No live headlines available right now.';
          return;
        }

        container.innerHTML = hits.slice(0, 5).map((h) => {
          const domain = (() => {
            try { return new URL(h.url).hostname.replace('www.', ''); } catch (_) { return 'News'; }
          })();
          return `<div class="item"><a href="${h.url}" target="_blank" rel="noopener">${h.title}</a><small>${domain}</small></div>`;
        }).join('');
      } catch (e) {
        container.textContent = 'Live news unavailable right now.';
      }
    }

    loadFarmWeather();
    loadLiveMandi();
    loadLiveHnNews();
  })();
</script>
@endsection
