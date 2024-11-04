# Magento Log Errors

The Magento log errors page is listing data from `records` function in the `MagentoProductPushErrors` controller.

1. ### Fetch Magento log errors from DB:
   Magento log will fetch from the `product_push_error_logs` table with the following conditions:
   - If request parameter `website` is not empty and not `all` then it applies condition to `id` of `store_website` table otherwise it directly creates relationship with `store_website` table
   - Applying LIKE query to `message` field if request parameter `keyword` is not empty
   - Applying LIKE query to `response_status` field if request parameter `response_status` is not empty
   - Applying date filter for single date on `created_at` field if request parameter `log_date` is not empty
   - Pagination `paginate` has set to `50` records
   - Default order by set as `latest` to fetch latest record first
2. ### Response JSON:
   - Fetch records need to be in an array that is assigned to `$recorsArray` and rest of parameters need to add in response JSON to load data in datatable grid.
   - `code` assign value to `200`
   - `pagination` assign value `(string) $records->links()`
   - `total` assign value to `$records->total()`
   - `page` assign value to `$records->currentPage()`
3. ### Listing/Set data into grid:
   Once records fetch from above Eloquent, it formatted to make compatible for server side datatable:
   - `Product ID` column set from `$row->product_id`
   - `Date` column set from `$row->created_at`->format('d-m-y H:i:s')
   - `Website` column set from `$row->store_website` and check if it exists or not otherwise set to (-).
   - `Message` get using `$row->message` with set limit of 30 characters. Also, on click, it will display full logs.
   - `Request data` get using `$row->request_data` with set limit of 30 characters. Also, on click it will display full logs.
   - `Response Data` get using `$row->response_data` with set limit of 30 characters.Also, on click it will display full logs.
   - `Condition Checked` column will display data if following conditions satisfy:
     - If requested `condition_id` is not null and set to perticular record then it will check id from `push_to_magento_conditions` table and display name in this column.
   - `Status` column has drop down option like success,error,php,magento,message,translation_not_found to update status manually and it auto select one if it save in `response_status` column
   - In `Status` column, there is informatic icon. On click it opening modal that contain history of status change.
4. ### Filters

   There are four filters at top of table. Keyword, Date, Website and Status filter will apply only if it's not empty

   - First filter is keyword that will search data from `message` column
   - Second filter is to search matched records from `created_at` for requested log_date
   - Third filter is drop down of `store_website` column for request parameter website.
   - Fourth filter contain drop down of status options
     Once user enters or select one or more filter, have to click on search icon.

   Seach process will filter data from mentioned column and grid will be updated from response JSON. Response format is same as mentioned in second step.

5. ### Reports

   There are two types of option available at top-right corner to either export file or view in browser

   1. #### Export Today Common Errors Report
      This report will download excel file which contains multiple rows of same message and it's count for today's date only.
      - On clicking this button, will fetch records from `product_push_error_logs` table.
      - Fitler it for today's date from `created_at` column, group by `message` column and calculate total number of message in `count`.
      - Fetched records will be processes to new array to set them in `count` and `message` key
      - This newly created array is requested to export function `MagentoProductCommonError` that will generate and download excel file using `Maatwebsite` composer package.
   2. #### Today Common Errors Report
      - On clicking this button, it will send on AJAX request to display data in pop up.
      - AJAX requests come to `groupErrorMessageReport` method of `MagentoProductPushErrors` controller. This function fetch records from `product_push_error_logs` table.
      - Default order by set as `latest` to fetch latest record first
      - Grouped by `message` column and calculate total number of message in `count`.
      - Applying date filter for single date on `created_at` field if request parameter `startDate` and `endDate` is not empty otherwise it will be set default date as current date.
      - After query executed, result will processed to make it compatible with final response. Query result will process through loop to check - If `message` column containing sub string `Failed readiness` then initialize default count, message and status otherwise query result count will assigned to response array
      - After `usort` user-defined and `rsort` reverse sort, response will be return to that AJAX call.
      - There are only two parameters are in AJAX response
        - `code` assign value to `200`
        - `data` assign value to `$recordsArr`
      - Once AJAX response available in front end, pop up will open to display result in tabular format.Table contain column of count of same message, status and message text. By default it filter for today only.
      - There are date range filter which allows you to select multiple dates and get records based on requested date
