<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreGTMetrixAccountRequest;
use App\Http\Requests\UpdateStoreGTMetrixAccountRequest;
use App\StoreGTMetrixAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoreGTMetrixAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $Accounts = StoreGTMetrixAccount::latest()->paginate(5);

        return view('GtMetrixAccount.index', compact('Accounts'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('GtMetrixAccount.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreGTMetrixAccountRequest $request): RedirectResponse
    {

        StoreGTMetrixAccount::create($request->all());

        return redirect()->route('GtMetrixAccount.index')
            ->with('success', 'GtMetrixAccount created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StoreGTMetrixAccount $StoreGTMetrixAccount): View
    {
        return view('GtMetrixAccount.show', compact('Accounts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  mixed  $id
     */
    public function edit($id): View
    {
        $account = StoreGTMetrixAccount::where('id', $id)->get()->first();

        return view('GtMetrixAccount.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreGTMetrixAccountRequest $request): RedirectResponse
    {
        $id = $request->input('id');
        $input['email'] = $request->input('email');
        $input['password'] = $request->input('password');
        $input['account_id'] = $request->input('account_id');
        $input['status'] = $request->input('status');

        StoreGTMetrixAccount::where('id', $id)->update($input);

        return redirect()->route('GtMetrixAccount.index')
            ->with('success', 'GtMetrixAccount updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $id
     */
    public function destroy($id): RedirectResponse
    {
        $StoreGTMetrixAccount = StoreGTMetrixAccount::find($id);
        $StoreGTMetrixAccount->delete();

        return redirect()->route('GtMetrixAccount.index')
            ->with('success', 'GtMetrix Account deleted successfully');
    }
}
