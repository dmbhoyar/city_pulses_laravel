<style>
  .ao-coming-wrap{max-width:760px;margin:28px auto;padding:0 12px}
  .ao-coming-card{position:relative;overflow:hidden;background:linear-gradient(135deg,#fff 0%,#fff7fb 45%,#f2fffc 100%);border:1px solid #f0e8dc;border-radius:18px;padding:30px 24px;box-shadow:0 18px 40px rgba(16,24,40,.08)}
  .ao-coming-card:before{content:'';position:absolute;inset:0 0 auto 0;height:4px;background:linear-gradient(90deg,#C368CA,#F391A0,#2A9D8F)}
  .ao-coming-chip{display:inline-flex;align-items:center;gap:8px;border:1px solid #f3d2ea;background:#fff;border-radius:999px;padding:6px 12px;font-size:.76rem;font-weight:800;color:#8e5bd6;letter-spacing:.25px}
  .ao-coming-title{margin:12px 0 8px;font-size:1.72rem;line-height:1.15;color:#1f2a37;font-weight:800}
  .ao-coming-sub{margin:0;color:#667085;font-size:.98rem;line-height:1.65}
  .ao-coming-slogan{margin-top:16px;padding:12px 14px;border-radius:12px;background:#fff;border:1px dashed #d7c5ef;color:#3f2d56;font-weight:700}
  .ao-coming-actions{margin-top:18px;display:flex;gap:10px;flex-wrap:wrap}
  .ao-coming-btn{display:inline-flex;align-items:center;gap:7px;text-decoration:none;border-radius:12px;padding:10px 14px;font-weight:700;font-size:.88rem}
  .ao-coming-btn.primary{background:#2A9D8F;color:#fff}
  .ao-coming-btn.ghost{background:#fff;color:#475467;border:1px solid #dbe3ef}
  @media(max-width:640px){.ao-coming-title{font-size:1.42rem}}
</style>

<div class="ao-coming-wrap">
  <div class="ao-coming-card">
    <span class="ao-coming-chip">{{ $chip ?? '🚧 Work in Progress' }}</span>
    <h1 class="ao-coming-title">{{ $title ?? 'This page is getting ready' }}</h1>
    <p class="ao-coming-sub">{{ $subtitle ?? 'We are polishing this feature to make it simple, fast, and useful for everyone.' }}</p>
    <div class="ao-coming-slogan">{{ $slogan ?? 'Big things are loading… stay tuned! ⚡' }}</div>
    <div class="ao-coming-actions">
      <a href="{{ $primaryUrl ?? route('home') }}" class="ao-coming-btn primary">{{ $primaryLabel ?? 'Go to Home' }}</a>
      <a href="{{ $secondaryUrl ?? route('about') }}" class="ao-coming-btn ghost">{{ $secondaryLabel ?? 'Know More' }}</a>
    </div>
  </div>
</div>