<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCampaignGoogleAdsRemarketingRequest;
use App\Http\Requests\CreateCampaignGoogleAdsRemarketingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Exception;
use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;
use Illuminate\Http\Request;
use App\Helpers\GoogleAdsHelper;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\V13\Common\ManualCpc;
use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use Google\Ads\GoogleAds\V13\Resources\Campaign;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClient;
use Google\Ads\GoogleAds\V13\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V13\Services\CampaignOperation;
use Google\Ads\GoogleAds\V13\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V13\Resources\Campaign\ShoppingSetting;
use Google\Ads\GoogleAds\V13\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V13\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V13\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;

class GoogleAdsRemarketingController extends Controller
{
    // Create remarketing campaign.
    /**
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createCampaign(CreateCampaignGoogleAdsRemarketingRequest $request): JsonResponse
    {
        try {
            $insert_data = [
                'account_id'   => $request->account_id,
                'type'         => 'remarketing',
                'channel_type' => 'DISPLAY',
            ];
            $campaignStatusArr                 = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $google_account                    = GoogleAdsAccount::find($request->account_id);
            $insert_data['google_customer_id'] = $google_account->google_customer_id;
            $insert_data['campaign_name']      = $request->campaignName;
            $insert_data['budget_amount']      = $request->budgetAmount;
            $insert_data['start_date']         = $request->start_date;
            $insert_data['end_date']           = $request->end_date;
            $insert_data['status']             = $campaignStatusArr[$request->campaignStatus];

            $googleAdsClient               = GoogleAdsHelper::getGoogleAdsClientV13($google_account->id);
            $shopping_settings             = self::getShoppingSettings($google_account->google_merchant_center_account_id);
            $budget_settings               = self::addCampaignBudget($googleAdsClient, $google_account->google_customer_id, $request->budgetAmount);
            $insert_data['budget_uniq_id'] = $budget_settings['budget_uniq_id'] ?? null;
            $insert_data['budget_id']      = $budget_settings['budget_id'] ?? null;
            $budgetResourceName            = $budget_settings['budget_resource_name'] ?? null;
            $insert_data['merchant_id']    = $google_account->google_merchant_center_account_id ?? null;

            // Creates the campaign.
            $campaign = new Campaign([
                'name' => $request->campaignName,
                // Dynamic remarketing campaigns are only available on the Google Display Network.
                'advertising_channel_type' => AdvertisingChannelType::DISPLAY,
                'status'                   => self::getCampaignStatus($insert_data['status']),
                'campaign_budget'          => $budgetResourceName,
                'manual_cpc'               => new ManualCpc(),
                // This connects the campaign to the merchant center account.
                'shopping_setting' => $shopping_settings,
                'start_date'       => $insert_data['start_date'],
                'end_date'         => $insert_data['end_date'],
            ]);

            // Creates a campaign operation.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setCreate($campaign);
            // Issues a mutate request to add the campaign.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response              = $campaignServiceClient->mutateCampaigns($google_account->google_customer_id, [$campaignOperation]);

            $addedCampaign                     = $response->getResults()[0];
            $addedCampaignResourceName         = $addedCampaign->getResourceName();
            $insert_data['google_campaign_id'] = substr($addedCampaignResourceName, strrpos($addedCampaignResourceName, '/') + 1);
            $insert_data['campaign_response']  = json_encode($addedCampaign);
            GoogleAdsCampaign::create($insert_data);
            // Insert google ads log
            $input = [
                'type'     => 'SUCCESS',
                'module'   => 'Campaign',
                'message'  => 'Created new campaign',
                'response' => json_encode($insert_data),
            ];
            insertGoogleAdsLog($input);

            return response()->json(['status' => true, 'message' => 'Campaign created successfully']);
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type'    => 'ERROR',
                'module'  => 'Campaign',
                'message' => 'Create new campaign > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // Update remarketing campaign.

    /**
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateCampaign(UpdateCampaignGoogleAdsRemarketingRequest $request): RedirectResponse
    {
        try {
            $campaignDetails              = GoogleAdsCampaign::where('google_campaign_id', $request->campaignId)->first();
            $update_data                  = [];
            $campaignStatusArr            = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $update_data['campaign_name'] = $request->campaignName;
            $update_data['budget_amount'] = $request->budgetAmount;
            $update_data['start_date']    = $request->start_date;
            $update_data['end_date']      = $request->end_date;
            $update_data['status']        = $campaignStatusArr[$request->campaignStatus];

            $googleAdsClient    = GoogleAdsHelper::getGoogleAdsClientV13($campaignDetails->account_id);
            $budget_settings    = self::updateCampaignBudget($googleAdsClient, $campaignDetails->google_customer_id, $request->budgetAmount, $campaignDetails->budget_id);
            $budgetResourceName = $budget_settings['budget_resource_name'] ?? null;

            // Creates the campaign.
            $campaign = new Campaign([
                'resource_name'   => ResourceNames::forCampaign($campaignDetails->google_customer_id, $request->campaignId),
                'name'            => $request->campaignName,
                'status'          => self::getCampaignStatus($update_data['status']),
                'campaign_budget' => $budgetResourceName,
                'start_date'      => $update_data['start_date'],
                'end_date'        => $update_data['end_date'],
            ]);

            // Creates a campaign operation.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setUpdate($campaign);
            $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));
            // Issues a mutate request to add the campaign.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response              = $campaignServiceClient->mutateCampaigns($campaignDetails->google_customer_id, [$campaignOperation]);

            $updateCampaign                    = $response->getResults()[0];
            $updateCampaignResourceName        = $updateCampaign->getResourceName();
            $update_data['google_campaign_id'] = substr($updateCampaignResourceName, strrpos($updateCampaignResourceName, '/') + 1);
            $update_data['campaign_response']  = json_encode($updateCampaign);
            GoogleAdsCampaign::whereId($campaignDetails->id)->update($update_data);
            // Insert google ads log
            $input = [
                'type'     => 'SUCCESS',
                'module'   => 'Campaign',
                'message'  => 'Updated campaign details for ' . $request->campaignName,
                'response' => json_encode($update_data),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id=' . $campaignDetails->account_id)->with('actSuccess', 'Campaign updated successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type'    => 'ERROR',
                'module'  => 'Campaign',
                'message' => 'Update campaign > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/update/' . $request->campaignId . '?account_id=' . $campaignDetails->account_id)->with('actError', $e->getMessage());
        }
    }

    // Configures the settings for the shopping campaign.

    /**
     * @param mixed $merchant_id
     */
    private function getShoppingSettings($merchant_id): ShoppingSetting
    {
        return new ShoppingSetting([
            'campaign_priority' => 0,
            'merchant_id'       => $merchant_id,
            // Display Network campaigns do not support partition by country. The only
            // supported value is "ZZ". This signals that products from all countries are
            // available in the campaign. The actual products which serve are based on
            // the products tagged in the user list entry.
            'sales_country' => 'ZZ',
            'enable_local'  => true,
        ]);
    }

    //get campaign status

    /**
     * @param mixed $status
     */
    private function getCampaignStatus($status): int
    {
        switch ($status) {
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

    // create a campaign single shared budget

    /**
     * @param mixed $amount
     *
     * @return array|\Google\Ads\GoogleAds\V13\Services\MutateCampaignBudgetsResponse
     */
    public function addCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId, $amount)
    {
        $response = [];
        try {
            $uniqId = uniqid();

            // Creates a campaign budget.
            $budgetArr = [
                'name'            => 'Remarketing campaign Budget #' . $uniqId,
                'delivery_method' => BudgetDeliveryMethod::STANDARD,
                'amount_micros'   => $amount * 1000000,
            ];
            $budget = new CampaignBudget($budgetArr);
            // Creates a campaign budget operation.
            $campaignBudgetOperation = new CampaignBudgetOperation();
            $campaignBudgetOperation->setCreate($budget);

            // Issues a mutate request.
            $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
            $response                    = $campaignBudgetServiceClient->mutateCampaignBudgets(
                $customerId,
                [$campaignBudgetOperation]
            );
            $createdBudget      = $response->getResults()[0];
            $budgetResourceName = $createdBudget->getResourceName();

            $response = [
                'budget_uniq_id'       => $uniqId,
                'budget_id'            => substr($budgetResourceName, strrpos($budgetResourceName, '/') + 1),
                'budget_resource_name' => $budgetResourceName,
            ];
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type'    => 'ERROR',
                'module'  => 'Campaign',
                'message' => 'Create campaign budget > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);
        }

        return $response;
    }

    // update a campaign single shared budget

    /**
     * @param mixed $amount
     * @param mixed $campaignBudgetId
     *
     * @return array|\Google\Ads\GoogleAds\V13\Services\MutateCampaignBudgetsResponse
     */
    public function updateCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId, $amount, $campaignBudgetId)
    {
        $response = [];
        try {
            // Update a campaign budget.
            $budget = new CampaignBudget([
                'resource_name' => ResourceNames::forCampaignBudget($customerId, $campaignBudgetId),
                'amount_micros' => $amount * 1000000,
            ]);

            // Creates a campaign budget operation.
            $campaignBudgetOperation = new CampaignBudgetOperation();
            $campaignBudgetOperation->setUpdate($budget);
            $campaignBudgetOperation->setUpdateMask(FieldMasks::allSetFieldsOf($budget));

            // Issues a mutate request.
            $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
            $response                    = $campaignBudgetServiceClient->mutateCampaignBudgets(
                $customerId,
                [$campaignBudgetOperation]
            );
            $updatedBudget      = $response->getResults()[0];
            $budgetResourceName = $updatedBudget->getResourceName();

            $response = [
                'budget_id'            => substr($budgetResourceName, strrpos($budgetResourceName, '/') + 1),
                'budget_resource_name' => $budgetResourceName,
            ];
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type'    => 'ERROR',
                'module'  => 'Campaign',
                'message' => 'Update campaign budget > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);
        }

        return $response;
    }
}
