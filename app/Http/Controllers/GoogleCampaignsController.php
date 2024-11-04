<?php

namespace App\Http\Controllers;

use App\GoogleAd;
use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;
use App\GoogleAdsGroup;
use App\Helpers\GoogleAdsHelper;
use App\Http\Requests\CreateCampaignGoogleCampaignRequest;
use App\Http\Requests\UpdateCampaignGoogleCampaignRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\GoogleAdGroupKeyword;
use App\Models\GoogleAppAd;
use App\Models\GoogleAppAdImage;
use App\Models\GoogleCampaignLocation;
use App\Models\GoogleCampaignTargetLanguage;
use App\Models\GoogleResponsiveDisplayAd;
use App\Models\GoogleResponsiveDisplayAdMarketingImage;
use App\Models\State;
use Exception;
use Google\Ads\GoogleAds\Lib\V16\GoogleAdsClient;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\Util\V16\ResourceNames;
use Google\Ads\GoogleAds\V16\Common\AddressInfo;
use Google\Ads\GoogleAds\V16\Common\LanguageInfo;
use Google\Ads\GoogleAds\V16\Common\LocationInfo;
use Google\Ads\GoogleAds\V16\Common\ManualCpc;
use Google\Ads\GoogleAds\V16\Common\MaximizeConversionValue;
use Google\Ads\GoogleAds\V16\Common\ProximityInfo;
use Google\Ads\GoogleAds\V16\Common\TargetCpa;
use Google\Ads\GoogleAds\V16\Common\TargetRoas;
use Google\Ads\GoogleAds\V16\Common\TargetSpend;
use Google\Ads\GoogleAds\V16\Enums\AdvertisingChannelSubTypeEnum\AdvertisingChannelSubType;
use Google\Ads\GoogleAds\V16\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V16\Enums\AppCampaignAppStoreEnum\AppCampaignAppStore;
use Google\Ads\GoogleAds\V16\Enums\AppCampaignBiddingStrategyGoalTypeEnum\AppCampaignBiddingStrategyGoalType;
use Google\Ads\GoogleAds\V16\Enums\BiddingStrategyTypeEnum\BiddingStrategyType;
use Google\Ads\GoogleAds\V16\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V16\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V16\Enums\GeoTargetConstantStatusEnum\GeoTargetConstantStatus;
use Google\Ads\GoogleAds\V16\Enums\NegativeGeoTargetTypeEnum\NegativeGeoTargetType;
use Google\Ads\GoogleAds\V16\Enums\OptimizationGoalTypeEnum\OptimizationGoalType;
use Google\Ads\GoogleAds\V16\Enums\PositiveGeoTargetTypeEnum\PositiveGeoTargetType;
use Google\Ads\GoogleAds\V16\Enums\ProximityRadiusUnitsEnum\ProximityRadiusUnits;
use Google\Ads\GoogleAds\V16\Resources\Campaign;
use Google\Ads\GoogleAds\V16\Resources\Campaign\AppCampaignSetting;
use Google\Ads\GoogleAds\V16\Resources\Campaign\GeoTargetTypeSetting;
use Google\Ads\GoogleAds\V16\Resources\Campaign\NetworkSettings;
use Google\Ads\GoogleAds\V16\Resources\Campaign\OptimizationGoalSetting;
use Google\Ads\GoogleAds\V16\Resources\Campaign\ShoppingSetting;
use Google\Ads\GoogleAds\V16\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V16\Resources\CampaignCriterion;
use Google\Ads\GoogleAds\V16\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V16\Services\CampaignCriterionOperation;
use Google\Ads\GoogleAds\V16\Services\CampaignOperation;
use Google\Ads\GoogleAds\V16\Services\MutateCampaignBudgetsRequest;
use Google\Ads\GoogleAds\V16\Services\MutateCampaignsRequest;
use Google\Ads\GoogleAds\V16\Services\SuggestGeoTargetConstantsRequest\LocationNames;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GoogleCampaignsController extends Controller
{
    // show campaigns in main page
    public $exceptionError = 'Something went wrong';

    public function campaignslist(Request $request): View
    {
        $search_data = GoogleAdsCampaign::has('account')->with('account')->latest()->get();
        $campaignslist = GoogleAdsCampaign::has('account')->with('account')->latest();

        if (! empty($request->account_name)) {
            $campaignslist->where('account_id', $request->account_name);
        }
        if (! empty($request->campaign_name)) {
            $campaignslist->Where('campaign_name', $request->campaign_name);
        }
        if (! empty($request->channel_type)) {
            $campaignslist->where('channel_type', 'like', '%'.$request->channel_type.'%');
        }
        if (! empty($request->channel_sub_type)) {
            $campaignslist->where('channel_sub_type', 'like', '%'.$request->channel_sub_type.'%');
        }
        if (! empty($request->status)) {
            $campaignslist->where('status', 'like', '%'.$request->status.'%');
        }
        if (! empty($request->start_date)) {
            $campaignslist->where('start_date', 'like', '%'.$request->start_date.'%');
        }
        if (! empty($request->end_date)) {
            $campaignslist->where('end_date', 'like', '%'.$request->end_date.'%');
        }

        $campaignslist = $campaignslist->paginate(10)->appends(request()->except(['page']));

        $totalNumEntries = count($campaignslist);

        return view('googlecampaigns.google_campaignslist', compact('campaignslist', 'totalNumEntries', 'search_data'));
    }

    public function adslist(Request $request): View
    {
        $search_data = GoogleAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest()->get();
        $adslist = GoogleAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest();

        if (! empty($request->campaign_name)) {
            $adslist->where('adgroup_google_campaign_id', $request->campaign_name);
        }
        if (! empty($request->ad_group_name)) {
            $adslist->where('google_adgroup_id', $request->ad_group_name);
        }
        if (! empty($request->google_ad_id)) {
            $adslist->where('google_ad_id', $request->google_ad_id);
        }

        $adslist = $adslist->paginate(10)->appends(request()->except(['page']));

        $totalNumEntries = count($adslist);

        return view('googleads.ads_list', compact('adslist', 'totalNumEntries', 'search_data'));
    }

    public function appadlist(Request $request): View
    {
        $search_data = GoogleAppAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest()->get();
        $googleappadd = GoogleAppAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest();

        if (! empty($request->campaign_name)) {
            $googleappadd->where('adgroup_google_campaign_id', $request->campaign_name);
        }
        if (! empty($request->google_adgroup_id)) {
            $googleappadd->where('google_adgroup_id', $request->google_adgroup_id);
        }
        if (! empty($request->headline1)) {
            $googleappadd->where('headline1', $request->headline1);
        }

        $googleappadd = $googleappadd->paginate(10)->appends(request()->except(['page']));

        $totalentries = $googleappadd->count();

        return view('google_app_ad.appaddlist', compact('googleappadd', 'totalentries', 'search_data'));
    }

    public function display_ads(Request $request): View
    {
        $search_data = GoogleResponsiveDisplayAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest()->get();
        $display_ads = GoogleResponsiveDisplayAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest();

        if (! empty($request->campaign_name)) {
            $display_ads->where('adgroup_google_campaign_id', $request->campaign_name);
        }
        if (! empty($request->google_adgroup_id)) {
            $display_ads->where('google_adgroup_id', $request->google_adgroup_id);
        }
        if (! empty($request->headline1)) {
            $display_ads->where('headline1', $request->headline1);
        }

        $display_ads = $display_ads->paginate(10)->appends(request()->except(['page']));

        $totalNumEntries = count($display_ads);

        return view('google_responsive_display_ad.displayads_list', compact('display_ads', 'totalNumEntries', 'search_data'));
    }

    public function adsgroupslist(Request $request): View
    {
        $search_data = GoogleAdsGroup::has('campaign')->with('campaign', 'campaign.account')->latest()->get();
        $adsgroups = GoogleAdsGroup::has('campaign')->with('campaign', 'campaign.account')->latest();

        if (! empty($request->campaign_name)) {
            $adsgroups->where('adgroup_google_campaign_id', $request->campaign_name);
        }
        if (! empty($request->ad_group_name)) {
            $adsgroups->where('ad_group_name', $request->ad_group_name);
        }
        if (! empty($request->created_at)) {
            $adsgroups->where('created_at', 'like', '%'.$request->created_at.'%');
        }

        $adsgroups = $adsgroups->paginate(10)->appends(request()->except(['page']));

        $totalentries = $adsgroups->count();

        return view('googleadgroups.grouplist', compact('adsgroups', 'totalentries', 'search_data'));
    }

    public function index(Request $request)
    {
        if ($request->get('account_id')) {
            $account_id = $request->get('account_id');
            $account = GoogleAdsAccount::findOrFail($account_id);
        } else {
            return redirect()->to('/google-campaigns/ads-account');
        }

        $query = GoogleAdsCampaign::query();
        if ($request->googlecampaign_id) {
            $query = $query->where('google_campaign_id', $request->googlecampaign_id);
        }
        if ($request->googlecampaign_name) {
            $query = $query->where('campaign_name', 'LIKE', '%'.$request->googlecampaign_name.'%');
        }

        if ($request->googlecampaign_budget) {
            $query = $query->where('budget_amount', 'LIKE', '%'.$request->googlecampaign_budget.'%');
        }
        if ($request->start_date) {
            $query = $query->where('start_date', 'LIKE', '%'.$request->start_date.'%');
        }
        if ($request->end_date) {
            $query = $query->where('end_date', 'LIKE', '%'.$request->end_date.'%');
        }
        if ($request->budget_uniq_id) {
            $query = $query->where('budget_uniq_id', $request->budget_uniq_id);
        }

        if ($request->campaign_status) {
            $query = $query->where('status', $request->campaign_status);
        }

        $query->where('account_id', $account_id);
        $campInfo = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googlecampaigns.partials.list-adscampaign', ['campaigns' => $campInfo])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $campInfo->render(),
                'count' => $campInfo->total(),
            ], 200);
        }

        $totalEntries = $campInfo->count();

        $biddingStrategyTypes = $this->getBiddingStrategyTypeArray();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Campaign',
            'message' => 'Viewed campaign listing',
        ];
        insertGoogleAdsLog($input);

        return view('googlecampaigns.index', [
            'campaigns' => $campInfo,
            'totalNumEntries' => $totalEntries,
            'biddingStrategyTypes' => $biddingStrategyTypes,
            'account_id' => $account_id,
            'google_map_api_key' => $account->google_map_api_key,
        ]);
    }

    // go to create page
    public function createPage(): View
    {
        $biddingStrategyTypes = $this->getBiddingStrategyTypeArray();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Campaign',
            'message' => 'Viewed create campaign',
        ];
        insertGoogleAdsLog($input);

        return view('googlecampaigns.create', compact('biddingStrategyTypes'));
    }

    // create campaign
    public function createCampaign(CreateCampaignGoogleCampaignRequest $request)
    {
        $account_id = $request->account_id;
        $account = GoogleAdsAccount::findOrFail($account_id);
        $customerId = $account->google_customer_id;

        try {
            $campaignArray = [];
            $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $budgetAmount = $request->budgetAmount;
            $campaignName = $request->campaignName;
            $campaign_start_date = $request->start_date;
            $campaign_end_date = $request->end_date;
            $campaignStatus = $campaignStatusArr[$request->campaignStatus];
            $targetLanguages = $request->target_languages;

            //start creating array to store data into database
            $campaignArray['account_id'] = $account_id;
            $campaignArray['google_customer_id'] = $customerId;

            $campaignArray['campaign_name'] = $campaignName;
            $campaignArray['budget_amount'] = $request->budgetAmount;
            $campaignArray['start_date'] = $campaign_start_date;
            $campaignArray['end_date'] = $campaign_end_date;
            $campaignArray['status'] = $campaignStatus;
            if ($request->channel_type) {
                $channel_type = $request->channel_type;
            } else {
                $channel_type = 'SEARCH';
            }
            $campaignArray['channel_type'] = $channel_type;

            if ($request->channel_sub_type) {
                $channel_sub_type = $request->channel_sub_type;
            } else {
                $channel_sub_type = 'UNKNOWN';
            }
            $campaignArray['channel_sub_type'] = $channel_sub_type;

            if ($request->biddingStrategyType) {
                $bidding_strategy_type = $request->biddingStrategyType;
            } else {
                $bidding_strategy_type = 'UNKNOWN';
            }
            $campaignArray['bidding_strategy_type'] = $bidding_strategy_type;

            if ($request->txt_target_cpa) {
                $txt_target_cpa = $request->txt_target_cpa;
            } else {
                $txt_target_cpa = 0.0;
            }
            $campaignArray['target_cpa_value'] = $txt_target_cpa;

            if ($request->txt_target_roas) {
                $txt_target_roas = $request->txt_target_roas;
            } else {
                $txt_target_roas = 0.0;
            }
            $campaignArray['target_roas_value'] = $txt_target_roas;

            if ($request->txt_maximize_clicks) {
                $txt_maximize_clicks = $request->txt_maximize_clicks;
            } else {
                $txt_maximize_clicks = '';
            }
            $campaignArray['maximize_clicks'] = $txt_maximize_clicks;

            if ($request->ad_rotation) {
                $ad_rotation = $request->ad_rotation;
            } else {
                $ad_rotation = '';
            }
            $campaignArray['ad_rotation'] = $ad_rotation;

            if ($request->tracking_template_url) {
                $tracking_template_url = $request->tracking_template_url;
            } else {
                $tracking_template_url = '';
            }
            $campaignArray['tracking_template_url'] = $tracking_template_url;

            if ($request->final_url_suffix) {
                $final_url_suffix = $request->final_url_suffix;
            } else {
                $final_url_suffix = '';
            }
            $campaignArray['final_url_suffix'] = $final_url_suffix;

            $merchant_id = $account->google_merchant_center_account_id;
            $campaignArray['merchant_id'] = $merchant_id;

            if ($request->sales_country) {
                $sales_country = $request->sales_country;
            } else {
                $sales_country = '';
            }
            $campaignArray['sales_country'] = $sales_country;
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);
            $budget = self::addCampaignBudget($googleAdsClient, $customerId, $budgetAmount, $channel_type);
            $campaignArray['budget_uniq_id'] = $budget['budget_uniq_id'] ?? null;
            $campaignArray['budget_id'] = $budget['budget_id'] ?? null;
            $budgetResourceName = $budget['budget_resource_name'] ?? null;
            // Creates a campaign.
            $campaignArr = [
                'name' => $campaignName,
                'campaign_budget' => $budgetResourceName,
                'status' => self::getCampaignStatus($campaignStatus),
                'advertising_channel_type' => self::getAdvertisingChannelType($channel_type),
                'network_settings' => self::getNetworkSettings($channel_type, $channel_sub_type),
                'shopping_setting' => ($channel_type === 'SHOPPING') ? self::getShoppingSetting($merchant_id, $sales_country) : null,
                'geo_target_type_setting' => self::getGeoTargetTypeSetting(),
                'bidding_strategy_type' => self::getBiddingStrategyType($bidding_strategy_type),
                'start_date' => $campaign_start_date,
                'end_date' => $campaign_end_date,
            ];

            if ($channel_type == 'PERFORMANCE_MAX') {
                $campaignArr['url_expansion_opt_out'] = false;
                unset($campaignArr['advertising_channel_sub_type']);
            } else {
                $campaignArr['advertising_channel_sub_type'] = self::getAdvertisingChannelSubType($channel_sub_type);
            }

            if (! empty($final_url_suffix)) {
                $campaignArr['final_url_suffix'] = $final_url_suffix;
            }

            if ($channel_type == 'MULTI_CHANNEL') {
                $campaignArray['app_id'] = $request->app_id;
                $campaignArray['app_store'] = $request->app_store;
                $campaignArr['app_campaign_setting'] = self::getAppCampaignSetting($campaignArray['app_id'], $campaignArray['app_store'], $channel_sub_type);
                unset($campaignArr['bidding_strategy_type']);
                unset($campaignArr['network_settings']);

                if (in_array($bidding_strategy_type, ['TARGET_CPA']) && ! $txt_target_cpa) {
                    $txt_target_cpa = 1;
                    $campaignArray['target_cpa_value'] = $txt_target_cpa;
                }

                if ($channel_sub_type == 'APP_CAMPAIGN_FOR_PRE_REGISTRATION') {
                    $campaignArr['optimization_goal_setting'] = new OptimizationGoalSetting([
                        'optimization_goal_types' => [
                            OptimizationGoalType::APP_PRE_REGISTRATION,
                        ],
                    ]);
                }
            }

            if (in_array($bidding_strategy_type, ['TARGET_CPA']) && $txt_target_cpa) {
                $campaignArr['target_cpa'] = new TargetCpa(['target_cpa_micros' => $txt_target_cpa * 1000000]);
            } elseif (in_array($bidding_strategy_type, ['TARGET_SPEND']) && $txt_maximize_clicks) {
                $campaignArr['target_spend'] = new TargetSpend(['target_spend_micros' => $txt_maximize_clicks * 1000000]);
            } elseif (in_array($bidding_strategy_type, ['TARGET_ROAS']) && $txt_target_roas) {
                $campaignArr['target_roas'] = new TargetRoas(['target_roas' => $txt_target_roas]);
            } elseif ($channel_type == 'PERFORMANCE_MAX' && $txt_target_roas) {
                $campaignArr['maximize_conversion_value'] = new MaximizeConversionValue(['target_roas' => $txt_target_roas]);
            } elseif ($channel_type != 'MULTI_CHANNEL') {
                $campaignArr['manual_cpc'] = new ManualCpc;
            }

            $campaign = new Campaign($campaignArr);
            // Create a campaign operation.
            $campaignOperation = new CampaignOperation;
            $campaignOperation->setCreate($campaign);

            // Create a MutateCampaignsRequest and set the customer ID and operations.
            $request = new MutateCampaignsRequest;
            $request->setCustomerId($customerId);
            $request->setOperations([$campaignOperation]);

            // Submits the campaign operation and retrieves the response.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns($request);

            // Get the created campaign from the response.
            $createdCampaign = $response->getResults()[0];
            $campaignResourceName = $createdCampaign->getResourceName();

            $campaignArray['google_campaign_id'] = substr($campaignResourceName, strrpos($campaignResourceName, '/') + 1);
            $campaignArray['campaign_response'] = json_encode($createdCampaign);
            GoogleAdsCampaign::create($campaignArray);

            // Start Target Language
            if (! empty($targetLanguages) && ! @$request->all_target_languages) {
                foreach ($targetLanguages as $value) {
                    $isAdded = self::addTargetLanguage($googleAdsClient, $customerId, $campaignArray['google_campaign_id'], $value);

                    if ($isAdded) {
                        GoogleCampaignTargetLanguage::create([
                            'google_customer_id' => $customerId,
                            'adgroup_google_campaign_id' => $campaignArray['google_campaign_id'],
                            'google_language_constant_id' => $value,
                        ]);
                    }
                }
            }
            // End Target Language

            // Start Target Location
            if (@$request->target_location == 'other') {
                if (@$request->target_location_type == 'radius') {
                    $addedLocation = self::addLocationWithRadius(
                        $googleAdsClient,
                        $customerId,
                        $campaignArray['google_campaign_id'],
                        @$request->target_location_address,
                        @$request->target_location_distance,
                        @$request->target_location_radius_units
                    );

                    if (! empty($addedLocation)) {
                        $locationArr = [
                            'google_customer_id' => $customerId,
                            'adgroup_google_campaign_id' => $campaignArray['google_campaign_id'],
                            'google_location_id' => $addedLocation['location_id'],
                            'type' => $request->target_location_type,
                            'address' => @$request->target_location_address,
                            'distance' => @$request->target_location_distance,
                            'radius_units' => @$request->target_location_radius_units,
                            'is_target' => true,
                        ];

                        GoogleCampaignLocation::create($locationArr);
                    }
                } else {
                    $search = '';
                    if (@$request->city_id) {
                        $city = City::find($request->city_id);
                        $search .= @$city->name ? $city->name : '';
                    }

                    if (@$request->state_id) {
                        $state = State::find($request->state_id);
                        $search .= @$state->name ? ','.$state->name : '';
                    }

                    if (@$request->country_id) {
                        $country = Country::find($request->country_id);
                        $search .= @$country->name ? ','.$country->name : '';
                    }

                    if (! empty($search)) {
                        $geoTargetConstant = self::getGeoTargetConstant($googleAdsClient, $search);

                        if (! empty($geoTargetConstant)) {
                            $addedLocation = self::addLocation(
                                $googleAdsClient,
                                $customerId,
                                $campaignArray['google_campaign_id'],
                                $geoTargetConstant['location_id'],
                                (@$request->is_target == 0 ? true : false)
                            );

                            if (! empty($addedLocation)) {
                                $locationArr = [
                                    'google_customer_id' => $customerId,
                                    'adgroup_google_campaign_id' => $campaignArray['google_campaign_id'],
                                    'google_location_id' => $addedLocation['location_id'],
                                    'type' => $request->target_location_type,
                                    'country_id' => @$request->country_id,
                                    'state_id' => @$request->state_id,
                                    'city_id' => @$request->city_id,
                                    'address' => $search,
                                    'is_target' => @$request->is_target,
                                ];

                                GoogleCampaignLocation::create($locationArr);
                            }
                        }
                    }
                }
            }
            // End Target Location

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Campaign',
                'message' => 'Created new campaign',
                'response' => json_encode($campaignArray),
            ];
            Log::info(json_encode($input));
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign created successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Create new campaign > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // go to update page
    public function updatePage(Request $request, $campaignId)
    {
        $campaign = GoogleAdsCampaign::with('target_languages')->where('google_campaign_id', $campaignId)->firstOrFail();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Campaign',
            'message' => 'Viewed update campaign for '.$campaign->name,
        ];
        Log::info(json_encode($input));
        insertGoogleAdsLog($input);

        return $campaign;
    }

    // save campaign's changes
    public function updateCampaign(UpdateCampaignGoogleCampaignRequest $request): RedirectResponse
    {

        $campaignDetail = GoogleAdsCampaign::with('target_languages')->where('google_campaign_id', $request->campaignId)->first();
        $account_id = $campaignDetail->account_id;
        $customerId = $campaignDetail->google_customer_id;
        try {
            $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $campaignId = $request->campaignId;
            $campaignName = $request->campaignName;
            $campaignStatus = $campaignStatusArr[$request->campaignStatus];

            $campaignArray = [];
            $budgetAmount = $request->budgetAmount;
            $campaign_start_date = $request->start_date;
            $campaign_end_date = $request->end_date;
            $targetLanguages = $request->target_languages;

            //start creating array to store data into database

            $campaignArray['campaign_name'] = $campaignName;
            $campaignArray['budget_amount'] = $request->budgetAmount;
            $campaignArray['start_date'] = $campaign_start_date;
            $campaignArray['end_date'] = $campaign_end_date;
            $campaignArray['status'] = $campaignStatus;

            if ($request->biddingStrategyType) {
                $bidding_strategy_type = $request->biddingStrategyType;
            } else {
                $bidding_strategy_type = 'UNKNOWN';
            }
            $campaignArray['bidding_strategy_type'] = $bidding_strategy_type;

            if ($request->txt_target_cpa) {
                $txt_target_cpa = $request->txt_target_cpa;
            } else {
                $txt_target_cpa = 0.0;
            }
            $campaignArray['target_cpa_value'] = $txt_target_cpa;

            if ($request->txt_target_roas) {
                $txt_target_roas = $request->txt_target_roas;
            } else {
                $txt_target_roas = 0.0;
            }
            $campaignArray['target_roas_value'] = $txt_target_roas;

            if ($request->txt_maximize_clicks) {
                $txt_maximize_clicks = $request->txt_maximize_clicks;
            } else {
                $txt_maximize_clicks = '';
            }
            $campaignArray['maximize_clicks'] = $txt_maximize_clicks;

            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            $budget = self::updateCampaignBudget($googleAdsClient, $customerId, $budgetAmount, $campaignDetail->budget_id);
            $budgetResourceName = $budget['budget_resource_name'] ?? null;

            // Creates a campaign.
            $campaignArr = [
                'resource_name' => ResourceNames::forCampaign($customerId, $campaignId),

                'name' => $campaignName,
                'campaign_budget' => $budgetResourceName,
                'status' => self::getCampaignStatus($campaignStatus),
                'bidding_strategy_type' => self::getBiddingStrategyType($bidding_strategy_type),
                'start_date' => $campaign_start_date,
                'end_date' => $campaign_end_date,
            ];

            if ($campaignDetail->channel_type == 'MULTI_CHANNEL') {
                if (! in_array($campaignDetail->bidding_strategy_type, ['TARGET_CPA']) || ! $txt_target_cpa) {
                    $txt_target_cpa = 1;
                    $bidding_strategy_type = 'TARGET_CPA';
                    $campaignArray['target_cpa_value'] = $txt_target_cpa;
                    $campaignArray['bidding_strategy_type'] = $bidding_strategy_type;
                }
            }

            if (in_array($bidding_strategy_type, ['TARGET_CPA', 'MAXIMIZE_CONVERSION_VALUE']) && $txt_target_cpa) {
                $campaignArr['target_cpa'] = new TargetCpa(['target_cpa_micros' => $txt_target_cpa * 1000000]);
            }

            if (in_array($bidding_strategy_type, ['TARGET_SPEND']) && $txt_maximize_clicks) {
                $campaignArr['target_spend'] = new TargetSpend(['target_spend_micros' => $txt_maximize_clicks * 1000000]);
            }

            if (in_array($bidding_strategy_type, ['TARGET_ROAS']) && $txt_target_roas) {
                $campaignArr['target_roas'] = new TargetRoas(['target_roas' => $txt_target_roas]);
            }

            if (in_array($bidding_strategy_type, ['MANUAL_CPC'])) {
                $campaignArr['target_cpa'] = null;
                $campaignArr['target_spend'] = null;
                $campaignArr['target_roas'] = null;
            }

            // Creates a campaign object with the specified resource name and other changes.
            $campaign = new Campaign($campaignArr);

            // Constructs an operation that will update the campaign with the specified resource name,
            // using the FieldMasks utility to derive the update mask. This mask tells the Google Ads
            // API which attributes of the campaign you want to change.
            $campaignOperation = new CampaignOperation;
            $campaignOperation->setUpdate($campaign);
            $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));

            // Issues a mutate request to update the campaign.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns(
                $customerId,
                [$campaignOperation]
            );

            $updatedCampaign = $response->getResults()[0];
            $campaignResourceName = $updatedCampaign->getResourceName();

            $campaignArray['google_campaign_id'] = substr($campaignResourceName, strrpos($campaignResourceName, '/') + 1);
            $campaignArray['campaign_response'] = json_encode($updatedCampaign);
            GoogleAdsCampaign::whereId($campaignDetail->id)->update($campaignArray);

            // Start Target Language
            if (! empty($targetLanguages) && ! @$request->all_target_languages) {
                $targetLanguageIds = [];
                foreach ($targetLanguages as $value) {
                    $insert = [
                        'google_customer_id' => $customerId,
                        'adgroup_google_campaign_id' => $campaignArray['google_campaign_id'],
                        'google_language_constant_id' => $value,
                    ];

                    $isExist = GoogleCampaignTargetLanguage::where($insert)->first();
                    if (! $isExist) {
                        self::addTargetLanguage($googleAdsClient, $customerId, $campaignArray['google_campaign_id'], $value);
                        GoogleCampaignTargetLanguage::create($insert);
                    }
                    array_push($targetLanguageIds, $value);
                }

                // Remove old unused lang
                $removeTargetLanguages = GoogleCampaignTargetLanguage::where('google_customer_id', $customerId)
                    ->where('adgroup_google_campaign_id', $campaignArray['google_campaign_id'])
                    ->whereNotIn('google_language_constant_id', $targetLanguageIds)
                    ->get();
                if (! empty($removeTargetLanguages)) {
                    foreach ($removeTargetLanguages as $key => $language) {
                        self::removeTargetLanguage($googleAdsClient, $customerId, $campaignArray['google_campaign_id'], $language->google_language_constant_id);

                        $language->delete();
                    }
                }
            } else {
                if (! empty($campaignDetail->target_languages)) {
                    foreach ($campaignDetail->target_languages as $key => $language) {
                        self::removeTargetLanguage($googleAdsClient, $customerId, $campaignArray['google_campaign_id'], $language->google_language_constant_id);

                        $language->delete();
                    }
                }
            }
            // End Target Language

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Campaign',
                'message' => 'Updated campaign details for '.$campaignName,
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign updated successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Update campaign > '.$e->getMessage(),
            ];
            Log::info(json_encode($input));
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/update/'.$request->campaignId.'?account_id='.$account_id)->with('actError', $e->getMessage());
        }
    }

    // delete campaign
    public function deleteCampaign(Request $request, $campaignId): RedirectResponse
    {
        try {
            $account_id = $request->delete_account_id;
            $googleAdsCampaign = GoogleAdsCampaign::where('account_id', $account_id)->where('google_campaign_id', $campaignId)->firstOrFail();
            $customerId = $googleAdsCampaign->google_customer_id;

            try {
                // Generate a refreshable OAuth2 credential for authentication.
                $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

                // Creates the resource name of a campaign to remove.
                $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

                // Creates a campaign operation.
                $campaignOperation = new CampaignOperation;
                $campaignOperation->setRemove($campaignResourceName);

                // Issues a mutate request to remove the campaign.
                $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
                $campaignServiceClient->mutateCampaigns($customerId, [$campaignOperation]);
            } catch (Exception $e) {
                return response()->json(['code' => 400, 'errors' => $e->getMessage(), 'message' => $e->getMessage()], 400);
            }

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Campaign',
                'message' => 'Deleted campaign',
                'response' => json_encode($googleAdsCampaign),
            ];

            // Delete other data
            GoogleAdGroupKeyword::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleResponsiveDisplayAd::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleResponsiveDisplayAdMarketingImage::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleAppAd::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleAppAdImage::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleAd::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleAdsGroup::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleCampaignTargetLanguage::where('adgroup_google_campaign_id', $campaignId)->delete();

            $googleAdsCampaign->delete();

            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign deleted successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Delete campaign > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actError', $this->exceptionError);
        }
    }

    // create a campaign single shared budget
    public function addCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId, $amount, $channelType)
    {
        $response = [];
        try {
            $uniqId = uniqid();

            // Creates a campaign budget.
            // $budgetArr = [
            //     'name'            => 'Interplanetary Cruise Budget #' . $uniqId,
            //     'delivery_method' => BudgetDeliveryMethod::STANDARD,
            //     'amount_micros'   => $amount * 1000000,
            // ];
            // if (in_array($channelType, ['PERFORMANCE_MAX', 'MULTI_CHANNEL'])) {
            //     // A Performance Max campaign cannot use a shared campaign budget.
            //     $budgetArr['explicitly_shared'] = false;
            // }

            // $budget = new CampaignBudget($budgetArr);

            // // Creates a campaign budget operation.
            // $campaignBudgetOperation = new CampaignBudgetOperation();
            // $campaignBudgetOperation->setCreate($budget);

            // $request = new MutateCampaignBudgetsRequest([
            //     'customerId' => $customerId,
            //     'operations' => [$campaignBudgetOperation]
            // ]);
            // // dd($campaignBudgetOperation);
            // // Issues a mutate request.
            // // dd($customerId);
            // $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
            // $response                    = $campaignBudgetServiceClient->mutateCampaignBudgets(
            //     $request
            // );

            // Construct campaign budget properties
            $budgetArr = [
                'name' => 'Interplanetary Cruise Budget #'.$uniqId,
                'delivery_method' => BudgetDeliveryMethod::STANDARD,
                'amount_micros' => $amount * 1000000,
            ];

            // Check if explicitly_shared key is set only if needed (for certain channel types)
            if (in_array($channelType, ['PERFORMANCE_MAX', 'MULTI_CHANNEL'])) {
                // A Performance Max campaign cannot use a shared campaign budget.
                $budgetArr['explicitly_shared'] = false;
            }

            // Create a campaign budget object
            $budget = new CampaignBudget($budgetArr);

            // Create a campaign budget operation
            $campaignBudgetOperation = new CampaignBudgetOperation;
            $campaignBudgetOperation->setCreate($budget);

            // Create a mutate request
            $request = new MutateCampaignBudgetsRequest;
            $request->setCustomerId($customerId); // Set the customerId property
            $request->setOperations([$campaignBudgetOperation]);

            // Get the CampaignBudgetServiceClient
            $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();

            // Issue a mutate request
            $response = $campaignBudgetServiceClient->mutateCampaignBudgets($request);

            // Process the response as needed

            $createdBudget = $response->getResults()[0];
            $budgetResourceName = $createdBudget->getResourceName();

            $response = [
                'budget_uniq_id' => $uniqId,
                'budget_id' => substr($budgetResourceName, strrpos($budgetResourceName, '/') + 1),
                'budget_resource_name' => $budgetResourceName,
            ];
        } catch (Exception $e) {
            // Insert google ads log
            Log::info($e->getMessage());
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Create campaign budget > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);
        }

        // dd($response);
        return $response;
    }

    // update a campaign single shared budget
    public function updateCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId, $amount, $campaignBudgetId)
    {
        $response = [];
        try {
            // Creates a campaign budget.
            $budget = new CampaignBudget([
                'resource_name' => ResourceNames::forCampaignBudget($customerId, $campaignBudgetId),

                'amount_micros' => $amount * 1000000,
            ]);

            // Creates a campaign budget operation.
            $campaignBudgetOperation = new CampaignBudgetOperation;
            $campaignBudgetOperation->setUpdate($budget);
            $campaignBudgetOperation->setUpdateMask(FieldMasks::allSetFieldsOf($budget));

            $request = new MutateCampaignBudgetsRequest([
                'customerId' => $customerId,
                'operations' => [$campaignBudgetOperation],
            ]);
            // Issues a mutate request.
            $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
            $response = $campaignBudgetServiceClient->mutateCampaignBudgets($request);
            $updatedBudget = $response->getResults()[0];
            $budgetResourceName = $updatedBudget->getResourceName();

            $response = [
                'budget_id' => substr($budgetResourceName, strrpos($budgetResourceName, '/') + 1),
                'budget_resource_name' => $budgetResourceName,
            ];
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Update campaign budget > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);
        }

        return $response;
    }

    //function to retrieve data from library
    //get advertising channel type
    public function getAdvertisingChannelType($v)
    {
        switch ($v) {
            case 'SEARCH':
                return AdvertisingChannelType::SEARCH;
                break;

            case 'DISPLAY':
                return AdvertisingChannelType::DISPLAY;
                break;

            case 'SHOPPING':
                return AdvertisingChannelType::SHOPPING;
                break;

            case 'MULTI_CHANNEL':
                return AdvertisingChannelType::MULTI_CHANNEL;
                break;

            case 'PERFORMANCE_MAX':
                return AdvertisingChannelType::PERFORMANCE_MAX;
                break;

            case 'UNKNOWN':
                return AdvertisingChannelType::UNKNOWN;
                break;

            default:
                return AdvertisingChannelType::SEARCH;
        }
    }

    //get advertising sub type
    private function getAdvertisingChannelSubType($v)
    {
        switch ($v) {
            case 'UNSPECIFIED':
                return AdvertisingChannelSubType::UNSPECIFIED;
                break;

            case 'SEARCH_MOBILE_APP':
                return AdvertisingChannelSubType::SEARCH_MOBILE_APP;
                break;

            case 'DISPLAY_MOBILE_APP':
                return AdvertisingChannelSubType::DISPLAY_MOBILE_APP;
                break;

            case 'SEARCH_EXPRESS':
                return AdvertisingChannelSubType::SEARCH_EXPRESS;
                break;

            case 'DISPLAY_EXPRESS':
                return AdvertisingChannelSubType::DISPLAY_EXPRESS;
                break;

            case 'DISPLAY_SMART_CAMPAIGN':
                return AdvertisingChannelSubType::DISPLAY_SMART_CAMPAIGN;
                break;

            case 'SHOPPING_GOAL_OPTIMIZED_ADS':
                return AdvertisingChannelSubType::SHOPPING_GOAL_OPTIMIZED_ADS;
                break;

            case 'DISPLAY_GMAIL_AD':
                return AdvertisingChannelSubType::DISPLAY_GMAIL_AD;
                break;

            case 'APP_CAMPAIGN':
                return AdvertisingChannelSubType::APP_CAMPAIGN;
                break;

            case 'APP_CAMPAIGN_FOR_ENGAGEMENT':
                return AdvertisingChannelSubType::APP_CAMPAIGN_FOR_ENGAGEMENT;
                break;

            case 'APP_CAMPAIGN_FOR_PRE_REGISTRATION':
                return AdvertisingChannelSubType::APP_CAMPAIGN_FOR_PRE_REGISTRATION;
                break;

            default:
                return AdvertisingChannelSubType::UNSPECIFIED;
        }
    }

    public function getBiddingStrategyTypeArray()
    {
        return [
            'MANUAL_CPC' => 'Manually set bids',
            'MANUAL_CPM' => 'Viewable CPM',
            'TARGET_SPEND' => 'Maximize clicks',
            'TARGET_CPA' => 'Target CPA',
            'TARGET_ROAS' => 'Target Roas',
            'MAXIMIZE_CONVERSIONS' => 'Maximise conversions',
            'MAXIMIZE_CONVERSION_VALUE' => 'Automatically maximize conversions',
            'UNSPECIFIED' => 'Unspecified',
        ];
    }

    // get network settings
    private function getNetworkSettings($channel_type, $channel_sub_type)
    {
        $networkSettingsArr = [
            'target_google_search' => false,
            'target_search_network' => false,
            'target_content_network' => false,
            'target_partner_search_network' => false,
        ];

        if ($channel_type == 'SEARCH' || $channel_type == 'MULTI_CHANNEL' || ($channel_type == 'DISPLAY' && $channel_sub_type == 'SHOPPING_GOAL_OPTIMIZED_ADS')) {
            $networkSettingsArr['target_google_search'] = true;
        } elseif ($channel_type == 'MULTI_CHANNEL' || $channel_sub_type == 'SHOPPING_GOAL_OPTIMIZED_ADS') {
            $networkSettingsArr['target_search_network'] = true;
        }

        if ($channel_type == 'DISPLAY' || $channel_type == 'MULTI_CHANNEL' || ($channel_type == 'DISPLAY' && $channel_sub_type == 'DISPLAY_SMART_CAMPAIGN')) {
            $networkSettingsArr['target_content_network'] = true;
        }

        if ($channel_type == 'SHOPPING') {
            $networkSettingsArr['target_google_search'] = true;
            $networkSettingsArr['target_search_network'] = true;
        }

        if ($channel_type == 'MULTI_CHANNEL') {
            $networkSettingsArr['target_partner_search_network'] = true;
        }
        $networkSettings = new NetworkSettings($networkSettingsArr);

        return $networkSettings;
    }

    // get shopping setting
    private function getShoppingSetting($merchant_id, $sales_country)
    {
        $shoppingSetting = new ShoppingSetting([
            'sales_country' => $sales_country,
            'campaign_priority' => 0,
            'merchant_id' => $merchant_id,
            'enable_local' => true,
        ]);

        return $shoppingSetting;
    }

    // get shopping setting
    private function getGeoTargetTypeSetting()
    {
        $shoppingSetting = new GeoTargetTypeSetting([
            'positive_geo_target_type' => PositiveGeoTargetType::UNSPECIFIED,
            'negative_geo_target_type' => NegativeGeoTargetType::UNSPECIFIED,
        ]);

        return $shoppingSetting;
    }

    //get bidding strategy type
    private function getBiddingStrategyType($v)
    {
        switch ($v) {
            case 'MANUAL_CPC':
                return BiddingStrategyType::MANUAL_CPC;
                break;

            case 'MANUAL_CPM':
                return BiddingStrategyType::MANUAL_CPM;
                break;

            case 'PAGE_ONE_PROMOTED':
                return BiddingStrategyType::PAGE_ONE_PROMOTED;
                break;

            case 'TARGET_SPEND':
                return BiddingStrategyType::TARGET_SPEND;
                break;

            case 'TARGET_CPA':
                return BiddingStrategyType::TARGET_CPA;
                break;

            case 'TARGET_ROAS':
                return BiddingStrategyType::TARGET_ROAS;
                break;

            case 'MAXIMIZE_CONVERSIONS':
                return BiddingStrategyType::MAXIMIZE_CONVERSIONS;
                break;

            case 'MAXIMIZE_CONVERSION_VALUE':
                return BiddingStrategyType::MAXIMIZE_CONVERSION_VALUE;
                break;

            case 'TARGET_OUTRANK_SHARE':
                return BiddingStrategyType::TARGET_OUTRANK_SHARE;
                break;

            case 'NONE':
                return BiddingStrategyType::NONE;
                break;

            default:
                return BiddingStrategyType::UNKNOWN;
        }
    }

    //get campaign status
    private function getCampaignStatus($v)
    {
        switch ($v) {
            case 'ENABLED':
                return CampaignStatus::ENABLED;
                break;

            case 'PAUSED':
                return CampaignStatus::PAUSED;
                break;

            case 'REMOVED':
                return CampaignStatus::REMOVED;
                break;

            default:
                return CampaignStatus::PAUSED;
        }
    }

    //get app campaign setting
    private function getAppCampaignSetting($appId, $appStore, $channel_sub_type)
    {
        $appStore = ($appStore == 'GOOGLE_APP_STORE' ? AppCampaignAppStore::GOOGLE_APP_STORE : AppCampaignAppStore::APPLE_APP_STORE);

        $bidding_strategy_type = AppCampaignBiddingStrategyGoalType::OPTIMIZE_INSTALLS_TARGET_INSTALL_COST;
        if ($channel_sub_type == 'APP_CAMPAIGN_FOR_PRE_REGISTRATION') {
            $bidding_strategy_type = AppCampaignBiddingStrategyGoalType::OPTIMIZE_PRE_REGISTRATION_CONVERSION_VOLUME;
        }

        $appCampaignSetting = new AppCampaignSetting([
            'app_id' => $appId,
            'app_store' => $appStore,
            'bidding_strategy_goal_type' => $bidding_strategy_type,
        ]);

        return $appCampaignSetting;
    }

    // add target language
    public static function addTargetLanguage($googleAdsClient, $customerId, $campaignId, $languageConstantId)
    {
        $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

        try {
            $campaignCriterion = new CampaignCriterion([
                'language' => new LanguageInfo([
                    'language_constant' => ResourceNames::forLanguageConstant($languageConstantId),
                ]),
                'campaign' => $campaignResourceName,
            ]);

            $campaignCriterionOperation = new CampaignCriterionOperation;
            $campaignCriterionOperation->setCreate($campaignCriterion);

            $campaignCriterionServiceClient = $googleAdsClient->getCampaignCriterionServiceClient();

            $response = $campaignCriterionServiceClient->mutateCampaignCriteria(
                $customerId,
                [$campaignCriterionOperation]
            );

            return true;
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Add campaign target language > '.$campaignId.' > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return false;
        }
    }

    // remove target language
    public static function removeTargetLanguage($googleAdsClient, $customerId, $campaignId, $languageConstantId)
    {
        try {
            // Creates campaign criterion resource name.
            $campaignCriterionResourceName =
                ResourceNames::forCampaignCriterion($customerId, $campaignId, $languageConstantId);

            // Constructs an operation that will remove the language with the specified resource name.
            $campaignCriterionOperation = new CampaignCriterionOperation;
            $campaignCriterionOperation->setRemove($campaignCriterionResourceName);

            // Issues a mutate request to remove the campaign criterion.
            $campaignCriterionServiceClient = $googleAdsClient->getCampaignCriterionServiceClient();
            $response = $campaignCriterionServiceClient->mutateCampaignCriteria(
                $customerId,
                [$campaignCriterionOperation]
            );

            return true;
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Remove campaign target language > '.$campaignId.' > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return false;
        }
    }

    public static function addLocation($googleAdsClient, $customerId, $campaignId, $locationId, $isTarget)
    {
        try {
            $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

            $campaignCriterion = new CampaignCriterion([
                'location' => new LocationInfo([
                    'geo_target_constant' => ResourceNames::forGeoTargetConstant($locationId),
                ]),
                'negative' => $isTarget,
                'campaign' => $campaignResourceName,
            ]);

            $campaignCriterionOperation = new CampaignCriterionOperation;
            $campaignCriterionOperation->setCreate($campaignCriterion);

            $campaignCriterionServiceClient = $googleAdsClient->getCampaignCriterionServiceClient();

            $response = $campaignCriterionServiceClient->mutateCampaignCriteria(
                $customerId,
                [$campaignCriterionOperation]
            );

            $addedCampaignCriterion = $response->getResults()[0];
            $locationResourceName = $addedCampaignCriterion->getResourceName();

            $response = [
                'location_id' => substr($locationResourceName, strrpos($locationResourceName, '~') + 1),
                'location_resource_name' => $locationResourceName,
            ];
        } catch (Exception $e) {
            $response = [];
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Add campaign target location > '.$campaignId.' > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);
        }

        return $response;
    }

    public static function addLocationWithRadius($googleAdsClient, $customerId, $campaignId, $address, $radius, $radiusUnits)
    {
        try {
            if ($radiusUnits == 'km') {
                $radiusUnits = ProximityRadiusUnits::KILOMETERS;
            } else {
                $radiusUnits = ProximityRadiusUnits::MILES;
            }

            $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

            $campaignCriterion = new CampaignCriterion([
                'proximity' => new ProximityInfo([
                    'address' => new AddressInfo([
                        'street_address' => $address,
                    ]),
                    'radius' => $radius,
                    // Default is kilometers.
                    'radius_units' => $radiusUnits,
                ]),
                'campaign' => $campaignResourceName,
            ]);

            $campaignCriterionOperation = new CampaignCriterionOperation;
            $campaignCriterionOperation->setCreate($campaignCriterion);

            $campaignCriterionServiceClient = $googleAdsClient->getCampaignCriterionServiceClient();

            $response = $campaignCriterionServiceClient->mutateCampaignCriteria(
                $customerId,
                [$campaignCriterionOperation]
            );

            $addedCampaignCriterion = $response->getResults()[0];
            $locationResourceName = $addedCampaignCriterion->getResourceName();

            $response = [
                'location_id' => substr($locationResourceName, strrpos($locationResourceName, '~') + 1),
                'location_resource_name' => $locationResourceName,
            ];
        } catch (Exception $e) {
            $response = [];
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign',
                'message' => 'Add campaign target location with radius > '.$campaignId.' > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);
        }

        return $response;
    }

    public static function getGeoTargetConstant($googleAdsClient, $search)
    {
        $geoTargetConstantServiceClient = $googleAdsClient->getGeoTargetConstantServiceClient();

        $response = $geoTargetConstantServiceClient->suggestGeoTargetConstants([
            'locationNames' => new LocationNames(['names' => [$search]]),
        ]);

        // Iterates over all geo target constant suggestion objects and prints the requested field
        // values for each one.
        $result = [];
        foreach ($response->getGeoTargetConstantSuggestions() as $geoTargetConstantSuggestion) {
            $status = GeoTargetConstantStatus::name(
                $geoTargetConstantSuggestion->getGeoTargetConstant()->getStatus()
            );

            if ($status == 'ENABLED') {
                $locationResourceName = $geoTargetConstantSuggestion->getGeoTargetConstant()->getResourceName();
                $result = [
                    'location_id' => substr($locationResourceName, strrpos($locationResourceName, '/') + 1),
                    'location_name' => $geoTargetConstantSuggestion->getGeoTargetConstant()->getCanonicalName(),
                ];

                return $result;
            }
        }

        return $result;
    }
}
