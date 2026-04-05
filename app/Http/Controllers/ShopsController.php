<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ClientRequest;
use App\Models\TemplateUnlockRequest;
use Illuminate\Http\Request;

class ShopsController extends Controller
{
    private const REQUEST_STATUSES = ['new', 'confirmed', 'pending', 'completed', 'cancelled', 'callback', 'called'];

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'publicShow', 'publicIdcard', 'publicShowBySlug', 'publicIdcardBySlug', 'templateDemo']);
    }

    public function index()
    {
        $shops = Shop::orderByDesc('created_at')->paginate(20);
        return view('shops.index', compact('shops'));
    }

    public function show(Shop $shop)
    {
        return redirect()->route('shops.public', ['publicSlug' => $shop->public_page_slug]);
    }

    public function publicShow(string $service, string $provider)
    {
        $shop = $this->resolvePublicShop($service, $provider);

        return redirect()->route('shops.public', ['publicSlug' => $shop->public_page_slug]);
    }

    public function publicShowBySlug(string $publicSlug)
    {
        $shop = $this->resolvePublicShopBySlug($publicSlug);

        return view($this->resolvePublicTemplateView($shop), compact('shop'));
    }

    public function storeClientRequest(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|integer|exists:shops,id',
            'source' => 'nullable|string|max:80',
            'template_key' => 'nullable|string|max:80',
            'customer_name' => 'nullable|string|max:120',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:120',
            'dob' => 'nullable|date',
            'service_name' => 'nullable|string|max:160',
            'message' => 'nullable|string|max:3000',
            'status' => 'nullable|string|max:20',
        ]);

        $shop = Shop::findOrFail((int) $validated['shop_id']);

        $incomingStatus = strtolower(trim((string) ($validated['status'] ?? '')));
        $source = trim((string) ($validated['source'] ?? 'public_form'));
        if ($incomingStatus === '') {
            $incomingStatus = str_contains(strtolower($source), 'callback') ? 'callback' : 'new';
        }
        $status = in_array($incomingStatus, self::REQUEST_STATUSES, true) ? $incomingStatus : 'new';

        $templateKey = trim((string) ($validated['template_key'] ?? $shop->template ?? 'dynamic_service'));

        $clientRequest = ClientRequest::create([
            'shop_id' => $shop->id,
            'template_key' => $templateKey !== '' ? $templateKey : 'dynamic_service',
            'source' => $source,
            'customer_name' => trim((string) ($validated['customer_name'] ?? '')),
            'phone' => trim((string) ($validated['phone'] ?? '')),
            'email' => trim((string) ($validated['email'] ?? '')),
            'dob' => $validated['dob'] ?? null,
            'service_name' => trim((string) ($validated['service_name'] ?? '')),
            'message' => trim((string) ($validated['message'] ?? '')),
            'status' => $status,
            'meta' => [
                'ip' => $request->ip(),
                'ua' => substr((string) $request->userAgent(), 0, 500),
                'referer' => (string) $request->headers->get('referer', ''),
            ],
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $clientRequest->id,
                'message' => 'Request submitted successfully',
            ]);
        }

        return back()->with('notice', 'Your request has been submitted successfully.');
    }

    public function publicIdcard(string $service, string $provider)
    {
        $shop = $this->resolvePublicShop($service, $provider);

        return redirect()->route('shops.idcard.public', ['publicSlug' => $shop->public_page_slug]);
    }

    public function publicIdcardBySlug(string $publicSlug)
    {
        $shop = $this->resolvePublicShopBySlug($publicSlug);

        return view('myservice.idcard', compact('shop'));
    }

    public function create()
    {
        $shop = new Shop();
        return view('shops.create', compact('shop'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:500',
            'city_id'     => 'nullable|exists:cities,id',
            'template'    => 'nullable|string|max:100',
        ]);
        $shop = auth()->user()->shops()->create($validated);
        return redirect()->route('shops.show', $shop)->with('notice', 'Shop created.');
    }

    public function edit(Shop $shop)
    {
        $this->authorizeOwner($shop);
        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $this->authorizeOwner($shop);
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:500',
            'city_id'     => 'nullable|exists:cities,id',
            'template'    => 'nullable|string|max:100',
        ]);
        $shop->update($validated);
        return redirect()->route('shops.show', $shop)->with('notice', 'Shop updated.');
    }

    public function destroy(Shop $shop)
    {
        $this->authorizeOwner($shop);
        $shop->delete();
        return redirect()->route('shops.index')->with('notice', 'Shop removed.');
    }

    private function authorizeOwner(Shop $shop): void
    {
        if ($shop->user_id !== auth()->id() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Not authorized');
        }
    }

    private function resolvePublicShop(string $service, string $provider): Shop
    {
        $shop = Shop::with('user')
            ->latest('id')
            ->get()
            ->first(function (Shop $candidate) use ($service, $provider) {
                return $candidate->public_service_slug === $service
                    && $candidate->public_provider_slug === $provider;
            });

        abort_if(!$shop, 404);

        return $shop;
    }

    private function resolvePublicShopBySlug(string $publicSlug): Shop
    {
        $slug = \Illuminate\Support\Str::slug($publicSlug);

        $shop = Shop::with('user')
            ->latest('id')
            ->get()
            ->first(fn (Shop $candidate) => $candidate->public_page_slug === $slug);

        abort_if(!$shop, 404);

        return $shop;
    }

    private function resolvePublicTemplateView(Shop $shop): string
    {
        $tpl = (string) $shop->template;

        if ($tpl === 'astro_dynamic') {
            $unlocked = TemplateUnlockRequest::query()
                ->where('shop_id', $shop->id)
                ->where('template_key', 'astro_dynamic')
                ->where('status', 'approved')
                ->exists();
            return $unlocked ? 'shops.templates.astro_dynamic' : 'shops.show';
        }

        return match ($tpl) {
            'metro_clean'   => 'shops.templates.metro_clean',
            'saffron_local' => 'shops.templates.saffron_local',
            default         => 'shops.show',
        };
    }

    public function templateDemo(string $template)
    {
        $allowed = ['dynamic_service', 'metro_clean', 'saffron_local', 'astro_dynamic'];
        if (!in_array($template, $allowed, true)) {
            abort(404);
        }

        // Build a realistic demo shop with sample data
        $shop = new Shop();
        $shop->id   = 0;
        $shop->name = 'Demo Business';
        $shop->description = 'Professional local service trusted by 500+ customers in your area.';
        $shop->phone = '+91 98765 43210';
        $shop->address = 'Main Road, Washim, MH';
        $shop->template = $template;

        $context = request()->query('ctx', 'service'); // shop | service

        $isShop = $context === 'shop';

        $shop->page_config = [
            'public_slug' => 'demo-page',
            'services' => [
                ['icon' => '🔧', 'name' => $isShop ? 'Premium Product A' : 'Plumbing Repair',    'description' => 'Fast and reliable, available 24/7.', 'price' => 'From ₹499'],
                ['icon' => '⚡', 'name' => $isShop ? 'Premium Product B' : 'Electrical Work',    'description' => 'Certified experts for safe installations.', 'price' => 'From ₹599'],
                ['icon' => '🏠', 'name' => $isShop ? 'Premium Product C' : 'Home Renovation',   'description' => 'Complete renovation with quality materials.', 'price' => 'From ₹2,999'],
                ['icon' => '✨', 'name' => $isShop ? 'Premium Product D' : 'Deep Cleaning',     'description' => 'Professional cleaning for home and office.', 'price' => 'From ₹799'],
            ],
            'template_content' => [
                'hero_badge'       => $isShop ? 'Trusted Local Shop · 500+ Customers' : 'Trusted Service Provider · 10+ Years',
                'hero_title'       => $isShop ? 'Your Premium Local Shop' : 'Professional Service For Every Need',
                'hero_description' => $isShop
                    ? 'Quality products at the best prices. Visit us or order online for fast delivery in your area.'
                    : 'Fast, reliable and affordable services by certified local experts. Book online or call now.',
                'primary_cta'      => $isShop ? 'Shop Now' : 'Book Service',
                'secondary_cta'    => $isShop ? 'View Products' : 'Get Free Quote',
                'services_label'   => $isShop ? 'Our Products' : 'Our Services',
                'services_title'   => $isShop ? 'What We Offer' : 'Services We Provide',
                'services_subtitle'=> $isShop ? 'Choose from our wide range of products.' : 'Pick from our most popular services.',
                'why_title'        => $isShop ? 'Why Shop With Us' : 'Why Choose Us',
                'why_subtitle'     => $isShop ? 'Quality, value and trusted service since 2015.' : 'Transparent pricing, expert team and fast response.',
                'cta_title'        => $isShop ? 'Visit Us Today' : 'Need Help Today?',
                'cta_description'  => $isShop ? 'Come in or call us for the best deals in town.' : 'Contact us now and get quick support from local experts.',
                'cta_button'       => $isShop ? 'Call Now' : 'Contact Now',
                'footer_brand'     => $isShop ? 'Demo Shop' : 'Demo Service',
                'footer_tagline'   => $isShop ? 'Quality · Value · Trusted' : 'Trusted · Fast · Professional',
                'provider_name'    => 'Dhananjay Bhoyar',
                'provider_title'   => $isShop ? 'Shop Owner' : 'Founder & Lead Expert',
                'provider_bio'     => 'Experienced local professional dedicated to quality service and customer satisfaction.',
                'provider_email'   => 'demo@example.com',
                'provider_contact' => '+91 98765 43210',
                'provider_experience' => '10+ Years',
                // Metro Clean
                'metro_accent'     => 'blue',
                'about_title'      => 'About Our Business',
                'about_text'       => 'We are a trusted local business serving the community since 2015. Our mission is to provide quality services at affordable prices.',
                'contact_heading'  => 'Get In Touch',
                // Saffron Local
                'opening_hours'    => 'Mon–Sat: 9 AM – 8 PM  |  Sun: 10 AM – 5 PM',
                'locality_note'    => 'Serving Washim, Mangrulpir, Karanja and nearby areas',
                'special_offer'    => '🎉 Grand Opening Offer: 20% off on all services this month!',
                // Astro specific stats
                'hero_stats' => [
                    ['value' => '500+', 'label' => 'Happy Customers'],
                    ['value' => '10+',  'label' => 'Years Experience'],
                    ['value' => '4.8★', 'label' => 'Average Rating'],
                    ['value' => '24/7', 'label' => 'Available'],
                ],
                'service_groups' => [
                    ['eyebrow' => '01 · Core Services', 'title' => 'Main Services', 'subtitle' => 'Our most popular offerings.', 'items' => [
                        ['icon' => '🔧', 'name' => 'Service A', 'description' => 'Quick and reliable.', 'price' => 'From ₹499'],
                        ['icon' => '⚡', 'name' => 'Service B', 'description' => 'Expert installation.', 'price' => 'From ₹699'],
                        ['icon' => '🏠', 'name' => 'Service C', 'description' => 'Complete solutions.', 'price' => 'From ₹999'],
                        ['icon' => '✨', 'name' => 'Service D', 'description' => 'Premium experience.', 'price' => 'From ₹1299'],
                    ]],
                    ['eyebrow' => '02 · Special Packages', 'title' => 'Package Deals', 'subtitle' => 'Bundle and save more.', 'items' => [
                        ['icon' => '💎', 'name' => 'Basic Pack', 'description' => 'Starter combo.', 'price' => '₹1,499'],
                        ['icon' => '🌟', 'name' => 'Pro Pack',   'description' => 'Best value combo.', 'price' => '₹2,999'],
                        ['icon' => '👑', 'name' => 'Elite Pack', 'description' => 'Complete package.', 'price' => '₹4,999'],
                    ]],
                ],
            ],
        ];

        $viewMap = [
            'astro_dynamic' => 'shops.templates.astro_dynamic',
            'metro_clean'   => 'shops.templates.metro_clean',
            'saffron_local' => 'shops.templates.saffron_local',
            'dynamic_service' => 'shops.show',
        ];

        return view($viewMap[$template], compact('shop'));
    }
}
