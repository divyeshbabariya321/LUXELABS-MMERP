<?php

namespace App\Http\Controllers;

use App\GoogleAdsCampaign;
use App\Helpers\GoogleAdsHelper;
use App\Http\Requests\GoogleCampaignLocationCreateLocationRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\GoogleCampaignLocation;
use App\Models\State;
use Exception;
use Google\Ads\GoogleAds\Util\V16\ResourceNames;
use Google\Ads\GoogleAds\V16\Enums\GeoTargetConstantStatusEnum\GeoTargetConstantStatus;
use Google\Ads\GoogleAds\V16\Services\CampaignCriterionOperation;
use Google\Ads\GoogleAds\V16\Services\SuggestGeoTargetConstantsRequest\LocationNames;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleCampaignLocationController extends Controller
{
    public function getAccountDetail($campaignId)
    {
        $campaignDetail = GoogleAdsCampaign::with('account')->where('google_campaign_id', $campaignId)->where('channel_type', 'SEARCH')->first();
        if ($campaignDetail->exists() > 0) {
            return [
                'account_id' => $campaignDetail->account_id,
                'campaign_name' => $campaignDetail->campaign_name,
                'google_customer_id' => $campaignDetail->google_customer_id,
                'google_map_api_key' => $campaignDetail->account->google_map_api_key,
            ];
        } else {
            abort(404, 'Invalid account!');
        }
    }

    public function index(Request $request, $campaignId)
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $campaign_name = $acDetail['campaign_name'];
        $google_map_api_key = $acDetail['google_map_api_key'];

        $where = [
            'adgroup_google_campaign_id' => $campaignId,
        ];

        $locations = GoogleCampaignLocation::where($where);

        if ($request->address) {
            $locations = $locations->where('address', 'LIKE', '%'.$request->address.'%');
        }

        $locations = $locations->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('google_campaign_location.partials.list', ['locations' => $locations, 'campaignId' => $campaignId])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $locations->render(),
                'count' => $locations->total(),
            ], 200);
        }

        $totalEntries = $locations->total();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Campaign location',
            'message' => 'Viewed campaign location listing for '.$campaign_name,
        ];
        insertGoogleAdsLog($input);

        return view('google_campaign_location.index', [
            'locations' => $locations,
            'totalNumEntries' => $totalEntries,
            'campaignId' => $campaignId,
            'account_id' => $account_id,
            'campaign_name' => $campaign_name,
            'google_map_api_key' => $google_map_api_key,
        ]);
    }

    // create location
    public function createLocation(GoogleCampaignLocationCreateLocationRequest $request, $campaignId): RedirectResponse
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $campaign_name = $acDetail['campaign_name'];
        $customerId = $acDetail['google_customer_id'];

        try {
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            $googleCampaignsController = new GoogleCampaignsController;

            // Start Target Location
            if (@$request->target_location == 'other') {
                if (@$request->target_location_type == 'radius') {
                    $addedLocation = $googleCampaignsController::addLocationWithRadius(
                        $googleAdsClient,
                        $customerId,
                        $campaignId,
                        @$request->target_location_address,
                        @$request->target_location_distance,
                        @$request->target_location_radius_units
                    );

                    if (! empty($addedLocation)) {
                        $locationArr = [
                            'google_customer_id' => $customerId,
                            'adgroup_google_campaign_id' => $campaignId,
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
                        $geoTargetConstant = $googleCampaignsController::getGeoTargetConstant($googleAdsClient, $search);

                        if (! empty($geoTargetConstant)) {
                            $addedLocation = $googleCampaignsController::addLocation(
                                $googleAdsClient,
                                $customerId,
                                $campaignId,
                                $geoTargetConstant['location_id'],
                                (@$request->is_target == 0 ? true : false)
                            );

                            if (! empty($addedLocation)) {
                                $locationArr = [
                                    'google_customer_id' => $customerId,
                                    'adgroup_google_campaign_id' => $campaignId,
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
            } else {
                $locations = GoogleCampaignLocation::where('adgroup_google_campaign_id', $campaignId)->get();

                foreach ($locations as $location) {
                    // Creates campaign criterion resource name.
                    $campaignCriterionResourceName =
                        ResourceNames::forCampaignCriterion($customerId, $campaignId, $location->google_location_id);

                    // Constructs an operation that will remove the language with the specified resource name.
                    $campaignCriterionOperation = new CampaignCriterionOperation;
                    $campaignCriterionOperation->setRemove($campaignCriterionResourceName);

                    // Issues a mutate request to remove the campaign criterion.
                    $campaignCriterionServiceClient = $googleAdsClient->getCampaignCriterionServiceClient();
                    $response = $campaignCriterionServiceClient->mutateCampaignCriteria(
                        $customerId,
                        [$campaignCriterionOperation]
                    );

                    $location->delete();
                }
            }
            // End Target Location

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Campaign Location',
                'message' => 'Created campaign location for '.$campaign_name,
                'response' => json_encode($request->all()),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/google-campaign-location')->with('actSuccess', 'Location added successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign Location',
                'message' => 'Create campaign location > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/google-campaign-location')->with('actError', $this->exceptionError);
        }
    }

    // delete location
    public function deleteLocation(Request $request, $campaignId, $locationId): RedirectResponse
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $customerId = $acDetail['google_customer_id'];

        $where = [
            'adgroup_google_campaign_id' => $campaignId,
            'google_location_id' => $locationId,
        ];

        $location = GoogleCampaignLocation::where($where)->firstOrFail();

        try {
            try {
                // Generate a refreshable OAuth2 credential for authentication.
                $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

                // Creates campaign criterion resource name.
                $campaignCriterionResourceName =
                    ResourceNames::forCampaignCriterion($customerId, $campaignId, $locationId);

                // Constructs an operation that will remove the language with the specified resource name.
                $campaignCriterionOperation = new CampaignCriterionOperation;
                $campaignCriterionOperation->setRemove($campaignCriterionResourceName);

                // Issues a mutate request to remove the campaign criterion.
                $campaignCriterionServiceClient = $googleAdsClient->getCampaignCriterionServiceClient();
                $response = $campaignCriterionServiceClient->mutateCampaignCriteria(
                    $customerId,
                    [$campaignCriterionOperation]
                );
            } catch (Exception $e) {
                return response()->json(['code' => 400, 'errors' => $e->getMessage(), 'message' => $e->getMessage()], 400);
            }

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Campaign Location',
                'message' => 'Deleted campaign location for '.$location->campaign->campaign_name,
                'response' => json_encode($location),
            ];

            $location->delete();

            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/google-campaign-location')->with('actSuccess', 'Location deleted successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Campaign Location',
                'message' => 'Delete campaign location > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/google-campaign-location')->with('actError', $this->exceptionError);
        }
    }

    // get countries
    public function countries(Request $request)
    {
        $records = Country::orderby('name', 'ASC');

        if (! empty($request->search)) {
            $records->where('name', 'LIKE', '%'.$request->search.'%');
        }

        $records = $records->paginate(50);

        $response = [];
        foreach ($records as $record) {
            $response[] = [
                'id' => $record->id,
                'text' => $record->name,
            ];
        }

        return ['result' => $response, 'pagination' => ['more' => $records->nextPageUrl() ? true : false]];
    }

    // get states
    public function states(Request $request)
    {
        $records = State::where('country_id', $request->country_id)->orderby('name', 'ASC');

        if (! empty($request->search)) {
            $records->where('name', 'LIKE', '%'.$request->search.'%');
        }

        $records = $records->paginate(50);

        $response = [];
        foreach ($records as $record) {
            $response[] = [
                'id' => $record->id,
                'text' => $record->name,
            ];
        }

        return ['result' => $response, 'pagination' => ['more' => $records->nextPageUrl() ? true : false]];
    }

    // get cities
    public function cities(Request $request)
    {
        $records = City::where('state_id', $request->state_id)->orderby('name', 'ASC');

        if (! empty($request->search)) {
            $records->where('name', 'LIKE', '%'.$request->search.'%');
        }

        $records = $records->paginate(50);

        $response = [];
        foreach ($records as $record) {
            $response[] = [
                'id' => $record->id,
                'text' => $record->name,
            ];
        }

        return ['result' => $response, 'pagination' => ['more' => $records->nextPageUrl() ? true : false]];
    }

    // get address
    public function address(Request $request)
    {
        // Generate a refreshable OAuth2 credential for authentication.
        $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($request->account_id);

        $geoTargetConstantServiceClient = $googleAdsClient->getGeoTargetConstantServiceClient();

        $response = $geoTargetConstantServiceClient->suggestGeoTargetConstants([
            'locationNames' => new LocationNames(['names' => [$request->search]]),
        ]);

        // Iterates over all geo target constant suggestion objects and prints the requested field
        // values for each one.
        $result = [];
        foreach ($response->getGeoTargetConstantSuggestions() as $geoTargetConstantSuggestion) {
            $status = GeoTargetConstantStatus::name(
                $geoTargetConstantSuggestion->getGeoTargetConstant()->getStatus()
            );

            if ($status == 'ENABLED') {
                $result[] = [
                    'id' => $geoTargetConstantSuggestion->getGeoTargetConstant()->getCanonicalName(),
                    'text' => $geoTargetConstantSuggestion->getGeoTargetConstant()->getCanonicalName(),
                ];
            }
        }

        return ['result' => $result];
    }
}
