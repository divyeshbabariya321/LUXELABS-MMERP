<?php

namespace App\Http\Controllers;
use App\ErpLog;

use App\StoreWebsite;
use App\StoreWebsiteAnalytic;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreWebsiteAnalyticsController extends Controller
{
    public function index(): View
    {
        try {
            $storeWebsiteAnalyticsData = StoreWebsiteAnalytic::all();
            $storeWebsites = StoreWebsite::where('deleted_at', null)->get();

            return view('store-website-analytics.index', compact('storeWebsiteAnalyticsData', 'storeWebsites'));
        } catch (Exception $e) {
            Log::error('Account page ::'.$e->getMessage());
        }
    }

    public function create(Request $request)
    {
        if ($request->post()) {
            $rules = [
                'website' => 'required',
                'account_id' => 'required',
                'store_website_id' => 'required|integer',
            ];

            //validation for googles service account json file for Google Analytics
            if (! $request->id) {
                $rules['google_service_account_json'] = 'required|file';
            } else {
                //
            }

            $messages = [
                'website' => 'Website field is required.',
                'account_id' => 'Account Id field is required.',
                'view_id' => 'View Id field is required.',
                'store_website_id' => 'Store Id field is required.',
                'store_website_id' => 'Store Id value must be a number.',
                'google_service_account_json' => 'Please Upload Valid Google Service Account Json File.',
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
                //file upload code for googles service account json file for google analytics
                $filename = '';
                if ($request->hasFile('google_service_account_json')) {
                    $GoogleServiceAccountJsonFile = $request->file('google_service_account_json');
                    $extension = $GoogleServiceAccountJsonFile->getClientOriginalExtension();
                    $filename = $request->view_id.$GoogleServiceAccountJsonFile->getFilename().'.'.$extension;
                    // file will be uploaded to resources/analytics_files
                    Storage::disk('analytics_files')->put($filename, File::get($GoogleServiceAccountJsonFile));
                }

                if ($request->id) {
                    $updatedData = $request->all();
                    unset($updatedData['_token']);
                    // save uploaded googles service account json file name
                    $updatedData['google_service_account_json'] = $filename;
                    StoreWebsiteAnalytic::whereId($request->id)->update($updatedData);

                    return redirect()->to('/store-website-analytics/index')->with('success', 'Store Website Analytics updated successfully.');
                } else {
                    $insertData = $request->all();
                    // save uploaded googles service account json file name
                    $insertData['google_service_account_json'] = $filename;
                    StoreWebsiteAnalytic::create($insertData);

                    return redirect()->to('/store-website-analytics/index')->with('success', 'Store Website Analytics saved successfully.');
                }
            }
        } else {
            $storeWebsites = StoreWebsite::where('deleted_at', null)->get();

            return view('store-website-analytics.create', compact('storeWebsites'));
        }
    }

    public function edit($id = null): View
    {
        $storeWebsiteAnalyticData = StoreWebsiteAnalytic::whereId($id)->first();
        $storeWebsites = StoreWebsite::where('deleted_at', null)->get();

        return view('store-website-analytics.edit', compact('storeWebsiteAnalyticData', 'storeWebsites'));
    }

    public function delete($id = null): RedirectResponse
    {
        StoreWebsiteAnalytic::whereId($id)->delete();

        return redirect()->to('/store-website-analytics/index')->with('success', 'Record deleted successfully.');
    }

    public function report($id = null): View
    {
        $reports = ErpLog::where('model', StoreWebsiteAnalytic::class)->orderByDesc('id')->where('model_id', $id)->get();

        return view('store-website-analytics.reports', compact('reports'));
    }
}
