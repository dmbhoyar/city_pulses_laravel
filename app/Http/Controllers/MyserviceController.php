<?php

namespace App\Http\Controllers;

use App\Models\Update;
use Illuminate\Http\Request;

class MyserviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('service_provider');
    }

    public function index()
    {
        $shop = $this->getOrBuildShop();
        $pageConfig = $shop->page_config ?? [];
        return view('myservice.index', compact('shop', 'pageConfig'));
    }

    public function configure()
    {
        $shop = $this->getOrBuildShop();
        $pageConfig = $shop->page_config ?? [];
        return view('myservice.configure', compact('shop', 'pageConfig'));
    }

    public function configureSave(Request $request)
    {
        $shop = $this->getOrBuildShop();
        if ($request->has('shop.page_config')) {
            $cfg = $request->input('shop.page_config');
            if (is_string($cfg)) $cfg = json_decode($cfg, true) ?? [];
            $shop->page_config = $cfg;
        }
        if ($shop->save()) {
            return redirect()->route('myservice')->with('notice', 'Service page saved.');
        }
        return back()->with('alert', 'Unable to save page configuration');
    }

    public function businessCard()
    {
        $shop = $this->getOrBuildShop();
        return view('myservice.business_card', compact('shop'));
    }

    public function businessCardSave(Request $request)
    {
        $shop = $this->getOrBuildShop();
        $cfg = $shop->page_config ?? [];
        $svc = $request->input('services_list', '');
        $cfg['services_list'] = array_filter(array_map('trim', explode("\n", str_replace("\r\n", "\n", $svc))));
        $shop->page_config = $cfg;
        if ($shop->save()) {
            return redirect()->route('myservice')->with('notice', 'Business card saved.');
        }
        return back()->with('alert', 'Unable to save');
    }

    private function getOrBuildShop()
    {
        return auth()->user()->shops()->first()
            ?? auth()->user()->shops()->make(['name' => 'My Service']);
    }
}
