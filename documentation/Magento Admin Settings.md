# Magento Admin Settings

- The magento admin setting is used to manage magento setting.
- The magento admin settings page is listing data from `index` function in the `MagentoSettingsController` controller.

1. ### Fetch Data from DB:
   - Data for grid will fetch from the `magento_settings` table
   - Applied eloquent relationships and joins
   - Applying condition to `scope` field if request parameter `scope` not empty
   - Getting data from `magento_setting_push_logs` table
   - Applying condition to `scope` field if request parameter `website` not empty
   - Inside `website` foreach will check if request parameter `scope` is empty or not, If it empty then apply condition store webisite relationships
   - If `scope` parameter is not empty then check scope and apply condition to `magento_settings` table
   - Applying Like query to `name`, `path`, and `status` fields
   - Default order by set as `created_at` to fetch newest record first
   - Pagination is default set 25 records
   - Get data from `store_websites` table
   - Get unique `name` from `website_stores` table
   - Get unique `code` from `website_store_views` table
   - GroupBy `magento_settings` table data by `store_website_id` column
   - After getting `magento_settings` table data then inside foreach get values on magento by calling magento API `/rest/V1/configvalue/get` and save API response to `log_requests` table.
   - Get total no of magento setting record counts
2. ### AJAX Response:
   - Fetch records need to add in response to load data in datatable grid.
3. ### Listing/Set data into grid:
   Once records fetch from above Eloquent, it formatted to make compatible for server side datatable:
   - `ID` column set magento setting id.
   - `Website` column set to store website name.
   - `Store` column set store website country.
   - `Store View` column set store website language.
   - `Scope` column set scope.
   - `Name` column set magento setting name.
   - `Path` column set magento setting path.
   - `Value` column set magento setting value.
   - `Value On Magento` column set the value on magento.
   - `Date` column set created date of magento setting.
   - `Status` column set status of magento setting.
   - `Created By` column set created by user name.
   - `Action` there are three type of action provided `edit`,`delete` and `view` magento setting.
4. ### Add Magento Setting:
   Add a new magento setting
   - We can add magento setting when click on `Add Setting` button then it's shows Add Magento Setting Form inside popup dialog.
   - There are seven input controls use to add new magento setting and all are required.
   - Input field are `Scope`, `Website`, `Name`, `Path`, `Value`, `Websites` and `Data Type`.
   - When Click on save changes button, it will send on AJAX request to store magento setting data.
   - AJAX requests come to `create` method of `MagentoSettingsController` controller.
   - In `create` method can get list of store websites inside checking scope.
   - `default` scope in store website loop through check availability in magento setting If it not found it will create a new magento setting.
   - `websites` scope in website stores loop through check availability in magento setting If it not found it will create a new magento setting with new another child website magento setting.
   - `stores` scope in website store views loop through check availability in magento setting If it not found it will create a new magento setting with new another child website magento setting.
5. ### Filters
   There are five filters at top of table. `Scope`, `Website`, `Name`, `Path` and `Status` filter will apply only if it's not empty
   - First filter is scope that will search data from `scope` column
   - Second filter is website that will search data from `website` column
   - Third filter is name that will search data from `name` column
   - Fourth filter is path that will search data from `path` column
   - Five filter is status that will search data from `status` column
6. ### Push To Magento Settings:
   - We can push to magento setting when click on `Filter` button icon beside the `Sync Logs` button
   - AJAX requests come to `pushMagentoSettings` method of `MagentoSettingsController` controller.
   - request parameter `store_webiste_id` is required.
   - getting magento setting and store website details.
   - magento setting through get scope details by checking `scope` condition.
   - Base script through execute deployment script command
   - `magento_setting_push_logs` table in store command,store details and command output details.
7. ### Sync Logs:
   - We can view magento setting push logs when click on `Sync Logs` button then it's shows Sync Logs grid inside popup dialog.
   - There are three column displayed in grid `Website`, `Synced on`, and `Error Status`.
   - Date picker is available for filter grid data.
   - AJAX requests come to `magentoSyncLogSearch` method of `MagentoSettingsController` controller.
8. ### Start sync:
   - We can view magento setting push logs when click on `Start sync` button then it's update magento setting Via File
   - AJAX requests come to `updateViaFile` method of `MagentoSettingsController` controller.
   - Checking condition request in file uploaded or not
   - getting data through file_get_contents
   - Inside loop check into magento setting by checking with `path` is exist or not.
   - Base script through execute deployment script command
   - All output check with `Pull Request Successfully merged` condition and If it matching then change status to `Success`.
   - `magento_setting_push_logs` table in store command,store details and command output details.
9. ### Cron/Command to get config value from Magento:
   - This cron is created to get config value from magento and update it in magento setting table which is executeing daily midnight on `Asia/Dubai` timezone.
   - Cron used to run one command `magento:get-config-value` mentioned in `MagentoConfigValue` file.
   - When this command fired it will fetch data from `magento_settings` by grouping store_website_id.
   - From result, `/rest/V1/configvalue/get` API will call by passing `path`, `scope`, and `scope_id` parameters for each website.
   - After getting response from above API, it will store response in to `log_requests` table and updating response to `value_on_magento` column of `magento_settings` table.
