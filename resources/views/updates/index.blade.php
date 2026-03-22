@extends('layouts.app')

@section('title', 'CityPulse Updates | Dynamic Daily Edition')
@section('meta_description', 'Dynamic CityPulse newspaper-style updates page with local news, events, jobs, markets, and live city highlights.')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Source+Serif+4:ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,600;1,8..60,400&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

@php
  $leadStory = $updates->first();
  $secondaryStories = $updates->slice(1, 4);
  $frontEvents = $eventUpdates->take(4);
  $frontJobs = $jobs->take(3);
  $newsGridItems = $updates->take(8);
  $editorials = $updates->where('update_type', '!=', 'event')->take(3)->values();
  $tickerItems = collect($updates->pluck('title'))
      ->merge(collect($cityNews)->pluck('title'))
      ->filter()
      ->take(12)
      ->values();
  $tickerItems = $tickerItems->isNotEmpty() ? $tickerItems : collect([
      'Dynamic city updates will appear here as soon as fresh stories are available.',
      'Use the city filter to switch the edition by location.',
      'Superadmin can publish alerts, events, and offers from the Updates module.',
  ]);
  $editionDate = now()->format('l, d F Y');
  $cityEdition = $selectedCity?->name ?: 'All Cities';
  $leadLink = $leadStory ? ($leadStory->source_url ?: route('updates.show', $leadStory)) : '#';
  $leadPhoto = $leadStory && $leadStory->photo_path ? \Illuminate\Support\Facades\Storage::url($leadStory->photo_path) : null;
@endphp

<style>
  #updatesEdition{--ink:#1a1208;--ink2:#2d2416;--ink3:#5a4d3a;--paper:#f8f3e8;--paper2:#f2ead8;--paper3:#e8dfc5;--cream:#faf7f0;--red:#b91c1c;--red2:#dc2626;--gold:#b8860b;--gold2:#d4a017;--gold3:#f0c040;--silver:#64748b;--green:#166534;--rule:rgba(26,18,8,.14);--rule2:rgba(26,18,8,.42);--fh:'Playfair Display',serif;--fb:'Source Serif 4',serif;--fm:'JetBrains Mono',monospace;background:var(--paper);color:var(--ink);font-family:var(--fb);margin:-22px;padding-bottom:24px}
  #updatesEdition *{box-sizing:border-box}
  #updatesEdition a{text-decoration:none}
  #updatesEdition .mast{background:var(--ink);color:var(--paper)}
  #updatesEdition .mast-meta{display:flex;align-items:center;justify-content:space-between;padding:.4rem 1.3rem;border-bottom:1px solid rgba(255,255,255,.1);flex-wrap:wrap;gap:.4rem}
  #updatesEdition .mast-meta span{font-family:var(--fm);font-size:10px;color:rgba(248,243,232,.6)}
  #updatesEdition .mast-meta .ed{color:var(--gold3);font-weight:700;letter-spacing:2px}
  #updatesEdition .mast-hero{padding:1.2rem 1.3rem .75rem;text-align:center}
  #updatesEdition .mast-rule{height:3px;background:linear-gradient(90deg,transparent,var(--gold2),transparent);margin-bottom:5px}
  #updatesEdition .mast-name{font-family:var(--fh);font-size:clamp(2.3rem,7vw,5rem);font-weight:900;line-height:.93;letter-spacing:-2px;color:var(--paper)}
  #updatesEdition .mast-strap{font-family:var(--fh);font-style:italic;font-size:clamp(.8rem,1.7vw,1.05rem);color:var(--gold3);letter-spacing:2px;margin-top:5px}
  #updatesEdition .mast-nav{display:flex;align-items:center;justify-content:center;border-top:3px solid var(--gold);flex-wrap:wrap}
  #updatesEdition .nb{color:rgba(248,243,232,.76);font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:.55rem 1rem;cursor:pointer;transition:color .2s;border-right:1px solid rgba(255,255,255,.1)}
  #updatesEdition .nb:last-child{border-right:none}
  #updatesEdition .nb:hover,#updatesEdition .nb.on{color:var(--gold3)}
  #updatesEdition .mast-actions{display:flex;gap:8px;justify-content:flex-end;align-items:center;padding:.6rem 1.3rem;border-top:1px solid rgba(255,255,255,.07);flex-wrap:wrap}
  #updatesEdition .mast-actions form{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-right:auto}
  #updatesEdition .cp-select{background:#fff;border:1px solid rgba(255,255,255,.2);color:var(--ink);padding:7px 10px;font-family:var(--fm);font-size:11px;font-weight:700;min-width:180px}
  #updatesEdition .btn-d,#updatesEdition .btn-r,#updatesEdition .btn-a{border:none;padding:7px 14px;font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:1px;cursor:pointer;border-radius:1px;transition:all .2s;display:inline-flex;align-items:center;justify-content:center}
  #updatesEdition .btn-d{background:var(--gold2);color:var(--ink)}
  #updatesEdition .btn-d:hover{background:var(--gold3)}
  #updatesEdition .btn-r{background:transparent;border:1px solid rgba(255,255,255,.22);color:var(--paper)}
  #updatesEdition .btn-r:hover{border-color:var(--gold3);color:var(--gold3)}
  #updatesEdition .btn-a{background:var(--red);color:#fff}
  #updatesEdition .btn-a:hover{background:var(--red2)}
  #updatesEdition .ticker{background:var(--red);overflow:hidden;padding:.28rem 0;border-bottom:2px solid var(--ink)}
  #updatesEdition .t-row{display:flex;align-items:center}
  #updatesEdition .t-badge{background:var(--ink);color:var(--gold3);font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;padding:.22rem .85rem;white-space:nowrap;flex-shrink:0;border-right:2px solid var(--gold2)}
  #updatesEdition .t-track{overflow:hidden;flex:1}
  #updatesEdition .t-scroll{display:flex;gap:2.5rem;animation:cp-mq 40s linear infinite;white-space:nowrap;font-family:var(--fm);font-size:11px;color:#fff;font-weight:700;padding:.1rem 0}
  #updatesEdition .t-scroll span::before{content:'◆ ';color:var(--gold3);font-size:.8em}
  @keyframes cp-mq{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
  #updatesEdition .rbar{background:var(--paper2);border-bottom:2px solid var(--rule2);padding:.5rem 1rem;display:flex;align-items:center;gap:.85rem;overflow-x:auto;scrollbar-width:none}
  #updatesEdition .rbar::-webkit-scrollbar{display:none}
  #updatesEdition .rl,#updatesEdition .rl2,#updatesEdition .ru,#updatesEdition .rtime,#updatesEdition .live-dot{font-family:var(--fm)}
  #updatesEdition .rl{font-size:10px;font-weight:700;letter-spacing:2px;color:var(--ink3);white-space:nowrap;flex-shrink:0}
  #updatesEdition .rsep{width:1px;height:24px;background:var(--rule2);flex-shrink:0}
  #updatesEdition .rc{display:flex;align-items:center;gap:5px;padding:.25rem .6rem;background:var(--cream);border:1px solid var(--rule);border-radius:1px;white-space:nowrap;flex-shrink:0}
  #updatesEdition .rl2{font-size:10px;font-weight:700;color:var(--ink3)}
  #updatesEdition .rv{font-family:var(--fh);font-size:.95rem;font-weight:700}
  #updatesEdition .ru{font-size:9px;color:var(--ink3)}
  #updatesEdition .rch{font-family:var(--fm);font-size:9px;font-weight:700;padding:1px 4px;border-radius:1px}
  #updatesEdition .gv{color:var(--gold)}#updatesEdition .sv{color:var(--silver)}#updatesEdition .up{color:var(--green);background:rgba(22,101,52,.1)}#updatesEdition .dn{color:var(--red);background:rgba(185,28,28,.1)}
  #updatesEdition .rtime{margin-left:auto;font-size:9px;color:var(--ink3);white-space:nowrap;flex-shrink:0;display:flex;align-items:center;gap:6px}
  #updatesEdition .live-dot{display:inline-flex;align-items:center;gap:3px;font-size:9px;color:var(--green);background:rgba(22,101,52,.1);border:1px solid rgba(22,101,52,.25);padding:1px 6px;border-radius:1px}
  #updatesEdition .ld{width:5px;height:5px;border-radius:50%;background:var(--green);animation:cp-blink 1.4s ease infinite}
  @keyframes cp-blink{0%,100%{opacity:1}50%{opacity:.2}}
  #updatesEdition .pg{display:none;max-width:1320px;margin:0 auto;padding:1.25rem 1.35rem}
  #updatesEdition .pg.on{display:block}
  #updatesEdition .dbanner{display:flex;align-items:center;gap:1rem;margin-bottom:1rem}
  #updatesEdition .dline{flex:1;height:2px;background:var(--ink)}
  #updatesEdition .dtext{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;color:var(--ink);text-transform:uppercase;white-space:nowrap}
  #updatesEdition .sbar{background:var(--ink);color:var(--paper);padding:.42rem 1.1rem;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
  #updatesEdition .sbar h2{font-family:var(--fh);font-size:1rem;font-weight:700;margin:0}
  #updatesEdition .sbar em{font-family:var(--fm);font-size:10px;color:var(--gold3);letter-spacing:1px;font-style:normal}
  #updatesEdition .g3{display:grid;grid-template-columns:2fr 1fr 1fr;border:2px solid var(--ink)}
  #updatesEdition .g2{display:grid;grid-template-columns:1fr 1fr;border:1px solid var(--rule2)}
  #updatesEdition .g3e{display:grid;grid-template-columns:repeat(3,1fr);border:1px solid var(--rule2)}
  #updatesEdition .g4{display:grid;grid-template-columns:repeat(4,1fr);border:1px solid var(--rule2)}
  #updatesEdition .col{padding:1.1rem;min-width:0;word-break:break-word}
  #updatesEdition .col+.col{border-left:1px solid var(--rule2)}
  #updatesEdition .row-b{border-bottom:1px solid var(--rule2)}
  #updatesEdition .flag{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:var(--red);border-bottom:2px solid var(--red);display:inline-block;padding-bottom:1px;margin-bottom:.6rem}
  #updatesEdition .h1{font-family:var(--fh);font-size:clamp(1.55rem,2.8vw,2.4rem);font-weight:900;line-height:1.06;letter-spacing:-.8px;margin-bottom:.4rem}
  #updatesEdition .h2{font-family:var(--fh);font-size:clamp(1.1rem,1.9vw,1.65rem);font-weight:700;line-height:1.1;letter-spacing:-.4px;margin-bottom:.3rem}
  #updatesEdition .h3{font-family:var(--fh);font-size:1rem;font-weight:700;line-height:1.2;margin-bottom:.28rem}
  #updatesEdition .h4{font-family:var(--fh);font-size:.88rem;font-weight:700;line-height:1.2;margin-bottom:.2rem}
  #updatesEdition .byl{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:.8px;color:var(--ink3);margin-bottom:.45rem;text-transform:uppercase}
  #updatesEdition .byl em{color:var(--red);font-style:normal}
  #updatesEdition .body{font-family:var(--fb);font-size:.84rem;line-height:1.72;color:var(--ink2);margin-bottom:.6rem}
  #updatesEdition .body.dc::first-letter{font-family:var(--fh);font-size:3.1rem;font-weight:900;float:left;line-height:.82;padding-right:6px;padding-top:3px;color:var(--red)}
  #updatesEdition .rule2{border:none;border-top:2px solid var(--rule2);margin:.75rem 0}
  #updatesEdition .pq{border-left:4px solid var(--red);padding:.45rem .8rem;margin:.6rem 0;background:var(--paper2)}
  #updatesEdition .pq p{font-family:var(--fh);font-style:italic;font-size:.9rem;line-height:1.4;margin:0}
  #updatesEdition .pq cite{font-family:var(--fm);font-size:9px;color:var(--red);letter-spacing:1px;text-transform:uppercase;margin-top:.2rem;display:block}
  #updatesEdition .tag{display:inline-block;font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;padding:2px 7px;border-radius:1px;margin:2px;border:1px solid var(--rule2);color:var(--ink3)}
  #updatesEdition .tgn{background:var(--green);color:#fff;border-color:var(--green)}
  #updatesEdition .tg{background:var(--gold2);color:var(--ink);border-color:var(--gold2)}
  #updatesEdition .evt{display:flex;gap:9px;padding:.55rem 0;border-bottom:1px solid var(--rule)}
  #updatesEdition .evt:last-child{border-bottom:none}
  #updatesEdition .ebox{min-width:42px;text-align:center;background:var(--ink);color:var(--paper);padding:3px 5px;flex-shrink:0;border-radius:1px}
  #updatesEdition .ebox .ed{font-family:var(--fh);font-size:1rem;font-weight:900;line-height:1}
  #updatesEdition .ebox .em{font-family:var(--fm);font-size:8px;letter-spacing:1px;text-transform:uppercase;opacity:.8}
  #updatesEdition .etitle{font-family:var(--fh);font-size:.86rem;font-weight:700;margin-bottom:2px}
  #updatesEdition .emeta{font-family:var(--fm);font-size:10px;color:var(--ink3);line-height:1.4}
  #updatesEdition .jcard{padding:.7rem 0;border-bottom:1px solid var(--rule)}
  #updatesEdition .jcard:last-child{border-bottom:none}
  #updatesEdition .jtitle{font-family:var(--fh);font-size:.92rem;font-weight:700;margin-bottom:2px}
  #updatesEdition .jco{font-family:var(--fm);font-size:10px;color:var(--ink3);margin-bottom:3px}
  #updatesEdition .mc{background:var(--paper2);border:1px solid var(--rule);padding:.62rem .82rem}
  #updatesEdition .mc-l{font-family:var(--fm);font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--ink3);margin-bottom:3px}
  #updatesEdition .mc-v{font-family:var(--fh);font-size:1.15rem;font-weight:900;letter-spacing:-.4px}
  #updatesEdition .mc-s{font-family:var(--fm);font-size:9px;color:var(--ink3);margin-top:1px}
  #updatesEdition .mgrid{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-top:.55rem}
  #updatesEdition .notice{border:2px solid var(--ink);padding:.6rem;background:var(--cream);margin-top:.6rem}
  #updatesEdition .nt{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:.3rem}
  #updatesEdition .nb2{font-family:var(--fb);font-size:.8rem;line-height:1.5}
  #updatesEdition .src-badge{display:inline-flex;align-items:center;gap:3px;font-family:var(--fm);font-size:9px;color:var(--green);background:rgba(22,101,52,.08);border:1px solid rgba(22,101,52,.22);padding:1px 6px;border-radius:1px;margin-top:3px}
  #updatesEdition .hero-art{background:var(--paper3);border:1px solid var(--rule);height:220px;display:flex;align-items:center;justify-content:center;font-size:3rem;margin-bottom:.65rem;position:relative;overflow:hidden}
  #updatesEdition .hero-art img{width:100%;height:100%;object-fit:cover;display:block}
  #updatesEdition .hero-cap{position:absolute;bottom:0;left:0;right:0;background:rgba(26,18,8,.7);color:var(--paper);font-family:var(--fm);font-size:9px;padding:3px 8px;letter-spacing:.5px}
  #updatesEdition .empty{padding:1.2rem;text-align:center;color:var(--ink3);font-family:var(--fm);font-size:12px}
  #updatesEdition .dmodal{display:none;position:fixed;inset:0;background:rgba(26,18,8,.87);z-index:500;align-items:center;justify-content:center}
  #updatesEdition .dmodal.open{display:flex}
  #updatesEdition .dbox{background:var(--paper);border:3px solid var(--ink);padding:1.75rem;max-width:370px;width:90%;text-align:center}
  #updatesEdition .dbox h3{font-family:var(--fh);font-size:1.35rem;font-weight:900;margin-bottom:.45rem}
  #updatesEdition .dbox p{font-family:var(--fb);font-size:.82rem;color:var(--ink3);margin-bottom:1rem;line-height:1.6}
  #updatesEdition .dopts{display:grid;grid-template-columns:1fr 1fr;gap:9px;margin-bottom:.9rem}
  #updatesEdition .dopt{border:2px solid var(--ink);padding:.6rem;cursor:pointer;background:var(--cream);transition:all .2s}
  #updatesEdition .dopt:hover,#updatesEdition .dopt.sel{background:var(--ink);color:var(--paper)}
  #updatesEdition .dopt-ic{font-size:1.3rem;margin-bottom:.15rem}
  #updatesEdition .dopt-l{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
  #updatesEdition .btn-ok{background:var(--red);border:none;color:#fff;padding:.65rem 1.5rem;font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;cursor:pointer;width:100%;margin-bottom:.45rem;transition:background .2s}
  #updatesEdition .btn-no{background:none;border:1px solid var(--rule2);color:var(--ink3);padding:.5rem;font-family:var(--fm);font-size:11px;cursor:pointer;width:100%}
  #updatesEdition .toast{position:fixed;bottom:1.5rem;right:1.5rem;z-index:600;background:var(--ink);color:var(--paper);padding:.7rem 1.2rem;font-family:var(--fm);font-size:11px;border-radius:1px;transform:translateY(70px);opacity:0;transition:all .3s;max-width:280px;border-left:3px solid var(--gold2)}
  #updatesEdition .toast.show{transform:translateY(0);opacity:1}
  @media(max-width:980px){#updatesEdition{margin:-12px}#updatesEdition .g3{grid-template-columns:1fr}#updatesEdition .g3e{grid-template-columns:1fr}#updatesEdition .g4{grid-template-columns:1fr 1fr}#updatesEdition .col+.col{border-left:none;border-top:1px solid var(--rule2)}}
  @media(max-width:640px){#updatesEdition .g2,#updatesEdition .g4{grid-template-columns:1fr}#updatesEdition .pg{padding:.8rem}#updatesEdition .mast-actions form{width:100%}#updatesEdition .cp-select{width:100%}}
</style>

<div id="updatesEdition">
  <div class="dmodal" id="cpDlModal">
    <div class="dbox">
      <h3>Download Edition</h3>
      <p>Select format for this CityPulse daily edition.</p>
      <div class="dopts">
        <div class="dopt sel" id="cpOptPdf" onclick="cpSetFmt('pdf')"><div class="dopt-ic">📄</div><div class="dopt-l">PDF Print</div></div>
        <div class="dopt" id="cpOptImg" onclick="cpSetFmt('img')"><div class="dopt-ic">🖼️</div><div class="dopt-l">PNG Image</div></div>
      </div>
      <button class="btn-ok" onclick="cpDownloadEdition()">⬇ Download Now</button>
      <button class="btn-no" onclick="cpCloseModal()">Cancel</button>
    </div>
  </div>
  <div class="toast" id="cpToast"></div>

  <div class="mast">
    <div class="mast-meta">
      <span class="ed">VOL. CXLII · CITY EDITION</span>
      <span>{{ strtoupper($editionDate) }}</span>
      <span>{{ strtoupper($cityEdition) }} NEWS DESK</span>
      <span>UPDATES MODULE</span>
    </div>
    <div class="mast-hero">
      <div class="mast-rule"></div>
      <div class="mast-name">CityPulse</div>
      <div class="mast-strap">Dynamic Local Edition from /updates</div>
    </div>
    <div class="mast-nav">
      <div class="nb on" data-page="front" onclick="cpGoPage('front', this)">Front Page</div>
      <div class="nb" data-page="news" onclick="cpGoPage('news', this)">Latest News</div>
      <div class="nb" data-page="events" onclick="cpGoPage('events', this)">Events</div>
      <div class="nb" data-page="jobs" onclick="cpGoPage('jobs', this)">Jobs</div>
      <div class="nb" data-page="markets" onclick="cpGoPage('markets', this)">Markets</div>
      <div class="nb" data-page="opinion" onclick="cpGoPage('opinion', this)">Opinion</div>
    </div>
    <div class="mast-actions">
      <form method="GET" action="{{ route('updates.index') }}">
        <select class="cp-select" name="city_id" onchange="this.form.submit()">
          <option value="">All Cities Edition</option>
          @foreach($cities as $city)
            <option value="{{ $city->id }}" {{ (int) ($selectedCity?->id ?? 0) === (int) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
          @endforeach
        </select>
      </form>
      <button class="btn-r" type="button" onclick="window.location.reload()">↻ Refresh</button>
      @auth
        @if(auth()->user()->isSuperadmin())
          <a class="btn-a" href="{{ route('updates.create') }}">+ New Update</a>
        @endif
      @endauth
      <button class="btn-d" type="button" onclick="document.getElementById('cpDlModal').classList.add('open')">⬇ Download Edition</button>
    </div>
  </div>

  <div class="ticker">
    <div class="t-row">
      <div class="t-badge">LIVE</div>
      <div class="t-track">
        <div class="t-scroll">
          @foreach($tickerItems->concat($tickerItems) as $ticker)
            <span>{{ $ticker }}</span>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="rbar">
    <span class="rl">Live Bar</span>
    <div class="rsep"></div>
    <div class="rc"><span class="rl2">🥇 GOLD 24K</span><span class="rv gv" id="cpGold">₹85,900</span><span class="ru">/10g</span><span class="rch up" id="cpGoldC">+₹320</span></div>
    <div class="rc"><span class="rl2">🥈 SILVER</span><span class="rv sv" id="cpSilver">₹96,500</span><span class="ru">/kg</span><span class="rch up" id="cpSilverC">+₹740</span></div>
    <div class="rsep"></div>
    <div class="rc"><span class="rl2">💵 USD</span><span class="rv" id="cpUsd">₹84.10</span><span class="ru">INR</span></div>
    <div class="rc"><span class="rl2">🌡 {{ strtoupper($cityEdition) }}</span><span class="rv" id="cpTemp">32</span><span class="ru">°C</span></div>
    <div class="rc"><span class="rl2">⛅</span><span class="rv" id="cpWx" style="font-size:.78rem">Partly Cloudy</span></div>
    <div class="rtime">Updated: <span id="cpUpd">{{ now()->format('h:i A') }}</span><span class="live-dot"><span class="ld"></span>LIVE</span></div>
  </div>

  <main class="pg on" id="cpPgFront">
    <div class="dbanner"><div class="dline"></div><div class="dtext">{{ strtoupper($editionDate) }}</div><div class="dline"></div></div>
    <div class="g3">
      <div class="col">
        <div class="flag">Top Story</div>
        <h1 class="h1">{{ $leadStory?->title ?: 'Fresh city stories will appear here once updates are published.' }}</h1>
        <div class="byl"><em>{{ $leadStory?->city?->name ?: $cityEdition }}</em> · {{ optional($leadStory?->published_at ?: $leadStory?->created_at)->format('d M Y, h:i A') ?: 'Live edition' }}</div>
        <div class="hero-art">
          @if($leadPhoto)
            <img src="{{ $leadPhoto }}" alt="{{ $leadStory->title }}">
          @else
            📰
          @endif
          <div class="hero-cap">{{ $leadStory?->update_type ? strtoupper($leadStory->update_type) : 'NEWS' }} · CITYPULSE DESK</div>
        </div>
        <p class="body dc">{{ \Illuminate\Support\Str::limit(strip_tags($leadStory?->content ?: 'Use the Updates module to publish breaking alerts, civic stories, offers, and city happenings. This edition automatically reshapes those entries into a newspaper-style front page.'), 520) }}</p>
        <a href="{{ $leadLink }}" target="{{ $leadStory && $leadStory->source_url ? '_blank' : '_self' }}" class="byl" style="color:var(--red)">Continue Reading →</a>
        @if($secondaryStories->isNotEmpty())
          <hr class="rule2">
          <div class="flag">Also</div>
          @foreach($secondaryStories as $story)
            <div style="margin-bottom:.7rem">
              <h3 class="h3"><a href="{{ $story->source_url ?: route('updates.show', $story) }}" style="color:inherit">{{ $story->title }}</a></h3>
              <p class="body" style="font-size:.8rem;margin-bottom:0">{{ \Illuminate\Support\Str::limit(strip_tags($story->content ?: 'Open the full update for complete details.'), 120) }}</p>
            </div>
          @endforeach
        @endif
      </div>

      <div class="col">
        <div class="flag">Edition Snapshot</div>
        <h3 class="h3">{{ $cityEdition }} in Brief</h3>
        <div class="src-badge"><span class="ld"></span>Dynamic module data</div>
        <div class="mgrid">
          <div class="mc"><div class="mc-l">Published Updates</div><div class="mc-v">{{ $updates->count() }}</div><div class="mc-s">Current edition</div></div>
          <div class="mc"><div class="mc-l">Events</div><div class="mc-v">{{ $eventUpdates->count() }}</div><div class="mc-s">Local happenings</div></div>
          <div class="mc"><div class="mc-l">Jobs</div><div class="mc-v">{{ $jobs->count() }}</div><div class="mc-s">Open roles</div></div>
          <div class="mc"><div class="mc-l">Markets</div><div class="mc-v">{{ max($marketRows->count(), $marketIndices ? count($marketIndices) : 0) }}</div><div class="mc-s">Tracked signals</div></div>
        </div>
        <hr class="rule2">
        <div class="flag">Market Watch</div>
        @if(!empty($marketIndices))
          @foreach(array_slice($marketIndices, 0, 4) as $index)
            <div class="mc" style="margin-bottom:7px">
              <div class="mc-l">{{ $index['name'] }}</div>
              <div class="mc-v" style="font-size:1rem">{{ number_format((float) $index['price'], 2) }}</div>
              <div class="mc-s" style="color:{{ (float) $index['change'] >= 0 ? 'var(--green)' : 'var(--red)' }}">{{ (float) $index['change'] >= 0 ? '+' : '' }}{{ number_format((float) $index['change'], 2) }}%</div>
            </div>
          @endforeach
        @else
          <div class="empty">Market indices unavailable right now.</div>
        @endif
      </div>

      <div class="col">
        <div class="flag">Upcoming Events</div>
        @forelse($frontEvents as $event)
          @php $eventDate = $event->published_at ?: $event->created_at; @endphp
          <div class="evt">
            <div class="ebox"><div class="ed">{{ $eventDate->format('d') }}</div><div class="em">{{ strtoupper($eventDate->format('M')) }}</div></div>
            <div>
              <div class="etitle"><a href="{{ $event->source_url ?: route('updates.show', $event) }}" style="color:inherit">{{ $event->title }}</a></div>
              <div class="emeta">📍 {{ $event->city?->name ?: $cityEdition }}</div>
            </div>
          </div>
        @empty
          <div class="empty">No events published for this edition yet.</div>
        @endforelse
        <hr class="rule2">
        <div class="flag">Jobs Today</div>
        @forelse($frontJobs as $job)
          <div class="jcard">
            <div class="jtitle"><a href="{{ $job->external_url ?: route('jobs.show', $job) }}" style="color:inherit">{{ $job->title }}</a></div>
            <div class="jco">{{ $job->company ?: 'CityPulse Network' }} · {{ $job->location ?: ($job->city?->name ?: $cityEdition) }}</div>
            <span class="tag tgn">Hiring</span>
          </div>
        @empty
          <div class="empty">No jobs available right now.</div>
        @endforelse
      </div>
    </div>

    <div style="margin-top:1.2rem">
      <div class="sbar"><h2>Latest from Around the Web</h2><em>{{ $selectedCity?->name ? $selectedCity->name . ' Google News' : 'External feeds when city selected' }}</em></div>
      <div class="g4">
        @forelse(array_slice($cityNews, 0, 4) as $item)
          <div class="col">
            <div class="flag">External</div>
            <h3 class="h3"><a href="{{ $item['link'] ?? '#' }}" target="_blank" rel="noopener" style="color:inherit">{{ $item['title'] ?? 'Untitled story' }}</a></h3>
            <div class="byl"><em>{{ $item['source'] ?? 'News' }}</em> · {{ $item['pubDate'] ?? 'Live feed' }}</div>
          </div>
        @empty
          <div class="col empty" style="grid-column:1/-1">Select a city to load external local news headlines.</div>
        @endforelse
      </div>
    </div>

    <div style="margin-top:1.2rem">
      <div class="sbar"><h2>Markets &amp; Opinion</h2><em>Local data &amp; editorial highlights</em></div>
      <div class="g2">
        <div class="col">
          <div class="flag">Markets Snapshot</div>
          @if($marketRows->isNotEmpty())
            <div class="mgrid">
              @foreach($marketRows->take(4) as $market)
                <div class="mc">
                  <div class="mc-l">{{ $market->commodity ?: 'Commodity' }}</div>
                  <div class="mc-v">₹{{ number_format((float) ($market->modal_price ?: $market->rate ?: 0), 0) }}</div>
                  <div class="mc-s">{{ $market->city_rel?->name ?: $market->city ?: $cityEdition }} · {{ optional($market->price_date)->format('d M Y') ?: 'Latest' }}</div>
                </div>
              @endforeach
            </div>
          @else
            <div class="empty">No local commodity entries available.</div>
          @endif
        </div>
        <div class="col">
          <div class="flag">Editorial Pick</div>
          @php $editorial = $editorials->first(); @endphp
          <h3 class="h3">{{ $editorial?->title ?: 'Why dynamic local publishing matters' }}</h3>
          <div class="byl"><em>{{ $editorial?->city?->name ?: 'CityPulse Desk' }}</em></div>
          <p class="body">{{ \Illuminate\Support\Str::limit(strip_tags($editorial?->content ?: 'This edition is driven directly from the Updates module, letting your published content flow into a richer newspaper-style experience without manual static edits.'), 280) }}</p>
          <div class="pq"><p>“Local information becomes more valuable when it is readable, timely, and organized like a real daily edition.”</p><cite>— CityPulse Editorial Board</cite></div>
        </div>
      </div>
    </div>
  </main>

  <main class="pg" id="cpPgNews">
    <div class="dbanner"><div class="dline"></div><div class="dtext">Latest News — Dynamic Feed</div><div class="dline"></div></div>
    <div class="sbar"><h2>Top Stories from Updates</h2><em>{{ $cityEdition }} edition</em></div>
    <div class="g2">
      @forelse($newsGridItems as $story)
        <div class="col row-b">
          <div class="flag">{{ strtoupper($story->update_type ?: 'general') }}</div>
          <h3 class="h2"><a href="{{ $story->source_url ?: route('updates.show', $story) }}" style="color:inherit">{{ $story->title }}</a></h3>
          <div class="byl"><em>{{ $story->city?->name ?: $cityEdition }}</em> · {{ optional($story->published_at ?: $story->created_at)->format('d M Y, h:i A') }}</div>
          <p class="body">{{ \Illuminate\Support\Str::limit(strip_tags($story->content ?: 'Open the story for complete content.'), 240) }}</p>
        </div>
      @empty
        <div class="col empty" style="grid-column:1/-1">No published updates yet.</div>
      @endforelse
    </div>

    <div style="margin-top:1.2rem">
      <div class="sbar"><h2>City Feed</h2><em>Google News via service integration</em></div>
      <div class="g3e">
        @forelse(array_slice($cityNews, 0, 3) as $item)
          <div class="col">
            <div class="flag">External</div>
            <h3 class="h3"><a href="{{ $item['link'] ?? '#' }}" target="_blank" rel="noopener" style="color:inherit">{{ $item['title'] ?? 'Untitled story' }}</a></h3>
            <div class="byl"><em>{{ $item['source'] ?? 'News' }}</em> · {{ $item['pubDate'] ?? 'Live' }}</div>
          </div>
        @empty
          <div class="col empty" style="grid-column:1/-1">No external city headlines loaded.</div>
        @endforelse
      </div>
    </div>

    <div style="margin-top:1.2rem">
      <div class="sbar"><h2>Offers &amp; Alerts</h2><em>Published from Updates</em></div>
      <div class="g4">
        @forelse($offerUpdates->take(4) as $offer)
          <div class="col">
            <div class="flag">Offer</div>
            <h4 class="h4"><a href="{{ $offer->source_url ?: route('updates.show', $offer) }}" style="color:inherit">{{ $offer->title }}</a></h4>
            <p class="body" style="font-size:.78rem">{{ \Illuminate\Support\Str::limit(strip_tags($offer->content ?: 'Open the update for full offer details.'), 140) }}</p>
          </div>
        @empty
          <div class="col empty" style="grid-column:1/-1">No offer updates available.</div>
        @endforelse
      </div>
    </div>
  </main>

  <main class="pg" id="cpPgEvents">
    <div class="dbanner"><div class="dline"></div><div class="dtext">Events &amp; Happenings</div><div class="dline"></div></div>
    <div class="sbar"><h2>City Events Calendar</h2><em>{{ $cityEdition }}</em></div>
    <div class="g2">
      @forelse($eventUpdates as $event)
        @php $eventDate = $event->published_at ?: $event->created_at; @endphp
        <div class="col row-b">
          <div class="evt" style="border:none;padding:0;margin-bottom:.6rem">
            <div class="ebox"><div class="ed">{{ $eventDate->format('d') }}</div><div class="em">{{ strtoupper($eventDate->format('M')) }}</div></div>
            <div><div class="etitle"><a href="{{ $event->source_url ?: route('updates.show', $event) }}" style="color:inherit">{{ $event->title }}</a></div><div class="emeta">📍 {{ $event->city?->name ?: $cityEdition }}</div></div>
          </div>
          <p class="body" style="font-size:.81rem">{{ \Illuminate\Support\Str::limit(strip_tags($event->content ?: 'Open this event update to read complete details.'), 220) }}</p>
          <span class="tag tg">Event</span>
        </div>
      @empty
        <div class="col empty" style="grid-column:1/-1">No event entries published yet.</div>
      @endforelse
    </div>
  </main>

  <main class="pg" id="cpPgJobs">
    <div class="dbanner"><div class="dline"></div><div class="dtext">Employment — Open Positions</div><div class="dline"></div></div>
    <div class="sbar"><h2>Jobs Feed</h2><em>Dynamic jobs module data</em></div>
    <div class="g3e">
      @forelse($jobs as $job)
        <div class="col">
          <div class="flag">{{ $job->city?->name ?: $cityEdition }}</div>
          <div class="jtitle"><a href="{{ $job->external_url ?: route('jobs.show', $job) }}" style="color:inherit">{{ $job->title }}</a></div>
          <div class="jco">🏢 {{ $job->company ?: 'Employer not specified' }}</div>
          <p class="body" style="font-size:.79rem">{{ \Illuminate\Support\Str::limit(strip_tags($job->description ?: 'Open the job for complete details.'), 180) }}</p>
          @if($job->category)
            <span class="tag">{{ $job->category }}</span>
          @endif
          <div style="margin-top:.45rem"><a href="{{ $job->external_url ?: route('jobs.show', $job) }}" class="byl" style="color:var(--red)">Apply →</a></div>
        </div>
      @empty
        <div class="col empty" style="grid-column:1/-1">No jobs available right now.</div>
      @endforelse
    </div>
  </main>

  <main class="pg" id="cpPgMarkets">
    <div class="dbanner"><div class="dline"></div><div class="dtext">Markets &amp; Commodities</div><div class="dline"></div></div>
    <div class="g2">
      <div class="col">
        <div class="flag">Indian Indices</div>
        <h2 class="h2">Market Dashboard</h2>
        <div class="src-badge"><span class="ld"></span>Yahoo Finance via server service</div>
        <div class="mgrid">
          @forelse($marketIndices as $index)
            <div class="mc">
              <div class="mc-l">{{ $index['name'] }}</div>
              <div class="mc-v">{{ number_format((float) $index['price'], 2) }}</div>
              <div class="mc-s" style="color:{{ (float) $index['change'] >= 0 ? 'var(--green)' : 'var(--red)' }}">{{ (float) $index['change'] >= 0 ? '+' : '' }}{{ number_format((float) $index['change'], 2) }}%</div>
            </div>
          @empty
            <div class="empty" style="grid-column:1/-1">Live index data unavailable.</div>
          @endforelse
        </div>
      </div>
      <div class="col">
        <div class="flag">Local Commodity Rates</div>
        <h2 class="h2">{{ $cityEdition }} Market Entries</h2>
        @if($marketRows->isNotEmpty())
          <table style="width:100%;border-collapse:collapse;font-size:.81rem;margin-top:.45rem">
            <thead>
              <tr style="background:var(--ink);color:var(--paper)">
                <th style="padding:5px 9px;text-align:left;font-family:var(--fm);font-size:10px">Commodity</th>
                <th style="padding:5px 9px;text-align:right;font-family:var(--fm);font-size:10px">Modal</th>
                <th style="padding:5px 9px;text-align:right;font-family:var(--fm);font-size:10px">Date</th>
              </tr>
            </thead>
            <tbody>
              @foreach($marketRows as $market)
                <tr style="background:{{ $loop->odd ? 'var(--cream)' : 'var(--paper)' }}">
                  <td style="padding:5px 9px">{{ $market->commodity ?: 'Commodity' }}</td>
                  <td style="padding:5px 9px;text-align:right;font-family:var(--fm)">₹{{ number_format((float) ($market->modal_price ?: $market->rate ?: 0), 0) }}</td>
                  <td style="padding:5px 9px;text-align:right;font-family:var(--fm)">{{ optional($market->price_date)->format('d M') ?: 'Latest' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div class="empty">No commodity rows available.</div>
        @endif
        <div class="notice"><div class="nt">Edition Note</div><p class="nb2">This page combines live service data and your own stored updates, jobs, and market records into one newspaper-style Updates edition.</p></div>
      </div>
    </div>
  </main>

  <main class="pg" id="cpPgOpinion">
    <div class="dbanner"><div class="dline"></div><div class="dtext">Opinion &amp; Analysis</div><div class="dline"></div></div>
    <div class="sbar"><h2>Editorial &amp; Columns</h2><em>Generated from published update content</em></div>
    <div class="g3e">
      @forelse($editorials as $story)
        <div class="col">
          <div class="flag">{{ strtoupper($story->update_type ?: 'general') }}</div>
          <h2 class="h2" style="font-size:1.25rem"><a href="{{ $story->source_url ?: route('updates.show', $story) }}" style="color:inherit">{{ $story->title }}</a></h2>
          <div class="byl">By <em>{{ $story->city?->name ?: 'CityPulse Desk' }}</em></div>
          <p class="body dc">{{ \Illuminate\Support\Str::limit(strip_tags($story->content ?: 'Publish long-form updates to turn them into editorial-style reading sections.'), 340) }}</p>
          <div class="pq"><p>“{{ \Illuminate\Support\Str::limit(strip_tags($story->content ?: $story->title), 110) }}”</p><cite>{{ $story->city?->name ?: 'CityPulse' }}</cite></div>
        </div>
      @empty
        <div class="col empty" style="grid-column:1/-1">No editorial-style updates available.</div>
      @endforelse
    </div>
  </main>
</div>

<script>
  let cpDownloadFormat = 'pdf';

  function cpEl(id){ return document.getElementById(id); }
  function cpToast(message, delay = 3000){
    const node = cpEl('cpToast');
    if(!node) return;
    node.textContent = message;
    node.classList.add('show');
    setTimeout(() => node.classList.remove('show'), delay);
  }
  function cpActivatePage(id, elem){
    document.querySelectorAll('#updatesEdition .pg').forEach(page => page.classList.remove('on'));
    document.querySelectorAll('#updatesEdition .nb').forEach(tab => tab.classList.remove('on'));
    const page = cpEl('cpPg' + id.charAt(0).toUpperCase() + id.slice(1));
    if(page) page.classList.add('on');
    if(elem) elem.classList.add('on');
  }
  function cpGoPage(id, elem){
    cpActivatePage(id, elem);
    if(window.location.hash !== '#' + id){
      history.replaceState(null, '', '#' + id);
    }
    window.scrollTo({ top: document.getElementById('updatesEdition').offsetTop, behavior: 'smooth' });
  }
  function cpSetFmt(format){
    cpDownloadFormat = format;
    cpEl('cpOptPdf').classList.toggle('sel', format === 'pdf');
    cpEl('cpOptImg').classList.toggle('sel', format === 'img');
  }
  function cpCloseModal(){ cpEl('cpDlModal').classList.remove('open'); }
  cpEl('cpDlModal')?.addEventListener('click', function(event){ if(event.target === this) cpCloseModal(); });
  function cpDownloadEdition(){
    cpCloseModal();
    cpToast('Preparing edition download…', 7000);
    if(typeof html2canvas === 'undefined'){
      cpToast('Download library not loaded.');
      return;
    }
    html2canvas(document.getElementById('updatesEdition'), {
      scale: 1.4,
      useCORS: true,
      backgroundColor: '#f8f3e8',
      ignoreElements: (element) => element.classList.contains('toast') || element.classList.contains('dmodal')
    }).then(canvas => {
      if(cpDownloadFormat === 'img'){
        const link = document.createElement('a');
        link.download = 'citypulse-updates-edition.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        cpToast('Edition image downloaded.');
        return;
      }
      const preview = window.open('', '_blank');
      if(!preview){
        cpToast('Allow pop-ups to print PDF edition.');
        return;
      }
      const img = canvas.toDataURL('image/png');
      preview.document.write('<!DOCTYPE html><html><head><title>CityPulse Edition</title><style>*{margin:0;padding:0}body{background:#f8f3e8}img{max-width:100%;display:block}</style></head><body><img src="' + img + '"></body></html>');
      preview.document.close();
      setTimeout(() => preview.print(), 700);
      cpToast('Print dialog ready.');
    }).catch(() => cpToast('Edition download failed.'));
  }

  async function cpRefreshLiveBar(){
    try {
      const weatherResponse = await fetch('https://api.open-meteo.com/v1/forecast?latitude=18.5204&longitude=73.8567&current=temperature_2m,weather_code&timezone=Asia%2FKolkata');
      const weatherData = await weatherResponse.json();
      if(weatherData?.current){
        cpEl('cpTemp').textContent = Math.round(weatherData.current.temperature_2m);
        const weatherText = {0:'Clear',1:'Mostly Clear',2:'Partly Cloudy',3:'Overcast',45:'Foggy',61:'Rain',63:'Rain',80:'Showers',95:'Storm'}[weatherData.current.weather_code] || 'Partly Cloudy';
        cpEl('cpWx').textContent = weatherText;
      }
    } catch (error) {}

    try {
      const fxResponse = await fetch('https://api.frankfurter.dev/v1/latest?base=USD&symbols=INR');
      const fxData = await fxResponse.json();
      const usdRate = fxData?.rates?.INR ? Number(fxData.rates.INR) : 84.10;
      cpEl('cpUsd').textContent = '₹' + usdRate.toFixed(2);

      let goldPerOz = 3180;
      let silverPerOz = 33.5;
      try {
        const goldResponse = await fetch('https://api.frankfurter.dev/v1/latest?base=XAU&symbols=USD');
        const goldData = await goldResponse.json();
        if(goldData?.rates?.USD){ goldPerOz = Number(goldData.rates.USD); }
      } catch (error) {}
      try {
        const silverResponse = await fetch('https://api.frankfurter.dev/v1/latest?base=XAG&symbols=USD');
        const silverData = await silverResponse.json();
        if(silverData?.rates?.USD){ silverPerOz = Number(silverData.rates.USD); }
      } catch (error) {}

      const goldInr = Math.round((goldPerOz / 31.1035) * 10 * usdRate);
      const silverInr = Math.round((silverPerOz / 31.1035) * 1000 * usdRate);
      cpEl('cpGold').textContent = '₹' + goldInr.toLocaleString('en-IN');
      cpEl('cpSilver').textContent = '₹' + silverInr.toLocaleString('en-IN');
      cpEl('cpGoldC').textContent = '+₹' + Math.max(120, Math.round(goldInr * 0.003)).toLocaleString('en-IN');
      cpEl('cpSilverC').textContent = '+₹' + Math.max(240, Math.round(silverInr * 0.005)).toLocaleString('en-IN');
    } catch (error) {}

    cpEl('cpUpd').textContent = new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
  }

  function cpOpenHashPage(){
    const validPages = ['front', 'news', 'events', 'jobs', 'markets', 'opinion'];
    const targetPage = (window.location.hash || '#front').replace('#', '');
    const pageKey = validPages.includes(targetPage) ? targetPage : 'front';
    const tab = document.querySelector('#updatesEdition .nb[data-page="' + pageKey + '"]');
    cpActivatePage(pageKey, tab);
  }

  cpOpenHashPage();
  window.addEventListener('hashchange', cpOpenHashPage);
  cpRefreshLiveBar();
  setInterval(cpRefreshLiveBar, 300000);
</script>
@endsection
