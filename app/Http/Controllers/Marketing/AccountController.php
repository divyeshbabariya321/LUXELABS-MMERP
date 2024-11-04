<?php

namespace App\Http\Controllers\Marketing;

use App\Account;
use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\EditAccountRequest;
use App\Http\Requests\Marketing\StoreAccountRequest;
use App\Marketing\MarketingPlatform;
use App\Setting;
use App\StoreWebsite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index($type, Request $request)
    {
        $query = Account::query();

        if ($type) {
            $query = $query->where('platform', $type);
        } else {
            $type = '';
        }

        if ($request->platform) {
            $query = $query->where('platform', $request->platform);
        }

        if ($request->term) {
            $query = $query->where('last_name', 'LIKE', '%'.$request->term.'%')
                ->orWhere('email', 'LIKE', '%'.$request->term.'%')
                ->orWhere('platform', 'LIKE', '%'.$request->term.'%');
        }

        if ($request->date) {
            $query = $query->whereDate('created_at', $request->date);
        }

        $accounts = $query->orderByDesc('id')->paginate(25);

        $platforms = MarketingPlatform::all();
        $automation_form = [];
        $automation_form['posts_per_day'] = Setting::get('posts_per_day');
        $automation_form['likes_per_day'] = Setting::get('likes_per_day');
        $automation_form['send_requests_per_day'] = Setting::get('send_requests_per_day');
        $automation_form['accept_requests_per_day'] = Setting::get('accept_requests_per_day');
        $automation_form['image_per_post'] = Setting::get('image_per_post');

        $websites = StoreWebsite::select('id', 'title')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('marketing.accounts.partials.data', compact('accounts', 'type', 'platforms', 'websites', 'automation_form'))->render(),
                'links' => (string) $accounts->render(),
                'count' => $accounts->total(),
            ], 200);
        }

        return view('marketing.accounts.index', compact('accounts', 'type', 'platforms', 'websites', 'automation_form'));
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {

        $check = Account::where('platform', $request->platform)->where('last_name', $request->username)->first();
        if ($check) {
            return redirect()->back()->with('message', 'Account Already Exist');
        }
        $account = new Account;
        $account->first_name = $request->username;
        $account->last_name = $request->username;
        $account->password = $request->password;
        $account->email = $request->email;
        $account->number = $request->number;
        $account->provider = $request->provider;
        $account->frequency = $request->frequency;
        $account->is_customer_support = $request->customer_support;
        $account->instance_id = $request->instance_id;
        $account->token = $request->token;
        $account->send_start = $request->send_start;
        $account->send_end = $request->send_end;
        $account->platform = $request->platform;
        $account->status = $request->status;
        $account->store_website_id = $request->website;
        $account->proxy = $request->proxy;
        $account->save();

        return redirect()->back()->with('message', 'Account Saved');
    }

    public function edit(EditAccountRequest $request): RedirectResponse
    {

        $account = Account::find($request->id);
        $account->first_name = $request->username;
        $account->last_name = $request->username;
        $account->password = $request->password;
        $account->email = $request->email;
        $account->number = $request->number;
        $account->provider = $request->provider;
        $account->frequency = $request->frequency;
        $account->is_customer_support = $request->customer_support;
        $account->instance_id = $request->instance_id;
        $account->token = $request->token;
        $account->send_start = $request->send_start;
        $account->send_end = $request->send_end;
        $account->platform = $request->platform;
        $account->store_website_id = $request->website;
        $account->proxy = $request->proxy;
        $account->status = $request->status;
        $account->save();

        return redirect()->back()->with('message', 'Account Updated');
    }

    public function automation(Request $request): RedirectResponse
    {
        foreach ($request->except('_token') as $key => $val) {
            Setting::where('name', $key)->update(['val' => $val]);
        }

        return redirect()->back()->with('message', 'Automation form Updated');
    }
}
