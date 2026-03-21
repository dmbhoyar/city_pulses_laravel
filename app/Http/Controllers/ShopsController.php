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
        $this->middleware('auth')->except(['index', 'show', 'publicShow', 'publicIdcard', 'publicShowBySlug', 'publicIdcardBySlug']);
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
        $astroUnlocked = TemplateUnlockRequest::query()
            ->where('shop_id', $shop->id)
            ->where('template_key', 'astro_dynamic')
            ->where('status', 'approved')
            ->exists();

        return match ((string) $shop->template) {
            'astro_dynamic' => $astroUnlocked ? 'shops.templates.astro_dynamic' : 'shops.show',
            default => 'shops.show',
        };
    }
}
