<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\City;
use App\Models\Listing;
use App\Services\TextTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RentsController extends Controller
{
    private const LISTING_FEE = 10;

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $selectedCityId = null;
        if ($request->filled('city_id')) {
            $selectedCityId = (int) $request->input('city_id');
            $request->session()->put('city_id', $selectedCityId);
        } elseif ($request->session()->has('city_id')) {
            $selectedCityId = (int) $request->session()->get('city_id');
        }

        $search = trim((string) $request->input('q', ''));
        $subcategory = trim((string) $request->input('subcategory', ''));

        $query = Listing::query()
            ->where('category', 'rent')
            ->where('status', 'active');

        if ($selectedCityId) {
            $query->where('city_id', $selectedCityId);
        }

        if ($subcategory !== '') {
            $query->where('subcategory', $subcategory);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        $listings = $query->with(['city', 'user'])->orderByDesc('created_at')->paginate(24)->withQueryString();

        $cities = City::query()->orderBy('name')->get(['id', 'name']);

        $totalActive = Listing::query()->where('category', 'rent')->where('status', 'active')->count();
        $cityActive = $selectedCityId
            ? Listing::query()->where('category', 'rent')->where('status', 'active')->where('city_id', $selectedCityId)->count()
            : $totalActive;

        $selectedCityName = optional($cities->firstWhere('id', $selectedCityId))->name;

        $locale = app()->getLocale();
        if ($locale !== 'en') {
            $listings->getCollection()->transform(function ($item) use ($locale) {
                if (is_string($item->title) && trim($item->title) !== '') {
                    $item->title = TextTranslationService::translate($item->title, $locale);
                }

                if (is_string($item->description) && trim($item->description) !== '') {
                    $item->description = TextTranslationService::translate($item->description, $locale);
                }

                if (is_string($item->location) && trim($item->location) !== '') {
                    $item->location = TextTranslationService::translate($item->location, $locale);
                }

                if (is_string($item->subcategory) && trim($item->subcategory) !== '') {
                    $item->subcategory = TextTranslationService::translate($item->subcategory, $locale);
                }

                return $item;
            });
        }

        return view('rents.index', compact('listings', 'cities', 'selectedCityId', 'selectedCityName', 'search', 'subcategory', 'totalActive', 'cityActive'));
    }

    public function show(Listing $listing)
    {
        if ($listing->category !== 'rent') {
            abort(404);
        }

        if ($listing->status !== 'active') {
            $viewer = auth()->user();
            $isOwner = $viewer && (int) $listing->user_id === (int) $viewer->id;
            $isAdmin = $viewer && method_exists($viewer, 'isSuperadmin') && $viewer->isSuperadmin();

            if (!$isOwner && !$isAdmin) {
                abort(404);
            }
        }

        return view('rents.show', ['rent' => $listing]);
    }

    public function create()
    {
        $this->ensureSellerRole();

        $listing = new Listing(['category' => 'rent']);
        $cities = City::query()->orderBy('name')->get(['id', 'name']);
        $listingFee = self::LISTING_FEE;

        $paymentQrPath = (string) AdminSetting::getValue('payment_qr_image_path', '');
        $paymentBarcodePath = (string) AdminSetting::getValue('payment_barcode_image_path', '');
        $paymentQrUrl = $paymentQrPath !== '' ? Storage::url($paymentQrPath) : '';
        $paymentBarcodeUrl = $paymentBarcodePath !== '' ? Storage::url($paymentBarcodePath) : '';

        return view('rents.create', compact('listing', 'cities', 'listingFee', 'paymentQrUrl', 'paymentBarcodeUrl'));
    }

    public function store(Request $request)
    {
        $this->ensureSellerRole();

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'subcategory'    => 'required|string|max:100',
            'price'          => 'nullable|numeric|min:0',
            'contact_number' => 'nullable|string|max:20',
            'city_id'        => 'nullable|exists:cities,id',
            'location'       => 'nullable|string|max:255',
            'photos'         => 'nullable|array|max:8',
            'photos.*'       => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'payment_transaction_id' => 'nullable|string|max:120',
            'payment_screenshot' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $transactionId = trim((string) ($validated['payment_transaction_id'] ?? ''));
        $hasPaymentScreenshot = $request->hasFile('payment_screenshot');

        if ($transactionId === '' && !$hasPaymentScreenshot) {
            return back()->withErrors([
                'payment_transaction_id' => 'Provide transaction ID or upload payment screenshot.',
            ])->withInput();
        }

        $photoPaths = [];
        foreach ((array) $request->file('photos', []) as $photo) {
            $photoPaths[] = $photo->store('listing_photos', 'public');
        }

        $paymentScreenshotPath = null;
        if ($hasPaymentScreenshot) {
            $paymentScreenshotPath = $request->file('payment_screenshot')->store('listing_payments', 'public');
        }

        $validated['category'] = 'rent';
        $validated['status'] = 'pending';
        $validated['user_id'] = auth()->id();
        $validated['photos'] = $photoPaths;
        $validated['payment_transaction_id'] = $transactionId !== '' ? $transactionId : null;
        $validated['payment_screenshot_path'] = $paymentScreenshotPath;
        unset($validated['payment_screenshot']);

        Listing::create($validated);

        return redirect()->route('rents.index')->with('notice', 'Rental listing request submitted. Payment proof received and status is in review until superadmin approval.');
    }

    public function edit(Listing $listing)
    {
        $this->authorizeOwner($listing);

        $cities = City::query()->orderBy('name')->get(['id', 'name']);

        return view('rents.edit', compact('listing', 'cities'));
    }

    public function update(Request $request, Listing $listing)
    {
        $this->authorizeOwner($listing);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'subcategory'    => 'required|string|max:100',
            'price'          => 'nullable|numeric|min:0',
            'contact_number' => 'nullable|string|max:20',
            'city_id'        => 'nullable|exists:cities,id',
            'location'       => 'nullable|string|max:255',
            'photos'         => 'nullable|array|max:8',
            'photos.*'       => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $existingPhotos = is_array($listing->photos) ? $listing->photos : [];
        $newPhotos = [];
        foreach ((array) $request->file('photos', []) as $photo) {
            $newPhotos[] = $photo->store('listing_photos', 'public');
        }

        if ($newPhotos !== []) {
            $validated['photos'] = array_values(array_unique(array_merge($existingPhotos, $newPhotos)));
        }

        $listing->update($validated);
        return redirect()->route('rents.show', $listing)->with('notice', 'Rental listing updated.');
    }

    public function destroy(Listing $listing)
    {
        $this->authorizeOwner($listing);
        $listing->update(['status' => 'removed']);
        return redirect()->route('rents.index')->with('notice', 'Rental listing removed.');
    }

    private function ensureSellerRole(): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->isSuperadmin()) {
            return;
        }

        if (!method_exists($user, 'isSeller') || !$user->isSeller()) {
            abort(403, 'Only seller role can post rental listings.');
        }
    }

    private function authorizeOwner(Listing $listing): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($listing->category !== 'rent') {
            abort(404);
        }

        if ((int) $listing->user_id === (int) $user->id || $user->isSuperadmin()) {
            return;
        }

        abort(403, 'Not authorized');
    }
}

