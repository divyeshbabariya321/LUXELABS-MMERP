# Flow1 Jobs

1. `PushProductOnlyJob`: This job used to get single products, find all store websites and send in second job.
2. `PushToMagento`: Second job created for each store website, and check basic conditions.
3. `MagentoServiceJob`: It check all conditions and call Magento's rest API to push single product on single store website.

# Flow2 jobs

## Condition Checking from final approved pages

1. `Flow2ConditionCheckProductOnly`: This job used to get single products, find all store websites and send in second job
2. `Flow2ConditionCheckBasic`: Second job created for each store website, and check basic conditions.
3. `Flow2ConditionCheckAll`: It is checking all conditions and save status of all-conditions-checked in db-table

## Push Product from condition checked page

1. `Flow2PushProductOnlyJob`: This job used to get single products, find all store websites and send in second job.
2. `Flow2PushToMagento`: Second job created for each store website, and call Magento's rest API to push single product on single store website.

# Product Push Flow 1

## Product Push Journey

The product push journey starts from `pushProduct` function in the `ProductController`.

1. ### Products selection:
   When the product push initiates, approved products will fetch from the `products` table with the following conditions:
   - `short_description` is not `null`
   - `name` is not `null`
   - `status_id` is `9`: _(9 represents the final approval status of a product)_
   - Grouped by `brand` and `category`
   - Default data fetching `limit` has set to `100`, this will be replaced if the `no_of_product` parameter has value
   - After product selection, it will pass all the selected products to in `pushproductonly` queue. Also passed `product`, total product(`no_of_product`) and current product order(`product_index`) to find them in Horizon and to be make all jobs are completed. Each queue well dispatched by `PushProductOnlyJob`.
2. ### PushProductOnlyJob:

   - This job is to get store websites by tag that have the same category & brand of the product and check if it must exist in the store_website table.
   - **How work tag functionality:**
     - This functionality is used get parent store website with all child website by using tag group.
   - Store websites which are has the these products will identified in this step. For that the **ids** of product that fetched in the **Products selection** will be sent to `ProductHelper::getStoreWebsiteNameByTag` which returns the and array with list of store websites which has these products. The flow to get the store website ids as follows:
   - **Get store website category:** Selected products will be looped to fetch all store categories from the `store_website_categories` which has a valid `remote_id` and which matches to the each product's `category_id`.
     - **Get store website ids from store website brands:** Fetched store website categories will be looped to get all the store website brands from the `store_website_brands` table. This will returns all the brands which matches the `store_website_id` of the each category and matches `brand_id` of each product get looped and which has a valid `magento_value`. The brands get from this steps will again loop to make an array (`$websiteArray`) of store websites ids.
     - **Get store website ids from the products which are landing page products:** if a product has an entry in `landing_page_products` table, that will be checked here as first step. Then get all store websites which has `o-labels` string in the `title` and `cropper_color` is not `null`. This store websites will be looped and will be pushed to `$websiteArray` if it is not already exists.

   After these steps it will return store websites from `store_websites` table with checking specified store website ids(`$websiteArray`) and groupping data by `tag_id` column.

3. ### Start push to magento:
   - Loops the store websites and checks each website are exists. If the website doesn't exist, the error will be logged to `product_push_error_logs`.
   - if the website exists, a log will be added to the `log_list_magentos` table with the `product_id`, `message`, `store_website_id`, `sync_status`, `languages` and `user_id`. At this point the `sync_status` is **initialization**.
   - A log will be added to `product_push_error_logs` table with necessary details like `request_data`, `response_data` and `response_status`.
   - At this stage, a queue will be created and product push job will be added to the queue.
4. ### Product push flow:
   - The product push flow starts from the `PushToMagento` job. As the first step of the job, product and website passed from the `pushProduct` function will be assigned to the `$product` and `$website` respectively.
   - push to magento Conditions with `status` is `1` will be fetched from the `push_to_magento_conditions` table and assigned to `$conditionsWithIds` variable and then it will convert as array and will assign to `$conditions`.
   - Push to magento Conditions with `upteam_status` is `1` will be fetched from the `push_to_magento_conditions` table and assigned to `$upteamconditionsWithIds` variable and then it will convert as array and will assign to `$upteamconditions`.
   - Parent of the product's category will find and assign to `$topParent` variable with the `getTopParent` function in the `ProductHelper`. Here the parent of the product will be fetched from the `categories` table.
   - **Charity Check:**
     - Does the product is for charity or not will define here. 2 variables `$charity` and `$isCharityChecked` will be declared with a default value `0`. `0` denotes the product is not for charity and the charity check of the product is not done.
     - `$isCharityChecked` will be set to `1` if one of the following conditions get satisfied.
       - If the parent of the product's category is `NEW` and `charity_condition` is exists in the `$conditions` array.
       - If the parent of the product's category is `PREOWNED` and `charity_condition` is exists in the `$upteamconditions` array. Up team is a supplier, this supplier has category preowned only.
     - if one of the above conditions satisfies, then a check will perform to find the product from the `customer_charities` table. If there is an entry with the `product_id` int the `customer_charities` table, then the value `$charity` variable will set as `1`.
     - These `$charity` and `$isCharityChecked` will be used in the product push flow in multiple cases.
   - The product push will move to the next step only if one of the following conditions get satisfied.
     - If the parent of the product's category is `NEW` and `status_condition` is exists in the `$conditions` array.
     - If the parent of the product's category is `PREOWNED` and `status_condition` is exists in the `$upteamconditions` array.
   - If the above check passed then the status of the product check will be checked with the `StatusHelper::$finalApproval`. If the status of the product is `9`:
     - The product has the final approval and ready for product push.
     - The `sync_status` will be set to `started_push` .
     - The message will be updated as `Product has been started to push`
   - If the status is not `9`:
     - The `sync_status` will be set as `error`.
     - The message will be updated as `Product have not set for final approval, current status is {status_id}`.
     - The product push flow will stop at this stage.
   - There are few condition check need to be performed during the product push flow. condition checks as follows:
     - **Condition Check 1 - Preowned products sale:**
       - Checks the store website sales preowned products or not. If it doesn't, the product push flow will stop at this stage and will add a log with `sync_status` as `error` and `message` as `Website do not sale preowned products` else the flow will continue to the next steps
       - If the value of `sale_old_products` of the store website is `0` and parent of the product category is 'PREOWNED', then the website doesn't sale preowned products.
     - **Condition Check 2 - Website source existence:**
       - Checks the website source status existence in `$conditions` array or in `$upteamconditions` array. If the parent of the product category is `NEW`, then existence of `website_source` status will be checked in `$conditions` and if the parent of the product category is `PREOWNED` `website_source` status will be checked in `$upteamconditions`.
       - if the website doesn't have `website_source` or the value in the `website_source` column is `NULL`, then the product push flow will stop and will add a log with `sync_status` as `error` and `message` as `Website source not found`.
     - **Condition Check 3 - Website product push status:**
       - Checks the website is disabled for product push or not. Value of the `disable_push` filed in the `store_websites` table defines the product push status of a website. If value of `disable_push` is `1`, then product push is disabled for that website and the product push flow will stop and will add a log with `sync_status` as `error` and `message` as `Website is disable for push product`.
     - **Condition Check 3 - Category/category parent size existence:**
       - Check the if the category/category parent needed the size chart and the size chart exists for the website. `size_chart_needed` in the `categories` table defines requirement of size chart for a category/ category parent.
       - if the value of `size_chart_needed` is 1 and the category/parent category doesn't have the size chart the product push flow will stop and will add a log with `sync_status` as `error` and `message` as `Size chart is needed for push product`.
     - **Condition Check 3 - product has images:**
       - Check if the product has images or not. if the `check_if_images_exists` exists in the `$condition` or `$upteamconditions` this check will perform.
       - If the `check_if_images_exists` condition exists and the images are not found for this product, the product push flow will stop and will add a log with `sync_status` as `image_not_found` and `message` as `Image(s) is needed for push product`.
   - If these checks done the product push process will move to MagentoService for further steps.
5. ### Product push flow with MagentoService:
   - Here multiple checks of the product push will perform in the `pushProduct` function. A new row will be inserted in to the `product_push_journey` along with the condition checked and status of the check`is_checked` as `1` for all the processes in this function.
     - **Condition Check 4 - Website token validation:**
       - Checks if the website has a valid token. If exists, the flow will continue, else the product push flow stop at this step and necessary logs will be updated.
     - **Condition Check 5 - category validation:**
       - Checks if the product has a valid category.
     - **Condition Check 6 - Product readiness validation:**
       - Checks the product's readiness by confirming the following conditions
         - If the product has a valid name
         - If the product has short description
         - if the product has a valid price range
     - **Condition Check 7 - Product brand validation:**
       - Checks if the product has a valid brand.
     - **Condition Check 8 - Product brand validation:**
       - Checks if the product has a valid product category
     - **Condition Check 9 - Product reference assignment:**
       - If the `assign_product_references` exists in `$conditions`, then the reference will be assigned
6. ### Product push flow with assignOperations:

   - Here, gets all the default data and will be assigned to each variable for further calculations. A new row will be inserted in to the `product_push_journey` along with the condition checked and status of the check`is_checked` as `1` for all the processes in this function. Data fetches in this function as follows,
     - **Get all website ids** and assigns to `$this->websiteIds`. In `getWebsiteIds` function checks if this product has a row in the `customer_charities`, if exists, then the websites data belongs to that row will be returned from `customer_charity_website_stores` else website data will be fetched from the `store_websites` table.
     - **Get all website attributes** and assigns to `$this->websiteAttributes`. In `getWebsiteAttributes` function, it returns all the website attributes which belongs to the store website from the `store_website_attributes` table.
     - **Starts Translation** of the product details like title, description, short description etc to the local language of the location where the websites access.
     - **Gets meta data** from the store website and will be assigned to `$this->meta`. `meta_title`, `meta_description` and `meta_keyword` will be assigned and returned in the `getMeta` function.
     - **Gets Sizes** of the product and assigns to `$this->sizes`.
     - **Gets SKU** of the product and assigns to `$this->sku`.
     - **Gets description** of the product and assigns to `$this->description`.
     - **Gets brands** belongs to that product and assigns to `$this->magentoBrand`.
     - **Gets images** belongs to that product and assigns to `$this->images`.
     - **Gets store website size** belong to that from the `sizes` table and assigns to the `$this->storeWebsiteSize`.
     - **Gets store website colors** belongs to the store website from the `store_website_colors` table and assigns to `$this->storeWebsiteColor`.
     - **Gets product measurements** from the `ProductHelper` and assigns to `$this->measurementv`.
     - **Gets estimated delivery time** of the product from the `suppliers` table and assigns to `$this->estMinimumDays`.
     - **Gets size chart** for each categories from `brand_category_size_charts` table and assigns to `$this->sizeChart`.
     - **Gets store color** from the `store_website_colors` table and assigns to `$this->storeColor`.
     - **Gets pricing** of the product from the `products` table and `store_website_product_prices` table and assigns to `$this->prices`.

7. ### Product push flow with assignProductOperation:
   - The final stage of the product push happens in this function.
   - As the first step, it checks the product push type and define if it's a single product push or configurable product with children push:
     - If the `push_type` of the product's category is `0` and not `NULL` then it's single product push. The `$pushSingle` variable will set as `true`.
     - If the `push_type` of the product's category is `1` then it's configurable product with children push. The `$pushSingle` variable will set as `false`.
     - If the `$this->sizes` is not empty and count is greater than `1` then it's configurable product with children push. The `$pushSingle` variable will set as `false`.
     - If the `size_eu` of the product is `OS` then the `$product->size_eu` set as `NULL` and he `$pushSingle` variable will set as `true`. Here `OS` denotes single size.
   - **Single product push:**
     - It starts from `_pushSingleProduct` function.
     - Some of the default values of the products will be set statically as array `$d` and this array will be passed to `defaultData` function to add default values of product from the database. This function will return an array with all default values.
     - This default data will be passed to `_pushProduct` along with product push type as `single`, `sku`.
     - In `_pushProduct` all the product details will be added to `$data['product']` and this data will be send to `sendRequest` function along with magento api and access token.
     - In single product push product images will be added to product in `_pushProduct` function.
     - In `sendRequest` function the request to push the product will be sent as curl request. The response and the status code of the request will be returned as array.
     - If the response code is `200`, `sync_status` will update as `success` and `Product (" . $this->productType . ") with SKU " . $this->sku . " successfully pushed to Magento` will update in the `message`.
     - If the response code is not `200`, `sync_status` will update as `error` and response will be update as `message`.
     - The result will be send back to `_pushSingleProduct`.
   - **Configurable product with children push**:
     - It starts from `_pushConfigurableProductWithChildren` function. Configurable products are products which have sizes so like shirts / trousers / etc - so there will be one main product and the different sizes in that are considered as child products of the main product.
       - Two types of product push happens in this function, configurable product push and simple configurable product push.
     - Some of the default values of the products will be set statically as array `$d` and this array will be passed to `defaultData` function to add default values of product from the database. This function will return an array with all default values.
     - This default data will be passed to `_pushProduct` along with product push type as `configurable`, `sku`.
     - If push type is `configurable`, product images will be added to product in `_pushProduct` function. Here `configurable` is products with multiple size options.
     - If push type is `simple_configurable`, product visibility will set as `1`. Here `simple_configurable` is products with one size option.
     - After this step it will follow the same steps of single product push.

# Product Push Flow 2

## Product Push Condition Checks

The product push condition check starts from `processProductsConditionsCheck` function in the `ProductController`.

1. ### Products selection:
   When the product push initiates, approved products will fetch from the `products` table with the following conditions:
   - `short_description` is not `null`
   - `name` is not `null`
   - `status_id` is `9`: _(9 represents the final approval status of a product)_
   - Grouped by `brand` and `category`
   - Default data fetching `limit` has set to `100`, this will be replaced if the `no_of_product` parameter has value
   - After product selection, it will pass all the selected products to in `conditioncheckonly` queue. Also passed `product`, total product(`no_of_product`) and current product order(`product_index`) to find them in Horizon and to be make all jobs are completed. Each queue well dispatched by `ConditionCheckOnlyJob`.
2. ### ConditionCheckOnlyJob:

   - This job is to get store websites by tag that have the same category & brand of the product and check if it must exist in the store_website table.
   - **Setting `is_conditions_checked` flag as 1**
   - Product conditions check started and is_conditions_checked set as 1
     Store websites which are has the these products will identified in this step. For that the **ids** of product that fetched in the **Products selection** will be sent to `ProductHelper::getStoreWebsiteNameByTag` which returns the and array with list of store websites which has these products. The flow to get the store website ids as follows:

   - **Get store website category:** Selected products will be looped to fetch all store categories from the `store_website_categories` which has a valid `remote_id` and which matches to the each product's `category_id`.
     - **Get store website ids from store website brands:** Fetched store website categories will be looped to get all the store website brands from the `store_website_brands` table. This will returns all the brands which matches the `store_website_id` of the each category and matches `brand_id` of each product get looped and which has a valid `magento_value`. The brands get from this steps will again loop to make an array (`$websiteArray`) of store websites ids.
     - **Get store website ids from the products which are landing page products:** if a product has an entry in `landing_page_products` table, that will be checked here as first step. Then get all store websites which has `o-labels` string in the `title` and `cropper_color` is not `null`. This store websites will be looped and will be pushed to `$websiteArray` if it is not already exists.

   After these steps it will return store websites from `store_websites` table with checking specified store website ids(`$websiteArray`) and groupping data by `tag_id` column.

3. ### Start to condition check:
   - Loops the store websites and checks each website are exists. If the website doesn't exist, the error will be logged to `product_push_error_logs`.
   - if the website exists, a log will be added to the `log_list_magentos` table with the `product_id`, `message`, `store_website_id`, `sync_status`, `languages` and `user_id`. At this point the `sync_status` is **initialization**.
   - A log will be added to `product_push_error_logs` table with necessary details like `request_data`, `response_data` and `response_status`.
   - At this stage, a queue will be created and product push job will be added to the queue.
4. ### Product push condition check flow:
   - The product push condition check flow starts from the `ConditionCheckFirstJob` job. As the first step of the job, product and website passed from the `processProductsConditionsCheck` function will be assigned to the `$product` and `$website` respectively.
   - push to magento Conditions with `status` is `1` will be fetched from the `push_to_magento_conditions` table and assigned to `$conditionsWithIds` variable and then it will convert as array and will assign to `$conditions`.
   - Push to magento Conditions with `upteam_status` is `1` will be fetched from the `push_to_magento_conditions` table and assigned to `$upteamconditionsWithIds` variable and then it will convert as array and will assign to `$upteamconditions`.
   - Parent of the product's category will find and assign to `$topParent` variable with the `getTopParent` function in the `ProductHelper`. Here the parent of the product will be fetched from the `categories` table.
   - **Charity Check:**
     - Does the product is for charity or not will define here. 2 variables `$charity` and `$isCharityChecked` will be declared with a default value `0`. `0` denotes the product is not for charity and the charity check of the product is not done.
     - `$isCharityChecked` will be set to `1` if one of the following conditions get satisfied.
       - If the parent of the product's category is `NEW` and `charity_condition` is exists in the `$conditions` array.
       - If the parent of the product's category is `PREOWNED` and `charity_condition` is exists in the `$upteamconditions` array. Up team is a supplier, this supplier has category preowned only.
     - if one of the above conditions satisfies, then a check will perform to find the product from the `customer_charities` table. If there is an entry with the `product_id` int the `customer_charities` table, then the value `$charity` variable will set as `1`.
     - These `$charity` and `$isCharityChecked` will be used in the product push flow in multiple cases.
   - The product push will move to the next step only if one of the following conditions get satisfied.
     - If the parent of the product's category is `NEW` and `status_condition` is exists in the `$conditions` array.
     - If the parent of the product's category is `PREOWNED` and `status_condition` is exists in the `$upteamconditions` array.
   - If the above check passed then the status of the product check will be checked with the `StatusHelper::$finalApproval`. If the status of the product is `9`:
     - The product has the final approval and ready for product push.
     - The `sync_status` will be set to `started_push` .
     - The message will be updated as `Product has been started to push`
   - If the status is not `9`:
     - The `sync_status` will be set as `error`.
     - The message will be updated as `Product have not set for final approval, current status is {status_id}`.
     - The product push flow will stop at this stage.
   - There are few condition check need to be performed during the product push flow. condition checks as follows:
     - **Condition Check 1 - Preowned products sale:**
       - Checks the store website sales preowned products or not. If it doesn't, the product push flow will stop at this stage and will add a log with `sync_status` as `error` and `message` as `Website do not sale preowned products` else the flow will continue to the next steps
       - If the value of `sale_old_products` of the store website is `0` and parent of the product category is 'PREOWNED', then the website doesn't sale preowned products.
     - **Condition Check 2 - Website source existence:**
       - Checks the website source status existence in `$conditions` array or in `$upteamconditions` array. If the parent of the product category is `NEW`, then existence of `website_source` status will be checked in `$conditions` and if the parent of the product category is `PREOWNED` `website_source` status will be checked in `$upteamconditions`.
       - if the website doesn't have `website_source` or the value in the `website_source` column is `NULL`, then the product push flow will stop and will add a log with `sync_status` as `error` and `message` as `Website source not found`.
     - **Condition Check 3 - Website product push status:**
       - Checks the website is disabled for product push or not. Value of the `disable_push` filed in the `store_websites` table defines the product push status of a website. If value of `disable_push` is `1`, then product push is disabled for that website and the product push flow will stop and will add a log with `sync_status` as `error` and `message` as `Website is disable for push product`.
     - **Condition Check 3 - Category/category parent size existence:**
       - Check the if the category/category parent needed the size chart and the size chart exists for the website. `size_chart_needed` in the `categories` table defines requirement of size chart for a category/ category parent.
       - if the value of `size_chart_needed` is 1 and the category/parent category doesn't have the size chart the product push flow will stop and will add a log with `sync_status` as `error` and `message` as `Size chart is needed for push product`.
     - **Condition Check 3 - product has images:**
       - Check if the product has images or not. if the `check_if_images_exists` exists in the `$condition` or `$upteamconditions` this check will perform.
       - If the `check_if_images_exists` condition exists and the images are not found for this product, the product push flow will stop and will add a log with `sync_status` as `image_not_found` and `message` as `Image(s) is needed for push product`.
   - If these checks done the product push process will move to MagentoService for further steps.
5. ### Product push condition check flow with MagentoService:
   - Here multiple checks of the product push will perform in the `pushProduct` function. A new row will be inserted in to the `product_push_journey` along with the condition checked and status of the check`is_checked` as `1` for all the processes in this function.
     - **Condition Check 4 - Website token validation:**
       - Checks if the website has a valid token. If exists, the flow will continue, else the product push flow stop at this step and necessary logs will be updated.
     - **Condition Check 5 - category validation:**
       - Checks if the product has a valid category.
     - **Condition Check 6 - Product readiness validation:**
       - Checks the product's readiness by confirming the following conditions
         - If the product has a valid name
         - If the product has short description
         - if the product has a valid price range
     - **Condition Check 7 - Product brand validation:**
       - Checks if the product has a valid brand.
     - **Condition Check 8 - Product brand validation:**
       - Checks if the product has a valid product category
     - **Condition Check 9 - Product reference assignment:**
       - If the `assign_product_references` exists in `$conditions`, then the reference will be assigned
6. ### Product push condition check flow with assignOperations:
   - Here, gets all the default data and will be assigned to each variable for further calculations. A new row will be inserted in to the `product_push_journey` along with the condition checked and status of the check`is_checked` as `1` for all the processes in this function. Data fetches in this function as follows,
     - **Get all website ids** and assigns to `$this->websiteIds`. In `getWebsiteIds` function checks if this product has a row in the `customer_charities`, if exists, then the websites data belongs to that row will be returned from `customer_charity_website_stores` else website data will be fetched from the `store_websites` table.
     - **Get all website attributes** and assigns to `$this->websiteAttributes`. In `getWebsiteAttributes` function, it returns all the website attributes which belongs to the store website from the `store_website_attributes` table.
     - **Starts Translation** of the product details like title, description, short description etc to the local language of the location where the websites access.
     - **Gets meta data** from the store website and will be assigned to `$this->meta`. `meta_title`, `meta_description` and `meta_keyword` will be assigned and returned in the `getMeta` function.
     - **Gets Sizes** of the product and assigns to `$this->sizes`.
     - **Gets SKU** of the product and assigns to `$this->sku`.
     - **Gets description** of the product and assigns to `$this->description`.
     - **Gets brands** belongs to that product and assigns to `$this->magentoBrand`.
     - **Gets images** belongs to that product and assigns to `$this->images`.
     - **Gets store website size** belong to that from the `sizes` table and assigns to the `$this->storeWebsiteSize`.
     - **Gets store website colors** belongs to the store website from the `store_website_colors` table and assigns to `$this->storeWebsiteColor`.
     - **Gets product measurements** from the `ProductHelper` and assigns to `$this->measurementv`.
     - **Gets estimated delivery time** of the product from the `suppliers` table and assigns to `$this->estMinimumDays`.
     - **Gets size chart** for each categories from `brand_category_size_charts` table and assigns to `$this->sizeChart`.
     - **Gets store color** from the `store_website_colors` table and assigns to `$this->storeColor`.
     - **Gets pricing** of the product from the `products` table and `store_website_product_prices` table and assigns to `$this->prices`.
     - If the above check passed and the mode of the product is `conditions-check` then changing the status of product and set to `StatusHelper::$productConditionsChecked`

## Product Push Conditions Checked

The product push conditions checked starts from `pushProductsToMagento` function in the `ProductController`.

1. ### Products selection conditions checked:
   When the product push initiates, approved products will fetch from the `products` table with the following conditions:
   - `short_description` is not `null`
   - `name` is not `null`
   - `status_id` is `153`: _(153 represents the product conditions checked status of a product)_
   - Grouped by `brand` and `category`
   - Default data fetching `limit` has set to `100`, this will be replaced if the `no_of_product` parameter has value
   - After product selection, it will pass all the selected products to in `pushproductflow2only` queue. Also passed `product`, total product(`no_of_product`) and current product order(`product_index`) to find them in Horizon and to be make all jobs are completed. Each queue well dispatched by `PushProductFlow2OnlyJob`.
2. ### PushProductFlow2OnlyJob:

   - This job is to get store websites by tag that have the same category & brand of the product and check if it must exist in the store_website table.
   - **Setting `is_push_attempted` flag as 1**
   - Product push attempted started and is_push_attempted set as 1
   - Store websites which are has the these products will identified in this step. For that the **ids** of product that fetched in the **Products selection** will be sent to `ProductHelper::getStoreWebsiteNameByTag` which returns the and array with list of store websites which has these products. The flow to get the store website ids as follows:

   - **Get store website category:** Selected products will be looped to fetch all store categories from the `store_website_categories` which has a valid `remote_id` and which matches to the each product's `category_id`.
     - **Get store website ids from store website brands:** Fetched store website categories will be looped to get all the store website brands from the `store_website_brands` table. This will returns all the brands which matches the `store_website_id` of the each category and matches `brand_id` of each product get looped and which has a valid `magento_value`. The brands get from this steps will again loop to make an array (`$websiteArray`) of store websites ids.
     - **Get store website ids from the products which are landing page products:** if a product has an entry in `landing_page_products` table, that will be checked here as first step. Then get all store websites which has `o-labels` string in the `title` and `cropper_color` is not `null`. This store websites will be looped and will be pushed to `$websiteArray` if it is not already exists.

   After these steps it will return store websites from `store_websites` table with checking specified store website ids(`$websiteArray`) and groupping data by `tag_id` column.

3. ### Start push to magento:

   - Loops the store websites and checks each website are exists. If the website doesn't exist, the error will be logged to `product_push_error_logs`.
   - if the website exists, a log will be added to the `log_list_magentos` table with the `product_id`, `message`, `store_website_id`, `sync_status`, `languages` and `user_id`. At this point the `sync_status` is **initialization**.
   - A log will be added to `product_push_error_logs` table with necessary details like `request_data`, `response_data` and `response_status`.
   - At this stage, a queue will be created and product push job will be added to the queue.

4. ### Product push conditions checked flow:

   - The product push flow starts from the `ProductPushFlow2Job` job. As the first step of the job, product and website passed from the `assignOperation` function will be assigned to the `$product` and `$website` respectively.
   - push to magento Conditions with `status` is `1` will be fetched from the `push_to_magento_conditions` table and assigned to `$conditionsWithIds` variable and then it will convert as array and will assign to `$conditions`.
   - Push to magento Conditions with `upteam_status` is `1` will be fetched from the `push_to_magento_conditions` table and assigned to `$upteamconditionsWithIds` variable and then it will convert as array and will assign to `$upteamconditions`.
   - Parent of the product's category will find and assign to `$topParent` variable with the `getTopParent` function in the `ProductHelper`. Here the parent of the product will be fetched from the `categories` table.

5. ### Product push conditions checked flow with assignOperations:

   - Here, gets all the default data and will be assigned to each variable for further calculations. A new row will be inserted in to the `product_push_journey` along with the condition checked and status of the check`is_checked` as `1` for all the processes in this function. Data fetches in this function as follows,
     - **Get all website ids** and assigns to `$this->websiteIds`. In `getWebsiteIds` function checks if this product has a row in the `customer_charities`, if exists, then the websites data belongs to that row will be returned from `customer_charity_website_stores` else website data will be fetched from the `store_websites` table.
     - **Get all website attributes** and assigns to `$this->websiteAttributes`. In `getWebsiteAttributes` function, it returns all the website attributes which belongs to the store website from the `store_website_attributes` table.
     - **Starts Translation** of the product details like title, description, short description etc to the local language of the location where the websites access.
     - **Gets meta data** from the store website and will be assigned to `$this->meta`. `meta_title`, `meta_description` and `meta_keyword` will be assigned and returned in the `getMeta` function.
     - **Gets Sizes** of the product and assigns to `$this->sizes`.
     - **Gets SKU** of the product and assigns to `$this->sku`.
     - **Gets description** of the product and assigns to `$this->description`.
     - **Gets brands** belongs to that product and assigns to `$this->magentoBrand`.
     - **Gets images** belongs to that product and assigns to `$this->images`.
     - **Gets store website size** belong to that from the `sizes` table and assigns to the `$this->storeWebsiteSize`.
     - **Gets store website colors** belongs to the store website from the `store_website_colors` table and assigns to `$this->storeWebsiteColor`.
     - **Gets product measurements** from the `ProductHelper` and assigns to `$this->measurementv`.
     - **Gets estimated delivery time** of the product from the `suppliers` table and assigns to `$this->estMinimumDays`.
     - **Gets size chart** for each categories from `brand_category_size_charts` table and assigns to `$this->sizeChart`.
     - **Gets store color** from the `store_website_colors` table and assigns to `$this->storeColor`.
     - **Gets pricing** of the product from the `products` table and `store_website_product_prices` table and assigns to `$this->prices`.

6. ### Product push conditions checked flow with assignProductOperation:
   - The final stage of the product push happens in this function.
   - As the first step, it checks the product push type and define if it's a single product push or configurable product with children push:
     - If the `push_type` of the product's category is `0` and not `NULL` then it's single product push. The `$pushSingle` variable will set as `true`.
     - If the `push_type` of the product's category is `1` then it's configurable product with children push. The `$pushSingle` variable will set as `false`.
     - If the `$this->sizes` is not empty and count is greater than `1` then it's configurable product with children push. The `$pushSingle` variable will set as `false`.
     - If the `size_eu` of the product is `OS` then the `$product->size_eu` set as `NULL` and he `$pushSingle` variable will set as `true`. Here `OS` denotes single size.
   - **Single product push:**
     - It starts from `_pushSingleProduct` function.
     - Some of the default values of the products will be set statically as array `$d` and this array will be passed to `defaultData` function to add default values of product from the database. This function will return an array with all default values.
     - This default data will be passed to `_pushProduct` along with product push type as `single`, `sku`.
     - In `_pushProduct` all the product details will be added to `$data['product']` and this data will be send to `sendRequest` function along with magento api and access token.
     - In single product push product images will be added to product in `_pushProduct` function.
     - In `sendRequest` function the request to push the product will be sent as curl request. The response and the status code of the request will be returned as array.
     - If the response code is `200`, `sync_status` will update as `success` and `Product (" . $this->productType . ") with SKU " . $this->sku . " successfully pushed to Magento` will update in the `message`.
     - If the response code is not `200`, `sync_status` will update as `error` and response will be update as `message`.
     - The result will be send back to `_pushSingleProduct`.
   - **Configurable product with children push**:
     - It starts from `_pushConfigurableProductWithChildren` function. Configurable products are products which have sizes so like shirts / trousers / etc - so there will be one main product and the different sizes in that are considered as child products of the main product.
       - Two types of product push happens in this function, configurable product push and simple configurable product push.
     - Some of the default values of the products will be set statically as array `$d` and this array will be passed to `defaultData` function to add default values of product from the database. This function will return an array with all default values.
     - This default data will be passed to `_pushProduct` along with product push type as `configurable`, `sku`.
     - If push type is `configurable`, product images will be added to product in `_pushProduct` function. Here `configurable` is products with multiple size options.
     - If push type is `simple_configurable`, product visibility will set as `1`. Here `simple_configurable` is products with one size option.
     - After this step it will follow the same steps of single product push.

## Magento push status

- This functionlity is used to view listing pushed product in magento.
- The product push status check starts from `magentoPushStatusForMagentoCheck` function in the `ProductController`.
- pushed products will fetch from the `products` table with the following conditions:
  - checking status `pushToMagento`(11) or `inMagento`(12)
  - `is_push_attempted` is `1`
  - `isUploaded` is `0`
  - Default data fetching `limit` has set to `10`
- **Filter**
  - There are eight filter provided in top of the page,filters are Product Id, Product Name,Brand, Category, Composition, Color, Price,Status
  - When click on search icon button then it will apply filter and getting data.
- **Listing**
  - After getting pushed product data then it will display in table grid.
