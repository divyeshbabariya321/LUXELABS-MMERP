<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Http\Requests\StoreMonetaryAccountRequest;
use App\Http\Requests\UpdateMonetaryAccountRequest;
use App\MonetaryAccount;
use App\MonetaryAccountHistory;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonetaryAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $data;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            session()->flash('active_tab', 'blogger_list_tab');

            return $next($request);
        });
    }

    public function index(MonetaryAccount $monetary_account, Request $request): View
    {
        $this->data['accounts'] = $monetary_account;
        $order_by = 'DESC';
        if ($request->orderby == '') {
            $order_by = 'ASC';
        }

        $this->data['orderby'] = $order_by;
        $this->data['accounts'] = $this->data['accounts']->paginate(50);
        $this->data['currencies'] = Helpers::currencies();
        $this->data['account_types'] = ['cash' => 'Cash', 'bank' => 'Bank'];

        return view('monetary-account.index', $this->data);
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
    public function store(StoreMonetaryAccountRequest $request): RedirectResponse
    {
        MonetaryAccount::create([
            'name' => $request->get('name'),
            'date' => $request->get('date'),
            'currency' => $request->get('currency'),
            'amount' => $request->get('amount'),
            'type' => $request->get('type'),
            'short_note' => $request->get('short_note'),
            'description' => $request->get('description'),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->back()->withSuccess('Monetary Capital Successfully stored.');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
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
     *
     * @param  int  $id
     */
    public function update(UpdateMonetaryAccountRequest $request, MonetaryAccount $monetary_account): RedirectResponse
    {
        $monetary_account->fill([
            'name' => $request->get('name'),
            'date' => $request->get('date'),
            'currency' => $request->get('currency'),
            'amount' => $request->get('amount'),
            'type' => $request->get('type'),
            'short_note' => $request->get('short_note'),
            'description' => $request->get('description'),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ])->save();

        return redirect()->back()->withSuccess('Monetary Capital Successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(MonetaryAccount $monetary_account): RedirectResponse
    {
        try {
            $monetary_account->delete();
        } catch (Exception $exception) {
            return redirect()->back()->withErrors('Couldn\'t delete data');
        }

        return redirect()->back()->withSuccess('You have successfully deleted account detail');
    }

    public function history(Request $request, $id)
    {
        $account = MonetaryAccount::find($id);
        if ($account) {
            $daterange = $request->daterange;
            $pricerange = $request->pricerange;
            $history = MonetaryAccountHistory::where('monetary_account_id', $id);
            if ($daterange != '') {
                $date = explode('-', $daterange);
                $datefrom = date('Y-m-d', strtotime($date[0]));
                $dateto = date('Y-m-d', strtotime($date[1]));
                $history->whereRaw("date(created_at) between date('$datefrom') and date('$dateto')");
            }
            if ($pricerange != '') {
                if ($pricerange == 1) {
                    $history->whereBetween('amount', [0, 1000]);
                }
                if ($pricerange == 2) {
                    $history->whereBetween('amount', [1000, 2000]);
                }
                if ($pricerange == 3) {
                    $history->whereBetween('amount', [2000, 5000]);
                }
                if ($pricerange == 4) {
                    $history->whereBetween('amount', [5000, 10000]);
                }
                if ($pricerange == 5) {
                    $history->where('amount', '>', 10000);
                }
            }

            $history = $history->latest()->paginate();

            return view('monetary-account.history', compact('history', 'account'));
        } else {
            return abort(404);
        }
    }
}
