<?php

namespace App\Http\Controllers;
use App\StoreWebsiteProductPriceHistory;
use App\StoreWebsiteProductPrice;
use App\Product;
use App\Http\Controllers;
use App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use App\Setting;
use App\LogRequest;
use App\StoreWebsite;
use App\SimplyDutyCountry;
use App\SimplyDutySegment;
use App\Jobs\PushToMagento;
use Illuminate\Http\Request;
use App\Loggers\LogListMagento;
use App\SimplyDutyCountryHistory;
use Illuminate\Support\Facades\Auth;

class SimplyDutyCountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->code || $request->country) {
            $query = SimplyDutyCountry::query();

            if (request('code') != null) {
                $query->where('country_code', 'LIKE', "%{$request->code}%");
            }
            if (request('country') != null) {
                $query->where('country_name', 'LIKE', "%{$request->country}%");
            }
            $countries = $query->paginate(Setting::get('pagination'));
        } else {
            $countries = SimplyDutyCountry::paginate(Setting::get('pagination'));
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('simplyduty.country.partials.data', compact('countries'))->render(),
                'links' => (string) $countries->render(),
            ], 200);
        }
        $segments = SimplyDutySegment::get();

        return view('simplyduty.country.index', compact('countries'), compact('segments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(SimplyDutyCountry $simplyDutyCountry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SimplyDutyCountry $simplyDutyCountry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SimplyDutyCountry $simplyDutyCountry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SimplyDutyCountry $simplyDutyCountry)
    {
        //
    }

    public function getCountryFromApi(): JsonResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch        = curl_init();
        $url       = 'https://www.api.simplyduty.com/api/Supporting/supported-countries';

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output   = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($output), $httpcode, SimplyDutyCountryController::class, 'getCountryFromApi');

        // close curl resource to free up system resources
        curl_close($ch);

        $countries = json_decode($output);

        foreach ($countries as $country) {
            $countryDetail = $country->Country;
            $countryCode   = $country->CountryCode;
            //Country Code wtih Details
            $cat = SimplyDutyCountry::where('country_code', $countryCode)->where('country_name', $countryDetail)->first();
            if ($cat != '' && $cat != null) {
                $cat->touch();
            } else {
                $category               = new SimplyDutyCountry;
                $category->country_code = $countryCode;
                $category->country_name = $countryDetail;
                $category->save();
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * @SWG\Get(
     *   path="/duty/v1/get-countries",
     *   tags={"Duty"},
     *   summary="Get Countries",
     *   operationId="get-countries",
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
    public function sendCountryJson()
    {
        $countries = SimplyDutyCountry::all();
        foreach ($countries as $country) {
            $countryArray[] = ['Country' => $country->country_name, 'CountryCode' => $country->country_code];
        }

        return json_encode($countryArray);
    }

    public function updateduty(Request $request): JsonResponse
    {
        if (! $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!']);
        }
        $country = SimplyDutyCountry::find($request->input('id'));
        $data    = [
            'simply_duty_countries_id' => $country->id,
            'old_segment'              => $country->segment_id,
            'new_segment'              => $country->segment_id,
            'old_duty'                 => $country->default_duty,
            'new_duty'                 => $request->input('duty'),
            'updated_by'               => Auth::user()->id,

        ];
        $country->default_duty = $request->input('duty');
        $country->status       = 0;
        SimplyDutyCountryHistory::insert($data);
        if ($country->save()) {
            $this->update_store_website_product_prices($country->country_code, $request->input('duty'));

            return response()->json(['success' => true, 'message' => 'Default duty update successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Something went wrong!']);
    }

    public function addsegment(Request $request): JsonResponse
    {
        $cid  = $request->cid;
        $sid  = $request->sid;
        $duty = SimplyDutyCountry::where('id', $cid)->first();
        $data = [
            'simply_duty_countries_id' => $duty->id,
            'old_segment'              => $duty->segment_id,
            'new_segment'              => $sid,
            'old_duty'                 => $duty->default_duty,
            'new_duty'                 => $duty->default_duty,
            'updated_by'               => Auth::user()->id,
        ];
        $duty->segment_id = $sid;
        $duty->status     = 0;
        $duty->save();
        SimplyDutyCountryHistory::insert($data);

        if ($duty->save()) {
            $segementDetail = SimplyDutySegment::where('id', $duty->segment_id)->first();
            $this->update_store_website_product_segment($duty->country_code, $segementDetail['price']);

            return response()->json(['success' => true, 'message' => 'segment update successfully']);
        }

        return response()->json(['success' => true, 'message' => 'Segment Updated Successfully']);
    }

    public function assignDefaultValue(Request $request): JsonResponse
    {
        $value   = $request->value;
        $segment = $request->segment;
        if ($value > 0 && $segment > 0) {
            $duty = SimplyDutyCountry::where('segment_id', $segment)->get();
            foreach ($duty as $d) {
                $data = [
                    'simply_duty_countries_id' => $d->id,
                    'old_segment'              => $d->segment_id,
                    'new_segment'              => $d->segment_id,
                    'old_duty'                 => $d->default_duty,
                    'new_duty'                 => $value,
                    'updated_by'               => Auth::user()->id,

                ];
                $d->default_duty = $value;
                $d->status       = 0;
                $d->save();
                SimplyDutyCountryHistory::insert($data);
                $this->update_store_website_product_prices($d->country_code, $value);
            }

            return response()->json(['code' => 200, 'message' => 'Default Duty assigned']);
        } else {
            return response()->json(['code' => 100, 'message' => 'somethings wrong']);
        }
    }

    public function approve(Request $request): JsonResponse
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);
        for ($i = 0; $i < count($ids); $i++) {
            if ($ids[$i] > 0) {
                StoreWebsiteProductPrice::where('id', $ids[$i])->update(['status' => 1]);
            }
        }

        return response()->json(['code' => 200, 'message' => 'Approved Successfully']);
    }

    public function update_store_website_product_prices($code, $amount)
    {
        $ps = StoreWebsiteProductPrice::select('store_website_product_prices.id', 'store_website_product_prices.duty_price',
            'store_website_product_prices.product_id', 'store_website_product_prices.store_website_id', 'websites.code')
            ->leftJoin('websites', 'store_website_product_prices.web_store_id', 'websites.id')
            ->where('websites.code', strtolower($code))
            ->get();
        if ($ps) {
            foreach ($ps as $p) {
                StoreWebsiteProductPrice::where('id', $p->id)->update(['duty_price' => $amount, 'status' => 0]);
                $note = 'Country Duty changed  from ' . $p->duty_price . ' To ' . $amount;
                $this->pushToMagento($p->product_id, $p->store_website_id);
                StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id' => $p->id, 'updated_by' => Auth::id(), 'notes' => $note, 'created_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    public function update_store_website_product_segment($code, $segmentDiscount)
    {
        $ps = StoreWebsiteProductPrice::select('store_website_product_prices.id', 'store_website_product_prices.duty_price', 'websites.code')
            ->leftJoin('websites', 'store_website_product_prices.web_store_id', 'websites.id')
            ->where('websites.code', strtolower($code))
            ->get();
        if ($ps) {
            foreach ($ps as $p) {
                StoreWebsiteProductPrice::where('id', $p->id)->update(['segment_discount' => $segmentDiscount, 'status' => 0]);
                $this->pushToMagento($p->product_id, $p->store_website_id);
                $note = 'Segment discount change  from ' . $p->segment_discount . ' To ' . $segmentDiscount;
                StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id' => $p->id, 'updated_by' => Auth::id(), 'notes' => $note, 'created_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    public function pushToMagento($productId, $websiteId)
    {
        $product = Product::find($productId);

        if ($product) {
            $website = StoreWebsite::where('id', $websiteId)->first();
            if ($website == null) {
                Log::channel('productUpdates')->info('Product started ' . $product->id . ' No website found');
                $msg = 'No website found for  Brand: ' . $product->brand . ' and Category: ' . $product->category;
                echo $msg;
                exit;
            } else {
                $i = 1;

                if ($website) {
                    // testing
                    Log::channel('productUpdates')->info('Product started website found For website' . $website->website);
                    $log = LogListMagento::log($product->id, 'Start push to magento for product id ' . $product->id, 'info', $website->id);
                    //currently we have 3 queues assigned for this task.
                    if ($i > 3) {
                        $i = 1;
                    }
                    $log->queue = Helpers::createQueueName($website->title);
                    $log->save();
                    PushToMagento::dispatch($product, $website, $log)->onQueue($log->queue);
                    $i++;
                }
            }
        }
    }
}
