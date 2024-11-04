# Google AdWords Log

The Google AdWords log page shows the success and failur log of account, campaign, ads group and ad module, and It's listing data from `index` method in the `GoogleAdsLogController` controller.

1. ### Fetch log records from DB:

   It will fetch from the `google_ads_logs` table with the following conditions:

   - Applying `user` relationship with `users` table to fetch user details
   - Applying LIKE query to `name` field on `user` relationship if request parameter `user_name` is not empty
   - Applying LIKE query to `type` field if request parameter `type` is not empty
   - Applying LIKE query to `module` field if request parameter `module` is not empty
   - Applying LIKE query to `message` field if request parameter `message` is not empty
   - Applying DATE function to `created_at` field if request parameter `created_at` is not empty
   - Pagination `paginate` has set to `Setting` table `pagination` key value for records
   - Default order by set as `created_at` to `desc` for fetch latest record first

2. ### Listing/Set data into grid:

   Once records fetch from above Eloquent, it formatted to make compatible for server side datatable:

   - `User name` column set from `$log->user->name`
   - `Module` column set from `$log->module`
   - `Type` column set from `$log->type`
   - `Message` get using `$row->message` with set limit of 110 characters. Also, on click, it will display full logs.
   - `Type` column will display data if following conditions satisfy:
   - If `$log->type` is `SUCCESS`
     - set to `GREEN` colour and shows `SUCCESS`
   - else
     - set to `RED` colour and shows `ERROR`
   - `Created At` column set from `$row->created_at`->format('d-m-y H:i:s')

3. ### Response JSON:

   Fetch records need to add in response JSON to load data in datatable grid.

   - `code` assign value to `200`
   - `links` assign value `$logs->links()`
   - `count` assign value to `$logs->total()`
   - `tbody` assign value to render list html from `google_ads_log.partials.list` using `$logs`

4. ### Filters:

   There are five filters at first row of table. User Name, Type, Module, Message and Created At filter will apply only if it's not empty

   - First filter is to search matched records from `users` table `name` column for requested parameter user_name
   - Second filter is to search matched records from `type` column for requested parameter type
   - Third filter is to search matched records from `module` column for requested parameter module
   - Fourth filter is to search matched records from `message` column for requested parameter message
   - Fifth filter is to search matched records from `created_at` column for requested parameter created_at

   Search process will filter data from mentioned column and grid will be updated from response JSON. Response format is same as mentioned in third step.
