<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\ScrappedFacebookUser;

class ScrappedFacebookUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = ScrappedFacebookUser::query();

        $scrapeFacebookUsers = $query->orderBy('id')->paginate(25)->appends(request()->except(['page']));

        return view('scrapefacebook.index', compact('scrapeFacebookUsers'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
}
