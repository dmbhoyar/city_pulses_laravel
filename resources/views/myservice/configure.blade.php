@extends('layouts.app')

@section('content')
@php
  $dashboardRoute = $dashboardRoute ?? 'myservice';
  $configureSaveRoute = $configureSaveRoute ?? 'configure_myservice_save';
  $unlockRoute = $unlockRoute ?? 'myservice.unlock';
  $entityLabel = $entityLabel ?? 'Service';
  $entityPluralLabel = $entityPluralLabel ?? 'Services';
  $providerProfileLabel = $providerProfileLabel ?? 'Service Provider Profile';
@endphp
<style>
  .cfg-section{background:#fff;border:1px solid #dbe7f8;border-radius:10px;padding:16px;margin-top:12px}
  .cfg-section,.cfg-section *{box-sizing:border-box}
  .cfg-section{overflow:hidden}
  .cfg-section .form-row{min-width:0}
  .cfg-section input[type="text"],
  .cfg-section input[type="email"],
  .cfg-section input[type="url"],
  .cfg-section input[type="file"],
  .cfg-section textarea,
  .cfg-section select{width:100%;max-width:100%;min-width:0}
  .cfg-section textarea{resize:vertical}
  .cfg-section h3{margin:0 0 10px;font-size:15px;color:#2f4e74;border-bottom:1px solid #e8eef9;padding-bottom:6px}
  .cfg-section h3 span{font-size:11px;font-weight:400;color:#86a0be;margin-left:6px}
  .template-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px}
  .tpl-card{border:2px solid #dbe7f8;border-radius:12px;padding:12px;cursor:pointer;transition:border-color .2s,background .2s,transform .2s;text-align:left;background:#fafcff}
  .tpl-card.active{border-color:#2f4e74;background:#eef4ff}
  .tpl-card:hover{transform:translateY(-1px)}
  .tpl-card.locked{border-color:#f2d27a;background:#fffaf0}
  .tpl-card h4{margin:0 0 4px;font-size:14px;color:#2f4e74}
  .tpl-card p{margin:0;font-size:12px;color:#6d84a5}
  .tpl-chip{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:3px 8px;border-radius:999px;margin-bottom:8px}
  .tpl-chip.free{background:#e8f7ee;color:#1f8a49;border:1px solid #ccefd9}
  .tpl-chip.paid{background:#fff2d8;color:#9a6500;border:1px solid #f4d49a}
  .tpl-chip.unlocked{background:#e6f9ee;color:#166534;border:1px solid #6ee7a0}
  .unlock-box{margin-top:12px;border:1px solid #f2d27a;background:#fffaf0;border-radius:10px;padding:14px}
  .unlock-box h4{margin:0 0 8px;color:#8c5a00}
  .unlock-box p{margin:0 0 10px;color:#7b6a3d;font-size:13px;line-height:1.6}
  .unlock-modal{position:fixed;inset:0;z-index:1400;display:flex;align-items:center;justify-content:center;padding:16px;overflow-y:auto}
  .unlock-modal-backdrop{position:absolute;inset:0;background:rgba(8,14,26,.58)}
  .unlock-modal-dialog{position:relative;width:min(760px,calc(100vw - 24px));max-height:calc(100vh - 32px);overflow:auto;background:#fff;border-radius:12px;box-shadow:0 18px 48px rgba(11,22,40,.28);border:1px solid #dbe7f8;padding:18px 18px 16px}
  .unlock-modal-close{position:absolute;top:10px;right:10px;border:1px solid #d0dff4;background:#fff;border-radius:8px;width:32px;height:32px;font-size:20px;line-height:1;cursor:pointer;color:#35527a}
  .unlock-form{display:grid;gap:10px}
  .unlock-grid{display:grid;grid-template-columns:1fr;gap:10px}
  .unlock-box .form-row{margin:0}
  .unlock-box input[type="text"],.unlock-box input[type="file"],.unlock-box textarea{width:100%;box-sizing:border-box}
  .unlock-box input[type="file"]{padding:8px;background:#fff;border:1px solid #d9e4f4;border-radius:8px}
  .unlock-pay-row{display:flex;gap:14px;align-items:center;flex-wrap:wrap}
  .unlock-qr-img{width:180px;height:180px;object-fit:cover;border:1px solid #ecd7a2;border-radius:8px;background:#fff}
  .unlock-pay-note{font-size:12px;color:#7b6a3d;line-height:1.65;max-width:360px}
  .unlock-pay-unavailable{padding:10px 12px;border:1px solid #f0d79a;background:#fff3df;border-radius:8px;color:#8c5a00;font-size:12px;font-weight:600}
  @media (min-width: 980px){.unlock-grid.two-col{grid-template-columns:1fr 1fr}}
  @media (max-width: 680px){
    .unlock-modal{padding:0;align-items:flex-end;justify-content:stretch}
    .unlock-modal-dialog{width:100vw;max-height:90vh;padding:12px 12px 10px;border-radius:14px 14px 0 0;border-bottom:none}
    .unlock-modal-close{top:6px;right:6px;width:30px;height:30px}
    .unlock-box{padding:10px}
    .unlock-pay-row{align-items:flex-start;flex-direction:column}
    .unlock-qr-img{width:min(84vw,280px);height:min(84vw,280px);max-width:100%}
    .unlock-pay-note{max-width:100%}
    .unlock-grid.two-col{grid-template-columns:1fr}
  }
  .tpl-thumb{height:130px;border-radius:10px;overflow:hidden;border:1px solid #dbe7f8;margin-bottom:10px;position:relative;background:#fff}
  .tpl-thumb.dynamic{background:linear-gradient(135deg,#1a0a3b,#7b2ff7)}
  .tpl-thumb.dynamic::before{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.08),transparent)}
  .tpl-thumb.dynamic .mini-top{height:24px;background:rgba(255,255,255,.12)}
  .tpl-thumb.dynamic .mini-hero{padding:14px}
  .tpl-thumb.dynamic .mini-pill{width:74px;height:10px;border-radius:999px;background:rgba(255,255,255,.28);margin-bottom:10px}
  .tpl-thumb.dynamic .mini-title{width:90px;height:14px;border-radius:6px;background:#fff;margin-bottom:8px}
  .tpl-thumb.dynamic .mini-sub{width:120px;height:9px;border-radius:5px;background:rgba(255,255,255,.5);margin-bottom:12px}
  .tpl-thumb.dynamic .mini-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px}
  .tpl-thumb.dynamic .mini-box{height:28px;border-radius:8px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.18)}
  .tpl-thumb.astro{background:linear-gradient(135deg,#05000f,#4b1f7a 48%,#c9860a 120%)}
  .tpl-thumb.astro::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 20% 20%,rgba(255,255,255,.12),transparent 32%),radial-gradient(circle at 80% 70%,rgba(245,197,24,.16),transparent 28%)}
  .tpl-thumb.astro .mini-top{height:22px;background:rgba(0,0,0,.24);display:flex;gap:4px;align-items:center;padding:0 8px}
  .tpl-thumb.astro .mini-dot{width:18px;height:6px;border-radius:999px;background:rgba(245,197,24,.5)}
  .tpl-thumb.astro .mini-hero{padding:12px;color:#fff}
  .tpl-thumb.astro .mini-title{width:110px;height:15px;border-radius:6px;background:linear-gradient(90deg,#fff,#f5c518);margin-bottom:8px}
  .tpl-thumb.astro .mini-sub{width:95px;height:8px;border-radius:5px;background:rgba(255,220,150,.5);margin-bottom:12px}
  .tpl-thumb.astro .mini-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:6px}
  .tpl-thumb.astro .mini-box{height:24px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(245,197,24,.2)}
  .tpl-preview{border-radius:10px;padding:12px;margin-top:10px;font-size:12px;color:#567;background:#f3f7ff;border:1px solid #dbe7f8;min-height:210px;display:grid;grid-template-columns:minmax(220px,320px) 1fr;gap:14px;align-items:start}
  .tpl-preview-visual{border-radius:10px;overflow:hidden;border:1px solid #dbe7f8;background:#fff;min-height:180px}
  .tpl-preview-copy h4{margin:0 0 6px;color:#2f4e74;font-size:15px}
  .tpl-preview-copy p{margin:0;color:#6d84a5;line-height:1.6}
  .tpl-sample-wrap{margin-top:12px;border:1px solid #dbe7f8;border-radius:12px;background:#f8fbff;padding:12px}
  .tpl-sample-head{display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:10px}
  .tpl-sample-head strong{font-size:14px;color:#2f4e74}
  .tpl-sample-head span{font-size:12px;color:#7a90ad}
  .tpl-sample{display:none;border-radius:12px;overflow:hidden;height:680px;border:1px solid #dbe7f8;background:#fff}
  .tpl-sample.active{display:block}
  .tpl-sample-scroll{height:100%;overflow-y:auto;overflow-x:hidden;background:#fff}
  .tpl-sample-scroll::-webkit-scrollbar{width:10px}
  .tpl-sample-scroll::-webkit-scrollbar-thumb{background:#c9d9ef;border-radius:999px}
  .tpl-sample-scroll::-webkit-scrollbar-track{background:#eef4fb}
  .sample-dyn{background:#fff;min-height:1100px}
  .sample-dyn-top{height:40px;background:#1a0a3b;display:flex;align-items:center;padding:0 12px;gap:8px}
  .sample-dyn-top span{display:inline-flex;align-items:center;justify-content:center;padding:5px 10px;border-radius:999px;background:rgba(255,255,255,.08);color:#dcd6ff;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
  .sample-dyn-hero{padding:24px;background:linear-gradient(135deg,#1a0a3b,#7b2ff7);color:#fff}
  .sample-dyn-badge{display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);margin-bottom:14px;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
  .sample-dyn-title{font-size:28px;font-weight:800;line-height:1.15;max-width:560px;margin-bottom:10px}
  .sample-dyn-sub{font-size:13px;line-height:1.7;max-width:680px;color:rgba(255,255,255,.82);margin-bottom:18px}
  .sample-dyn-actions{display:flex;gap:10px;flex-wrap:wrap}.sample-dyn-actions span{display:inline-flex;align-items:center;justify-content:center;min-width:128px;height:34px;padding:0 14px;border-radius:10px;font-size:11px;font-weight:700}.sample-dyn-actions span:first-child{background:#fff;color:#46238c}.sample-dyn-actions span:last-child{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.24);color:#fff}
  .sample-dyn-body{padding:18px}.sample-dyn-grid{display:grid;grid-template-columns:1.2fr 1fr;gap:14px}.sample-dyn-card{border:1px solid #dbe7f8;border-radius:14px;padding:16px;background:#fff}.sample-dyn-card.tall{min-height:210px}.sample-dyn-card h5{font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#7c3aed;margin-bottom:8px}.sample-dyn-card h4{font-size:21px;color:#243f63;margin-bottom:8px}.sample-dyn-card p{font-size:13px;color:#6d84a5;line-height:1.7}.sample-dyn-mini-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:12px}.sample-dyn-mini-grid .mini{min-height:74px;border-radius:12px;background:#eef4ff;border:1px solid #dbe7f8;padding:10px}.sample-dyn-mini-grid .mini strong{display:block;font-size:12px;color:#2f4e74;margin-bottom:4px}.sample-dyn-mini-grid .mini span{font-size:11px;color:#6d84a5;line-height:1.5}
  .sample-dyn-provider{display:grid;grid-template-columns:260px 1fr;gap:14px;margin-top:14px}.sample-dyn-provider-card{border-radius:18px;padding:18px;background:linear-gradient(145deg,#20395f,#4b86d7);color:#fff;min-height:250px}.sample-dyn-provider-card .avatar{width:72px;height:72px;border-radius:18px;background:#fff;margin-bottom:12px}.sample-dyn-provider-card h4{font-size:20px;margin-bottom:4px;color:#fff}.sample-dyn-provider-card small{display:block;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#d7ebff;margin-bottom:10px}.sample-dyn-provider-card p{font-size:12px;line-height:1.7;color:rgba(255,255,255,.82)}.sample-dyn-provider-meta{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}.sample-dyn-provider-meta .meta{border:1px solid #dbe7f8;border-radius:14px;padding:14px;background:#fff;min-height:92px}.sample-dyn-provider-meta .meta strong{display:block;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#6c84a8;margin-bottom:6px}.sample-dyn-provider-meta .meta span{font-size:13px;color:#304c72;line-height:1.5}
  .sample-dyn-alt{margin-top:16px;background:#f8fbff;border-radius:16px;padding:18px}.sample-dyn-alt h4{font-size:24px;color:#243f63;margin-bottom:8px}.sample-dyn-alt p{font-size:13px;color:#6d84a5;line-height:1.7;margin-bottom:12px}.sample-dyn-why{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.sample-dyn-why .why{border:1px solid #dbe7f8;border-radius:14px;background:#fff;padding:14px;min-height:110px}.sample-dyn-why .why strong{display:block;font-size:13px;color:#2f4e74;margin:8px 0 6px}.sample-dyn-why .why span{font-size:11px;color:#6d84a5;line-height:1.5}
  .sample-dyn-testi{margin-top:16px}.sample-dyn-testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.sample-dyn-testi-grid .testi{border:1px solid #dbe7f8;border-radius:14px;background:#fff;padding:16px;min-height:120px}.sample-dyn-testi-grid .testi strong{display:block;color:#7c3aed;margin-bottom:8px}.sample-dyn-testi-grid .testi span{font-size:12px;color:#6d84a5;line-height:1.7}
  .sample-dyn-cta{margin-top:16px;border-radius:18px;padding:28px;background:linear-gradient(135deg,#2d1265,#7b2ff7);color:#fff;text-align:center}.sample-dyn-cta h4{font-size:30px;margin-bottom:10px}.sample-dyn-cta p{font-size:13px;line-height:1.7;color:rgba(255,255,255,.84);margin-bottom:14px}.sample-dyn-footer{padding:18px;margin-top:16px;background:#1a0a3b;color:rgba(255,255,255,.72);display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;font-size:12px}
  .sample-astro{background:#04010c;color:#fff;position:relative;min-height:1520px}
  .sample-astro-switch{height:42px;background:rgba(8,3,18,.95);display:flex;align-items:center;gap:8px;padding:0 12px;border-bottom:1px solid rgba(245,197,24,.16)}
  .sample-astro-switch span{height:22px;padding:0 10px;border-radius:999px;background:rgba(245,197,24,.12);border:1px solid rgba(245,197,24,.2);display:inline-flex;align-items:center;font-size:10px;color:#f5c518}
  .sample-astro-canvas{display:grid;grid-template-columns:1fr;min-height:278px}
  .sample-astro-pane{padding:18px;border-bottom:1px solid rgba(255,255,255,.06);position:relative;overflow:hidden;min-height:470px}
  .sample-astro-pane:last-child{border-right:none}
  .sample-astro-pane h5{font-size:12px;letter-spacing:.14em;text-transform:uppercase;margin-bottom:12px}
  .sample-astro-pane .hero{min-height:132px;border-radius:12px;margin-bottom:12px;padding:14px;display:flex;flex-direction:column;justify-content:flex-end}
  .sample-astro-pane .hero small{display:inline-flex;align-self:flex-start;padding:4px 8px;border-radius:999px;margin-bottom:8px;font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase}
  .sample-astro-pane .hero strong{display:block;font-size:19px;line-height:1.1;margin-bottom:6px}
  .sample-astro-pane .hero p{font-size:11px;line-height:1.55;max-width:220px}
  .sample-astro-pane .cards{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:12px}.sample-astro-pane .cards div{min-height:92px;border-radius:10px;padding:12px}.sample-astro-pane .cards strong{display:block;font-size:11px;margin-bottom:4px}.sample-astro-pane .cards span{font-size:10px;line-height:1.45;display:block}.sample-astro-pane .why-row{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:14px}.sample-astro-pane .why-row div{min-height:84px;border-radius:10px;padding:12px}.sample-astro-pane .why-row strong{display:block;font-size:11px;margin-bottom:4px}.sample-astro-pane .why-row span{display:block;font-size:10px;line-height:1.45}
  .sample-astro-pane.cosmic{background:radial-gradient(circle at 20% 20%,rgba(120,0,255,.18),transparent 26%),#03000d}.sample-astro-pane.cosmic h5{color:#f5c518}.sample-astro-pane.cosmic .hero{background:linear-gradient(135deg,#120028,#5d2399 60%,#c9860a)}.sample-astro-pane.cosmic .l1{background:#fff}.sample-astro-pane.cosmic .l2{background:rgba(255,255,255,.55)}.sample-astro-pane.cosmic .l3{background:rgba(245,197,24,.45)}.sample-astro-pane.cosmic .cards div{background:rgba(255,255,255,.05);border:1px solid rgba(245,197,24,.15)}
  .sample-astro-pane.cosmic .why-row div{background:rgba(255,255,255,.05);border:1px solid rgba(245,197,24,.15)}
  .sample-astro-pane.saffron{background:#fdf6ec}.sample-astro-pane.saffron h5{color:#d4520a}.sample-astro-pane.saffron .hero{background:linear-gradient(135deg,#fff,#ffe3cf 58%,#d4520a)}.sample-astro-pane.saffron .hero small{background:#fff3e6;color:#d4520a}.sample-astro-pane.saffron .hero strong{color:#1a0a00}.sample-astro-pane.saffron .hero p{color:#6e5a4a}.sample-astro-pane.saffron .cards div,.sample-astro-pane.saffron .why-row div{background:#fff;border:1px solid #f0d9c8}.sample-astro-pane.saffron .cards strong,.sample-astro-pane.saffron .why-row strong{color:#1a0a00}.sample-astro-pane.saffron .cards span,.sample-astro-pane.saffron .why-row span{color:#846c5b}
  .sample-astro-pane.neon{background:#04010c}.sample-astro-pane.neon h5{color:#a855f7}.sample-astro-pane.neon .hero{background:linear-gradient(135deg,#120028,#3e1a72 55%,#06b6d4)}.sample-astro-pane.neon .hero small{background:rgba(168,85,247,.16);color:#c4b5fd}.sample-astro-pane.neon .hero strong{color:#fff}.sample-astro-pane.neon .hero p{color:rgba(232,224,255,.75)}.sample-astro-pane.neon .cards div,.sample-astro-pane.neon .why-row div{background:rgba(168,85,247,.07);border:1px solid rgba(168,85,247,.16)}.sample-astro-pane.neon .cards strong,.sample-astro-pane.neon .why-row strong{color:#c4b5fd}.sample-astro-pane.neon .cards span,.sample-astro-pane.neon .why-row span{color:rgba(232,224,255,.5)}
  .sample-astro-full{background:radial-gradient(circle at 20% 10%,rgba(94,40,170,.22),transparent 35%),#04010c;min-height:1620px;color:#fff}
  .sample-astro-top{height:44px;background:rgba(8,3,18,.95);display:flex;align-items:center;justify-content:space-between;padding:0 12px;border-bottom:1px solid rgba(245,197,24,.16)}
  .sample-astro-top .chip{height:24px;padding:0 10px;border-radius:999px;background:rgba(245,197,24,.12);border:1px solid rgba(245,197,24,.24);display:inline-flex;align-items:center;font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:#f5c518;font-weight:700}
  .sample-astro-top .brand{font-size:11px;color:rgba(255,255,255,.74);letter-spacing:.12em;text-transform:uppercase}
  .sample-astro-hero{padding:22px 18px 16px;border-bottom:1px solid rgba(255,255,255,.08)}
  .sample-astro-eyebrow{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;background:rgba(245,197,24,.12);border:1px solid rgba(245,197,24,.24);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#f5c518;margin-bottom:10px}
  .sample-astro-eyebrow i{width:7px;height:7px;border-radius:50%;background:#f5c518;box-shadow:0 0 0 4px rgba(245,197,24,.15)}
  .sample-astro-hero h4{font-size:29px;line-height:1.14;margin:0 0 8px;background:linear-gradient(90deg,#fff,#f5c518);-webkit-background-clip:text;background-clip:text;color:transparent}
  .sample-astro-hero p{font-size:13px;line-height:1.75;color:rgba(235,226,255,.78);max-width:640px;margin:0 0 12px}
  .sample-astro-actions{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
  .sample-astro-actions span{display:inline-flex;align-items:center;justify-content:center;height:34px;padding:0 14px;border-radius:10px;font-size:11px;font-weight:700;letter-spacing:.04em}
  .sample-astro-actions span:first-child{background:linear-gradient(135deg,#f5c518,#efb900);color:#221008}
  .sample-astro-actions span:last-child{border:1px solid rgba(245,197,24,.26);background:rgba(255,255,255,.05);color:#f5c518}
  .sample-astro-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
  .sample-astro-stats div{border:1px solid rgba(245,197,24,.2);background:rgba(255,255,255,.03);border-radius:10px;padding:10px 12px}
  .sample-astro-stats strong{display:block;font-size:16px;color:#f5c518}
  .sample-astro-stats small{display:block;font-size:11px;color:rgba(235,226,255,.7);margin-top:2px}
  .sample-astro-block{padding:18px;border-bottom:1px solid rgba(255,255,255,.08)}
  .sample-astro-head{margin-bottom:10px}
  .sample-astro-head em{display:block;font-style:normal;font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#f5c518;margin-bottom:6px}
  .sample-astro-head h5{margin:0 0 5px;font-size:22px;line-height:1.2;color:#fff}
  .sample-astro-head p{margin:0;font-size:12px;line-height:1.7;color:rgba(235,226,255,.72)}
  .sample-astro-grid4{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
  .sample-astro-grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
  .sample-astro-card{border-radius:12px;border:1px solid rgba(245,197,24,.18);background:rgba(255,255,255,.04);padding:12px;min-height:96px}
  .sample-astro-card strong{display:block;font-size:12px;color:#fff;margin-bottom:4px}
  .sample-astro-card span{display:block;font-size:11px;line-height:1.5;color:rgba(235,226,255,.72)}
  .sample-astro-card b{display:inline-flex;margin-top:6px;padding:3px 8px;border-radius:999px;background:rgba(245,197,24,.16);font-size:10px;color:#f5c518}
  .sample-astro-product{border-radius:12px;border:1px solid rgba(245,197,24,.2);background:linear-gradient(135deg,rgba(255,255,255,.06),rgba(245,197,24,.08));padding:14px;min-height:88px}
  .sample-astro-product strong{display:block;font-size:13px;color:#fff;margin-bottom:4px}
  .sample-astro-product span{display:block;font-size:11px;line-height:1.55;color:rgba(235,226,255,.75)}
  .sample-astro-cta{padding:24px 18px;text-align:center;background:linear-gradient(135deg,rgba(82,28,147,.7),rgba(179,115,8,.55));border-top:1px solid rgba(245,197,24,.2);border-bottom:1px solid rgba(245,197,24,.2)}
  .sample-astro-cta h5{margin:0 0 7px;font-size:28px;color:#fff}
  .sample-astro-cta p{margin:0 0 12px;font-size:13px;line-height:1.75;color:rgba(250,244,229,.82)}
  .sample-astro-foot{padding:14px 18px;background:#090214;display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;font-size:11px;color:rgba(235,226,255,.66)}
  @media (max-width: 860px){.tpl-preview{grid-template-columns:1fr}.tpl-sample{height:560px}.sample-dyn-grid,.sample-dyn-provider,.sample-dyn-why,.sample-dyn-testi-grid,.sample-astro-pane .cards,.sample-astro-pane .why-row{grid-template-columns:1fr 1fr}}
  @media (max-width: 620px){.sample-dyn-grid,.sample-dyn-provider,.sample-dyn-why,.sample-dyn-testi-grid,.sample-astro-pane .cards,.sample-astro-pane .why-row{grid-template-columns:1fr}.tpl-sample{height:520px}}
  @media (max-width: 860px){.sample-astro-stats,.sample-astro-grid4,.sample-astro-grid3{grid-template-columns:1fr 1fr}}
  @media (max-width: 620px){.sample-astro-stats,.sample-astro-grid4,.sample-astro-grid3{grid-template-columns:1fr}}
  .city-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:6px;max-height:220px;overflow-y:auto;border:1px solid #e2ecf9;border-radius:6px;padding:8px;background:#fafcff}
  .city-item label{display:flex;align-items:center;gap:5px;font-size:13px;color:#3a5478;cursor:pointer;padding:3px 4px;border-radius:4px}
  .city-item label:hover{background:#eef4ff}
  .svc-row{display:flex;gap:8px;align-items:flex-start;border:1px solid #e8eef9;border-radius:6px;padding:10px;margin-bottom:8px;background:#fafcff}
  .svc-row .svc-body{flex:1;display:grid;gap:6px}
  .svc-row .svc-body input,.svc-row .svc-body textarea{width:100%;box-sizing:border-box}
  .svc-row .svc-body textarea{resize:vertical;min-height:52px}
  .svc-remove{color:#e55;background:none;border:1px solid #e8d;border-radius:4px;padding:2px 7px;cursor:pointer;font-size:18px;line-height:1}
  .pf-row{display:flex;gap:8px;align-items:flex-start;border:1px solid #e8eef9;border-radius:5px;padding:8px;margin-bottom:6px;background:#fafcff}
  .pf-row .pf-body{flex:1;display:grid;grid-template-columns:1fr 2fr 120px;gap:6px;align-items:center}
  .pf-row .pf-body input,.pf-row .pf-body select{width:100%;box-sizing:border-box}
  .pf-actions{display:flex;flex-direction:column;gap:3px;width:34px}
  .pf-actions button{padding:2px 5px;font-size:11px}
  .cfg-grid{display:grid;gap:10px}
  .cfg-grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}
  .cfg-grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}
  .cfg-grid.split{grid-template-columns:minmax(0,1fr) minmax(0,2fr)}
  .cfg-grid > *{min-width:0}
  .svc-inline-grid{display:grid;grid-template-columns:90px minmax(0,1fr) 150px;gap:6px}
  .svc-inline-grid > *{min-width:0}
  .grp-item-row{display:grid;grid-template-columns:70px minmax(0,1fr) 130px;gap:5px;margin-bottom:5px;align-items:start}
  .grp-item-row > *{min-width:0}
  .grp-item-desc{grid-column:1/-1;display:flex;gap:5px;align-items:center}
  .grp-item-desc textarea{flex:1;min-width:0}
  @media (max-width: 760px){
    .cfg-grid.two,.cfg-grid.three,.cfg-grid.split,.svc-inline-grid,.pf-row .pf-body{grid-template-columns:1fr}
    .svc-row,.pf-row{flex-direction:column}
    .svc-remove{align-self:flex-end}
    .pf-actions{flex-direction:row;width:auto}
    .grp-item-desc{flex-direction:column;align-items:stretch}
    .grp-item-desc .grp-item-remove{align-self:flex-end}
    .cfg-section{padding:12px}
  }
</style>

<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
    <h1 style="margin:0">Configure {{ $entityLabel }}</h1>
    <a href="{{ route($dashboardRoute) }}" class="button">← Back to Dashboard</a>
  </div>

  <form action="{{ route($configureSaveRoute) }}" method="POST" id="cfg-form" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    {{-- ─── Basic Info ──────────────────────────────────── --}}
    <div class="cfg-section">
      <h3>Basic Information</h3>
      <div class="form-row">
        <label for="name">{{ $entityLabel }} Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $shop->name ?? '') }}" placeholder="e.g. SparkFix Electricals">
      </div>
      <div class="form-row">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3" placeholder="Briefly describe what you offer…">{{ old('description', $shop->description ?? '') }}</textarea>
      </div>
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone" value="{{ old('phone', $shop->phone ?? '') }}" placeholder="+91 …">
        </div>
        <div class="form-row" style="margin:0">
          <label for="address">Address / Area</label>
          <input type="text" id="address" name="address" value="{{ old('address', $shop->address ?? '') }}" placeholder="Area, City">
        </div>
      </div>
      <div class="form-row">
        <label for="public_slug">Public Page URL</label>
        <input type="text" id="public_slug" name="public_slug" value="{{ old('public_slug', $pageConfig['public_slug'] ?? $shop->public_page_slug) }}" placeholder="e.g. dhananjay-plumbing">
        <small style="display:block;margin-top:6px;color:#7d92ae">Choose your own unique page link. Use letters, numbers, and hyphens only.</small>
        <small style="display:block;margin-top:4px;color:#4a90d9">Preview: {{ url('/') }}/<span id="public-slug-preview">{{ old('public_slug', $pageConfig['public_slug'] ?? $shop->public_page_slug) }}</span></small>
        @error('public_slug')
          <div style="margin-top:6px;color:#d64545;font-size:12px">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- ─── Service Cities ──────────────────────────────── --}}
    <div class="cfg-section">
      <h3>{{ $entityLabel }} Cities <span>Select all cities where you provide this {{ strtolower($entityLabel) }}</span></h3>
      @php $savedCities = old('service_cities', $serviceCities ?? []); @endphp
      @if($cities->count())
        <div class="city-grid">
          @foreach($cities as $city)
            <div class="city-item">
              <label>
                <input type="checkbox" name="service_cities[]" value="{{ $city->id }}"
                       {{ in_array($city->id, (array)$savedCities) ? 'checked' : '' }}>
                {{ $city->name }}
              </label>
            </div>
          @endforeach
        </div>
      @else
        <p style="margin:0;color:#86a0be">No cities configured yet.</p>
      @endif
    </div>

    {{-- ─── Template ────────────────────────────────────── --}}
    <div class="cfg-section">
      <h3>Website Template <span>How your public {{ strtolower($entityLabel) }} page looks</span></h3>
      @php $activeTemplate = old('template', $shop->template ?? 'dynamic_service'); @endphp
      @if(!$astroUnlocked)
        @php $activeTemplate = $activeTemplate === 'astro_dynamic' ? 'dynamic_service' : $activeTemplate; @endphp
      @endif
      <input type="hidden" id="cfg-template" name="template" value="{{ $activeTemplate }}">
      <div class="template-cards">
        <button type="button" class="tpl-card {{ $activeTemplate === 'dynamic_service' ? 'active' : '' }}" data-template="dynamic_service"
                data-desc="Dynamic premium landing page with switcher, hero, services grid, testimonials, CTA and footer.">
          <span class="tpl-chip free">Included · Free</span>
          <div class="tpl-thumb dynamic">
            <div class="mini-top"></div>
            <div class="mini-hero">
              <div class="mini-pill"></div>
              <div class="mini-title"></div>
              <div class="mini-sub"></div>
              <div class="mini-grid">
                <div class="mini-box"></div>
                <div class="mini-box"></div>
                <div class="mini-box"></div>
              </div>
            </div>
          </div>
          <h4>Dynamic Template</h4>
          <p>Premium dynamic template (included free in your subscription plan).</p>
        </button>
        <button type="button" class="tpl-card {{ $activeTemplate === 'astro_dynamic' ? 'active' : '' }} {{ !$astroUnlocked ? 'locked' : '' }}" data-template="astro_dynamic" data-locked="{{ $astroUnlocked ? '0' : '1' }}"
          data-desc="Premium cosmic dark template with dramatic hero, luxury cards, and a richer high-visual landing page.">
          @if($astroUnlocked)
            <span class="tpl-chip unlocked">✓ Unlocked</span>
          @else
            <span class="tpl-chip paid">Premium · ₹{{ number_format($astroUnlockPrice, 0) }} Unlock</span>
          @endif
          <div class="tpl-thumb astro">
            <div class="mini-top"><span class="mini-dot"></span><span class="mini-dot"></span><span class="mini-dot"></span></div>
            <div class="mini-hero">
              <div class="mini-title"></div>
              <div class="mini-sub"></div>
              <div class="mini-grid">
                <div class="mini-box"></div>
                <div class="mini-box"></div>
                <div class="mini-box"></div>
                <div class="mini-box"></div>
              </div>
            </div>
          </div>
          <h4>Astro Dynamic Template</h4>
          @if($astroUnlocked)
            <p style="color:#166534;font-weight:500">Unlocked — cosmic premium dark template ready to use.</p>
          @else
            <p>Cosmic premium landing page in a single dark luxury style. Paid unlock required.</p>
          @endif
        </button>
      </div>
      @error('template')
        <div style="margin-top:8px;color:#d64545;font-size:12px">{{ $message }}</div>
      @enderror
      <div id="template-lock-message" style="margin-top:8px;color:#d64545;font-size:12px;display:none">You have to unlock this template first. Click Unlock option and submit payment proof.</div>
      @if(!$astroUnlocked)
        <div style="margin-top:10px">
          <a href="{{ route($unlockRoute) }}" class="button" style="background:#f5c518;border-color:#e0b10f;color:#2d1c00;text-decoration:none;display:inline-block">Unlock Astro Dynamic →</a>
        </div>
      @endif
      <div class="tpl-sample-wrap">
        <div class="tpl-sample-head">
          <strong>Sample View</strong>
          <span>Customers will see a full-page version of the selected design.</span>
        </div>
        <div class="tpl-sample active" data-sample="dynamic_service">
          <div class="tpl-sample-scroll">
          <div class="sample-dyn">
            <div class="sample-dyn-top"><span>Home</span><span>Services</span><span>Contact</span></div>
            <div class="sample-dyn-hero">
              <div class="sample-dyn-badge">Trusted Local Service</div>
              <div class="sample-dyn-title">Premium Home Repair & Local Expert Support</div>
              <div class="sample-dyn-sub">A clean modern landing page with a hero area, action buttons, service grid, provider section, and trust-building content blocks.</div>
              <div class="sample-dyn-actions"><span>Book Service</span><span>Get Free Quote</span></div>
            </div>
            <div class="sample-dyn-body">
              <div class="sample-dyn-grid">
                <div class="sample-dyn-card tall">
                  <h5>Our Services</h5>
                  <h4>Services We Offer</h4>
                  <p>Ideal for plumbers, electricians, mechanics, local consultants, home service teams, and all service providers who want a strong professional web presence.</p>
                  <div class="sample-dyn-mini-grid">
                    <div class="mini"><strong>Plumbing Repair</strong><span>Fast leak fixing and pipe service</span></div>
                    <div class="mini"><strong>Home Wiring</strong><span>Safe installation and maintenance</span></div>
                    <div class="mini"><strong>AC Service</strong><span>Regular cleaning and repair support</span></div>
                    <div class="mini"><strong>Emergency Visit</strong><span>Quick response with direct contact</span></div>
                  </div>
                </div>
                <div class="sample-dyn-card">
                  <h5>Provider Card</h5>
                  <h4>Meet Dhananjay</h4>
                  <p>Show provider name, experience, age, email, contact number, service area, and photo in a clear, trust-focused layout.</p>
                </div>
              </div>
              <div class="sample-dyn-provider">
                <div class="sample-dyn-provider-card">
                  <div class="avatar"></div>
                  <h4>Dhananjay Bhoyar</h4>
                  <small>Founder & Lead Expert</small>
                  <p>Experienced local provider focused on fast support, premium presentation, and direct communication with customers.</p>
                </div>
                <div class="sample-dyn-provider-meta">
                  <div class="meta"><strong>Experience</strong><span>10+ years of on-ground service support.</span></div>
                  <div class="meta"><strong>Email</strong><span>dhananjay@example.com</span></div>
                  <div class="meta"><strong>Contact</strong><span>902281139</span></div>
                  <div class="meta"><strong>Service Area</strong><span>Wasim and nearby locations</span></div>
                </div>
              </div>
              <div class="sample-dyn-alt">
                <h4>Why Customers Choose This Layout</h4>
                <p>This template gives a clean trust-first experience with a bold hero, clear service cards, visible provider profile, and a strong call-to-action section.</p>
                <div class="sample-dyn-why">
                  <div class="why"><strong>Fast Response</strong><span>Quick support with clear contact options.</span></div>
                  <div class="why"><strong>Modern Design</strong><span>Professional landing page feel.</span></div>
                  <div class="why"><strong>Trust Building</strong><span>Provider profile shown clearly.</span></div>
                  <div class="why"><strong>Action Ready</strong><span>Call and quote buttons stand out.</span></div>
                </div>
              </div>
              <div class="sample-dyn-testi">
                <div class="sample-dyn-card" style="padding:0;border:none;background:transparent">
                  <h5>Testimonials</h5>
                  <h4>Customer Feedback</h4>
                  <div class="sample-dyn-testi-grid">
                    <div class="testi"><strong>★★★★★</strong><span>Quick response and very professional service. Highly recommended for home visits.</span></div>
                    <div class="testi"><strong>★★★★★</strong><span>Good pricing, clear communication, and fast completion of work.</span></div>
                    <div class="testi"><strong>★★★★★</strong><span>Trustworthy provider profile and clean page design create confidence.</span></div>
                  </div>
                </div>
              </div>
              <div class="sample-dyn-cta">
                <h4>Need Help Today?</h4>
                <p>Contact now and get quick support from a trusted local expert.</p>
                <div class="sample-dyn-actions" style="justify-content:center"><span>Contact Now</span><span>View My Services</span></div>
              </div>
              <div class="sample-dyn-footer"><span>My Service Brand</span><span>Trusted · Fast · Professional</span><span>© 2026</span></div>
            </div>
          </div>
          </div>
        </div>
        <div class="tpl-sample" data-sample="astro_dynamic">
          <div class="tpl-sample-scroll">
          <div class="sample-astro-full">
            <div class="sample-astro-top">
              <span class="chip">Cosmic Dark</span>
              <span class="brand">Nakshtea Astro Sample</span>
            </div>

            <div class="sample-astro-hero">
              <span class="sample-astro-eyebrow"><i></i> Trusted Astro Guidance</span>
              <h4>Discover Your Path with Premium Vedic Astrology</h4>
              <p>Full-page preview flow for Astro Dynamic template: hero, service sections, products, trust cards, testimonials, CTA and footer — matching the actual template structure.</p>
              <div class="sample-astro-actions"><span>Book Consultation</span><span>View Services</span></div>
              <div class="sample-astro-stats">
                <div><strong>50K+</strong><small>Happy Clients</small></div>
                <div><strong>25+</strong><small>Years Experience</small></div>
                <div><strong>4.9★</strong><small>Average Rating</small></div>
                <div><strong>24×7</strong><small>Support</small></div>
              </div>
            </div>

            <div class="sample-astro-block">
              <div class="sample-astro-head"><em>01 · Astrology Consultation</em><h5>Expert Readings for Every Life Area</h5><p>Personalised astrology cards grouped under one heading.</p></div>
              <div class="sample-astro-grid4">
                <div class="sample-astro-card"><strong>Education & Studies</strong><span>Guidance for focus and academic growth.</span><b>From ₹499</b></div>
                <div class="sample-astro-card"><strong>Career & Business</strong><span>Timing support for work and growth.</span><b>From ₹699</b></div>
                <div class="sample-astro-card"><strong>Love & Marriage</strong><span>Compatibility and relationship insights.</span><b>From ₹799</b></div>
                <div class="sample-astro-card"><strong>Health & Wellness</strong><span>Balanced lifestyle and remedy guidance.</span><b>From ₹599</b></div>
              </div>
            </div>

            <div class="sample-astro-block">
              <div class="sample-astro-head"><em>02 · Premium Services</em><h5>Remedies, Pujas & Special Reports</h5><p>Secondary service section with its own dynamic heading and cards.</p></div>
              <div class="sample-astro-grid4">
                <div class="sample-astro-card"><strong>Kundali Analysis</strong><span>Complete birth chart interpretation.</span><b>From ₹999</b></div>
                <div class="sample-astro-card"><strong>Manglik Dosha</strong><span>Detailed dosha analysis and remedy path.</span><b>From ₹899</b></div>
                <div class="sample-astro-card"><strong>Vastu Consultation</strong><span>Home and office energy balancing.</span><b>From ₹1499</b></div>
                <div class="sample-astro-card"><strong>Personal Puja</strong><span>Custom puja recommendations and timing.</span><b>From ₹1299</b></div>
              </div>
            </div>

            <div class="sample-astro-block">
              <div class="sample-astro-head"><em>Astro Products</em><h5>Gemstones, Vastu Tools & Spiritual Kits</h5><p>Product cards and trust-building blocks in full page flow.</p></div>
              <div class="sample-astro-grid3">
                <div class="sample-astro-product"><strong>Natural Gemstones</strong><span>Energised and authenticity-verified options.</span></div>
                <div class="sample-astro-product"><strong>Vastu Products</strong><span>Yantras, pyramids and directional solutions.</span></div>
                <div class="sample-astro-product"><strong>Spiritual Accessories</strong><span>Rudraksha, malas and puja essentials.</span></div>
              </div>
            </div>

            <div class="sample-astro-block">
              <div class="sample-astro-head"><em>Testimonials</em><h5>What Clients Say</h5><p>Review and social-proof section from the Astro template flow.</p></div>
              <div class="sample-astro-grid3">
                <div class="sample-astro-card"><strong>★★★★★ Priya S.</strong><span>Very accurate guidance and practical remedies.</span></div>
                <div class="sample-astro-card"><strong>★★★★★ Rahul K.</strong><span>Clear predictions and supportive consultation.</span></div>
                <div class="sample-astro-card"><strong>★★★★★ Meena P.</strong><span>Professional experience with premium design feel.</span></div>
              </div>
            </div>

            <div class="sample-astro-cta">
              <h5>Ready to Get Your Personal Reading?</h5>
              <p>Strong CTA block in the final section of page flow with high visual focus.</p>
              <div class="sample-astro-actions" style="justify-content:center"><span>Book Now</span><span>WhatsApp</span></div>
            </div>

            <div class="sample-astro-foot"><span>Nakshtea Astro</span><span>Trusted · Accurate · Confidential</span><span>© 2026</span></div>
          </div>
          </div>
        </div>
      </div>

      @if(!$astroUnlocked)
      <div class="unlock-modal" id="astro-unlock-modal" style="display:none" aria-hidden="true">
        <div class="unlock-modal-backdrop" data-close-astro-unlock></div>
        <div class="unlock-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="astro-unlock-title">
          <button type="button" class="unlock-modal-close" aria-label="Close" data-close-astro-unlock>&times;</button>
          <div class="unlock-box" id="astro-unlock-box" style="margin-top:0">
            <h4 id="astro-unlock-title">Unlock Astro Dynamic (₹{{ number_format($astroUnlockPrice, 0) }})</h4>
            <p>
              UPI/QR Payment: <strong>Scan your barcode/QR and complete ₹{{ number_format($astroUnlockPrice, 0) }} payment</strong>. Submit transaction ID or payment screenshot (one is mandatory).
              Approval is completed within {{ $unlockSlaHours }} hours by super admin.
            </p>

            @if(!$hasActiveSubscription)
              <div style="margin-bottom:10px;padding:10px;border:1px solid #f0d79a;background:#fff7e4;border-radius:8px;font-size:12px;color:#7b5b1d">
                Activate <strong>Yearly Base Plan (₹{{ number_format($yearlyBasePrice, 0) }})</strong> first to submit unlock request for Astro Dynamic.
                <a href="{{ route('subscriptions.new') }}" class="button" style="margin-left:8px">Go to Subscription</a>
              </div>
            @endif

            @if($latestAstroUnlockRequest)
              <div style="margin-bottom:10px;font-size:12px;color:#6d5a2a">
                Latest request status: <strong>{{ ucfirst($latestAstroUnlockRequest->status) }}</strong>
                @if($latestAstroUnlockRequest->created_at)
                  · submitted {{ $latestAstroUnlockRequest->created_at->diffForHumans() }}
                @endif
              </div>
            @endif

            @if($hasPendingAstroUnlockRequest)
              <div style="margin-bottom:10px;padding:10px;border:1px solid #f0d79a;background:#fff7e4;border-radius:8px;font-size:12px;color:#7b5b1d">
                Your request is submitted and currently under review by admin. You cannot submit another request until current review is completed.
              </div>
            @endif

            <div class="unlock-form">
              <input type="hidden" name="template_key" value="astro_dynamic" form="astro-unlock-request-form">
              <input type="hidden" name="amount" value="{{ number_format($astroUnlockPrice, 2, '.', '') }}" form="astro-unlock-request-form">

              <div class="form-row">
                <label>Payment QR / Barcode (Pay ₹{{ number_format($astroUnlockPrice, 0) }})</label>
                @php $hasPaymentCode = (string) $paymentQrUrl !== '' || (string) $paymentBarcodeUrl !== ''; @endphp
                @if($hasPaymentCode)
                  <div class="unlock-pay-row">
                    <img src="{{ $paymentQrUrl ?: $paymentBarcodeUrl }}" alt="Payment QR" class="unlock-qr-img" onerror="this.style.display='none'; this.nextElementSibling && (this.nextElementSibling.style.display='block');">
                    <div class="unlock-pay-unavailable" style="display:none">Payment QR/Barcode unavailable right now. Please contact admin.</div>
                    <div class="unlock-pay-note">
                      Scan this code and pay <strong>₹{{ number_format($astroUnlockPrice, 0) }}</strong>, then submit transaction ID or screenshot below.
                    </div>
                  </div>
                @else
                  <div class="unlock-pay-unavailable">Payment QR/Barcode unavailable right now. Please contact admin.</div>
                @endif
              </div>

              <div class="unlock-grid two-col">
                <div class="form-row">
                  <label for="unlock_transaction_id">Transaction ID (optional)</label>
                  <input id="unlock_transaction_id" type="text" name="payment_transaction_id" value="{{ old('payment_transaction_id') }}" placeholder="UPI/Bank transaction reference" form="astro-unlock-request-form">
                </div>
                <div class="form-row">
                  <label for="unlock_screenshot">Payment Screenshot (optional)</label>
                  <input id="unlock_screenshot" type="file" name="payment_screenshot" accept="image/*" form="astro-unlock-request-form">
                </div>
              </div>

              <div class="form-row">
                <label for="unlock_comment">Additional Comment (optional)</label>
                <textarea id="unlock_comment" name="comment" rows="2" placeholder="Anything you want admin to know..." form="astro-unlock-request-form">{{ old('comment') }}</textarea>
              </div>

              <div style="font-size:12px;color:#7b6a3d">Either transaction ID or screenshot is required.</div>
              <div>
                <button type="submit" class="button" style="background:#f5c518;border-color:#e0b10f;color:#2d1c00" form="astro-unlock-request-form" {{ !$hasActiveSubscription || $hasPendingAstroUnlockRequest ? 'disabled' : '' }}>Submit Unlock Request</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- ─── Dynamic Template Content ────────────────────── --}}
    <div class="cfg-section">
      <h3>Template Content <span>These fields control your public website text</span></h3>
      @php $tc = old('tc', $templateContent ?? []); @endphp
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="tc_hero_badge">Hero Badge</label>
          <input id="tc_hero_badge" type="text" name="tc[hero_badge]" value="{{ $tc['hero_badge'] ?? '' }}" placeholder="Trusted Since 2010 · 5000+ Customers">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_hero_title">Hero Title</label>
          <input id="tc_hero_title" type="text" name="tc[hero_title]" value="{{ $tc['hero_title'] ?? '' }}" placeholder="Professional Service For Your Needs">
        </div>
      </div>
      <div class="form-row">
        <label for="tc_hero_description">Hero Description</label>
        <textarea id="tc_hero_description" name="tc[hero_description]" rows="3" placeholder="Short intro for your service page">{{ $tc['hero_description'] ?? '' }}</textarea>
      </div>
      <div class="cfg-grid three">
        <div class="form-row" style="margin:0">
          <label for="tc_primary_cta">Primary CTA Button</label>
          <input id="tc_primary_cta" type="text" name="tc[primary_cta]" value="{{ $tc['primary_cta'] ?? '' }}" placeholder="Book Now">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_secondary_cta">Secondary CTA Button</label>
          <input id="tc_secondary_cta" type="text" name="tc[secondary_cta]" value="{{ $tc['secondary_cta'] ?? '' }}" placeholder="Get Free Quote">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_services_label">Services Label</label>
          <input id="tc_services_label" type="text" name="tc[services_label]" value="{{ $tc['services_label'] ?? '' }}" placeholder="Our Services">
        </div>
      </div>
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="tc_services_title">Services Section Title</label>
          <input id="tc_services_title" type="text" name="tc[services_title]" value="{{ $tc['services_title'] ?? '' }}" placeholder="Services We Offer">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_services_subtitle">Services Section Subtitle</label>
          <input id="tc_services_subtitle" type="text" name="tc[services_subtitle]" value="{{ $tc['services_subtitle'] ?? '' }}" placeholder="Choose from our most popular services">
        </div>
      </div>
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="tc_why_title">Why-Us Title</label>
          <input id="tc_why_title" type="text" name="tc[why_title]" value="{{ $tc['why_title'] ?? '' }}" placeholder="Why Choose Us">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_why_subtitle">Why-Us Subtitle</label>
          <input id="tc_why_subtitle" type="text" name="tc[why_subtitle]" value="{{ $tc['why_subtitle'] ?? '' }}" placeholder="Quality work and transparent pricing">
        </div>
      </div>
      <div class="cfg-grid three">
        <div class="form-row" style="margin:0">
          <label for="tc_cta_title">Bottom CTA Title</label>
          <input id="tc_cta_title" type="text" name="tc[cta_title]" value="{{ $tc['cta_title'] ?? '' }}" placeholder="Need Help Today?">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_cta_description">Bottom CTA Description</label>
          <input id="tc_cta_description" type="text" name="tc[cta_description]" value="{{ $tc['cta_description'] ?? '' }}" placeholder="Contact us now">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_cta_button">Bottom CTA Button</label>
          <input id="tc_cta_button" type="text" name="tc[cta_button]" value="{{ $tc['cta_button'] ?? '' }}" placeholder="Contact Now">
        </div>
      </div>
      <div class="cfg-grid two">
        <div class="form-row" style="margin:0">
          <label for="tc_footer_brand">Footer Brand</label>
          <input id="tc_footer_brand" type="text" name="tc[footer_brand]" value="{{ $tc['footer_brand'] ?? '' }}" placeholder="Your brand name">
        </div>
        <div class="form-row" style="margin:0">
          <label for="tc_footer_tagline">Footer Tagline</label>
          <input id="tc_footer_tagline" type="text" name="tc[footer_tagline]" value="{{ $tc['footer_tagline'] ?? '' }}" placeholder="Trusted · Fast · Professional">
        </div>
      </div>
    </div>

    <div class="cfg-section">
      <h3>{{ $providerProfileLabel }} <span>This is managed from Subscription page now</span></h3>
      <p style="margin:0;color:#6d84a5;font-size:13px;line-height:1.7">
        To update {{ strtolower($providerProfileLabel) }} details (name, contact, bio, photo), go to
        <a href="{{ route('subscriptions.new') }}">Subscription</a>.
      </p>
    </div>

    {{-- ─── Services Offered ────────────────────────────── --}}
    <div class="cfg-section">
      <h3>{{ $entityPluralLabel }} Offered <span>Each item will appear as a business card on your public page</span></h3>
      <datalist id="service-icon-options">
        <option value="📚" label="Education"></option>
        <option value="💼" label="Career"></option>
        <option value="❤️" label="Love & Marriage"></option>
        <option value="🏠" label="Home / Vastu"></option>
        <option value="💎" label="Gemstone"></option>
        <option value="🪔" label="Puja / Ritual"></option>
        <option value="🧿" label="Protection / Nazar"></option>
        <option value="💰" label="Finance"></option>
        <option value="🧘" label="Health / Wellness"></option>
        <option value="👶" label="Child / Family"></option>
        <option value="🚰" label="Plumbing"></option>
        <option value="⚡" label="Electrical"></option>
        <option value="🎨" label="Painting"></option>
        <option value="🧹" label="Cleaning"></option>
        <option value="🛠️" label="General Service"></option>
        <option value="✨" label="Premium / Featured"></option>
      </datalist>
      <div id="services-container">
        @php $svcs = old('services', $services ?? []); @endphp
        @foreach($svcs as $i => $svc)
          <div class="svc-row">
            <div class="svc-body">
              <div class="svc-inline-grid">
                <input type="text" name="services[{{ $i }}][icon]" value="{{ $svc['icon'] ?? '🛠️' }}" placeholder="Icon" list="service-icon-options">
                <input type="text" name="services[{{ $i }}][name]" value="{{ $svc['name'] ?? '' }}" placeholder="Service name (e.g. Plumbing Repair)">
                <input type="text" name="services[{{ $i }}][price]" value="{{ $svc['price'] ?? '' }}" placeholder="Price (e.g. From ₹499)">
              </div>
              <textarea name="services[{{ $i }}][description]" placeholder="Short description…">{{ $svc['description'] ?? '' }}</textarea>
              <input type="url" name="services[{{ $i }}][url]" value="{{ $svc['url'] ?? '' }}" placeholder="Web page URL (optional)">
            </div>
            <button type="button" class="svc-remove">✕</button>
          </div>
        @endforeach
      </div>
      <button type="button" id="add-service" class="button" style="margin-top:6px">+ Add Service</button>
    </div>

      {{-- ─── Astro: Service Sections ─────────────────────── --}}
      @if($astroUnlocked)
      <div class="cfg-section" id="astro-groups-section" style="{{ $activeTemplate !== 'astro_dynamic' ? 'display:none' : '' }}">
        <h3>Service Sections <span>Astro Template only — group your services under separate headings</span></h3>
        <p style="font-size:12px;color:#667;margin-bottom:12px">Each section has its own eyebrow label, heading and subtitle with service cards underneath. Leave untouched to use auto-split defaults.</p>
        <div id="svc-groups-container">
          @php $savedGroups = old('service_groups', $tc['service_groups'] ?? []); @endphp
          @foreach($savedGroups as $gi => $grp)
            <div class="svc-group-row" style="border:1px solid #dbe7f8;border-radius:10px;padding:12px;margin-bottom:10px">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <strong style="font-size:13px;color:#2d4a7a">Section {{ $gi + 1 }}</strong>
                <button type="button" class="grp-remove" style="background:#fee;border:1px solid #fbb;color:#c33;border-radius:6px;padding:2px 8px;font-size:12px;cursor:pointer">✕ Remove</button>
              </div>
              <div class="cfg-grid split" style="margin-bottom:6px">
                <input type="text" name="service_groups[{{ $gi }}][eyebrow]" value="{{ $grp['eyebrow'] ?? '' }}" placeholder="Eyebrow label (e.g. 01 · Astrology)">
                <input type="text" name="service_groups[{{ $gi }}][title]"   value="{{ $grp['title'] ?? '' }}"   placeholder="Section heading *">
              </div>
              <input type="text" name="service_groups[{{ $gi }}][subtitle]" value="{{ $grp['subtitle'] ?? '' }}" placeholder="Subtitle / description" style="width:100%;margin-bottom:8px">
              <div class="grp-items-container" style="padding-left:10px;border-left:3px solid #e8eef8">
                @foreach($grp['items'] ?? [] as $ii => $item)
                  <div class="grp-item-row">
                    <input type="text"  name="service_groups[{{ $gi }}][items][{{ $ii }}][icon]"        value="{{ $item['icon'] ?? '✨' }}"     placeholder="Icon" list="service-icon-options">
                    <input type="text"  name="service_groups[{{ $gi }}][items][{{ $ii }}][name]"        value="{{ $item['name'] ?? '' }}"       placeholder="Service name *">
                    <input type="text"  name="service_groups[{{ $gi }}][items][{{ $ii }}][price]"       value="{{ $item['price'] ?? '' }}"      placeholder="Price">
                    <div class="grp-item-desc">
                      <textarea name="service_groups[{{ $gi }}][items][{{ $ii }}][description]" placeholder="Short description…" rows="1" style="flex:1">{{ $item['description'] ?? '' }}</textarea>
                      <button type="button" class="grp-item-remove" style="background:#fee;border:1px solid #fbb;color:#c33;border-radius:6px;padding:2px 6px;font-size:11px;cursor:pointer;white-space:nowrap">✕</button>
                    </div>
                  </div>
                @endforeach
              </div>
              <button type="button" class="add-grp-item" style="margin-top:6px;font-size:12px;padding:4px 10px" class="button">+ Add Service to Section</button>
            </div>
          @endforeach
        </div>
        <button type="button" id="add-group" class="button" style="margin-top:6px">+ Add Section</button>
      </div>
      @endif

    {{-- ─── Custom Page Fields ─────────────────────────── --}}
    <div class="cfg-section">
      <h3>Custom Page Fields <span>Additional info shown on your public page</span></h3>
      <div id="pf-container">
        @php
          $pfFields = [];
          if (!empty($shop->page_config['fields'])) $pfFields = $shop->page_config['fields'];
        @endphp
        @foreach($pfFields as $fi => $pf)
          <div class="pf-row">
            <div class="pf-body">
              <input type="text" name="pf[{{ $fi }}][title]" value="{{ $pf['title'] ?? '' }}" placeholder="Title">
              <input type="text" name="pf[{{ $fi }}][value]" value="{{ $pf['value'] ?? '' }}" placeholder="Value">
              <div style="display:flex;gap:6px;align-items:center">
                <label style="font-size:12px;display:flex;gap:3px;align-items:center;white-space:nowrap">
                  <input type="checkbox" name="pf[{{ $fi }}][bold]" value="1" {{ !empty($pf['bold']) ? 'checked' : '' }}> Bold
                </label>
                <select name="pf[{{ $fi }}][align]" style="font-size:12px">
                  <option value="left"   {{ ($pf['align']??'') === 'left'   ? 'selected' : '' }}>Left</option>
                  <option value="center" {{ ($pf['align']??'') === 'center' ? 'selected' : '' }}>Center</option>
                  <option value="right"  {{ ($pf['align']??'') === 'right'  ? 'selected' : '' }}>Right</option>
                </select>
              </div>
            </div>
            <div class="pf-actions">
              <button type="button" class="pf-up button">↑</button>
              <button type="button" class="pf-down button">↓</button>
              <button type="button" class="pf-del button" style="color:#e55">✕</button>
            </div>
          </div>
        @endforeach
      </div>
      <button type="button" id="add-pf" class="button" style="margin-top:6px">+ Add Field</button>
    </div>

    {{-- ─── Submit ───────────────────────────────────────── --}}
    @if(!$hasActiveSubscription)
      <div style="margin-top:10px;padding:10px;border:1px solid #f0d79a;background:#fff7e4;border-radius:8px;font-size:12px;color:#7b5b1d">
        Activate your yearly base subscription first to save configuration.
        <a href="{{ route('subscriptions.new') }}" class="button" style="margin-left:8px">Go to Subscription</a>
      </div>
    @endif
    <div style="display:flex;gap:10px;margin-top:14px">
      <button type="submit" class="toggle-btn" {{ !$hasActiveSubscription ? 'disabled' : '' }}>Save Configuration</button>
      @if($shop->id)
        <a href="{{ route('shops.public', ['publicSlug' => $shop->public_page_slug]) }}" target="_blank" class="button">View Public Page ↗</a>
      @endif
    </div>
  </form>

  @if(!$astroUnlocked)
    <form id="astro-unlock-request-form" action="{{ route('subscriptions.template_unlock_request') }}" method="POST" enctype="multipart/form-data" style="display:none">
      @csrf
    </form>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){

  const publicSlugInput = document.getElementById('public_slug');
  const publicSlugPreview = document.getElementById('public-slug-preview');
  function normalizeSlug(value){
    return String(value || '')
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  }
  if (publicSlugInput && publicSlugPreview) {
    const syncPublicSlug = () => {
      const slug = normalizeSlug(publicSlugInput.value);
      publicSlugInput.value = slug;
      publicSlugPreview.textContent = slug || '{{ $shop->public_page_slug }}';
    };
    publicSlugInput.addEventListener('input', syncPublicSlug);
    syncPublicSlug();
  }

  /* ── Template picker ─────────────────────────────────── */
  const tplInput   = document.getElementById('cfg-template');
  const tplPreview = document.getElementById('tpl-preview');
  const tplPreviewVisual = tplPreview ? tplPreview.querySelector('.tpl-preview-visual') : null;
  const tplPreviewTitle = tplPreview ? tplPreview.querySelector('.tpl-preview-copy h4') : null;
  const tplPreviewDesc = tplPreview ? tplPreview.querySelector('.tpl-preview-copy p') : null;
  const tplSamples = document.querySelectorAll('.tpl-sample');
  const unlockModal = document.getElementById('astro-unlock-modal');
  const templateLockMessage = document.getElementById('template-lock-message');
  function openUnlockModal(){
    if (!unlockModal) return;
    unlockModal.style.display = 'flex';
    unlockModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    unlockModal.scrollTop = 0;
    const dialog = unlockModal.querySelector('.unlock-modal-dialog');
    if (dialog) dialog.scrollTop = 0;
  }
  function closeUnlockModal(){
    if (!unlockModal) return;
    unlockModal.style.display = 'none';
    unlockModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }
  document.querySelectorAll('[data-open-astro-unlock]').forEach(function(btn){
    btn.addEventListener('click', openUnlockModal);
  });
  document.querySelectorAll('[data-close-astro-unlock]').forEach(function(btn){
    btn.addEventListener('click', closeUnlockModal);
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeUnlockModal();
  });
  function syncTemplatePreview(card){
    if (!card) return;
    tplInput.value = card.dataset.template;
    if (tplPreviewVisual && tplPreviewTitle && tplPreviewDesc) {
      tplPreviewVisual.innerHTML = card.querySelector('.tpl-thumb').outerHTML;
      tplPreviewTitle.textContent = card.querySelector('h4').textContent;
      tplPreviewDesc.textContent = card.dataset.desc || '';
    }
    tplSamples.forEach(sample => sample.classList.toggle('active', sample.dataset.sample === card.dataset.template));
  }
  const unlockPageUrl = '{{ route($unlockRoute) }}';
  document.querySelectorAll('.tpl-card').forEach(function(card){
    card.addEventListener('click', function(){
      const isLocked = card.dataset.locked === '1';
      document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('active'));
      card.classList.add('active');
      syncTemplatePreview(card);
      if (isLocked) {
        window.location.href = unlockPageUrl;
      }
    });
  });
  syncTemplatePreview(document.querySelector('.tpl-card.active') || document.querySelector('.tpl-card[data-template="dynamic_service"]'));

  const cfgForm = document.getElementById('cfg-form');
  const astroCard = document.querySelector('.tpl-card[data-template="astro_dynamic"]');
  if (cfgForm && tplInput && astroCard) {
    cfgForm.addEventListener('submit', function(e){
      if (tplInput.value === 'astro_dynamic' && astroCard.dataset.locked === '1') {
        e.preventDefault();
        window.location.href = unlockPageUrl;
      }
    });
  }

  /* ── Services offered ─────────────────────────────────── */
  let svcIdx = {{ count($services ?? []) }};
  const svcContainer = document.getElementById('services-container');

  function addSvcRow(n, icon, name, price, desc, url){
    const row = document.createElement('div');
    row.className = 'svc-row';
    row.innerHTML = `
      <div class="svc-body">
        <div class="svc-inline-grid">
          <input type="text" name="services[${n}][icon]" value="${esc(icon)}" placeholder="Icon" list="service-icon-options">
          <input type="text" name="services[${n}][name]" value="${esc(name)}" placeholder="Service name (e.g. Plumbing Repair)">
          <input type="text" name="services[${n}][price]" value="${esc(price)}" placeholder="Price (e.g. From ₹499)">
        </div>
        <textarea name="services[${n}][description]" placeholder="Short description…">${esc(desc)}</textarea>
        <input type="url" name="services[${n}][url]" value="${esc(url)}" placeholder="Web page URL (optional)">
      </div>
      <button type="button" class="svc-remove">✕</button>`;
    row.querySelector('.svc-remove').addEventListener('click', () => row.remove());
    svcContainer.appendChild(row);
  }

  // Attach remove to existing rows
  document.querySelectorAll('.svc-row .svc-remove').forEach(function(btn){
    btn.addEventListener('click', () => btn.closest('.svc-row').remove());
  });

  document.getElementById('add-service').addEventListener('click', function(){
    addSvcRow(svcIdx++, '🛠️', '', '', '', '');
  });

    /* ── Service Groups (astro_dynamic) ──────────────────── */
    @if($astroUnlocked)
    const grpSection     = document.getElementById('astro-groups-section');
    const grpContainer   = document.getElementById('svc-groups-container');
    let   grpIdx         = {{ count($savedGroups ?? old('service_groups', $tc['service_groups'] ?? [])) }};

    function getGrpItemCount(grpEl){
      return grpEl.querySelectorAll('.grp-item-row').length;
    }

    function addGrpItemRow(grpEl, gi, ii, icon, name, price, desc){
      const c   = grpEl.querySelector('.grp-items-container');
      const row = document.createElement('div');
      row.className = 'grp-item-row';
      row.innerHTML = `
        <input type="text"  name="service_groups[${gi}][items][${ii}][icon]"        value="${esc(icon)}"  placeholder="Icon" list="service-icon-options">
        <input type="text"  name="service_groups[${gi}][items][${ii}][name]"        value="${esc(name)}"  placeholder="Service name *">
        <input type="text"  name="service_groups[${gi}][items][${ii}][price]"       value="${esc(price)}" placeholder="Price">
        <div class="grp-item-desc">
          <textarea name="service_groups[${gi}][items][${ii}][description]" rows="1" placeholder="Short description…" style="flex:1">${esc(desc)}</textarea>
          <button type="button" class="grp-item-remove" style="background:#fee;border:1px solid #fbb;color:#c33;border-radius:6px;padding:2px 6px;font-size:11px;cursor:pointer;white-space:nowrap">✕</button>
        </div>`;
      row.querySelector('.grp-item-remove').addEventListener('click', () => row.remove());
      c.appendChild(row);
    }

    function addGrpRow(gi, data){
      data = data || {};
      const grp = document.createElement('div');
      grp.className = 'svc-group-row';
      grp.style.cssText = 'border:1px solid #dbe7f8;border-radius:10px;padding:12px;margin-bottom:10px';
      grp.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
          <strong style="font-size:13px;color:#2d4a7a">Section ${gi + 1}</strong>
          <button type="button" class="grp-remove" style="background:#fee;border:1px solid #fbb;color:#c33;border-radius:6px;padding:2px 8px;font-size:12px;cursor:pointer">✕ Remove</button>
        </div>
        <div class="cfg-grid split" style="margin-bottom:6px">
          <input type="text" name="service_groups[${gi}][eyebrow]"  value="${esc(data.eyebrow||'')}"  placeholder="Eyebrow label (e.g. 01 · Astrology)">
          <input type="text" name="service_groups[${gi}][title]"    value="${esc(data.title||'')}"    placeholder="Section heading *">
        </div>
        <input type="text" name="service_groups[${gi}][subtitle]"   value="${esc(data.subtitle||'')}" placeholder="Subtitle / description" style="width:100%;margin-bottom:8px">
        <div class="grp-items-container" style="padding-left:10px;border-left:3px solid #e8eef8"></div>
        <button type="button" class="add-grp-item button" style="margin-top:6px;font-size:12px;padding:4px 10px">+ Add Service to Section</button>`;
      grp.querySelector('.grp-remove').addEventListener('click', () => { grp.remove(); reindexGroups(); });
      grp.querySelector('.add-grp-item').addEventListener('click', function(){
        addGrpItemRow(grp, gi, getGrpItemCount(grp), '✨', '', '', '');
      });
      grpContainer.appendChild(grp);
      // Populate existing items
      if (Array.isArray(data.items)) {
        data.items.forEach(function(item, ii){
          addGrpItemRow(grp, gi, ii, item.icon||'✨', item.name||'', item.price||'', item.description||'');
        });
      }
    }

    function reindexGroups(){
      grpContainer.querySelectorAll('.svc-group-row').forEach(function(grp, gi){
        grp.querySelector('strong').textContent = 'Section ' + (gi + 1);
        grp.querySelectorAll('[name]').forEach(function(el){
          el.name = el.name.replace(/service_groups\[\d+\]/, 'service_groups[' + gi + ']');
        });
      });
      grpIdx = grpContainer.querySelectorAll('.svc-group-row').length;
    }

    // Attach events to existing rows rendered by Blade
    document.querySelectorAll('.svc-group-row').forEach(function(grp, gi){
      grp.querySelector('.grp-remove') && grp.querySelector('.grp-remove').addEventListener('click', () => { grp.remove(); reindexGroups(); });
      const addItemBtn = grp.querySelector('.add-grp-item');
      addItemBtn && addItemBtn.addEventListener('click', function(){
        addGrpItemRow(grp, gi, getGrpItemCount(grp), '✨', '', '', '');
      });
      grp.querySelectorAll('.grp-item-remove').forEach(function(btn){
        btn.addEventListener('click', () => btn.closest('.grp-item-row').remove());
      });
    });

    document.getElementById('add-group').addEventListener('click', function(){
      addGrpRow(grpIdx++);
    });

    // Show/hide groups section based on template selection
    function syncGrpSection(){
      const tpl = document.getElementById('cfg-template').value;
      if (grpSection) grpSection.style.display = (tpl === 'astro_dynamic') ? '' : 'none';
    }
    document.querySelectorAll('.tpl-card').forEach(function(btn){
      btn.addEventListener('click', function(){ setTimeout(syncGrpSection, 50); });
    });
    syncGrpSection();
    @endif

  /* ── Custom page fields ───────────────────────────────── */
  let pfIdx = {{ count($pfFields ?? []) }};
  const pfContainer = document.getElementById('pf-container');

  function addPfRow(n, title, val, bold, align){
    const row = document.createElement('div');
    row.className = 'pf-row';
    row.innerHTML = `
      <div class="pf-body">
        <input type="text" name="pf[${n}][title]" value="${esc(title)}" placeholder="Title">
        <input type="text" name="pf[${n}][value]" value="${esc(val)}"   placeholder="Value">
        <div style="display:flex;gap:6px;align-items:center">
          <label style="font-size:12px;display:flex;gap:3px;align-items:center;white-space:nowrap">
            <input type="checkbox" name="pf[${n}][bold]" value="1" ${bold ? 'checked' : ''}> Bold
          </label>
          <select name="pf[${n}][align]" style="font-size:12px">
            <option value="left"   ${align==='left'   ? 'selected' : ''}>Left</option>
            <option value="center" ${align==='center' ? 'selected' : ''}>Center</option>
            <option value="right"  ${align==='right'  ? 'selected' : ''}>Right</option>
          </select>
        </div>
      </div>
      <div class="pf-actions">
        <button type="button" class="pf-up  button">↑</button>
        <button type="button" class="pf-down button">↓</button>
        <button type="button" class="pf-del  button" style="color:#e55">✕</button>
      </div>`;
    attachPfEvents(row);
    pfContainer.appendChild(row);
  }

  function attachPfEvents(row){
    row.querySelector('.pf-del').addEventListener('click', () => row.remove());
    row.querySelector('.pf-up').addEventListener('click', function(){
      if (row.previousElementSibling) pfContainer.insertBefore(row, row.previousElementSibling);
      reindexPf();
    });
    row.querySelector('.pf-down').addEventListener('click', function(){
      if (row.nextElementSibling) pfContainer.insertBefore(row.nextElementSibling, row);
      reindexPf();
    });
  }

  function reindexPf(){
    pfContainer.querySelectorAll('.pf-row').forEach(function(row, i){
      row.querySelectorAll('[name]').forEach(function(el){
        el.name = el.name.replace(/pf\[\d+\]/, 'pf[' + i + ']');
      });
    });
  }

  // Attach events to existing rows
  document.querySelectorAll('.pf-row').forEach(attachPfEvents);

  document.getElementById('add-pf').addEventListener('click', function(){
    addPfRow(pfIdx++, '', '', false, 'left');
  });

  function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
});
</script>
@endsection
