@extends('layouts.app')

@section('content')
<style>
  :root {
    --gold:#D4A017;--gold-light:#F0C040;--dark:#0D0D0D;--dark2:#141414;--dark3:#1C1C1C;--dark4:#252525;
    --card-bg:#181818;--text:#F5F0E8;--text-muted:#9A9080;--text-dim:#5A5248;
    --accent-green:#2EC87A;--accent-red:#E84040;--accent-blue:#4A8FE8;
    --border:rgba(212,160,23,0.15);--border-hover:rgba(212,160,23,0.4);--radius:14px;
  }
  .hero{padding:3rem 2rem;text-align:center;position:relative;overflow:hidden;border-bottom:1px solid var(--border)}
  .hero::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:600px;height:300px;background:radial-gradient(ellipse,rgba(212,160,23,0.12) 0%,transparent 70%);pointer-events:none}
  .hero h1{font-size:clamp(2rem,5vw,3.5rem);font-weight:700;line-height:1.05;letter-spacing:-1px;margin-bottom:1rem}
  .hero h1 span{color:var(--gold)}
  .hero p{color:var(--text-muted);font-size:1rem;max-width:620px;margin:0 auto 2rem;line-height:1.6}
  .hero-search{display:flex;max-width:680px;margin:0 auto;background:var(--dark3);border:1px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s}
  .hero-search:focus-within{border-color:var(--gold)}
  .hero-search select{background:var(--dark4);border:none;color:var(--text-muted);font-size:13px;padding:0 16px;border-right:1px solid var(--border);cursor:pointer;outline:none;min-width:160px}
  .hero-search input{flex:1;background:transparent;border:none;color:var(--text);font-size:14px;padding:12px 16px;outline:none}
  .hero-search input::placeholder{color:var(--text-dim)}
  .hero-search .search-btn{background:var(--gold);border:none;color:var(--dark);padding:0 24px;font-weight:700;font-size:13px;cursor:pointer;transition:background .2s}
  .hero-search .search-btn:hover{background:var(--gold-light)}

  .stats-strip{display:flex;justify-content:center;border-bottom:1px solid var(--border);background:var(--dark2);overflow-x:auto}
  .stat-item{padding:1rem 2rem;border-right:1px solid var(--border);text-align:center;white-space:nowrap}
  .stat-item:last-child{border-right:none}
  .stat-num{font-size:1.5rem;font-weight:700;color:var(--gold);letter-spacing:-1px}
  .stat-label{font-size:12px;color:var(--text-muted);margin-top:2px}

  .section{padding:2rem;max-width:1280px;margin:0 auto}
  .cat-tabs{display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none}
  .cat-tabs::-webkit-scrollbar{display:none}
  .cat-tab{display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;border:1px solid var(--border);background:var(--dark3);cursor:pointer;white-space:nowrap;font-size:13px;font-weight:500;color:var(--text-muted);transition:all .2s}
  .cat-tab:hover{border-color:var(--border-hover);color:var(--text)}
  .cat-tab.active{background:rgba(212,160,23,0.12);border-color:var(--gold);color:var(--gold)}

  .filter-bar{display:flex;gap:10px;align-items:center;margin:1rem 0;flex-wrap:wrap}
  .filter-chip{padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--dark3);color:var(--text-muted);font-size:12px;cursor:pointer;transition:all .2s}

  .jobs-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-top:1rem}
  .job-card{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:all .25s;cursor:pointer;position:relative}
  .job-card:hover{border-color:var(--border-hover);transform:translateY(-3px);box-shadow:0 12px 40px rgba(0,0,0,0.4)}
  .job-card-head{padding:14px 16px;border-bottom:1px solid var(--border)}
  .job-card-title{font-size:16px;font-weight:700;color:var(--text);margin-bottom:6px;line-height:1.3}
  .job-card-company{font-size:13px;color:var(--text-muted)}
  .job-card-body{padding:14px 16px}
  .job-card-meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px}
  .job-chip{font-size:11px;padding:4px 9px;border-radius:999px;background:var(--dark3);border:1px solid var(--border);color:var(--text-muted)}
  .job-card-desc{font-size:13px;color:var(--text-dim);line-height:1.6;min-height:64px}
  .job-card-actions{display:flex;gap:8px;margin-top:12px}
  .btn-primary{background:var(--gold);border:none;color:var(--dark);padding:10px 20px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:700;transition:all .2s;text-decoration:none}
  .btn-primary:hover{background:var(--gold-light)}
  .btn-outline{background:transparent;border:1px solid var(--border-hover);color:var(--gold);padding:10px 16px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;text-decoration:none}

  .detail-panel{display:none;position:fixed;right:0;top:0;bottom:0;width:min(500px,100%);z-index:150;background:var(--dark2);border-left:1px solid var(--border);overflow-y:auto;transform:translateX(100%);transition:transform .3s}
  .detail-panel.open{display:block;transform:translateX(0)}
  .panel-hdr{padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;position:sticky;top:0;background:var(--dark2);z-index:5}
  .panel-cls{background:none;border:none;color:var(--text-muted);font-size:20px;cursor:pointer;padding:4px}
  .panel-body{padding:1.5rem}
  .panel-title{font-size:1.2rem;font-weight:700;color:var(--text);margin-bottom:6px}
  .panel-sub{font-size:13px;color:var(--text-muted);margin-bottom:14px}
  .panel-desc{color:var(--text-muted);font-size:13px;line-height:1.7;margin-bottom:1.2rem}
  .panel-specs{background:var(--dark3);border-radius:10px;padding:1rem;margin-bottom:1.2rem}
  .spec-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:12px}
  .spec-row:last-child{border-bottom:none}
  .spec-k{color:var(--text-muted)}
  .spec-v{font-weight:500;color:var(--text)}
  .panel-actions{display:flex;gap:10px;flex-wrap:wrap}
  .no-results{text-align:center;padding:3rem;color:var(--text-muted)}

  @media(max-width:1024px){
    .jobs-grid{grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px}
    .section{padding:1.5rem}
  }

  @media(max-width:768px){
    .hero{padding:2rem 1.5rem}
    .hero h1{font-size:clamp(1.5rem,5vw,2.5rem);margin-bottom:.8rem}
    .hero p{font-size:.95rem;margin-bottom:1.5rem}
    .hero-search{flex-direction:column;border-radius:8px}
    .hero-search select,.hero-search .search-btn{border-right:none;border-bottom:1px solid var(--border);width:100%;text-align:left}
    .hero-search select{min-width:auto}
    .hero-search .search-btn{padding:11px 14px}
    .stats-strip{flex-wrap:wrap;overflow:visible}
    .stat-item{padding:.8rem 1.5rem;border-right:1px solid var(--border);border-bottom:1px solid var(--border)}
    .stat-item:nth-child(even){border-right:none}
    .stat-num{font-size:1.3rem}
    .stat-label{font-size:11px}
    .section{padding:1.25rem 1rem}
    .cat-tabs{gap:8px;margin-bottom:10px}
    .cat-tab{padding:6px 12px;font-size:12px}
    .jobs-grid{grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-top:.8rem}
    .job-card-head{padding:12px 14px}
    .job-card-title{font-size:14px;margin-bottom:4px}
    .job-card-company{font-size:12px}
    .job-card-body{padding:12px 14px}
    .job-card-meta{gap:6px;margin-bottom:8px}
    .job-chip{font-size:10px;padding:3px 7px}
    .job-card-desc{font-size:12px;min-height:50px}
    .job-card-actions{gap:6px;margin-top:10px}
    .btn-primary,.btn-outline{padding:8px 14px;font-size:12px}
    .detail-panel{width:100%;max-width:100%}
  }

  @media(max-width:640px){
    .hero{padding:1.5rem 1rem}
    .hero h1{font-size:clamp(1.25rem,4vw,1.8rem);margin-bottom:6px}
    .hero p{font-size:.9rem;margin-bottom:1.2rem}
    .hero-search input{padding:10px 12px;font-size:13px}
    .hero-search select{padding:10px 12px}
    .stats-strip{gap:0;border-bottom:none}
    .stat-item{flex:1;padding:.6rem 1rem;border-right:1px solid var(--border);border-bottom:none;font-size:12px}
    .stat-item:nth-child(odd){border-bottom:1px solid var(--border)}
    .stat-item:nth-child(3n){border-right:none}
    .stat-num{font-size:1.15rem}
    .stat-label{font-size:10px;margin-top:1px}
    .section{padding:1rem 8px}
    .cat-tabs{gap:6px;padding-bottom:2px;scroll-behavior:smooth}
    .cat-tab{padding:5px 10px;font-size:11px}
    .jobs-grid{grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px}
    .job-card{border-radius:10px}
    .job-card-head{padding:10px 12px}
    .job-card-title{font-size:13px;margin-bottom:3px}
    .job-card-company{font-size:11px}
    .job-card-body{padding:10px 12px}
    .job-chip{font-size:9px;padding:2px 6px}
    .job-card-desc{font-size:11px;line-height:1.4;min-height:40px}
    .job-card-actions{flex-direction:column;gap:4px}
    .btn-primary,.btn-outline{width:100%;padding:7px 10px;font-size:11px;text-align:center}
    .detail-panel{width:100%;right:auto;left:0}
  }

  @media(max-width:480px){
    .hero{padding:1.2rem 10px}
    .hero h1{font-size:clamp(1.1rem,3vw,1.4rem);margin-bottom:4px;letter-spacing:0}
    .hero p{font-size:.85rem;margin-bottom:1rem}
    .hero-search{border-radius:6px}
    .hero-search select,.hero-search .search-btn{border-bottom:1px solid var(--border);padding:9px 10px;font-size:12px}
    .hero-search input{padding:9px 10px;font-size:12px}
    .stats-strip{gap:0}
    .stat-item{padding:.5rem .8rem;border-right:none;border-bottom:1px solid var(--border);text-align:center;flex:1 1 50%}
    .stat-item:nth-child(2n){border-right:1px solid var(--border);border-bottom:1px solid var(--border)}
    .stat-item:last-child{border-right:0;border-bottom:0}
    .stat-num{font-size:1rem}
    .stat-label{font-size:9px}
    .section{padding:.8rem 6px}
    .cat-tabs{gap:4px;overflow-x:auto;-webkit-overflow-scrolling:touch}
    .cat-tab{padding:4px 8px;font-size:10px;border-radius:6px}
    .filter-bar{flex-direction:column;gap:8px;margin-top:.8rem}
    .filter-chip{width:auto}
    .jobs-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:10px}
    .job-card-head{padding:8px 10px}
    .job-card-title{font-size:12px;margin-bottom:2px;line-height:1.2}
    .job-card-company{font-size:10px}
    .job-card-body{padding:8px 10px}
    .job-card-meta{gap:4px;margin-bottom:6px}
    .job-chip{font-size:8px;padding:2px 5px}
    .job-card-desc{display:none}
    .job-card-actions{display:none}
  }

  @media(max-width:360px){
    .hero h1{font-size:clamp(1rem,2.5vw,1.2rem)}
    .hero p{font-size:.8rem}
    .stat-item{padding:.4rem .6rem;font-size:10px}
    .stat-num{font-size:.9rem}
    .stat-label{font-size:8px}
    .jobs-grid{grid-template-columns:1fr}
  }
</style>

<div class="hero">
  <h1>{{ __('ui.city_label') }} <span>{{ __('ui.jobs') }}</span></h1>
  <p>{{ __('ui.jobs_intro') }}</p>
  <div class="hero-search">
    <select id="searchCategory" onchange="applyFilters()">
      <option value="">{{ __('ui.all_categories') }}</option>
      @foreach($categories as $cat)
        <option value="{{ $cat }}" {{ ($category ?? '') === $cat ? 'selected' : '' }}>{{ $categoryLabels[$cat] ?? $cat }}</option>
      @endforeach
    </select>
    <input type="text" id="searchInput" value="{{ $q ?? '' }}" placeholder="{{ __('ui.jobs_search_placeholder') }}" oninput="applyFilters()">
    <button class="search-btn" onclick="applyFilters()">{{ __('ui.search') }}</button>
  </div>
</div>

<div class="stats-strip">
  <div class="stat-item"><div class="stat-num">{{ $jobs->total() }}</div><div class="stat-label">{{ __('ui.filtered_jobs') }}</div></div>
  <div class="stat-item"><div class="stat-num">{{ $cityJobs }}</div><div class="stat-label">{{ $selectedCityName ? city_display_name($selectedCityName) : __('ui.all_cities') }}</div></div>
  <div class="stat-item"><div class="stat-num">{{ $totalJobs }}</div><div class="stat-label">{{ __('ui.total_jobs') }}</div></div>
  <div class="stat-item"><div class="stat-num">{{ __('ui.live') }}</div><div class="stat-label">{{ __('ui.updated_daily') }}</div></div>
</div>

<div class="section">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:1rem">
    <h2 style="font-size:1.3rem;color:#0D0D0D;margin:0">{{ __('ui.browse_jobs') }}</h2>
    @auth
      @if(auth()->user()->isSuperadmin())
        <a class="btn-primary" href="{{ route('jobs.create') }}">+ {{ __('ui.add_job') }}</a>
      @endif
    @endauth
  </div>

  <div class="cat-tabs">
    <div class="cat-tab {{ ($category ?? '') === '' ? 'active' : '' }}" onclick="filterByCat('',this)">🔥 {{ __('ui.all') }}</div>
    @foreach($categories->take(8) as $cat)
      <div class="cat-tab {{ ($category ?? '') === $cat ? 'active' : '' }}" onclick="filterByCat(@js($cat),this)">{{ $categoryLabels[$cat] ?? $cat }}</div>
    @endforeach
  </div>

  <form method="GET" action="{{ route('jobs.index') }}" class="filter-bar" id="filterForm" style="margin-top:1rem">
    <input type="hidden" id="categoryFilter" name="category" value="{{ $category ?? '' }}">
    <input type="hidden" id="cityIdFilter" name="city_id" value="{{ $selectedCityId ?? '' }}">
    <input type="hidden" id="searchFilter" name="q" value="{{ $q ?? '' }}">
    <select id="citySelect" onchange="document.getElementById('cityIdFilter').value=this.value;document.getElementById('filterForm').submit()" class="filter-chip" style="margin-left:auto">
      <option value="">{{ __('ui.all_cities') }}</option>
      @foreach($cities as $city)
        <option value="{{ $city->id }}" {{ (int) ($selectedCityId ?? 0) === (int) $city->id ? 'selected' : '' }}>📍 {{ city_display_name($city->name) }}</option>
      @endforeach
    </select>
  </form>

  <div class="jobs-grid" id="jobsGrid">
    @forelse($jobs as $job)
      @php
        $jobPayload = json_encode([
          'title' => $job->title,
          'company' => $job->company,
          'location' => $job->location,
          'category' => $job->category,
          'city' => $job->city?->name,
          'description' => $job->description,
          'showUrl' => route('jobs.show', $job),
          'applyUrl' => route('jobs.apply', $job),
          'publishedAt' => $job->created_at?->format('d M Y, h:i A'),
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
      @endphp
      <div class="job-card" onclick='openPanel({!! $jobPayload !!})'>
        <div class="job-card-head">
          <div class="job-card-title">{{ $job->title }}</div>
          <div class="job-card-company">{{ $job->company ?: __('ui.company_not_specified') }}</div>
        </div>
        <div class="job-card-body">
          <div class="job-card-meta">
            <span class="job-chip">📍 {{ $job->city?->name ?: ($job->location ?: __('ui.city_not_set')) }}</span>
            @if($job->category)
              <span class="job-chip">🏷 {{ $job->category }}</span>
            @endif
          </div>
          <div class="job-card-desc">{{ \Illuminate\Support\Str::limit(strip_tags($job->description ?: __('ui.open_job_for_complete_details')), 140) }}</div>
          <div class="job-card-actions">
            <a class="btn-outline" href="{{ route('jobs.show', $job) }}" onclick="event.stopPropagation()">{{ __('ui.view') }}</a>
            <a class="btn-primary" href="{{ route('jobs.apply', $job) }}" onclick="event.stopPropagation()">{{ __('ui.apply') }}</a>
          </div>
        </div>
      </div>
    @empty
      <div class="no-results" style="grid-column:1/-1">
        <div style="font-size:3rem;margin-bottom:1rem">🔍</div>
        <p>{{ __('ui.no_jobs_found_filter') }}</p>
      </div>
    @endforelse
  </div>

  @if($jobs->count())
    <div style="margin-top:2rem;text-align:center">
      {{ $jobs->links('pagination::simple-bootstrap-5') }}
    </div>
  @endif
</div>

<div class="detail-panel" id="detailPanel">
  <div class="panel-hdr">
    <button class="panel-cls" onclick="closePanel()">✕</button>
    <span style="font-size:14px;color:var(--text-muted)">{{ __('ui.job_detail') }}</span>
  </div>
  <div class="panel-body">
    <div class="panel-title" id="panelTitle"></div>
    <div class="panel-sub" id="panelSub"></div>
    <div class="panel-desc" id="panelDesc"></div>
    <div class="panel-specs" id="panelSpecs"></div>
    <div class="panel-actions">
      <a class="btn-outline" id="panelViewLink" href="#">{{ __('ui.view_job') }}</a>
      <a class="btn-primary" id="panelApplyLink" href="#">{{ __('ui.apply_now') }}</a>
    </div>
  </div>
</div>

<script>
const jobsI18n = {
  job: @json(__('ui.job')),
  companyNotSpecified: @json(__('ui.company_not_specified')),
  locationNotSet: @json(__('ui.location_not_set')),
  noDescriptionAvailable: @json(__('ui.no_description_available')),
  category: @json(__('ui.category')),
  location: @json(__('ui.location')),
  published: @json(__('ui.published')),
  general: @json(__('ui.general')),
  dash: @json(__('ui.dash')),
};

function applyFilters(){
  const search = document.getElementById('searchInput').value;
  const category = document.getElementById('searchCategory').value;
  const form = document.getElementById('filterForm');
  document.getElementById('searchFilter').value = search;
  document.getElementById('categoryFilter').value = category;
  form.submit();
}

function filterByCat(cat, el){
  document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('categoryFilter').value = cat;
  document.getElementById('filterForm').submit();
}

function openPanel(payload){
  document.getElementById('panelTitle').textContent = payload.title || jobsI18n.job;
  document.getElementById('panelSub').textContent = (payload.company || jobsI18n.companyNotSpecified) + ' · ' + (payload.city || payload.location || jobsI18n.locationNotSet);
  document.getElementById('panelDesc').textContent = payload.description || jobsI18n.noDescriptionAvailable;
  document.getElementById('panelSpecs').innerHTML =
    `<div class="spec-row"><span class="spec-k">${jobsI18n.category}</span><span class="spec-v">${payload.category || jobsI18n.general}</span></div>` +
    `<div class="spec-row"><span class="spec-k">${jobsI18n.location}</span><span class="spec-v">${payload.location || payload.city || jobsI18n.dash}</span></div>` +
    `<div class="spec-row"><span class="spec-k">${jobsI18n.published}</span><span class="spec-v">${payload.publishedAt || jobsI18n.dash}</span></div>`;

  document.getElementById('panelViewLink').href = payload.showUrl || '#';
  document.getElementById('panelApplyLink').href = payload.applyUrl || '#';
  document.getElementById('detailPanel').classList.add('open');
}

function closePanel(){
  document.getElementById('detailPanel').classList.remove('open');
}
</script>
@endsection
