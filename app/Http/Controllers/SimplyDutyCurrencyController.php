<?php

namespace App\Http\Controllers;

use App\LogRequest;
use App\Setting;
use App\SimplyDutyCurrency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SimplyDutyCurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->currency) {
            $query = SimplyDutyCurrency::query();

            if (request('currency') != null) {
                $query->where('currency', request('currency'));
            }

            $currencies = $query->paginate(Setting::get('pagination'));
        } else {
            $currencies = SimplyDutyCurrency::paginate(Setting::get('pagination'));
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('simplyduty.currency.partials.data', compact('currencies'))->render(),
                'links' => (string) $currencies->render(),
            ], 200);
        }

        return view('simplyduty.currency.index', compact('currencies'));
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
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(SimplyDutyCurrency $simplyDutyCurrency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SimplyDutyCurrency $simplyDutyCurrency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SimplyDutyCurrency $simplyDutyCurrency)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SimplyDutyCurrency $simplyDutyCurrency)
    {
        //
    }

    public function getCurrencyFromApi(): JsonResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        $url = 'https://www.api.simplyduty.com/api/Supporting/supported-currencies';

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($output), $httpcode, SimplyDutyCurrencyController::class, 'getCurrencyFromApi');

        // close curl resource to free up system resources
        curl_close($ch);

        $currencies = json_decode($output);

        foreach ($currencies as $currency) {
            $currency = $currency->CurrencyType;
            $cur = SimplyDutyCurrency::where('currency', $currency)->first();
            if ($cur != '' && $cur != null) {
                $cur->touch();
            } else {
                $currencySave = new SimplyDutyCurrency;
                $currencySave->currency = $currency;
                $currencySave->save();
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * @SWG\Get(
     *   path="/duty/v1/get-currencies",
     *   tags={"Duty"},
     *   summary="Get currencies",
     *   operationId="get-currencies",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function sendCurrencyJson()
    {
        $currencies = SimplyDutyCurrency::all();
        foreach ($currencies as $currency) {
            $currencyArray[] = ['CurrencyType' => $currency->currency];
        }

        return json_encode($currencyArray);
    }
}
