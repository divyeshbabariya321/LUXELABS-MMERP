<?php

namespace App\Http\Controllers;

use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;
use App\GoogleAdsGroup;
use App\Helpers\GoogleAdsHelper;
use App\Http\Requests\GoogleResponsiveDisplayAdCreateAdRequest;
use App\Models\GoogleResponsiveDisplayAd;
use App\Models\GoogleResponsiveDisplayAdMarketingImage;
use Exception;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClient;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Common\AdImageAsset;
use Google\Ads\GoogleAds\V12\Common\AdTextAsset;
use Google\Ads\GoogleAds\V12\Common\ImageAsset;
use Google\Ads\GoogleAds\V12\Common\ResponsiveDisplayAdInfo;
use Google\Ads\GoogleAds\V12\Enums\AdGroupAdStatusEnum\AdGroupAdStatus;
use Google\Ads\GoogleAds\V12\Enums\AssetTypeEnum\AssetType;
use Google\Ads\GoogleAds\V12\Resources\Ad;
use Google\Ads\GoogleAds\V12\Resources\AdGroupAd;
use Google\Ads\GoogleAds\V12\Resources\Asset;
use Google\Ads\GoogleAds\V12\Services\AdGroupAdOperation;
use Google\Ads\GoogleAds\V12\Services\AssetOperation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class GoogleResponsiveDisplayAdController extends Controller
{
    const PAGE_LIMIT = 500;

    public $exceptionError = 'Something went wrong';

    // show campaigns in main page
    public function getstoragepath($account_id)
    {
        $result = GoogleAdsAccount::find($account_id);
        if (Storage::disk('adsapi')->exists($account_id.'/'.$result->config_file_path)) {
            $storagepath = storage_path('app/adsapi/'.$account_id.'/'.$result->config_file_path);

            return $storagepath;
        } else {
            abort(404, 'Please add adspai_php.ini file');
        }
    }

    public function getAccountDetail($campaignId)
    {
        $campaignDetail = GoogleAdsCampaign::where('google_campaign_id', $campaignId)->first();
        if ($campaignDetail->exists() > 0) {
            return [
                'account_id' => $campaignDetail->account_id,
                'campaign_name' => $campaignDetail->campaign_name,
                'google_customer_id' => $campaignDetail->google_customer_id,
            ];
        } else {
            abort(404, 'Invalid account!');
        }
    }

    public function index(Request $request, $campaignId, $adGroupId)
    {
        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();
        $query = GoogleResponsiveDisplayAd::query();

        if ($request->headline) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('headline1', 'LIKE', '%'.$request->headline.'%');
            });
        }

        if ($request->business_name) {
            $query = $query->where('business_name', 'LIKE', '%'.$request->business_name.'%');
        }

        if ($request->final_url) {
            $query = $query->where('final_url', 'LIKE', '%'.$request->final_url.'%');
        }

        if ($request->ads_status) {
            $query = $query->where('status', $request->ads_status);
        }

        $query->where('adgroup_google_campaign_id', $campaignId)->where('google_adgroup_id', $adGroupId);
        $adsInfo = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('google_responsive_display_ad.partials.list-ads', ['ads' => $adsInfo, 'campaignId' => $campaignId, 'adGroupId' => $adGroupId])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $adsInfo->render(),
                'count' => $adsInfo->total(),
            ], 200);
        }

        $totalEntries = $adsInfo->total();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Responsive Display Ad',
            'message' => 'Viewed ad listing for '.$groupDetail->ad_group_name,
        ];
        insertGoogleAdsLog($input);

        return view('google_responsive_display_ad.index', ['ads' => $adsInfo, 'totalNumEntries' => $totalEntries, 'campaignId' => $campaignId, 'adGroupId' => $adGroupId, 'groupname' => @$groupDetail->ad_group_name]);
    }

    // go to ad create page
    public function createPage($campaignId, $adGroupId): View
    {
        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Responsive Display Ad',
            'message' => 'Viewed ad create for '.$groupDetail->ad_group_name,
        ];
        insertGoogleAdsLog($input);

        return view('google_responsive_display_ad.create', ['campaignId' => $campaignId, 'adGroupId' => $adGroupId]);
    }

    // create ad
    public function createAd(GoogleResponsiveDisplayAdCreateAdRequest $request, $campaignId, $adGroupId): RedirectResponse
    {
        $input = $request->all();

        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();

        try {

            $acDetail = $this->getAccountDetail($campaignId);
            $account_id = $acDetail['account_id'];
            $customerId = $acDetail['google_customer_id'];

            $adStatuses = ['ENABLED', 'PAUSED', 'DISABLED'];
            $adStatus = $adStatuses[$request->adStatus];

            $input['status'] = $adStatuses[$request->adStatus];
            $input['adgroup_google_campaign_id'] = $campaignId;
            $input['google_adgroup_id'] = $adGroupId;
            $input['google_customer_id'] = $customerId;

            ini_set('max_execution_time', -1);

            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            // store marketing image on folder as well as google
            $marketingImagesArr = self::storeMarketingImageOnStorageAndGoogle($googleAdsClient, $customerId, $account_id, $input['marketing_images']);
            $squareMarketingImagesArr = self::storeMarketingImageOnStorageAndGoogle($googleAdsClient, $customerId, $account_id, $input['square_marketing_images']);

            // Creates an ad and sets responsive search ad info.
            $ad = new Ad([
                'responsive_display_ad' => new ResponsiveDisplayAdInfo([
                    'headlines' => [
                        // Sets a pinning to always choose this asset for HEADLINE_1. Pinning is
                        // optional; if no pinning is set, then headlines and descriptions will be
                        // rotated and the ones that perform best will be used more often.
                        self::createAdTextAsset($input['headline1']),
                        self::createAdTextAsset($input['headline2']),
                        self::createAdTextAsset($input['headline3']),
                    ],
                    'descriptions' => [
                        self::createAdTextAsset($input['description1']),
                        self::createAdTextAsset($input['description2']),
                    ],
                    'long_headline' => self::createAdTextAsset($input['long_headline']),
                    'business_name' => $input['business_name'] ?? null,
                    'marketing_images' => array_column($marketingImagesArr, 'google_asset_resource_name'),
                    'square_marketing_images' => array_column($squareMarketingImagesArr, 'google_asset_resource_name'),
                ]),
                'final_urls' => [$input['final_url']],
            ]);

            // Creates an ad group ad to hold the above ad.
            $adGroupAd = new AdGroupAd([
                'ad_group' => ResourceNames::forAdGroup($customerId, $adGroupId),
                'status' => self::getAdStatus($adStatus),
                'ad' => $ad,
            ]);

            // Creates an ad group ad operation.
            $adGroupAdOperation = new AdGroupAdOperation;
            $adGroupAdOperation->setCreate($adGroupAd);

            // Issues a mutate request to add the ad group ad.
            $adGroupAdServiceClient = $googleAdsClient->getAdGroupAdServiceClient();
            $response = $adGroupAdServiceClient->mutateAdGroupAds($customerId, [$adGroupAdOperation]);

            $createdAdGroupAd = $response->getResults()[0];
            $createdAdGroupAdResourceName = $createdAdGroupAd->getResourceName();

            $input['google_ad_id'] = substr($createdAdGroupAdResourceName, strrpos($createdAdGroupAdResourceName, '~') + 1);
            $input['ads_response'] = json_encode($createdAdGroupAd);
            $obj = GoogleResponsiveDisplayAd::create($input);

            // Store marketing images records into database
            if ($obj->id) {
                foreach ($marketingImagesArr as $value) {
                    $value['adgroup_google_campaign_id'] = $campaignId;
                    $value['google_adgroup_id'] = $adGroupId;
                    $value['google_customer_id'] = $customerId;
                    $value['google_responsive_display_ad_id'] = $obj->id;
                    $value['type'] = 'NORMAL';
                    unset($value['google_asset_resource_name']);
                    GoogleResponsiveDisplayAdMarketingImage::create($value);
                }
                foreach ($squareMarketingImagesArr as $value) {
                    $value['adgroup_google_campaign_id'] = $campaignId;
                    $value['google_adgroup_id'] = $adGroupId;
                    $value['google_customer_id'] = $customerId;
                    $value['google_responsive_display_ad_id'] = $obj->id;
                    $value['type'] = 'SQUARE';
                    unset($value['google_asset_resource_name']);
                    GoogleResponsiveDisplayAdMarketingImage::create($value);
                }
            }

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Responsive Display Ad',
                'message' => 'Created ad for '.$groupDetail->ad_group_name,
                'response' => json_encode($input),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/responsive-display-ad')->with('actSuccess', 'Ads created successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Responsive Display Ad',
                'message' => 'Create new ad > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/responsive-display-ad/create')->with('actError', $this->exceptionError);
        }
    }

    // delete ad
    public function deleteAd(Request $request, $campaignId, $adGroupId, $adId): RedirectResponse
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $customerId = $acDetail['google_customer_id'];

        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();

        try {
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            // Creates ad group ad resource name.
            $adGroupAdResourceName = ResourceNames::forAdGroupAd($customerId, $adGroupId, $adId);

            // Constructs an operation that will remove the ad with the specified resource name.
            $adGroupAdOperation = new AdGroupAdOperation;
            $adGroupAdOperation->setRemove($adGroupAdResourceName);

            // Issues a mutate request to remove the ad group ad.
            $adGroupAdServiceClient = $googleAdsClient->getAdGroupAdServiceClient();
            $response = $adGroupAdServiceClient->mutateAdGroupAds(
                $customerId,
                [$adGroupAdOperation]
            );

            $ad = GoogleResponsiveDisplayAd::where('adgroup_google_campaign_id', $campaignId)->where('google_adgroup_id', $adGroupId)->where('google_ad_id', $adId)->first();

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Responsive Display Ad',
                'message' => 'Deleted ad for '.$groupDetail->ad_group_name,
                'response' => json_encode($ad),
            ];

            GoogleResponsiveDisplayAdMarketingImage::where('google_responsive_display_ad_id', $ad->id)->delete();

            $ad->delete();

            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/responsive-display-ad')->with('actSuccess', 'Ads deleted successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Responsive Display Ad',
                'message' => 'Delete ad > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/responsive-display-ad')->with('actError', $this->exceptionError);
        }
    }

    // delete ad
    public function show($campaignId, $adGroupId, $adId)
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];

        try {
            $record = GoogleResponsiveDisplayAd::where('adgroup_google_campaign_id', $campaignId)->where('google_adgroup_id', $adGroupId)->where('google_ad_id', $adId)->first();

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Responsive Display Ad',
                'message' => 'View ad details for '.$record->headline1,
                'response' => json_encode($record),
            ];

            insertGoogleAdsLog($input);

            return view('google_responsive_display_ad.view', compact('record', 'campaignId', 'adGroupId', 'account_id'));
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Responsive Display Ad',
                'message' => 'View ad details > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/responsive-display-ad')->with('actError', $this->exceptionError);
        }
    }

    //Creates an ad text asset with the specified text and pin field enum value.
    private function createAdTextAsset(string $text, ?int $pinField = null)
    {
        $adTextAsset = new AdTextAsset(['text' => $text]);
        if (! is_null($pinField)) {
            $adTextAsset->setPinnedField($pinField);
        }

        return $adTextAsset;
    }

    private function createAdImageAsset(string $text)
    {
        $adImageAsset = new AdImageAsset(['asset' => $text]);

        return $adImageAsset;
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

            case 'DISABLED':
                return AdGroupAdStatus::DISABLED;
                break;

            default:
                return AdGroupAdStatus::PAUSED;
        }
    }

    // uploads an image on google ads for get image asset
    private function uploadImageOnGoogleAds(GoogleAdsClient $googleAdsClient, int $customerId, $imageUrl)
    {
        $response = [];
        try {
            // Creates an image content.
            $imageContent = file_get_contents($imageUrl);

            // Creates an asset.
            $asset = new Asset([
                'name' => 'Marketing Image'.uniqid(),
                'type' => AssetType::IMAGE,
                'image_asset' => new ImageAsset([
                    'data' => $imageContent,
                ]),
            ]);

            // Creates an asset operation.
            $assetOperation = new AssetOperation;
            $assetOperation->setCreate($asset);

            // Issues a mutate request to add the asset.
            $assetServiceClient = $googleAdsClient->getAssetServiceClient();
            $response = $assetServiceClient->mutateAssets(
                $customerId,
                [$assetOperation]
            );

            $addedImageAsset = $response->getResults()[0];
            $imageAssetResourceName = $addedImageAsset->getResourceName();

            $response = [
                'asset_id' => substr($imageAssetResourceName, strrpos($imageAssetResourceName, '/') + 1),
                'asset_resource_name' => $imageAssetResourceName,
            ];
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Responsive Display Ad',
                'message' => 'Upload marketing image > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);
        }

        return $response;
    }

    // store marketing image on storage as well as google
    private function storeMarketingImageOnStorageAndGoogle(GoogleAdsClient $googleAdsClient, int $customerId, int $account_id, $images)
    {
        $response = [];
        foreach ($images as $image) {
            $uploadfile = MediaUploader::fromSource($image)->toDestination('google_ads', 'responsive_display_ad/'.$account_id)->upload();
            if ($uploadfile) {
                $getfilename = $uploadfile->filename.'.'.$uploadfile->extension;

                $imageUrl = storage_path('app/google_ads/responsive_display_ad/'.$account_id.'/'.$getfilename);
                $uploadedImageAsset = self::uploadImageOnGoogleAds($googleAdsClient, $customerId, $imageUrl);
                if (! empty($uploadedImageAsset)) {
                    $response[] = [
                        'google_asset_id' => $uploadedImageAsset['asset_id'],
                        'google_asset_resource_name' => self::createAdImageAsset($uploadedImageAsset['asset_resource_name']),
                        'name' => $getfilename,
                    ];
                }
            }
        }

        return $response;
    }
}
