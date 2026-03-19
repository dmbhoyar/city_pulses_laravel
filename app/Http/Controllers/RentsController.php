<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class RentsController extends Controller
{
    public function index()
    {
        $rents = Listing::where('category', 'rent')->orderByDesc('created_at')->get();
        return view('rents.index', compact('rents'));
    }

    public function show(Listing $listing)
    {
        return view('rents.show', compact('listing'));
    }
}
