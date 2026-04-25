@extends('layouts.app')

@section('title', $update->title . ' | CityPulse')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($update->content ?? ''), 160))

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Source+Serif+4:ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,600;1,8..60,400&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
  #updShow{--ink:#1a1208;--ink2:#2d2416;--ink3:#5a4d3a;--paper:#f8f3e8;--paper2:#f2ead8;--paper3:#e8dfc5;--cream:#faf7f0;--red:#b91c1c;--gold:#b8860b;--gold2:#d4a017;--gold3:#f0c040;--rule:rgba(26,18,8,.14);--rule2:rgba(26,18,8,.42);--fh:'Playfair Display',serif;--fb:'Source Serif 4',serif;--fm:'JetBrains Mono',monospace;background:var(--paper);margin:-22px;padding-bottom:40px;font-family:var(--fb);color:var(--ink)}
  #updShow *{box-sizing:border-box}
  #updShow a{text-decoration:none;color:inherit}
  #updShow .mast{background:var(--ink);color:var(--paper);padding:.45rem 1.3rem;display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap}
  #updShow .mast-name{font-family:var(--fh);font-size:1.25rem;font-weight:900;letter-spacing:-1px;color:var(--paper)}
  #updShow .mast-name span{color:var(--gold3)}
  #updShow .mast-back{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:1px;color:rgba(248,243,232,.75);border:1px solid rgba(255,255,255,.22);padding:.35rem .85rem;border-radius:1px;transition:color .2s,border-color .2s}
  #updShow .mast-back:hover{color:var(--gold3);border-color:var(--gold3)}
  #updShow .wrap{max-width:760px;margin:0 auto;padding:1.75rem 1.35rem}
  #updShow .flag{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:var(--red);border-bottom:2px solid var(--red);display:inline-block;padding-bottom:1px;margin-bottom:.85rem}
  #updShow .art-title{font-family:var(--fh);font-size:clamp(1.7rem,4vw,2.9rem);font-weight:900;line-height:1.06;letter-spacing:-1px;margin-bottom:.55rem}
  #updShow .art-meta{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:.8px;color:var(--ink3);text-transform:uppercase;margin-bottom:1.1rem;display:flex;align-items:center;gap:.6rem;flex-wrap:wrap}
  #updShow .art-meta em{color:var(--red);font-style:normal}
  #updShow .art-meta .sep{color:var(--rule2)}
  #updShow .art-photo{width:100%;max-height:420px;object-fit:cover;display:block;border:1px solid var(--rule);margin-bottom:1.1rem}
  #updShow .art-photo-wrap{position:relative;margin-bottom:1.1rem}
  #updShow .art-caption{position:absolute;bottom:0;left:0;right:0;background:rgba(26,18,8,.72);color:var(--paper);font-family:var(--fm);font-size:9px;padding:4px 10px;letter-spacing:.5px}
  #updShow .art-rule{border:none;border-top:2px solid var(--rule2);margin:1.1rem 0}
  #updShow .art-body{font-family:var(--fb);font-size:.95rem;line-height:1.82;color:var(--ink2)}
  #updShow .art-body::first-letter{font-family:var(--fh);font-size:3.5rem;font-weight:900;float:left;line-height:.82;padding-right:7px;padding-top:4px;color:var(--red)}
  #updShow .art-body p{margin-bottom:1rem}
  #updShow .art-body p:first-child::first-letter{font-family:var(--fh);font-size:3.5rem;font-weight:900;float:left;line-height:.82;padding-right:7px;padding-top:4px;color:var(--red)}
  #updShow .art-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-top:1.75rem;padding-top:1rem;border-top:2px solid var(--rule2)}
  #updShow .btn-back{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:1px;color:var(--ink3);border:1px solid var(--rule2);padding:.5rem 1rem;border-radius:1px;transition:all .2s}
  #updShow .btn-back:hover{background:var(--ink);color:var(--paper)}
  #updShow .btn-edit{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:1px;color:var(--gold2);border:1px solid var(--gold2);padding:.5rem 1rem;border-radius:1px;transition:all .2s}
  #updShow .btn-edit:hover{background:var(--gold2);color:var(--ink)}
  #updShow .btn-del{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:1px;color:var(--red);border:1px solid var(--red);padding:.5rem 1rem;border-radius:1px;background:none;cursor:pointer;transition:all .2s}
  #updShow .btn-del:hover{background:var(--red);color:#fff}
  #updShow .admin-bar{display:flex;gap:.6rem;align-items:center;flex-wrap:wrap}
  #updShow .edition-tag{font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;padding:2px 7px;border-radius:1px;border:1px solid var(--rule2);color:var(--ink3)}
  @media(max-width:640px){#updShow{margin:-12px}#updShow .wrap{padding:1.1rem .9rem}#updShow .art-body::first-letter{font-size:2.8rem}}
</style>

<div id="updShow">
  <div class="mast">
    <div class="mast-name">City<span>Pulse</span></div>
    <a href="{{ route('updates.index') }}" class="mast-back">← {{ __('ui.upd_front_page') }}</a>
  </div>

  <div class="wrap">
    @php
      $photoUrl = $update->photo_path ? \Illuminate\Support\Facades\Storage::url($update->photo_path) : null;
      $typeLabel = $update->update_type ? strtoupper($update->update_type) : __('ui.upd_news');
      $cityLabel = $update->city?->name ? city_display_name($update->city->name) : __('ui.all_cities');
      $dateLabel = optional($update->published_at ?: $update->created_at)->isoFormat('D MMM Y, h:mm A');
    @endphp

    <div class="flag">{{ $typeLabel }}</div>
    <h1 class="art-title">{{ $update->title }}</h1>

    <div class="art-meta">
      <em>{{ $cityLabel }}</em>
      <span class="sep">·</span>
      <span>{{ $dateLabel }}</span>
      @if($update->shop?->name)
        <span class="sep">·</span>
        <span class="edition-tag">{{ $update->shop->name }}</span>
      @endif
    </div>

    <hr class="art-rule">

    @if($photoUrl)
      <div class="art-photo-wrap">
        <img src="{{ $photoUrl }}" alt="{{ $update->title }}" class="art-photo">
        <div class="art-caption">{{ $typeLabel }} · CITYPULSE DESK</div>
      </div>
    @endif

    <div class="art-body">
      {!! nl2br(e($update->content ?? '')) !!}
    </div>

    <div class="art-footer">
      <a href="{{ route('updates.index') }}" class="btn-back">← {{ __('ui.upd_back_to_edition') }}</a>

      @auth
        @if(auth()->user()->isSuperadmin())
          <div class="admin-bar">
            <a href="{{ route('updates.edit', $update->id) }}" class="btn-edit">✎ {{ __('ui.edit') }}</a>
            <form action="{{ route('updates.destroy', $update->id) }}" method="POST" style="margin:0" onsubmit="return confirm('{{ __('ui.confirm_delete') }}')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-del">✕ {{ __('ui.delete') }}</button>
            </form>
          </div>
        @endif
      @endauth
    </div>
  </div>
</div>
@endsection
