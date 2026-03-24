<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\AdminSetting;
use App\Models\ClientRequest;
use App\Models\Subscription;
use App\Models\TemplateUnlockRequest;
use App\Models\Update;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MyserviceController extends Controller
{
    private const AVAILABLE_TEMPLATES = ['dynamic_service', 'astro_dynamic'];
    private const REQUEST_STATUSES = ['new', 'confirmed', 'pending', 'completed', 'cancelled', 'callback', 'called'];

    private const RESERVED_PUBLIC_SLUGS = [
        'login', 'logout', 'register', 'password', 'jobs', 'shops', 'listings', 'farming', 'updates',
        'rents', 'buy', 'services', 'newspaper', 'offers', 'about', 'robots-txt', 'sitemap-xml',
        'shop-dashboard', 'shop_dashboard', 'subscriptions', 'myshop', 'myservice', 'admin', 'webhooks', 'up',
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('service_provider');
    }

    public function index()
    {
        $shop = $this->getOrBuildShop();
        $pageConfig = $shop->page_config ?? [];

        $hasServiceProfile = (bool) $shop->exists;
        $workers = $hasServiceProfile
            ? User::where('shop_id', $shop->id)->latest()->take(5)->get()
            : collect();

        $offersQuery = Update::offers()->latest();
        if ($hasServiceProfile && $shop->city_id) {
            $offersQuery->where('city_id', $shop->city_id);
        }
        $offers = $offersQuery->take(5)->get();

        $workersCount = $hasServiceProfile ? User::where('shop_id', $shop->id)->count() : 0;
        $offersCount  = (clone $offersQuery)->count();

        $requestsBaseQuery = $hasServiceProfile
            ? ClientRequest::query()->where('shop_id', $shop->id)
            : ClientRequest::query()->whereRaw('1 = 0');
        $requestsCount = (clone $requestsBaseQuery)->count();
        $requestsPendingCount = (clone $requestsBaseQuery)
            ->whereIn('status', ['new', 'pending', 'callback'])
            ->count();
        $recentClientRequests = (clone $requestsBaseQuery)->latest('id')->take(5)->get();

        // Services offered (configured in Configure page)
        $services = $pageConfig['services'] ?? [];

        // Service cities
        $serviceCityIds = $pageConfig['service_cities'] ?? [];
        $serviceCities  = $serviceCityIds
            ? \App\Models\City::whereIn('id', $serviceCityIds)->pluck('name')
            : collect();

        return view('myservice.index', compact(
            'shop',
            'pageConfig',
            'hasServiceProfile',
            'workers',
            'offers',
            'workersCount',
            'offersCount',
            'services',
            'serviceCities',
            'requestsCount',
            'requestsPendingCount',
            'recentClientRequests'
        ));
    }

    public function clientRequests(Request $request)
    {
        $shop = $this->getOrBuildShop();
        if (!$shop->exists) {
            return redirect()->route('myservice')->with('alert', 'Create your service profile first.');
        }

        $query = ClientRequest::query()->where('shop_id', $shop->id);

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('service_name', 'like', '%' . $search . '%')
                    ->orWhere('source', 'like', '%' . $search . '%');
            });
        }

        $statusFilter = strtolower(trim((string) $request->query('status', '')));
        if ($statusFilter !== '' && in_array($statusFilter, self::REQUEST_STATUSES, true)) {
            $query->where('status', $statusFilter);
        }

        $templateFilter = trim((string) $request->query('template', ''));
        if ($templateFilter !== '') {
            $query->where('template_key', $templateFilter);
        }

        $sourceFilter = trim((string) $request->query('source', ''));
        if ($sourceFilter !== '') {
            $query->where('source', $sourceFilter);
        }

        $requests = $query->latest('id')->paginate(20)->withQueryString();

        $statsBase = ClientRequest::query()->where('shop_id', $shop->id);
        $stats = [
            'total' => (clone $statsBase)->count(),
            'new' => (clone $statsBase)->where('status', 'new')->count(),
            'pending' => (clone $statsBase)->where('status', 'pending')->count(),
            'callback' => (clone $statsBase)->where('status', 'callback')->count(),
            'completed' => (clone $statsBase)->where('status', 'completed')->count(),
        ];

        $templates = ClientRequest::query()->where('shop_id', $shop->id)
            ->select('template_key')
            ->distinct()
            ->orderBy('template_key')
            ->pluck('template_key');

        $sources = ClientRequest::query()->where('shop_id', $shop->id)
            ->select('source')
            ->whereNotNull('source')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        return view('myservice.requests', compact(
            'shop',
            'requests',
            'stats',
            'templates',
            'sources',
            'search',
            'statusFilter',
            'templateFilter',
            'sourceFilter'
        ));
    }

    public function updateClientRequest(Request $request, int $id)
    {
        $shop = $this->getOrBuildShop();
        $clientRequest = ClientRequest::query()
            ->where('shop_id', $shop->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|string|in:new,confirmed,pending,completed,cancelled,callback,called',
            'admin_notes' => 'nullable|string|max:3000',
        ]);

        $clientRequest->status = $validated['status'];
        $clientRequest->admin_notes = trim((string) ($validated['admin_notes'] ?? ''));

        if (in_array($validated['status'], ['called', 'confirmed'], true) && !$clientRequest->contacted_at) {
            $clientRequest->contacted_at = now();
        }
        if (in_array($validated['status'], ['completed', 'cancelled'], true) && !$clientRequest->resolved_at) {
            $clientRequest->resolved_at = now();
        }

        $clientRequest->save();

        return back()->with('notice', 'Client request updated.');
    }

    public function configure()
    {
        $shop       = $this->getOrBuildShop();
        $pageConfig = $shop->page_config ?? [];
        $cities     = \App\Models\City::orderBy('name')->get();

        if (empty($shop->template) || !in_array($shop->template, self::AVAILABLE_TEMPLATES, true)) {
            $shop->template = 'dynamic_service';
        }

        // Pull saved service cities (array of city IDs) and services offered from page_config
        $serviceCities = $pageConfig['service_cities'] ?? [];
        $services      = $pageConfig['services']       ?? [];
        $pfFields      = $pageConfig['fields']         ?? [];
        $templateContent = $pageConfig['template_content'] ?? [];

        $hasActiveSubscription = Subscription::query()
            ->where('user_id', auth()->id())
            ->where('shop_id', $shop->id)
            ->where('plan_key', 'yearly_base')
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->exists();

        $astroUnlocked = TemplateUnlockRequest::query()
            ->where('shop_id', $shop->id)
            ->where('template_key', 'astro_dynamic')
            ->where('status', 'approved')
            ->exists();

        $latestAstroUnlockRequest = $shop->exists
            ? TemplateUnlockRequest::query()
                ->where('shop_id', $shop->id)
                ->where('template_key', 'astro_dynamic')
                ->latest('id')
                ->first()
            : null;
        $hasPendingAstroUnlockRequest = (bool) ($latestAstroUnlockRequest && $latestAstroUnlockRequest->status === 'pending');

        $astroUnlockPrice = (float) AdminSetting::getValue('astro_dynamic_template_price', '499');
        $yearlyBasePrice = (float) AdminSetting::getValue('yearly_base_plan_price', '999');
        $unlockSlaHours = (int) AdminSetting::getValue('template_unlock_sla_hours', '24');
        $paymentQrPath = (string) AdminSetting::getValue('payment_qr_image_path', '');
        $paymentBarcodePath = (string) AdminSetting::getValue('payment_barcode_image_path', '');
        $paymentQrUrl = $paymentQrPath !== '' ? Storage::url($paymentQrPath) : '';
        $paymentBarcodeUrl = $paymentBarcodePath !== '' ? Storage::url($paymentBarcodePath) : '';

        return view('myservice.configure', compact(
            'shop',
            'pageConfig',
            'cities',
            'serviceCities',
            'services',
            'pfFields',
            'templateContent',
            'hasActiveSubscription',
            'astroUnlocked',
            'latestAstroUnlockRequest',
            'hasPendingAstroUnlockRequest',
            'astroUnlockPrice',
            'yearlyBasePrice',
            'unlockSlaHours',
            'paymentQrUrl',
            'paymentBarcodeUrl'
        ));
    }

    public function unlockPage()
    {
        $shop  = $this->getOrBuildShop();
        $hasActiveSubscription = Subscription::query()
            ->where('user_id', auth()->id())
            ->where('shop_id', $shop->id)
            ->where('plan_key', 'yearly_base')
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->exists();

        $astroUnlocked = TemplateUnlockRequest::query()
            ->where('shop_id', $shop->id)
            ->where('template_key', 'astro_dynamic')
            ->where('status', 'approved')
            ->exists();

        $latestAstroUnlockRequest = $shop->exists
            ? TemplateUnlockRequest::query()
                ->where('shop_id', $shop->id)
                ->where('template_key', 'astro_dynamic')
                ->latest('id')
                ->first()
            : null;

        $hasPendingAstroUnlockRequest = (bool) ($latestAstroUnlockRequest && $latestAstroUnlockRequest->status === 'pending');
        $isRejected = (bool) ($latestAstroUnlockRequest && $latestAstroUnlockRequest->status === 'rejected');

        $astroUnlockPrice  = (float) AdminSetting::getValue('astro_dynamic_template_price', '499');
        $yearlyBasePrice   = (float) AdminSetting::getValue('yearly_base_plan_price', '999');
        $unlockSlaHours    = (int)   AdminSetting::getValue('template_unlock_sla_hours', '24');
        $paymentQrPath     = (string) AdminSetting::getValue('payment_qr_image_path', '');
        $paymentBarcodePath= (string) AdminSetting::getValue('payment_barcode_image_path', '');
        $paymentQrUrl      = $paymentQrPath !== '' ? Storage::url($paymentQrPath) : '';
        $paymentBarcodeUrl = $paymentBarcodePath !== '' ? Storage::url($paymentBarcodePath) : '';

        return view('myservice.unlock', compact(
            'shop',
            'hasActiveSubscription',
            'astroUnlocked',
            'latestAstroUnlockRequest',
            'hasPendingAstroUnlockRequest',
            'isRejected',
            'astroUnlockPrice',
            'yearlyBasePrice',
            'unlockSlaHours',
            'paymentQrUrl',
            'paymentBarcodeUrl'
        ));
    }

    public function configureSave(Request $request)
    {
        $shop = $this->getOrBuildShop();
        $owner = auth()->user();

        $hasActiveSubscription = Subscription::query()
            ->where('user_id', auth()->id())
            ->where('shop_id', $shop->id)
            ->where('plan_key', 'yearly_base')
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->exists();

        if (!$hasActiveSubscription) {
            return redirect()->route('subscriptions.new')->with('alert', 'Activate your yearly base subscription first, then save configuration.');
        }

        $normalizedPublicSlug = Str::slug((string) $request->input('public_slug', ''));
        $request->merge(['public_slug' => $normalizedPublicSlug]);

        $request->validate([
            'public_slug' => 'nullable|string|min:3|max:80|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
        ]);

        if ($normalizedPublicSlug !== '') {
            if (in_array($normalizedPublicSlug, self::RESERVED_PUBLIC_SLUGS, true)) {
                return back()->withErrors(['public_slug' => 'This page URL is reserved. Please choose another one.'])->withInput();
            }

            $slugTaken = Shop::query()
                ->whereKeyNot($shop->id)
                ->get()
                ->contains(fn (Shop $candidate) => $candidate->public_page_slug === $normalizedPublicSlug);

            if ($slugTaken) {
                return back()->withErrors(['public_slug' => 'This page URL is already taken. Please choose a unique one.'])->withInput();
            }
        }

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

        $shop->fill($request->only(['name', 'description', 'phone', 'address']));
        $shop->template = $selectedTemplate;

        // Preserve existing page_config and merge in new values
        $cfg = $shop->page_config ?? [];

        // Service cities — array of city IDs (checkboxes)
        $cfg['service_cities'] = array_values(array_map('intval', $request->input('service_cities', [])));
        $cfg['public_slug'] = $normalizedPublicSlug;

        // Services offered — each: name, description, url, icon, price
        $rawSvcs = $request->input('services', []);
        $cfg['services'] = array_values(array_filter(array_map(function($s){
            $name = trim($s['name'] ?? '');
            return $name ? [
                'name'        => $name,
                'description' => trim($s['description'] ?? ''),
                'url'         => trim($s['url']         ?? ''),
                'icon'        => trim($s['icon']        ?? '🛠️'),
                'price'       => trim($s['price']       ?? ''),
            ] : null;
        }, $rawSvcs)));

        $existingTc = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];

        // Dynamic template content
        $cfg['template_content'] = [
            'hero_badge'        => trim((string) $request->input('tc.hero_badge', 'विश्वासार्ह स्थानिक सेवा')),
            'hero_title'        => trim((string) $request->input('tc.hero_title', 'तुमच्या गरजांसाठी व्यावसायिक सेवा')),
            'hero_description'  => trim((string) $request->input('tc.hero_description', 'अनुभवी तज्ञांकडून जलद, विश्वासार्ह आणि परवडणारी सेवा.')),
            'primary_cta'       => trim((string) $request->input('tc.primary_cta', 'आत्ता बुक करा')),
            'secondary_cta'     => trim((string) $request->input('tc.secondary_cta', 'मोफत कोट घ्या')),
            'services_label'    => trim((string) $request->input('tc.services_label', 'आमच्या सेवा')),
            'services_title'    => trim((string) $request->input('tc.services_title', 'आम्ही देत असलेल्या सेवा')),
            'services_subtitle' => trim((string) $request->input('tc.services_subtitle', 'आमच्या लोकप्रिय सेवांमधून निवडा.')),
            'why_title'         => trim((string) $request->input('tc.why_title', 'आम्हालाच का निवडाल?')),
            'why_subtitle'      => trim((string) $request->input('tc.why_subtitle', 'दर्जेदार काम, पारदर्शक किंमत आणि जलद प्रतिसाद.')),
            'cta_title'         => trim((string) $request->input('tc.cta_title', 'आज मदत हवी आहे?')),
            'cta_description'   => trim((string) $request->input('tc.cta_description', 'आत्ताच संपर्क करा आणि त्वरित मदत मिळवा.')),
            'cta_button'        => trim((string) $request->input('tc.cta_button', 'आत्ता संपर्क करा')),
            'footer_brand'      => trim((string) $request->input('tc.footer_brand', $shop->name ?: 'माझी सेवा')),
            'footer_tagline'    => trim((string) $request->input('tc.footer_tagline', 'विश्वासार्ह · जलद · व्यावसायिक')),
            'provider_name'     => trim((string) ($existingTc['provider_name'] ?? ($owner->full_name ?: 'सेवा प्रदाता'))),
            'provider_age'      => trim((string) ($existingTc['provider_age'] ?? '')),
            'provider_email'    => trim((string) ($existingTc['provider_email'] ?? ($owner->email ?: ''))),
            'provider_contact'  => trim((string) ($existingTc['provider_contact'] ?? ($shop->phone ?: ($owner->mobile_number ?? '')))),
            'provider_photo'    => trim((string) ($existingTc['provider_photo'] ?? '')),
            'provider_title'    => trim((string) ($existingTc['provider_title'] ?? 'संस्थापक आणि प्रमुख सेवा तज्ञ')),
            'provider_bio'      => trim((string) ($existingTc['provider_bio'] ?? 'अनुभवी स्थानिक तज्ञ, विश्वासार्ह आणि ग्राहकाभिमुख सेवेसाठी समर्पित.')),
            'provider_experience' => trim((string) ($existingTc['provider_experience'] ?? '५+ वर्षांचा अनुभव')),
        ];

            // Save service_groups for astro_dynamic template
            $rawGroups = $request->input('service_groups', []);
            if (is_array($rawGroups)) {
                $cfg['template_content']['service_groups'] = array_values(array_filter(
                    array_map(function ($g) {
                        if (empty(trim((string) ($g['title'] ?? '')))) return null;
                        $items = array_values(array_filter(array_map(function ($i) {
                            return empty(trim((string) ($i['name'] ?? ''))) ? null : [
                                'icon'        => trim((string) ($i['icon']        ?? '✨')),
                                'name'        => trim((string) ($i['name']        ?? '')),
                                'description' => trim((string) ($i['description'] ?? '')),
                                'price'       => trim((string) ($i['price']       ?? '')),
                            ];
                        }, is_array($g['items'] ?? null) ? $g['items'] : [])));
                        return [
                            'eyebrow'  => trim((string) ($g['eyebrow']  ?? '')),
                            'title'    => trim((string) ($g['title']    ?? '')),
                            'subtitle' => trim((string) ($g['subtitle'] ?? '')),
                            'items'    => $items,
                        ];
                    }, $rawGroups)
                ));
            }

        // Custom page fields (inline builder, no longer via shared page_config_form)
        $rawPf = $request->input('pf', []);
        $cfg['fields'] = array_values(array_filter(array_map(function($f){
            $title = trim($f['title'] ?? '');
            return $title ? [
                'title' => $title,
                'value' => trim($f['value'] ?? ''),
                'bold'  => isset($f['bold']),
                'align' => in_array($f['align'] ?? 'left', ['left','center','right']) ? $f['align'] : 'left',
            ] : null;
        }, $rawPf)));

        $shop->page_config = $cfg;

        if ($shop->save()) {
            return redirect()->route('myservice')->with('notice', 'Service page saved.');
        }
        return back()->with('alert', 'Unable to save page configuration');
    }

    public function workers()
    {
        $shop = auth()->user()->shops()->first();
        $workers = $shop ? User::where('shop_id', $shop->id)->get() : collect();
        return view('myservice.workers', compact('shop', 'workers'));
    }

    public function createWorker(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) {
            return redirect()->route('myservice')->with('alert', 'No service profile found');
        }

        $attrs = $request->validate([
            'worker.first_name'    => 'required|string|max:100',
            'worker.last_name'     => 'nullable|string|max:100',
            'worker.email'         => 'required|email|unique:users,email',
            'worker.mobile_number' => 'nullable|string|max:20',
        ]);
        $worker = $attrs['worker'];
        $password = Str::random(10);

        $user = new User([
            'first_name'    => $worker['first_name'],
            'last_name'     => $worker['last_name'] ?? '',
            'email'         => $worker['email'],
            'mobile_number' => $worker['mobile_number'] ?? '',
            'password'      => Hash::make($password),
            'role'          => 'shopworker',
            'shop_id'       => $shop->id,
        ]);

        if ($user->save()) {
            $user->sendPasswordResetNotification(app('auth.password.broker')->createToken($user));
            return redirect()->route('workers_myservice')->with('notice', 'Worker created — email sent to set password.');
        }

        return back()->with('alert', 'Unable to create worker')->withInput();
    }

    public function updateWorker(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) {
            return redirect()->route('myservice')->with('alert', 'No service profile found');
        }

        $workerId = $request->input('worker.id');
        $worker = User::where('id', $workerId)->where('shop_id', $shop->id)->first();
        if (!$worker) {
            return redirect()->route('workers_myservice')->with('alert', 'Worker not found');
        }

        $worker->fill($request->only(['worker.experience', 'worker.tags'])['worker'] ?? []);
        if ($worker->save()) {
            return redirect()->route('workers_myservice')->with('notice', 'Worker updated.');
        }

        return back()->with('alert', 'Unable to update worker');
    }

    public function workerExperience(Request $request, $id)
    {
        $shop = auth()->user()->shops()->first();
        $worker = User::where('id', $id)->where('shop_id', $shop?->id)->firstOrFail();
        return view('myservice.worker_experience', compact('shop', 'worker'));
    }

    public function offerNew()
    {
        $shop = auth()->user()->shops()->first();
        $offer = new Update();
        return view('myservice.offer_new', compact('shop', 'offer'));
    }

    public function offerCreate(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'photo'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $validated['update_type'] = 'offer';
        if ($shop?->city_id) {
            $validated['city_id'] = $shop->city_id;
        }
        if ($shop?->id) {
            $validated['shop_id'] = $shop->id;
        }

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('offer-photos', 'public');
        }

        Update::create($validated);
        return redirect()->route('myservice')->with('notice', 'Offer added.');
    }

    public function experience()
    {
        $shop = auth()->user()->shops()->first();
        return view('myservice.experience', compact('shop'));
    }

    public function idcard()
    {
        $shop = auth()->user()->shops()->first();
        return view('myservice.idcard', compact('shop'));
    }

    private function getOrBuildShop(): Shop
    {
        return auth()->user()->shops()->first()
            ?? auth()->user()->shops()->make(['name' => 'My Service']);
    }
}
