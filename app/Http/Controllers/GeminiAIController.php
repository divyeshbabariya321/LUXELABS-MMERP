<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeminiAIRequest;
use App\Models\GeminiAiAccount;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeminiAIController extends Controller
{
    public function index(): View
    {
        $store_websites = StoreWebsite::all();
        $accounts = GeminiAiAccount::all();

        return view('geminiai.index', compact('store_websites', 'accounts'));
    }

    public function store(GeminiAIRequest $request): JsonResponse
    {
        GeminiAiAccount::create($request->all());

        return response()->json(['code' => 200, 'message' => 'Account Successfully created']);
    }

    public function show(int $id): JsonResponse
    {
        $account = GeminiAiAccount::find($id);
        $store_websites = StoreWebsite::all();

        return response()->json(['account' => $account, 'store_websites' => $store_websites]);
    }

    public function update(GeminiAIRequest $request, int $id): JsonResponse
    {
        $account = GeminiAiAccount::find($id);
        $params = $request->except('_token');
        $account->update($params);

        return response()->json(['code' => 200, 'message' => 'Account Successfully updated']);
    }

    public function destroy(int $id): RedirectResponse
    {
        $account = GeminiAiAccount::find($id);
        $account->delete();

        return redirect()->back();
    }
}
