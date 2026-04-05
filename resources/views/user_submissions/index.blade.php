@extends('layouts.app')

@section('title', __('ui.community_stories') . ' · CityPulse')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Source+Serif+4:ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,600;1,8..60,400&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
  #cpCommunity{--ink:#1a1208;--ink2:#2d2416;--ink3:#5a4d3a;--paper:#f8f3e8;--paper2:#f2ead8;--paper3:#e8dfc5;--cream:#faf7f0;--red:#b91c1c;--red2:#dc2626;--gold:#b8860b;--gold2:#d4a017;--gold3:#f0c040;--silver:#64748b;--green:#166534;--green2:rgba(22,101,52,.1);--rule:rgba(26,18,8,.13);--rule2:rgba(26,18,8,.34);--fh:'Playfair Display',serif;--fb:'Source Serif 4',serif;--fm:'JetBrains Mono',monospace;background:var(--paper);color:var(--ink);font-family:var(--fb);margin:-22px;padding-bottom:3rem}
  #cpCommunity *{box-sizing:border-box}
  #cpCommunity a{text-decoration:none}

  /* ── masthead ── */
  #cpCommunity .mast{background:var(--ink);color:var(--paper)}
  #cpCommunity .mast-top{display:flex;align-items:center;justify-content:space-between;padding:.4rem 1.4rem;border-bottom:1px solid rgba(255,255,255,.08);flex-wrap:wrap;gap:.3rem}
  #cpCommunity .mast-top span{font-family:var(--fm);font-size:10px;color:rgba(248,243,232,.5);letter-spacing:1.5px}
  #cpCommunity .mast-top .ed{color:var(--gold3);font-weight:700;letter-spacing:2px}
  #cpCommunity .mast-hero{text-align:center;padding:1rem 1.4rem .6rem}
  #cpCommunity .mast-rule{height:3px;background:linear-gradient(90deg,transparent,var(--gold2),transparent);margin-bottom:5px}
  #cpCommunity .mast-title{font-family:var(--fh);font-size:clamp(2rem,6vw,4rem);font-weight:900;line-height:.95;letter-spacing:-1.5px}
  #cpCommunity .mast-sub{font-family:var(--fh);font-style:italic;font-size:clamp(.8rem,1.5vw,.95rem);color:var(--gold3);letter-spacing:2px;margin-top:5px}
  #cpCommunity .mast-ruby{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.06);border:1px solid rgba(240,192,64,.3);padding:4px 12px;border-radius:2px;font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:1px;color:var(--gold3);margin-top:.5rem}
  #cpCommunity .ld{width:5px;height:5px;border-radius:50%;background:var(--gold3);animation:cpBlink 1.4s infinite}
  @keyframes cpBlink{0%,100%{opacity:1}50%{opacity:.25}}

  /* ── tab nav ── */
  #cpCommunity .tab-nav{background:var(--ink);border-top:3px solid var(--gold2);display:flex;flex-wrap:wrap}
  #cpCommunity .tn{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(248,243,232,.65);padding:.55rem 1.1rem;cursor:pointer;border-right:1px solid rgba(255,255,255,.07);transition:all .15s}
  #cpCommunity .tn:hover,#cpCommunity .tn.on{color:var(--gold3)}
  #cpCommunity .tn .cnt{display:inline-block;background:rgba(240,192,64,.2);color:var(--gold3);font-size:9px;padding:1px 5px;border-radius:1px;margin-left:3px}

  /* ── category filter ── */
  #cpCommunity .cat-bar{background:var(--paper2);border-bottom:2px solid var(--rule2);padding:.5rem 1.4rem;display:flex;gap:.5rem;flex-wrap:wrap;align-items:center}
  #cpCommunity .cat-btn{font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:4px 12px;border:2px solid var(--rule2);color:var(--ink3);cursor:pointer;transition:all .15s;text-decoration:none;display:inline-block}
  #cpCommunity .cat-btn:hover,#cpCommunity .cat-btn.on{background:var(--ink);color:var(--paper);border-color:var(--ink)}
  #cpCommunity .cat-btn .n{font-size:8px;opacity:.7;margin-left:2px}

  /* ── layout ── */
  #cpCommunity .layout{display:grid;grid-template-columns:1fr 320px;gap:0;min-height:60vh}
  #cpCommunity .main{padding:1.4rem;border-right:2px solid var(--rule2)}
  #cpCommunity .side{padding:1.2rem;background:var(--cream)}

  /* ── story cards ── */
  #cpCommunity .stories-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem}
  #cpCommunity .card{border:1px solid var(--rule2);background:var(--cream);cursor:pointer;transition:all .2s;position:relative;overflow:hidden}
  #cpCommunity .card:hover{border-color:var(--ink);transform:translateY(-2px);box-shadow:0 4px 12px rgba(26,18,8,.12)}
  #cpCommunity .card-img{width:100%;height:140px;object-fit:cover;display:block;border-bottom:1px solid var(--rule)}
  #cpCommunity .card-img-placeholder{width:100%;height:140px;background:var(--paper3);display:flex;align-items:center;justify-content:center;font-size:2.5rem;border-bottom:1px solid var(--rule)}
  #cpCommunity .card-body{padding:.8rem}
  #cpCommunity .card-flag{font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--red);border-bottom:2px solid var(--red);display:inline-block;padding-bottom:1px;margin-bottom:.5rem}
  #cpCommunity .card-title{font-family:var(--fh);font-size:1rem;font-weight:700;line-height:1.2;color:var(--ink);margin-bottom:.3rem}
  #cpCommunity .card-meta{font-family:var(--fm);font-size:9px;color:var(--ink3);margin-bottom:.45rem;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap}
  #cpCommunity .card-meta .author{color:var(--gold);font-weight:700}
  #cpCommunity .card-excerpt{font-family:var(--fb);font-size:.78rem;color:var(--ink2);line-height:1.6;margin-bottom:.5rem}
  #cpCommunity .card-read{font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1px;color:var(--red);text-transform:uppercase}

  /* ── pagination ── */
  #cpCommunity .pag{display:flex;gap:.4rem;flex-wrap:wrap;margin-top:1.2rem;align-items:center}
  #cpCommunity .pag a,#cpCommunity .pag span{font-family:var(--fm);font-size:10px;font-weight:700;padding:5px 10px;border:2px solid var(--rule2);color:var(--ink3);text-decoration:none;transition:all .15s}
  #cpCommunity .pag a:hover{background:var(--ink);color:var(--paper);border-color:var(--ink)}
  #cpCommunity .pag .active span{background:var(--ink);color:var(--paper);border-color:var(--ink)}
  #cpCommunity .pag .disabled span{opacity:.4}

  /* ── sidebar sections ── */
  #cpCommunity .side-section{margin-bottom:1.4rem}
  #cpCommunity .side-head{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--ink3);border-bottom:2px solid var(--ink);padding-bottom:.3rem;margin-bottom:.8rem;display:flex;align-items:center;justify-content:space-between}
  #cpCommunity .side-head .badge{font-size:9px;background:var(--gold3);color:var(--ink);padding:1px 6px;letter-spacing:.5px}

  /* ── ruby points card ── */
  #cpCommunity .ruby-card{background:var(--ink);color:var(--paper);padding:.9rem 1rem;margin-bottom:.8rem}
  #cpCommunity .ruby-card .rb-val{font-family:var(--fh);font-size:2.2rem;font-weight:900;color:var(--gold3);line-height:1}
  #cpCommunity .ruby-card .rb-lbl{font-family:var(--fm);font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(248,243,232,.6);margin-top:3px}
  #cpCommunity .ruby-card .rb-hint{font-family:var(--fm);font-size:9px;color:var(--gold2);margin-top:.5rem;line-height:1.4}

  /* ── my submissions list ── */
  #cpCommunity .sub-item{border:1px solid var(--rule);background:var(--paper);padding:.55rem .7rem;margin-bottom:.5rem;border-radius:0}
  #cpCommunity .sub-item .si-title{font-family:var(--fh);font-size:.82rem;font-weight:700;color:var(--ink);line-height:1.2;margin-bottom:3px}
  #cpCommunity .sub-item .si-meta{font-family:var(--fm);font-size:9px;color:var(--ink3);display:flex;align-items:center;gap:.4rem;flex-wrap:wrap}
  #cpCommunity .pill{display:inline-block;font-family:var(--fm);font-size:8px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:1px 6px;border-radius:1px}
  #cpCommunity .pill-pending{background:rgba(180,140,11,.15);color:var(--gold);border:1px solid var(--gold)}
  #cpCommunity .pill-approved{background:var(--green2);color:var(--green);border:1px solid var(--green)}
  #cpCommunity .pill-rejected{background:rgba(185,28,28,.1);color:var(--red);border:1px solid var(--red)}
  #cpCommunity .pill-inactive{background:rgba(26,18,8,.07);color:var(--ink3);border:1px solid var(--rule2)}
  #cpCommunity .pill-points{background:rgba(240,192,64,.15);color:var(--gold);border:1px solid var(--gold2);font-size:8px}

  /* ── submit form ── */
  #cpCommunity .form-section{}
  #cpCommunity .field{margin-bottom:.9rem}
  #cpCommunity .field label{display:block;font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--ink3);margin-bottom:.35rem}
  #cpCommunity .field label .req{color:var(--red)}
  #cpCommunity .field select,
  #cpCommunity .field input[type="text"],
  #cpCommunity .field textarea{width:100%;border:2px solid var(--rule2);background:var(--cream);color:var(--ink);font-family:var(--fb);font-size:.85rem;padding:.5rem .7rem;outline:none;transition:border-color .2s;border-radius:0}
  #cpCommunity .field select:focus,
  #cpCommunity .field input[type="text"]:focus,
  #cpCommunity .field textarea:focus{border-color:var(--ink)}
  #cpCommunity .field textarea{resize:vertical;min-height:100px;line-height:1.65}
  #cpCommunity .file-wrap{border:2px dashed var(--rule2);background:var(--paper2);padding:.7rem;text-align:center;cursor:pointer}
  #cpCommunity .file-wrap input{display:none}
  #cpCommunity .file-lbl{font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1px;color:var(--ink3);cursor:pointer}
  #cpCommunity .file-nm{font-family:var(--fm);font-size:9px;color:var(--red);margin-top:3px;display:none}
  #cpCommunity .type-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.35rem;margin-top:.3rem}
  #cpCommunity .type-opt{border:2px solid var(--rule2);padding:.4rem .3rem;cursor:pointer;text-align:center;transition:all .15s;user-select:none}
  #cpCommunity .type-opt.sel{border-color:var(--ink);background:var(--ink);color:var(--paper)}
  #cpCommunity .type-opt .ico{font-size:1rem;display:block;margin-bottom:1px}
  #cpCommunity .type-opt .lbl{font-family:var(--fm);font-size:8px;font-weight:700;letter-spacing:.5px;text-transform:uppercase}
  #cpCommunity input[name="type"]{display:none}
  #cpCommunity .btn-submit{width:100%;background:var(--ink);color:var(--paper);border:none;padding:.65rem;font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;cursor:pointer;margin-top:.5rem;transition:background .2s}
  #cpCommunity .btn-submit:hover{background:#3d3020}
  #cpCommunity .login-prompt{border:2px solid var(--ink);padding:1rem;background:var(--ink);color:var(--paper);text-align:center}
  #cpCommunity .login-prompt .lp-title{font-family:var(--fh);font-size:1.05rem;font-weight:700;margin-bottom:.4rem}
  #cpCommunity .login-prompt .lp-desc{font-family:var(--fb);font-size:.78rem;color:rgba(248,243,232,.7);margin-bottom:.8rem;line-height:1.5}
  #cpCommunity .btn-login{display:block;background:var(--gold2);color:var(--ink);padding:.55rem;font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;text-align:center;margin-bottom:.4rem;text-decoration:none;transition:background .15s}
  #cpCommunity .btn-login:hover{background:var(--gold3)}
  #cpCommunity .btn-reg{display:block;border:1px solid rgba(255,255,255,.25);color:rgba(248,243,232,.75);padding:.5rem;font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;text-align:center;text-decoration:none;transition:all .15s}
  #cpCommunity .btn-reg:hover{border-color:var(--gold3);color:var(--gold3)}

  /* ── reading modal ── */
  #cpReadModal{display:none;position:fixed;inset:0;background:rgba(26,18,8,.9);z-index:999;overflow-y:auto;padding:1.5rem 1rem}
  #cpReadModal.open{display:flex;align-items:flex-start;justify-content:center}
  #cpReadInner{background:var(--paper);max-width:720px;width:100%;padding:0;position:relative;margin:auto}
  #cpReadModal .rm-img{width:100%;max-height:320px;object-fit:cover;display:block}
  #cpReadModal .rm-body{padding:1.6rem 1.8rem}
  #cpReadModal .rm-flag{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--red);border-bottom:2px solid var(--red);display:inline-block;padding-bottom:1px;margin-bottom:.6rem}
  #cpReadModal .rm-title{font-family:var(--fh);font-size:clamp(1.4rem,3vw,2.1rem);font-weight:900;line-height:1.1;letter-spacing:-.6px;margin-bottom:.5rem}
  #cpReadModal .rm-byline{font-family:var(--fm);font-size:10px;color:var(--ink3);margin-bottom:1rem;display:flex;align-items:center;gap:.6rem;flex-wrap:wrap}
  #cpReadModal .rm-byline .author{color:var(--gold);font-weight:700}
  #cpReadModal .rm-rule{border:none;border-top:2px solid var(--rule2);margin:.6rem 0}
  #cpReadModal .rm-content{font-family:var(--fb);font-size:.92rem;line-height:1.8;color:var(--ink2)}
  #cpReadModal .rm-content p{margin-bottom:.9rem}
  #cpReadModal .rm-close{position:absolute;top:.8rem;right:.8rem;background:var(--ink);color:var(--paper);border:none;width:34px;height:34px;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;font-family:var(--fm);font-weight:700;z-index:10}
  #cpReadModal .rm-close:hover{background:var(--red)}
  #cpReadModal .rm-footer{padding:.8rem 1.8rem;border-top:2px solid var(--rule2);display:flex;align-items:center;justify-content:space-between;font-family:var(--fm);font-size:10px;color:var(--ink3)}

  /* ── empty state ── */
  #cpCommunity .empty{text-align:center;padding:3rem 1rem;font-family:var(--fm);font-size:11px;color:var(--ink3);letter-spacing:1px;border:1px dashed var(--rule2)}
  #cpCommunity .empty .big{font-size:2.5rem;display:block;margin-bottom:.5rem}

  /* ── success alert ── */
  #cpCommunity .alert-ok{border-left:4px solid var(--green);background:rgba(22,101,52,.08);padding:.6rem .9rem;font-family:var(--fm);font-size:11px;font-weight:700;color:var(--green);letter-spacing:.5px;margin-bottom:1rem}
  #cpCommunity .alert-err{border-left:4px solid var(--red);background:rgba(185,28,28,.06);padding:.6rem .9rem;font-family:var(--fm);font-size:11px;color:var(--red);margin-bottom:1rem}

  /* ── responsive ── */
  @media(max-width:900px){
    #cpCommunity{margin:-12px}
    #cpCommunity .layout{grid-template-columns:1fr}
    #cpCommunity .main{border-right:none;padding:1rem}
    #cpCommunity .side{padding:1rem;border-top:2px solid var(--rule2)}
    #cpCommunity .stories-grid{grid-template-columns:1fr 1fr}
    #cpReadModal .rm-body{padding:1.1rem}
  }
  @media(max-width:560px){
    #cpCommunity .stories-grid{grid-template-columns:1fr}
    #cpCommunity .type-grid{grid-template-columns:repeat(3,1fr)}
  }
</style>

{{-- Reading Modal --}}
<div id="cpReadModal" onclick="if(event.target===this)cpCloseModal()">
  <div id="cpReadInner">
    <button class="rm-close" onclick="cpCloseModal()">✕</button>
    <img id="rmImg" class="rm-img" src="" alt="" style="display:none">
    <div class="rm-body">
      <div class="rm-flag" id="rmFlag"></div>
      <h1 class="rm-title" id="rmTitle"></h1>
      <div class="rm-byline">
        <span class="author" id="rmAuthor"></span>
        <span id="rmDate"></span>
      </div>
      <hr class="rm-rule">
      <div class="rm-content" id="rmContent"></div>
    </div>
    <div class="rm-footer">
      <span>CityPulse Community · {{ __('ui.community_stories') }}</span>
      <span id="rmType" style="text-transform:uppercase;letter-spacing:1px"></span>
    </div>
  </div>
</div>

<div id="cpCommunity">
  {{-- Masthead --}}
  <div class="mast">
    <div class="mast-top">
      <span class="ed">COMMUNITY DESK</span>
      <span>{{ strtoupper(now()->format('l, d F Y')) }}</span>
      <span>{{ strtoupper(__('ui.community_stories')) }}</span>
    </div>
    <div class="mast-hero">
      <div class="mast-rule"></div>
      <div class="mast-title">{{ __('ui.community_stories') }}</div>
      <div class="mast-sub">{{ __('ui.earn_10_points') }}</div>
      <div class="mast-ruby">
        <span class="ld"></span>
        {{ __('ui.earn_10_points') }}
      </div>
    </div>

    {{-- Main tabs --}}
    <div class="tab-nav">
      <div class="tn {{ $tab === 'read' ? 'on' : '' }}" onclick="cpTab('read', this)">
        📰 {{ __('ui.browse_stories') }}
        <span class="cnt">{{ $approved->total() }}</span>
      </div>
      @auth
        <div class="tn {{ $tab === 'submit' ? 'on' : '' }}" onclick="cpTab('submit', this)">
          ✍️ {{ __('ui.submit_story') }}
        </div>
        <div class="tn {{ $tab === 'mine' ? 'on' : '' }}" onclick="cpTab('mine', this)">
          📁 {{ __('ui.my_stories') }}
          @if($mySubmissions->isNotEmpty())
            <span class="cnt">{{ $mySubmissions->count() }}</span>
          @endif
        </div>
      @else
        <div class="tn" onclick="cpTab('submit', this)">✍️ {{ __('ui.submit_story') }}</div>
      @endauth
    </div>
  </div>

  {{-- Tab: Browse --}}
  <div id="cpTabRead" class="cp-tab-pane" style="{{ $tab !== 'read' ? 'display:none' : '' }}">
    @php $flagLabels = ['story' => __('ui.type_story'), 'news' => __('ui.type_news'), 'blog' => __('ui.type_blog'), 'analysis' => __('ui.type_analysis'), 'information' => __('ui.type_information')]; @endphp
    {{-- Category filter --}}
    <div class="cat-bar">
      <span style="font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--ink3);white-space:nowrap">{{ strtoupper(__('ui.type')) }}:</span>
      @php
        $cats = ['all' => '★ All', 'story' => '📖 '.__('ui.type_story'), 'news' => '📰 '.__('ui.type_news'), 'blog' => '✍️ '.__('ui.type_blog'), 'analysis' => '📊 '.__('ui.type_analysis'), 'information' => 'ℹ️ '.__('ui.type_information')];
      @endphp
      @foreach($cats as $slug => $label)
        @php $cnt = $slug === 'all' ? array_sum($categoryCounts) : ($categoryCounts[$slug] ?? 0); @endphp
        <a href="{{ route('user_submissions.index', ['category' => $slug]) }}"
           class="cat-btn {{ $category === $slug ? 'on' : '' }}">
          {{ $label }}<span class="n">({{ $cnt }})</span>
        </a>
      @endforeach
    </div>

    <div class="layout">
      <div class="main">
        @if(session('success'))
          <div class="alert-ok">✓ &nbsp;{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert-err">
            @foreach($errors->all() as $e)<div>✕ {{ $e }}</div>@endforeach
          </div>
        @endif

        @if($approved->isEmpty())
          <div class="empty">
            <span class="big">📭</span>
            {{ __('ui.no_approved_stories') }}
          </div>
        @else
          <div class="stories-grid">
            @foreach($approved as $item)
              @php
                $flagLabels = ['story' => __('ui.type_story'), 'news' => __('ui.type_news'), 'blog' => __('ui.type_blog'), 'analysis' => __('ui.type_analysis'), 'information' => __('ui.type_information')];
                $imgUrl = $item->photo ? asset('storage/' . $item->photo) : null;
                $excerpt = \Illuminate\Support\Str::limit(strip_tags($item->content), 110);
              @endphp
              <div class="card" onclick="cpOpenStory({{ $item->id }},
                @json($item->title),
                @json($item->content),
                @json($item->user->name ?? 'Community'),
                @json($item->created_at->format('d M Y')),
                @json($flagLabels[$item->type] ?? $item->type),
                @json($imgUrl))">
                @if($imgUrl)
                  <img class="card-img" src="{{ $imgUrl }}" alt="{{ $item->title }}">
                @else
                  <div class="card-img-placeholder">
                    {{ ['story' => '📖', 'news' => '📰', 'blog' => '✍️', 'analysis' => '📊', 'information' => 'ℹ️'][$item->type] ?? '📄' }}
                  </div>
                @endif
                <div class="card-body">
                  <div class="card-flag">{{ $flagLabels[$item->type] ?? $item->type }}</div>
                  <div class="card-title">{{ $item->title }}</div>
                  <div class="card-meta">
                    <span class="author">{{ $item->user->name ?? 'Community' }}</span>
                    <span>·</span>
                    <span>{{ $item->created_at->format('d M Y') }}</span>
                  </div>
                  <div class="card-excerpt">{{ $excerpt }}</div>
                  <div class="card-read">{{ __('ui.read_more') }} →</div>
                </div>
              </div>
            @endforeach
          </div>

          {{-- Pagination --}}
          @if($approved->hasPages())
            <div class="pag">{{ $approved->links() }}</div>
          @endif
        @endif
      </div>

      {{-- Sidebar: login prompt or submit teaser --}}
      <div class="side">
        @auth
          {{-- Auth sidebar: just show points summary --}}
          <div class="side-section">
            <div class="ruby-card">
              <div class="rb-val">{{ $myPoints }} 💎</div>
              <div class="rb-lbl">{{ __('ui.ruby_points_earned') }}</div>
              <div class="rb-hint">{{ __('ui.earn_10_points') }}</div>
            </div>
            <div class="side-head" style="margin-top:.8rem">{{ strtoupper(__('ui.my_stories')) }}</div>
            @if($mySubmissions->isEmpty())
              <div style="font-family:var(--fm);font-size:10px;color:var(--ink3)">{{ __('ui.my_submissions_empty') }}</div>
            @else
              @foreach($mySubmissions->take(5) as $sub)
                @php $pillMap = ['pending' => 'pill-pending', 'approved' => 'pill-approved', 'rejected' => 'pill-rejected', 'inactive' => 'pill-inactive']; @endphp
                <div class="sub-item">
                  <div class="si-title">{{ \Illuminate\Support\Str::limit($sub->title, 50) }}</div>
                  <div class="si-meta">
                    <span class="pill {{ $pillMap[$sub->status] ?? 'pill-pending' }}">{{ __('ui.status_'.$sub->status) }}</span>
                    @if($sub->status === 'approved') <span class="pill pill-points">+10 💎</span> @endif
                  </div>
                </div>
              @endforeach
              @if($mySubmissions->count() > 5)
                <div style="font-family:var(--fm);font-size:9px;color:var(--ink3);margin-top:.4rem;cursor:pointer" onclick="cpTab('mine', document.querySelector('.tn:nth-child(3)'))">+ {{ $mySubmissions->count() - 5 }} more →</div>
              @endif
            @endif
          </div>
        @else
          <div class="side-section">
            <div class="login-prompt">
              <div class="lp-title">✍️ {{ __('ui.login_to_submit') }}</div>
              <div class="lp-desc">{{ __('ui.login_to_submit_desc') }}</div>
              <a href="{{ route('login') }}" class="btn-login">{{ __('ui.login') ?? 'Login' }}</a>
              <a href="{{ route('register') }}" class="btn-reg">{{ __('ui.register') ?? 'Register' }}</a>
            </div>
          </div>
          <div class="side-section">
            <div class="side-head">{{ strtoupper(__('ui.community_stories')) }}</div>
            <div style="font-family:var(--fm);font-size:10px;color:var(--ink3);line-height:1.7">
              @foreach($categoryCounts as $type => $count)
                <div style="display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid var(--rule)">
                  <span>{{ $flagLabels[$type] ?? $type }}</span>
                  <span style="color:var(--red);font-weight:700">{{ $count }}</span>
                </div>
              @endforeach
            </div>
          </div>
        @endauth
      </div>
    </div>
  </div>

  {{-- Tab: Submit --}}
  <div id="cpTabSubmit" class="cp-tab-pane" style="{{ $tab !== 'submit' ? 'display:none' : '' }}">
    <div style="max-width:640px;margin:0 auto;padding:1.6rem 1.4rem">
      @auth
        @if(session('success'))
          <div class="alert-ok">✓ &nbsp;{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert-err">
            @foreach($errors->all() as $e)<div>✕ {{ $e }}</div>@endforeach
          </div>
        @endif

        <div style="display:flex;align-items:center;gap:.8rem;margin-bottom:1.4rem">
          <div style="flex:1;height:2px;background:var(--ink)"></div>
          <div style="font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase">{{ __('ui.submit_story') }}</div>
          <div style="flex:1;height:2px;background:var(--ink)"></div>
        </div>

        <div style="border:2px solid var(--ink);background:var(--cream);padding:.65rem 1rem;margin-bottom:1.2rem;display:flex;gap:.5rem;align-items:center">
          <span style="font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--red);white-space:nowrap">NOTE</span>
          <span style="font-family:var(--fb);font-size:.8rem;color:var(--ink3);line-height:1.5">{{ __('ui.login_to_submit_desc') }}</span>
        </div>

        <form method="POST" action="{{ route('user_submissions.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="field">
            <label>{{ __('ui.type') }} <span class="req">*</span></label>
            @php $oldType = old('type', 'story'); @endphp
            <div class="type-grid">
              @foreach(['story' => ['📖',__('ui.type_story')], 'news' => ['📰',__('ui.type_news')], 'blog' => ['✍️',__('ui.type_blog')], 'analysis' => ['📊',__('ui.type_analysis')], 'information' => ['ℹ️',__('ui.type_information')]] as $v => [$ico, $lbl])
                <div class="type-opt {{ $oldType === $v ? 'sel' : '' }}" onclick="cpPickType('{{ $v }}', this)">
                  <span class="ico">{{ $ico }}</span><span class="lbl">{{ $lbl }}</span>
                </div>
              @endforeach
            </div>
            <input type="hidden" name="type" id="cpTypeInput" value="{{ $oldType }}" required>
          </div>
          <div class="field">
            <label>{{ __('ui.title') }} <span class="req">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="255" placeholder="{{ __('ui.title') }}...">
          </div>
          <div class="field">
            <label>{{ __('ui.content') }} <span class="req">*</span></label>
            <textarea name="content" rows="7" required>{{ old('content') }}</textarea>
          </div>
          <div class="field">
            <label>{{ __('ui.photo') }} <em style="font-weight:400;text-transform:none;letter-spacing:0">({{ __('ui.optional') }})</em></label>
            <div class="file-wrap" onclick="document.getElementById('cpPhotoInput').click()">
              <input type="file" name="photo" id="cpPhotoInput" accept="image/*" onchange="cpShowFile(this)">
              <label class="file-lbl" for="cpPhotoInput">📷 {{ __('ui.photo') }} — JPG / PNG · max 2 MB</label>
              <div class="file-nm" id="cpFileNm"></div>
            </div>
          </div>
          <button type="submit" class="btn-submit">{{ __('ui.submit') }} →</button>
        </form>
      @else
        <div class="login-prompt" style="max-width:460px;margin:2rem auto">
          <div class="lp-title">✍️ {{ __('ui.login_to_submit') }}</div>
          <div class="lp-desc">{{ __('ui.login_to_submit_desc') }}</div>
          <a href="{{ route('login') }}" class="btn-login">{{ __('ui.login') ?? 'Login' }}</a>
          <a href="{{ route('register') }}" class="btn-reg">{{ __('ui.register') ?? 'Register' }}</a>
        </div>
      @endauth
    </div>
  </div>

  {{-- Tab: My Stories --}}
  <div id="cpTabMine" class="cp-tab-pane" style="{{ $tab !== 'mine' ? 'display:none' : '' }}">
    <div style="max-width:720px;margin:0 auto;padding:1.6rem 1.4rem">
      @auth
        @if(session('success'))
          <div class="alert-ok">✓ &nbsp;{{ session('success') }}</div>
        @endif

        {{-- Ruby points summary --}}
        <div class="ruby-card" style="margin-bottom:1.2rem">
          <div class="rb-val">{{ $myPoints }} 💎</div>
          <div class="rb-lbl">{{ __('ui.ruby_points_earned') }}</div>
          <div class="rb-hint">{{ __('ui.earn_10_points') }}</div>
        </div>

        {{-- Stats row --}}
        @php
          $statCounts = ['pending' => $mySubmissions->where('status','pending')->count(), 'approved' => $mySubmissions->where('status','approved')->count(), 'rejected' => $mySubmissions->where('status','rejected')->count(), 'inactive' => $mySubmissions->where('status','inactive')->count()];
        @endphp
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-bottom:1.2rem">
          @foreach(['pending' => ['🕐', __('ui.status_pending')], 'approved' => ['✓', __('ui.status_approved')], 'rejected' => ['✕', __('ui.status_rejected')], 'inactive' => ['◌', __('ui.status_inactive')]] as $st => [$ico, $stlbl])
            <div style="border:1px solid var(--rule2);background:var(--cream);padding:.6rem;text-align:center">
              <div style="font-family:var(--fh);font-size:1.4rem;font-weight:900;color:var(--ink)">{{ $statCounts[$st] }}</div>
              <div style="font-family:var(--fm);font-size:9px;letter-spacing:1px;text-transform:uppercase;color:var(--ink3)">{{ $ico }} {{ $stlbl }}</div>
            </div>
          @endforeach
        </div>

        <div style="display:flex;align-items:center;gap:.7rem;margin-bottom:1rem">
          <div style="flex:1;height:2px;background:var(--ink)"></div>
          <div style="font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase">{{ __('ui.my_stories') }}</div>
          <div style="flex:1;height:2px;background:var(--ink)"></div>
        </div>

        @if($mySubmissions->isEmpty())
          <div class="empty">
            <span class="big">📝</span>
            {{ __('ui.my_submissions_empty') }}
            <div style="margin-top:.8rem">
              <span onclick="cpTab('submit', document.querySelector('.tn:nth-child(2)'))" style="font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--red);cursor:pointer">{{ __('ui.submit_story') }} →</span>
            </div>
          </div>
        @else
          @foreach($mySubmissions as $sub)
            @php
              $pillMap = ['pending' => 'pill-pending', 'approved' => 'pill-approved', 'rejected' => 'pill-rejected', 'inactive' => 'pill-inactive'];
            @endphp
            <div class="sub-item">
              <div class="si-title">{{ $sub->title }}</div>
              <div class="si-meta">
                <span class="pill {{ $pillMap[$sub->status] ?? 'pill-pending' }}">{{ __('ui.status_'.$sub->status) }}</span>
                <span class="pill">{{ __('ui.type_'.$sub->type) }}</span>
                @if($sub->status === 'approved')
                  <span class="pill pill-points">+10 💎 Ruby</span>
                @endif
                <span>{{ $sub->created_at->format('d M Y') }}</span>
              </div>
              @if($sub->status === 'pending')
                <div style="font-family:var(--fm);font-size:9px;color:var(--gold);margin-top:4px">⏳ {{ __('ui.pending_review') }}</div>
              @elseif($sub->status === 'rejected')
                <div style="font-family:var(--fm);font-size:9px;color:var(--red);margin-top:4px">✕ {{ __('ui.status_rejected') }}</div>
              @endif
            </div>
          @endforeach
        @endif
      @else
        <div class="login-prompt" style="max-width:460px;margin:2rem auto">
          <div class="lp-title">{{ __('ui.login_to_submit') }}</div>
          <div class="lp-desc">{{ __('ui.login_to_submit_desc') }}</div>
          <a href="{{ route('login') }}" class="btn-login">{{ __('ui.login') ?? 'Login' }}</a>
          <a href="{{ route('register') }}" class="btn-reg">{{ __('ui.register') ?? 'Register' }}</a>
        </div>
      @endauth
    </div>
  </div>

</div>

<script>
// ── Tab switching
function cpTab(name, el) {
  document.querySelectorAll('.cp-tab-pane').forEach(p => p.style.display = 'none');
  document.getElementById('cpTab' + name.charAt(0).toUpperCase() + name.slice(1)).style.display = '';
  document.querySelectorAll('#cpCommunity .tn').forEach(t => t.classList.remove('on'));
  if (el) el.classList.add('on');
}

// ── Type picker
function cpPickType(val, el) {
  document.querySelectorAll('#cpCommunity .type-opt').forEach(o => o.classList.remove('sel'));
  el.classList.add('sel');
  document.getElementById('cpTypeInput').value = val;
}

// ── File name display
function cpShowFile(input) {
  var nm = document.getElementById('cpFileNm');
  if (input.files && input.files[0]) {
    nm.textContent = '📎 ' + input.files[0].name;
    nm.style.display = 'block';
  }
}

// ── Reading modal
function cpOpenStory(id, title, content, author, date, type, imgUrl) {
  document.getElementById('rmTitle').textContent = title;
  document.getElementById('rmAuthor').textContent = '✍ ' + author;
  document.getElementById('rmDate').textContent = date;
  document.getElementById('rmFlag').textContent = type;
  document.getElementById('rmType').textContent = type;
  var img = document.getElementById('rmImg');
  if (imgUrl) { img.src = imgUrl; img.style.display = 'block'; }
  else { img.style.display = 'none'; }
  // render content as paragraphs
  var paragraphs = content.split(/\n\n+/).map(function(p){
    return '<p>' + p.replace(/\n/g, '<br>') + '</p>';
  }).join('');
  document.getElementById('rmContent').innerHTML = paragraphs || '<p>' + content + '</p>';
  document.getElementById('cpReadModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function cpCloseModal() {
  document.getElementById('cpReadModal').classList.remove('open');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e){ if(e.key === 'Escape') cpCloseModal(); });
</script>
@endsection
