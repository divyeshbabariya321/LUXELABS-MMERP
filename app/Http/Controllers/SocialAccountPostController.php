<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\BusinessPost;
use App\Social\SocialConfig;

class SocialAccountPostController extends Controller
{
    public function index($accountId): View
    {
        $account = SocialConfig::find($accountId);
        $posts   = BusinessPost::where('social_config_id', $accountId)->orderByDesc('post_id')->latest('time')->paginate(50);

        return view('social-account.post', compact('account', 'posts'));
    }
}
