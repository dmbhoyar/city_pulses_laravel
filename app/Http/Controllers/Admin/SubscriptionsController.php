<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Subscription;
use App\Models\TemplateUnlockRequest;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $subscriptions = Subscription::with(['user', 'shop', 'reviewer'])->latest('id')->paginate(30);
        $unlockRequests = TemplateUnlockRequest::with(['user', 'shop', 'reviewer'])->latest('id')->paginate(30);
        $astroUnlockPrice = (float) AdminSetting::getValue('astro_dynamic_template_price', '499');
        $unlockSlaHours = (int) AdminSetting::getValue('template_unlock_sla_hours', '24');

        return view('admin.subscriptions.index', compact('subscriptions', 'unlockRequests', 'astroUnlockPrice', 'unlockSlaHours'));
    }

    public function updateSubscriptionStatus(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,active,cancelled,failed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $payload = [
            'status' => $validated['status'],
            'admin_notes' => trim((string) ($validated['admin_notes'] ?? '')),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ];

        if ($validated['status'] === 'active') {
            $payload['starts_at'] = $subscription->starts_at ?: now();
            $payload['expires_at'] = now()->addYear();
        }

        $subscription->update($payload);

        if ($validated['status'] === 'active' && $subscription->user) {
            $subscription->user->update([
                'subscription_expires_at' => $subscription->expires_at,
            ]);
        }

        return back()->with('notice', 'Subscription status updated.');
    }

    public function updateUnlockStatus(Request $request, TemplateUnlockRequest $unlockRequest)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $unlockRequest->update([
            'status' => $validated['status'],
            'admin_notes' => trim((string) ($validated['admin_notes'] ?? '')),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('notice', 'Template unlock request updated.');
    }
}
