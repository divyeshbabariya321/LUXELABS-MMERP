# Google Ads Report

The Google Ads Report page shows the report of google ads with Impression, Click, Cost Micros, Average CPC, and It's listing data from `index` method in the `GoogleAdReportController` controller.

1. ### Fetch log records from DB:

   It will records fetch from the `google_ads_reportings` table with the following conditions:

   - Applying `account` relationship with `googleadsaccounts` table to fetch account details
   - Applying `campaign` relationship with `googlecampaigns` table to fetch campaign details
   - Applying `adgroup` relationship with `googleadsgroups` table to fetch adgroup details
   - Applying `search_ad` relationship with `googleads` table to fetch search ad details
   - Applying `display_ad` relationship with `google_responsive_display_ads` table to fetch display ad details
   - Applying `multi_channel_ad` relationship with `google_app_ads` table to fetch multi-channel ad details
   - Applying LIKE query to `google_account_id` field if request parameter `account_id` is not empty
   - Applying LIKE query to `adgroup_google_campaign_id` field if request parameter `campaign_id` is not empty
   - Applying LIKE query to `google_adgroup_id` field if request parameter `adgroup_id` is not empty
   - Applying LIKE query to `status` field on `campaign` relationship if request parameter `campaign_status` is not empty
   - Applying DATE function to `created_at` field if request parameter `start_date` is not empty
   - Applying DATE function to `created_at` field if request parameter `start_date` is not empty
   - Pagination `paginate` has set to value `20` for records
   - Default order by set as `created_at` to `desc` for fetch latest record first

2. ### Listing/Set data into grid:

   Once records fetch from above Eloquent, it formatted to make compatible:

   - `Account Id` column set from `$record->account->id`
   - `Account Name` column set from `$record->account->account_name`
   - `Google Customer Id` column set from `$record->account->google_customer_id`
   - `Campaign Name` column set from `$record->campaign->campaign_name`
   - `Google Campaign Id` column set from `$record->campaign->google_campaign_id`
   - `Channel Type` column set from `$record->campaign->channel_type`
   - `Ad Group` column set from `$record->adgroup->ad_group_name`
   - `Google Ad Group Id` column set from `$record->adgroup->google_adgroup_id`
   - `Google Ad Id` column set from `$record->google_ad_id`
   - `Ad Headline1` column will display data if following conditions satisfy:
   - If `$record->search_ad->headline1` is not `NULL`
     - `Ad Headline1` column set from `$record->search_ad->headline1`
   - else If `$record->display_ad->headline1` is not `NULL`
     - `Ad Headline1` column set from `$record->display_ad->headline1`
   - else
     - `Ad Headline1` column set from `$record->multi_channel_ad->headline1`
   - `Campaign Status` column set from `$record->campaign->status`
   - `Impression` column set from `$record->sum_impression`
   - `Click` column set from `$record->sum_click`
   - `Cost Micros` column set from `$record->sum_cost_micros`
   - `Average CPC` column set from `$record->sum_average_cpc`

3. ### Filters:

   There are six filters at top of table. Account Name, Campaign Name, Ad Group Name, Campaign Status, Start Date and End Date filter will apply only if it's not empty

   - First filter is to search matched records from `adgroup_google_campaign_id` column for requested parameter `campaign_id`
   - Second filter is to search matched records from `google_adgroup_id` column for requested parameter `adgroup_id`
   - Third filter is to search matched records from `google_account_id` column for requested parameter `account_id`
   - Fourth filter is to search matched records from `campaign` table `status` column for requested parameter `campaign_status`
   - Fifth filter is to search matched records from `created_at` column for requested parameter `start_date`
   - Sixth filter is to search matched records from `created_at` column for requested parameter `end_date`

   Search process will filter data from mentioned column and shows list according.
