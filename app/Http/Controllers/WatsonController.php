<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWatsonRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\StoreWebsite;
use App\WatsonAccount;
use App\Jobs\PushToWatson;
use Illuminate\Http\Request;

class WatsonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $store_websites = StoreWebsite::all();
        $accounts       = WatsonAccount::all();

        return view('watson.index', compact('store_websites', 'accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWatsonRequest $request): JsonResponse
    {
        WatsonAccount::create($request->all());

        return response()->json(['code' => 200, 'message' => 'Account Successfully created']);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $account        = WatsonAccount::find($id);
        $store_websites = StoreWebsite::all();

        return response()->json(['account' => $account, 'store_websites' => $store_websites]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $account = WatsonAccount::find($id);
        $params  = $request->except('_token');
        if (array_key_exists('is_active', $params)) {
            $params['is_active'] = 1;
        } else {
            $params['is_active'] = 0;
        }
        $account->update($params);

        return response()->json(['code' => 200, 'message' => 'Account Successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $account = WatsonAccount::find($id);
        $account->delete();

        return redirect()->back();
    }

    public function addIntentsToWatson($id): JsonResponse
    {
        $account = WatsonAccount::find($id);
        PushToWatson::dispatch($id)->onQueue('watson_push');
        $account->update(['watson_push' => 1]);

        return response()->json(['message' => 'Successfully added to the queue', 'code' => 200]);
    }
}
