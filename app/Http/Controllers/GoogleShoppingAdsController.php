<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdGoogleShoppingAdRequest;
use Illuminate\Http\RedirectResponse;
use Exception;
use App\GoogleAd;
use App\GoogleAdsGroup;
use App\GoogleAdsCampaign;
use Illuminate\Http\Request;
use App\Helpers\GoogleAdsHelper;
use Google\Ads\GoogleAds\V12\Resources\Ad;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Resources\AdGroupAd;
use Google\Ads\GoogleAds\V12\Services\AdGroupAdOperation;
use Google\Ads\GoogleAds\V12\Common\ShoppingProductAdInfo;
use Google\Ads\GoogleAds\V12\Enums\AdGroupAdStatusEnum\AdGroupAdStatus;

class GoogleShoppingAdsController extends Controller
{
    public function getAccountDetail($campaignId)
    {
        $campaignDetail = GoogleAdsCampaign::where('google_campaign_id', $campaignId)->first();
        if ($campaignDetail->exists() > 0) {
            return [
                'account_id'         => $campaignDetail->account_id,
                'campaign_name'      => $campaignDetail->campaign_name,
                'google_customer_id' => $campaignDetail->google_customer_id,
            ];
        } else {
            abort(404, 'Invalid account!');
        }
    }

    public function index(Request $request, $campaignId, $adGroupId)
    {
        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();
        $query       = GoogleAd::query();

        if ($request->adname) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('headline1', 'LIKE', '%' . $request->headline . '%');
            });
        }

        if ($request->description) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('description1', 'LIKE', '%' . $request->description . '%')->orWhere('description2', 'LIKE', '%' . $request->description . '%');
            });
        }

        if ($request->path) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('path1', 'LIKE', '%' . $request->path . '%')->orWhere('path2', 'LIKE', '%' . $request->path . '%');
            });
        }

        if ($request->final_url) {
            $query = $query->where('final_url', 'LIKE', '%' . $request->final_url . '%');
        }

        if ($request->ads_status) {
            $query = $query->where('status', $request->ads_status);
        }

        $query->where('adgroup_google_campaign_id', $campaignId)->where('google_adgroup_id', $adGroupId);
        $adsInfo = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googleads.partials.list-ads', ['ads' => $adsInfo, 'campaignId' => $campaignId, 'adGroupId' => $adGroupId])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $adsInfo->render(),
                'count' => $adsInfo->total(),
            ], 200);
        }

        $totalEntries = $adsInfo->total();

        // Insert google ads log
        $input = [
            'type'    => 'SUCCESS',
            'module'  => 'Ad',
            'message' => 'Viewed ad listing for ' . $groupDetail->ad_group_name,
        ];
        insertGoogleAdsLog($input);

        return view('googleshoppingads.index', ['ads' => $adsInfo, 'totalNumEntries' => $totalEntries, 'campaignId' => $campaignId, 'adGroupId' => $adGroupId, 'groupname' => @$groupDetail->ad_group_name]);
    }

    // create ad
    public function createAd(CreateAdGoogleShoppingAdRequest $request, $campaignId, $adGroupId): RedirectResponse
    {
        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();

        //create account

        $acDetail   = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $customerId = $acDetail['google_customer_id'];
        $adStatuses = ['ENABLED', 'PAUSED', 'DISABLED'];

        $adsArray                               = [];
        $adsArray['google_customer_id']         = $customerId;
        $adsArray['adgroup_google_campaign_id'] = $campaignId;
        $adsArray['google_adgroup_id']          = $adGroupId;
        $adsArray['headline1']                  = $request->adname;
        $adsArray['status']                     = $adStatuses[$request->adStatus];

        try {
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            // Creates an ad and sets responsive search ad info.
            $ad = new Ad([
                'shopping_product_ad' => new ShoppingProductAdInfo(),
                'name'                => $request->adname,
            ]);

            // Creates an ad group ad to hold the above ad.
            $adGroupAd = new AdGroupAd([
                'ad_group' => ResourceNames::forAdGroup($customerId, $adGroupId),
                'status'   => self::getAdStatus($adsArray['status']),
                'ad'       => $ad,
            ]);

            // Creates an ad group ad operation.
            $adGroupAdOperation = new AdGroupAdOperation();
            $adGroupAdOperation->setCreate($adGroupAd);

            // Issues a mutate request to add the ad group ad.
            $adGroupAdServiceClient = $googleAdsClient->getAdGroupAdServiceClient();
            $response               = $adGroupAdServiceClient->mutateAdGroupAds($customerId, [$adGroupAdOperation]);

            $createdAdGroupAd             = $response->getResults()[0];
            $createdAdGroupAdResourceName = $createdAdGroupAd->getResourceName();

            $adsArray['google_ad_id'] = substr($createdAdGroupAdResourceName, strrpos($createdAdGroupAdResourceName, '~') + 1);
            $adsArray['ads_response'] = json_encode($createdAdGroupAd);
            GoogleAd::create($adsArray);

            // Insert google ads log
            $input = [
                'type'     => 'SUCCESS',
                'module'   => 'Ad',
                'message'  => 'Created ad for ' . $groupDetail->ad_group_name,
                'response' => json_encode($adsArray),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/' . $campaignId . '/adgroups/' . $adGroupId . '/shopping-ad')->with('actSuccess', 'Ads created successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type'    => 'ERROR',
                'module'  => 'Ad',
                'message' => 'Create new ad > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/' . $campaignId . '/adgroups/' . $adGroupId . '/shopping-ad/create')->with('actError', $this->exceptionError);
        }
    }

    // delete ad
    public function deleteAd(Request $request, $campaignId, $adGroupId, $adId): RedirectResponse
    {
        $acDetail    = $this->getAccountDetail($campaignId);
        $account_id  = $acDetail['account_id'];
        $customerId  = $acDetail['google_customer_id'];
        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();

        try {
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);
            // Creates ad group ad resource name.
            $adGroupAdResourceName = ResourceNames::forAdGroupAd($customerId, $adGroupId, $adId);
            // Constructs an operation that will remove the ad with the specified resource name.
            $adGroupAdOperation = new AdGroupAdOperation();
            $adGroupAdOperation->setRemove($adGroupAdResourceName);

            // Issues a mutate request to remove the ad group ad.
            $adGroupAdServiceClient = $googleAdsClient->getAdGroupAdServiceClient();
            $adGroupAdServiceClient->mutateAdGroupAds(
                $customerId,
                [$adGroupAdOperation]
            );

            $ad = GoogleAd::where('adgroup_google_campaign_id', $campaignId)->where('google_adgroup_id', $adGroupId)->where('google_ad_id', $adId)->first();

            // Insert google ads log
            $input = [
                'type'     => 'SUCCESS',
                'module'   => 'Ad',
                'message'  => 'Deleted ad for ' . $groupDetail->ad_group_name,
                'response' => json_encode($ad),
            ];

            $ad->delete();

            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/' . $campaignId . '/adgroups/' . $adGroupId . '/shopping-ad')->with('actSuccess', 'Ads deleted successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type'    => 'ERROR',
                'module'  => 'Ad',
                'message' => 'Delete ad > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/' . $campaignId . '/adgroups/' . $adGroupId . '/shopping-ad')->with('actError', $this->exceptionError);
        }
    }

    //get ad status
    private function getAdStatus($v)
    {
        switch ($v) {
            case 'ENABLED':
                return AdGroupAdStatus::ENABLED;
                break;

            case 'PAUSED':
                return AdGroupAdStatus::PAUSED;
                break;

            default:
                return AdGroupAdStatus::PAUSED;
        }
    }
}
