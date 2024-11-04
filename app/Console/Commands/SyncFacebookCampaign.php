<?php

namespace App\Console\Commands;

use App\Models\SocialAdAccount;
use App\Services\Facebook\FB;
use App\Social\SocialAd;
use App\Social\SocialAdCreative;
use App\Social\SocialAdset;
use App\Social\SocialCampaign;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncFacebookCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all facebook campaign linked with the added ad accounts';

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
        $ad_accounts = SocialAdAccount::where('status', 1)->get();
        foreach ($ad_accounts as $account) {
            $fb = new FB($account);
            $results = $fb->getCampaigns($account->ad_account_id);
            foreach ($results['campaigns'] as $campaign) {
                $camp = SocialCampaign::updateOrCreate(['ref_campaign_id' => $campaign['id']], [
                    'config_id' => $account->id,
                    'name' => $campaign['name'],
                    'objective_name' => $campaign['objective'],
                    'buying_type' => $campaign['buying_type'],
                    'daily_budget' => $campaign['daily_budget'] ?? null,
                    'live_status' => $campaign['status'],
                    'created_at' => Carbon::parse($campaign['created_time']),
                ]);

                if (isset($campaign['adsets'])) {
                    foreach ($campaign['adsets'] as $adset) {
                        $ads = SocialAdset::updateOrCreate(['ref_adset_id' => $adset['id']], [
                            'config_id' => $account->id,
                            'name' => $adset['name'],
                            'campaign_id' => $camp->id,
                            'destination_type' => $adset['destination_type'],
                            'billing_event' => $adset['billing_event'],
                            'start_time' => Carbon::parse($adset['start_time']),
                            'end_time' => Carbon::parse($adset['end_time']),
                            'daily_budget' => $adset['daily_budget'] ?? null,
                            'bid_amount' => $adset['bid_amount'] ?? null,
                            'status' => $adset['effective_status'] ?? null,
                            'live_status' => $adset['status'],
                            'created_at' => Carbon::parse($adset['created_time']),
                        ]);
                        if (isset($adset['adcreatives'])) {
                            foreach ($adset['adcreatives'] as $adcreative) {
                                SocialAdCreative::updateOrCreate([
                                    'ref_adcreative_id' => $adcreative['id'],
                                ], [
                                    'name' => $adcreative['title'] ?? $adcreative['name'],
                                    'config_id' => $account->id,
                                    'object_story_title' => $adcreative['title'] ?? null,
                                    'object_story_id' => $adcreative['object_story_id'] ?? null,
                                    'live_status' => $adcreative['status'],
                                ]);
                            }
                        }
                    }
                }
            }

            foreach ($results['campaigns'] as $campaign) {
                if (isset($campaign['adsets'])) {
                    foreach ($campaign['adsets'] as $adset) {
                        if (isset($adset['ads'])) {
                            foreach ($adset['ads'] as $ad) {
                                $creative = SocialAdCreative::where('ref_adcreative_id', $ad['creative']['id'])->select('id')->first()->toArray();
                                SocialAd::updateOrCreate(['ref_ads_id' => $ad['id']], [
                                    'adset_id' => $ads->id,
                                    'config_id' => $account->id,
                                    'name' => $ad['name'],
                                    'creative_id' => $creative['id'],
                                    'ad_set_name' => $adset['name'],
                                    'ad_creative_name' => $ad['creative']['name'],
                                    'status' => $ad['status'],
                                    'live_status' => $ad['effective_status'],
                                    'created_at' => Carbon::parse($ad['created_time']),
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
