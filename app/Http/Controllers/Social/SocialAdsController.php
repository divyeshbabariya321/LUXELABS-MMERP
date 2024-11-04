<?php

namespace App\Http\Controllers\Social;

use App\Http\Requests\Social\EditSocialAdRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Setting;
use App\Social\SocialAd;
use Illuminate\View\View;
use App\Social\SocialAdset;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Social\SocialPostLog;
use App\Models\SocialAdAccount;
use App\Social\SocialAdCreative;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use JanuSoftware\Facebook\Exception\SDKException;
use Exception;

class SocialAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|View
     */
    public function index(Request $request)
    {
        $ads_data = SocialAd::orderByDesc('id');
        $ads_data = $ads_data->get();

        $configs = SocialAdAccount::pluck('name', 'id');

        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $ads = SocialAd::orderByDesc('id')->with('account.storeWebsite', 'adset', 'creative');
        } else {
            $ads = SocialAd::latest()->with('account.storeWebsite', 'adset', 'creative');
        }

        if (! empty($request->date)) {
            $ads->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if (! empty($request->name)) {
            $ads->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if (! empty($request->config_name)) {
            $ads->whereIn('config_id', $request->config_name);
        }

        if (! empty($request->adset_name)) {
            $ads->whereIn('ad_set_name', $request->adset_name);
        }

        $ads = $ads->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.ads.data', compact('ads', 'configs', 'ads_data'))->render(),
                'links' => (string) $ads->render(),
            ]);
        }

        return view('social.ads.index', compact('ads', 'configs', 'ads_data'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log                  = new SocialPostLog();
        $Log->config_id       = $config_id;
        $Log->post_id         = $post_id;
        $Log->platform        = $platform;
        $Log->log_title       = $title;
        $Log->log_description = $description;
        $Log->modal           = 'SocialAd';
        $Log->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $configs = SocialAdAccount::pluck('name', 'id');

        return view('social.ads.create', compact('configs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws SDKException
     */
    public function store(Request $request): RedirectResponse
    {
        $ad = SocialAd::create([
            'config_id'        => $request->config_id,
            'name'             => $request->name,
            'adset_id'         => $request->adset_id,
            'creative_id'      => $request->adcreative_id,
            'status'           => $request->status,
            'ad_creative_name' => $request->ad_creative_name,
            'ad_set_name'      => $request->ad_set_name,
        ]);

        // Get adset and ad_creative reference ID. DEVTASK-24765
        $ref_adset    = SocialAdset::find($request->adset_id);
        $ref_adset_id = $ref_adset->ref_adset_id;

        $ref_adcreative    = SocialAdCreative::find($request->adcreative_id);
        $ref_adcreative_id = $ref_adcreative->ref_adcreative_id;

        $data['name']     = $request->input('name');
        $data['adset_id'] = $ref_adset_id;
        $data['status']   = $request->input('status');
        $data['creative'] = json_encode(['creative_id' => $ref_adcreative_id]);

        $config = SocialAdAccount::find($ad->config_id);
        $fb     = new FB($config);
        $this->socialPostLog($config->id, $ad->id, $config->platform, 'message', 'get page access token');
        try {
            $response = $fb->createAd($config->ad_account_id, $data);
            if ($response['id']) {
                $ad->update([
                    'ref_ads_id' => $response['id'],
                ]);
            }
            $ad->update([
                'live_status' => 'SUCCESS',
            ]);

            Session::flash('message', 'Campaign created  successfully');

            return redirect()->route('social.ad.index');
        } catch (Exception $e) {
            $ad->update([
                'status' => 'ERROR',
            ]);
            $this->socialPostLog($config->id, $ad->id, $config->platform, 'error', $e);
            Session::flash('message', $e);

            return redirect()->route('social.ad.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditSocialAdRequest $request): RedirectResponse
    {
        $config           = SocialAd::findorfail($request->id);
        $data             = $request->except('_token', 'id');
        $data['password'] = Crypt::encrypt($request->password);
        $config->fill($data);
        $config->save();

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        $config = SocialAd::findorfail($request->id);
        $config->delete();

        return response()->json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialAd')->orderByDesc('created_at')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }

    public function getpost(Request $request): JsonResponse
    {
        $postData = SocialConfig::where('ad_account_id', $request->id)->with('posts')->first()->toArray();

        return response()->json($postData['posts']);
    }

    public function getAdsets(Request $request): JsonResponse
    {
        $adsets      = SocialAdset::where('config_id', $request->id)->get()->toArray();
        $adCreatives = SocialAdCreative::where('config_id', $request->id)->get()->toArray();

        return response()->json(['adsets' => $adsets, 'adcreatives' => $adCreatives]);
    }
}
