@extends('layouts.app')

@section('title', __('ui.send_news') . ' · CityPulse')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;1,8..60,400&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
  #cpSubmit{--ink:#1a1208;--ink2:#2d2416;--ink3:#5a4d3a;--paper:#f8f3e8;--paper2:#f2ead8;--paper3:#e8dfc5;--cream:#faf7f0;--red:#b91c1c;--gold:#b8860b;--gold2:#d4a017;--gold3:#f0c040;--rule:rgba(26,18,8,.14);--rule2:rgba(26,18,8,.38);--fh:'Playfair Display',serif;--fb:'Source Serif 4',serif;--fm:'JetBrains Mono',monospace;background:var(--paper);color:var(--ink);font-family:var(--fb);margin:-22px;min-height:60vh;padding-bottom:3rem}
  #cpSubmit *{box-sizing:border-box}
  /* masthead */
  #cpSubmit .mast{background:var(--ink);color:var(--paper);padding:.55rem 1.4rem .7rem;border-bottom:3px solid var(--gold2)}
  #cpSubmit .mast-top{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.4rem;margin-bottom:.3rem}
  #cpSubmit .mast-brand{font-family:var(--fh);font-size:1.7rem;font-weight:900;letter-spacing:-1px;line-height:1}
  #cpSubmit .mast-brand span{color:var(--gold3)}
  #cpSubmit .mast-meta{font-family:var(--fm);font-size:10px;color:rgba(248,243,232,.55);letter-spacing:1.5px}
  #cpSubmit .mast-flag{display:inline-block;font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;background:var(--red);color:#fff;padding:3px 10px;margin-top:.3rem}
  /* form area */
  #cpSubmit .wrap{max-width:720px;margin:0 auto;padding:2rem 1.4rem}
  #cpSubmit .section-rule{display:flex;align-items:center;gap:.8rem;margin-bottom:1.5rem}
  #cpSubmit .section-rule .line{flex:1;height:2px;background:var(--ink)}
  #cpSubmit .section-rule .label{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;white-space:nowrap;color:var(--ink)}
  #cpSubmit .notice-box{border:2px solid var(--ink);background:var(--cream);padding:.75rem 1rem;margin-bottom:1.5rem;display:flex;gap:.65rem;align-items:flex-start}
  #cpSubmit .notice-box .nt{font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--red);white-space:nowrap;margin-top:2px}
  #cpSubmit .notice-box p{font-family:var(--fb);font-size:.82rem;color:var(--ink3);line-height:1.55;margin:0}
  /* success */
  #cpSubmit .success-box{border-left:4px solid #166534;background:rgba(22,101,52,.07);padding:.75rem 1rem;margin-bottom:1.2rem;font-family:var(--fm);font-size:12px;color:#166534;font-weight:700;letter-spacing:.5px}
  /* error */
  #cpSubmit .error-box{border-left:4px solid var(--red);background:rgba(185,28,28,.06);padding:.75rem 1rem;margin-bottom:1.2rem}
  #cpSubmit .error-box p{font-family:var(--fm);font-size:11px;color:var(--red);margin:0;font-weight:700}
  /* form field */
  #cpSubmit .field{margin-bottom:1.2rem}
  #cpSubmit .field label{display:block;font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--ink3);margin-bottom:.4rem}
  #cpSubmit .field label .req{color:var(--red);margin-left:2px}
  #cpSubmit .field select,
  #cpSubmit .field input[type="text"],
  #cpSubmit .field textarea{width:100%;border:2px solid var(--rule2);background:var(--cream);color:var(--ink);font-family:var(--fb);font-size:.92rem;padding:.6rem .8rem;outline:none;transition:border-color .2s;border-radius:0;appearance:none;-webkit-appearance:none}
  #cpSubmit .field select:focus,
  #cpSubmit .field input[type="text"]:focus,
  #cpSubmit .field textarea:focus{border-color:var(--ink)}
  #cpSubmit .field textarea{resize:vertical;min-height:160px;line-height:1.7}
  #cpSubmit .field .hint{font-family:var(--fm);font-size:9px;color:var(--ink3);margin-top:.3rem;letter-spacing:.5px}
  /* file upload */
  #cpSubmit .file-wrap{border:2px dashed var(--rule2);background:var(--paper2);padding:1rem;text-align:center;cursor:pointer;transition:border-color .2s}
  #cpSubmit .file-wrap:hover{border-color:var(--ink)}
  #cpSubmit .file-wrap input[type="file"]{display:none}
  #cpSubmit .file-wrap .file-label{font-family:var(--fm);font-size:11px;font-weight:700;letter-spacing:1px;color:var(--ink3);cursor:pointer;display:block}
  #cpSubmit .file-wrap .file-name{font-family:var(--fm);font-size:10px;color:var(--red);margin-top:.3rem;display:none}
  /* submit button */
  #cpSubmit .submit-row{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-top:1.5rem;padding-top:1rem;border-top:2px solid var(--ink)}
  #cpSubmit .btn-submit{background:var(--ink);color:var(--paper);border:none;padding:.75rem 2rem;font-family:var(--fm);font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:background .2s}
  #cpSubmit .btn-submit:hover{background:#3d3020}
  #cpSubmit .btn-back{font-family:var(--fm);font-size:10px;font-weight:700;letter-spacing:1px;color:var(--ink3);text-decoration:none;text-transform:uppercase}
  #cpSubmit .btn-back:hover{color:var(--ink)}
  /* type badge colours */
  #cpSubmit .type-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:.5rem;margin-top:.4rem}
  #cpSubmit .type-opt{border:2px solid var(--rule2);padding:.5rem .6rem;cursor:pointer;text-align:center;transition:all .15s;user-select:none}
  #cpSubmit .type-opt.sel{border-color:var(--ink);background:var(--ink);color:var(--paper)}
  #cpSubmit .type-opt .ico{font-size:1.2rem;display:block;margin-bottom:2px}
  #cpSubmit .type-opt .lbl{font-family:var(--fm);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
  #cpSubmit input[name="type"]{display:none}
  @media(max-width:600px){#cpSubmit{margin:-12px}#cpSubmit .type-grid{grid-template-columns:repeat(3,1fr)}}
</style>

<div id="cpSubmit">
  <div class="mast">
    <div class="mast-top">
      <div class="mast-brand">City<span>Pulse</span></div>
      <div class="mast-meta">{{ strtoupper(now()->format('l, d F Y')) }} &nbsp;·&nbsp; {{ __('ui.upd_news_desk') }}</div>
    </div>
    <div class="mast-flag">{{ __('ui.send_news') }}</div>
  </div>

  <div class="wrap">
    <div class="section-rule">
      <div class="line"></div>
      <div class="label">{{ __('ui.send_news') }}</div>
      <div class="line"></div>
    </div>

    @if(session('success'))
      <div class="success-box">✓ &nbsp;{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="error-box">
        @foreach($errors->all() as $error)
          <p>✕ {{ $error }}</p>
        @endforeach
      </div>
    @endif

    <div class="notice-box">
      <span class="nt">Note</span>
      <p>{{ __('ui.submission_sent') }} {{ __('ui.upd_citypulse_desk') }} {{ __('ui.upd_dynamic_module_data') }}</p>
    </div>

    <form method="POST" action="{{ route('user_submissions.store') }}" enctype="multipart/form-data" id="cpSubmitForm">
      @csrf

      {{-- Type picker --}}
      <div class="field">
        <label>{{ __('ui.type') }} <span class="req">*</span></label>
        <div class="type-grid">
          @php
            $types = [
              'story'       => ['ico' => '📖', 'label' => __('ui.type_story')],
              'news'        => ['ico' => '📰', 'label' => __('ui.type_news')],
              'blog'        => ['ico' => '✍️',  'label' => __('ui.type_blog')],
              'analysis'    => ['ico' => '📊', 'label' => __('ui.type_analysis')],
              'information' => ['ico' => 'ℹ️',  'label' => __('ui.type_information')],
            ];
            $oldType = old('type', 'story');
          @endphp
          @foreach($types as $val => $t)
            <div class="type-opt {{ $oldType === $val ? 'sel' : '' }}" onclick="cpPickType('{{ $val }}', this)">
              <span class="ico">{{ $t['ico'] }}</span>
              <span class="lbl">{{ $t['label'] }}</span>
            </div>
          @endforeach
        </div>
        <input type="hidden" name="type" id="cpTypeInput" value="{{ $oldType }}" required>
      </div>

      {{-- Title --}}
      <div class="field">
        <label for="cp_title">{{ __('ui.title') }} <span class="req">*</span></label>
        <input type="text" name="title" id="cp_title" value="{{ old('title') }}" required maxlength="255" placeholder="{{ __('ui.title') }}...">
      </div>

      {{-- Content --}}
      <div class="field">
        <label for="cp_content">{{ __('ui.content') }} <span class="req">*</span></label>
        <textarea name="content" id="cp_content" required>{{ old('content') }}</textarea>
        <div class="hint">{{ __('ui.upd_publish_updates_note') }}</div>
      </div>

      {{-- Photo --}}
      <div class="field">
        <label>{{ __('ui.photo') }} <em style="font-family:var(--fm);font-size:9px;font-weight:400;letter-spacing:.5px;text-transform:lowercase">({{ __('ui.optional') }})</em></label>
        <div class="file-wrap" onclick="document.getElementById('cp_photo').click()">
          <input type="file" name="photo" id="cp_photo" accept="image/*" onchange="cpShowFileName(this)">
          <label class="file-label" for="cp_photo">📷 &nbsp;{{ __('ui.photo') }} — JPG / PNG · max 2 MB</label>
          <div class="file-name" id="cpFileName"></div>
        </div>
      </div>

      <div class="submit-row">
        <a href="{{ url()->previous() }}" class="btn-back">← {{ __('ui.cancel') ?? 'Back' }}</a>
        <button type="submit" class="btn-submit">{{ __('ui.submit') }} →</button>
      </div>
    </form>
  </div>
</div>

<script>
function cpPickType(val, el) {
  document.querySelectorAll('#cpSubmit .type-opt').forEach(o => o.classList.remove('sel'));
  el.classList.add('sel');
  document.getElementById('cpTypeInput').value = val;
}
function cpShowFileName(input) {
  var fn = document.getElementById('cpFileName');
  if (input.files && input.files[0]) {
    fn.textContent = '📎 ' + input.files[0].name;
    fn.style.display = 'block';
  }
}
</script>
@endsection
