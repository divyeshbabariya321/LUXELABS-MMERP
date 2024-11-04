<?php

namespace App\Http\Controllers\Social;

use App\Http\Requests\Social\EditSocialAdCreativeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Setting;
use Illuminate\View\View;
use App\Social\SocialPost;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Social\SocialPostLog;
use App\Social\SocialCampaign;
use App\Models\SocialAdAccount;
use App\Social\SocialAdCreative;
use App\Http\Controllers\Controller;
use Exception;

class SocialAdCreativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|View
     */
    public function index(Request $request)
    {
        $configs = SocialAdAccount::pluck('name', 'id');

        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $adcreatives = SocialAdCreative::orderByDesc('id')->with('account.storeWebsite');
        } else {
            $adcreatives = SocialAdCreative::latest()->with('account.storeWebsite');
        }

        if (! empty($request->date)) {
            $adcreatives->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if (! empty($request->config_name)) {
            $adcreatives->whereIn('config_id', $request->config_name);
        }

        if (! empty($request->campaign_name)) {
            $adcreatives->whereIn('campaign_id', $request->campaign_name);
        }

        if (! empty($request->name)) {
            $adcreatives->whereIn('name', $request->name);
        }

        $adcreatives = $adcreatives->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.adcreatives.data', compact('adcreatives', 'configs'))->render(),
                'links' => (string) $adcreatives->render(),
            ]);
        }

        return view('social.adcreatives.index', compact('adcreatives', 'configs'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log                  = new SocialPostLog();
        $Log->config_id       = $config_id;
        $Log->post_id         = $post_id;
        $Log->platform        = $platform;
        $Log->log_title       = $title;
        $Log->log_description = $description;
        $Log->modal           = 'SocialAdCreative';
        $Log->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $configs    = SocialAdAccount::pluck('name', 'id');
        $campaingns = SocialCampaign::whereNotNull('ref_campaign_id')->get();

        return view('social.adcreatives.create', compact('configs', 'campaingns'));
    }

    public function getpost(Request $request): JsonResponse
    {
        $postData = SocialConfig::where('ad_account_id', $request->id)->with('posts')->first()->toArray();

        return response()->json($postData['posts']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $ad_creative = SocialAdCreative::create([
            'config_id'          => $request->config_id,
            'object_story_title' => $request->object_story_title,
            'object_story_id'    => $request->object_story_id,
            'name'               => $request->name,

        ]);

        // Get reference Post ID. DEVTASK-24765
        $ref_post    = SocialPost::where('id', $request->object_story_id)->first();
        $ref_post_id = $ref_post->ref_post_id;

        $data['name']            = $request->input('name');
        $data['object_story_id'] = $ref_post_id;

        $config = SocialAdAccount::find($ad_creative->config_id);
        $fb     = new FB($config);

        $this->socialPostLog($config->id, $ad_creative->id, $config->platform, 'message', 'get page access token');

        try {
            $response = $fb->createAdCreatives($config->ad_account_id, $data);
            if ($response['id']) {
                $ad_creative->update([
                    'ref_adcreative_id' => $response['id'],
                ]);
            }
            $ad_creative->update([
                'live_status' => 'ACTIVE',
            ]);
            Session::flash('message', 'adcreative created  successfully');

            return redirect()->route('social.adcreative.index');
        } catch (Exception $e) {
            $this->socialPostLog($config->id, $ad_creative->id, $config->platform, 'error', $e);
            $ad_creative->update([
                'live_status' => 'ERROR',
            ]);
            Session::flash('message', $e);

            return redirect()->route('social.adcreative.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SocialAdCreative $SocialAdCreative
     */
    public function edit(EditSocialAdCreativeRequest $request): RedirectResponse
    {
        $config           = SocialAdCreative::findorfail($request->id);
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
        $config = SocialAdCreative::findorfail($request->id);
        $config->delete();

        return response()->json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialAdCreative')->orderByDesc('created_at')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }
}
