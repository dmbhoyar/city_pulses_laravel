<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function create(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $event     = null;

        if (env('STRIPE_ENDPOINT_SECRET')) {
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, env('STRIPE_ENDPOINT_SECRET'));
            } catch (\UnexpectedValueException $e) {
                return response('Invalid payload', 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
                return response('Invalid signature', 400);
            }
        } elseif (env('WEBHOOK_ALLOW_DEMO') == '1' ||
            (env('WEBHOOK_DEMO_SECRET') && $request->header('X-DEMO-SIGNATURE') === env('WEBHOOK_DEMO_SECRET'))) {
            try {
                $event = json_decode($payload, true);
            } catch (\Exception $e) {
                return response('Invalid payload', 400);
            }
        } else {
            return response('Webhooks not enabled', 403);
        }

        $eventType  = is_array($event) ? ($event['type'] ?? null) : ($event->type ?? null);
        $dataObject = is_array($event) ? ($event['data']['object'] ?? []) : ($event->data->object ?? null);

        switch ($eventType) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($dataObject);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($dataObject);
                break;
            default:
                Log::info('Unhandled stripe webhook event: ' . $eventType);
        }

        return response('ok');
    }

    private function handleCheckoutCompleted($obj): void
    {
        $metadata  = is_array($obj) ? ($obj['metadata'] ?? []) : (object_get($obj, 'metadata') ?? []);
        $subId     = is_array($metadata) ? ($metadata['subscription_id'] ?? null) : ($metadata->subscription_id ?? null);
        $sessionId = is_array($obj) ? ($obj['id'] ?? null) : ($obj->id ?? null);

        if ($subId) {
            $sub = Subscription::find($subId);
            if ($sub) {
                $sub->update([
                    'status'     => 'active',
                    'provider_id'=> $sessionId,
                    'starts_at'  => now(),
                    'expires_at' => now()->addYear(),
                ]);
                $sub->user?->update(['subscription_expires_at' => $sub->expires_at]);
                Log::info("Subscription {$sub->id} activated via webhook");
            } else {
                Log::warning("Subscription id {$subId} not found for webhook session");
            }
        } else {
            Log::warning('No subscription_id in webhook metadata');
        }
    }

    private function handlePaymentFailed($obj): void
    {
        $metadata = is_array($obj) ? ($obj['metadata'] ?? []) : (object_get($obj, 'metadata') ?? []);
        $subId    = is_array($metadata) ? ($metadata['subscription_id'] ?? null) : ($metadata->subscription_id ?? null);
        if ($subId) {
            Subscription::find($subId)?->update(['status' => 'past_due']);
        }
    }
}
