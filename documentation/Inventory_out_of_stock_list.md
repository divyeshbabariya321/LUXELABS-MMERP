# Inventory out of stock List

The Inventory out of stock page is listing product's inventory. Data listed from `inventoryListNew` function in the `ProductInventoryController` controller.

1. ### Fetch Inventory from DB:
   Inventory will fetch from the `products` table with the following conditions:
   - join `products` table to `store_website_product_attributes`,`brands` and `categories` table
   - If request parameter `start_date` and `end_date` is not empty then it applies condition to `created_at` of `products` table.
   - If request parameter `brand_names` is not empty then it applies condition to `brand` column `products` table.
   - Fetch filter in selected brand data from `brands` table.
   - Applying LIKE query to `products.name`,`products.sku`,`categories.title`,`brands.name` and `products.id` field if request parameter `term` is not empty.
   - Default order by set as `store_website_product_attributes` table in `created_at` column to fetch latest record first
   - Pagination `paginate` has set to `20` records
   - Total no of inventory count also fetch from full query to display it after module name
2. ### Fetch Inventory summary from DB:
   totalProduct will fetch from the `products` table with the below conditions: - join `products` table to `suppliers`,`scraped_products` table with checking `supplier_status_id` condition.
   - get no of product stock counts by using check `stock` value is greater than 0.
   - get product updated counts from `inventory_status_histories` table.
   - get inventory history list from `inventory_status_histories` table and it will take 7 records.
3. ### Response HTML:
   Once both result fetch it add below parameters to bind in response HTML that will load data in grid.
   - `inventory_data` assign value to `$inventory_data`
   - `inventory_data_count` assign value `$inventory_data_count`
   - `noofProductInStock` assign value to `$noofProductInStock`
   - `productUpdated` assign value to `$productUpdated`
   - `totalProduct` assign value to `$totalProduct`
   - `history` assign value to `$history`
   - `selected_brand` assign value to `$selected_brand`
   - `term` assign value to `$term`
4. ### Inventory Summary data into grid:
   Eloquent result formatted to make compatible for server side datatable:
   - `Total Product` column set from `$totalProduct`
   - `No of product in stock` column set from `$noofProductInStock`
   - `No of product Updated` column set from `$productUpdated`
   - `No of product Not updated` column set from `($totalProduct - $productUpdated)`
   - `History` column show icon and when click on it then show popup and set data from `$history`
5. ### Inventory Listing/Set data into grid:
   Eloquent result for listing data will be formatted to bind response in server side datatable:
   - `ID` column set from `$data['id']`
   - `SKU` column set from `$data['sku']`
   - `Name` column set from `$data['product_name']`
   - `Brand` column set from `$data['brand_name']`
   - `Supplier` column set from `$data['supplier']`
   - `Stock` column set default to 0
   - `Created Date` column set from `$data['created_at']`
   - `Action` column set show history button icon and when click on it then show popup and set data of specific inventory history in popup grid.
6. ### Filters
   There are four filters at top of table. Start date, End date Keyword, and Brand will apply only if it's not empty.
   - First and second filter is to search matched records from `created_at` column of `products` table for requested created_date
   - Third filter is keyword that will search data from product `products.name`,`products.sku`,`categories.title`,`brands.name` and `products.id` columns
   - Fourth filter contain drop down of brand options
     Once user enters or select one or more filter, have to click on `Filter` button.
     Seach process will filter data from mentioned column and grid will be updated.
