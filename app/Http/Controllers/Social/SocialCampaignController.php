<?php

namespace App\Http\Controllers\Social;

use App\Http\Requests\Social\EditSocialCampaignRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Setting;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Social\SocialPostLog;
use App\Social\SocialCampaign;
use App\Models\SocialAdAccount;
use App\Http\Controllers\Controller;
use Exception;

class SocialCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|View
     */
    public function index(Request $request)
    {
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $campaigns = SocialCampaign::orderByDesc('id')->with('account.storeWebsite');
        } else {
            $campaigns = SocialCampaign::latest()->with('account.storeWebsite');
        }

        $configs = SocialAdAccount::pluck('name', 'id');

        if (! empty($request->date)) {
            $campaigns->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if (! empty($request->config_name)) {
            $campaigns->whereIn('config_id', $request->config_name);
        }

        if (! empty($request->campaign_name)) {
            $campaigns->where('name', $request->campaign_name);
        }

        if (! empty($request->objective)) {
            $campaigns->whereIn('objective_name', $request->objective);
        }

        if (! empty($request->type)) {
            $type = $request->type;
            $campaigns->where('buying_type', 'LIKE', '%' . $request->type . '%');
        }

        if (! empty($request->status)) {
            $status = $request->status;
            $campaigns->where('status', 'LIKE', '%' . $request->status . '%');
        }

        $campaigns = $campaigns->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.campaigns.data', compact('campaigns', 'configs', 'type', 'status'))->render(),
                'links' => (string) $campaigns->render(),
            ]);
        }

        return view('social.campaigns.index', compact('campaigns', 'configs'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log                  = new SocialPostLog();
        $Log->config_id       = $config_id;
        $Log->post_id         = $post_id;
        $Log->platform        = $platform;
        $Log->log_title       = $title;
        $Log->log_description = $description;
        $Log->modal           = 'SocialCampaign';
        $Log->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $configs = SocialAdAccount::pluck('name', 'id');
        $objectives = SocialCampaign::OBJECTIVES;
        $special_ad_categories = SocialCampaign::SPECIAL_AD_CATEGORIES;

        return view('social.campaigns.create', compact('configs', 'objectives', 'special_ad_categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $categories = $request->special_ad_categories ? implode(", ", $request->special_ad_categories) : null;

        $post = SocialCampaign::create([
            'config_id'      => $request->config_id,
            'name'           => $request->name,
            'objective_name' => $request->objective,
            'buying_type'    => $request->buying_type,
            'daily_budget'   => $request->daily_budget,
            'status'         => $request->status,
            'special_ad_categories' => $categories
        ]);

        $data['name']                  = $request->input('name');
        $data['objective']             = $request->input('objective');
        $data['status']                = $request->input('status');

        if ($request->special_ad_categories) {
            $data['special_ad_categories'] = json_encode($request->special_ad_categories);
            $data['special_ad_category_country'] = ['AE', 'IN', 'US'];
        } else {
            $data['special_ad_categories'] = json_encode([]);
        }

        if ($request->daily_budget) {
            $data['daily_budget']          = $request->daily_budget;
        }

        if ($request->has('buying_type')) {
            $data['buying_type'] = $request->input('buying_type');
        } else {
            $data['buying_type'] = 'AUCTION';
        }
        $config = SocialAdAccount::find($request->config_id);

        $fb = new FB($config);
        $this->socialPostLog($config->id, '', 'facebook', 'message', 'get page access token');
        try {
            $response              = $fb->createCampaign($config->ad_account_id, $data);
            $post->live_status     = 'sucess';
            $post->ref_campaign_id = $response['id'];
            $post->save();
            Session::flash('message', 'Campaign created  successfully');

            return redirect()->route('social.campaign.index');
        } catch (Exception $e) {
            $post->live_status = 'error';
            $post->save();
            Session::flash('message', $e);
            $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);

            return redirect()->route('social.campaign.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SocialCampaign $SocialCampaign
     */
    public function edit(EditSocialCampaignRequest $request): RedirectResponse
    {
        $config           = SocialCampaign::findorfail($request->id);
        $data             = $request->except('_token', 'id');
        $data['password'] = Crypt::encrypt($request->password);
        $config->fill($data);
        $config->save();

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\SocialCampaign $SocialCampaign
     */
    public function destroy(Request $request): JsonResponse
    {
        $config = SocialCampaign::findorfail($request->id);
        $config->delete();

        return response()->json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialCampaign')->orderByDesc('created_at')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }
}
