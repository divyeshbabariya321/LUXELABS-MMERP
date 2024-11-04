<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        return view('home');
    }

    public function generateFavicon(Request $request)
    {

        $title = $request->get('title', 'Home') ?? 'Home';
        $words = explode(' ', $title);
        $acronym = '';
        $i = 1;
        foreach ($words as $w) {
            if (isset($w[0])) {
                $acronym .= $w[0];
                if ($i == 2) {
                    break;
                }
                $i++;
            }
        }

        // create Image from file
        $img = \Image::make(public_path('favicon/favicon-30X30.png'));
        // use callback to define details
        $img->text(strtoupper($acronym), 16, 8, function ($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(20);
            $font->align('center');
            $font->valign('top');
            $font->color('#6E6767');
        });

        return $img->response('png');
    }

    public function logoutRefresh(): RedirectResponse
    {
        Session::flush();

        return redirect()->route('login');
    }
}
