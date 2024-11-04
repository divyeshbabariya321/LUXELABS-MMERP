# Update Inventory In Magento

- The Update Inventory command is used to updating stock to magento.
- Update Inventory Journy start from `inventory:update` command in `UpdateInventory.php` file.

1. ### UpdateInventory command
   - Creating entry in `cron_job_reports` table with `signature` and `start_time` column.
   - Get products data with joining `scrapers`,`scraped_products` and `suppliers` table and checking condition to `last_cron_check` column
   - Create an empty array and attach products with sku groups
   - The chunk() takes care of fetching a small amount of data at a time and the result is present inside the closure for processing.
   - after getting data then looping through assigning the data
   - inside loop checking condition of product `isUploaded` column
   - push the scraped products ids to empty array
   - checking is exists or not `product_id` and `supplier_id`
   - get `inventory_status_histories` table in today and previous day one record
   - If lasthistory is found then assign `$prev_in_stock` to `$lasthistory->in_stock` and `$new_in_stock` to `$lasthistory->in_stock` plus 1
   - If `inventory_status_histories` table in today record found then update the data otherwise push the data to empty array
   - checking condition `last_inventory_at` condition and If it's true then push data to `$needToCheck` array
   - checking `$hasInventory` and `$productId` condition and If it's true then push data to `$productIdsArr` array
   - Update `scraped_products` table and set `last_cron_check` column to set current datetime.
   - Update `products` table and set `stock` to `0` and `updated_at` column to set current datetime.
   - Bulk insert data in `inventory_status_histories` table
2. ### CallHelperForZeroStockQtyUpdate
   - The `CallHelperForZeroStockQtyUpdate` is a job for dispatching the inventory data.
   - Inside products loop through get product store websites and push data to empty array, also deleting product details from `store_website_products` table.
   - If `$zeroStock` is not empty then calling a `callHelperForZeroStockQtyUpdate` helper function of `MagentoHelper`
3. ### MagentoHelper callHelperForZeroStockQtyUpdate
   - Inside this function loop through find store website and check `website_source` is `magento` and `api_token` is not empty
   - call `MagentoHelperv2` function `pushZeroStockQtyToMagento` and passing argument like `stockData` and `website`
   - `pushZeroStockQtyToMagento` function checking `api_token` is generated or not and If token is genrated then calling API `/rest/V1/multistore/qtyupdate` of magento otherwise it will return false.
   - Checking API response condition and If `httpcode` not `200` then return to false otherwise it return true.
   - `$result` is true then get product details and call `setRandomDescription` function with arguments like `website` and `stock` value
   - assign value `$description` to `$product->short_description`
   - In `setRandomDescription` function checking `store_website_product_attributes` is exist and it's `description` column is not empty
   - condition checking if `storeWebsiteAttributes` is not empty then assing description to `$storeWebsiteAttributes->description` otherwise random description from `product_suppliers` table.
   - If random description is not empty then get data from `store_website_product_attributes` table and If data found then assign to `$description` otherwise it will assign through `randomDescription`.
   - if description is not empty create new entry in `store_website_product_attributes` table.
