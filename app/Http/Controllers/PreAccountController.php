<?php

namespace App\Http\Controllers;

use App\Account;
use App\Http\Requests\StorePreAccountRequest;
use App\PeopleNames;
use App\PreAccount;
use App\TargetLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PreAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $accounts = PreAccount::all();
        $firstName = PeopleNames::select('id', 'name')->inRandomOrder()->take(10)->get();
        $lastName = PeopleNames::select('id', 'name')->inRandomOrder()->take(10)->get()->toArray();
        $countries = TargetLocation::all();

        return view('pre.accounts', compact('accounts', 'firstName', 'lastName', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePreAccountRequest $request): RedirectResponse
    {

        $emails = $request->get('email');

        foreach ($emails as $key => $email) {
            if (! $email) {
                continue;
            }
            $account = new PreAccount;
            $account->first_name = $request->get('first_name')[$key];
            $account->last_name = $request->get('last_name')[$key];
            $account->email = $email;
            $account->password = $request->get('password')[$key];
            $account->instagram = 0;
            $account->facebook = 0;
            $account->pinterest = 0;
            $account->twitter = 0;
            $account->save();

            $a = new Account;
            $a->email = $account->email;
            $a->first_name = $account->first_name.' '.$account->last_name;
            $a->platform = 'instagram';
            $a->dob = date('Y-m-d');
            $a->save();

            $a = new Account;
            $a->email = $account->email;
            $a->first_name = $account->first_name.' '.$account->last_name;
            $a->platform = 'pinterest';
            $a->dob = date('Y-m-d');
            $a->save();
        }

        return redirect()->back()->with('message', 'E-mail added successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PreAccount  $preAccount
     * @param  mixed  $id
     */
    public function destroy($id): RedirectResponse
    {
        $pre = PreAccount::findOrFail($id);
        $pre->delete();

        return redirect()->back()->with('success', 'Deleted successfully!');
    }
}
