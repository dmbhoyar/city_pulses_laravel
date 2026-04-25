<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user->isShopowner() && !$user->isServiceProvider() && !$user->isSuperadmin()) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function edit()
    {
        $user    = auth()->user();
        $profile = BusinessProfile::forUser($user->id);
        $shop    = $user->shops()->first() ?? $user->shop;
        return view('invoices.settings', compact('profile', 'shop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name'  => 'nullable|string|max:150',
            'business_phone' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:150',
            'business_address' => 'nullable|string|max:500',
            'gstin'          => 'nullable|string|max:25',
            'bank_name'      => 'nullable|string|max:100',
            'bank_account'   => 'nullable|string|max:50',
            'bank_ifsc'      => 'nullable|string|max:20',
            'bank_holder'    => 'nullable|string|max:100',
            'default_terms'  => 'nullable|string|max:2000',
            'default_notes'  => 'nullable|string|max:1000',
            'business_logo'  => 'nullable|image|max:2048',
            'payment_qr'     => 'nullable|image|max:2048',
            'signature'      => 'nullable|image|max:2048',
        ]);

        $user    = auth()->user();
        $profile = BusinessProfile::forUser($user->id);

        $fields = $request->only([
            'business_name', 'business_phone', 'business_email', 'business_address',
            'gstin', 'bank_name', 'bank_account', 'bank_ifsc', 'bank_holder',
            'default_terms', 'default_notes',
        ]);

        // Handle logo upload
        if ($request->hasFile('business_logo')) {
            if ($profile->business_logo) {
                Storage::disk('public')->delete($profile->business_logo);
            }
            $fields['business_logo'] = $request->file('business_logo')->store('business/logos', 'public');
        } elseif ($request->boolean('remove_logo') && $profile->business_logo) {
            Storage::disk('public')->delete($profile->business_logo);
            $fields['business_logo'] = null;
        }

        // Handle QR upload
        if ($request->hasFile('payment_qr')) {
            if ($profile->payment_qr) {
                Storage::disk('public')->delete($profile->payment_qr);
            }
            $fields['payment_qr'] = $request->file('payment_qr')->store('business/qr', 'public');
        } elseif ($request->boolean('remove_qr') && $profile->payment_qr) {
            Storage::disk('public')->delete($profile->payment_qr);
            $fields['payment_qr'] = null;
        }

        // Handle signature upload
        if ($request->hasFile('signature')) {
            if ($profile->signature) {
                Storage::disk('public')->delete($profile->signature);
            }
            $fields['signature'] = $request->file('signature')->store('business/signatures', 'public');
        } elseif ($request->boolean('remove_signature') && $profile->signature) {
            Storage::disk('public')->delete($profile->signature);
            $fields['signature'] = null;
        }

        $profile->update($fields);

        return back()->with('success', 'Invoice settings saved successfully!');
    }
}
