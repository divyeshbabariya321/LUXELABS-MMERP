<?php

namespace App\Http\Controllers\Pinterest;

use App\Http\Controllers\Controller;
use App\PinterestAds;
use App\PinterestAdsAccounts;
use App\PinterestAdsGroups;
use App\PinterestBusinessAccountMails;
use App\PinterestCampaigns;
use App\PinterestPins;
use App\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PinterestCampaignsController extends Controller
{
    /**
     * Get all campaigns for a account.
     *
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function campaignsIndex(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestCampaigns = PinterestCampaigns::with(['account'])
                ->where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                    $query->whereHas('account', function ($query2) use ($pinterestBusinessAccountMail) {
                        $query2->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                    });
                    if ($request->has('name') && $request->name) {
                        $query->where('name', 'like', '%'.$request->name.'%');
                    }
                })->paginate(Setting::get('pagination'), ['*'], 'pins');
            $pinterestAdsAccounts = PinterestAdsAccounts::where('pinterest_mail_id', $pinterestBusinessAccountMail->id)->pluck('ads_account_name', 'id')->toArray();

            return view('pinterest.campaigns-index', compact('pinterestBusinessAccountMail', 'pinterestCampaigns', 'pinterestAdsAccounts'));
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Campaign.
     *
     * @param  mixed  $id
     */
    public function createCampaign(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'pinterest_ads_account_id' => 'required',
                'name' => 'required',
                'status' => 'required|in:ACTIVE,PAUSED,ARCHIVED',
                'lifetime_spend_cap' => 'required_without:daily_spend_cap|integer|nullable',
                'daily_spend_cap' => 'required_without:lifetime_spend_cap|integer|nullable',
                'start_time' => 'required|date',
                'end_time' => 'required|date',
                'summary_status' => 'sometimes|nullable|in:RUNNING,PAUSED,NOT_STARTED,COMPLETED,ADVERTISER_DISABLED,ARCHIVED',
                'is_flexible_daily_budgets' => '',
                'objective_type' => 'required|in:AWARENESS,CONSIDERATION,VIDEO_VIEW,WEB_CONVERSION,CATALOG_SALES',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestAdsAccount = PinterestAdsAccounts::where('id', $request->get('pinterest_ads_account_id'))->first();
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $this->buildTrackingUrls($request->all());
            $response = $pinterest->createCampaign($pinterestAdsAccount->ads_account_id, [
                [
                    'ad_account_id' => $pinterestAdsAccount->ads_account_id,
                    'name' => $request->get('name'),
                    'status' => $request->get('status', 'ACTIVE'),
                    'lifetime_spend_cap' => $request->has('lifetime_spend_cap') ? $request->get('lifetime_spend_cap', 0) * 1000000 : null,
                    'daily_spend_cap' => $request->has('daily_spend_cap') ? $request->get('daily_spend_cap', 0) * 1000000 : null,
                    'start_time' => $request->has('start_time') && $request->start_time ? strtotime($request->get('start_time')) : null,
                    'end_time' => $request->has('end_time') && $request->end_time ? strtotime($request->get('end_time')) : null,
                    'summary_status' => $request->get('summary_status'),
                    'is_campaign_budget_optimization' => false,
                    'is_flexible_daily_budgets' => $request->has('is_flexible_daily_budgets') ? $request->get('is_flexible_daily_budgets') == 'true' : false,
                    'default_ad_group_budget_in_micro_currency' => null,
                    'is_automated_campaign' => false,
                    'objective_type' => $request->get('objective_type'),
                ],
            ]);
            if ($response['status']) {
                PinterestCampaigns::create([
                    'pinterest_ads_account_id' => $pinterestAdsAccount->id,
                    'campaign_id' => $response['data']['items'][0]['data']['id'],
                    'name' => $request->get('name'),
                    'status' => $request->get('status', 'ACTIVE'),
                    'lifetime_spend_cap' => $request->has('lifetime_spend_cap') ? $request->get('lifetime_spend_cap', 0) : null,
                    'daily_spend_cap' => $request->has('daily_spend_cap') ? $request->get('daily_spend_cap', 0) : null,
                    'start_time' => $request->has('start_time') && $request->start_time ? strtotime($request->get('start_time')) : null,
                    'end_time' => $request->has('end_time') && $request->end_time ? strtotime($request->get('end_time')) : null,
                    'summary_status' => $request->get('summary_status'),
                    'is_campaign_budget_optimization' => false,
                    'is_flexible_daily_budgets' => $request->has('is_flexible_daily_budgets') ? $request->get('is_flexible_daily_budgets') == 'true' : false,
                    'default_ad_group_budget_in_micro_currency' => null,
                    'is_automated_campaign' => false,
                    'objective_type' => $request->get('objective_type'),
                ]);

                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('success', 'Campaign created successfully.');
            } else {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.campaign.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Build Tracking URL object
     *
     * @param  mixed  $params
     * @param  mixed  $isEdit
     */
    public function buildTrackingUrls($params, $isEdit = false): array
    {
        $urls = [
            'impression' => [],
            'click' => [],
            'engagement' => [],
            'buyable_button' => [],
            'audience_verification' => [],
        ];
        if (isset($params[$isEdit ? 'edit_tracking_urls_impression' : 'tracking_urls_impression'])) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_impression' : 'tracking_urls_impression']);
            if (count($values) > 0) {
                $urls['impression'] = $values;
            } else {
                unset($urls['impression']);
            }
        } else {
            unset($urls['impression']);
        }
        if (isset($params[$isEdit ? 'edit_tracking_urls_click' : 'tracking_urls_click']) && count($params[$isEdit ? 'edit_tracking_urls_click' : 'tracking_urls_click']) > 0) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_click' : 'tracking_urls_click']);
            if (count($values) > 0) {
                $urls['click'] = $values;
            } else {
                unset($urls['click']);
            }
        } else {
            unset($urls['click']);
        }
        if (isset($params[$isEdit ? 'edit_tracking_urls_engagement' : 'tracking_urls_engagement']) && count($params[$isEdit ? 'edit_tracking_urls_engagement' : 'tracking_urls_engagement']) > 0) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_engagement' : 'tracking_urls_engagement']);
            if (count($values) > 0) {
                $urls['engagement'] = $values;
            } else {
                unset($urls['engagement']);
            }
        } else {
            unset($urls['engagement']);
        }
        if (isset($params[$isEdit ? 'edit_tracking_urls_buyable_button' : 'tracking_urls_buyable_button']) && count($params[$isEdit ? 'edit_tracking_urls_buyable_button' : 'tracking_urls_buyable_button']) > 0) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_buyable_button' : 'tracking_urls_buyable_button']);
            if (count($values) > 0) {
                $urls['buyable_button'] = $values;
            } else {
                unset($urls['buyable_button']);
            }
        } else {
            unset($urls['buyable_button']);
        }
        if (isset($params[$isEdit ? 'edit_tracking_urls_audience_verification' : 'tracking_urls_audience_verification']) && count($params[$isEdit ? 'edit_tracking_urls_audience_verification' : 'tracking_urls_audience_verification']) > 0) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_audience_verification' : 'tracking_urls_audience_verification']);
            if (count($values) > 0) {
                $urls['audience_verification'] = $values;
            } else {
                unset($urls['audience_verification']);
            }
        } else {
            unset($urls['audience_verification']);
        }

        return $urls;
    }

    /**
     * Get Campaign Details
     *
     * @param  mixed  $id
     * @param  mixed  $campaignId
     */
    public function getCampaign($id, $campaignId): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            $pinterestCampaign = PinterestCampaigns::findOrFail($campaignId);
            if (! $pinterestCampaign) {
                return response()->json(['status' => false, 'message' => 'Campaign not found']);
            }

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $pinterestCampaign->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a Campaign.
     *
     * @param  mixed  $id
     */
    public function updateCampaign(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'edit_campaign_id' => 'required',
                'edit_pinterest_ads_account_id' => 'required',
                'edit_name' => 'required',
                'edit_status' => 'required|in:ACTIVE,PAUSED,ARCHIVED',
                'edit_lifetime_spend_cap' => 'required_without:edit_daily_spend_cap|integer|nullable',
                'edit_daily_spend_cap' => 'required_without:edit_lifetime_spend_cap|integer|nullable',
                'edit_start_time' => 'required|date',
                'edit_end_time' => 'required|date',
                'edit_summary_status' => 'sometimes|nullable|in:RUNNING,PAUSED,NOT_STARTED,COMPLETED,ADVERTISER_DISABLED,ARCHIVED',
                'edit_is_flexible_daily_budgets' => '',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestCampaign = PinterestCampaigns::where('id', $request->get('edit_campaign_id'))->first();
            $pinterestAdsAccount = PinterestAdsAccounts::where('id', $request->get('edit_pinterest_ads_account_id'))->first();
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $this->buildTrackingUrls($request->all(), true);
            $response = $pinterest->updateCampaign($pinterestAdsAccount->ads_account_id, [
                [
                    'id' => $pinterestCampaign->campaign_id,
                    'ad_account_id' => $pinterestAdsAccount->ads_account_id,
                    'name' => $request->get('edit_name'),
                    'status' => $request->get('edit_status', 'ACTIVE'),
                    'lifetime_spend_cap' => $request->has('edit_lifetime_spend_cap') && $request->edit_lifetime_spend_cap ? $request->get('edit_lifetime_spend_cap', 0) * 1000000 : null,
                    'daily_spend_cap' => $request->has('edit_daily_spend_cap') && $request->edit_daily_spend_cap ? $request->get('edit_daily_spend_cap', 0) * 1000000 : null,
                    'start_time' => $request->has('edit_start_time') && $request->edit_start_time ? strtotime($request->get('edit_start_time')) * 1000 : null,
                    'end_time' => $request->has('edit_end_time') && $request->edit_end_time ? strtotime($request->get('edit_end_time')) * 1000 : null,
                    'summary_status' => $request->get('edit_summary_status'),
                    'is_campaign_budget_optimization' => false,
                    'is_flexible_daily_budgets' => $request->has('edit_is_flexible_daily_budgets') ? $request->get('edit_is_flexible_daily_budgets') == 'true' : false,
                    'default_ad_group_budget_in_micro_currency' => null,
                    'is_automated_campaign' => false,
                ],
            ]);
            if ($response['status']) {
                $pinterestCampaign->pinterest_ads_account_id = $pinterestAdsAccount->id;
                $pinterestCampaign->name = $request->get('edit_name');
                $pinterestCampaign->status = $request->get('edit_status', 'ACTIVE');
                $pinterestCampaign->lifetime_spend_cap = $request->has('edit_lifetime_spend_cap') && $request->edit_lifetime_spend_cap ? $request->get('edit_lifetime_spend_cap', 0) : null;
                $pinterestCampaign->daily_spend_cap = $request->has('edit_daily_spend_cap') && $request->edit_daily_spend_cap ? $request->get('edit_daily_spend_cap', 0) : null;
                $pinterestCampaign->start_time = $request->has('edit_start_time') && $request->edit_start_time ? strtotime($request->get('edit_start_time')) * 1000 : null;
                $pinterestCampaign->end_time = $request->has('edit_end_time') && $request->edit_end_time ? strtotime($request->get('edit_end_time')) * 1000 : null;
                $pinterestCampaign->summary_status = $request->get('edit_summary_status');
                $pinterestCampaign->is_campaign_budget_optimization = false;
                $pinterestCampaign->is_flexible_daily_budgets = $request->has('edit_is_flexible_daily_budgets') ? $request->get('edit_is_flexible_daily_budgets') == 'true' : false;
                $pinterestCampaign->default_ad_group_budget_in_micro_currency = null;
                $pinterestCampaign->is_automated_campaign = false;
                $pinterestCampaign->save();

                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('success', 'Campaign updated successfully.');
            } else {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.campaign.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get all Ads group for a account.
     *
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function adsGroupIndex(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestAdsGroups = PinterestAdsGroups::with(['account'])
                ->where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                    $query->whereHas('account', function ($query2) use ($pinterestBusinessAccountMail) {
                        $query2->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                    });
                    if ($request->has('name') && $request->name) {
                        $query->where('name', 'like', '%'.$request->name.'%');
                    }
                })->paginate(Setting::get('pagination'), ['*'], 'pins');
            $pinterestCampaigns = PinterestCampaigns::whereHas('account', function ($query) use ($pinterestBusinessAccountMail) {
                $query->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
            })->pluck('name', 'id')->toArray();

            return view('pinterest.ads-group-index', compact('pinterestBusinessAccountMail', 'pinterestCampaigns', 'pinterestAdsGroups'));
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Ads Group.
     *
     * @param  mixed  $id
     */
    public function createAdsGroup(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'pinterest_campaign_id' => 'required',
                'name' => 'required',
                'status' => 'required|in:ACTIVE,PAUSED,ARCHIVED',
                'budget_in_micro_currency' => 'sometimes|integer|nullable',
                'bid_in_micro_currency' => 'sometimes|integer|nullable',
                'budget_type' => 'required|in:DAILY,LIFETIME',
                'start_time' => 'required|date',
                'end_time' => 'required|date',
                'lifetime_frequency_cap' => 'sometimes|integer|nullable',
                'placement_group' => 'required|in:ALL,SEARCH,BROWSE,OTHER',
                'pacing_delivery_type' => 'required|in:STANDARD,ACCELERATED',
                'billable_event' => 'required|in:CLICKTHROUGH,IMPRESSION,VIDEO_V_50_MRC',
                'bid_strategy_type' => 'sometimes|nullable|in:AUTOMATIC_BID,MAX_BID,TARGET_AVG',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestCampaign = PinterestCampaigns::with('account')->where('id', $request->get('pinterest_campaign_id'))->first();
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $response = $pinterest->createAdsGroup($pinterestCampaign->account->ads_account_id, [
                [
                    'name' => $request->get('name'),
                    'status' => $request->get('status', 'ACTIVE'),
                    'budget_in_micro_currency' => $request->has('budget_in_micro_currency') && $request->budget_in_micro_currency ? $request->get('budget_in_micro_currency', 0) * 1000000 : null,
                    'bid_in_micro_currency' => $request->has('bid_in_micro_currency') && $request->bid_in_micro_currency ? $request->get('bid_in_micro_currency', 0) * 1000000 : null,
                    'budget_type' => $request->get('budget_type'),
                    'start_time' => $request->has('start_time') && $request->start_time ? strtotime($request->get('start_time')) : null,
                    'end_time' => $request->has('end_time') && $request->end_time ? strtotime($request->get('end_time')) : null,
                    'lifetime_frequency_cap' => $request->has('lifetime_frequency_cap') && $request->lifetime_frequency_cap ? $request->get('lifetime_frequency_cap', 0) * 1000000 : null,
                    'placement_group' => $request->get('placement_group'),
                    'campaign_id' => $pinterestCampaign->campaign_id,
                    'pacing_delivery_type' => $request->get('pacing_delivery_type'),
                    'billable_event' => $request->get('billable_event'),
                    'bid_strategy_type' => $request->get('bid_strategy_type'),
                ],
            ]);
            if ($response['status']) {
                PinterestAdsGroups::create([
                    'name' => $request->get('name'),
                    'status' => $request->get('status', 'ACTIVE'),
                    'budget_in_micro_currency' => $request->has('budget_in_micro_currency') && $request->budget_in_micro_currency ? $request->get('budget_in_micro_currency', 0) : null,
                    'bid_in_micro_currency' => $request->has('bid_in_micro_currency') && $request->bid_in_micro_currency ? $request->get('bid_in_micro_currency', 0) : null,
                    'budget_type' => $request->get('budget_type'),
                    'start_time' => $request->has('start_time') && $request->start_time ? strtotime($request->get('start_time')) : null,
                    'end_time' => $request->has('end_time') && $request->end_time ? strtotime($request->get('end_time')) : null,
                    'lifetime_frequency_cap' => $request->has('lifetime_frequency_cap') && $request->lifetime_frequency_cap ? $request->get('lifetime_frequency_cap', 0) : null,
                    'placement_group' => $request->get('placement_group'),
                    'pacing_delivery_type' => $request->get('pacing_delivery_type'),
                    'billable_event' => $request->get('billable_event'),
                    'bid_strategy_type' => $request->get('bid_strategy_type'),
                    'pinterest_ads_account_id' => $pinterestCampaign->account->id,
                    'pinterest_campaign_id' => $pinterestCampaign->id,
                    'ads_group_id' => $response['data']['items'][0]['data']['id'],
                ]);

                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('success', 'Ads Group created successfully.');
            } else {
                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Ads group Details
     *
     * @param  mixed  $id
     * @param  mixed  $adsGroupId
     */
    public function getAdsGroup($id, $adsGroupId): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            $pinterestAdsGroup = PinterestAdsGroups::findOrFail($adsGroupId);
            if (! $pinterestAdsGroup) {
                return response()->json(['status' => false, 'message' => 'Campaign not found']);
            }

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $pinterestAdsGroup->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a Ads Group.
     *
     * @param  mixed  $id
     */
    public function updateAdsGroup(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'edit_ads_group_id' => 'required',
                'edit_pinterest_campaign_id' => 'required',
                'edit_name' => 'required',
                'edit_status' => 'required|in:ACTIVE,PAUSED,ARCHIVED',
                'edit_budget_in_micro_currency' => 'sometimes|integer|nullable',
                'edit_bid_in_micro_currency' => 'sometimes|integer|nullable',
                'edit_budget_type' => 'required|in:DAILY,LIFETIME',
                'edit_start_time' => 'required|date',
                'edit_end_time' => 'required|date',
                'edit_lifetime_frequency_cap' => 'sometimes|integer|nullable',
                'edit_placement_group' => 'required|in:ALL,SEARCH,BROWSE,OTHER',
                'edit_pacing_delivery_type' => 'required|in:STANDARD,ACCELERATED',
                'edit_billable_event' => 'required|in:CLICKTHROUGH,IMPRESSION,VIDEO_V_50_MRC',
                'edit_bid_strategy_type' => 'sometimes|nullable|in:AUTOMATIC_BID,MAX_BID,TARGET_AVG',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestAdsGroup = PinterestAdsGroups::where('id', $request->get('edit_ads_group_id'))->first();
            $pinterestCampaign = PinterestCampaigns::with('account')->where('id', $request->get('edit_pinterest_campaign_id'))->first();
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $response = $pinterest->updateAdsGroup($pinterestCampaign->account->ads_account_id, [
                [
                    'id' => $pinterestAdsGroup->ads_group_id,
                    'campaign_id' => $pinterestCampaign->campaign_id,
                    'name' => $request->get('edit_name'),
                    'status' => $request->get('edit_status', 'ACTIVE'),
                    'budget_in_micro_currency' => $request->has('edit_budget_in_micro_currency') && $request->edit_budget_in_micro_currency ? $request->get('edit_budget_in_micro_currency', 0) * 1000000 : null,
                    'bid_in_micro_currency' => $request->has('edit_bid_in_micro_currency') && $request->edit_bid_in_micro_currency ? $request->get('edit_bid_in_micro_currency', 0) * 1000000 : null,
                    'budget_type' => $request->get('edit_budget_type'),
                    'end_time' => $request->has('edit_end_time') && $request->edit_end_time ? strtotime($request->get('edit_end_time')) : null,
                    'lifetime_frequency_cap' => $request->has('edit_lifetime_frequency_cap') && $request->edit_lifetime_frequency_cap ? $request->get('edit_lifetime_frequency_cap', 0) * 1000000 : null,
                    'placement_group' => $request->get('edit_placement_group'),
                    'pacing_delivery_type' => $request->get('edit_pacing_delivery_type'),
                    'billable_event' => $request->get('edit_billable_event'),
                    'bid_strategy_type' => $request->get('edit_bid_strategy_type'),
                ],
            ]);
            if ($response['status']) {
                $pinterestAdsGroup->pinterest_ads_account_id = $pinterestCampaign->account->id;
                $pinterestAdsGroup->pinterest_campaign_id = $pinterestCampaign->id;
                $pinterestAdsGroup->name = $request->get('edit_name');
                $pinterestAdsGroup->status = $request->get('edit_status', 'ACTIVE');
                $pinterestAdsGroup->budget_in_micro_currency = $request->has('edit_lifetime_spend_cap') && $request->edit_lifetime_spend_cap ? $request->get('edit_lifetime_spend_cap', 0) : null;
                $pinterestAdsGroup->bid_in_micro_currency = $request->has('edit_daily_spend_cap') && $request->edit_daily_spend_cap ? $request->get('edit_daily_spend_cap', 0) : null;
                $pinterestAdsGroup->budget_type = $request->get('edit_budget_type');
                $pinterestAdsGroup->end_time = $request->has('edit_end_time') && $request->edit_end_time ? strtotime($request->get('edit_end_time')) : null;
                $pinterestAdsGroup->lifetime_frequency_cap = $request->has('edit_lifetime_frequency_cap') && $request->edit_lifetime_frequency_cap ? $request->get('edit_lifetime_frequency_cap', 0) : null;
                $pinterestAdsGroup->placement_group = $request->get('edit_placement_group');
                $pinterestAdsGroup->pacing_delivery_type = $request->get('edit_pacing_delivery_type');
                $pinterestAdsGroup->billable_event = $request->get('edit_billable_event');
                $pinterestAdsGroup->bid_strategy_type = $request->get('edit_bid_strategy_type');
                $pinterestAdsGroup->save();

                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('success', 'Ads Group updated successfully.');
            } else {
                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get all Ads for a account.
     *
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function adsIndex(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestAds = PinterestAds::with(['account', 'adsGroup', 'pin'])
                ->where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                    $query->whereHas('account', function ($query2) use ($pinterestBusinessAccountMail) {
                        $query2->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                    });
                    if ($request->has('name') && $request->name) {
                        $query->where('name', 'like', '%'.$request->name.'%');
                    }
                })->paginate(Setting::get('pagination'), ['*'], 'pins');
            $pinterestAdsGroups = PinterestAdsGroups::whereHas('account', function ($query) use ($pinterestBusinessAccountMail) {
                $query->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
            })->pluck('name', 'id')->toArray();
            $pinterestPins = PinterestPins::whereHas('account', function ($query) use ($pinterestBusinessAccountMail) {
                $query->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
            })->pluck('title', 'id')->toArray();

            return view('pinterest.ads-index', compact('pinterestBusinessAccountMail', 'pinterestAds', 'pinterestAdsGroups', 'pinterestPins'));
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Ads.
     *
     * @param  mixed  $id
     */
    public function createAds(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'pinterest_ad_group_id' => 'required',
                'pinterest_pin_id' => 'required',
                'creative_type' => 'required|in:REGULAR,VIDEO,SHOPPING,CAROUSEL,MAX_VIDEO,SHOP_THE_PIN,IDEA',
                'destination_url' => 'sometimes|url|nullable',
                'name' => 'sometimes|nullable',
                'status' => 'required|in:ACTIVE,PAUSED,ARCHIVED',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.ads.index', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestAdsGroup = PinterestAdsGroups::with('account')->where('id', $request->get('pinterest_ad_group_id'))->first();
            $pinterestPin = PinterestPins::where('id', $request->get('pinterest_pin_id'))->first();
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $response = $pinterest->createAd($pinterestAdsGroup->account->ads_account_id, [
                [
                    'ad_group_id' => $pinterestAdsGroup->ads_group_id,
                    'pin_id' => $pinterestPin->pin_id,
                    'creative_type' => $request->get('creative_type'),
                    'destination_url' => $request->has('destination_url') && $request->destination_url ? $request->get('destination_url') : null,
                    'name' => $request->has('name') && $request->name ? $request->get('name') : null,
                    'status' => $request->get('status'),
                ],
            ]);
            if ($response['status']) {
                PinterestAds::create([
                    'pinterest_ads_account_id' => $pinterestAdsGroup->account->id,
                    'pinterest_ads_group_id' => $pinterestAdsGroup->id,
                    'pinterest_pin_id' => $pinterestPin->id,
                    'ads_id' => $response['data']['items'][0]['data']['id'],
                    'creative_type' => $request->get('creative_type'),
                    'destination_url' => $request->has('destination_url') && $request->destination_url ? $request->get('destination_url') : null,
                    'name' => $request->has('name') && $request->name ? $request->get('name') : null,
                    'status' => $request->get('status'),
                ]);

                return Redirect::route('pinterest.accounts.ads.index', [$id])
                    ->with('success', 'Ads created successfully.');
            } else {
                return Redirect::route('pinterest.accounts.ads.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.ads.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Ads Details
     *
     * @param  mixed  $id
     * @param  mixed  $adsId
     */
    public function getAds($id, $adsId): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            $pinterestAds = PinterestAds::findOrFail($adsId);
            if (! $pinterestAds) {
                return response()->json(['status' => false, 'message' => 'Pin not found']);
            }

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $pinterestAds->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a Ads.
     *
     * @param  mixed  $id
     */
    public function updateAds(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.adsGroup.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'edit_ads_id' => 'required',
                'edit_pinterest_ad_group_id' => 'required',
                'edit_pinterest_pin_id' => 'required',
                'edit_creative_type' => 'required|in:REGULAR,VIDEO,SHOPPING,CAROUSEL,MAX_VIDEO,SHOP_THE_PIN,IDEA',
                'edit_destination_url' => 'sometimes',
                'edit_name' => 'sometimes',
                'edit_status' => 'required|in:ACTIVE,PAUSED,ARCHIVED',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.ads.index', [$id])
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestAds = PinterestAds::where('id', $request->get('edit_ads_id'))->first();
            $pinterestAdsGroup = PinterestAdsGroups::with('account')->where('id', $request->get('edit_pinterest_ad_group_id'))->first();
            $pinterestPin = PinterestPins::where('id', $request->get('edit_pinterest_pin_id'))->first();
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $response = $pinterest->updateAd($pinterestAdsGroup->account->ads_account_id, [
                [
                    'id' => $pinterestAds->ads_id,
                    'ad_group_id' => $pinterestAdsGroup->ads_group_id,
                    'pin_id' => $pinterestPin->pin_id,
                    'creative_type' => $request->get('edit_creative_type'),
                    'destination_url' => $request->has('edit_destination_url') && $request->edit_destination_url ? $request->get('edit_destination_url') : null,
                    'name' => $request->has('edit_name') && $request->edit_name ? $request->get('edit_name') : null,
                    'status' => $request->get('edit_status'),
                ],
            ]);
            if ($response['status']) {
                $pinterestAds->pinterest_ads_account_id = $pinterestAdsGroup->account->id;
                $pinterestAds->pinterest_ads_group_id = $pinterestAdsGroup->id;
                $pinterestAds->pinterest_pin_id = $pinterestPin->id;
                $pinterestAds->creative_type = $request->get('edit_creative_type');
                $pinterestAds->destination_url = $request->has('edit_destination_url') && $request->edit_destination_url ? $request->get('edit_destination_url') : null;
                $pinterestAds->name = $request->has('edit_name') && $request->edit_name ? $request->get('edit_name') : null;
                $pinterestAds->status = $request->get('edit_status');
                $pinterestAds->save();

                return Redirect::route('pinterest.accounts.ads.index', [$id])
                    ->with('success', 'Ads updated successfully.');
            } else {
                return Redirect::route('pinterest.accounts.ads.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.ads.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Pinterest Client
     *
     * @param  mixed  $pinterestAccount
     *
     * @throws Exception
     */
    public function getPinterestClient($pinterestAccount): PinterestService
    {
        $pinterest = new PinterestService($pinterestAccount->account->pinterest_client_id, $pinterestAccount->account->pinterest_client_secret, $pinterestAccount->account->id);
        $pinterest->updateAccessToken($pinterestAccount->pinterest_access_token);

        return $pinterest;
    }
}
