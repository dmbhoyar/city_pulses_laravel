<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<title>ShortsPlay</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent;}
:root{
  --bg:#080810;--bg2:#10101c;--bg3:#181828;--bg4:#222236;
  --b1:rgba(255,255,255,0.06);--b2:rgba(255,255,255,0.11);
  --t1:#f0f0f8;--t2:#8888a8;--t3:#44445a;
  --ruby:#e8345a;--ruby2:#ff6680;
  --gold:#f5a623;--silver:#b0b8cc;--diamond:#50c8f0;--green:#3ecf8e;
  --fd:'Syne',sans-serif;--fb:'DM Sans',sans-serif;
  --r:12px;--r2:20px;--r3:28px;
  --app-width:430px;--app-height:100dvh;
}
html,body{height:100%;min-height:100svh;background:#03030a;font-family:var(--fb);overflow:hidden;color:var(--t1);}

/* ── DEVICE ── */
.device{
  width:min(100vw,var(--app-width));height:var(--app-height);min-height:100svh;background:var(--bg);
  display:flex;flex-direction:column;position:relative;overflow:hidden;
  max-width:430px;margin:0 auto;
}
@media(min-width:430px){
  body{display:flex;align-items:center;justify-content:center;}
  .device{max-height:var(--app-height);}
}

/* ── SCREEN SYSTEM ── */
.page{display:none;flex-direction:column;position:absolute;inset:0;z-index:1;}
.page.show{display:flex;}

/* ════════════════════════════════════
   LOGIN
════════════════════════════════════ */
#pg-login{
  background:radial-gradient(ellipse 80% 60% at 50% 0%,rgba(232,52,90,0.18) 0%,transparent 70%),
             radial-gradient(ellipse 60% 50% at 80% 80%,rgba(80,200,240,0.08) 0%,transparent 60%),
             var(--bg);
  justify-content:flex-end;
}
.login-hero{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;position:relative;}
.hero-rings{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;}
.hr{position:absolute;border-radius:50%;border:1px solid rgba(232,52,90,0.1);animation:hrPulse 4s ease-in-out infinite;}
.hr:nth-child(1){width:120px;height:120px;}
.hr:nth-child(2){width:200px;height:200px;animation-delay:.6s;}
.hr:nth-child(3){width:290px;height:290px;animation-delay:1.2s;}
.hr:nth-child(4){width:380px;height:380px;animation-delay:1.8s;}
@keyframes hrPulse{0%,100%{opacity:.25;transform:scale(1);}50%{opacity:.6;transform:scale(1.015);}}
.hero-center{position:relative;z-index:2;text-align:center;}
.hero-gem{
  width:76px;height:76px;border-radius:24px;margin:0 auto 14px;
  background:linear-gradient(135deg,#e8345a,#ff8040);
  display:flex;align-items:center;justify-content:center;font-size:36px;
  box-shadow:0 0 48px rgba(232,52,90,0.45),0 8px 32px rgba(0,0,0,0.4);
}
.hero-brand{font-family:var(--fd);font-size:36px;font-weight:800;letter-spacing:-0.5px;}
.hero-brand em{color:var(--ruby);font-style:normal;}
.hero-sub{font-size:13px;color:var(--t2);margin-top:4px;}
.login-sheet{
  background:var(--bg2);border-radius:28px 28px 0 0;
  border-top:1px solid var(--b2);padding:26px 22px 28px;
}
.ltabs{display:flex;background:var(--bg3);border-radius:var(--r);padding:4px;margin-bottom:18px;}
.ltab{
  flex:1;padding:10px;border:none;background:none;cursor:pointer;
  font-family:var(--fd);font-size:13px;font-weight:700;color:var(--t2);
  border-radius:9px;transition:all .2s;
}
.ltab.on{background:var(--ruby);color:#fff;}
.lform{display:flex;flex-direction:column;gap:10px;}
.lform input{
  padding:14px 16px;background:var(--bg3);border:1px solid var(--b1);
  border-radius:var(--r);color:var(--t1);font-family:var(--fb);font-size:14px;
  outline:none;transition:border-color .2s;
}
.pwd-wrap{position:relative;}
.pwd-wrap input{padding-right:46px;width:100%;}
.pwd-toggle{
  position:absolute;right:10px;top:50%;transform:translateY(-50%);
  border:none;background:none;color:var(--t2);cursor:pointer;
  width:30px;height:30px;border-radius:10px;font-size:15px;
}
.pwd-toggle:active{background:rgba(255,255,255,.05);}
.lform input:focus{border-color:var(--ruby);}
.lform input::placeholder{color:var(--t3);}
.role-row{display:flex;gap:6px;}
.rpill{
  flex:1;padding:9px 4px;border-radius:var(--r);border:1px solid var(--b1);
  background:var(--bg3);color:var(--t2);font-family:var(--fd);font-size:10px;
  font-weight:700;cursor:pointer;transition:all .2s;text-align:center;
}
.rpill.on{border-color:var(--ruby);background:rgba(232,52,90,.12);color:var(--ruby);}
.lseclabel{font-family:var(--fd);font-size:10px;font-weight:700;color:var(--t3);letter-spacing:.8px;text-transform:uppercase;margin:2px 0 4px;}
.btnmain{
  padding:15px;background:linear-gradient(135deg,#e8345a,#c0203e);
  border:none;border-radius:var(--r);color:#fff;
  font-family:var(--fd);font-size:15px;font-weight:700;
  cursor:pointer;letter-spacing:.3px;transition:opacity .15s;margin-top:4px;
}
.btnmain:active{opacity:.85;}
.ldivider{text-align:center;font-size:11px;color:var(--t3);padding:4px 0;}

/* ════════════════════════════════════
   MAIN APP
════════════════════════════════════ */
#pg-app{background:var(--bg);}
.app-body{flex:1;overflow:hidden;position:relative;}
.tab{display:none;flex-direction:column;height:100%;position:absolute;inset:0;}
.tab.on{display:flex;}

/* BOTTOM NAV */
.bnav{
  display:flex;background:var(--bg2);border-top:1px solid var(--b1);
  padding:6px 0 max(12px,env(safe-area-inset-bottom));flex-shrink:0;z-index:20;
}
.bni{
  flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;
  background:none;border:none;cursor:pointer;
  color:var(--t3);transition:color .2s;padding:5px 4px;
}
.bni.on{color:var(--ruby);}
.bni svg{width:21px;height:21px;}
.bnl{font-family:var(--fd);font-size:9px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;}

/* ════════════════════════════════════
   FEED TAB
════════════════════════════════════ */
.feed-hdr{
  padding:14px 18px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;
  gap:10px;
}
.feed-brand{font-family:var(--fd);font-size:22px;font-weight:800;}
.feed-brand em{color:var(--ruby);font-style:normal;}
.feed-mode-toggle{
  display:flex;background:var(--bg3);border-radius:20px;padding:3px;gap:3px;
}
.fmt-btn{
  padding:5px 12px;border-radius:16px;border:none;background:none;
  font-family:var(--fd);font-size:10px;font-weight:700;color:var(--t2);cursor:pointer;transition:all .2s;
}
.fmt-btn.on{background:var(--ruby);color:#fff;}

.feed-scroll{flex:1;overflow-y:auto;scrollbar-width:none;}
.feed-scroll::-webkit-scrollbar{display:none;}

/* Feed Card */
.vcard{background:var(--bg2);border-bottom:1px solid var(--b1);overflow:hidden;}
.vthumb{
  position:relative;height:clamp(180px,46vw,220px);overflow:hidden;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
}
.vthumb-bg{position:absolute;inset:0;}
.vthumb-fade{position:absolute;inset:0;background:linear-gradient(to bottom,transparent 35%,rgba(0,0,0,.72));}
.vplay{
  width:54px;height:54px;border-radius:50%;z-index:2;
  background:rgba(255,255,255,.14);backdrop-filter:blur(8px);
  border:2px solid rgba(255,255,255,.5);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:20px;transition:transform .2s,background .2s;
}
.vcard:hover .vplay{transform:scale(1.08);background:rgba(232,52,90,.35);}
.vdur{
  position:absolute;bottom:10px;right:12px;z-index:2;
  font-family:var(--fd);font-size:10px;font-weight:700;
  background:rgba(0,0,0,.55);color:#fff;padding:2px 7px;border-radius:6px;
}
.reel-open-hint{
  position:absolute;bottom:10px;left:12px;z-index:2;
  font-family:var(--fd);font-size:9px;font-weight:700;color:rgba(255,255,255,.6);
  display:flex;align-items:center;gap:4px;
}
.vmeta{padding:10px 16px 6px;}
.vcrow{display:flex;align-items:center;gap:9px;margin-bottom:6px;}
.vav{
  width:34px;height:34px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-family:var(--fd);font-size:11px;font-weight:700;
}
.vnwrap{flex:1;min-width:0;}
.vname{font-family:var(--fd);font-size:13px;font-weight:700;color:var(--t1);display:flex;align-items:center;gap:5px;flex-wrap:wrap;}
.vpts{font-size:11px;color:var(--t2);margin-top:1px;}
.vtitle{font-size:13px;color:var(--t2);line-height:1.45;margin-bottom:2px;}
/* chips */
.chip{display:inline-flex;align-items:center;gap:3px;font-family:var(--fd);font-size:9px;font-weight:700;padding:2px 7px;border-radius:20px;}
.cs{background:rgba(176,184,204,.12);color:var(--silver);border:1px solid rgba(176,184,204,.25);}
.cg{background:rgba(245,166,35,.12);color:var(--gold);border:1px solid rgba(245,166,35,.25);}
.cd{background:rgba(80,200,240,.12);color:var(--diamond);border:1px solid rgba(80,200,240,.25);}
.cr{background:rgba(232,52,90,.12);color:var(--ruby2);border:1px solid rgba(232,52,90,.25);}
.c1{background:rgba(245,166,35,.15);color:var(--gold);border:1px solid rgba(245,166,35,.3);font-size:8px;}
/* sub btn */
.subbtn{
  padding:5px 12px;border-radius:20px;
  border:1px solid var(--ruby);background:transparent;color:var(--ruby);
  font-family:var(--fd);font-size:10px;font-weight:700;cursor:pointer;
  transition:all .2s;flex-shrink:0;
}
.subbtn.on{background:var(--bg3);border-color:var(--b2);color:var(--t2);}
/* action bar */
.vacts{display:flex;padding:6px 16px 10px;gap:4px;border-top:1px solid var(--b1);}
.vact{
  flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;
  background:none;border:none;cursor:pointer;padding:5px;
  border-radius:10px;color:var(--t2);transition:background .15s;
}
.vact:hover{background:var(--bg3);}
.vact.liked{color:var(--ruby);}
.vact svg{width:19px;height:19px;}
.vact-c{font-family:var(--fd);font-size:10px;font-weight:600;}

/* ════════════════════════════════════
   REELS PLAYER  (fullscreen swipe)
════════════════════════════════════ */
#pg-reels{background:#000;z-index:100;}
.reel-vp{flex:1;overflow:hidden;position:relative;touch-action:none;}
.reel-stack{display:flex;flex-direction:column;will-change:transform;}
.reel-slide{
  width:100%;flex-shrink:0;position:relative;background:#000;
  display:flex;align-items:center;justify-content:center;overflow:hidden;
}

/* ── EMBED WRAPPER ── */
.embed-wrap{
  position:absolute;inset:0;overflow:hidden;
  pointer-events:none;
}
.embed-wrap iframe{
  position:absolute;
  top:50%;left:50%;
  width:100%;
  height:100%;
  min-width:100%;
  min-height:100%;
  transform:translate(-50%,-50%);
  border:none;
  pointer-events:none;
}
.embed-tap{position:absolute;inset:0;z-index:3;cursor:pointer;}
.reel-tfade{position:absolute;top:0;left:0;right:0;height:200px;background:linear-gradient(to bottom,rgba(0,0,0,.6),transparent);pointer-events:none;z-index:4;}
.reel-bfade{position:absolute;bottom:0;left:0;right:0;height:280px;background:linear-gradient(to top,rgba(0,0,0,.85),transparent);pointer-events:none;z-index:4;}
.reel-ph{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;z-index:1;}
.reel-ph-emoji{font-size:72px;opacity:.5;}
.reel-ph-txt{font-family:var(--fd);font-size:12px;color:rgba(255,255,255,.3);font-weight:600;}
.reel-top{position:absolute;top:0;left:0;right:0;padding:52px 16px 0;z-index:6;display:flex;align-items:center;justify-content:space-between;}
.reel-topbrand{font-family:var(--fd);font-size:18px;font-weight:800;color:#fff;}
.reel-topbrand em{color:var(--ruby);font-style:normal;}
.reel-back{
  width:36px;height:36px;border-radius:50%;
  background:rgba(0,0,0,.35);backdrop-filter:blur(8px);
  border:none;color:#fff;font-size:18px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
}
.reel-back:hover{background:rgba(0,0,0,.55);}
.reel-dots{
  position:absolute;top:44px;left:50%;transform:translateX(-50%);
  display:flex;gap:4px;z-index:6;
}
.rdot{width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.28);transition:all .25s;}
.rdot.on{width:16px;border-radius:2px;background:#fff;}
.reel-info{
  position:absolute;bottom:0;left:0;right:72px;
  padding:0 16px 32px;z-index:6;
}
.reel-crow{display:flex;align-items:center;gap:10px;margin-bottom:8px;}
.reel-av{
  width:40px;height:40px;border-radius:50%;border:2px solid #fff;
  display:flex;align-items:center;justify-content:center;
  font-family:var(--fd);font-size:12px;font-weight:700;flex-shrink:0;
}
.reel-cname{font-family:var(--fd);font-size:14px;font-weight:700;color:#fff;display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
.reel-follow{
  padding:4px 13px;border-radius:20px;
  border:1.5px solid #fff;background:transparent;
  color:#fff;font-family:var(--fd);font-size:10px;font-weight:700;
  cursor:pointer;transition:all .2s;white-space:nowrap;
}
.reel-follow.on{background:rgba(255,255,255,.15);border-color:rgba(255,255,255,.4);}
.reel-title{font-size:13px;color:rgba(255,255,255,.82);line-height:1.45;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.reel-ptschip{
  display:inline-flex;align-items:center;gap:4px;margin-top:7px;
  font-family:var(--fd);font-size:9px;font-weight:700;
  padding:3px 10px;border-radius:20px;
  background:rgba(0,0,0,.38);backdrop-filter:blur(6px);
  border:1px solid rgba(255,255,255,.13);color:rgba(255,255,255,.75);
}
.reel-acts{
  position:absolute;bottom:32px;right:0;width:68px;
  display:flex;flex-direction:column;align-items:center;gap:20px;
  z-index:6;padding-right:8px;
}
.ract{
  display:flex;flex-direction:column;align-items:center;gap:4px;
  background:none;border:none;cursor:pointer;color:#fff;
}
.ract-ic{
  width:44px;height:44px;border-radius:50%;
  background:rgba(255,255,255,.12);backdrop-filter:blur(8px);
  display:flex;align-items:center;justify-content:center;
  font-size:20px;transition:all .15s;
}
.ract:active .ract-ic{transform:scale(.9);}
.ract.liked .ract-ic{background:rgba(232,52,90,.5);}
.ract-c{font-family:var(--fd);font-size:11px;font-weight:700;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.5);}
.pause-ring{
  position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
  width:72px;height:72px;border-radius:50%;
  background:rgba(0,0,0,.45);backdrop-filter:blur(6px);
  display:flex;align-items:center;justify-content:center;
  font-size:26px;z-index:5;
  opacity:0;pointer-events:none;transition:opacity .2s;
}
.pause-ring.show{opacity:1;}
.reel-cue{
  position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
  width:72px;height:72px;border-radius:50%;
  background:rgba(0,0,0,.45);backdrop-filter:blur(6px);
  display:flex;align-items:center;justify-content:center;
  font-size:26px;z-index:8;
  opacity:0;pointer-events:none;transition:opacity .2s;
}
.reel-cue.show{opacity:1;}

/* ════════════════════════════════════
   UPLOAD TAB
════════════════════════════════════ */
.uscroll{flex:1;overflow-y:auto;padding:16px;}
.uscroll::-webkit-scrollbar{display:none;}
.upzone{
  border:1.5px dashed rgba(232,52,90,.28);border-radius:var(--r3);
  padding:28px 20px;text-align:center;background:rgba(232,52,90,.03);
  margin-bottom:20px;cursor:pointer;transition:all .2s;
}
.upzone:hover{border-color:var(--ruby);background:rgba(232,52,90,.07);}
.upzone-ic{font-size:38px;margin-bottom:8px;}
.upzone-t{font-family:var(--fd);font-size:16px;font-weight:700;margin-bottom:5px;}
.upzone-s{font-size:12px;color:var(--t2);line-height:1.5;}
.slbl{font-family:var(--fd);font-size:10px;font-weight:700;color:var(--t2);letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;}
.upload-card{background:var(--bg3);border:1px solid var(--b1);border-radius:16px;padding:14px;margin-bottom:14px;}
.urow{display:flex;gap:8px;margin-bottom:12px;align-items:center;}
.uinput{
  flex:1;padding:12px 14px;background:var(--bg3);border:1px solid var(--b1);
  border-radius:var(--r);color:var(--t1);font-family:var(--fb);font-size:13px;
  outline:none;transition:border-color .2s;
}
.urow .uinput{flex:1;min-width:0;}
.uinput:focus{border-color:var(--ruby);}
.uinput::placeholder{color:var(--t2);opacity:.8;}
.usbtn{
  min-width:98px;padding:12px 16px;background:var(--ruby);border:none;border-radius:var(--r);
  color:#fff;font-family:var(--fd);font-size:12px;font-weight:700;
  cursor:pointer;white-space:nowrap;transition:opacity .15s;
}
.usbtn:hover{opacity:.85;}
.udesc{display:block;width:100%;resize:vertical;line-height:1.45;min-height:96px;max-height:180px;margin-bottom:8px;}
.emoji-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:2px;}
.emoji-btn{
  border:1px solid var(--b1);background:var(--bg2);color:var(--t1);
  border-radius:999px;padding:6px 10px;font-size:16px;line-height:1;
  cursor:pointer;transition:all .15s;
}
.emoji-btn:hover{border-color:var(--ruby);transform:translateY(-1px);}
.uinfo-box{
  background:rgba(80,200,240,.06);border:1px solid rgba(80,200,240,.15);
  border-radius:var(--r);padding:12px 14px;margin-bottom:16px;
}
.uinfo-title{font-family:var(--fd);font-size:11px;font-weight:700;color:var(--diamond);margin-bottom:6px;}
.uinfo-item{font-size:12px;color:var(--t2);margin-bottom:4px;display:flex;gap:6px;}

@media (max-width:420px){
  .upload-card{padding:12px;}
  .urow{flex-direction:column;align-items:stretch;gap:10px;}
  .usbtn{width:100%;}
}
.pitem{
  display:flex;align-items:center;gap:10px;
  background:var(--bg3);border:1px solid var(--b1);
  border-radius:var(--r);padding:12px 14px;margin-bottom:8px;
}
.pind{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.pi-p{background:var(--gold);box-shadow:0 0 6px rgba(245,166,35,.4);}
.pi-a{background:var(--green);box-shadow:0 0 6px rgba(62,207,142,.4);}
.pi-r{background:var(--ruby);}
.pinfo{flex:1;min-width:0;}
.ptitle{font-family:var(--fd);font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.psub{font-size:11px;color:var(--t2);margin-top:2px;}
.pstat{font-family:var(--fd);font-size:9px;font-weight:700;padding:3px 8px;border-radius:10px;}
.psp{background:rgba(245,166,35,.1);color:var(--gold);}
.psa{background:rgba(62,207,142,.1);color:var(--green);}
.psr{background:rgba(232,52,90,.1);color:var(--ruby);}

/* ════════════════════════════════════
   RUBY TAB
════════════════════════════════════ */
.rscroll{flex:1;overflow-y:auto;padding:16px;}
.rscroll::-webkit-scrollbar{display:none;}
.ruby-hero{
  background:linear-gradient(135deg,rgba(232,52,90,.12),rgba(245,166,35,.06));
  border:1px solid rgba(232,52,90,.18);border-radius:var(--r3);
  padding:22px 20px;margin-bottom:16px;text-align:center;
}
.rh-ic{font-size:42px;margin-bottom:6px;}
.rh-t{font-family:var(--fd);font-size:22px;font-weight:800;}
.rh-s{font-size:12px;color:var(--t2);margin-top:4px;line-height:1.5;}
.ptsgrid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;}
.ptsc{background:var(--bg3);border:1px solid var(--b1);border-radius:var(--r);padding:14px;}
.ptsc-ic{font-size:20px;margin-bottom:5px;}
.ptsc-l{font-size:11px;color:var(--t2);}
.ptsc-v{font-family:var(--fd);font-size:20px;font-weight:700;color:var(--ruby);margin-top:2px;}
.tierc{
  display:flex;align-items:center;gap:12px;
  background:var(--bg3);border:1px solid var(--b1);
  border-radius:var(--r);padding:14px;margin-bottom:8px;
}
.tierc.ach{border-color:rgba(232,52,90,.28);background:rgba(232,52,90,.04);}
.tgem{font-size:28px;flex-shrink:0;}
.tbody{flex:1;min-width:0;}
.tname{font-family:var(--fd);font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
.treq{font-size:11px;color:var(--t2);margin-top:3px;}
.tbar{height:3px;background:var(--bg4);border-radius:2px;margin-top:6px;overflow:hidden;}
.tfill{height:100%;border-radius:2px;background:linear-gradient(90deg,var(--ruby),var(--gold));}
.tba{font-family:var(--fd);font-size:9px;font-weight:700;padding:3px 8px;border-radius:10px;background:rgba(62,207,142,.1);color:var(--green);border:1px solid rgba(62,207,142,.25);}
.tbl{font-family:var(--fd);font-size:9px;font-weight:700;padding:3px 8px;border-radius:10px;background:var(--bg4);color:var(--t3);border:1px solid var(--b1);}
.lbi{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--b1);}
.lbi.me{background:rgba(232,52,90,.05);border-radius:var(--r);padding:9px 10px;border:1px solid rgba(232,52,90,.12);}
.lbrank{font-family:var(--fd);font-size:14px;font-weight:700;color:var(--t3);width:24px;text-align:center;flex-shrink:0;}
.lbrank.top{color:var(--gold);}
.lbav{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--fd);font-size:10px;font-weight:700;color:#fff;flex-shrink:0;}
.lbinfo{flex:1;}
.lbn{font-family:var(--fd);font-size:13px;font-weight:600;}
.lbp{font-size:11px;color:var(--t2);margin-top:2px;}

/* ════════════════════════════════════
   PROFILE TAB
════════════════════════════════════ */
.pscroll{flex:1;overflow-y:auto;}
.pscroll::-webkit-scrollbar{display:none;}
.pcover{
  height:108px;flex-shrink:0;position:relative;
  background:linear-gradient(135deg,#190814,#0a0a1e,#081814);
}
.pcoverglow{position:absolute;inset:0;background:radial-gradient(ellipse at 25% 50%,rgba(232,52,90,.2),transparent 55%),radial-gradient(ellipse at 75% 50%,rgba(80,200,240,.1),transparent 55%);}
.pbody{padding:0 20px 20px;}
.pavwrap{margin-top:-28px;margin-bottom:10px;display:inline-block;}
.pav{width:60px;height:60px;border-radius:50%;border:3px solid var(--bg);display:flex;align-items:center;justify-content:center;font-family:var(--fd);font-size:20px;font-weight:700;}
.pname{font-family:var(--fd);font-size:20px;font-weight:800;margin-bottom:6px;}
.psearchbtn{
  border:1px solid var(--b2);background:var(--bg3);color:var(--t1);
  border-radius:999px;padding:7px 12px;font-family:var(--fd);font-size:11px;
  font-weight:700;cursor:pointer;transition:all .15s;margin-bottom:10px;
}
.psearchbtn:hover{border-color:var(--ruby);color:var(--ruby);}
.pbadges{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;}
.pstats{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:16px;}
.pst{background:var(--bg3);border:1px solid var(--b1);border-radius:var(--r);padding:10px 6px;text-align:center;}
.pstn{font-family:var(--fd);font-size:15px;font-weight:700;}
.pstl{font-size:9px;color:var(--t2);margin-top:3px;text-transform:uppercase;letter-spacing:.4px;}
.rmrow{display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;}
.rml{font-family:var(--fd);font-size:12px;font-weight:600;}
.rmpct{font-size:11px;color:var(--t2);}
.rmbar{height:5px;background:var(--bg3);border-radius:3px;overflow:hidden;margin-bottom:16px;}
.rmfill{height:100%;border-radius:3px;background:linear-gradient(90deg,var(--ruby),var(--gold));transition:width .8s ease;}
.logoutbtn{
  display:block;width:100%;padding:14px;margin-top:8px;
  background:var(--bg3);border:1px solid var(--b1);border-radius:var(--r);
  color:var(--t2);font-family:var(--fd);font-size:13px;font-weight:600;
  cursor:pointer;text-align:center;transition:all .2s;
}
.logoutbtn:hover{border-color:var(--ruby);color:var(--ruby);}

/* ════════════════════════════════════
   LEGAL TAB
════════════════════════════════════ */
.lscroll{flex:1;overflow-y:auto;padding:16px;}
.lscroll::-webkit-scrollbar{display:none;}
.legal-section{margin-bottom:20px;}
.legal-title{font-family:var(--fd);font-size:16px;font-weight:700;margin-bottom:10px;color:var(--ruby);}
.legal-text{font-size:12px;color:var(--t2);line-height:1.6;margin-bottom:10px;}
.legal-list{font-size:12px;color:var(--t2);line-height:1.8;margin-left:16px;}
.legal-list li{margin-bottom:6px;}

/* ════════════════════════════════════
   ADMIN TAB
════════════════════════════════════ */
.ascroll{flex:1;overflow-y:auto;padding:16px;}
.ascroll::-webkit-scrollbar{display:none;}
.astats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:20px;}
.asc{background:var(--bg3);border:1px solid var(--b1);border-radius:var(--r);padding:12px;text-align:center;}
.ascn{font-family:var(--fd);font-size:22px;font-weight:800;color:var(--ruby);}
.ascl{font-size:9px;color:var(--t2);text-transform:uppercase;letter-spacing:.5px;margin-top:3px;}
.acard{background:var(--bg3);border:1px solid var(--b1);border-radius:var(--r);padding:14px;margin-bottom:10px;transition:opacity .3s,transform .3s;}
.actop{display:flex;gap:10px;margin-bottom:10px;}
.acth{width:56px;height:56px;border-radius:10px;background:var(--bg4);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;}
.act{font-family:var(--fd);font-size:13px;font-weight:700;margin-bottom:3px;}
.acc{font-size:11px;color:var(--t2);margin-bottom:2px;}
.acu{font-size:10px;color:var(--t3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.acbtns{display:flex;gap:8px;}
.abta{flex:1;padding:10px;border-radius:var(--r);background:rgba(62,207,142,.1);border:1px solid rgba(62,207,142,.22);color:var(--green);font-family:var(--fd);font-size:12px;font-weight:700;cursor:pointer;transition:background .15s;}
.abta:hover{background:rgba(62,207,142,.18);}
.abtr{flex:1;padding:10px;border-radius:var(--r);background:rgba(232,52,90,.08);border:1px solid rgba(232,52,90,.2);color:var(--ruby);font-family:var(--fd);font-size:12px;font-weight:700;cursor:pointer;transition:background .15s;}
.abtr:hover{background:rgba(232,52,90,.15);}
.ac-preview{
  position:relative;width:100%;height:160px;border-radius:var(--r);overflow:hidden;
  margin-bottom:10px;background:#000;
}
.ac-preview iframe{position:absolute;top:-30px;left:-20px;width:calc(100% + 40px);height:calc(100% + 60px);border:none;}
.ac-preview-label{position:absolute;top:8px;left:8px;font-family:var(--fd);font-size:9px;font-weight:700;padding:3px 8px;border-radius:10px;background:rgba(0,0,0,.6);color:var(--t2);backdrop-filter:blur(4px);z-index:2;}

/* ════════════════════════════════════
   OVERLAYS
════════════════════════════════════ */
.overlay{display:none;position:absolute;inset:0;background:rgba(0,0,0,.55);z-index:60;backdrop-filter:blur(3px);}
.overlay.on{display:block;}
.csheet{display:none;position:absolute;bottom:0;left:0;right:0;background:var(--bg2);border-radius:24px 24px 0 0;border-top:1px solid var(--b2);z-index:70;max-height:65%;flex-direction:column;}
.csheet.on{display:flex;}
.shdrag{width:36px;height:4px;background:var(--bg4);border-radius:2px;margin:12px auto 0;flex-shrink:0;}
.shttl{font-family:var(--fd);font-size:14px;font-weight:700;padding:12px 16px;border-bottom:1px solid var(--b1);flex-shrink:0;}
.cmtlist{flex:1;overflow-y:auto;padding:12px 16px;}
.cmtlist::-webkit-scrollbar{display:none;}
.cmti{display:flex;gap:8px;margin-bottom:14px;}
.cmtav{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--fd);font-size:9px;font-weight:700;flex-shrink:0;}
.cmtb{flex:1;}
.cmtu{font-family:var(--fd);font-size:11px;font-weight:700;color:var(--ruby);margin-bottom:2px;}
.cmtt{font-size:13px;color:var(--t1);line-height:1.4;}
.cmttime{font-size:10px;color:var(--t3);margin-top:3px;}
.cmtrow{display:flex;gap:8px;padding:10px 14px 16px;border-top:1px solid var(--b1);flex-shrink:0;}
.cmtinp{flex:1;padding:10px 14px;background:var(--bg3);border:1px solid var(--b1);border-radius:24px;color:var(--t1);font-family:var(--fb);font-size:13px;outline:none;transition:border-color .2s;}
.cmtinp:focus{border-color:var(--ruby);}
.cmtinp::placeholder{color:var(--t3);}
.cmtsend{width:38px;height:38px;border-radius:50%;background:var(--ruby);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;flex-shrink:0;transition:opacity .15s;}
.cmtsend:hover{opacity:.85;}
/* Comment actions: like, reply, pin */
.cmtactions{display:flex;align-items:center;gap:10px;margin-top:5px;}
.cmtact-btn{background:none;border:none;cursor:pointer;font-size:11px;color:var(--t3);padding:0;display:flex;align-items:center;gap:3px;transition:color .15s;}
.cmtact-btn:hover{color:var(--t1);}
.cmtact-btn.liked{color:#e05;}
.cmtact-btn.pinbtn{color:var(--t3);}
.cmtact-btn.pinbtn.active{color:#f5a623;}
/* Pinned badge */
.cmtpin-badge{display:inline-flex;align-items:center;gap:3px;font-size:9px;font-weight:700;color:#f5a623;background:rgba(245,166,35,.12);border-radius:4px;padding:1px 5px;margin-bottom:3px;}
/* Reply tag above input */
.cmtreplying{display:none;align-items:center;justify-content:space-between;padding:5px 14px;background:rgba(245,166,35,.1);border-top:1px solid var(--b1);font-size:11px;color:#f5a623;flex-shrink:0;}
.cmtreplying.on{display:flex;}
/* Nested replies */
.cmtreplies{margin-top:6px;padding-left:12px;border-left:2px solid var(--b2);}
.cmtreplies .cmti{margin-bottom:8px;}
.ssheet{display:none;position:absolute;bottom:0;left:0;right:0;background:var(--bg2);border-radius:24px 24px 0 0;border-top:1px solid var(--b2);z-index:70;padding:0 16px 24px;}
.ssheet.on{display:block;}
.sgrid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;padding-top:4px;}
.sopt{display:flex;flex-direction:column;align-items:center;gap:6px;background:var(--bg3);border:none;border-radius:var(--r);padding:12px 6px;cursor:pointer;transition:background .15s;}
.sopt:hover{background:var(--bg4);}
.sopic{font-size:24px;}
.soplb{font-family:var(--fd);font-size:9px;font-weight:600;color:var(--t2);}
.usrsheet,.pubsheet{display:none;position:absolute;bottom:0;left:0;right:0;background:var(--bg2);border-radius:24px 24px 0 0;border-top:1px solid var(--b2);z-index:70;max-height:78%;overflow:hidden;}
.usrsheet.on,.pubsheet.on{display:flex;flex-direction:column;}
.sheethdr{padding:12px 14px;border-bottom:1px solid var(--b1);display:flex;align-items:center;justify-content:space-between;gap:10px;}
.sheetttl{font-family:var(--fd);font-size:14px;font-weight:700;color:var(--t1);}
.sheetclose{width:30px;height:30px;border-radius:50%;border:1px solid var(--b2);background:var(--bg3);color:var(--t2);cursor:pointer;}
.usrsearch-wrap{padding:10px 14px;border-bottom:1px solid var(--b1);}
.usrsearch-inp{width:100%;padding:11px 13px;border-radius:12px;background:var(--bg3);border:1px solid var(--b1);color:var(--t1);font-size:13px;outline:none;}
.usrsearch-inp:focus{border-color:var(--ruby);}
.usrresults{flex:1;overflow-y:auto;padding:10px 14px 16px;}
.usrrow{display:flex;align-items:center;gap:10px;background:var(--bg3);border:1px solid var(--b1);border-radius:12px;padding:10px;margin-bottom:8px;cursor:pointer;}
.usrav{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-family:var(--fd);font-size:12px;font-weight:700;flex-shrink:0;}
.usrmeta{flex:1;min-width:0;}
.usrn{font-family:var(--fd);font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.usrs{font-size:11px;color:var(--t2);margin-top:2px;}
.usrfbtn{padding:7px 10px;border-radius:11px;border:1px solid var(--ruby);background:transparent;color:var(--ruby);font-family:var(--fd);font-size:10px;font-weight:700;cursor:pointer;}
.usrfbtn.on{border-color:var(--b2);color:var(--t2);background:var(--bg4);}
.pubbody{flex:1;overflow-y:auto;padding:14px;}
.pubtop{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.pubav{width:54px;height:54px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-family:var(--fd);font-size:18px;font-weight:700;flex-shrink:0;}
.pubname{font-family:var(--fd);font-size:16px;font-weight:800;}
.pubrole{font-size:11px;color:var(--t2);margin-top:2px;}
.pubfollow{margin-left:auto;padding:8px 12px;border-radius:12px;border:1px solid var(--ruby);background:transparent;color:var(--ruby);font-family:var(--fd);font-size:11px;font-weight:700;cursor:pointer;}
.pubfollow.on{border-color:var(--b2);color:var(--t2);background:var(--bg4);}
.pubstats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:10px 0 12px;}
.pubst{background:var(--bg3);border:1px solid var(--b1);border-radius:10px;padding:10px 6px;text-align:center;}
.pubstn{font-family:var(--fd);font-size:14px;font-weight:700;}
.pubstl{font-size:9px;color:var(--t2);margin-top:2px;text-transform:uppercase;}
.pubsec{font-family:var(--fd);font-size:11px;font-weight:700;color:var(--t2);letter-spacing:.6px;text-transform:uppercase;margin-bottom:8px;}
.pubgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:7px;}
.pubvid{border-radius:10px;background:var(--bg3);border:1px solid var(--b1);padding:8px;min-height:74px;display:flex;flex-direction:column;justify-content:space-between;}
.pubvidt{font-size:11px;color:var(--t1);line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.pubvidm{font-size:9px;color:var(--t3);margin-top:7px;}
.toast{position:absolute;top:58px;left:50%;transform:translateX(-50%);background:rgba(14,14,26,.95);color:var(--t1);font-family:var(--fd);font-size:11px;font-weight:600;padding:8px 16px;border-radius:20px;border:1px solid var(--b2);z-index:200;white-space:nowrap;pointer-events:none;opacity:0;transition:opacity .2s;}
.toast.on{opacity:1;}
.ptpop{position:absolute;right:20px;background:rgba(232,52,90,.88);color:#fff;font-family:var(--fd);font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;z-index:300;pointer-events:none;opacity:0;}
.ptpop.pop{animation:ppAnim 1.5s ease forwards;}
@keyframes ppAnim{0%{opacity:0;transform:translateY(0);}20%{opacity:1;transform:translateY(-22px);}70%{opacity:1;transform:translateY(-30px);}100%{opacity:0;transform:translateY(-42px);}}
.shdr{padding:16px 18px 12px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--b1);flex-shrink:0;}
.shdr h1{font-family:var(--fd);font-size:20px;font-weight:700;}

@media (max-width: 480px){
  .feed-hdr{padding:12px 14px;align-items:flex-start;flex-wrap:wrap;}
  .feed-brand{font-size:20px;}
  .feed-mode-toggle{margin-left:auto;}
  .vmeta{padding:10px 12px 6px;}
  .vacts{padding:6px 10px 10px;}
  .reel-top{padding:calc(14px + env(safe-area-inset-top)) 12px 0;}
  .reel-dots{top:calc(10px + env(safe-area-inset-top));}
  .reel-info{right:60px;padding:0 12px calc(22px + env(safe-area-inset-bottom));}
  .reel-acts{width:60px;bottom:calc(22px + env(safe-area-inset-bottom));padding-right:6px;gap:16px;}
  .ract-ic{width:40px;height:40px;font-size:18px;}
  .ract-c{font-size:10px;}
  .reel-title{-webkit-line-clamp:3;}
  .shdr{padding:14px 14px 10px;}
}

@media (max-width: 360px){
  .feed-brand{font-size:18px;}
  .fmt-btn{padding:5px 10px;font-size:9px;}
  .reel-crow{align-items:flex-start;gap:8px;}
  .reel-cname{font-size:13px;}
  .reel-follow{padding:4px 10px;font-size:9px;}
  .pstats{grid-template-columns:repeat(2,1fr);}
  .ptsgrid{grid-template-columns:1fr;}
  .astats{grid-template-columns:1fr;}
}
</style>
</head>
<body>
<div class="device" id="device">
  <div class="toast" id="toast"></div>
  <div class="ptpop" id="ptpop"></div>

  <!-- ══════════════ LOGIN ══════════════ -->
  <div class="page show" id="pg-login">
    <div class="login-hero">
      <div class="hero-rings">
        <div class="hr"></div><div class="hr"></div><div class="hr"></div><div class="hr"></div>
      </div>
      <div class="hero-center">
        <div class="hero-gem">💎</div>
        <div class="hero-brand">Shorts<em>Play</em></div>
        <div class="hero-sub">Play. Earn. Rise.</div>
      </div>
    </div>
    <div class="login-sheet">
      <div class="ltabs">
        <button class="ltab on" onclick="setLTab('in',this)">Sign In</button>
        <button class="ltab" onclick="setLTab('up',this)">Register</button>
      </div>
      <!-- sign in -->
      <div class="lform" id="lf-in">
        <input type="text" placeholder="Email or mobile" id="li-email"/>
        <div class="pwd-wrap">
          <input type="password" placeholder="Password" id="li-password"/>
          <button type="button" class="pwd-toggle" id="li-password-toggle" aria-label="Show password" onclick="togglePassword('li-password','li-password-toggle')">👁️</button>
        </div>
        <button class="btnmain" onclick="doLogin()">Sign In</button>
        <p class="ldivider">Users, shop owners, and service providers can sign in with the same global account</p>
      </div>
      <!-- register -->
      <div class="lform" id="lf-up" style="display:none;">
        <!-- Step 1: Fill details -->
        <div id="lu-step1" style="display:flex;flex-direction:column;gap:10px;">
          <input type="text" placeholder="Username @handle" id="lu-username" style="width:100%;"/>
          <input type="email" placeholder="Email" id="lu-email" style="width:100%;"/>
          <input type="tel" placeholder="10-digit mobile" id="lu-mobile" maxlength="10" inputmode="numeric" style="width:100%;"/>
          <div class="pwd-wrap">
            <input type="password" placeholder="Password" id="lu-password"/>
            <button type="button" class="pwd-toggle" id="lu-password-toggle" aria-label="Show password" onclick="togglePassword('lu-password','lu-password-toggle')">👁️</button>
          </div>
          <button class="btnmain" id="lu-submit-btn" onclick="doRegister()">Create Account</button>
        </div>
        <!-- Step 2: OTP verification (shown after first submit) -->
        <div id="lu-step2" style="display:none;flex-direction:column;gap:10px;">
          <div style="background:var(--bg3);border-radius:var(--r);padding:12px;text-align:center;">
            <div style="font-size:20px;margin-bottom:6px;">📱</div>
            <div style="font-size:13px;color:var(--t1);font-weight:600;margin-bottom:4px;">OTP Sent!</div>
            <div id="lu-otp-msg" style="font-size:12px;color:var(--t2);line-height:1.4;"></div>
          </div>
          <input type="text" placeholder="Enter 6-digit OTP" id="lu-otp" maxlength="6" inputmode="numeric" autocomplete="one-time-code" style="width:100%;letter-spacing:6px;text-align:center;font-size:20px;font-weight:700;"/>
          <button class="btnmain" onclick="doVerifyOtp()">Verify &amp; Complete Registration</button>
          <button onclick="doBackToRegister()" style="background:transparent;border:1px solid var(--b2);color:var(--t2);border-radius:var(--r);padding:12px;font-size:14px;cursor:pointer;width:100%;">← Back to Edit Details</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════ REELS PLAYER ══════════════ -->
  <div class="page" id="pg-reels">
    <div class="reel-dots" id="reelDots"></div>
    <div class="reel-vp" id="reelVp">
      <div class="reel-stack" id="reelStack"></div>
    </div>
    <div class="reel-cue" id="reelCue">⏸</div>
  </div>

  <!-- ══════════════ MAIN APP ══════════════ -->
  <div class="page" id="pg-app">
    <div class="app-body">

      <!-- FEED -->
      <div class="tab on" id="tab-feed">
        <div class="feed-hdr">
          <div class="feed-brand">Shorts<em>Play</em></div>
          <div style="display:flex;align-items:center;gap:10px;">
            <div id="myHdrBadge"></div>
            <div class="feed-mode-toggle">
              <button class="fmt-btn on" id="fmtFeed" onclick="setFeedMode('feed',this)">📋 Feed</button>
              <button class="fmt-btn" id="fmtReels" onclick="setFeedMode('reels',this)">▶ Reels</button>
            </div>
          </div>
        </div>
        <div class="feed-scroll" id="feedScroll"></div>
      </div>

      <!-- UPLOAD -->
      <div class="tab" id="tab-upload">
        <div class="shdr"><h1>Add Short</h1></div>
        <div class="uscroll">
          <div class="uinfo-box">
            <div class="uinfo-title">How To Submit</div>
            <div class="uinfo-item">1. Paste a public YouTube Shorts or Google Drive video link.</div>
            <div class="uinfo-item">2. Add a short description so users know what your video is about.</div>
            <div class="uinfo-item">3. Tap Submit and wait for admin approval.</div>
          </div>

          <div class="upload-card">
            <div class="slbl">Paste YouTube Shorts or Google Drive link</div>
            <div class="urow">
              <input class="uinput" id="urlInput" placeholder="https://youtube.com/shorts/... or drive.google.com/file/..."/>
              <button class="usbtn" onclick="submitVideo()">Submit</button>
            </div>
            <div class="slbl" style="margin-top:2px;">Description (Optional)</div>
            <textarea class="uinput udesc" id="descInput" rows="3" placeholder="Example: Morning workout highlights at city court"></textarea>
            <div class="emoji-row" aria-label="Add emoji to description">
              <button class="emoji-btn" type="button" onclick="addDescEmoji('🔥')">🔥</button>
              <button class="emoji-btn" type="button" onclick="addDescEmoji('💯')">💯</button>
              <button class="emoji-btn" type="button" onclick="addDescEmoji('😍')">😍</button>
              <button class="emoji-btn" type="button" onclick="addDescEmoji('😂')">😂</button>
              <button class="emoji-btn" type="button" onclick="addDescEmoji('🎉')">🎉</button>
              <button class="emoji-btn" type="button" onclick="addDescEmoji('💪')">💪</button>
              <button class="emoji-btn" type="button" onclick="addDescEmoji('✨')">✨</button>
            </div>
          </div>

          <div class="slbl" style="margin-bottom:10px;">My Submissions</div>
          <div id="pendingList"></div>
        </div>
      </div>

      <!-- RUBY -->
      <div class="tab" id="tab-ruby">
        <div class="shdr"><h1>Ruby System</h1></div>
        <div class="rscroll">
          <div class="ruby-hero">
            <div class="rh-ic">💎</div>
            <div class="rh-t">Earn Ruby Points</div>
            <div class="rh-s">Get likes, comments, shares &amp; subscribers<br/>to climb the ruby tiers</div>
          </div>
          <div class="slbl" style="margin-bottom:10px;">How to earn</div>
          <div class="ptsgrid" id="ptsGrid"></div>
          <div class="slbl" style="margin:2px 0 10px;">Viewer points</div>
          <div class="ptsgrid" id="viewerPtsGrid"></div>
          <div class="slbl" style="margin:8px 0 10px;">Ruby Tiers</div>
          <div id="tierList"></div>
          <div class="slbl" style="margin:16px 0 10px;">Leaderboard</div>
          <div id="lbList"></div>
          <div style="height:16px;"></div>
        </div>
      </div>

      <!-- PROFILE -->
      <div class="tab" id="tab-profile">
        <div class="pscroll">
          <div class="pcover"><div class="pcoverglow"></div></div>
          <div class="pbody">
            <div class="pavwrap"><div class="pav" id="profAv"></div></div>
            <div class="pname" id="profName"></div>
            <button class="psearchbtn" type="button" onclick="openUserSearch()">🔍 Search Users To Follow</button>
            <div class="pbadges" id="profBadges"></div>
            <div class="pstats" id="profStats"></div>
            <div class="rmrow">
              <span class="rml" id="rmLabel">Progress</span>
              <span class="rmpct" id="rmPct"></span>
            </div>
            <div class="rmbar"><div class="rmfill" id="rmFill" style="width:0%"></div></div>
            <div class="slbl" style="margin-bottom:10px;">Ruby Tiers</div>
            <div id="profTiers"></div>
            <button class="logoutbtn" onclick="doLogout()">Sign Out</button>
          </div>
        </div>
      </div>

      <!-- LEGAL -->
      <div class="tab" id="tab-legal">
        <div class="shdr"><h1>Legal & Policies</h1></div>
        <div class="lscroll">
          <div class="legal-section">
            <div class="legal-title">Terms of Service</div>
            <div class="legal-text">By using ShortsPlay, you agree to comply with all applicable laws and regulations. Users must be at least 13 years old (or the minimum age required in your jurisdiction) to use this platform.</div>
            <div class="legal-text">• Creators are responsible for ensuring they have rights to upload content</div>
            <div class="legal-text">• Admin may remove content that violates these terms without notice</div>
            <div class="legal-text">• Users agree not to upload content that is illegal, offensive, or infringes rights</div>
          </div>

          <div class="legal-section">
            <div class="legal-title">Privacy Policy</div>
            <div class="legal-text">Your privacy matters to us. ShortsPlay collects user data only to improve your experience and provide services.</div>
            <div class="legal-text">• We do not sell your personal information to third parties</div>
            <div class="legal-text">• Your email and account details are encrypted and secured</div>
            <div class="legal-text">• You can request data deletion by contacting our support team</div>
          </div>

          <div class="legal-section">
            <div class="legal-title">Content Policy</div>
            <div class="legal-text">ShortsPlay maintains strict content standards to ensure a safe community.</div>
            <div class="legal-text">Prohibited content includes:</div>
            <ul class="legal-list">
              <li>• Hate speech, discrimination, or harassment</li>
              <li>• Violence, graphic content, or illegal activities</li>
              <li>• Copyright infringement or unauthorized content</li>
              <li>• Spam, scams, or misleading information</li>
              <li>• Explicit sexual or adult content</li>
              <li>• Personal identification of private individuals</li>
            </ul>
          </div>

          <div class="legal-section">
            <div class="legal-title">Ruby Points Policy</div>
            <div class="legal-text">Ruby Points are a virtual reward system and have no real monetary value. They cannot be converted to cash or sold.</div>
            <div class="legal-text">• Points earned through legitimate engagement only</div>
            <div class="legal-text">• ShortsPlay reserves the right to revoke points for policy violations</div>
            <div class="legal-text">• Tier status is determined by accumulated ruby points</div>
          </div>

          <div class="legal-section">
            <div class="legal-title">Disclaimer</div>
            <div class="legal-text">ShortsPlay is provided "as is" without warranties of any kind. We are not liable for damages resulting from service interruptions, data loss, or user-generated content.</div>
          </div>

          <div class="legal-section">
            <div class="legal-title">Contact & Support</div>
            <div class="legal-text">For questions regarding these policies or to report violations, contact our support team at support@shortsplay.co or visit our help center.</div>
          </div>

          <div style="height:20px;"></div>
        </div>
      </div>

      <!-- ADMIN -->
      <div class="tab" id="tab-admin">
        <div class="shdr">
          <h1>Admin Panel</h1>
          <span style="font-family:var(--fd);font-size:10px;font-weight:700;padding:4px 10px;border-radius:10px;background:rgba(232,52,90,.1);color:var(--ruby);border:1px solid rgba(232,52,90,.22);">MOD</span>
        </div>
        <div class="ascroll">
          <div class="astats" id="adminStats"></div>
          <div class="slbl" id="adminSectionLabel" style="margin-bottom:12px;">Pending Review</div>
          <div id="adminQueue"></div>
        </div>
      </div>

    </div>
    <!-- BOTTOM NAV -->
    <nav class="bnav" id="bnav">
      <button class="bni on" id="bn-feed" onclick="switchTab('feed',this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        <span class="bnl">Feed</span>
      </button>
      <button class="bni" id="bn-upload" onclick="switchTab('upload',this)" style="display:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        <span class="bnl">Upload</span>
      </button>
      <!-- Coupons tab removed -->
      <button class="bni" id="bn-ruby" onclick="switchTab('ruby',this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <span class="bnl">Rubies</span>
      </button>
      <button class="bni" id="bn-profile" onclick="switchTab('profile',this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        <span class="bnl">Profile</span>
      </button>
      <button class="bni" id="bn-legal" onclick="switchTab('legal',this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="bnl">Legal</span>
      </button>
      <button class="bni" id="bn-admin" onclick="switchTab('admin',this)" style="display:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span class="bnl">Admin</span>
      </button>
    </nav>
  </div>

  <!-- OVERLAYS -->
  <div class="overlay" id="overlay" onclick="closeSheets()"></div>
  <!-- Comment sheet -->
  <div class="csheet" id="csheet">
    <div class="shdrag"></div>
    <div class="shttl">Comments <span id="csheetCnt" style="color:var(--t2);font-weight:400;font-family:var(--fb);"></span></div>
    <div class="cmtlist" id="cmtList"></div>
    <div class="cmtreplying" id="cmtReplyingTag">
      <span>↩ Replying to <span id="cmtReplyingName"></span></span>
      <button style="background:none;border:none;cursor:pointer;color:var(--t2);font-size:14px;" onclick="cancelReply()">✕</button>
    </div>
    <div class="cmtrow">
      <input class="cmtinp" id="cmtInput" placeholder="Write a comment…"/>
      <button class="cmtsend" onclick="postComment()">↑</button>
    </div>
  </div>
  <!-- Share sheet -->
  <div class="ssheet" id="ssheet">
    <div class="shdrag" style="margin:12px auto 0;width:36px;height:4px;background:var(--bg4);border-radius:2px;"></div>
    <div style="font-family:var(--fd);font-size:14px;font-weight:700;padding:12px 0;">Share to</div>
    <div class="sgrid">
      <button class="sopt" onclick="doShare('WhatsApp')"><span class="sopic">💬</span><span class="soplb">WhatsApp</span></button>

      <button class="sopt" onclick="doShare('Twitter')"><span class="sopic">🐦</span><span class="soplb">Twitter</span></button>
      <button class="sopt" onclick="doShare('Facebook')"><span class="sopic">👍</span><span class="soplb">Facebook</span></button>
      <button class="sopt" onclick="doShare('Telegram')"><span class="sopic">✈️</span><span class="soplb">Telegram</span></button>
      <button class="sopt" onclick="doShare('Threads')"><span class="sopic">🔁</span><span class="soplb">Threads</span></button>
      <button class="sopt" onclick="doShare('Copy')"><span class="sopic">🔗</span><span class="soplb">Copy Link</span></button>
      <button class="sopt" onclick="doShare('More')"><span class="sopic">⋯</span><span class="soplb">More</span></button>
    </div>
  </div>

  <!-- User Search sheet -->
  <div class="usrsheet" id="userSearchSheet">
    <div class="shdrag"></div>
    <div class="sheethdr">
      <div class="sheetttl">Find Creators</div>
      <button class="sheetclose" type="button" onclick="closeSheets()">✕</button>
    </div>
    <div class="usrsearch-wrap">
      <input class="usrsearch-inp" id="userSearchInput" placeholder="Search by name or email" oninput="queueUserSearch()" />
    </div>
    <div class="usrresults" id="userSearchResults"></div>
  </div>

  <!-- Public profile sheet -->
  <div class="pubsheet" id="publicProfileSheet">
    <div class="shdrag"></div>
    <div class="sheethdr">
      <div class="sheetttl">Creator Profile</div>
      <button class="sheetclose" type="button" onclick="closeSheets()">✕</button>
    </div>
    <div class="pubbody" id="publicProfileBody"></div>
  </div>

</div><!-- /device -->

<script>
// ═══════════════════════════════════════════════
//  CONFIG
// ═══════════════════════════════════════════════
let CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
@php
  $shortsBootUser = null;
  if (auth()->check()) {
      $authUser = auth()->user();
      $shortsBootUser = [
          'id' => $authUser->id,
          'name' => $authUser->first_name ? $authUser->first_name : strtok((string) $authUser->email, '@'),
          'email' => $authUser->email,
          'role' => $authUser->role,
          'shorts_role' => $authUser->shorts_role ?? 'viewer',
          'is_admin' => $authUser->isSuperadmin(),
          'ruby_points' => $authUser->ruby_points ?? 0,
      ];
  }
@endphp
const BOOT_USER = @json($shortsBootUser);

const PTS = { like:1, comment:2, share:3, subscribe:5 };
const VIEWER_PTS = { view:1, like:1, comment:1, follow:2 };

const TIERS = [
  { id:'silver',  name:'Silver Ruby',  tag:'Silver Ruby Taker',   gem:'🥈', chipCls:'cs', thresh:5000 },
  { id:'gold',    name:'Gold Ruby',    tag:'Golden Ruby Holder',  gem:'🥇', chipCls:'cg', thresh:10000 },
  { id:'diamond', name:'Diamond Ruby', tag:'Diamond Ruby Holder', gem:'💎', chipCls:'cd', thresh:15000 },
  { id:'red',     name:'Red Ruby',     tag:'Red Ruby Achiever',   gem:'🔴', chipCls:'cr', thresh:20000 },
];

// ═══════════════════════════════════════════════
//  STATE
// ═══════════════════════════════════════════════
let ME = null;   // { id, name, role, is_admin, shorts_role, ruby_points }
let activeCmtId = null, activeShareId = null;
let replyingTo = null; // { commentId, username }
let FEED_ITEMS  = [];  // approved videos
let PENDING     = [];  // my submissions
let ADMIN_Q     = [];  // pending review queue
let ADMIN_COUNTS = { pending:0, approved:0, rejected:0 };
let ADMIN_FILTER = 'pending';
let REEL_IDX    = 0;   // active reel index
let REEL_AUDIO_ON = {}; // tracks whether a reel was unmuted by user tap
let REEL_AUTO_UNMUTE = false;
let RUBY_TIERS_DATA = [];   // from /ruby/tiers
let LB_DATA         = [];   // leaderboard
let MY_RUBY_STATS   = null; // /ruby/stats response
let USER_SEARCH_TIMER = null;
let PUBLIC_PROFILE_DATA = null;
let VIEW_TRACKED = {};

function updateViewportSizing(){
  const doc=document.documentElement;
  const viewport=window.visualViewport;
  const width=Math.min(Math.round(viewport?.width||window.innerWidth||doc.clientWidth||430),430);
  const height=Math.round(viewport?.height||window.innerHeight||doc.clientHeight||0);
  if(width>0)doc.style.setProperty('--app-width',`${width}px`);
  if(height>0)doc.style.setProperty('--app-height',`${height}px`);
}

function handleViewportResize(){
  updateViewportSizing();
  if(document.getElementById('pg-reels').classList.contains('show')&&FEED_ITEMS.length){
    renderReelSlides();
    scrollToReel(REEL_IDX,false);
  }
}

// ═══════════════════════════════════════════════
//  HELPERS
// ═══════════════════════════════════════════════
function getTierIdx(pts){for(let i=TIERS.length-1;i>=0;i--)if(pts>=(TIERS[i].thresh))return i;return -1;}
function chipHtml(pts, firstIds){
  const i=getTierIdx(pts); if(i<0)return '';
  const t=TIERS[i];
  const isFirst = firstIds && firstIds[t.id] && ME && firstIds[t.id]===ME.id;
  return `<span class="chip ${t.chipCls}">${t.gem} ${t.tag}</span>${isFirst?'<span class="chip c1">⭐ 1st</span>':''}`;
}
function rndBg(s){const c=['#7c3aed','#0891b2','#059669','#d97706','#dc2626','#4f46e5'];let h=0;for(const x of s)h=(h*31+x.charCodeAt(0))%c.length;return c[h];}
function toast(msg,ms=2200){const el=document.getElementById('toast');el.textContent=msg;el.classList.add('on');setTimeout(()=>el.classList.remove('on'),ms);}
function ptPop(label,y){const el=document.getElementById('ptpop');el.textContent=label;el.style.top=(y||300)+'px';el.className='ptpop';void el.offsetWidth;el.classList.add('pop');setTimeout(()=>el.className='ptpop',1600);}
function closeSheets(){
  document.getElementById('overlay').classList.remove('on');
  document.getElementById('csheet').classList.remove('on');
  document.getElementById('ssheet').classList.remove('on');
  document.getElementById('userSearchSheet').classList.remove('on');
  document.getElementById('publicProfileSheet').classList.remove('on');
}
function togglePassword(inputId,btnId){
  const input=document.getElementById(inputId);const btn=document.getElementById(btnId);
  if(!input||!btn)return;
  const reveal=input.type==='password';input.type=reveal?'text':'password';
  btn.textContent=reveal?'🙈':'👁️';btn.setAttribute('aria-label',reveal?'Hide password':'Show password');
}
async function readJson(res){
  const type=(res.headers.get('content-type')||'').toLowerCase();
  if(!type.includes('application/json'))return {};
  return await res.json().catch(()=>({}));
}
function readErrorMessage(data,fallback){
  if(data&&typeof data==='object'&&data.errors&&typeof data.errors==='object'){
    for(const key of Object.keys(data.errors)){const row=data.errors[key];if(Array.isArray(row)&&row.length&&row[0])return row[0];}
  }
  return data?.message||fallback;
}
function csrfHeaderMap(base){
  const headers=Object.assign({},base||{});
  if(!headers['X-CSRF-TOKEN']&&CSRF_TOKEN)headers['X-CSRF-TOKEN']=CSRF_TOKEN;
  return headers;
}
async function refreshCsrfToken(){
  try{
    const res=await fetch(window.location.href,{headers:{'Accept':'text/html'},credentials:'same-origin',cache:'no-store'});
    const html=await res.text();
    const match=html.match(/<meta\s+name=["']csrf-token["']\s+content=["']([^"']+)["']/i);
    if(match&&match[1]){
      CSRF_TOKEN=match[1];
      const meta=document.querySelector('meta[name="csrf-token"]');
      if(meta)meta.setAttribute('content',CSRF_TOKEN);
      return true;
    }
  }catch(e){}
  return false;
}
async function apiFetch(url,options,retryOnCsrf=true){
  const req=Object.assign({},options||{});
  req.credentials='same-origin';
  req.headers=csrfHeaderMap(req.headers||{});
  let res=await fetch(url,req);
  if(res.status===419&&retryOnCsrf){
    const refreshed=await refreshCsrfToken();
    if(refreshed){
      req.headers=csrfHeaderMap(req.headers||{});
      res=await fetch(url,req);
    }
  }
  return res;
}
function setMeFromUser(user,fallbackName='player'){
  const name=user?.name||fallbackName;
  const role=user?.role||(user?.is_admin?'admin':'user');
  const isAdmin=!!user?.is_admin;
  const shortsRole=(user?.shorts_role||'viewer').toLowerCase();
  const rubyPoints=Number(user?.ruby_points||0);
  ME={id:user?.id||null,name,role,is_admin:isAdmin,shorts_role:shortsRole,ruby_points:rubyPoints};
}
function canModerateUI(){
  const role=(ME?.role||'').toLowerCase();
  const shortsRole=(ME?.shorts_role||'').toLowerCase();
  return !!ME?.is_admin||role==='admin'||shortsRole==='admin';
}
function escapeHtml(value){
  return String(value??'').replace(/[&<>"']/g,function(ch){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch];
  });
}
function feedEmbedUrl(item){
  if(item.video_type==='yt')return `https://www.youtube-nocookie.com/embed/${item.embed_id}?enablejsapi=1&controls=0&modestbranding=1&rel=0&playsinline=1&autoplay=1&mute=1&origin=${encodeURIComponent(window.location.origin)}`;
  if(item.video_type==='drive')return `https://drive.google.com/file/d/${item.embed_id}/preview`;
  return '';
}
function feedThumb(item){
  if(item.video_type==='yt')return `https://i.ytimg.com/vi/${item.embed_id}/hqdefault.jpg`;
  if(item.video_type==='drive')return 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?auto=format&fit=crop&w=1200&q=60';
  return '';
}
function feedLabel(item){
  if(item.video_type==='yt')return 'YouTube';
  if(item.video_type==='drive')return 'Google Drive';
  return 'Video';
}
function statusBadge(status){
  const clean=(status||'pending').toLowerCase();
  if(clean==='approved')return {cls:'psa',text:'APPROVED',sub:'✅ Live on feed'};
  if(clean==='rejected')return {cls:'psr',text:'REJECTED',sub:'❌ Not approved'};
  return {cls:'psp',text:'PENDING',sub:'⏳ Awaiting admin review'};
}

// ═══════════════════════════════════════════════
//  DATA LOADING
// ═══════════════════════════════════════════════
async function loadFeed(){
  try{
    const res=await fetch('/shortsplay/data/feed',{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(!res.ok){document.getElementById('feedScroll').innerHTML='<div style="text-align:center;padding:40px 20px;color:var(--t2);">Failed to load feed</div>';return;}
    FEED_ITEMS=Array.isArray(data?.data)?data.data:[];
    VIEW_TRACKED={};
    renderFeed();
  }catch(e){
    document.getElementById('feedScroll').innerHTML='<div style="text-align:center;padding:40px 20px;color:var(--t2);">Network error</div>';
  }
}
async function loadMySubmissions(){
  try{
    const res=await fetch('/shortsplay/data/my-videos',{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(!res.ok)return;
    PENDING=Array.isArray(data?.data)?data.data:[];
    renderPending();
  }catch(e){}
}
async function loadAdminQueue(){
  if(!canModerateUI())return;
  try{
    const res=await fetch(`/shortsplay/data/pending?status=${encodeURIComponent(ADMIN_FILTER)}`,{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    ADMIN_Q=res.ok&&Array.isArray(data?.data)?data.data:[];
    if(res.ok&&data?.counts){
      ADMIN_COUNTS={
        pending:Number(data.counts.pending||0),
        approved:Number(data.counts.approved||0),
        rejected:Number(data.counts.rejected||0),
      };
    }
    renderAdmin();
  }catch(e){ADMIN_Q=[];renderAdmin();}
}
async function loadRubyStats(){
  try{
    const res=await fetch('/shortsplay/data/ruby/stats',{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(res.ok&&data?.data)MY_RUBY_STATS=data.data;
    if(MY_RUBY_STATS&&ME)ME.ruby_points=MY_RUBY_STATS.ruby_points;
    renderRubyScreen();
    renderProfile();
  }catch(e){}
}
async function loadLeaderboard(){
  try{
    const res=await fetch('/shortsplay/data/ruby/leaderboard',{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(res.ok&&Array.isArray(data?.data))LB_DATA=data.data;
    renderLeaderboard();
  }catch(e){}
}
async function loadTiers(){
  try{
    const res=await fetch('/shortsplay/data/ruby/tiers',{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(res.ok&&Array.isArray(data?.data))RUBY_TIERS_DATA=data.data;
    renderTierList();
  }catch(e){}
}

// ═══════════════════════════════════════════════
//  LOGIN
// ═══════════════════════════════════════════════
function setLTab(t,btn){
  document.querySelectorAll('.ltab').forEach(b=>b.classList.remove('on'));btn.classList.add('on');
  document.getElementById('lf-in').style.display=t==='in'?'flex':'none';
  document.getElementById('lf-up').style.display=t==='up'?'flex':'none';
  if(t==='up'){
    // Reset register flow to step 1
    document.getElementById('lu-step1').style.display='flex';
    document.getElementById('lu-step2').style.display='none';
    const btn2=document.getElementById('lu-submit-btn');
    if(btn2){btn2.disabled=false;btn2.textContent='Create Account';}
  }
}
async function doLogin(){
  const loginId=document.getElementById('li-email').value.trim();
  const password=document.getElementById('li-password').value;
  if(!loginId||!password){toast('⚠️ Enter email/mobile and password');return;}
  try{
    const res=await apiFetch('/shortsplay/auth/login',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({login_id:loginId,email:loginId,password})});
    const data=await readJson(res);
    if(!res.ok){toast(`⚠️ ${readErrorMessage(data,'Login failed')}`);return;}
    if(data?.already_authenticated&&data?.user){setMeFromUser(data.user,loginId.split('@')[0]||'player');startApp();return;}
    if(!data?.user){toast('⚠️ Login failed. Please try again.');return;}
    setMeFromUser(data.user,loginId.split('@')[0]||'player');startApp();
  }catch(e){toast('⚠️ Network error while signing in');}
}
async function doRegister(){
  const username=document.getElementById('lu-username').value.trim();
  const email=document.getElementById('lu-email').value.trim();
  const mobile=document.getElementById('lu-mobile').value.trim();
  const password=document.getElementById('lu-password').value;
  if(!username||!mobile||!password){toast('⚠️ Fill all required fields');return;}
  if(!/^\d{10}$/.test(mobile)){toast('⚠️ Enter valid 10-digit mobile number');return;}
  const btn=document.getElementById('lu-submit-btn');
  btn.disabled=true;btn.textContent='Sending OTP…';
  try{
    const body={username,email,mobile_number:mobile,password};
    const res=await apiFetch('/shortsplay/auth/register',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(body)});
    const data=await readJson(res);
    if(!res.ok){toast(`⚠️ ${readErrorMessage(data,'Registration failed')}`);btn.disabled=false;btn.textContent='Create Account';return;}
    if(data?.already_authenticated&&data?.user){setMeFromUser(data.user,username.replace(/^@+/,''));toast('Already signed in.');setTimeout(startApp,400);return;}
    if(data?.requires_otp){
      // Show OTP step
      document.getElementById('lu-step1').style.display='none';
      const step2=document.getElementById('lu-step2');
      step2.style.display='flex';
      const otpRecipient=email||mobile;
      document.getElementById('lu-otp-msg').textContent=`OTP sent to ${otpRecipient}. Enter the 6-digit code below. Valid for 10 minutes.`;
      document.getElementById('lu-otp').value='';
      setTimeout(()=>document.getElementById('lu-otp').focus(),100);
      return;
    }
    if(!data?.user){toast('⚠️ Registration failed.');btn.disabled=false;btn.textContent='Create Account';return;}
    setMeFromUser(data.user,username.replace(/^@+/,''));toast('🎉 Welcome to ShortsPlay!');setTimeout(startApp,500);
  }catch(e){toast('⚠️ Network error while registering');btn.disabled=false;btn.textContent='Create Account';}
}
async function doVerifyOtp(){
  const otpCode=document.getElementById('lu-otp').value.trim();
  if(!otpCode||otpCode.length!==6){toast('⚠️ Enter the 6-digit OTP');return;}
  const verifyBtn=document.querySelector('#lu-step2 .btnmain');
  verifyBtn.disabled=true;verifyBtn.textContent='Verifying…';
  try{
    const res=await apiFetch('/shortsplay/auth/register',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({otp_code:otpCode})});
    const data=await readJson(res);
    if(!res.ok){toast(`⚠️ ${readErrorMessage(data,'Verification failed')}`);verifyBtn.disabled=false;verifyBtn.textContent='Verify & Complete Registration';return;}
    if(!data?.user){toast('⚠️ Registration failed.');verifyBtn.disabled=false;verifyBtn.textContent='Verify & Complete Registration';return;}
    const username=document.getElementById('lu-username').value.trim();
    setMeFromUser(data.user,username.replace(/^@+/,'')||'player');toast('🎉 Welcome to ShortsPlay!');setTimeout(startApp,500);
  }catch(e){toast('⚠️ Network error');verifyBtn.disabled=false;verifyBtn.textContent='Verify & Complete Registration';}
}
function doBackToRegister(){
  document.getElementById('lu-step2').style.display='none';
  document.getElementById('lu-step1').style.display='flex';
  document.getElementById('lu-otp').value='';
  const btn=document.getElementById('lu-submit-btn');
  btn.disabled=false;btn.textContent='Create Account';
}
async function doLogout(){
  try{await apiFetch('/shortsplay/auth/logout',{method:'POST',headers:{'Accept':'application/json'}});}catch(e){}
  ME=null;FEED_ITEMS=[];PENDING=[];ADMIN_Q=[];MY_RUBY_STATS=null;LB_DATA=[];
  document.getElementById('pg-app').classList.remove('show');
  document.getElementById('pg-login').classList.add('show');
}
function startApp(){
  document.getElementById('pg-login').classList.remove('show');
  document.getElementById('pg-app').classList.add('show');
  document.getElementById('bn-upload').style.display='flex';
  document.getElementById('bn-admin').style.display=canModerateUI()?'flex':'none';
  renderFeed();renderProfile();renderPending();renderAdmin();
  loadFeed();loadMySubmissions();loadAdminQueue();loadRubyStats();loadLeaderboard();loadTiers();
  processReferralVisit();
}

// ═══════════════════════════════════════════════
//  NAVIGATION
// ═══════════════════════════════════════════════
function switchTab(name,btn){
  document.getElementById('pg-reels').classList.remove('show');
  document.querySelectorAll('.tab').forEach(t=>t.classList.remove('on'));
  document.querySelectorAll('.bni').forEach(b=>b.classList.remove('on'));
  document.getElementById('tab-'+name).classList.add('on');
  if(btn)btn.classList.add('on');
  if(name==='profile'){loadRubyStats();renderProfile();}
  if(name==='ruby'){renderRubyScreen();loadLeaderboard();loadTiers();}
  if(name==='admin'){renderAdmin();loadAdminQueue();}
  if(name==='upload'){renderPending();loadMySubmissions();}
  if(name==='feed'){loadFeed();}
}
function setFeedMode(mode,btn){
  document.querySelectorAll('.fmt-btn').forEach(b=>b.classList.remove('on'));
  btn.classList.add('on');
  if(mode==='reels')openReels(0);
}

// ═══════════════════════════════════════════════
//  FEED RENDER
// ═══════════════════════════════════════════════
function renderFeed(){
  const pts=MY_RUBY_STATS?.ruby_points??ME?.ruby_points??0;
  const ti=getTierIdx(pts);
  document.getElementById('myHdrBadge').innerHTML=
    (ti>=0?`<span class="chip ${TIERS[ti].chipCls}">${TIERS[ti].gem}</span>`:'')
    +`<span style="font-family:var(--fd);font-size:11px;font-weight:700;color:var(--ruby);">${Number(pts).toLocaleString()} pts</span>`;

  if(!FEED_ITEMS.length){
    document.getElementById('feedScroll').innerHTML='<div style="text-align:center;padding:40px 20px;color:var(--t2);">No approved shorts yet</div>';
    return;
  }
  document.getElementById('feedScroll').innerHTML=FEED_ITEMS.map(function(v,idx){
    const title=escapeHtml(v.title||'Untitled short');
    const description=escapeHtml(v.description||'');
    const creatorName=escapeHtml(v.creator?.username||v.creator?.name||'@creator');
    const creatorId=v.creator?.id||0;
    const initials=(v.creator?.name||'CR').replace('@','').slice(0,2).toUpperCase();
    const points=Number(v.creator?.ruby_points||0).toLocaleString();
    const likes=Number(v.likes||0);
    const comments=Number(v.comments||0);
    const shares=Number(v.shares||0);
    const label=escapeHtml(feedLabel(v));
    const thumb=feedThumb(v);
    const embed=feedEmbedUrl(v);
    const isl=!!v.is_liked;
    const iss=!!v.is_subscribed;
    return `<div class="vcard" id="vcard-${v.id}">
      <div class="vthumb" style="background:#070711;" onclick="openReels(${idx})">
        ${thumb?`<img src="${thumb}" alt="${title}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.85;" loading="lazy"/>`:''}
        <div class="vthumb-fade"></div>
        <div class="vplay">▶</div>
        <span class="vdur">${label}</span>
        <span class="reel-open-hint">▶ Tap to play</span>
      </div>
      <div class="vmeta">
        <div class="vcrow">
          <div class="vav" style="background:${rndBg(creatorName)};color:#fff;cursor:pointer;" onclick="openCreatorProfile(${creatorId})">${escapeHtml(initials)}</div>
          <div class="vnwrap">
            <div class="vname"><span style="cursor:pointer;" onclick="openCreatorProfile(${creatorId})">${creatorName}</span> <button onclick="subscribeCreator(${creatorId},this,${v.id})" style="padding:3px 9px;border-radius:12px;border:1px solid ${iss?'var(--b2)':'var(--ruby)'};background:${iss?'var(--bg3)':'transparent'};color:${iss?'var(--t2)':'var(--ruby)'};font-family:var(--fd);font-size:9px;font-weight:700;cursor:pointer;" data-creator-id="${creatorId}" data-subscribed="${iss?1:0}">${iss?'Following':'Follow'}</button></div>
            <div class="vpts">${points} ruby pts</div>
          </div>
        </div>
        <div class="vtitle">${title}</div>
        ${description?`<div style="font-size:12px;color:var(--t2);line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${description}</div>`:''}
      </div>
      <div class="vacts">
        <button class="vact${isl?' liked':''}" type="button" id="like-btn-${v.id}" onclick="likeVideo(${v.id},this)"><span class="vact-c" id="like-cnt-${v.id}">${isl?'❤️':'🤍'} ${likes}</span></button>
        <button class="vact" type="button" onclick="openComments(${v.id})"><span class="vact-c" id="cmt-cnt-${v.id}">💬 ${comments}</span></button>
        <button class="vact" type="button" onclick="openShare(${v.id})"><span class="vact-c" id="shr-cnt-${v.id}">↗ ${shares}</span></button>
        <button class="vact" type="button" onclick="openReels(${idx})"><span class="vact-c">▶ Play</span></button>
      </div>
    </div>`;
  }).join('');
}

// ═══════════════════════════════════════════════
//  ENGAGEMENT – LIKE
// ═══════════════════════════════════════════════
async function likeVideo(videoId, btn){
  // Optimistic toggle
  const isCurrLiked=btn.classList.contains('liked');
  btn.classList.toggle('liked',!isCurrLiked);
  const cntEl=document.getElementById('like-cnt-'+videoId);
  if(cntEl){
    const cur=parseInt(cntEl.textContent.replace(/\D+/g,''))||0;
    cntEl.textContent=`${!isCurrLiked?'❤️':'🤍'} ${!isCurrLiked?cur+1:Math.max(0,cur-1)}`;
  }
  try{
    const res=await fetch(`/shortsplay/data/${videoId}/like`,{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN}});
    const data=await readJson(res);
    if(!res.ok){btn.classList.toggle('liked',isCurrLiked);if(cntEl){const c=parseInt(cntEl.textContent.replace(/\D+/g,''))||0;cntEl.textContent=`${isCurrLiked?'❤️':'🤍'} ${isCurrLiked?c+1:Math.max(0,c-1)}`;}toast('⚠️ Failed to like');return;}
    const liked=!!data.liked;
    const count=data.likes??0;
    btn.classList.toggle('liked',liked);
    if(cntEl)cntEl.textContent=`${liked?'❤️':'🤍'} ${count}`;
    // Sync reel like button if visible
    const rBtn=document.getElementById('rlike-btn-'+videoId);if(rBtn){rBtn.classList.toggle('liked',liked);const rc=document.getElementById('rlike-cnt-'+videoId);if(rc)rc.textContent=count;}
    if(liked&&data.viewer_points_awarded>0)ptPop(`+${data.viewer_points_awarded} pts`,200);
    if(!liked&&data.points_deducted>0)ptPop(`-${data.points_deducted} pts`,200);
    if((liked&&data.viewer_points_awarded>0)||(!liked&&data.points_deducted>0))await loadRubyStats();
  }catch(e){btn.classList.toggle('liked',isCurrLiked);}
}

// ═══════════════════════════════════════════════
//  ENGAGEMENT – COMMENT
// ═══════════════════════════════════════════════
// ═══════════════════════════════════════════════
//  COMMENTS – helpers
// ═══════════════════════════════════════════════
function renderReplyHtml(r){
  const initials=(r.user?.name||'U').replace('@','').slice(0,2).toUpperCase();
  const bg=rndBg(r.user?.name||'u');
  const likedCls=r.is_liked?'liked':'';
  const txt=escapeHtml(r.text);
  return `<div class="cmti" id="cmti-${r.id}">
    <div class="cmtav" style="background:${bg};color:#fff;">${initials}</div>
    <div class="cmtb">
      <div class="cmtu">💬 ${escapeHtml(r.user?.name||'anonymous')}</div>
      <div class="cmtt">${txt}</div>
      <div class="cmttime">🕒 ${new Date(r.created_at).toLocaleString()}</div>
      <div class="cmtactions">
        <button class="cmtact-btn ${likedCls}" onclick="likeComment(${r.id},${r.parent_id||0})" id="clike-${r.id}">
          ${r.is_liked?'❤️':'🤍'} <span id="clikes-${r.id}">${r.likes||0}</span>
        </button>
      </div>
    </div>
  </div>`;
}
function renderCommentHtml(c, videoId){
  const initials=(c.user?.name||'U').replace('@','').slice(0,2).toUpperCase();
  const bg=rndBg(c.user?.name||'u');
  const likedCls=c.is_liked?'liked':'';
  const pinnedBadge=c.pinned?'<div class="cmtpin-badge">📌 Pinned</div>':'';
  const canPin=ME&&(ME.is_admin||(FEED_ITEMS.find(v=>v.id===videoId)?.creator_id===ME.id));
  const pinBtnCls=c.pinned?'cmtact-btn pinbtn active':'cmtact-btn pinbtn';
  const pinBtn=canPin?`<button class="${pinBtnCls}" onclick="pinComment(${c.id},${videoId})" id="cpin-${c.id}">${c.pinned?'📌 Unpin':'📌 Pin'}</button>`:'';
  const repliesHtml=(c.replies&&c.replies.length)?`<div class="cmtreplies">${c.replies.map(r=>renderReplyHtml(r)).join('')}</div>`:'<div class="cmtreplies" id="replist-${c.id}"></div>';
  const txt=escapeHtml(c.text);
  return `<div class="cmti" id="cmti-${c.id}">
    <div class="cmtav" style="background:${bg};color:#fff;">${initials}</div>
    <div class="cmtb">
      ${pinnedBadge}
      <div class="cmtu">💬 ${escapeHtml(c.user?.name||'anonymous')}</div>
      <div class="cmtt">${txt}</div>
      <div class="cmttime">🕒 ${new Date(c.created_at).toLocaleString()}</div>
      <div class="cmtactions">
        <button class="cmtact-btn ${likedCls}" onclick="likeComment(${c.id},0)" id="clike-${c.id}">
          ${c.is_liked?'❤️':'🤍'} <span id="clikes-${c.id}">${c.likes||0}</span>
        </button>
        <button class="cmtact-btn" onclick="startReply(${c.id},'${escapeHtml(c.user?.name||'user').replace('@','')}')">↩ Reply</button>
        ${pinBtn}
      </div>
      ${repliesHtml}
    </div>
  </div>`;
}

async function openComments(videoId){
  activeCmtId=videoId;
  cancelReply();
  document.getElementById('cmtList').innerHTML='<div style="padding:20px;text-align:center;color:var(--t2);">Loading…</div>';
  document.getElementById('csheetCnt').textContent='';
  document.getElementById('overlay').classList.add('on');
  document.getElementById('csheet').classList.add('on');
  document.getElementById('cmtInput').value='';
  document.getElementById('cmtInput').dataset.videoId=videoId;
  try{
    const res=await fetch(`/shortsplay/data/${videoId}/comments`,{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(!res.ok){document.getElementById('cmtList').innerHTML='<div style="padding:20px;text-align:center;color:var(--t2);">Failed to load</div>';return;}
    const cmts=Array.isArray(data?.data)?data.data:[];
    document.getElementById('csheetCnt').textContent=`(${cmts.length})`;
    document.getElementById('cmtList').innerHTML=cmts.length
      ?cmts.map(c=>renderCommentHtml(c,videoId)).join('')
      :'<div style="padding:30px;text-align:center;color:var(--t2);">No comments yet. Be the first!</div>';
  }catch(e){document.getElementById('cmtList').innerHTML='<div style="padding:20px;text-align:center;color:var(--t2);">Network error</div>';}
}
async function postComment(){
  const input=document.getElementById('cmtInput');
  const text=input.value.trim();
  const videoId=activeCmtId||Number(input.dataset.videoId)||0;
  if(!text||!videoId){toast('⚠️ Write something first');return;}
  const parentId=replyingTo?replyingTo.commentId:null;
  const payload={text};
  if(parentId)payload.parent_id=parentId;
  try{
    const res=await apiFetch(`/shortsplay/data/${videoId}/comment`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(payload)});
    const data=await readJson(res);
    if(!res.ok){toast(`⚠️ ${readErrorMessage(data,'Failed')}`);return;}
    input.value='';
    cancelReply();
    const c=data.comment;
    if(parentId){
      // Insert reply under its parent
      let container=document.getElementById('replist-'+parentId);
      if(!container){
        // replies list may have been rendered with id inline; try to find the replies div
        const parentEl=document.getElementById('cmti-'+parentId);
        if(parentEl){
          container=parentEl.querySelector('.cmtreplies');
        }
      }
      if(container){
        // Remove empty-state placeholder if present
        if(container.innerHTML.includes('No replies'))container.innerHTML='';
        container.insertAdjacentHTML('beforeend',renderReplyHtml(c));
        container.scrollIntoView({behavior:'smooth',block:'nearest'});
      }
    } else {
      const prev=document.getElementById('cmtList').innerHTML.includes('No comments')?'':document.getElementById('cmtList').innerHTML;
      document.getElementById('cmtList').innerHTML=renderCommentHtml(c,videoId)+prev;
      // Update count
      const cnt=document.getElementById('cmt-cnt-'+videoId);if(cnt){const n=parseInt(cnt.textContent.replace(/\D+/g,''))||0;cnt.textContent=`💬 ${n+1}`;}
      const rcnt=document.getElementById('rcmt-cnt-'+videoId);if(rcnt)rcnt.textContent=Number(rcnt.textContent)+1;
      const cshCnt=document.getElementById('csheetCnt');if(cshCnt){const n=parseInt((cshCnt.textContent||'').replace(/\D+/g,''))||0;cshCnt.textContent=`(${n+1})`;}
      if(data.points_awarded>0)ptPop(`+${data.points_awarded} pts`,200);
      if(data.viewer_points_awarded>0)ptPop(`+${data.viewer_points_awarded} pts`,200);
      await loadRubyStats();
    }
  }catch(e){toast('⚠️ Network error');}
}
function startReply(commentId, username){
  replyingTo={commentId, username};
  const tag=document.getElementById('cmtReplyingTag');
  const nameEl=document.getElementById('cmtReplyingName');
  if(tag)tag.classList.add('on');
  if(nameEl)nameEl.textContent='@'+username;
  const input=document.getElementById('cmtInput');
  if(input){input.placeholder=`Reply to @${username}…`;input.focus();}
}
function cancelReply(){
  replyingTo=null;
  const tag=document.getElementById('cmtReplyingTag');
  if(tag)tag.classList.remove('on');
  const input=document.getElementById('cmtInput');
  if(input)input.placeholder='Write a comment…';
}
async function likeComment(commentId, parentId){
  try{
    const res=await apiFetch(`/shortsplay/data/comments/${commentId}/like`,{method:'POST',headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(!data.success)return;
    const btn=document.getElementById('clike-'+commentId);
    const cnt=document.getElementById('clikes-'+commentId);
    if(btn){
      if(data.liked){btn.classList.add('liked');btn.childNodes[0].textContent='❤️ ';}
      else{btn.classList.remove('liked');btn.childNodes[0].textContent='🤍 ';}
    }
    if(cnt)cnt.textContent=data.likes;
  }catch(e){}
}
async function pinComment(commentId, videoId){
  try{
    const res=await apiFetch(`/shortsplay/data/comments/${commentId}/pin`,{method:'POST',headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(!data.success)return;
    // Refresh the comment list to reflect new pin state
    await openComments(videoId);
  }catch(e){}
}

// ═══════════════════════════════════════════════
//  ENGAGEMENT – SHARE
// ═══════════════════════════════════════════════
function openShare(videoId){
  activeShareId=videoId;
  document.getElementById('overlay').classList.add('on');
  document.getElementById('ssheet').classList.add('on');
}
async function doShare(platform){
  const videoId=activeShareId;
  closeSheets();
  activeShareId=null;
  if(!videoId)return;

  const item=FEED_ITEMS.find(v=>v.id===videoId);
  const shareUrl=`${window.location.origin}/shortsplay?v=${videoId}&ref=${ME?.id||0}`;
  const shareText=encodeURIComponent(`Watch this on ShortsPlay: ${item?.title||'Check this out'}`);
  const encodedUrl=encodeURIComponent(shareUrl);

  if(platform==='WhatsApp')window.open(`https://wa.me/?text=${shareText}%20${encodedUrl}`,'_blank');
  else if(platform==='Twitter')window.open(`https://twitter.com/intent/tweet?text=${shareText}&url=${encodedUrl}`,'_blank');
  else if(platform==='Facebook')window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,'_blank');
  else if(platform==='Telegram')window.open(`https://t.me/share/url?url=${encodedUrl}&text=${shareText}`,'_blank');
  else if(platform==='Threads')window.open(`https://www.threads.net/`,'_blank');
  else if(platform==='Copy'){
    try{await navigator.clipboard.writeText(shareUrl);toast('🔗 Referral link copied');}
    catch(e){toast('📋 Copy: '+shareUrl);}
    return;
  }
  else if(platform==='More'){
    if(navigator.share){
      try{await navigator.share({title:item?.title||'ShortsPlay',url:shareUrl});}
      catch(e){}
    }
    return;
  }

  // Only track share intent. Counts and ruby points increase only when another user opens shared link.
  try{
    await fetch(`/shortsplay/data/${videoId}/share`,{
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
      body:JSON.stringify({platform})
    });
  }catch(e){}

  toast('↗ Share sent. Points add only when another user opens your shared link.');
}

async function processReferralVisit(){
  const params=new URLSearchParams(window.location.search);
  const videoId=Number(params.get('v')||0);
  const ref=Number(params.get('ref')||0);
  if(!videoId||!ref||!ME?.id)return;

  try{
    const res=await fetch('/shortsplay/data/referral-visit',{
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
      body:JSON.stringify({video_id:videoId,ref})
    });
    const data=await readJson(res);
    if(res.ok&&data?.awarded){
      toast('🎉 Referral counted. Creator earned ruby points!');
      await loadFeed();
    }
  }catch(e){}
}

// ═══════════════════════════════════════════════
//  ENGAGEMENT – SUBSCRIBE
// ═══════════════════════════════════════════════
function applyFollowButtonState(btn, sub){
  if(!btn)return;
  btn.dataset.subscribed=sub?'1':'0';
  if(btn.classList.contains('reel-follow')){
    btn.classList.toggle('on',sub);
    btn.textContent=sub?'Following':'Follow';
    return;
  }
  if(btn.classList.contains('usrfbtn')||btn.classList.contains('pubfollow')){
    btn.classList.toggle('on',sub);
    btn.textContent=sub?'Following':'Follow';
    return;
  }
  btn.textContent=sub?'Following':'Follow';
  btn.style.borderColor=sub?'var(--b2)':'var(--ruby)';
  btn.style.color=sub?'var(--t2)':'var(--ruby)';
}
function syncCreatorFollowState(creatorId, sub){
  FEED_ITEMS=FEED_ITEMS.map(function(v){
    if(v?.creator?.id===creatorId)v.is_subscribed=!!sub;
    return v;
  });
  document.querySelectorAll(`[data-creator-id="${creatorId}"]`).forEach(function(btn){
    applyFollowButtonState(btn, !!sub);
  });
}

async function subscribeCreator(creatorId, btn, contextVideoId){
  if(!creatorId){toast('⚠️ No creator');return;}
  const wasSub=btn.dataset.subscribed==='1';
  // Optimistic
  applyFollowButtonState(btn,!wasSub);
  syncCreatorFollowState(creatorId,!wasSub);
  try{
    const res=await fetch(`/shortsplay/data/creator/${creatorId}/subscribe`,{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN}});
    const data=await readJson(res);
    if(!res.ok){applyFollowButtonState(btn,wasSub);syncCreatorFollowState(creatorId,wasSub);toast(`⚠️ ${readErrorMessage(data,'Failed')}`);return;}
    const sub=!!data.subscribed;
    applyFollowButtonState(btn,sub);
    syncCreatorFollowState(creatorId,sub);
    if(sub&&data.viewer_points_awarded>0)ptPop(`+${data.viewer_points_awarded} pts`,200);
    toast(sub?'✅ Subscribed':'Unfollowed');
    if(sub&&data.viewer_points_awarded>0)await loadRubyStats();
    if(PUBLIC_PROFILE_DATA&&PUBLIC_PROFILE_DATA.user?.id===creatorId){
      PUBLIC_PROFILE_DATA.user.is_following=sub;
      PUBLIC_PROFILE_DATA.stats.followers=Math.max(0,Number(PUBLIC_PROFILE_DATA.stats.followers||0)+(sub?1:-1));
      renderPublicProfileBody(PUBLIC_PROFILE_DATA);
    }
  }catch(e){applyFollowButtonState(btn,wasSub);syncCreatorFollowState(creatorId,wasSub);}
}
async function trackView(videoId){
  if(!videoId||VIEW_TRACKED[videoId])return;
  VIEW_TRACKED[videoId]=true;
  try{
    const res=await apiFetch(`/shortsplay/data/${videoId}/view`,{method:'POST',headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(res.ok&&data?.viewer_points_awarded>0){
      ptPop(`+${data.viewer_points_awarded} pts`,240);
      await loadRubyStats();
    }
  }catch(e){}
}

function openUserSearch(){
  document.getElementById('overlay').classList.add('on');
  document.getElementById('userSearchSheet').classList.add('on');
  document.getElementById('publicProfileSheet').classList.remove('on');
  const inp=document.getElementById('userSearchInput');
  if(inp){inp.value='';setTimeout(()=>inp.focus(),100);}
  searchUsers('');
}
function queueUserSearch(){
  const q=document.getElementById('userSearchInput')?.value||'';
  if(USER_SEARCH_TIMER)clearTimeout(USER_SEARCH_TIMER);
  USER_SEARCH_TIMER=setTimeout(function(){searchUsers(q);},250);
}
async function searchUsers(q){
  const target=document.getElementById('userSearchResults');
  if(!target)return;
  target.innerHTML='<div style="padding:18px;text-align:center;color:var(--t2);">Searching...</div>';
  try{
    const res=await fetch(`/shortsplay/data/users/search?q=${encodeURIComponent(q||'')}&limit=20`,{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(!res.ok){target.innerHTML='<div style="padding:18px;text-align:center;color:var(--t2);">Search failed</div>';return;}
    const rows=Array.isArray(data?.data)?data.data:[];
    if(!rows.length){target.innerHTML='<div style="padding:18px;text-align:center;color:var(--t2);">No users found</div>';return;}
    target.innerHTML=rows.map(function(u){
      const sub=!!u.is_following;
      const av=(u.username||'@U').replace('@','').slice(0,2).toUpperCase();
      return `<div class="usrrow" onclick="openCreatorProfile(${u.id})">
        <div class="usrav" style="background:${rndBg(u.username||'u')};">${av}</div>
        <div class="usrmeta"><div class="usrn">${escapeHtml(u.username||'@user')}</div><div class="usrs">${Number(u.followers||0).toLocaleString()} followers · ${Number(u.posts||0).toLocaleString()} posts · ${Number(u.ruby_points||0).toLocaleString()} pts</div></div>
        <button class="usrfbtn${sub?' on':''}" data-creator-id="${u.id}" data-subscribed="${sub?1:0}" onclick="event.stopPropagation();subscribeCreator(${u.id},this)">${sub?'Following':'Follow'}</button>
      </div>`;
    }).join('');
  }catch(e){target.innerHTML='<div style="padding:18px;text-align:center;color:var(--t2);">Network error</div>';}
}
function renderPublicProfileBody(data){
  const body=document.getElementById('publicProfileBody');
  if(!body)return;
  const u=data?.user||{};
  const s=data?.stats||{};
  const vids=Array.isArray(data?.videos)?data.videos:[];
  const initials=(u.username||'@U').replace('@','').slice(0,2).toUpperCase();
  const canFollow=Number(u.id)!==Number(ME?.id);
  body.innerHTML=`<div class="pubtop">
    <div class="pubav" style="background:${rndBg(u.username||'u')};">${initials}</div>
    <div style="min-width:0;"><div class="pubname">${escapeHtml(u.username||'@user')}</div><div class="pubrole">${escapeHtml((u.role||'creator').toString().toUpperCase())} · 💎 ${Number(u.ruby_points||0).toLocaleString()} pts</div></div>
    ${canFollow?`<button class="pubfollow${u.is_following?' on':''}" data-creator-id="${u.id}" data-subscribed="${u.is_following?1:0}" onclick="subscribeCreator(${u.id},this)">${u.is_following?'Following':'Follow'}</button>`:''}
  </div>
  <div class="pubstats">
    <div class="pubst"><div class="pubstn">${Number(s.posts||0).toLocaleString()}</div><div class="pubstl">Posts</div></div>
    <div class="pubst"><div class="pubstn">${Number(s.followers||0).toLocaleString()}</div><div class="pubstl">Followers</div></div>
    <div class="pubst"><div class="pubstn">${Number(s.following||0).toLocaleString()}</div><div class="pubstl">Following</div></div>
  </div>
  <div class="pubstats" style="margin-top:0;">
    <div class="pubst"><div class="pubstn">${Number(s.likes||0).toLocaleString()}</div><div class="pubstl">Likes</div></div>
    <div class="pubst"><div class="pubstn">${Number(s.comments||0).toLocaleString()}</div><div class="pubstl">Comments</div></div>
    <div class="pubst"><div class="pubstn">${Number(s.shares||0).toLocaleString()}</div><div class="pubstl">Shares</div></div>
  </div>
  <div class="pubsec">Recent Shorts</div>
  <div class="pubgrid">${vids.length?vids.map(v=>`<div class="pubvid" onclick="closeSheets();openReels(${Math.max(0,FEED_ITEMS.findIndex(x=>x.id===v.id))});"><div class="pubvidt">${escapeHtml(v.title||'Short')}</div><div class="pubvidm">❤️ ${Number(v.likes||0)} · 💬 ${Number(v.comments||0)}</div></div>`).join(''):'<div style="grid-column:1/-1;text-align:center;color:var(--t2);padding:12px;">No public shorts yet</div>'}</div>`;
}
async function openCreatorProfile(creatorId){
  if(!creatorId)return;
  document.getElementById('overlay').classList.add('on');
  document.getElementById('publicProfileSheet').classList.add('on');
  const body=document.getElementById('publicProfileBody');
  if(body)body.innerHTML='<div style="padding:24px;text-align:center;color:var(--t2);">Loading profile...</div>';
  try{
    const res=await fetch(`/shortsplay/data/creator/${creatorId}/profile`,{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(!res.ok||!data?.data){if(body)body.innerHTML='<div style="padding:24px;text-align:center;color:var(--t2);">Failed to load profile</div>';return;}
    PUBLIC_PROFILE_DATA=data.data;
    renderPublicProfileBody(PUBLIC_PROFILE_DATA);
  }catch(e){if(body)body.innerHTML='<div style="padding:24px;text-align:center;color:var(--t2);">Network error</div>';}
}

// ═══════════════════════════════════════════════
//  REELS FULLSCREEN PLAYER
// ═══════════════════════════════════════════════
function openReels(startIdx){
  if(!FEED_ITEMS.length){toast('No reels yet');return;}
  REEL_AUTO_UNMUTE=true;
  REEL_AUDIO_ON={};
  const cue=document.getElementById('reelCue'); if(cue) cue.classList.remove('show');
  REEL_IDX=Math.max(0,Math.min(startIdx||0,FEED_ITEMS.length-1));
  renderReelSlides();
  document.getElementById('pg-reels').classList.add('show');
  scrollToReel(REEL_IDX,false);
}
function closeReels(){
  document.getElementById('pg-reels').classList.remove('show');
  const cue=document.getElementById('reelCue'); if(cue) cue.classList.remove('show');
  stopNonActiveReelAudio(-1);
  // Reset reel mode button
  document.querySelector('.fmt-btn[data-mode="feed"]') && document.querySelector('.fmt-btn[data-mode="feed"]').classList.add('on');
  document.querySelectorAll('.fmt-btn').forEach(b=>{if(b.id==='fmtFeed')b.classList.add('on');else b.classList.remove('on');});
}
function scrollToReel(idx,smooth){
  const stack=document.getElementById('reelStack');
  const vh=document.getElementById('reelVp').clientHeight||window.innerHeight;
  const cue=document.getElementById('reelCue'); if(cue) cue.classList.remove('show');
  stack.style.transition=smooth?'transform 0.35s cubic-bezier(.4,0,.2,1)':'none';
  stack.style.transform=`translateY(${-idx * vh}px)`;
  updateReelDots(idx);
  updateReelInfo(idx);
}
function renderReelSlides(){
  const vp=document.getElementById('reelVp');
  const vh=vp.clientHeight||window.innerHeight;
  const stack=document.getElementById('reelStack');

  stack.innerHTML=FEED_ITEMS.map(function(v,i){
    const embed=feedEmbedUrl(v);
    const thumb=feedThumb(v);
    const description=escapeHtml(v.description||'');
    const creatorName=escapeHtml(v.creator?.username||'@creator');
    const creatorId=v.creator?.id||0;
    const title=escapeHtml(v.title||'ShortsPlay');
    const isl=!!v.is_liked;
    const iss=!!v.is_subscribed;
    let embedHtml='';
    if(embed){
      embedHtml=`<div class="embed-wrap"><iframe id="rframe-${v.id}" src="" data-src="${embed}" data-type="${v.video_type}" loading="lazy" allow="autoplay;encrypted-media;picture-in-picture" referrerpolicy="strict-origin-when-cross-origin"></iframe></div><div class="embed-tap" onclick="togglePause(${i})"></div>`;
    } else {
      embedHtml=`<div class="reel-ph"><div class="reel-ph-emoji">🎬</div><div class="reel-ph-txt">Tap to open</div></div>`;
    }
    return `<div class="reel-slide" id="rslide-${v.id}" style="height:${vh}px;">
      ${thumb?`<img src="${thumb}" alt="${title}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.55;" loading="lazy"/>`:''}
      ${embedHtml}
      <div class="reel-top">
        <div class="reel-topbrand">Shorts<em>Play</em></div>
        <button class="reel-back" onclick="closeReels()" aria-label="Back to ShortsPlay">←</button>
      </div>
      <div class="reel-info">
        <div class="reel-crow">
          <div class="reel-av" style="background:${rndBg(creatorName)};color:#fff;cursor:pointer;" onclick="closeReels();openCreatorProfile(${creatorId})">${creatorName.replace('@','').slice(0,2).toUpperCase()}</div>
          <div class="reel-cname">
            <span style="cursor:pointer;" onclick="closeReels();openCreatorProfile(${creatorId})">${creatorName}</span>
            <button class="reel-follow${iss?' on':''}" data-creator-id="${creatorId}" onclick="subscribeCreator(${creatorId},this,${v.id})" data-subscribed="${iss?1:0}">${iss?'Following':'Follow'}</button>
          </div>
        </div>
        <div class="reel-title">${title}</div>
        ${description?`<div class="reel-title" style="font-size:12px;opacity:.85;margin-top:4px;-webkit-line-clamp:3;">${description}</div>`:''}
        <div class="reel-ptschip">💎 ${Number(v.creator?.ruby_points||0).toLocaleString()} ruby pts</div>
      </div>
      <div class="reel-acts">
        <button class="ract${isl?' liked':''}" id="rlike-btn-${v.id}" onclick="likeReel(${v.id},this)">
          <div class="ract-ic">❤️</div>
          <div class="ract-c" id="rlike-cnt-${v.id}">${v.likes||0}</div>
        </button>
        <button class="ract" onclick="openReelComments(${v.id})">
          <div class="ract-ic">💬</div>
          <div class="ract-c" id="rcmt-cnt-${v.id}">${v.comments||0}</div>
        </button>
        <button class="ract" onclick="openReelShare(${v.id})">
          <div class="ract-ic">↗</div>
          <div class="ract-c" id="rshr-cnt-${v.id}">${v.shares||0}</div>
        </button>
      </div>
    </div>`;
  }).join('');

  // Dots
  const dots=document.getElementById('reelDots');
  dots.innerHTML=FEED_ITEMS.map((_,i)=>`<div class="rdot${i===REEL_IDX?' on':''}" id="dot-${i}"></div>`).join('');

  // Set up touch swipe
  setupReelSwipe();
}
function updateReelDots(idx){
  document.querySelectorAll('.rdot').forEach((d,i)=>{d.classList.toggle('on',i===idx);});
}
function updateReelInfo(idx){
  // Keep only current reel player active (prevents overlapping audio)
  FEED_ITEMS.forEach(function(v, i){
    const frame=document.getElementById('rframe-'+v.id);
    if(!frame)return;
    if(i===idx){
      trackView(v.id);
      if(!frame.src||frame.src===window.location.href){
        frame.src=frame.dataset.src;
        REEL_AUDIO_ON[v.id]=false;
        if(REEL_AUTO_UNMUTE&&v.video_type==='yt'){
          frame.onload=function(){
            setTimeout(function(){autoUnmuteReel(v, i);}, 300);
          };
        }
      } else if(REEL_AUTO_UNMUTE&&v.video_type==='yt'){
        setTimeout(function(){autoUnmuteReel(v, i);}, 120);
      }
    } else {
      if(frame.src&&frame.src!==window.location.href){
        frame.contentWindow?.postMessage('{"event":"command","func":"mute","args":""}','*');
        frame.contentWindow?.postMessage('{"event":"command","func":"pauseVideo","args":""}','*');
        frame.src='';
      }
      REEL_AUDIO_ON[v.id]=false;
    }
  });
}
function stopNonActiveReelAudio(activeIdx){
  FEED_ITEMS.forEach(function(v, i){
    if(v.video_type!=='yt' || i===activeIdx)return;
    const frame=document.getElementById('rframe-'+v.id);
    if(!frame||!frame.src)return;
    REEL_AUDIO_ON[v.id]=false;
    frame.contentWindow?.postMessage('{"event":"command","func":"mute","args":""}','*');
    frame.contentWindow?.postMessage('{"event":"command","func":"pauseVideo","args":""}','*');
    frame.src='';
  });
}
function autoUnmuteReel(video, idx){
  if(!video||video.video_type!=='yt')return;
  const frame=document.getElementById('rframe-'+video.id);
  if(!frame||!frame.src)return;
  REEL_AUDIO_ON[video.id]=true;
  const cue=document.getElementById('reelCue');
  if(cue){
    cue.textContent='🔊';
    cue.classList.add('show');
    setTimeout(function(){ if(cue.textContent==='🔊')cue.classList.remove('show'); },900);
  }
  frame.contentWindow?.postMessage('{"event":"command","func":"unMute","args":""}','*');
  frame.contentWindow?.postMessage('{"event":"command","func":"setVolume","args":[100]}','*');
  frame.contentWindow?.postMessage('{"event":"command","func":"playVideo","args":""}','*');
}
let _paused=false;
function togglePause(idx){
  const cue=document.getElementById('reelCue');if(!cue)return;
  const v=FEED_ITEMS[idx];if(!v)return;
  const frame=document.getElementById('rframe-'+v.id);
  if(!frame||!frame.src)return;
  if(v.video_type==='yt'&&!REEL_AUDIO_ON[v.id]){
    REEL_AUDIO_ON[v.id]=true;
    _paused=false;
    cue.textContent='🔊';
    cue.classList.add('show');
    frame.contentWindow?.postMessage('{"event":"command","func":"unMute","args":""}','*');
    frame.contentWindow?.postMessage('{"event":"command","func":"setVolume","args":[100]}','*');
    frame.contentWindow?.postMessage('{"event":"command","func":"playVideo","args":""}','*');
    setTimeout(function(){
      if(cue.textContent==='🔊')cue.classList.remove('show');
    },900);
    return;
  }
  _paused=!_paused;
  cue.textContent='⏸';
  cue.classList.toggle('show',_paused);
  if(v.video_type==='yt'){
    if(_paused){frame.contentWindow?.postMessage('{"event":"command","func":"pauseVideo","args":""}','*');}
    else{frame.contentWindow?.postMessage('{"event":"command","func":"playVideo","args":""}','*');}
  }
}
function setupReelSwipe(){
  const vp=document.getElementById('reelVp');
  if(vp._swipeSetup)return;
  vp._swipeSetup=true;
  let startY=0,lastY=0,startX=0,lastX=0,dragging=false;
  vp.addEventListener('touchstart',e=>{
    startY=e.touches[0].clientY;lastY=startY;
    startX=e.touches[0].clientX;lastX=startX;
    dragging=true;
  },{ passive:true });
  vp.addEventListener('touchmove',e=>{
    if(!dragging)return;
    lastY=e.touches[0].clientY;
    lastX=e.touches[0].clientX;
  },{ passive:true });
  vp.addEventListener('touchend',e=>{
    if(!dragging)return;dragging=false;
    const dx=lastX-startX;
    const dy=startY-lastY;
    // Right swipe closes reels and returns to main page.
    if(dx>70 && Math.abs(dx)>Math.abs(dy)+20){
      closeReels();
      return;
    }
    if(Math.abs(dy)>50){
      if(dy>0&&REEL_IDX<FEED_ITEMS.length-1){REEL_IDX++;scrollToReel(REEL_IDX,true);}
      else if(dy<0&&REEL_IDX>0){REEL_IDX--;scrollToReel(REEL_IDX,true);}
    }
  },{ passive:true });
  // Mouse wheel
  vp.addEventListener('wheel',e=>{
    if(e.deltaY>30&&REEL_IDX<FEED_ITEMS.length-1){REEL_IDX++;scrollToReel(REEL_IDX,true);}
    else if(e.deltaY<-30&&REEL_IDX>0){REEL_IDX--;scrollToReel(REEL_IDX,true);}
  },{passive:true});
}
// Reels-context engagement
async function likeReel(videoId, btn){
  // delegate to same likeVideo function
  const fBtn=document.getElementById('like-btn-'+videoId);
  await likeVideo(videoId, fBtn||btn);
  // Sync reel button state
  const rBtn=document.getElementById('rlike-btn-'+videoId);
  if(rBtn){const v=FEED_ITEMS.find(x=>x.id===videoId);if(v)rBtn.classList.toggle('liked',!!v.is_liked);}
}
function openReelComments(videoId){closeReels();openComments(videoId);}
function openReelShare(videoId){activeShareId=videoId;document.getElementById('overlay').classList.add('on');document.getElementById('ssheet').classList.add('on');}

// ═══════════════════════════════════════════════
//  UPLOAD
// ═══════════════════════════════════════════════
function inferVideoType(url){
  const value=(url||'').toLowerCase();
  if(value.includes('youtube.com/shorts/')||value.includes('youtu.be/')||value.includes('youtube.com/watch'))return 'yt';
  if(value.includes('drive.google.com/file/d/')||value.includes('drive.google.com/open?id=')||value.includes('drive.google.com/uc?id='))return 'drive';
  return null;
}
function addDescEmoji(emoji){
  const descInput=document.getElementById('descInput');
  if(!descInput)return;
  const start=descInput.selectionStart ?? descInput.value.length;
  const end=descInput.selectionEnd ?? descInput.value.length;
  const before=descInput.value.slice(0,start);
  const after=descInput.value.slice(end);
  descInput.value=before+emoji+after;
  const pos=start+emoji.length;
  descInput.focus();
  descInput.setSelectionRange(pos,pos);
}
async function submitVideo(){
  const input=document.getElementById('urlInput');
  const descInput=document.getElementById('descInput');
  const sourceUrl=input.value.trim();
  const description=(descInput?.value||'').trim();
  if(!sourceUrl){toast('⚠️ Paste a link first');return;}
  const videoType=inferVideoType(sourceUrl);
  if(!videoType){toast('⚠️ Only YouTube Shorts or Google Drive video links are allowed');return;}
  const title=videoType==='yt'?'YouTube Short Submission':'Google Drive Video Submission';
  try{
    const res=await apiFetch('/shortsplay/data/upload',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({source_url:sourceUrl,video_type:videoType,title,description})});
    const data=await readJson(res);
    if(!res.ok){toast(`⚠️ ${readErrorMessage(data,'Submission failed')}`);return;}
    input.value='';
    if(descInput)descInput.value='';
    toast('✅ Submitted for admin review.');
    await loadMySubmissions();await loadFeed();
  }catch(e){toast('⚠️ Network error while submitting');}
}
function renderPending(){
  if(!PENDING.length){document.getElementById('pendingList').innerHTML='<div style="text-align:center;padding:24px;color:var(--t2);font-size:13px;">No submissions yet</div>';return;}
  document.getElementById('pendingList').innerHTML=PENDING.map(v=>{
    const badge=statusBadge(v.status);
    const title=v.title||'Untitled submission';
    const created=v.created_at?new Date(v.created_at).toLocaleString():'Recently';
    return `<div class="pitem"><div class="pinfo"><div class="ptitle">${title}</div><div class="psub">${badge.sub} • ${created}</div></div><span class="pstat ${badge.cls}">${badge.text}</span></div>`;
  }).join('');
}

// ═══════════════════════════════════════════════
//  RUBY SCREEN
// ═══════════════════════════════════════════════
function renderRubyScreen(){
  document.getElementById('ptsGrid').innerHTML=[
    {ic:'❤️',l:'Like received',pts:PTS.like},
    {ic:'💬',l:'Comment received',pts:PTS.comment},
    {ic:'↗',l:'Share received',pts:PTS.share},
    {ic:'⭐',l:'New subscriber',pts:PTS.subscribe},
  ].map(a=>`<div class="ptsc"><div class="ptsc-ic">${a.ic}</div><div class="ptsc-l">${a.l}</div><div class="ptsc-v">+${a.pts} pts</div></div>`).join('');

  document.getElementById('viewerPtsGrid').innerHTML=[
    {ic:'👀',l:'Unique video view',pts:VIEWER_PTS.view},
    {ic:'❤️',l:'First like on video',pts:VIEWER_PTS.like},
    {ic:'💬',l:'First top comment',pts:VIEWER_PTS.comment},
    {ic:'⭐',l:'First follow creator',pts:VIEWER_PTS.follow},
  ].map(a=>`<div class="ptsc"><div class="ptsc-ic">${a.ic}</div><div class="ptsc-l">${a.l}</div><div class="ptsc-v">+${a.pts} pts</div></div>`).join('');

  renderTierList();
  renderLeaderboard();
}
function renderTierList(){
  const pts=MY_RUBY_STATS?.ruby_points??ME?.ruby_points??0;
  const tierData=RUBY_TIERS_DATA.length?RUBY_TIERS_DATA:TIERS.map(t=>({name:t.id,display_name:t.name,tag:t.tag,emoji:t.gem,adjusted_threshold:t.thresh,first_achiever:null}));
  document.getElementById('tierList').innerHTML=tierData.map((t,i)=>{
    const thresh=t.adjusted_threshold||t.base_threshold||TIERS[i]?.thresh||0;
    const achieved=pts>=thresh;
    const pct=Math.min(100,Math.round((pts/thresh)*100));
    const firstAch=t.first_achiever;
    return `<div class="tierc${achieved?' ach':''}">
      <span class="tgem">${t.emoji}</span>
      <div class="tbody">
        <div class="tname">${t.display_name}${firstAch?` <span class="chip c1">⭐ 1st: ${escapeHtml(firstAch.name)}</span>`:''}${achieved?` <span class="chip" style="background:rgba(62,207,142,.1);color:var(--green);border:1px solid rgba(62,207,142,.25);">✅</span>`:''}</div>
        <div class="treq">${Number(thresh).toLocaleString()} pts${achieved?' – Achieved!':` – ${Math.max(0,thresh-pts).toLocaleString()} more needed`}</div>
        <div class="tbar"><div class="tfill" style="width:${pct}%"></div></div>
      </div>
      <span class="${achieved?'tba':'tbl'}">${achieved?'Achieved':'Locked'}</span>
    </div>`;
  }).join('');
}
function renderLeaderboard(){
  if(!LB_DATA.length){document.getElementById('lbList').innerHTML='<div style="text-align:center;color:var(--t2);padding:20px;">Loading leaderboard…</div>';return;}
  document.getElementById('lbList').innerHTML=LB_DATA.map((u,i)=>{
    const isMe=u.id===ME?.id;
    const tier=u.tier;
    return `<div class="lbi${isMe?' me':''}">
      <div class="lbrank${i<3?' top':''}">${i+1}</div>
      <div class="lbav" style="background:${rndBg(u.name||'')};">${(u.name||'?').replace('@','').slice(0,2).toUpperCase()}</div>
      <div class="lbinfo"><div class="lbn">${escapeHtml(u.name||'anonymous')}${isMe?' (You)':''}</div><div class="lbp">${Number(u.ruby_points||0).toLocaleString()} pts${tier?` · ${tier.emoji||''} ${tier.display_name||''}`:''}  </div></div>
    </div>`;
  }).join('');
}

// ═══════════════════════════════════════════════
//  PROFILE
// ═══════════════════════════════════════════════
function renderProfile(){
  if(!ME)return;
  const pts=MY_RUBY_STATS?.ruby_points??ME?.ruby_points??0;
  const stats=MY_RUBY_STATS?.stats;
  const progress=MY_RUBY_STATS?.progress_to_next;
  const achieved=Array.isArray(MY_RUBY_STATS?.achieved_tiers)?MY_RUBY_STATS.achieved_tiers:[];
  const cols=['#f87171','#f5a623','#60d0f0','#3ecf8e','#c084fc'];
  const ci=Math.abs((ME?.name||'').charCodeAt(0)||0)%cols.length;
  document.getElementById('profAv').style.cssText=`background:rgba(232,52,90,.18);color:${cols[ci]};`;
  document.getElementById('profAv').textContent=(ME?.name||'U').slice(0,2).toUpperCase();
  document.getElementById('profName').textContent='@'+(ME?.name||'my_channel');

  const ti=getTierIdx(pts);
  const badges=[];
  if(ti>=0)badges.push(`<span class="chip ${TIERS[ti].chipCls}">${TIERS[ti].gem} ${TIERS[ti].tag}</span>`);
  if(achieved.length){
    badges.push(`<span class="chip" style="background:rgba(62,207,142,.1);color:var(--green);border:1px solid rgba(62,207,142,.25);">🏆 ${achieved.length} Tier${achieved.length>1?'s':''} Achieved</span>`);
  }
  badges.push(`<span class="chip" style="background:rgba(255,255,255,.05);color:var(--t2);">${(ME?.role||'user').toUpperCase()}</span>`);
  document.getElementById('profBadges').innerHTML=badges.join('');

  document.getElementById('profStats').innerHTML=[
    {n:Number(pts).toLocaleString(),l:'💎 Ruby Pts'},
    {n:Number(stats?.subscribers??0).toLocaleString(),l:'⭐ Subscribers'},
    {n:Number(stats?.likes??0).toLocaleString(),l:'❤️ Likes'},
    {n:Number(stats?.shares??0).toLocaleString(),l:'↗ Real Shares'},
  ].map(s=>`<div class="pst"><div class="pstn">${s.n}</div><div class="pstl">${s.l}</div></div>`).join('');

  // Progress bar
  if(progress){
    const pct=Math.min(100,progress.percentage||0);
    document.getElementById('rmLabel').textContent=progress.next_tier_display?`🚀 Progress to ${progress.next_tier_display}`:'👑 Max Tier Reached!';
    document.getElementById('rmPct').textContent=`${pct}%`;
    document.getElementById('rmFill').style.width=pct+'%';
  }

  // Tier list in profile
  const tierData=RUBY_TIERS_DATA.length?RUBY_TIERS_DATA:TIERS.map(t=>({name:t.id,display_name:t.name,emoji:t.gem,adjusted_threshold:t.thresh}));
  document.getElementById('profTiers').innerHTML=tierData.map((t,i)=>{
    const thresh=t.adjusted_threshold||TIERS[i]?.thresh||0;
    const achieved=pts>=thresh;
    return `<div class="tierc${achieved?' ach':''}" style="margin-bottom:8px;"><span class="tgem">${t.emoji}</span><div class="tbody"><div class="tname">${t.display_name}</div><div class="treq">${Number(thresh).toLocaleString()} pts required</div></div><span class="${achieved?'tba':'tbl'}">${achieved?'✅ Achieved':'🔒 Locked'}</span></div>`;
  }).join('');
}

// ═══════════════════════════════════════════════
//  ADMIN
// ═══════════════════════════════════════════════
function renderAdmin(){
  if(!canModerateUI()){
    document.getElementById('adminStats').innerHTML='';
    document.getElementById('adminQueue').innerHTML='<div style="text-align:center;padding:40px 20px;color:var(--t2);">Only admins can moderate submissions.</div>';
    return;
  }
  const pendingCount=Number(ADMIN_COUNTS.pending||0);
  const approvedCount=Number(ADMIN_COUNTS.approved||0);
  const rejectedCount=Number(ADMIN_COUNTS.rejected||0);
  const visibleCount=Array.isArray(ADMIN_Q)?ADMIN_Q.length:0;
  const sectionLabel=ADMIN_FILTER==='approved'?'Approved Videos':(ADMIN_FILTER==='rejected'?'Rejected Videos':'Pending Review');
  document.getElementById('adminSectionLabel').textContent=sectionLabel;
  document.getElementById('adminStats').innerHTML=`
    <button class="asc${ADMIN_FILTER==='pending'?' on':''}" type="button" onclick="setAdminFilter('pending')"><div class="ascn">${pendingCount}</div><div class="ascl">Pending</div></button>
    <button class="asc${ADMIN_FILTER==='approved'?' on':''}" type="button" onclick="setAdminFilter('approved')"><div class="ascn">${approvedCount}</div><div class="ascl">Approved</div></button>
    <button class="asc${ADMIN_FILTER==='rejected'?' on':''}" type="button" onclick="setAdminFilter('rejected')"><div class="ascn">${rejectedCount}</div><div class="ascl">Rejected</div></button>`;
  if(!visibleCount){
    const emptyText=ADMIN_FILTER==='approved'?'No approved videos found':(ADMIN_FILTER==='rejected'?'No rejected videos found':'No videos pending review');
    document.getElementById('adminQueue').innerHTML=`<div style="text-align:center;padding:40px 20px;color:var(--t2);"><div style="font-size:42px;margin-bottom:10px;">✅</div><div style="font-family:var(--fd);font-size:16px;font-weight:700;color:var(--t1);margin-bottom:6px;">All caught up!</div><div style="font-size:12px;">${emptyText}</div></div>`;
    return;
  }
  document.getElementById('adminQueue').innerHTML=ADMIN_Q.map(function(v){
    const title=escapeHtml(v.title||'Untitled submission');
    const creator=escapeHtml(v.creator?.username||v.creator?.name||'@creator');
    const source=escapeHtml(feedLabel(v));
    const canApprove=v.status!=='approved';
    const canReject=v.status!=='rejected';
    return `<div class="acard" id="ac-${v.id}">
      <div class="actop"><div class="acth">🎬</div><div style="flex:1;min-width:0;"><div class="act">${title}</div><div class="acc">by ${creator}</div><div class="acu">${source}</div></div></div>
      <div class="acbtns">
        ${canApprove?`<button class="abta" type="button" onclick="moderateVideo(${v.id},'approve')">✅ Approve</button>`:''}
        ${canReject?`<button class="abtr" type="button" onclick="moderateVideo(${v.id},'reject')">❌ Reject</button>`:''}
      </div>
      ${ME?.is_admin?`<div class="acbtns" style="margin-top:8px;"><button class="abtr" type="button" onclick="deleteVideo(${v.id})">🗑️ Delete</button></div>`:''}
    </div>`;
  }).join('');
}
function setAdminFilter(status){
  ADMIN_FILTER=status;
  loadAdminQueue();
}
async function moderateVideo(videoId,action){
  const endpoint=action==='approve'?`/shortsplay/data/${videoId}/approve`:`/shortsplay/data/${videoId}/reject`;
  const payload=action==='reject'?{admin_notes:'Rejected by moderator'}:{};
  try{
    const res=await fetch(endpoint,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},body:JSON.stringify(payload)});
    const data=await readJson(res);
    if(!res.ok){toast(`⚠️ ${readErrorMessage(data,'Moderation failed')}`);return;}
    toast(action==='approve'?'✅ Approved and live':'❌ Rejected');
    await loadAdminQueue();await loadFeed();await loadMySubmissions();
  }catch(e){toast('⚠️ Network error while moderating');}
}
async function deleteVideo(videoId){
  if(!ME?.is_admin){toast('⚠️ Only superadmin can delete');return;}
  try{
    const res=await fetch(`/shortsplay/data/${videoId}`,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN}});
    const data=await readJson(res);
    if(!res.ok){toast(`⚠️ ${readErrorMessage(data,'Delete failed')}`);return;}
    toast('🗑️ Deleted by superadmin');await loadAdminQueue();await loadFeed();await loadMySubmissions();
  }catch(e){toast('⚠️ Network error while deleting');}
}

// ═══════════════════════════════════════════════
//  BOOTSTRAP
// ═══════════════════════════════════════════════
async function bootstrapAuth(){
  updateViewportSizing();
  if(BOOT_USER){
    setMeFromUser(BOOT_USER,'player');
    startApp();
    return;
  }
  try{
    const res=await fetch('/shortsplay/auth/me',{headers:{'Accept':'application/json'}});
    const data=await readJson(res);
    if(res.ok&&data?.user){setMeFromUser(data.user,'player');startApp();}
  }catch(e){}
}
window.addEventListener('resize',handleViewportResize,{passive:true});
window.addEventListener('orientationchange',handleViewportResize,{passive:true});
if(window.visualViewport){
  window.visualViewport.addEventListener('resize',handleViewportResize,{passive:true});
}
bootstrapAuth();
</script>
</body>
</html>
