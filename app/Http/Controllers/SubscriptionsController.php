<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) return redirect()->route('myshop')->with('alert', 'Create a shop first.');
        $subscription = new Subscription(['user_id' => auth()->id(), 'shop_id' => $shop->id]);
        return view('subscriptions.new', compact('shop', 'subscription'));
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) return redirect()->route('myshop')->with('alert', 'Create a shop first.');

        $amount = (float)$request->input('amount', 0);
        if ($amount <= 0) $amount = 1000;

        $provider = env('STRIPE_SECRET_KEY') ? 'stripe' : 'local';
        $sub = Subscription::create([
            'user_id'    => auth()->id(),
            'shop_id'    => $shop->id,
            'provider'   => $provider,
            'status'     => 'pending',
            'amount'     => $amount,
            'starts_at'  => now(),
        ]);

        if (env('STRIPE_SECRET_KEY')) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => [[
                    'price_data' => [
                        'currency'     => 'inr',
                        'product_data' => ['name' => 'AajchaOffer Shop Subscription'],
                        'unit_amount'  => (int)($amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'metadata'    => ['subscription_id' => $sub->id],
                'success_url' => url('/') . '?sub_success=1',
                'cancel_url'  => url('/') . '?sub_cancel=1',
            ]);
            return redirect()->away($session->url);
        }

        // Demo fallback: activate immediately
        $sub->update([
            'status'     => 'active',
            'starts_at'  => now(),
            'expires_at' => now()->addYear(),
        ]);
        auth()->user()->update(['subscription_expires_at' => $sub->expires_at]);
        return redirect()->route('shop_dashboard')->with('notice', 'Subscription activated (demo).');
    }
}
