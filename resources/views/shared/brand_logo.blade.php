<svg class="{{ $className ?? '' }}" viewBox="0 0 64 64" role="img" aria-label="{{ $title ?? 'AajchaOffer' }}" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="aoBg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#C368CA"/>
      <stop offset="100%" stop-color="#F391A0"/>
    </linearGradient>
    <linearGradient id="aoLens" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.95"/>
      <stop offset="100%" stop-color="#ffe5f4" stop-opacity="0.92"/>
    </linearGradient>
  </defs>

  <rect x="2" y="2" width="60" height="60" rx="16" fill="url(#aoBg)"/>
  <rect x="6" y="6" width="52" height="52" rx="13" fill="none" stroke="rgba(255,255,255,0.35)" stroke-width="1.4"/>

  <circle cx="30" cy="30" r="12" fill="none" stroke="url(#aoLens)" stroke-width="4"/>
  <line x1="38" y1="38" x2="48" y2="48" stroke="#fff" stroke-width="4" stroke-linecap="round"/>

  <text x="30" y="33.5" text-anchor="middle" font-size="10" font-weight="800" fill="#fff" font-family="Segoe UI, Arial, sans-serif">₹</text>
  <path d="M46 16l1.8 3.8L52 21.6l-4.2 1.8L46 27.2l-1.8-3.8L40 21.6l4.2-1.8z" fill="#fff" fill-opacity="0.95"/>
</svg>
