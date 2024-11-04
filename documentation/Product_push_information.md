# Product Information Update

Purpose to create this page is for import/update bulk products from csv and listed out in grid with filters.

The process starts from `productPushInformation` function in the `LogListMagentoController`.

1. ### Listing - Fetch product information from DB:
   - `push_product_informations` table directly creates relationship with `store_website` table.
   - If request parameter `brand_names` or `category_names` is not empty then it applies Like query to `sku` column.
   - If request parameter `brand_names` is not empty then it will assign `selected_brands` to select query data of `brands` table.
   - If request parameter `category_names` is not empty then it will assign `selected_categories` to select query data of `categories` table.
   - If request parameter `website_name` is not empty then get data from `store_website` table in get ids and apply condition to `push_product_informations` table with selected ids data.
   - `store_website` table directly relationship with `website_product_csvs` table.
   - If request parameter `filter_product_id` is not empty then it applies Like query to `product_id` column.
   - If request parameter `filter_product_sku` is not empty then it applies Like query to `sku` column.
   - If request parameter `filter_product_status` is not empty then it applies Like query to `status` column.
   - Pagination `paginate` has set to `settings` table in get value `pagination`.
   - Dropdown list get from `push_product_informations` table and select only unique `status`.
   - Get total count of records from `push_product_informations` table.
   - Relationship created for `product_push_information_summeries` table with `brand`,`categories` and `store_websites` table to get latest records based on `created_at` column.
   - Final query created from above and will fetch records from database. Result formatted to make compatible of blade file.
   - Response will load in blade with all parameters.
2. ### Filters
   - There are six filters available at top of grid, product id, SKU, status, brands, category, website. On click filter icon, new records will fetch from db based on requested one or more filters.
3. ### Refresh grid
   - Reload icon available after filters and above grid to get up to date records from database.
4. ### Summary:
   - This button will display all the products which were updated in default or selected date range.
   - Mainly it listout Store website, Brand, Category, Product count and Created at in table.
   - When someone click on this button, it starts from `updateProductPushInformationSummery` function in the `LogListMagentoController`.
   - `product_push_information_summeries` table directly creates relationship with `brands`,`categoreis` and `	store_websites` table.
   - Applying date filter for single date on `created_at` field.
   - Return result from above query and set in table.
5. ### Read CSV:
   - This provision is used to import/update multiple products of each store and this button available at top right corner.
   - Once you click, it open popup with list of all store website and another input box available to enter URL of CSV. Once you enter URL for multiple stores, need to click on Send icon, that will actually fetch csvm parse it and update products in database.
   - After completing this process, click on "Update data" button which available in popup footer that will save csv path in `website_product_csvs` table.
   - Read CSV starts from `updateProductPushInformation` function in the `LogListMagentoController`.
   - If request parameter `website_url` is empty then showing error.
   - `GuzzleHttpClient` object created and get method through pass `file_url` parameter.
   - If file is exists then start `fgetcsv` function using read csv data.
   - Checking Availability of product by checking `sku`.
   - `Update` or `create` operation of `push_product_informations` table.
   - Updating `is_available` status to `1` with checking `product_id`,`store_website_id` and `is_available` condition.
   - `json` through return response `message` parameter.
