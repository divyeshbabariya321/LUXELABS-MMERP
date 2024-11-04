<?php

namespace App\Http\Controllers;

use App\GoogleAd;
use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;
use App\GoogleAdsGroup;
use App\Helpers\GoogleAdsHelper;
use App\Http\Requests\CreateAdGoogleAdRequest;
use Exception;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Common\AdTextAsset;
use Google\Ads\GoogleAds\V12\Common\ResponsiveSearchAdInfo;
use Google\Ads\GoogleAds\V12\Enums\AdGroupAdStatusEnum\AdGroupAdStatus;
use Google\Ads\GoogleAds\V12\Enums\ServedAssetFieldTypeEnum\ServedAssetFieldType;
use Google\Ads\GoogleAds\V12\Resources\Ad;
use Google\Ads\GoogleAds\V12\Resources\AdGroupAd;
use Google\Ads\GoogleAds\V12\Services\AdGroupAdOperation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GoogleAdsController extends Controller
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
        $query = GoogleAd::query();

        if ($request->headline) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('headline1', 'LIKE', '%'.$request->headline.'%')->orWhere('headline2', 'LIKE', '%'.$request->headline.'%')
                    ->orWhere('headline3', 'LIKE', '%'.$request->headline.'%');
            });
        }

        if ($request->description) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('description1', 'LIKE', '%'.$request->description.'%')->orWhere('description2', 'LIKE', '%'.$request->description.'%');
            });
        }

        if ($request->path) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('path1', 'LIKE', '%'.$request->path.'%')->orWhere('path2', 'LIKE', '%'.$request->path.'%');
            });
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
                'tbody' => view('googleads.partials.list-ads', ['ads' => $adsInfo, 'campaignId' => $campaignId, 'adGroupId' => $adGroupId])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $adsInfo->render(),
                'count' => $adsInfo->total(),
            ], 200);
        }

        $totalEntries = $adsInfo->total();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Ad',
            'message' => 'Viewed ad listing for '.$groupDetail->ad_group_name,
        ];
        insertGoogleAdsLog($input);

        return view('googleads.index', ['ads' => $adsInfo, 'totalNumEntries' => $totalEntries, 'campaignId' => $campaignId, 'adGroupId' => $adGroupId, 'groupname' => @$groupDetail->ad_group_name]);
    }

    // getting ads
    public function getAds(AdWordsServices $adWordsServices, AdWordsSession $session, $adGroupId)
    {
        $adGroupAdService = $adWordsServices->get($session, AdGroupAdService::class);

        // Create a selector to select all ads for the specified ad group.
        $selector = new Selector;
        $selector->setFields(
            ['Id', 'Status', 'HeadlinePart1', 'HeadlinePart2', 'Description']
        );
        $selector->setOrdering([new OrderBy('Id', SortOrder::ASCENDING)]);
        $selector->setPredicates(
            [
                new Predicate('AdGroupId', PredicateOperator::IN, [$adGroupId]),
                new Predicate(
                    'AdType',
                    PredicateOperator::IN,
                    [AdType::EXPANDED_TEXT_AD]
                ),
                new Predicate(
                    'Status',
                    PredicateOperator::IN,
                    [AdGroupAdStatus::ENABLED, AdGroupAdStatus::PAUSED]
                ),
            ]
        );

        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));

        $totalNumEntries = 0;
        $ads = [];
        do {
            // Retrieve ad group ads one page at a time, continuing to request pages
            // until all ad group ads have been retrieved.
            $page = $adGroupAdService->get($selector);

            // Print out some information for each ad group ad.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $adGroupAd) {
                    $ad = $adGroupAd->getAd();
                    $ads[] = [
                        'adId' => $ad->getId(),
                        'status' => $adGroupAd->getStatus(),
                        'headlinePart1' => $ad->getHeadlinePart1(),
                        'headlinePart2' => $ad->getHeadlinePart2(),
                        'description' => $ad->getDescription(),
                        'type' => $ad->getAdType(),
                    ];
                }
            }

            $selector->getPaging()->setStartIndex(
                $selector->getPaging()->getStartIndex() + self::PAGE_LIMIT
            );
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);

        return [
            'ads' => $ads,
            'totalNumEntries' => $totalNumEntries,
        ];
    }

    // go to ad create page
    public function createPage($campaignId, $adGroupId): View
    {
        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Ad',
            'message' => 'Viewed ad create for '.$groupDetail->ad_group_name,
        ];
        insertGoogleAdsLog($input);

        return view('googleads.create', ['campaignId' => $campaignId, 'adGroupId' => $adGroupId]);
    }

    // create ad
    public function createAd(CreateAdGoogleAdRequest $request, $campaignId, $adGroupId): RedirectResponse
    {
        $groupDetail = GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->firstOrFail();

        //create account

        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $customerId = $acDetail['google_customer_id'];

        $adStatuses = ['ENABLED', 'PAUSED', 'DISABLED'];
        $headlinePart1 = $request->headlinePart1;
        $headlinePart2 = $request->headlinePart2;
        $headlinePart3 = $request->headlinePart3;
        $description1 = $request->description1;
        $description2 = $request->description2;
        $finalUrl = $request->finalUrl;
        $path1 = $request->path1;
        $path2 = $request->path2;
        $adStatus = $adStatuses[$request->adStatus];

        $adsArray = [];
        $adsArray['google_customer_id'] = $customerId;
        $adsArray['adgroup_google_campaign_id'] = $campaignId;
        $adsArray['google_adgroup_id'] = $adGroupId;
        $adsArray['headline1'] = $headlinePart1;
        $adsArray['headline2'] = $headlinePart2;
        $adsArray['headline3'] = $headlinePart3;
        $adsArray['description1'] = $description1;
        $adsArray['description2'] = $description2;
        $adsArray['final_url'] = $finalUrl;
        $adsArray['path1'] = $path1;
        $adsArray['path2'] = $path2;
        $adsArray['status'] = $adStatus;

        try {
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            // Creates an ad and sets responsive search ad info.
            $ad = new Ad([
                'responsive_search_ad' => new ResponsiveSearchAdInfo([
                    'headlines' => [
                        // Sets a pinning to always choose this asset for HEADLINE_1. Pinning is
                        // optional; if no pinning is set, then headlines and descriptions will be
                        // rotated and the ones that perform best will be used more often.
                        self::createAdTextAsset($headlinePart1, ServedAssetFieldType::HEADLINE_1),
                        self::createAdTextAsset($headlinePart2, ServedAssetFieldType::HEADLINE_2),
                        self::createAdTextAsset($headlinePart3, ServedAssetFieldType::HEADLINE_3),
                    ],
                    'descriptions' => [
                        self::createAdTextAsset($description1, ServedAssetFieldType::DESCRIPTION_1),
                        self::createAdTextAsset($description2, ServedAssetFieldType::DESCRIPTION_2),
                    ],
                    'path1' => $path1 ?? null,
                    'path2' => $path2 ?? null,
                ]),
                'final_urls' => [$finalUrl],
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

            $adsArray['google_ad_id'] = substr($createdAdGroupAdResourceName, strrpos($createdAdGroupAdResourceName, '~') + 1);
            $adsArray['ads_response'] = json_encode($createdAdGroupAd);
            GoogleAd::create($adsArray);

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Ad',
                'message' => 'Created ad for '.$groupDetail->ad_group_name,
                'response' => json_encode($adsArray),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ads')->with('actSuccess', 'Ads created successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Ad',
                'message' => 'Create new ad > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ads/create')->with('actError', $this->exceptionError);
        }
    }

    // go to ad update page
    public function updatePage() {}

    // update ad
    public function updateAd() {}

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

            $ad = GoogleAd::where('adgroup_google_campaign_id', $campaignId)->where('google_adgroup_id', $adGroupId)->where('google_ad_id', $adId)->first();

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Ad',
                'message' => 'Deleted ad for '.$groupDetail->ad_group_name,
                'response' => json_encode($ad),
            ];

            $ad->delete();

            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ads')->with('actSuccess', 'Ads deleted successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Ad',
                'message' => 'Delete ad > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ads')->with('actError', $this->exceptionError);
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
