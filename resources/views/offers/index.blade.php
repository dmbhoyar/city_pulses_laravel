@extends('layouts.app')

@section('title', __('ui.offers_benefits') . ' · CityPulse')

@php
  $userPoints  = Auth::check() ? (int)(Auth::user()->ruby_points ?? 0) : 0;
  $redeemedIds = [];
  if (Auth::check() && isset($userRedemptions)) {
      foreach ($userRedemptions as $r) {
          if (str_starts_with($r->coupon_code, 'coupon_'))
              $redeemedIds[] = (int) str_replace('coupon_', '', $r->coupon_code);
          if (str_starts_with($r->coupon_code, 'offer_'))
              $redeemedIds[] = 'offer_' . str_replace('offer_', '', $r->coupon_code);
      }
  }
@endphp

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0}
#cpHub{
  --r:#e11d7a;--r2:#c0005a;--r3:#8b003f;
  --g:#10b981;--g2:#059669;
  --b:#6366f1;--b2:#4f46e5;
  --y:#f59e0b;--y2:#d97706;
  --dark:#0f0a1e;--dark2:#1a1033;--dark3:#241847;
  --mid:#4b3b7c;--muted:#9f8fc0;
  --surface:rgba(255,255,255,.06);--surface2:rgba(255,255,255,.1);
  --border:rgba(255,255,255,.12);
  --white:#fff;
  --ff:'Inter',sans-serif;
  --fh:'Syne',sans-serif;
  background:var(--dark);color:var(--white);font-family:var(--ff);
  margin:-22px -22px -22px -22px;overflow:hidden;min-height:100vh;padding-bottom:4rem;
  position:relative
}
#cpHub *{box-sizing:border-box}
#cpHub a{text-decoration:none;color:inherit}

/* ── animated BG ── */
#cpHub .bg-orbs{position:absolute;inset:0;pointer-events:none;z-index:0;overflow:hidden}
#cpHub .orb{position:absolute;border-radius:50%;filter:blur(80px);opacity:.35;animation:orb-drift 20s ease-in-out infinite}
#cpHub .orb-1{width:600px;height:600px;background:var(--r);top:-200px;left:-200px;animation-delay:0s}
#cpHub .orb-2{width:500px;height:500px;background:var(--b);bottom:-150px;right:-100px;animation-delay:7s}
#cpHub .orb-3{width:400px;height:400px;background:var(--y);top:40%;left:40%;animation-delay:14s;opacity:.18}
@keyframes orb-drift{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(60px,80px) scale(1.1)}66%{transform:translate(-40px,-60px) scale(.9)}}

/* ── hero ── */
#cpHub .hero{position:relative;z-index:1;padding:3rem 1.5rem 2rem;text-align:center}
#cpHub .hero-eyebrow{display:inline-flex;align-items:center;gap:.5rem;background:var(--surface);border:1px solid var(--border);padding:.35rem 1rem;border-radius:100px;font-size:.7rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:1.2rem}
#cpHub .hero-eyebrow span{width:6px;height:6px;border-radius:50%;background:var(--r);animation:pulse-dot 1.5s ease-in-out infinite}
@keyframes pulse-dot{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.7}}
#cpHub .hero-title{font-family:var(--fh);font-size:clamp(2.2rem,7vw,4.5rem);font-weight:800;line-height:1.05;letter-spacing:-1.5px;margin-bottom:.8rem;background:linear-gradient(135deg,#fff 0%,#f9a8d4 50%,var(--y) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
#cpHub .hero-sub{font-size:.95rem;color:var(--muted);max-width:480px;margin:0 auto 1.8rem;line-height:1.65}

/* ── points pill ── */
#cpHub .points-pill{display:inline-flex;align-items:center;gap:.75rem;background:linear-gradient(135deg,rgba(225,29,122,.25),rgba(99,102,241,.25));border:1px solid rgba(225,29,122,.4);backdrop-filter:blur(12px);padding:.65rem 1.4rem;border-radius:100px;margin-bottom:2rem;flex-wrap:wrap;justify-content:center}
#cpHub .pp-gem{font-size:1.5rem;animation:gem-spin 4s linear infinite}
@keyframes gem-spin{0%,100%{transform:rotateY(0)}50%{transform:rotateY(180deg)}}
#cpHub .pp-val{font-family:var(--fh);font-size:1.6rem;font-weight:800;color:#fff}
#cpHub .pp-lbl{font-size:.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted)}
#cpHub .pp-sep{width:1px;height:24px;background:var(--border)}
#cpHub .pp-earn{font-size:.72rem;color:#f9a8d4;line-height:1.4;text-align:left}
#cpHub .pp-earn strong{color:var(--y)}

/* ── search bar ── */
#cpHub .search-bar{max-width:520px;margin:0 auto 1.5rem;position:relative;z-index:1}
#cpHub .search-bar input{width:100%;background:var(--surface2);border:1.5px solid var(--border);color:#fff;font-family:var(--ff);font-size:.92rem;padding:.75rem 1rem .75rem 3rem;border-radius:14px;outline:none;transition:all .2s}
#cpHub .search-bar input::placeholder{color:var(--muted)}
#cpHub .search-bar input:focus{border-color:var(--r);background:rgba(255,255,255,.13);box-shadow:0 0 0 4px rgba(225,29,122,.15)}
#cpHub .search-bar .si{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:1.1rem}

/* ── filter chips ── */
#cpHub .chips{display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap;margin-bottom:2rem;z-index:1;position:relative}
#cpHub .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.4rem 1rem;border-radius:100px;border:1.5px solid var(--border);background:var(--surface);color:var(--muted);font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;white-space:nowrap}
#cpHub .chip:hover{border-color:rgba(225,29,122,.5);color:#fff;background:rgba(225,29,122,.12)}
#cpHub .chip.on{background:linear-gradient(135deg,var(--r),var(--b2));border-color:transparent;color:#fff;box-shadow:0 4px 16px rgba(225,29,122,.35)}
#cpHub .chip .chip-cnt{font-size:.65rem;background:rgba(255,255,255,.2);padding:1px 5px;border-radius:100px;font-weight:700}

/* ── section header ── */
#cpHub .sec-hd{display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem}
#cpHub .sec-hd h2{font-family:var(--fh);font-size:1.25rem;font-weight:800;white-space:nowrap}
#cpHub .sec-hd .line{flex:1;height:1px;background:var(--border)}
#cpHub .sec-hd .badge{font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;background:linear-gradient(135deg,var(--r),var(--b2));padding:3px 10px;border-radius:100px}

/* ── cards grid ── */
#cpHub .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.2rem;position:relative;z-index:1}

/* ── card ── */
#cpHub .ocard{background:var(--dark2);border:1.5px solid var(--border);border-radius:20px;overflow:hidden;transition:all .25s;cursor:default;display:flex;flex-direction:column;position:relative}
#cpHub .ocard:hover{border-color:rgba(225,29,122,.5);transform:translateY(-4px);box-shadow:0 20px 48px rgba(0,0,0,.4),0 0 0 1px rgba(225,29,122,.2)}
#cpHub .ocard-glow{position:absolute;inset:0;border-radius:20px;background:radial-gradient(circle at 50% 0%,rgba(225,29,122,.12),transparent 70%);pointer-events:none;opacity:0;transition:opacity .25s}
#cpHub .ocard:hover .ocard-glow{opacity:1}

/* card image */
#cpHub .ocard-img{width:100%;height:150px;object-fit:cover;display:block}
#cpHub .ocard-img-ph{width:100%;height:110px;display:flex;align-items:center;justify-content:center;font-size:2.8rem;background:linear-gradient(135deg,var(--dark3),var(--mid))}

/* store badge */
#cpHub .ocard-store{display:inline-flex;align-items:center;gap:.35rem;font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:3px 9px;border-radius:100px;margin-bottom:.55rem}
#cpHub .ocard-store.local{background:rgba(225,29,122,.18);color:#f9a8d4;border:1px solid rgba(225,29,122,.3)}
#cpHub .ocard-store.amazon{background:rgba(245,158,11,.18);color:#fcd34d;border:1px solid rgba(245,158,11,.3)}
#cpHub .ocard-store.default{background:var(--surface);color:var(--muted);border:1px solid var(--border)}

/* card body */
#cpHub .ocard-body{padding:1rem 1.1rem 1.1rem;flex:1;display:flex;flex-direction:column;gap:.4rem}
#cpHub .ocard-title{font-size:.92rem;font-weight:700;line-height:1.35;color:#fff}
#cpHub .ocard-desc{font-size:.78rem;color:var(--muted);line-height:1.55;flex:1}

/* points badge */
#cpHub .pts-badge{display:inline-flex;align-items:center;gap:.4rem;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);padding:4px 10px;border-radius:100px;font-size:.75rem;font-weight:700;color:var(--y);width:fit-content}

/* code display */
#cpHub .code-lock{display:flex;align-items:center;gap:.5rem;background:var(--dark3);border:1px dashed rgba(255,255,255,.15);padding:.45rem .75rem;border-radius:10px;font-size:.78rem;color:var(--muted)}
#cpHub .code-reveal{background:linear-gradient(135deg,rgba(16,185,129,.15),rgba(16,185,129,.08));border:1px solid rgba(16,185,129,.3);padding:.45rem .75rem;border-radius:10px;font-size:.85rem;font-weight:700;color:#6ee7b7;text-align:center;letter-spacing:1.5px;cursor:pointer}
#cpHub .code-reveal:hover{background:rgba(16,185,129,.25)}
#cpHub .copied-tip{font-size:.7rem;color:var(--g);margin-top:2px;display:none}

/* redeemed state */
#cpHub .redeemed-badge{display:flex;align-items:center;gap:.4rem;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);padding:5px 10px;border-radius:10px;font-size:.73rem;font-weight:700;color:var(--g)}

/* action buttons */
#cpHub .ocard-actions{display:flex;gap:.5rem;margin-top:.3rem;flex-wrap:wrap}
#cpHub .btn-redeem{flex:1;min-width:100px;padding:.6rem .8rem;border:none;border-radius:12px;font-family:var(--ff);font-size:.78rem;font-weight:700;cursor:pointer;background:linear-gradient(135deg,var(--r),var(--r3));color:#fff;transition:all .2s;box-shadow:0 4px 14px rgba(225,29,122,.3)}
#cpHub .btn-redeem:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 8px 22px rgba(225,29,122,.45)}
#cpHub .btn-redeem:disabled{background:rgba(255,255,255,.08);color:var(--muted);cursor:not-allowed;box-shadow:none}
#cpHub .btn-shop{display:inline-flex;align-items:center;justify-content:center;gap:.3rem;padding:.58rem .8rem;border-radius:12px;border:1.5px solid var(--border);background:var(--surface);color:var(--muted);font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none}
#cpHub .btn-shop:hover{border-color:var(--b);color:#a5b4fc;background:rgba(99,102,241,.1)}
#cpHub .btn-login-cta{flex:1;padding:.6rem;border-radius:12px;border:none;background:linear-gradient(135deg,var(--r),var(--b2));color:#fff;font-size:.78rem;font-weight:700;cursor:pointer;text-align:center;display:block}

/* admin controls */
#cpHub .admin-row{display:flex;gap:.4rem;margin-top:.3rem;flex-wrap:wrap}
#cpHub .btn-adm{padding:3px 9px;border-radius:8px;font-size:.65rem;font-weight:700;cursor:pointer;border:1.5px solid;transition:all .15s}
#cpHub .btn-adm-pts{border-color:rgba(245,158,11,.35);color:var(--y);background:rgba(245,158,11,.08)}
#cpHub .btn-adm-pts:hover{background:rgba(245,158,11,.2)}
#cpHub .btn-adm-del{border-color:rgba(239,68,68,.35);color:#f87171;background:rgba(239,68,68,.08)}
#cpHub .btn-adm-del:hover{background:rgba(239,68,68,.2)}

/* expiry */
#cpHub .ocard-expiry{font-size:.68rem;color:var(--muted);margin-top:.25rem}

/* ── my redemptions ── */
#cpHub .my-section{margin-top:3rem;position:relative;z-index:1}
#cpHub .redemption-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.9rem}
#cpHub .rcard{background:var(--dark2);border:1.5px solid rgba(16,185,129,.2);border-radius:16px;padding:.9rem 1rem;transition:all .2s}
#cpHub .rcard:hover{border-color:rgba(16,185,129,.4);transform:translateY(-2px)}
#cpHub .rcard-store{font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--g);margin-bottom:.3rem}
#cpHub .rcard-title{font-size:.82rem;font-weight:600;color:#fff;margin-bottom:.45rem;line-height:1.3}
#cpHub .rcard-code{font-size:.78rem;font-weight:700;color:#6ee7b7;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);padding:4px 9px;border-radius:8px;display:inline-block;letter-spacing:1.5px;margin-bottom:.3rem}
#cpHub .rcard-date{font-size:.65rem;color:var(--muted)}

/* ── main container ── */
#cpHub .wrap{max-width:1280px;margin:0 auto;padding:0 1.5rem;position:relative;z-index:1}

/* ── empty ── */
#cpHub .empty-state{text-align:center;padding:3rem 1rem;color:var(--muted)}
#cpHub .empty-state .emo{font-size:3rem;margin-bottom:.75rem}
#cpHub .empty-state p{font-size:.88rem;line-height:1.6}

/* ── toast ── */
#cpToast2{position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%) translateY(60px);z-index:9999;background:#fff;color:var(--dark);padding:.65rem 1.4rem;border-radius:100px;font-size:.82rem;font-weight:700;box-shadow:0 8px 32px rgba(0,0,0,.25);opacity:0;transition:all .3s;white-space:nowrap;display:flex;align-items:center;gap:.5rem}
#cpToast2.show{transform:translateX(-50%) translateY(0);opacity:1}
#cpToast2.err{background:#ef4444;color:#fff}
#cpToast2.ok{background:var(--g);color:#fff}

/* ── modal ── */
#cpModal2{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(8px);z-index:99999;align-items:center;justify-content:center;padding:1rem}
#cpModal2.open{display:flex}
#cpModal2 .mbox{background:var(--dark2);border:1.5px solid var(--border);border-radius:24px;padding:2rem;max-width:380px;width:100%;position:relative}
#cpModal2 .mbox-close{position:absolute;top:1rem;right:1rem;width:32px;height:32px;border-radius:50%;background:var(--surface);border:none;color:var(--muted);font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s}
#cpModal2 .mbox-close:hover{background:rgba(239,68,68,.2);color:#f87171}
#cpModal2 .mbox-title{font-family:var(--fh);font-size:1.15rem;font-weight:800;margin-bottom:.5rem}
#cpModal2 .mbox-body{font-size:.85rem;color:var(--muted);margin-bottom:1.2rem;line-height:1.55}
#cpModal2 .mbox-input{width:100%;background:var(--dark3);border:1.5px solid var(--border);color:#fff;font-family:var(--ff);font-size:1rem;padding:.6rem .9rem;border-radius:12px;outline:none;margin-bottom:1rem}
#cpModal2 .mbox-input:focus{border-color:var(--r)}
#cpModal2 .mbox-btns{display:flex;gap:.6rem}
#cpModal2 .mbox-ok{flex:1;padding:.65rem;border:none;border-radius:12px;background:linear-gradient(135deg,var(--r),var(--r3));color:#fff;font-size:.85rem;font-weight:700;cursor:pointer}
#cpModal2 .mbox-cancel{flex:1;padding:.65rem;border:1.5px solid var(--border);border-radius:12px;background:transparent;color:var(--muted);font-size:.85rem;font-weight:600;cursor:pointer;transition:all .15s}
#cpModal2 .mbox-cancel:hover{border-color:rgba(255,255,255,.3);color:#fff}

/* ── login prompt card ── */
#cpHub .login-card{background:linear-gradient(135deg,rgba(225,29,122,.2),rgba(99,102,241,.2));border:1.5px solid rgba(225,29,122,.3);border-radius:20px;padding:1.5rem;text-align:center;margin-bottom:2rem}
#cpHub .login-card h3{font-family:var(--fh);font-size:1.2rem;font-weight:800;margin-bottom:.4rem}
#cpHub .login-card p{font-size:.82rem;color:var(--muted);margin-bottom:1rem;line-height:1.55}
#cpHub .login-card .lc-btns{display:flex;gap:.7rem;justify-content:center;flex-wrap:wrap}
#cpHub .btn-primary-cta{padding:.65rem 1.8rem;border-radius:100px;border:none;background:linear-gradient(135deg,var(--r),var(--b2));color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;text-decoration:none;display:inline-block;box-shadow:0 4px 16px rgba(225,29,122,.3)}
#cpHub .btn-secondary-cta{padding:.62rem 1.6rem;border-radius:100px;border:1.5px solid var(--border);background:transparent;color:var(--muted);font-size:.85rem;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block;transition:all .2s}
#cpHub .btn-secondary-cta:hover{border-color:rgba(255,255,255,.3);color:#fff}

/* ── how to earn section ── */
#cpHub .earn-section{margin-bottom:2.5rem;position:relative;z-index:1}
#cpHub .earn-toggle{display:flex;align-items:center;gap:.75rem;cursor:pointer;background:linear-gradient(135deg,rgba(99,102,241,.18),rgba(225,29,122,.12));border:1.5px solid rgba(99,102,241,.3);border-radius:16px;padding:.85rem 1.2rem;transition:all .2s;user-select:none}
#cpHub .earn-toggle:hover{border-color:rgba(99,102,241,.55);background:linear-gradient(135deg,rgba(99,102,241,.25),rgba(225,29,122,.18))}
#cpHub .earn-toggle-icon{font-size:1.4rem}
#cpHub .earn-toggle-text{flex:1}
#cpHub .earn-toggle-text h3{font-family:var(--fh);font-size:1rem;font-weight:800;margin-bottom:.1rem}
#cpHub .earn-toggle-text p{font-size:.72rem;color:var(--muted)}
#cpHub .earn-toggle-arrow{font-size:.9rem;color:var(--muted);transition:transform .25s}
#cpHub .earn-toggle.open .earn-toggle-arrow{transform:rotate(180deg)}
#cpHub .earn-body{display:none;padding:1.2rem 0 .4rem}
#cpHub .earn-body.open{display:block}
#cpHub .earn-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1rem;margin-bottom:1rem}
#cpHub .earn-card{background:var(--dark2);border:1.5px solid var(--border);border-radius:16px;padding:1rem 1.1rem;display:flex;flex-direction:column;gap:.55rem}
#cpHub .earn-card-head{display:flex;align-items:center;gap:.6rem}
#cpHub .earn-card-ico{font-size:1.5rem;line-height:1}
#cpHub .earn-card-title{font-family:var(--fh);font-size:.85rem;font-weight:800}
#cpHub .earn-card-sub{font-size:.7rem;color:var(--muted);line-height:1.45}
#cpHub .earn-rows{display:flex;flex-direction:column;gap:.35rem}
#cpHub .earn-row{display:flex;align-items:center;justify-content:space-between;gap:.5rem;font-size:.75rem}
#cpHub .earn-row-label{color:var(--muted)}
#cpHub .earn-row-pts{font-weight:800;color:var(--y);background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.25);padding:2px 8px;border-radius:100px;white-space:nowrap}
#cpHub .earn-tier-strip{display:flex;gap:.6rem;flex-wrap:wrap;margin-top:.2rem}
#cpHub .tier-pill{display:inline-flex;align-items:center;gap:.3rem;padding:4px 12px;border-radius:100px;font-size:.7rem;font-weight:700;border:1px solid}
#cpHub .tier-silver{background:rgba(148,163,184,.12);border-color:rgba(148,163,184,.3);color:#cbd5e1}
#cpHub .tier-gold{background:rgba(245,158,11,.12);border-color:rgba(245,158,11,.3);color:#fcd34d}
#cpHub .tier-diamond{background:rgba(99,102,241,.12);border-color:rgba(99,102,241,.3);color:#a5b4fc}
#cpHub .tier-red{background:rgba(225,29,122,.12);border-color:rgba(225,29,122,.3);color:#f9a8d4}
#cpHub .earn-note{font-size:.7rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:.5rem .8rem;line-height:1.5}

/* ── delete expired btn ── */
#cpHub .top-admin-bar{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:12px;padding:.6rem 1rem;display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap}
#cpHub .top-admin-bar span{font-size:.78rem;color:#f87171;flex:1}
#cpHub .btn-del-exp{padding:.45rem 1rem;border:1.5px solid rgba(239,68,68,.4);border-radius:8px;background:transparent;color:#f87171;font-size:.75rem;font-weight:700;cursor:pointer;transition:all .2s}
#cpHub .btn-del-exp:hover{background:rgba(239,68,68,.15)}

/* ── address modal select options ── */
#addrModal select option { background:#1a1033; color:#fff; }
#addrModal { display:none; }
#addrModal.open { display:flex; }

/* ── responsive ── */
@media(max-width:900px){#cpHub{margin:-12px}}
@media(max-width:640px){#cpHub .grid{grid-template-columns:1fr 1fr}#cpHub .chips{gap:.35rem}#cpHub .chip{font-size:.7rem;padding:.35rem .8rem}}
@media(max-width:420px){#cpHub .grid{grid-template-columns:1fr}}
</style>

<div id="cpHub">
  <div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
  </div>

  {{-- Toast --}}
  <div id="cpToast2"></div>

  {{-- Modal --}}
  <div id="cpModal2" onclick="if(event.target===this)cp2CloseModal()">
    <div class="mbox">
      <button class="mbox-close" onclick="cp2CloseModal()">✕</button>
      <div class="mbox-title" id="cp2ModalTitle"></div>
      <div class="mbox-body" id="cp2ModalBody"></div>
      <input class="mbox-input" type="number" id="cp2PtsInput" min="1" style="display:none">
      <div class="mbox-btns" id="cp2ModalBtns"></div>
    </div>
  </div>

  {{-- Address Modal --}}
  <div id="addrModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);backdrop-filter:blur(10px);z-index:99999;align-items:center;justify-content:center;padding:1rem;overflow-y:auto" onclick="if(event.target===this)closeAddrModal()">
    <div style="background:#1a1033;border:1.5px solid rgba(255,255,255,.12);border-radius:24px;padding:0;max-width:520px;width:100%;position:relative;max-height:92vh;overflow-y:auto">

      {{-- Modal header --}}
      <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:20px 24px;border-radius:22px 22px 0 0;position:sticky;top:0;z-index:1">
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div>
            <div style="font-size:.7rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.7);margin-bottom:4px">📦 Product Order</div>
            <div style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#fff" id="addrModalTitle">Enter Delivery Address</div>
          </div>
          <button onclick="closeAddrModal()" style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0">✕</button>
        </div>
        {{-- Offer info strip --}}
        <div id="addrOfferStrip" style="margin-top:12px;background:rgba(0,0,0,.25);border-radius:10px;padding:8px 12px;font-size:.8rem;color:rgba(255,255,255,.85)"></div>
      </div>

      <div style="padding:20px 24px 24px">
        {{-- Points warning --}}
        <div id="addrPtsWarning" style="display:none;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:.82rem;color:#fcd34d;display:flex;align-items:center;gap:8px">
          <span>💎</span><span id="addrPtsText"></span>
        </div>

        <form id="addrForm" autocomplete="on">
          <input type="hidden" id="addrOfferId">

          {{-- Contact info --}}
          <div style="font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9f8fc0;margin-bottom:10px;display:flex;align-items:center;gap:6px">
            <span style="flex:1;height:1px;background:rgba(255,255,255,.08)"></span> Contact Info <span style="flex:1;height:1px;background:rgba(255,255,255,.08)"></span>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
            <div style="display:flex;flex-direction:column;gap:5px">
              <label style="font-size:.73rem;font-weight:600;color:#9f8fc0">Full Name *</label>
              <input id="addr_name" name="delivery_name" type="text" autocomplete="name" placeholder="Your full name" required style="background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:9px 12px;font-size:.88rem;outline:none;transition:border-color .15s" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
            </div>
            <div style="display:flex;flex-direction:column;gap:5px">
              <label style="font-size:.73rem;font-weight:600;color:#9f8fc0">Phone Number *</label>
              <input id="addr_phone" name="delivery_phone" type="tel" autocomplete="tel" placeholder="+91 98765 43210" required style="background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:9px 12px;font-size:.88rem;outline:none;transition:border-color .15s" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
            </div>
          </div>

          {{-- Address --}}
          <div style="font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9f8fc0;margin-bottom:10px;display:flex;align-items:center;gap:6px">
            <span style="flex:1;height:1px;background:rgba(255,255,255,.08)"></span> Delivery Address <span style="flex:1;height:1px;background:rgba(255,255,255,.08)"></span>
          </div>

          <div style="display:flex;flex-direction:column;gap:10px">
            <div style="display:flex;flex-direction:column;gap:5px">
              <label style="font-size:.73rem;font-weight:600;color:#9f8fc0">Address Line 1 * <span style="color:#6b7280;font-weight:400">(House / Flat / Street)</span></label>
              <input id="addr_line1" name="delivery_address1" type="text" autocomplete="address-line1" placeholder="House no., Building, Street" required style="background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:9px 12px;font-size:.88rem;outline:none;transition:border-color .15s" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
            </div>
            <div style="display:flex;flex-direction:column;gap:5px">
              <label style="font-size:.73rem;font-weight:600;color:#9f8fc0">Address Line 2 <span style="color:#6b7280;font-weight:400">(optional)</span></label>
              <input id="addr_line2" name="delivery_address2" type="text" autocomplete="address-line2" placeholder="Area, Colony, Locality" style="background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:9px 12px;font-size:.88rem;outline:none;transition:border-color .15s" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
              <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:.73rem;font-weight:600;color:#9f8fc0">City / Town *</label>
                <input id="addr_city" name="delivery_city" type="text" autocomplete="address-level2" placeholder="City" required style="background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:9px 12px;font-size:.88rem;outline:none;transition:border-color .15s" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
              </div>
              <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:.73rem;font-weight:600;color:#9f8fc0">PIN Code *</label>
                <input id="addr_pin" name="delivery_pincode" type="text" autocomplete="postal-code" placeholder="400001" required maxlength="10" style="background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:9px 12px;font-size:.88rem;outline:none;transition:border-color .15s" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
              <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:.73rem;font-weight:600;color:#9f8fc0">State *</label>
                <select id="addr_state" name="delivery_state" required style="background:#1a1033;border:1.5px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:9px 12px;font-size:.88rem;outline:none;transition:border-color .15s" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
                  <option value="">Select state</option>
                  @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Delhi','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal'] as $st)
                    <option value="{{ $st }}">{{ $st }}</option>
                  @endforeach
                </select>
              </div>
              <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:.73rem;font-weight:600;color:#9f8fc0">Landmark <span style="color:#6b7280;font-weight:400">(optional)</span></label>
                <input id="addr_landmark" name="delivery_landmark" type="text" placeholder="Near school, temple..." style="background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:9px 12px;font-size:.88rem;outline:none;transition:border-color .15s" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
              </div>
            </div>
          </div>

          {{-- Error msg --}}
          <div id="addrError" style="display:none;margin-top:12px;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:9px 14px;font-size:.8rem;color:#fca5a5"></div>

          {{-- Submit --}}
          <div style="margin-top:18px;display:flex;gap:10px">
            <button type="submit" id="addrSubmitBtn" style="flex:1;padding:.75rem;border:none;border-radius:14px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;font-family:'Inter',sans-serif;font-size:.88rem;font-weight:700;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px">
              <span id="addrSubmitIcon">📦</span> <span id="addrSubmitText">Place Order</span>
            </button>
            <button type="button" onclick="closeAddrModal()" style="padding:.75rem 1.2rem;border:1.5px solid rgba(255,255,255,.15);border-radius:14px;background:transparent;color:#9f8fc0;font-size:.85rem;font-weight:600;cursor:pointer">Cancel</button>
          </div>

          <div style="margin-top:12px;text-align:center;font-size:.72rem;color:#6b7280;line-height:1.5">
            🔒 Your address is stored securely and only used for this delivery
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Hero --}}
  <div class="hero">
    <div class="hero-eyebrow"><span></span> {{ __('ui.offers_rewards') }}</div>
    <h1 class="hero-title">{{ __('ui.offers_benefits') }}</h1>
    <p class="hero-sub">{{ __('ui.redeem_hero_desc') }}</p>

    {{-- Points pill --}}
    @auth
      <div class="points-pill">
        <span class="pp-gem">💎</span>
        <div>
          <div class="pp-val" id="cpPtsVal">{{ number_format($userPoints) }}</div>
          <div class="pp-lbl">{{ __('ui.ruby_points') }}</div>
        </div>
        <div class="pp-sep"></div>
        <div class="pp-earn">
          {!! __('ui.offers_points_earn_story') !!}<br>
          {!! __('ui.offers_points_earn_shortsplay') !!}
        </div>
      </div>
      <div style="margin-bottom:1.5rem">
        <a href="{{ route('offers.my_orders') }}" style="display:inline-flex;align-items:center;gap:.5rem;padding:.55rem 1.4rem;border-radius:100px;background:linear-gradient(135deg,rgba(99,102,241,.35),rgba(139,92,246,.35));border:1.5px solid rgba(99,102,241,.45);color:#a5b4fc;font-size:.82rem;font-weight:700;text-decoration:none;transition:all .2s" onmouseover="this.style.background='linear-gradient(135deg,rgba(99,102,241,.5),rgba(139,92,246,.5))'" onmouseout="this.style.background='linear-gradient(135deg,rgba(99,102,241,.35),rgba(139,92,246,.35))'">
          📦 My Orders &amp; Redemptions →
        </a>
      </div>
    @else
      <div style="margin-bottom:2rem">
        <a href="{{ route('login') }}" class="btn-primary-cta" style="margin-right:.5rem">{{ __('ui.login_to_redeem') }} →</a>
        <a href="{{ route('register') }}" class="btn-secondary-cta">{{ __('ui.register_free') }}</a>
      </div>
    @endauth

    {{-- Search --}}
    <div class="search-bar">
      <span class="si">🔍</span>
      <input type="text" id="cpSearch" placeholder="{{ __('ui.offers_search_placeholder') }}">
    </div>

    {{-- Filter chips --}}
    @php
      $totalCount = $offers->count() + $apiCoupons->count();
      $catCounts  = ['shopping' => 0, 'food' => 0, 'travel' => 0, 'entertainment' => 0];
      foreach ($apiCoupons as $c) {
          $cat = strtolower($c->category ?? 'shopping');
          if (isset($catCounts[$cat])) $catCounts[$cat]++;
      }
    @endphp
    <div class="chips">
      <div class="chip on" data-filter="all">⭐ {{ __('ui.all') }} <span class="chip-cnt">{{ $totalCount }}</span></div>
      <div class="chip" data-filter="affordable">💰 {{ __('ui.i_can_afford') }}</div>
      <div class="chip" data-filter="shopping">🛍 {{ __('ui.shopping') }} <span class="chip-cnt">{{ $catCounts['shopping'] }}</span></div>
      <div class="chip" data-filter="food">🍔 {{ __('ui.food_and_dining') }} <span class="chip-cnt">{{ $catCounts['food'] }}</span></div>
      <div class="chip" data-filter="travel">✈️ {{ __('ui.travel') }} <span class="chip-cnt">{{ $catCounts['travel'] }}</span></div>
      <div class="chip" data-filter="entertainment">🎬 {{ __('ui.entertainment') }} <span class="chip-cnt">{{ $catCounts['entertainment'] }}</span></div>
    </div>
  </div>

  <div class="wrap">
    {{-- Superadmin bar --}}
    @if(Auth::check() && Auth::user()->isSuperadmin())
      <div class="top-admin-bar">
        <span>🛡 {{ __('ui.offers_superadmin_mode') }}</span>
        <button class="btn-del-exp" onclick="cp2DeleteExpired()">🗑 {{ __('ui.offers_delete_expired_coupons') }}</button>
      </div>
    @endif

    {{-- Guest login card --}}
    @guest
      <div class="login-card">
        <h3>💎 {{ __('ui.offers_unlock_exclusive_deals') }}</h3>
        <p>{{ __('ui.offers_login_to_redeem_desc') }}</p>
        <div class="lc-btns">
          <a href="{{ route('login') }}" class="btn-primary-cta">{{ __('ui.login_now') }}</a>
          <a href="{{ route('register') }}" class="btn-secondary-cta">{{ __('ui.create_account') }}</a>
        </div>
      </div>
    @endguest

    {{-- How to Earn Points --}}
    <div class="earn-section">
      <div class="earn-toggle" id="earnToggle" onclick="toggleEarn()">
        <div class="earn-toggle-icon">💎</div>
        <div class="earn-toggle-text">
          <h3>{{ __('ui.offers_how_earn_points') }}</h3>
          <p>{{ __('ui.offers_how_earn_points_sub') }}</p>
        </div>
        <div class="earn-toggle-arrow" id="earnArrow">▼</div>
      </div>
      <div class="earn-body" id="earnBody">
        <div class="earn-grid">

          {{-- Viewer --}}
          <div class="earn-card">
            <div class="earn-card-head">
              <div class="earn-card-ico">📺</div>
              <div class="earn-card-title">{{ __('ui.offers_watch_shortsplay') }}</div>
            </div>
            <div class="earn-card-sub">{{ __('ui.offers_watch_shortsplay_sub') }}</div>
            <div class="earn-rows">
              <div class="earn-row"><span class="earn-row-label">▶ {{ __('ui.offers_watch_video') }}</span><span class="earn-row-pts">+1 {{ __('ui.pt') }}</span></div>
              <div class="earn-row"><span class="earn-row-label">❤️ {{ __('ui.offers_like_video') }}</span><span class="earn-row-pts">+1 {{ __('ui.pt') }}</span></div>
              <div class="earn-row"><span class="earn-row-label">💬 {{ __('ui.offers_comment_video') }}</span><span class="earn-row-pts">+1 {{ __('ui.pt') }}</span></div>
              <div class="earn-row"><span class="earn-row-label">👤 {{ __('ui.offers_follow_creator') }}</span><span class="earn-row-pts">+2 {{ __('ui.pts') }}</span></div>
            </div>
          </div>

          {{-- Creator --}}
          <div class="earn-card">
            <div class="earn-card-head">
              <div class="earn-card-ico">🎬</div>
              <div class="earn-card-title">{{ __('ui.offers_create_content') }}</div>
            </div>
            <div class="earn-card-sub">{{ __('ui.offers_create_content_sub') }}</div>
            <div class="earn-rows">
              <div class="earn-row"><span class="earn-row-label">❤️ {{ __('ui.offers_video_liked') }}</span><span class="earn-row-pts">+1 {{ __('ui.pt') }}</span></div>
              <div class="earn-row"><span class="earn-row-label">💬 {{ __('ui.offers_someone_comments') }}</span><span class="earn-row-pts">+2 {{ __('ui.pts') }}</span></div>
              <div class="earn-row"><span class="earn-row-label">↗️ {{ __('ui.offers_video_shared') }}</span><span class="earn-row-pts">+3 {{ __('ui.pts') }}</span></div>
              <div class="earn-row"><span class="earn-row-label">🔔 {{ __('ui.offers_someone_subscribes') }}</span><span class="earn-row-pts">+5 {{ __('ui.pts') }}</span></div>
            </div>
          </div>

          {{-- Community --}}
          <div class="earn-card">
            <div class="earn-card-head">
              <div class="earn-card-ico">📰</div>
              <div class="earn-card-title">{{ __('ui.offers_submit_stories') }}</div>
            </div>
            <div class="earn-card-sub">{{ __('ui.offers_submit_stories_sub') }}</div>
            <div class="earn-rows">
              <div class="earn-row"><span class="earn-row-label">✅ {{ __('ui.offers_story_approved') }}</span><span class="earn-row-pts">+10 {{ __('ui.pts') }}</span></div>
              <div class="earn-row"><span class="earn-row-label">📝 {{ __('ui.offers_submission_pending') }}</span><span class="earn-row-pts">{{ __('ui.offers_zero_pts_yet') }}</span></div>
            </div>
            <a href="{{ route('user_submissions.index', ['tab' => 'submit']) }}" class="btn-primary-cta" style="font-size:.72rem;padding:.4rem 1rem;text-align:center;margin-top:.3rem">{{ __('ui.submit_story') }} →</a>
          </div>

          {{-- Reading time --}}
          <div class="earn-card">
            <div class="earn-card-head">
              <div class="earn-card-ico">📖</div>
              <div class="earn-card-title">{{ __('ui.offers_read_explore') }}</div>
            </div>
            <div class="earn-card-sub">{{ __('ui.offers_read_explore_sub') }}</div>
            <div class="earn-rows">
              <div class="earn-row"><span class="earn-row-label">⏱ {{ __('ui.offers_spend_10min') }}</span><span class="earn-row-pts">+2 {{ __('ui.pts') }}</span></div>
              <div class="earn-row"><span class="earn-row-label">🔁 {{ __('ui.offers_resets_daily') }}</span><span class="earn-row-pts">{{ __('ui.offers_every_day') }}</span></div>
            </div>
            <div style="font-size:.68rem;color:var(--muted);margin-top:.2rem;line-height:1.45">{{ __('ui.offers_points_auto_award') }}</div>
          </div>

          {{-- Tiers --}}
          <div class="earn-card">
            <div class="earn-card-head">
              <div class="earn-card-ico">🏆</div>
              <div class="earn-card-title">{{ __('ui.offers_tier_milestones') }}</div>
            </div>
            <div class="earn-card-sub">{{ __('ui.offers_tier_milestones_sub') }}</div>
            <div class="earn-tier-strip">
              <span class="tier-pill tier-silver">🥈 {{ __('ui.offers_tier_silver') }} · 5,000 {{ __('ui.pts') }}</span>
              <span class="tier-pill tier-gold">🥇 {{ __('ui.offers_tier_gold') }} · 10,000 {{ __('ui.pts') }}</span>
              <span class="tier-pill tier-diamond">💠 {{ __('ui.offers_tier_diamond') }} · 15,000 {{ __('ui.pts') }}</span>
              <span class="tier-pill tier-red">❤️ {{ __('ui.offers_tier_red') }} · 20,000 {{ __('ui.pts') }}</span>
            </div>
          </div>

        </div>
        <div class="earn-note">
          💡 <strong>{{ __('ui.offers_note_label') }}:</strong> {{ __('ui.offers_note_text') }}
        </div>
      </div>
    </div>

    {{-- Offers section header --}}
    <div class="sec-hd">
      <h2>🎁 {{ __('ui.offers_local_offers') }}</h2>
      <div class="line"></div>
      <span class="badge">{{ $offers->count() }} {{ __('ui.active') }}</span>
    </div>

    {{-- Cards grid --}}
    <div class="grid" id="cpGrid">

      {{-- ── Local Offers ── --}}
      @forelse($offers as $offer)
        @php
          $pts        = ($offer->points_required > 0) ? (int)$offer->points_required : 200;
          $canAfford  = $userPoints >= $pts;
          $redeemed   = in_array('offer_' . $offer->id, $redeemedIds);
          $isProduct  = ($offer->offer_category ?? 'coupon') === 'product';
        @endphp
        <div class="ocard"
             data-type="offer" data-id="{{ $offer->id }}"
             data-pts="{{ $pts }}"
             data-category="{{ strtolower($offer->update_type ?? 'offer') }}"
             data-q="{{ strtolower($offer->title . ' ' . ($offer->shop?->name ?? '')) }}">
          <div class="ocard-glow"></div>
          @if($offer->photo_path)
            <img class="ocard-img" src="{{ \Illuminate\Support\Facades\Storage::url($offer->photo_path) }}" alt="{{ $offer->title }}" loading="lazy">
          @else
            <div class="ocard-img-ph">{{ $isProduct ? '📦' : '🎁' }}</div>
          @endif
          <div class="ocard-body">
            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.3rem">
              <span class="ocard-store local">{{ $offer->shop?->name ?? __('ui.offers_local_offer') }}</span>
              @if($isProduct)
                <span style="background:rgba(99,102,241,.18);color:#a5b4fc;border:1px solid rgba(99,102,241,.3);font-size:.63rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;padding:2px 8px;border-radius:100px;">📦 Product</span>
              @else
                <span style="background:rgba(245,158,11,.12);color:#fcd34d;border:1px solid rgba(245,158,11,.25);font-size:.63rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;padding:2px 8px;border-radius:100px;">🎟 Coupon</span>
              @endif
            </div>
            <div class="ocard-title">{{ $offer->title }}</div>
            <div class="ocard-desc">{{ \Illuminate\Support\Str::limit(strip_tags($offer->content), 80) }}</div>
            <div class="pts-badge">💎 {{ $pts }} {{ __('ui.offers_pts_required') }}</div>

            @if($redeemed)
              @if($isProduct)
                <div style="display:flex;align-items:center;gap:.4rem;background:rgba(99,102,241,.12);border:1px solid rgba(99,102,241,.25);padding:5px 10px;border-radius:10px;font-size:.73rem;font-weight:700;color:#a5b4fc;">📦 Order Placed — Pending Review</div>
              @else
                <div class="redeemed-badge">✓ {{ __('ui.offers_redeemed_successfully') }}</div>
              @endif
            @else
              @if($isProduct)
                <div class="code-lock">🔒 <span>Redeem to place order</span></div>
              @else
                <div class="code-lock">🔒 <span>{{ __('ui.offers_redeem_unlock_offer') }}</span></div>
              @endif
            @endif

            <div class="ocard-actions">
              @auth
                @if($redeemed && $isProduct)
                  <a href="{{ route('offers.my_orders') }}" class="btn-redeem" style="text-decoration:none;text-align:center;background:linear-gradient(135deg,#6366f1,#4f46e5);">📦 Track Order</a>
                @else
                  <button class="btn-redeem cp-redeem"
                          data-type="offer" data-id="{{ $offer->id }}" data-pts="{{ $pts }}" data-is-product="{{ $isProduct ? '1' : '0' }}"
                          {{ $redeemed ? 'disabled' : (!$canAfford ? 'disabled' : '') }}>
                    {{ $redeemed ? '✓ '.__('ui.offers_redeemed') : ($canAfford ? ($isProduct ? '📦 Order · '.$pts.' '.(__('ui.pts')) : '💎 '.__('ui.offers_redeem').' · '.$pts.' '.__('ui.pts')) : '⚡ '.__('ui.offers_need_points', ['pts' => $pts])) }}
                  </button>
                @endif
              @else
                <a href="{{ route('login') }}" class="btn-login-cta">{{ __('ui.login_to_redeem') }}</a>
              @endauth
              @if($offer->source_url ?? false)
                <a class="btn-shop" href="{{ $offer->source_url }}" target="_blank" rel="noopener">🔗 {{ __('ui.view') }}</a>
              @endif
            </div>

            @if(Auth::check() && Auth::user()->isSuperadmin())
              <div class="admin-row">
                <button class="btn-adm btn-adm-pts cp-edit-pts" data-type="offer" data-id="{{ $offer->id }}" data-pts="{{ $pts }}">✏️ {{ __('ui.offers_edit_points_short') }}</button>
                <button class="btn-adm btn-adm-del cp-del-offer" data-id="{{ $offer->id }}">🗑 {{ __('ui.offers_delete') }}</button>
              </div>
            @endif
            @if($offer->published_at)
              <div class="ocard-expiry">📅 {{ $offer->published_at->format('d M Y') }}</div>
            @endif
          </div>
        </div>
      @empty
        <div class="empty-state" style="grid-column:1/-1">
          <div class="emo">🎁</div>
          <p>{{ __('ui.offers_no_local_offers') }}<br>{{ __('ui.offers_check_back_soon') }}</p>
        </div>
      @endforelse

      {{-- ── API / Amazon Coupons ── --}}
      @if($apiCoupons->isNotEmpty())
    </div>{{-- close grid for section break --}}

    <div class="sec-hd" style="margin-top:2.5rem">
      <h2>🛍 {{ __('ui.offers_amazon_online_coupons') }}</h2>
      <div class="line"></div>
      <span class="badge">{{ $apiCoupons->count() }} {{ __('ui.all_coupons') }}</span>
    </div>

    <div class="grid" id="cpGrid2">
      @foreach($apiCoupons as $coupon)
        @php
          $pts       = ($coupon->points_required > 0) ? (int)$coupon->points_required : 200;
          $canAfford = $userPoints >= $pts;
          $redeemed  = in_array($coupon->id, $redeemedIds);
        @endphp
        <div class="ocard"
             data-type="coupon" data-id="{{ $coupon->id }}"
             data-pts="{{ $pts }}"
             data-category="{{ strtolower($coupon->category ?? 'shopping') }}"
             data-q="{{ strtolower(($coupon->title ?? '') . ' ' . ($coupon->store ?? '')) }}">
          <div class="ocard-glow"></div>
          @if($coupon->image_url)
            <img class="ocard-img" src="{{ $coupon->image_url }}" alt="{{ $coupon->title }}" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="ocard-img-ph" style="display:none">🛍</div>
          @else
            <div class="ocard-img-ph">🛍</div>
          @endif
          <div class="ocard-body">
            @if($coupon->store)
              <span class="ocard-store {{ strtolower($coupon->store ?? '') === 'amazon' ? 'amazon' : 'default' }}">{{ $coupon->store }}</span>
            @endif
            @if($coupon->discount_text)
              <div style="font-size:.72rem;font-weight:700;color:var(--y);margin-bottom:.3rem">🏷 {{ $coupon->discount_text }}</div>
            @endif
            <div class="ocard-title">{{ $coupon->title }}</div>
            <div class="ocard-desc">{{ \Illuminate\Support\Str::limit($coupon->description ?? '', 80) }}</div>
            <div class="pts-badge">💎 {{ $pts }} {{ __('ui.offers_pts_required') }}</div>

            @if($redeemed)
              <div class="code-reveal cp-code-{{ $coupon->id }}" title="{{ __('ui.click_to_copy') }}" onclick="cpCopyCode(this,'{{ $coupon->discount_text }}')">
                {{ $coupon->discount_text ?: '✓ '.__('ui.offers_redeemed') }}
              </div>
              <div class="copied-tip" id="cp-copied-{{ $coupon->id }}">✓ {{ __('ui.copied') }}!</div>
            @else
              <div class="code-lock cp-locked-{{ $coupon->id }}">🔒 <span>{{ __('ui.offers_redeem_to_reveal_coupon') }}</span></div>
              <div class="code-reveal cp-code-{{ $coupon->id }}" style="display:none" onclick="cpCopyCode(this,'{{ $coupon->discount_text }}')">{{ $coupon->discount_text }}</div>
              <div class="copied-tip" id="cp-copied-{{ $coupon->id }}">✓ {{ __('ui.copied') }}!</div>
            @endif

            <div class="ocard-actions">
              @auth
                <button class="btn-redeem cp-redeem"
                        data-type="coupon" data-id="{{ $coupon->id }}" data-pts="{{ $pts }}"
                        {{ $redeemed ? 'disabled' : (!$canAfford ? 'disabled' : '') }}>
                  {{ $redeemed ? '✓ '.__('ui.offers_redeemed') : ($canAfford ? '💎 '.__('ui.offers_redeem').' · '.$pts.' '.__('ui.pts') : '⚡ '.__('ui.offers_need_points', ['pts' => $pts])) }}
                </button>
              @else
                <a href="{{ route('login') }}" class="btn-login-cta">{{ __('ui.login_to_redeem') }}</a>
              @endauth
              @if($coupon->shop_url)
                <a class="btn-shop" href="{{ $coupon->shop_url }}" target="_blank" rel="noopener">🔗 {{ __('ui.shop_now') }}</a>
              @endif
            </div>

            @if(Auth::check() && Auth::user()->isSuperadmin())
              <div class="admin-row">
                <button class="btn-adm btn-adm-pts cp-edit-pts" data-type="coupon" data-id="{{ $coupon->id }}" data-pts="{{ $pts }}">✏️ {{ __('ui.offers_edit_points_short') }}</button>
                <button class="btn-adm btn-adm-del cp-del-coupon" data-id="{{ $coupon->id }}">🗑 {{ __('ui.offers_delete') }}</button>
              </div>
            @endif
            @if($coupon->expiry_date)
              <div class="ocard-expiry">⏳ {{ __('ui.offers_expires') }} {{ \Carbon\Carbon::parse($coupon->expiry_date)->format('d M Y') }}</div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
      @else
    </div>{{-- close first grid --}}
      @endif

    {{-- My Redemptions --}}
    @auth
      <div class="my-section" id="cpMySec">
        <div class="sec-hd" style="margin-top:2.5rem">
          <h2>✅ {{ __('ui.offers_my_redeemed') }}</h2>
          <div class="line"></div>
        </div>
        <div class="redemption-grid" id="cpMyGrid">
          <div style="color:var(--muted);font-size:.82rem">{{ __('ui.offers_loading') }}</div>
        </div>
      </div>
    @endauth
  </div>{{-- .wrap --}}
</div>{{-- #cpHub --}}

<script>
window._pts  = {{ $userPoints }};
window._sa   = {{ Auth::check() && Auth::user()->isSuperadmin() ? 'true' : 'false' }};
window._auth = {{ Auth::check() ? 'true' : 'false' }};
const _csrf  = document.querySelector('meta[name="csrf-token"]')?.content || '';
const I18N = {
  copied: @json(__('ui.copied')),
  codeCopied: @json(__('ui.offers_code_copied')),
  needPoints: @json(__('ui.offers_need_points_js')),
  redeeming: @json(__('ui.offers_redeeming')),
  redeemed: @json(__('ui.offers_redeemed')),
  redeemedSuccess: @json(__('ui.offers_redeemed_successfully')),
  redeemAction: @json(__('ui.offers_redeem')),
  pts: @json(__('ui.pts')),
  offerRedeemedToast: @json(__('ui.offers_success_redeem')),
  networkError: @json(__('ui.offers_network_error')),
  setRedemptionPoints: @json(__('ui.offers_set_redemption_points')),
  rubyPointsRequired: @json(__('ui.offers_ruby_points_required')),
  save: @json(__('ui.offers_save')),
  cancel: @json(__('ui.offers_cancel')),
  deleteOffer: @json(__('ui.offers_delete_offer')),
  deleteCoupon: @json(__('ui.offers_delete_coupon')),
  deleteAll: @json(__('ui.offers_delete_all')),
  cannotUndo: @json(__('ui.offers_cannot_undo')),
  deleteExpiredCoupons: @json(__('ui.offers_delete_expired_coupons')),
  removeExpiredConfirm: @json(__('ui.offers_remove_expired_confirm')),
  expiredDeleted: @json(__('ui.offers_expired_deleted')),
  myRedeemedEmpty: @json(__('ui.offers_my_redeemed_empty')),
  citypulse: @json(__('ui.citypulse')),
  offerRedeemed: @json(__('ui.offers_offer_redeemed')),
  clickToCopy: @json(__('ui.click_to_copy'))
};

// ── Toast
function cpToast(msg, type) {
  const t = document.getElementById('cpToast2');
  t.innerHTML = (type==='ok'?'✓ ':'') + msg;
  t.className = 'show ' + (type||'');
  clearTimeout(t._t);
  t._t = setTimeout(() => t.className = '', 3200);
}

// ── Modal
function cp2OpenModal(title, body, btns, withInput, inputVal) {
  document.getElementById('cp2ModalTitle').textContent = title;
  document.getElementById('cp2ModalBody').textContent  = body;
  const inp = document.getElementById('cp2PtsInput');
  inp.style.display = withInput ? 'block' : 'none';
  if (withInput) { inp.value = inputVal || ''; }
  document.getElementById('cp2ModalBtns').innerHTML = btns;
  document.getElementById('cpModal2').classList.add('open');
}
function cp2CloseModal() { document.getElementById('cpModal2').classList.remove('open'); }

// ── Update points display
function cpSetPts(val) {
  window._pts = val;
  const el = document.getElementById('cpPtsVal');
  if (el) el.textContent = val.toLocaleString();
  document.querySelectorAll('.cp-redeem').forEach(b => {
    if (b.dataset.redeemed) return;
    const p = parseInt(b.dataset.pts||0);
    b.disabled = val < p;
    if (!b.disabled && !b.dataset.redeemed) {
      b.textContent = '💎 ' + I18N.redeemAction + ' · ' + p + ' ' + I18N.pts;
    }
  });
}

// ── Search & Filter
function cpFilter() {
  const q = (document.getElementById('cpSearch')?.value||'').toLowerCase();
  const f = document.querySelector('.chip.on')?.dataset.filter || 'all';
  ['cpGrid','cpGrid2'].forEach(gid => {
    const g = document.getElementById(gid);
    if (!g) return;
    g.querySelectorAll('.ocard').forEach(card => {
      const qs  = card.dataset.q || '';
      const cat = card.dataset.category || '';
      const p   = parseInt(card.dataset.pts||0);
      let show  = !q || qs.includes(q);
      if (f === 'affordable') show = show && window._pts >= p;
      else if (f !== 'all') show = show && (cat===f || cat.includes(f));
      card.style.display = show ? '' : 'none';
    });
  });
}
document.getElementById('cpSearch')?.addEventListener('input', cpFilter);
document.querySelectorAll('.chip').forEach(c => {
  c.addEventListener('click', function() {
    document.querySelectorAll('.chip').forEach(x => x.classList.remove('on'));
    this.classList.add('on');
    cpFilter();
  });
});

// ── Copy code
function cpCopyCode(el, code) {
  if (!code) return;
  navigator.clipboard?.writeText(code).then(() => {
    const id = el.className.match(/cp-code-(\d+)/)?.[1];
    if (id) {
      const tip = document.getElementById('cp-copied-' + id);
      if (tip) { tip.style.display = 'block'; setTimeout(() => tip.style.display='none', 2000); }
    }
    cpToast(I18N.codeCopied + ': ' + code, 'ok');
  });
}

// ── Address modal
let _addrBtn = null;
function openAddrModal(btn, offerId, offerTitle, pts) {
  _addrBtn = btn;
  document.getElementById('addrOfferId').value = offerId;
  document.getElementById('addrModalTitle').textContent = 'Delivery Address';
  document.getElementById('addrOfferStrip').innerHTML =
    `<strong>${offerTitle}</strong> &nbsp;·&nbsp; 💎 ${pts} pts`;
  const warn = document.getElementById('addrPtsWarning');
  warn.style.display = 'flex';
  document.getElementById('addrPtsText').textContent =
    `${pts} Ruby Points will be deducted from your balance (${window._pts} pts remaining)`;
  document.getElementById('addrError').style.display = 'none';
  document.getElementById('addrSubmitText').textContent = 'Place Order';
  document.getElementById('addrSubmitIcon').textContent = '📦';
  document.getElementById('addrSubmitBtn').disabled = false;
  document.getElementById('addrModal').classList.add('open');
}
function closeAddrModal() {
  document.getElementById('addrModal').classList.remove('open');
  if (_addrBtn) {
    const pts = parseInt(_addrBtn.dataset.pts||200);
    _addrBtn.disabled = false;
    _addrBtn.innerHTML = '📦 Order · ' + pts + ' ' + I18N.pts;
  }
  _addrBtn = null;
}

// Address form submit
document.getElementById('addrForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const offerId = document.getElementById('addrOfferId').value;
  const btn     = document.getElementById('addrSubmitBtn');
  const errEl   = document.getElementById('addrError');
  errEl.style.display = 'none';
  btn.disabled = true;
  document.getElementById('addrSubmitText').textContent = 'Placing Order...';
  document.getElementById('addrSubmitIcon').textContent = '⏳';

  const payload = {
    offer_id:          offerId,
    delivery_name:     document.getElementById('addr_name').value,
    delivery_phone:    document.getElementById('addr_phone').value,
    delivery_address1: document.getElementById('addr_line1').value,
    delivery_address2: document.getElementById('addr_line2').value,
    delivery_city:     document.getElementById('addr_city').value,
    delivery_state:    document.getElementById('addr_state').value,
    delivery_pincode:  document.getElementById('addr_pin').value,
    delivery_landmark: document.getElementById('addr_landmark').value,
  };

  fetch('/coupons/redeem', {
    method: 'POST',
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':_csrf},
    body: JSON.stringify(payload)
  }).then(r => r.json()).then(data => {
    if (data.success) {
      cpSetPts(data.points_left ?? window._pts);
      closeAddrModal();
      // Update the card UI
      if (_addrBtn === null && offerId) {
        const card = document.querySelector(`.ocard[data-id="${offerId}"]`);
        if (card) {
          const lock = card.querySelector('.code-lock');
          if (lock) lock.outerHTML = '<div style="display:flex;align-items:center;gap:.4rem;background:rgba(99,102,241,.12);border:1px solid rgba(99,102,241,.25);padding:5px 10px;border-radius:10px;font-size:.73rem;font-weight:700;color:#a5b4fc;">📦 Order Placed — Pending Review</div>';
          const redeemBtn = card.querySelector('.cp-redeem');
          if (redeemBtn) redeemBtn.outerHTML = `<a href="{{ route('offers.my_orders') }}" class="btn-redeem" style="text-decoration:none;text-align:center;background:linear-gradient(135deg,#6366f1,#4f46e5);">📦 Track Order</a>`;
        }
      }
      cpToast('📦 Order placed! Track it in My Orders.', 'ok');
      cpLoadMine();
    } else {
      errEl.textContent = data.error || 'Something went wrong. Please try again.';
      errEl.style.display = 'block';
      btn.disabled = false;
      document.getElementById('addrSubmitText').textContent = 'Place Order';
      document.getElementById('addrSubmitIcon').textContent = '📦';
    }
  }).catch(() => {
    errEl.textContent = 'Network error. Please check your connection.';
    errEl.style.display = 'block';
    btn.disabled = false;
    document.getElementById('addrSubmitText').textContent = 'Place Order';
    document.getElementById('addrSubmitIcon').textContent = '📦';
  });
});

// ── Redeem
document.querySelectorAll('.cp-redeem').forEach(btn => {
  btn.addEventListener('click', function() {
    if (this.disabled) return;
    if (!window._auth) { location.href = '/login'; return; }
    const type      = this.dataset.type;
    const id        = this.dataset.id;
    const pts       = parseInt(this.dataset.pts||200);
    const isProduct = this.dataset.isProduct === '1';

    if (window._pts < pts) {
      cpToast(I18N.needPoints.replace(':pts', pts).replace(':have', window._pts), 'err');
      return;
    }

    // Product offer → show address modal
    if (type === 'offer' && isProduct) {
      const title = this.closest('.ocard')?.querySelector('.ocard-title')?.textContent || 'Offer';
      this.disabled = true;
      openAddrModal(this, id, title, pts);
      return;
    }

    // Coupon or standard offer → direct redeem
    const self = this;
    self.disabled = true;
    self.textContent = '⏳ ' + I18N.redeeming;
    const body = type === 'offer' ? {offer_id: id} : {coupon_id: id};
    fetch('/coupons/redeem', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':_csrf},
      body: JSON.stringify(body)
    }).then(r => r.json()).then(data => {
      if (data.success) {
        cpSetPts(data.points_left ?? (window._pts - pts));
        self.textContent = '✓ ' + I18N.redeemed;
        self.dataset.redeemed = '1';
        if (type === 'coupon') {
          const locked   = self.closest('.ocard-body')?.querySelector('.cp-locked-' + id);
          const revealed = self.closest('.ocard-body')?.querySelector('.cp-code-' + id);
          if (locked)   locked.style.display = 'none';
          if (revealed) revealed.style.display = 'block';
        }
        const lock = self.closest('.ocard-body')?.querySelector('.code-lock');
        if (lock) { lock.outerHTML = '<div class="redeemed-badge">✓ ' + I18N.redeemedSuccess + '</div>'; }
        cpToast(I18N.offerRedeemedToast + ' 🎉', 'ok');
        cpLoadMine();
      } else {
        self.disabled = false;
        self.textContent = '💎 ' + I18N.redeemAction + ' · ' + pts + ' ' + I18N.pts;
        cpToast(data.error || @json(__('ui.offers_error')), 'err');
      }
    }).catch(() => {
      self.disabled = false;
      self.textContent = '💎 ' + I18N.redeemAction + ' · ' + pts + ' ' + I18N.pts;
      cpToast(I18N.networkError, 'err');
    });
  });
});

// ── Edit pts (superadmin)
document.querySelectorAll('.cp-edit-pts').forEach(btn => {
  btn.addEventListener('click', function() {
    const type = this.dataset.type, id = this.dataset.id, cur = this.dataset.pts;
    cp2OpenModal(I18N.setRedemptionPoints, I18N.rubyPointsRequired,
      `<button class="mbox-ok" id="cp2PtsSave">${I18N.save}</button>
       <button class="mbox-cancel" onclick="cp2CloseModal()">${I18N.cancel}</button>`,
      true, cur);
    document.getElementById('cp2PtsSave').onclick = () => {
      const p = parseInt(document.getElementById('cp2PtsInput').value);
      if (!p||p<1) return;
      const url = type==='offer'?`/admin/offers/${id}/set-points`:`/admin/coupons/${id}/set-points`;
      fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrf},body:JSON.stringify({points_required:p})})
        .then(r=>r.json()).then(d=>{if(d.success){cpToast(@json(__('ui.offers_set_points_success')),'ok');cp2CloseModal();location.reload();}else cpToast(d.error||@json(__('ui.offers_set_points_error')),'err');});
    };
  });
});

// ── Delete offer
document.querySelectorAll('.cp-del-offer').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    cp2OpenModal(I18N.deleteOffer, I18N.cannotUndo,
      `<button class="mbox-ok" id="cp2DelOk" style="background:#ef4444">${@json(__('ui.offers_delete'))}</button>
       <button class="mbox-cancel" onclick="cp2CloseModal()">${I18N.cancel}</button>`,false);
    document.getElementById('cp2DelOk').onclick = () =>
      fetch(`/admin/offers/${id}`,{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrf}}).then(()=>location.reload());
  });
});

// ── Delete coupon
document.querySelectorAll('.cp-del-coupon').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    cp2OpenModal(I18N.deleteCoupon, I18N.cannotUndo,
      `<button class="mbox-ok" id="cp2DelCpnOk" style="background:#ef4444">${@json(__('ui.offers_delete'))}</button>
       <button class="mbox-cancel" onclick="cp2CloseModal()">${I18N.cancel}</button>`,false);
    document.getElementById('cp2DelCpnOk').onclick = () =>
      fetch(`/admin/coupons/${id}`,{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrf}}).then(()=>location.reload());
  });
});

// ── Delete expired
window.cp2DeleteExpired = function() {
  cp2OpenModal(I18N.deleteExpiredCoupons, I18N.removeExpiredConfirm,
    `<button class="mbox-ok" id="cp2DelExpOk" style="background:#ef4444">${I18N.deleteAll}</button>
     <button class="mbox-cancel" onclick="cp2CloseModal()">${I18N.cancel}</button>`,false);
  document.getElementById('cp2DelExpOk').onclick = () =>
    fetch('/admin/coupons/delete-expired',{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrf}})
      .then(r=>r.json()).then(d=>{if(d.success){cpToast(I18N.expiredDeleted,'ok');cp2CloseModal();location.reload();}else cpToast(d.error||@json(__('ui.offers_error')),'err');});
};

// ── Load my redemptions
function cpLoadMine() {
  if (!window._auth) return;
  fetch('/coupons/my-redemptions').then(r=>r.json()).then(data=>{
    const g = document.getElementById('cpMyGrid');
    if (!g) return;
    const list = data.redemptions||[];
    if (!list.length) {
      g.innerHTML = '<div style="color:var(--muted);font-size:.82rem;padding:.5rem 0">'+I18N.myRedeemedEmpty+'</div>';
      return;
    }
    const statusColors = {pending:'#f59e0b',approved:'#6366f1',on_the_way:'#8b5cf6',delivered:'#10b981',rejected:'#ef4444'};
    const statusLabels = {pending:'⏳ Pending',approved:'✅ Confirmed',on_the_way:'🚚 Shipped',delivered:'🎉 Delivered',rejected:'❌ Rejected'};
    g.innerHTML = list.map(r => {
      const code = r.coupon_code||'';
      const isOffer = code.startsWith('offer_');
      const isCpn   = code.startsWith('coupon_');
      const status  = r.status || 'pending';
      const sColor  = statusColors[status] || '#6b7280';
      const sLabel  = statusLabels[status] || status;
      let bottom = '';
      if (isOffer) {
        bottom = `<div style="display:inline-flex;align-items:center;gap:4px;background:${sColor}22;border:1px solid ${sColor}55;padding:3px 9px;border-radius:100px;font-size:.7rem;font-weight:700;color:${sColor}">${sLabel}</div>`;
      } else if (isCpn) {
        bottom = '<div class="redeemed-badge" style="font-size:.7rem">🎟 Coupon Redeemed</div>';
      } else if (code) {
        bottom = `<div class="rcard-code" onclick="navigator.clipboard&&navigator.clipboard.writeText('${code}')" title="${I18N.clickToCopy}" style="cursor:pointer">${code}</div>`;
      }
      return `<div class="rcard">
        <div class="rcard-store">${r.store||I18N.citypulse}</div>
        <div class="rcard-title">${r.title||''}</div>
        ${bottom}
        <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-top:.3rem">
          <div class="rcard-date">📅 ${r.redeemed_at?r.redeemed_at.substring(0,10):''}</div>
          ${isOffer ? '<a href="{{ route("offers.my_orders") }}" style="font-size:.65rem;color:#a5b4fc;font-weight:700;">Track →</a>' : ''}
        </div>
      </div>`;
    }).join('');
  });
}
cpLoadMine();

// ── Earn toggle
function toggleEarn() {
  const body   = document.getElementById('earnBody');
  const toggle = document.getElementById('earnToggle');
  const open   = body.classList.toggle('open');
  toggle.classList.toggle('open', open);
}
</script>
@endsection
