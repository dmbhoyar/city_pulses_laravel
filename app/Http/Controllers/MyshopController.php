<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Update;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MyshopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('shopowner');
    }

    public function index()
    {
        $shop = auth()->user()->shops()->first();
        return view('myshop.index', compact('shop'));
    }

    public function configure()
    {
        $shop = $this->getOrBuildShop();
        $pageConfig = $shop->page_config ?? [];
        return view('myshop.configure', compact('shop', 'pageConfig'));
    }

    public function configureSave(Request $request)
    {
        $shop = $this->getOrBuildShop();
        $saved = $shop->fill($request->only(['name', 'description', 'phone', 'address', 'template']));

        // Update page_config if provided
        if ($request->has('shop.page_config')) {
            $cfg = $request->input('shop.page_config');
            if (is_string($cfg)) {
                $cfg = json_decode($cfg, true) ?? [];
            }
            $shop->page_config = $cfg;
        }

        if ($shop->save()) {
            return redirect()->route('myshop')->with('notice', 'Shop updated.');
        }
        return back()->with('alert', 'Unable to save page configuration');
    }

    public function workers()
    {
        $shop = auth()->user()->shops()->first();
        $workers = $shop ? User::where('shop_id', $shop->id)->get() : collect();
        return view('myshop.workers', compact('shop', 'workers'));
    }

    public function createWorker(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) return redirect()->route('myshop')->with('alert', 'No shop found');

        $attrs = $request->validate([
            'worker.first_name'    => 'required|string|max:100',
            'worker.last_name'     => 'nullable|string|max:100',
            'worker.email'         => 'required|email|unique:users,email',
            'worker.mobile_number' => 'nullable|string|max:20',
        ]);
        $w = $attrs['worker'];
        $password = Str::random(10);

        $user = new User([
            'first_name'    => $w['first_name'],
            'last_name'     => $w['last_name'] ?? '',
            'email'         => $w['email'],
            'mobile_number' => $w['mobile_number'] ?? '',
            'password'      => Hash::make($password),
            'role'          => 'shopworker',
            'shop_id'       => $shop->id,
        ]);

        if ($user->save()) {
            // Send password reset email so worker can set own password
            $user->sendPasswordResetNotification(app('auth.password.broker')->createToken($user));
            return redirect()->route('workers_myshop')->with('notice', 'Worker created — an email was sent to set password.');
        }

        $workers = User::where('shop_id', $shop->id)->get();
        return back()->with('alert', 'Unable to create worker')->withInput();
    }

    public function updateWorker(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) return redirect()->route('myshop')->with('alert', 'No shop found');

        $workerId = $request->input('worker.id');
        $worker = User::where('id', $workerId)->where('shop_id', $shop->id)->first();
        if (!$worker) return redirect()->route('workers_myshop')->with('alert', 'Worker not found');

        $worker->fill($request->only(['worker.experience', 'worker.tags'])['worker'] ?? []);
        if ($worker->save()) {
            return redirect()->route('workers_myshop')->with('notice', 'Worker updated.');
        }
        return back()->with('alert', 'Unable to update worker');
    }

    public function workerExperience(Request $request, $id)
    {
        $shop = auth()->user()->shops()->first();
        $worker = User::where('id', $id)->where('shop_id', $shop?->id)->firstOrFail();
        return view('myshop.worker_experience', compact('shop', 'worker'));
    }

    public function subscribe()
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) return redirect()->route('myshop')->with('alert', 'No shop found');
        auth()->user()->update(['subscription_expires_at' => now()->addYear()]);
        return redirect()->route('myshop')->with('notice', 'Subscription activated for 1 year (demo)');
    }

    public function offerNew()
    {
        $shop = auth()->user()->shops()->first();
        $offer = new Update();
        return view('myshop.offer_new', compact('shop', 'offer'));
    }

    public function offerCreate(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);
        $validated['update_type'] = 'offer';
        if ($shop?->city_id) $validated['city_id'] = $shop->city_id;
        $offer = Update::create($validated);
        return redirect()->route('myshop')->with('notice', 'Offer added.');
    }

    public function experience()
    {
        $shop = auth()->user()->shops()->first();
        return view('myshop.experience', compact('shop'));
    }

    public function idcard()
    {
        $shop = auth()->user()->shops()->first();
        return view('myshop.idcard', compact('shop'));
    }

    private function getOrBuildShop(): Shop
    {
        return auth()->user()->shops()->first()
            ?? auth()->user()->shops()->make(['name' => auth()->user()->full_name . "'s Shop"]);
    }
}
