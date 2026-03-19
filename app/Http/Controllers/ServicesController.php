<?php

namespace App\Http\Controllers;

use App\Models\Listing;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Listing::where('category', 'service')->orderByDesc('created_at')->get();
        return view('services.index', compact('services'));
    }
}
