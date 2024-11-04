<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\StoreWebsite;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index(Request $request): View
    {
        $websites = StoreWebsite::all();

        return view('StoreWebsite.index', compact('websites'));
    }
}
