<?php

namespace App\Http\Controllers;
use App\Hubstaff\HubstaffPaymentAccount;

use App\Http\Requests\StoreHubstaffPaymentRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HubstaffPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'Hubstaff Payment';

        if (isset($request->run_command) && $request->run_command == true) {
            Artisan::call('users:payment');

            return redirect()->back();
        }

        return view('hubstaff.payment.index', compact('title'));
    }

    public function records(Request $request): JsonResponse
    {
        $records = HubstaffPaymentAccount::join('users as u', 'hubstaff_payment_accounts.user_id', 'u.id');

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('user_name', 'LIKE', "%$keyword%");
            });
        }

        if ($request->start_date != null) {
            $records = $records->whereDate('billing_start', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->end_date != null) {
            $records = $records->whereDate('billing_start', '<=', $request->end_date . ' 23:59:59');
        }

        $records = $records->select(['hubstaff_payment_accounts.*', 'u.name as user_name'])->get();

        foreach ($records as $record) {
            $record->status_name = new HubstaffPaymentAccount::STATUS[$record->status];
        }

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }

    public function save(Request $request): JsonResponse
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'billing_start' => 'required',
            'billing_end'   => 'required',
            'hrs'           => 'required',
            'rate'          => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . '<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $id = $request->get('id', 0);

        $records = HubstaffPaymentAccount::find($id);

        if (! $records) {
            $records = new HubstaffPaymentAccount;
        }

        $records->fill($post);
        $records->save();

        return response()->json(['code' => 200, 'data' => $records]);
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
    public function store(StoreHubstaffPaymentRequest $request): RedirectResponse
    {

        $data = $request->except('_token');

        HubstaffPaymentAccount::create($data);

        return redirect()->route('vendors.index')->withSuccess('You have successfully created a vendor category!');
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
     * Edit Page
     *
     * @param Request $request [description]
     * @param mixed   $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $modal = HubstaffPaymentAccount::where('id', $id)->first();

        if ($modal) {
            return response()->json(['code' => 200, 'data' => $modal]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }

    /**
     * delete Page
     *
     * @param Request $request [description]
     * @param mixed   $id
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $isExist = HubstaffPaymentAccount::where('id', $id)->first();
        if ($isExist) {
            $isExist->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong id!']);
    }
}
