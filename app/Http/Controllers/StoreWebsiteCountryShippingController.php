<?php

namespace App\Http\Controllers;
use App\Helpers\GuzzleHelper;

use App\SimplyDutyCountry;
use App\StoreWebsite;
use App\StoreWebsitesCountryShipping;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StoreWebsiteCountryShippingController extends Controller
{
    public function index(): View
    {
        try {
            $dataList = StoreWebsitesCountryShipping::all();

            return view('store-website-country-shipping.index', compact('dataList'));
        } catch (Exception $e) {
            Log::error('Shipping page ::'.$e->getMessage());
        }
    }

    public function create(Request $request)
    {
        if ($request->post()) {
            $rules = [
                'store_website_id' => 'required',
                'country_name' => 'required',
                'price' => 'required|integer',
                'currency' => 'required',
            ];

            $messages = [
                'store_website_id' => 'Website field is required.',
                'country_name' => 'Country name field is required.',
                'price' => 'Price field is required.',
                'currency' => 'Price field is required.',
            ];

            $validation = validator(
                $request->all(),
                $rules,
                $messages
            );
            //If validation fail send back the Input with errors
            if ($validation->fails()) {
                //withInput keep the users info
                return redirect()->back()->withErrors($validation)->withInput();
            } else {
                $countyCode = SimplyDutyCountry::where('country_name', $request->country_name)->first();
                $storeWebsites = StoreWebsite::where('id', $request->store_website_id)->first();
                $url = $storeWebsites->magento_url.'/default/rest/all/V1/shippingcost/';
                $api_key = $storeWebsites->api_token;

                $headers = [
                    'Authorization' => 'Bearer '.$api_key,
                    'Content-Type' => 'application/json',
                ];

                $pushMagentoArr = [
                    'shippingCountryCode' => $countyCode->country_code,
                    'shippingCountryName' => $request->country_name,
                    'shippingPrice' => $request->price,
                    'shippingCurrency' => $request->currency,
                ];

                if ($request->id) {
                    $updatedData = $request->all();
                    unset($updatedData['_token']);
                    if ($request->ship_id) {
                        $url .= 'update';
                        $pushMagentoArr['ship_id'] = $request->ship_id;
                        $pushMagentoArr['updatedShippingPrice'] = $request->price;
                        $response = GuzzleHelper::post($url, $pushMagentoArr, $headers);
                        if (isset($response[0]->status)) {
                            StoreWebsitesCountryShipping::whereId($request->id)->update($updatedData);
                        } else {
                            return redirect()->route('store-website-country-shipping.index')->with('error', $response[0]->Message ?? 'Something went wrong');
                        }
                    }

                    return redirect()->route('store-website-country-shipping.index')->with('success', 'Data updated successfully.');
                } else {
                    $insertData = $request->all();
                    $url .= 'add';
                    $response = GuzzleHelper::post($url, $pushMagentoArr, $headers);

                    if (isset($response[0]->status)) {
                        $insertData['ship_id'] = $response[0]->ship_id;
                        StoreWebsitesCountryShipping::create($insertData);

                        return redirect()->route('store-website-country-shipping.index')->with('success', 'Data saved successfully.');
                    } else {
                        return redirect()->route('store-website-country-shipping.index')->with('error', $response[0]->Message ?? 'Something went wrong');
                    }
                }
            }
        } else {
            $storeWebsites = StoreWebsite::where('deleted_at', null)->get();
            $simplyDutyCountry = SimplyDutyCountry::get();

            return view('store-website-country-shipping.create', compact('storeWebsites', 'simplyDutyCountry'));
        }
    }

    public function edit($id = null): View
    {
        $data = StoreWebsitesCountryShipping::whereId($id)->first();
        $storeWebsites = StoreWebsite::where('deleted_at', null)->get();
        $simplyDutyCountry = SimplyDutyCountry::get();

        return view('store-website-country-shipping.edit', compact('data', 'storeWebsites', 'simplyDutyCountry'));
    }

    public function delete($id = null): RedirectResponse
    {
        $data = StoreWebsitesCountryShipping::whereId($id)->first();
        $storeWebsites = StoreWebsite::where('id', $data->store_website_id)->first();

        $url = $storeWebsites->magento_url.'/default/rest/all/V1/shippingcost/delete';
        $api_key = $storeWebsites->api_token;

        $pushMagentoArr = ['ship_id' => $data->ship_id];
        $headers = [
            'Authorization' => 'Bearer '.$api_key,
            'Content-Type' => 'application/json',
        ];
        $response = GuzzleHelper::post($url, $pushMagentoArr, $headers);
        if (isset($response[0]->status)) {
            StoreWebsitesCountryShipping::whereId($id)->delete();

            return redirect()->route('store-website-country-shipping.index')->with('success', 'Record deleted successfully.');
        } else {
            return redirect()->route('store-website-country-shipping.index')->with('error', $response[0]->Message ?? 'Something went wrong');
        }
    }
}
