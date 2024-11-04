<?php

namespace App\Http\Controllers\Social;

use App\Http\Requests\Social\EditSocialAdsetRequest;
use App\Http\Requests\Social\StoreSocialAdsetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Setting;
use App\Social\SocialAdset;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Social\SocialPostLog;
use App\Social\SocialCampaign;
use App\Models\SocialAdAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use JanuSoftware\Facebook\Exception\SDKException;
use Exception;

class SocialAdsetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse | View
     */
    public function index(Request $request): JsonResponse
    {
        $configs = SocialAdAccount::pluck('name', 'id');
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $adsets = SocialAdset::orderByDesc('id')->with('account.storeWebsite', 'campaign');
        } else {
            $adsets = SocialAdset::latest()->with('account.storeWebsite', 'campaign');
        }

        if (! empty($request->date)) {
            $adsets->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if (! empty($request->config_name)) {
            $adsets->whereIn('config_id', $request->config_name);
        }

        if (! empty($request->campaign_name)) {
            $adsets->whereIn('campaign_id', $request->campaign_name);
        }

        if (! empty($request->event)) {
            $adsets->whereIn('billing_event', $request->event);
        }

        if (! empty($request->name)) {
            $adsets->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if (! empty($request->status)) {
            $adsets->where('status', 'LIKE', '%' . $request->status . '%');
        }

        $adsets = $adsets->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.adsets.data', compact('adsets', 'configs'))->render(),
                'links' => (string) $adsets->render(),
            ], 200);
        }

        return view('social.adsets.index', compact('adsets', 'configs'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log                  = new SocialPostLog();
        $Log->config_id       = $config_id;
        $Log->post_id         = $post_id;
        $Log->platform        = $platform;
        $Log->log_title       = $title;
        $Log->log_description = $description;
        $Log->modal           = 'SocialAdset';
        $Log->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $configs    = SocialAdAccount::pluck('name', 'id');
        $campaingns = SocialCampaign::where('ref_campaign_id', '!=', '')->get();
        $billing_events = SocialAdset::BILIING_EVENTS;

        return view('social.adsets.create', compact('configs', 'campaingns', 'billing_events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws SDKException
     */
    public function store(StoreSocialAdsetRequest $request): RedirectResponse
    {
        /* Removed daily budget as we already store budget on campaign level
            Add destination_type field, make bid_amount and optimization_goal field dynamic
            Error handling message not showing issue fix. DEVTASK-24765
        */

        $adset = SocialAdset::create([
            'config_id'     => $request->get('config_id'),
            'campaign_id'   => $request->get('campaign_id'),
            'name'          => $request->get('name'),
            'billing_event' => $request->get('billing_event'),
            'start_time'    => $request->get('start_time'),
            'end_time'      => $request->get('end_time'),
            'bid_amount'    => $request->get('bid_amount'),
            'status'        => $request->get('status'),
            'destination_type'        => $request->get('destination_type'),
        ]);

        $data['name']                   = $request->input('name');
        $data['status']                 = $request->input('status');

        $config  = SocialAdAccount::find($adset->config_id);
        $page_id = $config->social_configs?->page_id;
        $fb      = new FB($config);
        // $this->socialPostLog($config->id, $adset->id, $config->platform, 'message', 'get page access token');

        try {
            $data['name']          = $request->input('name');
            $data['campaign_id']   = $adset->campaign->ref_campaign_id;
            $data['billing_event'] = $request->input('billing_event');
            $data['bid_amount']    = $request->input('bid_amount');

            $data['OPTIMIZATION_GOAL'] = $request->input('optimization_goal');
            $data['targeting']         = ['geo_locations' => ['countries' => ['AE', 'IN', 'US']]];

            $data['bid_amount']      = $request->input('bid_amount');
            $data['daily_budget']    = $request->input('daily_budget');
            $data['status']          = $request->input('status');
            $data['start_time']      = $request->input('start_time');
            $data['end_time']        = $request->input('end_time');
            $data['promoted_object'] = ['page_id' => $page_id];

            $response = $fb->createAdsets($config->ad_account_id, $data);
            if ($response['id']) {
                $adset->update([
                    'ref_adset_id' => $response['id'],
                ]);
            }

            $this->socialPostLog($config->id, $adset->id, $config->platform, 'response->create adset', json_encode($response));
            $adset->update([
                'live_status' => 'ACTIVE',
                'status'      => 'ACTIVE',
            ]);
            Session::flash('message', 'adset created  successfully');

            return redirect()->route('social.adset.index');
        } catch (Exception $e) {
            $adset->update([
                'live_status' => 'ERROR',
            ]);
            $this->socialPostLog($config->id, $adset->id, $config->platform, 'error', $e);
            Session::flash('message', 'Unable to create adset');

            return redirect()->route('social.adset.index')->withErrors(['message' => 'Something went wrong']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SocialAdset $SocialAdset
     */
    public function edit(EditSocialAdsetRequest $request): RedirectResponse
    {
        $config           = SocialAdset::findorfail($request->id);
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
        $config = SocialAdset::findorfail($request->id);
        $config->delete();

        return response()->json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialAdset')->orderByDesc('created_at')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }

    // Add this function to handle Campaign Objective and Adset Optimization goal validations. DEVTASK-24765
    public function getOptimizationGoals($id): JsonResponse {
        $campaign = SocialCampaign::find($id);
        $objective = $campaign->objective_name;

        $goals = SocialCampaign::OBJECTIVE_OPTIMIZATION_GOAL;

        $goals_collect = collect($goals);
        $output = $goals_collect->where('objective', $objective)->first();

        return response()->json($output);
    }
}
