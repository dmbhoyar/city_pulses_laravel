@extends('layouts.app')
@php
$userPoints = Auth::check() ? (Auth::user()->ruby_points ?? 0) : 0;
$redemptions = isset($userRedemptions) ? $userRedemptions : [];
// $apiCoupons is now always passed from the controller as a collection
// Merge offers and coupons for JS
$offersArray = isset($offers) ? $offers->map(function($o) {
    return array_merge($o->toArray(), ['_type' => 'offer']);
})->toArray() : [];
$couponsArray = isset($apiCoupons) ? $apiCoupons->map(function($c) {
    return array_merge($c->toArray(), ['_type' => 'coupon']);
})->toArray() : [];
@endphp
@section('content')
@if(auth()->check() && auth()->user()->isSuperadmin())
<script>window.isSuperadmin = true;</script>
@else
<script>window.isSuperadmin = false;</script>
@endif
<div class="offers-coupon-hub offers-coupon-bg">
    <div class="background-effect"></div>
    <div class="offers-header" style="display:flex;justify-content:space-between;align-items:center;max-width:1400px;margin:0 auto 1.5rem;padding:0 1.5rem;">
        <div style="font-family:'Fraunces',serif;font-size:2rem;font-weight:900;background:linear-gradient(135deg,#D81159,#8F2D56);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-0.02em;">💎 Ruby Points</div>
        <div style="display:flex;align-items:center;gap:1rem;">
            <div class="ruby-balance" style="display:flex;align-items:center;gap:1rem;background:linear-gradient(135deg,#D81159,#8F2D56);color:white;padding:1rem 1.5rem;border-radius:100px;font-weight:700;box-shadow:0 8px 24px rgba(216,17,89,0.3);animation:pulse 2s ease-in-out infinite;">
                <span class="ruby-icon" style="font-size:1.5rem;animation:spin 3s linear infinite;">💎</span>
                <span id="userPoints">{{ number_format($userPoints) }}</span>
                <span>Points</span>
            </div>
            @if(auth()->check() && auth()->user()->isSuperadmin())
                <button class="btn btn-danger ms-2" id="deleteExpiredCouponsBtn" style="margin-left:1.5rem;" onclick="deleteExpiredCoupons()">🗑️ Delete Expired Coupons</button>
            @endif
        </div>
    </div>
    <div class="hero">
        <div class="hero-title">{{ __('ui.offers_benefits') }}</div>
        <div class="hero-desc">{{ __('ui.redeem_hero_desc') }}</div>
        <div class="search-container">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="searchInput" placeholder="Search for stores, categories, or deals...">
        </div>
        <div class="filters">
            <button class="filter-btn active" data-filter="all">{{ __('ui.offers_my_redeemed') }}</button>
            <button class="filter-btn" data-filter="affordable">{{ __('ui.offers_redeem_for') }}</button>
            <button class="filter-btn" data-filter="shopping">{{ __('ui.offers_points') }}</button>
            <button class="filter-btn" data-filter="food">{{ __('ui.offers_redeem') }}</button>
            <button class="filter-btn" data-filter="travel">{{ __('ui.offers_redeemed') }}</button>
            <button class="filter-btn" data-filter="entertainment">{{ __('ui.offers_delete') }}</button>
        </div>
    </div>
    <div class="loading" id="loadingState" style="display: none;">
        <div class="spinner"></div>
        <p>Fetching latest coupons...</p>
    </div>
    <div class="coupons-grid" id="couponsGrid">
        <!-- Offer and coupon cards will be rendered here -->
    </div>
    <div class="empty-state" id="emptyState" style="display: none;">
        <div class="empty-state-svg">
            <!-- Decorative SVG replaced with div for structure -->
            <div style="width:80px;height:80px;border-radius:50%;border:4px solid #ccc;margin:0 auto 10px;"></div>
            <div style="width:60px;height:4px;background:#ccc;margin:10px auto 0;border-radius:2px;"></div>
        </div>
        <div class="empty-state-title">{{ __('ui.offers_no_items') }}</div>
        <div class="empty-state-desc">{{ __('ui.offers_loading') }}</div>
    </div>
    <div class="my-redemptions">
        <div class="section-title">{{ __('ui.offers_my_redeemed') }}</div>
        <!-- Redemption content here -->
    </div>
</div>
@endsection

@push('styles')
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@300;600;900&family=DM+Sans:wght@400;500;700&display=swap');


:root {
    --ruby-primary: #D81159;
    --ruby-dark: #8F2D56;
    --ruby-light: #FFBC42;
    --emerald: #0EAD69;
    --sapphire: #218380;
    --pearl: #F7F7F2;
    --obsidian: #1A1423;
    --shadow: rgba(26, 20, 35, 0.1);
    --font-display: 'Fraunces', serif;
    --font-body: 'DM Sans', sans-serif;
    --space-xs: 0.5rem;
    --space-sm: 1rem;
    --space-md: 1.5rem;
    --space-lg: 2.5rem;
    --space-xl: 4rem;
    --blur: 24px;
    --radius: 16px;
    --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Desktop container width and centering */
.offers-coupon-hub {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 2rem 2.5vw;
    border-radius: var(--radius);
    box-shadow: 0 8px 32px var(--shadow);
    min-height: 80vh;
    position: relative;
    z-index: 1;
    overflow: hidden;
}

@media (max-width: 768px) {
    .offers-coupon-hub {
        max-width: 100%;
        margin: 0;
        padding: 0.5rem 0.5rem;
        border-radius: 0;
        box-shadow: none;
    }
}

.offers-coupon-hub {
    font-family: var(--font-body);
    background: var(--pearl);
    color: var(--obsidian);
    line-height: 1.6;
    overflow-x: hidden;
}


/* Animated Background */
.offers-coupon-hub .background-effect {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%; z-index: 0;
    opacity: 0.4; pointer-events: none;
}
.offers-coupon-hub .background-effect::before,
.offers-coupon-hub .background-effect::after {
    content: '';
    position: absolute;
    width: 800px; height: 800px;
    border-radius: 50%; filter: blur(80px);
    animation: float 20s ease-in-out infinite;
}
.offers-coupon-hub .background-effect::before {
    background: radial-gradient(circle, var(--ruby-primary), transparent);
    top: -200px; left: -200px; animation-delay: 0s;
}
.offers-coupon-hub .background-effect::after {
    background: radial-gradient(circle, var(--sapphire), transparent);
    bottom: -200px; right: -200px; animation-delay: 10s;
}
@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(50px, 100px) scale(1.1); }
    66% { transform: translate(-50px, -50px) scale(0.9); }
}

/* Hero Section */
.offers-coupon-hub .hero {
    text-align: center;
    margin-bottom: var(--space-xl);
    animation: fadeInUp 0.8s ease-out;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.offers-coupon-hub .hero-title {
    font-family: var(--font-display);
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: var(--space-md);
    background: linear-gradient(135deg, var(--obsidian), var(--ruby-dark));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.offers-coupon-hub .hero-desc {
    font-size: 1.25rem;
    color: var(--ruby-dark);
    max-width: 600px;
    margin: 0 auto;
}

/* Filters */
.offers-coupon-hub .filters {
    display: flex;
    gap: var(--space-sm);
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: var(--space-lg);
    animation: fadeInUp 0.8s ease-out 0.2s both;
}
.offers-coupon-hub .filter-btn {
    padding: var(--space-sm) var(--space-md);
    border: 2px solid var(--ruby-primary);
    background: white;
    color: var(--ruby-primary);
    font-weight: 600;
    border-radius: 100px;
    cursor: pointer;
    transition: all var(--transition);
    font-family: var(--font-body);
    font-size: 0.95rem;
}
.offers-coupon-hub .filter-btn:hover {
    background: var(--ruby-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(216, 17, 89, 0.3);
}
.offers-coupon-hub .filter-btn.active {
    background: var(--ruby-primary);
    color: white;
    box-shadow: 0 4px 12px rgba(216, 17, 89, 0.4);
}

/* Search Bar */
.offers-coupon-hub .search-container {
    max-width: 600px;
    margin: 0 auto var(--space-lg);
    position: relative;
    animation: fadeInUp 0.8s ease-out 0.4s both;
}
.offers-coupon-hub .search-input {
    width: 100%;
    padding: var(--space-md) var(--space-md) var(--space-md) 3.5rem;
    border: 2px solid rgba(26, 20, 35, 0.1);
    border-radius: var(--radius);
    font-size: 1rem;
    font-family: var(--font-body);
    transition: all var(--transition);
    background: white;
}
.offers-coupon-hub .search-input:focus {
    outline: none;
    border-color: var(--ruby-primary);
    box-shadow: 0 0 0 4px rgba(216, 17, 89, 0.1);
}
.offers-coupon-hub .search-icon {
    position: absolute;
    left: var(--space-md);
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    color: var(--ruby-primary);
}

/* Coupons Grid */
.offers-coupon-hub .coupons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--space-lg);
    margin-top: var(--space-lg);
}

/* Coupon Card */
.offers-coupon-hub .coupon-card {
    background: white;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 4px 24px var(--shadow);
    transition: all var(--transition);
    animation: fadeInUp 0.6s ease-out both;
    position: relative;
}
.offers-coupon-hub .coupon-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 48px rgba(26, 20, 35, 0.15);
}
.offers-coupon-hub .coupon-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 4px;
    background: linear-gradient(90deg, var(--ruby-primary), var(--ruby-light), var(--emerald));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform var(--transition);
}
.offers-coupon-hub .coupon-card:hover::before {
    transform: scaleX(1);
}
.offers-coupon-hub .coupon-image {
    width: 100%; height: 180px; object-fit: cover;
    background: linear-gradient(135deg, var(--pearl), var(--ruby-light));
}
.offers-coupon-hub .coupon-content {
    padding: var(--space-md);
}
.offers-coupon-hub .store-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: var(--sapphire);
    color: white;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: var(--space-sm);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.offers-coupon-hub .coupon-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: var(--space-sm);
    color: var(--obsidian);
    line-height: 1.3;
}
.offers-coupon-hub .coupon-description {
    color: var(--ruby-dark);
    margin-bottom: var(--space-md);
    font-size: 0.95rem;
}
.offers-coupon-hub .discount-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--emerald), var(--sapphire));
    color: white;
    padding: var(--space-sm) var(--space-md);
    border-radius: 100px;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: var(--space-md);
}
.offers-coupon-hub .coupon-footer {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
    padding-top: var(--space-md);
    border-top: 1px solid rgba(26, 20, 35, 0.1);
}
.offers-coupon-hub .coupon-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--space-sm);
}
.offers-coupon-hub .action-buttons {
    display: flex;
    gap: var(--space-sm);
    flex: 1;
}
.offers-coupon-hub .ruby-cost {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    color: var(--ruby-primary);
    font-size: 1.1rem;
}
.offers-coupon-hub .redeem-btn {
    padding: var(--space-sm) var(--space-md);
    background: linear-gradient(135deg, var(--ruby-primary), var(--ruby-dark));
    color: white;
    border: none;
    border-radius: 100px;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--transition);
    font-family: var(--font-body);
    box-shadow: 0 4px 12px rgba(216, 17, 89, 0.3);
    flex: 1;
    font-size: 0.9rem;
}
.offers-coupon-hub .redeem-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(216, 17, 89, 0.4);
}
.offers-coupon-hub .redeem-btn:disabled {
    background: linear-gradient(135deg, #ccc, #999);
    cursor: not-allowed;
    opacity: 0.6;
}
.offers-coupon-hub .shop-now-btn {
    padding: var(--space-sm) var(--space-md);
    background: white;
    color: var(--sapphire);
    border: 2px solid var(--sapphire);
    border-radius: 100px;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--transition);
    font-family: var(--font-body);
    flex: 1;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.offers-coupon-hub .shop-now-btn:hover {
    background: var(--sapphire);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(33, 131, 128, 0.3);
}
.offers-coupon-hub .expiry-tag {
    font-size: 0.85rem;
    color: var(--ruby-dark);
    margin-top: var(--space-sm);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Responsive */
@media (max-width: 768px) {
    .offers-coupon-hub .coupons-grid {
        grid-template-columns: 1fr;
    }
    .offers-coupon-hub .hero-title {
        font-size: 2.5rem;
    }
    .offers-coupon-hub .filters {
        flex-direction: column;
    }
    .offers-coupon-hub .filter-btn {
        width: 100%;
    }
    .offers-coupon-hub .action-buttons {
        flex-direction: column;
    }
}

/* Empty State */
.offers-coupon-hub .empty-state {
    text-align: center;
    padding: var(--space-xl);
    color: var(--ruby-dark);
}

/* My Redemptions Section */
.offers-coupon-hub .my-redemptions {
    margin-top: var(--space-xl);
    padding-top: var(--space-xl);
    border-top: 2px solid rgba(26, 20, 35, 0.1);
}
.offers-coupon-hub .section-title {
    font-family: var(--font-display);
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: var(--space-lg);
    text-align: center;
}
/* End offers coupon hub styles */
@endpush


@push('scripts')
<script>
// Provide translations to JS from Blade
window.translations = {
    redeem_for: "{{ __('ui.redeem_for') }}",
    redeem_to_unlock_code: "{{ __('ui.redeem_to_unlock_code') }}",
    shop_now: "{{ __('ui.shop_now') }}"
};
window.allItems = @json(array_merge($offersArray, $couponsArray));
let allItems = window.allItems || [];
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Log all items count (now inside DOMContentLoaded)
    console.log('All items:', allItems.length);
    // Offers Coupon Hub JS: Handles search, filter, and coupon actions
    window.deleteExpiredCoupons = async function() {
        if (!confirm('Are you sure you want to delete all expired coupons? This action cannot be undone.')) return;
        try {
            const res = await fetch('/admin/coupons/delete-expired', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await res.json();
            if (data.success) {
                alert('Expired coupons deleted successfully.');
                location.reload();
            } else {
                alert(data.error || 'Failed to delete expired coupons.');
            }
        } catch (e) {
            alert('Network error.');
        }
    }
    // Search/filter logic
    const searchInput = document.getElementById('searchInput');
    const filterBtns = document.querySelectorAll('.offers-coupon-hub .filter-btn');
    const couponsGrid = document.getElementById('couponsGrid');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    let userPoints = Number(document.getElementById('userPoints')?.textContent.replace(/,/g, '') || 0);

    // Custom popup modal
    function showPopup(message) {
        let modal = document.getElementById('customPopupModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'customPopupModal';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100vw';
            modal.style.height = '100vh';
            modal.style.background = 'rgba(26,20,35,0.45)';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.zIndex = '9999';
            modal.innerHTML = `
                <div style="background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(26,20,35,0.18);padding:2.5rem 2rem;max-width:90vw;width:350px;text-align:center;">
                    <div style="font-size:1.3rem;font-weight:700;color:#8F2D56;margin-bottom:1.2rem;">Notice</div>
                    <div id="customPopupMessage" style="font-size:1.05rem;color:#1A1423;margin-bottom:2rem;"></div>
                    <button id="customPopupClose" style="padding:0.7rem 2.5rem;background:linear-gradient(135deg,#D81159,#8F2D56);color:white;border:none;border-radius:100px;font-weight:700;font-size:1rem;cursor:pointer;">OK</button>
                </div>
            `;
            document.body.appendChild(modal);
            modal.querySelector('#customPopupClose').onclick = function() {
                modal.style.display = 'none';
            };
        }
        modal.querySelector('#customPopupMessage').textContent = message;
        modal.style.display = 'flex';
    }

    function renderCoupons(list) {
        couponsGrid.innerHTML = '';
        if (!list.length) {
            emptyState.style.display = 'block';
            return;
        }
        emptyState.style.display = 'none';
        list.forEach(item => {
            const isCoupon = item._type === 'coupon';
            const canAfford = userPoints >= (item.points_required || 0);
            const card = document.createElement('div');
            card.className = 'coupon-card';
            let deleteBtn = '';
            if (window.isSuperadmin) {
                deleteBtn = `<button class="delete-coupon-btn" data-id="${item.id}" style="background:#fff;border:1px solid #eee;color:#8F2D56;padding:0.5rem 1rem;border-radius:10px;cursor:pointer;">🗑️ Delete</button>`;
            }
            let redeemBtn = '';
            // Default points_required to 200 if not set or 0
            let pointsRequired = item.points_required && item.points_required > 0 ? item.points_required : 200;
            let editPointsBtn = '';
            if (window.isSuperadmin) {
                editPointsBtn = `<button class="edit-points-btn" data-id="${item.id}" data-current="${pointsRequired}" style="background:#fff;border:1px solid #eee;color:#218380;padding:0.3rem 0.8rem;border-radius:8px;cursor:pointer;font-size:0.95rem;margin-left:0.5rem;">✏️ Points</button>`;
            }
            // Show redeem button for both coupons and offers (with translation)
            redeemBtn = `<button class="redeem-btn" ${canAfford ? '' : 'disabled'} data-id="${item.id}" data-title="${item.title || ''}" data-store="${item.store || ''}" data-code="${item.code || item.discount_text || ''}" data-points="${pointsRequired}" style="background:linear-gradient(135deg,#D81159,#8F2D56);color:white;padding:0.75rem 1.5rem;border:none;border-radius:100px;font-weight:700;box-shadow:0 4px 12px rgba(216,17,89,0.2);font-size:1rem;cursor:pointer;transition:all 0.2s;">${window.translations.redeem_for} <span class="ruby-cost">💎 ${pointsRequired}</span></button>${editPointsBtn}`;
            // Only show code after redemption (add a placeholder or lock icon before redeem)
            let codeSection = '';
            if (item.redeemed) {
                codeSection = `<div class="discount-badge" style="margin-bottom:1rem;">${item.discount_text || item.code || ''}</div>`;
            } else {
                codeSection = `<div class="discount-badge" style="margin-bottom:1rem;opacity:0.5;"><span style="font-size:1.2em;">🔒</span> ${window.translations.redeem_to_unlock_code}</div>`;
            }
            card.innerHTML = `
                
                <div class="coupon-content">
                    <span class="store-badge">${item.store ? item.store : ''}</span>
                    <div class="coupon-title">${item.title ? item.title : ''}</div>
                    <div class="coupon-description">${item.description ? item.description : (item.content ? item.content : '')}</div>
                    ${codeSection}
                    <div class="coupon-footer" style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;flex-wrap:wrap;">
                        ${redeemBtn}
                        <a class="shop-now-btn" href="${item.shop_url ? item.shop_url : '#'}" target="_blank" style="padding:0.75rem 1.5rem;background:white;color:#218380;border:2px solid #218380;border-radius:100px;font-weight:700;font-size:1rem;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;">${window.translations.shop_now}</a>
                        ${deleteBtn}
                        <div class="expiry-tag" style="font-size:0.95rem;color:#8F2D56;margin-left:auto;">Expires: ${(item.expiry ? item.expiry : (item.expiry_date ? item.expiry_date : 'N/A'))}</div>
                    </div>
                </div>
            `;
            couponsGrid.appendChild(card);
        });
        // Attach redeem handlers
        couponsGrid.querySelectorAll('.redeem-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.disabled) return;
                const itemId = this.getAttribute('data-id');
                const itemTitle = this.getAttribute('data-title');
                const itemStore = this.getAttribute('data-store');
                const itemCode = this.getAttribute('data-code');
                const itemPoints = parseInt(this.getAttribute('data-points'), 10) || 200;
                // Find the item type from allItems
                const itemObj = allItems.find(i => String(i.id) === String(itemId));
                const itemType = itemObj && itemObj._type ? itemObj._type : 'coupon';
                // Check login
                if (!window.isLoggedIn) {
                    if (confirm('You must be logged in to redeem. Login now?')) {
                        window.location.href = '/login';
                    }
                    return;
                }
                // Check points
                if (userPoints < itemPoints) {
                    showPopup('You do not have enough Ruby Points to redeem this coupon.');
                    return;
                }
                // Redeem via AJAX
                let body = {};
                if (itemType === 'offer') {
                    body = { offer_id: itemId };
                } else {
                    body = { coupon_code: itemCode, store: itemStore, title: itemTitle };
                }
                fetch('/coupons/redeem', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(body)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Coupon redeemed!');
                        userPoints -= itemPoints;
                        document.getElementById('userPoints').textContent = userPoints;
                        // Show the code on the card after redeem (simulate by updating the DOM)
                        if (itemType === 'offer') {
                            this.closest('.coupon-card').querySelector('.discount-badge').innerHTML = 'Redeemed!';
                        } else {
                            this.closest('.coupon-card').querySelector('.discount-badge').innerHTML = itemCode;
                        }
                        this.closest('.coupon-card').querySelector('.discount-badge').style.opacity = 1;
                        fetchMyRedemptions();
                        this.disabled = true;
                    } else {
                        alert(data.error || 'Failed to redeem coupon.');
                    }
                })
                .catch(() => alert('Network error.'));
            });
        });
        // Attach delete handlers
        couponsGrid.querySelectorAll('.delete-coupon-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const couponId = this.getAttribute('data-id');
                if (!confirm('Are you sure you want to delete this coupon?')) return;
                fetch(`/admin/coupons/${couponId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Coupon deleted successfully.');
                        location.reload();
                    } else {
                        alert(data.error || 'Failed to delete coupon.');
                    }
                })
                .catch(() => alert('Network error.'));
            });
        });
        // Attach edit points handlers (superadmin only)
        couponsGrid.querySelectorAll('.edit-points-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const currentPoints = this.getAttribute('data-current');
                // Find the item type from allItems
                const itemObj = allItems.find(i => String(i.id) === String(itemId));
                const itemType = itemObj && itemObj._type ? itemObj._type : 'coupon';
                showEditPointsModal(itemId, currentPoints, itemType);
            });
        });
    }

    // Modal for editing points
    function showEditPointsModal(itemId, currentPoints, itemType) {
        let modal = document.getElementById('editPointsModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'editPointsModal';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100vw';
            modal.style.height = '100vh';
            modal.style.background = 'rgba(26,20,35,0.45)';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.zIndex = '9999';
            modal.innerHTML = `
                <div style="background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(26,20,35,0.18);padding:2.5rem 2rem;max-width:90vw;width:350px;text-align:center;">
                    <div style="font-size:1.3rem;font-weight:700;color:#218380;margin-bottom:1.2rem;">Set Redemption Points</div>
                    <div style="font-size:1.05rem;color:#1A1423;margin-bottom:1.2rem;">How many Ruby Points are required to redeem this coupon?</div>
                    <input id="editPointsInput" type="number" min="1" style="width:120px;padding:0.5rem 1rem;font-size:1.1rem;border:1px solid #ccc;border-radius:8px;margin-bottom:1.5rem;" value="${currentPoints}">
                    <div style="margin-bottom:1.5rem;"></div>
                    <button id="editPointsSave" style="padding:0.7rem 2.5rem;background:linear-gradient(135deg,#D81159,#8F2D56);color:white;border:none;border-radius:100px;font-weight:700;font-size:1rem;cursor:pointer;">Save</button>
                    <button id="editPointsCancel" style="padding:0.7rem 2.5rem;background:#eee;color:#8F2D56;border:none;border-radius:100px;font-weight:700;font-size:1rem;cursor:pointer;margin-left:1rem;">Cancel</button>
                </div>
            `;
            document.body.appendChild(modal);
        }
        modal.style.display = 'flex';
        modal.querySelector('#editPointsInput').focus();
        modal.querySelector('#editPointsCancel').onclick = function() {
            modal.style.display = 'none';
        };
        modal.querySelector('#editPointsSave').onclick = function() {
            const newPoints = parseInt(modal.querySelector('#editPointsInput').value, 10);
            if (!newPoints || newPoints < 1) {
                showPopup('Please enter a valid number of points.');
                return;
            }
            // Save via AJAX to correct endpoint
            let url = '';
            if (itemType === 'offer') {
                url = `/admin/offers/${itemId}/set-points`;
            } else {
                url = `/admin/coupons/${itemId}/set-points`;
            }
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ points_required: newPoints })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showPopup('Points updated successfully!');
                    modal.style.display = 'none';
                    location.reload();
                } else {
                    showPopup(data.error || 'Failed to update points.');
                }
            })
            .catch(() => showPopup('Network error.'));
        };
    }

    // Expose login state to JS
    window.isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

    // Fetch and render My Redeemed Coupons
    function fetchMyRedemptions() {
        fetch('/coupons/my-redemptions')
            .then(res => res.json())
            .then(data => {
                const redemptions = data.redemptions || [];
                const redemptionsDiv = document.querySelector('.my-redemptions');
                let html = '<div class="section-title">My Redeemed Coupons</div>';
                if (!redemptions.length) {
                    html += '<div style="color:#8F2D56;text-align:center;">No redeemed coupons yet.</div>';
                } else {
                    html += '<div class="coupons-grid">';
                    redemptions.forEach(r => {
                        html += `<div class="coupon-card">
                            <div class="coupon-content">
                                <span class="store-badge">${r.store || ''}</span>
                                <div class="coupon-title">${r.title || ''}</div>
                                <div class="discount-badge">${r.coupon_code || ''}</div>
                                <div class="expiry-tag" style="font-size:0.95rem;color:#8F2D56;margin-top:1rem;">Redeemed: ${r.redeemed_at ? r.redeemed_at.substring(0,10) : ''}</div>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                }
                redemptionsDiv.innerHTML = html;
            });
    }
    if (window.isLoggedIn) fetchMyRedemptions();

    function filterCoupons() {
        let val = (searchInput.value || '').toLowerCase();
        let activeFilter = document.querySelector('.offers-coupon-hub .filter-btn.active')?.dataset.filter || 'all';
        let filtered = allItems.filter(item => {
            let match = !val || (item.title && item.title.toLowerCase().includes(val)) || (item.store && item.store.toLowerCase().includes(val));
            if (activeFilter === 'all') return match;
            if (activeFilter === 'affordable') return match && userPoints >= (item.points_required || 0);
            return match && (item.category === activeFilter);
        });
        renderCoupons(filtered);
    }
    if (searchInput) {
        searchInput.addEventListener('input', filterCoupons);
    }
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterCoupons();
        });
    });
    // Initial render
    filterCoupons();

});
</script>
@endpush

