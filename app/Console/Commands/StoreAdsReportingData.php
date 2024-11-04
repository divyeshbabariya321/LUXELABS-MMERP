<?php

namespace App\Console\Commands;

use App\CronJob;
use App\GoogleAdsAccount;
use App\GoogleAdsReporting;
use App\Helpers\LogHelper;
use Google\Ads\GoogleAds\Lib\ConfigurationLoader;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClientBuilder;
use Illuminate\Console\Command;
use Symfony\Component\Config\Definition\Exception\Exception;

class StoreAdsReportingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:ads-reporting-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for store ads reporting data into database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $googleAdsAccounts = GoogleAdsAccount::has('campaigns')->with(['campaigns'])->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'GoogleAdsAccount model query finished']);

            foreach ($googleAdsAccounts as $googleAdsAccount) {
                $campaignIds = $googleAdsAccount->campaigns->pluck('google_campaign_id')->toArray();
                if (! empty($campaignIds)) {
                    $this->getAllCampaignData($googleAdsAccount, $campaignIds);
                }
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }

        return 0;
    }

    /**
     * @param  $googleAdsCampaign
     * @param  mixed  $googleAdsAccount
     * @param  mixed  $campaignIds
     *
     * @throws \Google\ApiCore\ApiException
     */
    public function getAllCampaignData($googleAdsAccount, $campaignIds)
    {
        $campaignIds = implode(',', $campaignIds);

        $storagepath = storage_path('app/adsapi/'.$googleAdsAccount->id.'/'.$googleAdsAccount->config_file_path);        // Get OAuth2 configuration from file.

        if (! file_exists($storagepath)) {
            return true;
        }
        $oAuth2Configuration = (new ConfigurationLoader)->fromFile($storagepath);

        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder)->from($oAuth2Configuration)->build();

        $googleAdsClient = (new GoogleAdsClientBuilder)
            ->from($oAuth2Configuration)
            ->withOAuth2Credential($oAuth2Credential)
            ->build();
        $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();

        // get data from google query
        $query = "SELECT 
                  metrics.clicks, 
                  metrics.impressions, 
                  metrics.ctr, 
                  metrics.average_cpc, 
                  ad_group_ad.ad.id, 
                  -- ad_group_ad.ad.name, 
                  ad_group.name, 
                  ad_group.id, 
                  -- ad_group_ad.ad.type, 
                  -- ad_group_ad.ad.responsive_search_ad.headlines, 
                  campaign.id, 
                  campaign.name, 
                  campaign.advertising_channel_sub_type, 
                  campaign.advertising_channel_type 
                FROM ad_group_ad 
                WHERE 
                  campaign.id IN ($campaignIds) AND segments.date DURING TODAY";

        $stream = $googleAdsServiceClient->search($googleAdsAccount->google_customer_id, $query);

        // Iterates over all rows in all messages and prints the requested field values for
        // the keyword in each row.
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $metrics = $googleAdsRow->getMetrics();
            $campaign = $googleAdsRow->getCampaign();
            $adGroup = $googleAdsRow->getAdGroup();
            $ad = $googleAdsRow->getAdGroupAd()->getAd();

            try {
                $input = [
                    'google_customer_id' => $googleAdsAccount->google_customer_id,
                    'adgroup_google_campaign_id' => $campaign->getId(),
                    'google_adgroup_id' => $adGroup->getId(),
                    'google_ad_id' => $ad->getId(),
                    'google_account_id' => $googleAdsAccount->id,
                    'campaign_type' => self::getCampaignType($campaign->getAdvertisingChannelType()),
                    'impression' => $metrics->getImpressions(),
                    'click' => $metrics->getClicks(),
                    'cost_micros' => $metrics->getCostMicros(),
                    'average_cpc' => $metrics->getAverageCpc(),
                    'date' => date('Y-m-d'),
                ];
                GoogleAdsReporting::updateOrCreate([
                    'google_customer_id' => $googleAdsAccount->google_customer_id,
                    'adgroup_google_campaign_id' => $campaign->getId(),
                    'google_adgroup_id' => $adGroup->getId(),
                    'google_ad_id' => $ad->getId(),
                    'google_account_id' => $googleAdsAccount->id,
                    'date' => date('Y-m-d'),
                ], $input);

                $this->info('Store reporting data for ad: '.$ad->getId());
            } catch (Exception $e) {
                return response()->json(['code' => 400, 'error' => 'Opps! Something went wrong, Please try again.'], 400);
            }
        }
    }

    private function getCampaignType($advertisingChannelTypeId)
    {
        $data = [
            0 => 'UNSPECIFIED',
            2 => 'SEARCH',
            3 => 'DISPLAY',
            4 => 'SHOPPING',
            7 => 'MULTI_CHANNEL',
        ];

        return $data[$advertisingChannelTypeId] ?? $data[0];
    }
}
