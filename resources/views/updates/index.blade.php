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
      __('ui.upd_ticker_fallback_1'),
      __('ui.upd_ticker_fallback_2'),
      __('ui.upd_ticker_fallback_3'),
  ]);
  $editionDate = now()->format('l, d F Y');
  $cityEdition = $selectedCity?->name ? city_display_name($selectedCity->name) : __('ui.all_cities');
  $leadLink = $leadStory ? (((!empty($leadStory->source_url) && $leadStory->source_url !== '#') ? $leadStory->source_url : route('updates.show', $leadStory))) : '#';
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
      <h3>{{ __('ui.upd_download_edition') }}</h3>
      <p>{{ __('ui.upd_select_format') }}</p>
      <div class="dopts">
        <div class="dopt sel" id="cpOptPdf" onclick="cpSetFmt('pdf')"><div class="dopt-ic">📄</div><div class="dopt-l">{{ __('ui.upd_pdf_print') }}</div></div>
        <div class="dopt" id="cpOptImg" onclick="cpSetFmt('img')"><div class="dopt-ic">🖼️</div><div class="dopt-l">{{ __('ui.upd_png_image') }}</div></div>
      </div>
      <button class="btn-ok" onclick="cpDownloadEdition()">⬇ {{ __('ui.upd_download_now') }}</button>
      <button class="btn-no" onclick="cpCloseModal()">{{ __('ui.cancel') }}</button>
    </div>
  </div>
  <div class="toast" id="cpToast"></div>

  <div class="mast">
    <div class="mast-meta">
      <span class="ed">VOL. CXLII · CITY EDITION</span>
      <span>{{ strtoupper($editionDate) }}</span>
      <span>{{ strtoupper($cityEdition) }} {{ __('ui.upd_news_desk') }}</span>
      <span>{{ __('ui.upd_updates_module') }}</span>
    </div>
    <div class="mast-hero">
      <div class="mast-rule"></div>
      <div class="mast-name">CityPulse</div>
      <div class="mast-strap">{{ __('ui.upd_mast_strap') }}</div>
    </div>
    <div class="mast-nav">
      <div class="nb on" data-page="front" onclick="cpGoPage('front', this)">{{ __('ui.upd_front_page') }}</div>
      <div class="nb" data-page="news" onclick="cpGoPage('news', this)">{{ __('ui.upd_latest_news') }}</div>
      <div class="nb" data-page="events" onclick="cpGoPage('events', this)">{{ __('ui.upd_events') }}</div>
      <div class="nb" data-page="jobs" onclick="cpGoPage('jobs', this)">{{ __('ui.upd_jobs') }}</div>
      <div class="nb" data-page="markets" onclick="cpGoPage('markets', this)">{{ __('ui.upd_markets') }}</div>
      <div class="nb" data-page="opinion" onclick="cpGoPage('opinion', this)">{{ __('ui.upd_opinion') }}</div>
    </div>
    <div class="mast-actions">
      <form method="GET" action="{{ route('updates.index') }}">
        <select class="cp-select" name="city_id" onchange="this.form.submit()">
          <option value="">{{ __('ui.upd_all_cities_edition') }}</option>
          @foreach($cities as $city)
            <option value="{{ $city->id }}" {{ (int) ($selectedCity?->id ?? 0) === (int) $city->id ? 'selected' : '' }}>{{ city_display_name($city->name) }}</option>
          @endforeach
        </select>
      </form>
      <button class="btn-r" type="button" onclick="window.location.reload()">↻ {{ __('ui.refresh') }}</button>
      @auth
        @if(auth()->user()->isSuperadmin())
          <a class="btn-a" href="{{ route('updates.create') }}">+ {{ __('ui.upd_new_update') }}</a>
        @endif
      @endauth
      <a class="btn-a" href="{{ route('user_submissions.create') }}">+ {{ __('ui.send_news') }}</a>
      <button class="btn-d" type="button" onclick="document.getElementById('cpDlModal').classList.add('open')">⬇ {{ __('ui.upd_download_edition') }}</button>
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
    <span class="rl">{{ __('ui.upd_live_bar') }}</span>
    <div class="rsep"></div>
    <div class="rc"><span class="rl2">🥇 GOLD 24K</span><span class="rv gv" id="cpGold">₹85,900</span><span class="ru">/10g</span><span class="rch up" id="cpGoldC">+₹320</span></div>
    <div class="rc"><span class="rl2">🥈 SILVER</span><span class="rv sv" id="cpSilver">₹96,500</span><span class="ru">/kg</span><span class="rch up" id="cpSilverC">+₹740</span></div>
    <div class="rsep"></div>
    <div class="rc"><span class="rl2">💵 USD</span><span class="rv" id="cpUsd">₹84.10</span><span class="ru">INR</span></div>
    <div class="rc"><span class="rl2">🌡 {{ strtoupper($cityEdition) }}</span><span class="rv" id="cpTemp">32</span><span class="ru">°C</span></div>
    <div class="rc"><span class="rl2">⛅</span><span class="rv" id="cpWx" style="font-size:.78rem">{{ __('ui.weather_partly_cloudy') }}</span></div>
    <div class="rtime">{{ __('ui.upd_updated_label') }}: <span id="cpUpd">{{ now()->format('h:i A') }}</span><span class="live-dot"><span class="ld"></span>{{ __('ui.upd_live_label') }}</span></div>
  </div>

  <main class="pg on" id="cpPgFront">
    <div class="dbanner"><div class="dline"></div><div class="dtext">{{ strtoupper($editionDate) }}</div><div class="dline"></div></div>
    <div class="g3">
      <div class="col">
        <div class="flag">{{ __('ui.upd_top_story') }}</div>
        <h1 class="h1">{{ $leadStory?->title ?: __('ui.upd_fresh_city_stories') }}</h1>
        <div class="byl"><em>{{ $leadStory?->city?->name ?: $cityEdition }}</em> · {{ optional($leadStory?->published_at ?: $leadStory?->created_at)->format('d M Y, h:i A') ?: __('ui.upd_live_edition') }}</div>
        <div class="hero-art">
          @if($leadPhoto)
            <img src="{{ $leadPhoto }}" alt="{{ $leadStory->title }}">
          @else
            📰
          @endif
          <div class="hero-cap">{{ $leadStory?->update_type ? strtoupper($leadStory->update_type) : __('ui.upd_news') }} · {{ __('ui.upd_citypulse_desk') }}</div>
        </div>
        <p class="body dc">{{ \Illuminate\Support\Str::limit(strip_tags($leadStory?->content ?: __('ui.upd_publish_updates_note')), 520) }}</p>
        <a href="{{ $leadLink }}" target="{{ $leadStory && !empty($leadStory->source_url) && $leadStory->source_url !== '#' ? '_blank' : '_self' }}" class="byl" style="color:var(--red)">{{ __('ui.upd_continue_reading') }} →</a>
        @if($secondaryStories->isNotEmpty())
          <hr class="rule2">
          <div class="flag">{{ __('ui.upd_also') }}</div>
          @foreach($secondaryStories as $story)
            <div style="margin-bottom:.7rem">
              <h3 class="h3"><a href="{{ (!empty($story->source_url) && $story->source_url !== '#') ? $story->source_url : route('updates.show', $story) }}" style="color:inherit">{{ $story->title }}</a></h3>
                <p class="body" style="font-size:.8rem;margin-bottom:0">{{ \Illuminate\Support\Str::limit(strip_tags($story->content ?: __('ui.upd_open_full_update')), 120) }}</p>
            </div>
          @endforeach
        @endif
      </div>

      <div class="col">
        <div class="flag">{{ __('ui.upd_edition_snapshot') }}</div>
        <h3 class="h3">{{ $cityEdition }} {{ __('ui.upd_in_brief') }}</h3>
        <div class="src-badge"><span class="ld"></span>{{ __('ui.upd_dynamic_module_data') }}</div>
        <div class="mgrid">
          <div class="mc"><div class="mc-l">{{ __('ui.upd_published_updates') }}</div><div class="mc-v">{{ $updates->count() }}</div><div class="mc-s">{{ __('ui.upd_current_edition') }}</div></div>
          <div class="mc"><div class="mc-l">{{ __('ui.upd_events') }}</div><div class="mc-v">{{ $eventUpdates->count() }}</div><div class="mc-s">{{ __('ui.upd_local_happenings') }}</div></div>
          <div class="mc"><div class="mc-l">{{ __('ui.upd_jobs') }}</div><div class="mc-v">{{ $jobs->count() }}</div><div class="mc-s">{{ __('ui.upd_open_roles') }}</div></div>
          <div class="mc"><div class="mc-l">{{ __('ui.upd_markets') }}</div><div class="mc-v">{{ max($marketRows->count(), $marketIndices ? count($marketIndices) : 0) }}</div><div class="mc-s">{{ __('ui.upd_tracked_signals') }}</div></div>
        </div>
        <hr class="rule2">
        <div class="flag">{{ __('ui.upd_market_watch') }}</div>
        @if(!empty($marketIndices))
          @foreach(array_slice($marketIndices, 0, 4) as $index)
            <div class="mc" style="margin-bottom:7px">
              <div class="mc-l">{{ $index['name'] }}</div>
              <div class="mc-v" style="font-size:1rem">{{ number_format((float) $index['price'], 2) }}</div>
              <div class="mc-s" style="color:{{ (float) $index['change'] >= 0 ? 'var(--green)' : 'var(--red)' }}">{{ (float) $index['change'] >= 0 ? '+' : '' }}{{ number_format((float) $index['change'], 2) }}%</div>
            </div>
          @endforeach
        @else
          <div class="empty">{{ __('ui.upd_market_indices_unavailable') }}</div>
        @endif
      </div>

      <div class="col">
        <div class="flag">{{ __('ui.upd_upcoming_events') }}</div>
        @forelse($frontEvents as $event)
          @php $eventDate = $event->published_at ?: $event->created_at; @endphp
          <div class="evt">
            <div class="ebox"><div class="ed">{{ $eventDate->format('d') }}</div><div class="em">{{ strtoupper($eventDate->format('M')) }}</div></div>
            <div>
              <div class="etitle"><a href="{{ (!empty($event->source_url) && $event->source_url !== '#') ? $event->source_url : route('updates.show', $event) }}" style="color:inherit">{{ $event->title }}</a></div>
              <div class="emeta">📍 {{ $event->city?->name ?: $cityEdition }}</div>
            </div>
          </div>
        @empty
          <div class="empty">{{ __('ui.upd_no_events') }}</div>
        @endforelse
        <hr class="rule2">
        <div class="flag">{{ __('ui.upd_jobs_today') }}</div>
        @forelse($frontJobs as $job)
          <div class="jcard">
            <div class="jtitle"><a href="{{ $job->external_url ?: route('jobs.show', $job) }}" style="color:inherit">{{ $job->title }}</a></div>
            <div class="jco">{{ $job->company ?: __('ui.upd_citypulse_network') }} · {{ $job->location ?: ($job->city?->name ?: $cityEdition) }}</div>
            <span class="tag tgn">{{ __('ui.upd_hiring') }}</span>
          </div>
        @empty
          <div class="empty">{{ __('ui.upd_no_jobs') }}</div>
        @endforelse
      </div>
    </div>

    <div style="margin-top:1.2rem">
      <div class="sbar"><h2>{{ __('ui.upd_latest_from_web') }}</h2><em>{{ $selectedCity?->name ? city_display_name($selectedCity->name) . ' Google News' : __('ui.upd_external_feeds_hint') }}</em></div>
      <div class="g4">
        @forelse(array_slice($cityNews, 0, 4) as $item)
          <div class="col">
            <div class="flag">{{ __('ui.upd_external') }}</div>
            <h3 class="h3"><a href="{{ $item['link'] ?? '#' }}" target="_blank" rel="noopener" style="color:inherit">{{ $item['title'] ?? __('ui.upd_untitled_story') }}</a></h3>
            <div class="byl"><em>{{ $item['source'] ?? __('ui.upd_news_source') }}</em> · {{ $item['pubDate'] ?? __('ui.upd_live_feed') }}</div>
          </div>
        @empty
          <div class="col empty" style="grid-column:1/-1">{{ __('ui.upd_select_city_headlines') }}</div>
        @endforelse
      </div>
    </div>

    <div style="margin-top:1.2rem">
      <div class="sbar"><h2>{{ __('ui.upd_markets_opinion') }}</h2><em>{{ __('ui.upd_local_data_editorial') }}</em></div>
      <div class="g2">
        <div class="col">
          <div class="flag">{{ __('ui.upd_markets_snapshot') }}</div>
          @if($marketRows->isNotEmpty())
            <div class="mgrid">
              @foreach($marketRows->take(4) as $market)
                <div class="mc">
                  <div class="mc-l">{{ $market->commodity ?: __('ui.upd_commodity') }}</div>
                  <div class="mc-v">₹{{ number_format((float) ($market->modal_price ?: $market->rate ?: 0), 0) }}</div>
                  <div class="mc-s">{{ $market->city_rel?->name ?: $market->city ?: $cityEdition }} · {{ optional($market->price_date)->format('d M Y') ?: __('ui.upd_latest') }}</div>
                </div>
              @endforeach
            </div>
          @else
            <div class="empty">{{ __('ui.upd_no_local_commodity') }}</div>
          @endif
        </div>
        <div class="col">
          <div class="flag">{{ __('ui.upd_editorial_pick') }}</div>
          @php $editorial = $editorials->first(); @endphp
          <h3 class="h3">{{ $editorial?->title ?: __('ui.upd_why_dynamic') }}</h3>
          <div class="byl"><em>{{ $editorial?->city?->name ?: __('ui.upd_citypulse_desk') }}</em></div>
          <p class="body">{{ \Illuminate\Support\Str::limit(strip_tags($editorial?->content ?: __('ui.upd_edition_fallback_text')), 280) }}</p>
          <div class="pq"><p>"{{ __('ui.upd_quote_text') }}"</p><cite>{{ __('ui.upd_editorial_board') }}</cite></div>
        </div>
      </div>
    </div>
  </main>

  <main class="pg" id="cpPgNews">
    <div class="dbanner"><div class="dline"></div><div class="dtext">{{ __('ui.upd_latest_news_feed_banner') }}</div><div class="dline"></div></div>
    <div class="sbar"><h2>{{ __('ui.upd_top_stories') }}</h2><em>{{ $cityEdition }} {{ __('ui.upd_edition') }}</em></div>
    <div class="g2">
      @forelse($newsGridItems as $story)
        <div class="col row-b">
          <div class="flag">{{ strtoupper($story->update_type ?: 'general') }}</div>
          <h3 class="h2"><a href="{{ (!empty($story->source_url) && $story->source_url !== '#') ? $story->source_url : route('updates.show', $story) }}" style="color:inherit">{{ $story->title }}</a></h3>
          <div class="byl"><em>{{ $story->city?->name ?: $cityEdition }}</em> · {{ optional($story->published_at ?: $story->created_at)->format('d M Y, h:i A') }}</div>
          <p class="body">{{ \Illuminate\Support\Str::limit(strip_tags($story->content ?: __('ui.upd_open_story')), 240) }}</p>
        </div>
      @empty
        <div class="col empty" style="grid-column:1/-1">{{ __('ui.upd_no_updates') }}</div>
      @endforelse
    </div>

    <div style="margin-top:1.2rem">
      <div class="sbar"><h2>{{ __('ui.upd_city_feed') }}</h2><em>{{ __('ui.upd_google_news_integration') }}</em></div>
      <div class="g3e">
        @forelse(array_slice($cityNews, 0, 3) as $item)
          <div class="col">
            <div class="flag">{{ __('ui.upd_external') }}</div>
            <h3 class="h3"><a href="{{ $item['link'] ?? '#' }}" target="_blank" rel="noopener" style="color:inherit">{{ $item['title'] ?? __('ui.upd_untitled_story') }}</a></h3>
            <div class="byl"><em>{{ $item['source'] ?? __('ui.upd_news_source') }}</em> · {{ $item['pubDate'] ?? __('ui.upd_live_feed') }}</div>
          </div>
        @empty
          <div class="col empty" style="grid-column:1/-1">{{ __('ui.upd_no_external_headlines') }}</div>
        @endforelse
      </div>
    </div>

    <div style="margin-top:1.2rem">
      <div class="sbar"><h2>{{ __('ui.upd_offers_alerts') }}</h2><em>{{ __('ui.upd_published_from_updates') }}</em></div>
      <div class="g4">
        @forelse($offerUpdates->take(4) as $offer)
          <div class="col">
            <div class="flag">{{ __('ui.upd_offer') }}</div>
            <h4 class="h4"><a href="{{ (!empty($offer->source_url) && $offer->source_url !== '#') ? $offer->source_url : route('updates.show', $offer) }}" style="color:inherit">{{ $offer->title }}</a></h4>
            <p class="body" style="font-size:.78rem">{{ \Illuminate\Support\Str::limit(strip_tags($offer->content ?: __('ui.upd_open_offer')), 140) }}</p>
          </div>
        @empty
          <div class="col empty" style="grid-column:1/-1">{{ __('ui.upd_no_offers') }}</div>
        @endforelse
      </div>
    </div>
  </main>

  <main class="pg" id="cpPgEvents">
    <div class="dbanner"><div class="dline"></div><div class="dtext">{{ __('ui.upd_events_happenings') }}</div><div class="dline"></div></div>
    <div class="sbar"><h2>{{ __('ui.upd_city_events_calendar') }}</h2><em>{{ $cityEdition }}</em></div>
    <div class="g2">
      @forelse($eventUpdates as $event)
        @php $eventDate = $event->published_at ?: $event->created_at; @endphp
        <div class="col row-b">
          <div class="evt" style="border:none;padding:0;margin-bottom:.6rem">
            <div class="ebox"><div class="ed">{{ $eventDate->format('d') }}</div><div class="em">{{ strtoupper($eventDate->format('M')) }}</div></div>
            <div><div class="etitle"><a href="{{ (!empty($event->source_url) && $event->source_url !== '#') ? $event->source_url : route('updates.show', $event) }}" style="color:inherit">{{ $event->title }}</a></div><div class="emeta">📍 {{ $event->city?->name ?: $cityEdition }}</div></div>
          </div>
          <p class="body" style="font-size:.81rem">{{ \Illuminate\Support\Str::limit(strip_tags($event->content ?: __('ui.upd_open_event')), 220) }}</p>
          <span class="tag tg">{{ __('ui.upd_event_tag') }}</span>
        </div>
      @empty
        <div class="col empty" style="grid-column:1/-1">{{ __('ui.upd_no_events_published') }}</div>
      @endforelse
    </div>
  </main>

  <main class="pg" id="cpPgJobs">
    <div class="dbanner"><div class="dline"></div><div class="dtext">{{ __('ui.upd_employment_banner') }}</div><div class="dline"></div></div>
    <div class="sbar"><h2>{{ __('ui.upd_jobs_feed') }}</h2><em>{{ __('ui.upd_dynamic_jobs') }}</em></div>
    <div class="g3e">
      @forelse($jobs as $job)
        <div class="col">
          <div class="flag">{{ $job->city?->name ?: $cityEdition }}</div>
          <div class="jtitle"><a href="{{ $job->external_url ?: route('jobs.show', $job) }}" style="color:inherit">{{ $job->title }}</a></div>
          <div class="jco">🏢 {{ $job->company ?: __('ui.upd_employer_not_specified') }}</div>
          <p class="body" style="font-size:.79rem">{{ \Illuminate\Support\Str::limit(strip_tags($job->description ?: __('ui.upd_open_job')), 180) }}</p>
          @if($job->category)
            <span class="tag">{{ $job->category }}</span>
          @endif
          <div style="margin-top:.45rem"><a href="{{ $job->external_url ?: route('jobs.show', $job) }}" class="byl" style="color:var(--red)">{{ __('ui.upd_apply') }}</a></div>
        </div>
      @empty
        <div class="col empty" style="grid-column:1/-1">{{ __('ui.upd_no_jobs') }}</div>
      @endforelse
    </div>
  </main>

  <main class="pg" id="cpPgMarkets">
    <div class="dbanner"><div class="dline"></div><div class="dtext">{{ __('ui.upd_markets_commodities') }}</div><div class="dline"></div></div>
    <div class="g2">
      <div class="col">
        <div class="flag">{{ __('ui.upd_indian_indices') }}</div>
        <h2 class="h2">{{ __('ui.upd_market_dashboard') }}</h2>
        <div class="src-badge"><span class="ld"></span>{{ __('ui.upd_yahoo_finance') }}</div>
        <div class="mgrid">
          @forelse($marketIndices as $index)
            <div class="mc">
              <div class="mc-l">{{ $index['name'] }}</div>
              <div class="mc-v">{{ number_format((float) $index['price'], 2) }}</div>
              <div class="mc-s" style="color:{{ (float) $index['change'] >= 0 ? 'var(--green)' : 'var(--red)' }}">{{ (float) $index['change'] >= 0 ? '+' : '' }}{{ number_format((float) $index['change'], 2) }}%</div>
            </div>
          @empty
            <div class="empty" style="grid-column:1/-1">{{ __('ui.upd_live_index_unavailable') }}</div>
          @endforelse
        </div>
      </div>
      <div class="col">
        <div class="flag">{{ __('ui.upd_local_commodity_rates') }}</div>
        <h2 class="h2">{{ $cityEdition }} {{ __('ui.upd_market_entries') }}</h2>
        @if($marketRows->isNotEmpty())
          <table style="width:100%;border-collapse:collapse;font-size:.81rem;margin-top:.45rem">
            <thead>
              <tr style="background:var(--ink);color:var(--paper)">
                <th style="padding:5px 9px;text-align:left;font-family:var(--fm);font-size:10px">{{ __('ui.upd_commodity') }}</th>
                <th style="padding:5px 9px;text-align:right;font-family:var(--fm);font-size:10px">{{ __('ui.upd_modal') }}</th>
                <th style="padding:5px 9px;text-align:right;font-family:var(--fm);font-size:10px">{{ __('ui.upd_date') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($marketRows as $market)
                <tr style="background:{{ $loop->odd ? 'var(--cream)' : 'var(--paper)' }}">
                  <td style="padding:5px 9px">{{ $market->commodity ?: __('ui.upd_commodity') }}</td>
                  <td style="padding:5px 9px;text-align:right;font-family:var(--fm)">₹{{ number_format((float) ($market->modal_price ?: $market->rate ?: 0), 0) }}</td>
                  <td style="padding:5px 9px;text-align:right;font-family:var(--fm)">{{ optional($market->price_date)->format('d M') ?: __('ui.upd_latest') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div class="empty">{{ __('ui.upd_no_commodity_rows') }}</div>
        @endif
        <div class="notice"><div class="nt">{{ __('ui.upd_edition_note') }}</div><p class="nb2">{{ __('ui.upd_edition_note_text') }}</p></div>
      </div>
    </div>
  </main>

  <main class="pg" id="cpPgOpinion">
    <div class="dbanner"><div class="dline"></div><div class="dtext">{{ __('ui.upd_opinion_analysis') }}</div><div class="dline"></div></div>
    <div class="sbar"><h2>{{ __('ui.upd_editorial_columns') }}</h2><em>{{ __('ui.upd_generated_from_updates') }}</em></div>
    <div class="g3e">
      @forelse($editorials as $story)
        <div class="col">
          <div class="flag">{{ strtoupper($story->update_type ?: 'general') }}</div>
          <h2 class="h2" style="font-size:1.25rem"><a href="{{ (!empty($story->source_url) && $story->source_url !== '#') ? $story->source_url : route('updates.show', $story) }}" style="color:inherit">{{ $story->title }}</a></h2>
          <div class="byl">{{ __('ui.upd_by') }} <em>{{ $story->city?->name ?: __('ui.upd_citypulse_desk') }}</em></div>
          <p class="body dc">{{ \Illuminate\Support\Str::limit(strip_tags($story->content ?: __('ui.upd_publish_long_form')), 340) }}</p>
          <div class="pq"><p>“{{ \Illuminate\Support\Str::limit(strip_tags($story->content ?: $story->title), 110) }}”</p><cite>{{ $story->city?->name ?: 'CityPulse' }}</cite></div>
        </div>
      @empty
        <div class="col empty" style="grid-column:1/-1">{{ __('ui.upd_no_editorial') }}</div>
      @endforelse
    </div>
  </main>
</div>

<script>
  const cpServerMetals = @json($liveBarMetals ?? []);
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
        const cpWeatherMap = {0:@json(__('ui.weather_clear')),1:@json(__('ui.weather_mostly_clear')),2:@json(__('ui.weather_partly_cloudy')),3:@json(__('ui.weather_overcast')),45:@json(__('ui.weather_fog')),61:@json(__('ui.weather_rain')),63:@json(__('ui.weather_rain')),80:@json(__('ui.weather_showers')),95:@json(__('ui.weather_thunderstorm'))};
        const weatherText = cpWeatherMap[weatherData.current.weather_code] || @json(__('ui.weather_partly_cloudy'));
        cpEl('cpWx').textContent = weatherText;
      }
    } catch (error) {}

    try {
      let usdRate = cpServerMetals?.usd_inr ? Number(cpServerMetals.usd_inr) : 84.10;
      if (!cpServerMetals?.usd_inr) {
        const fxResponse = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
        const fxData = await fxResponse.json();
        usdRate = fxData?.rates?.INR ? Number(fxData.rates.INR) : usdRate;
      }
      cpEl('cpUsd').textContent = '₹' + usdRate.toFixed(2);

      const goldInr = Number(cpServerMetals?.gold_10g || 0) || 85900;
      const silverInr = Number(cpServerMetals?.silver_kg || 0) || 96500;
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
