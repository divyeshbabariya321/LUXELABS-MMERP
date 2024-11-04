<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePurchaseStatusRequest;
use App\Http\Requests\StorePurchaseStatusRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\PurchaseStatus;
use Illuminate\Http\Request;

class PurchaseStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $purchaseStatus = PurchaseStatus::orderByDesc('id')->paginate(10);

        return view('purchase-status.index', compact('purchaseStatus'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('purchase-status.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseStatusRequest $request): RedirectResponse
    {

        PurchaseStatus::create(['name' => $request->input('name')]);

        return redirect()->route('purchase-status.index')
            ->with('success', 'Purchase Status created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $status = PurchaseStatus::find($id);

        return view('purchase-status.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseStatusRequest $request, int $id): RedirectResponse
    {

        $purchaseStatus       = PurchaseStatus::find($id);
        $purchaseStatus->name = $request->input('name');
        $purchaseStatus->save();

        return redirect()->route('purchase-status.index')
            ->with('success', 'Purchase Status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        PurchaseStatus::where('id', $id)->delete();

        return redirect()->route('purchase-status.index')
            ->with('success', 'Purchase Status deleted successfully');
    }
}
