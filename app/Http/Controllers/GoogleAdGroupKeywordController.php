<?php

namespace App\Http\Controllers;

use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;
use App\GoogleAdsGroup;
use App\Helpers\GoogleAdsHelper;
use App\Http\Requests\GoogleAdGroupKeywordCreateKeywordRequest;
use App\Models\GoogleAdGroupKeyword;
use Exception;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Common\KeywordInfo;
use Google\Ads\GoogleAds\V12\Enums\AdGroupCriterionStatusEnum\AdGroupCriterionStatus;
use Google\Ads\GoogleAds\V12\Enums\KeywordMatchTypeEnum\KeywordMatchType;
use Google\Ads\GoogleAds\V12\Resources\AdGroupCriterion;
use Google\Ads\GoogleAds\V12\Services\AdGroupCriterionOperation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoogleAdGroupKeywordController extends Controller
{
    const PAGE_LIMIT = 500;

    public $exceptionError = 'Something went wrong';

    // show campaigns in main page
    public function getstoragepath($account_id)
    {
        $result = GoogleAdsAccount::find($account_id);
        if (\Storage::disk('adsapi')->exists($account_id.'/'.$result->config_file_path)) {
            $storagepath = storage_path('app/adsapi/'.$account_id.'/'.$result->config_file_path);

            return $storagepath;
        } else {
            abort(404, 'Please add adspai_php.ini file');
        }
    }

    public function getAccountDetail($campaignId)
    {
        $campaignDetail = GoogleAdsCampaign::where('google_campaign_id', $campaignId)->where('channel_type', 'SEARCH')->first();
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
        $acDetail = $this->getAccountDetail($campaignId);
        $campaign_account_id = $acDetail['account_id'];

        $where = [
            'google_adgroup_id' => $adGroupId,
            'adgroup_google_campaign_id' => $campaignId,
        ];

        $adGroup = GoogleAdsGroup::where($where)->firstOrFail();
        $ad_group_name = $adGroup->ad_group_name;

        $keywords = GoogleAdGroupKeyword::where($where);

        if ($request->keyword) {
            $keywords = $keywords->where('keyword', 'LIKE', '%'.$request->keyword.'%');
        }

        $keywords = $keywords->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('google_ad_group_keyword.partials.list', ['keywords' => $keywords, 'campaignId' => $campaignId, 'adGroupId' => $adGroupId])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $keywords->render(),
                'count' => $keywords->total(),
            ], 200);
        }

        $totalEntries = $keywords->total();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Ad Group keyword',
            'message' => 'Viewed ad group keyword listing for '.$ad_group_name,
        ];
        insertGoogleAdsLog($input);

        return view('google_ad_group_keyword.index', ['keywords' => $keywords, 'totalNumEntries' => $totalEntries, 'campaignId' => $campaignId, 'ad_group_name' => $ad_group_name, 'campaign_account_id' => $campaign_account_id, 'adGroupId' => $adGroupId]);
    }

    // create page
    public function createPage($campaignId, $adGroupId): View
    {
        $where = [
            'google_adgroup_id' => $adGroupId,
            'adgroup_google_campaign_id' => $campaignId,
        ];

        $adGroup = GoogleAdsGroup::where($where)->firstOrFail();
        $ad_group_name = $adGroup->ad_group_name;

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Ad Group keyword',
            'message' => 'Viewed create ad group keyword for '.$ad_group_name,
        ];
        insertGoogleAdsLog($input);

        return view('google_ad_group_keyword.create', ['campaignId' => $campaignId, 'ad_group_name' => $ad_group_name, 'adGroupId' => $adGroupId]);
    }

    // create ad group
    public function createKeyword(GoogleAdGroupKeywordCreateKeywordRequest $request, $campaignId, $adGroupId): RedirectResponse
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $customerId = $acDetail['google_customer_id'];

        $where = [
            'google_adgroup_id' => $adGroupId,
            'adgroup_google_campaign_id' => $campaignId,
        ];

        $adGroup = GoogleAdsGroup::where($where)->firstOrFail();
        $ad_group_name = $adGroup->ad_group_name;

        try {
            // $storagepath = $this->getstoragepath($account_id);

            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            // Start keyword
            ini_set('max_execution_time', -1);

            $keywordArr = array_slice(explode(',', $request->suggested_keywords), 0, 80);

            foreach ($keywordArr as $keyword) {
                $keyword = substr($keyword, 0, 80);

                $keywordInfo = new KeywordInfo([
                    'text' => $keyword,
                    'match_type' => KeywordMatchType::EXACT,
                ]);

                // Constructs an ad group criterion using the keyword text info above.
                $adGroupCriterion = new AdGroupCriterion([
                    'ad_group' => ResourceNames::forAdGroup($customerId, $adGroupId),
                    'status' => AdGroupCriterionStatus::ENABLED,
                    'keyword' => $keywordInfo,
                ]);

                $adGroupCriterionOperation = new AdGroupCriterionOperation;
                $adGroupCriterionOperation->setCreate($adGroupCriterion);

                // Issues a mutate request to add the ad group criterion.
                $adGroupCriterionServiceClient = $googleAdsClient->getAdGroupCriterionServiceClient();
                $response = $adGroupCriterionServiceClient->mutateAdGroupCriteria(
                    $customerId,
                    [$adGroupCriterionOperation]
                );

                $addedKeyword = $response->getResults()[0];
                $keywordResourceName = $addedKeyword->getResourceName();
                if (! empty($keywordResourceName)) {
                    $keywordId = substr($keywordResourceName, strrpos($keywordResourceName, '~') + 1);

                    $inputKeyword = [
                        'google_customer_id' => $customerId,
                        'adgroup_google_campaign_id' => $campaignId,
                        'google_adgroup_id' => $adGroupId,
                        'google_keyword_id' => $keywordId,
                        'keyword' => $keyword,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    GoogleAdGroupKeyword::updateOrCreate(
                        [
                            'google_adgroup_id' => $adGroupId,
                            'keyword' => $keyword,
                        ],
                        $inputKeyword
                    );
                }
            }
            // End keyword

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Ad Group keyword',
                'message' => 'Created ad group keyword for '.$ad_group_name,
                'response' => json_encode($inputKeyword),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ad-group-keyword')->with('actSuccess', 'Ad group keywords added successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Ad Group keyword',
                'message' => 'Create ad group keyword > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ad-group-keyword/create')->with('actError', $this->exceptionError);
        }
    }

    // delete keyword
    public function deleteKeyword(Request $request, $campaignId, $adGroupId, $keywordId): RedirectResponse
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $customerId = $acDetail['google_customer_id'];

        // $storagepath = $this->getstoragepath($account_id);

        $where = [
            'google_adgroup_id' => $adGroupId,
            'adgroup_google_campaign_id' => $campaignId,
            'google_keyword_id' => $keywordId,
        ];

        $keyword = GoogleAdGroupKeyword::where($where)->firstOrFail();

        try {
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            // Creates ad group criterion resource name.
            $adGroupCriterionResourceName =
                ResourceNames::forAdGroupCriterion($customerId, $adGroupId, $keywordId);

            // Constructs an operation that will remove the keyword with the specified resource name.
            $adGroupCriterionOperation = new AdGroupCriterionOperation;
            $adGroupCriterionOperation->setRemove($adGroupCriterionResourceName);

            // Issues a mutate request to remove the ad group criterion.
            $adGroupCriterionServiceClient = $googleAdsClient->getAdGroupCriterionServiceClient();
            $response = $adGroupCriterionServiceClient->mutateAdGroupCriteria(
                $customerId,
                [$adGroupCriterionOperation]
            );

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Ad Group keyword',
                'message' => 'Deleted ad group keyword for '.$keyword->ad_group->ad_group_name,
                'response' => json_encode($keyword),
            ];

            $keyword->delete();

            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ad-group-keyword')->with('actSuccess', 'Ad group keyword deleted successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Ad Group keyword',
                'message' => 'Delete ad group keyword > '.$e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ad-group-keyword')->with('actError', $this->exceptionError);
        }
    }
}
