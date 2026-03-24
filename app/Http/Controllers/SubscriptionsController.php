<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\TemplateUnlockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubscriptionsController extends Controller
{
    private const AVAILABLE_TEMPLATES = ['dynamic_service', 'astro_dynamic'];

    private const DEFAULT_YEARLY_BASE_PRICE = 999;
    private const DEFAULT_ASTRO_UNLOCK_PRICE = 499;
    private const DEFAULT_UNLOCK_SLA_HOURS = 24;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $isShopowner = auth()->user()->isShopowner();
        $shop = $this->getOrCreateOwnedShop();
        $dashboardRoute = $isShopowner ? 'myshop' : 'myservice';
        $unlockRoute = $isShopowner ? 'myshop.unlock' : 'myservice.unlock';
        $entityLabel = $isShopowner ? 'Shop' : 'Service';
        $dashboardLabel = $isShopowner ? 'MyShop' : 'MyService';
        $websiteLabel = $isShopowner ? 'Public shop website' : 'Public service website';

        $subscription = new Subscription(['user_id' => auth()->id(), 'shop_id' => $shop->id]);
        $yearlyPrice = $this->getYearlyBasePrice();
        $astroUnlockPrice = $this->getAstroUnlockPrice();
        $unlockSlaHours = $this->getUnlockSlaHours();
        $hasActiveSubscription = $this->hasActiveYearlySubscription($shop);

        $activeSubscription = Subscription::query()
            ->where('user_id', auth()->id())
            ->where('shop_id', $shop->id)
            ->where('plan_key', 'yearly_base')
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('expires_at')
            ->first();

        $latestSubscriptionRequest = Subscription::query()
            ->where('user_id', auth()->id())
            ->where('shop_id', $shop->id)
            ->where('plan_key', 'yearly_base')
            ->latest('id')
            ->first();

        $hasPendingSubscriptionRequest = (bool) ($latestSubscriptionRequest && $latestSubscriptionRequest->status === 'pending');

        $astroUnlocked = TemplateUnlockRequest::query()
            ->where('shop_id', $shop->id)
            ->where('template_key', 'astro_dynamic')
            ->where('status', 'approved')
            ->exists();

        $latestAstroUnlockRequest = TemplateUnlockRequest::query()
            ->where('shop_id', $shop->id)
            ->where('template_key', 'astro_dynamic')
            ->latest('id')
            ->first();
        $hasPendingAstroUnlockRequest = (bool) ($latestAstroUnlockRequest && $latestAstroUnlockRequest->status === 'pending');

        $paymentQrPath = (string) AdminSetting::getValue('payment_qr_image_path', '');
        $paymentBarcodePath = (string) AdminSetting::getValue('payment_barcode_image_path', '');
        $paymentQrUrl = $paymentQrPath !== '' ? Storage::url($paymentQrPath) : '';
        $paymentBarcodeUrl = $paymentBarcodePath !== '' ? Storage::url($paymentBarcodePath) : '';

        $plans = [
            [
                'key' => 'yearly_base',
                'title' => 'Yearly Base Plan',
            'amount' => $yearlyPrice,
                'billing' => 'Per year',
                'description' => 'Includes Dynamic Service Template (free with yearly subscription).',
                'includes' => [
                    'Public service website',
                    'Custom URL slug',
                    'Dynamic Service Template (free)',
                ],
            ],
        ];

        $pageConfig = $shop->page_config ?? [];
        $templateContent = $pageConfig['template_content'] ?? [];
        $activeTemplate = $shop->template ?? 'dynamic_service';

        return view('subscriptions.new', compact(
            'shop',
            'subscription',
            'plans',
            'astroUnlockPrice',
            'unlockSlaHours',
            'hasActiveSubscription',
            'activeSubscription',
            'astroUnlocked',
            'latestAstroUnlockRequest',
            'hasPendingAstroUnlockRequest',
            'paymentQrUrl',
            'paymentBarcodeUrl',
            'activeTemplate',
            'templateContent'
            ,
            'dashboardRoute',
            'unlockRoute',
            'entityLabel',
            'dashboardLabel',
            'websiteLabel',
            'latestSubscriptionRequest',
            'hasPendingSubscriptionRequest'
        ));
    }

    public function updateTemplate(Request $request)
    {
        $dashboardRoute = auth()->user()->isShopowner() ? 'myshop' : 'myservice';
        $shop = $this->getOrCreateOwnedShop();

        $selectedTemplate = (string) $request->input('template', 'dynamic_service');
        if (!in_array($selectedTemplate, self::AVAILABLE_TEMPLATES, true)) {
            $selectedTemplate = 'dynamic_service';
        }

        if ($selectedTemplate === 'astro_dynamic') {
            $isUnlocked = TemplateUnlockRequest::query()
                ->where('shop_id', $shop->id)
                ->where('template_key', 'astro_dynamic')
                ->where('status', 'approved')
                ->exists();

            if (!$isUnlocked) {
                return back()->withErrors([
                    'template' => 'You have to unlock this template first. Click Unlock option and submit payment proof.',
                ])->withInput();
            }
        }

        $shop->template = $selectedTemplate;
        $shop->save();

        return back()->with('notice', 'Template updated successfully.');
    }

    public function store(Request $request)
    {
        $dashboardRoute = auth()->user()->isShopowner() ? 'myshop' : 'myservice';
        $shop = $this->getOrCreateOwnedShop();

        $activeExists = $this->hasActiveYearlySubscription($shop);

        if ($activeExists) {
            return back()->with('notice', 'Your yearly subscription is already active.');
        }

        $pendingExists = Subscription::query()
            ->where('user_id', auth()->id())
            ->where('shop_id', $shop->id)
            ->where('plan_key', 'yearly_base')
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return back()->with('notice', 'Your subscription request is already under review by admin.');
        }

        $validated = $request->validate([
            'plan_key' => 'required|string|in:yearly_base',
            'payment_transaction_id' => 'nullable|string|max:120',
            'payment_screenshot' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'comment' => 'nullable|string|max:2000',
        ]);

        $transactionId = trim((string) ($validated['payment_transaction_id'] ?? ''));
        $hasScreenshot = $request->hasFile('payment_screenshot');

        if ($transactionId === '' && !$hasScreenshot) {
            return back()->withErrors([
                'payment_transaction_id' => 'Provide transaction ID or upload payment screenshot.',
            ])->withInput();
        }

        $planKey = (string) ($validated['plan_key'] ?? 'yearly_base');
        $planAmount = match ($planKey) {
            'yearly_base' => $this->getYearlyBasePrice(),
            default => $this->getYearlyBasePrice(),
        };

        $amount = $planAmount;

        $screenshotPath = null;
        if ($hasScreenshot) {
            $screenshotPath = $request->file('payment_screenshot')->store('subscription_payments', 'public');
        }

        Subscription::create([
            'user_id'    => auth()->id(),
            'shop_id'    => $shop->id,
            'provider'   => 'manual',
            'plan_key'   => $planKey,
            'status'     => 'pending',
            'amount'     => $amount,
            'payment_transaction_id' => $transactionId ?: null,
            'payment_screenshot_path' => $screenshotPath,
            'comment' => trim((string) ($validated['comment'] ?? '')),
        ]);

        return back()->with('notice', 'Subscription request submitted successfully. Admin will review your payment proof soon.');
    }

    public function requestTemplateUnlock(Request $request)
    {
        $unlockRoute = auth()->user()->isShopowner() ? 'myshop.unlock' : 'myservice.unlock';
        $dashboardRoute = auth()->user()->isShopowner() ? 'myshop' : 'myservice';

        $shop = $this->getOrCreateOwnedShop();
        $hasActiveYearlyPlan = $this->hasActiveYearlySubscription($shop);

        if (!$hasActiveYearlyPlan) {
            return back()->with('alert', 'Activate Yearly Base Plan (₹' . $this->getYearlyBasePrice() . ') before requesting Astro Dynamic unlock.');
        }

        $approvedRequestExists = TemplateUnlockRequest::query()
            ->where('shop_id', $shop->id)
            ->where('template_key', 'astro_dynamic')
            ->where('status', 'approved')
            ->exists();

        if ($approvedRequestExists) {
            return redirect()->route($unlockRoute)->with('notice', 'Astro Dynamic is already unlocked for your page.');
        }

        $pendingRequestExists = TemplateUnlockRequest::query()
            ->where('shop_id', $shop->id)
            ->where('template_key', 'astro_dynamic')
            ->where('status', 'pending')
            ->exists();

        if ($pendingRequestExists) {
            return redirect()->route($unlockRoute)->with('notice', 'Your unlock request is already submitted and under review by admin.');
        }

        $validated = $request->validate([
            'template_key' => 'required|string|in:astro_dynamic',
            'payment_transaction_id' => 'nullable|string|max:120',
            'payment_screenshot' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'comment' => 'nullable|string|max:2000',
        ]);

        $unlockAmount = $this->getAstroUnlockPrice();
        $slaHours = $this->getUnlockSlaHours();

        $transactionId = trim((string) ($validated['payment_transaction_id'] ?? ''));
        $hasScreenshot = $request->hasFile('payment_screenshot');

        if ($transactionId === '' && !$hasScreenshot) {
            return back()->withErrors([
                'payment_transaction_id' => 'Provide transaction ID or upload payment screenshot.',
            ])->withInput();
        }

        $screenshotPath = null;
        if ($hasScreenshot) {
            $screenshotPath = $request->file('payment_screenshot')->store('template_unlock_payments', 'public');
        }

        TemplateUnlockRequest::create([
            'user_id' => auth()->id(),
            'shop_id' => $shop->id,
            'template_key' => 'astro_dynamic',
            'amount' => $unlockAmount,
            'status' => 'pending',
            'payment_transaction_id' => $transactionId ?: null,
            'payment_screenshot_path' => $screenshotPath,
            'comment' => trim((string) ($validated['comment'] ?? '')),
        ]);

        return redirect()->route($unlockRoute)->with('notice', 'Unlock request sent successfully! Review is usually completed within ' . $slaHours . ' hours.');
    }

    public function updateProfile(Request $request)
    {
        $shop = $this->getOrCreateOwnedShop();

        if (!$this->hasActiveYearlySubscription($shop)) {
            return back()->with('alert', 'Activate your yearly base subscription first to save profile details.');
        }

        $validated = $request->validate([
            'profile.provider_name' => 'nullable|string|max:120',
            'profile.provider_age' => 'nullable|string|max:20',
            'profile.provider_title' => 'nullable|string|max:160',
            'profile.provider_email' => 'nullable|email|max:160',
            'profile.provider_contact' => 'nullable|string|max:40',
            'profile.provider_experience' => 'nullable|string|max:120',
            'profile.provider_bio' => 'nullable|string|max:3000',
            'profile.provider_photo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $cfg = $shop->page_config ?? [];
        $tc = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];

        $owner = auth()->user();
        $profile = $validated['profile'] ?? [];

        $existingPhoto = trim((string) $request->input('profile.provider_photo_existing', $tc['provider_photo'] ?? ''));
        $providerPhoto = $existingPhoto;
        if ($request->hasFile('profile.provider_photo_file')) {
            if ($existingPhoto && !str_starts_with($existingPhoto, 'http://') && !str_starts_with($existingPhoto, 'https://') && !str_starts_with($existingPhoto, 'data:')) {
                Storage::disk('public')->delete(ltrim(str_replace('/storage/', '', $existingPhoto), '/'));
            }
            $providerPhoto = $request->file('profile.provider_photo_file')->store('provider_profiles', 'public');
        }

        $tc['provider_name'] = trim((string) ($profile['provider_name'] ?? ($tc['provider_name'] ?? ($owner->full_name ?: 'सेवा प्रदाता'))));
        $tc['provider_age'] = trim((string) ($profile['provider_age'] ?? ($tc['provider_age'] ?? '')));
        $tc['provider_title'] = trim((string) ($profile['provider_title'] ?? ($tc['provider_title'] ?? 'संस्थापक आणि प्रमुख तज्ञ')));
        $tc['provider_email'] = trim((string) ($profile['provider_email'] ?? ($tc['provider_email'] ?? ($owner->email ?: ''))));
        $tc['provider_contact'] = trim((string) ($profile['provider_contact'] ?? ($tc['provider_contact'] ?? ($shop->phone ?: ($owner->mobile_number ?? '')))));
        $tc['provider_experience'] = trim((string) ($profile['provider_experience'] ?? ($tc['provider_experience'] ?? '५+ वर्षांचा अनुभव')));
        $tc['provider_bio'] = trim((string) ($profile['provider_bio'] ?? ($tc['provider_bio'] ?? '')));
        $tc['provider_photo'] = $providerPhoto;

        $cfg['template_content'] = $tc;
        $shop->page_config = $cfg;
        $shop->save();

        return back()->with('notice', 'Profile details updated successfully.');
    }

    private function getYearlyBasePrice(): float
    {
        return (float) AdminSetting::getValue('yearly_base_plan_price', (string) self::DEFAULT_YEARLY_BASE_PRICE);
    }

    private function getAstroUnlockPrice(): float
    {
        return (float) AdminSetting::getValue('astro_dynamic_template_price', (string) self::DEFAULT_ASTRO_UNLOCK_PRICE);
    }

    private function getUnlockSlaHours(): int
    {
        return (int) AdminSetting::getValue('template_unlock_sla_hours', (string) self::DEFAULT_UNLOCK_SLA_HOURS);
    }

    private function hasActiveYearlySubscription(Shop $shop): bool
    {
        return Subscription::query()
            ->where('user_id', auth()->id())
            ->where('shop_id', $shop->id)
            ->where('plan_key', 'yearly_base')
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->exists();
    }

    private function getOrCreateOwnedShop(): Shop
    {
        $user = auth()->user();
        $shop = $user->shops()->first();
        if ($shop) {
            return $shop;
        }

        return $user->shops()->create([
            'name' => $user->isShopowner() ? ($user->full_name . "'s Shop") : 'My Service',
            'description' => '',
            'phone' => $user->mobile_number ?? null,
            'address' => '',
            'template' => 'dynamic_service',
            'page_config' => [],
        ]);
    }
}
