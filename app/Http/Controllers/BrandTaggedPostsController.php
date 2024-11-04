<?php

namespace App\Http\Controllers;
use App\Http\Controllers;

use App\Account;
use App\BrandTaggedPosts;
use App\Http\Requests\StoreBrandTaggedPostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BrandTaggedPostsController extends Controller
{
    public function index(): View
    {
        $posts = BrandTaggedPosts::all();
        $accounts = Account::where('platform', 'instagram')->get();

        return view('instagram.bt.index', compact('posts', 'accounts'));
    }

    public function store(StoreBrandTaggedPostRequest $request): RedirectResponse
    {

        $account = Account::find($request->get('account_id'));

        $message = $request->get('message');

        $usernames = $request->get('receipts');

        foreach ($usernames as $username) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $photo = new InstagramPhoto($file);
            }
        }

        return redirect()->back()->with('message', 'Message sent successfully!');
    }
}
