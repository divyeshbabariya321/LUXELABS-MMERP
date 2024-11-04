## Product Pricing

1. Remove memory limit, so memory exhausted error can be bypassed, and process will continue.
2. Get all categories `id` column in `categoryIds` variable.
3. Get all categories `id` and `title` columns whose parent is not in `categoryIds` and `parent_id > 0` in `categories` variable.
4. Get `StoreWebsite` with following conditions:
   - `is_published` = `1`
   - crossJoin with `products`
   - crossJoin with `simply_duty_countries`
   - left join `brands`
   - left join `categories`
   - left join `category_segments`
   - left join `scraped_products`
   - join `product_suppliers`
   - where products are not deleted
   - If `country_code` is present
     - Add where `simply_duty_countries.country_code` = `country_code`
   - If `supplier` is present
     - get `product_id` which are present in `product_suppliers` by `supplier_id` in request.
   - If `brand_names` is present
     - Add where `brand_id` is in `brand_names` request
   - If `websites` is present
     - Add where `store_websites.id` in `websites` request
   - If `term` is present
     - match product name, category title, sku, brand name, product id in request `term`
   - Get products with above conditions with limit of 25
5. Count products and loop over products
   - Find product by id
   - Get duty price from product
     - Get country code from `SimpleDutyCountry`
       - IF country code exists, get default duty if > 0
       - Else Get `SimpleDutyCountry` by country code segment, and return segment price.
   - Get category_segment or brand_segment which is available
   - get product price
     - Get website by websiteId or find from `StoreWebsite`
     - store logs
     - Get Categories
       - Join `category_segments`, `category_segment_discounts` by category id and brand id. Select first category. Store it in `$catdiscount` variable.
     - Get `category_segment_discounts` where id is `$catdiscount->id` and update `amount`
     - If `$catdiscount->amount_type` is `percentage`
       - calculate `percentage`, `segment_discount`, `product_price`
     - Else
       - calculate `segmentDiscount`, `productPrice`
     - If `segmentDiscount` is not zero
       - Store `operation` variable.
     - If `$isOvveride` is true
       - Calculate `productPrice`, `IVApercentage`
     - If `dutyPrice` > 0
       - Calculate `totalAmount`, `productPrice`
     - If `website` exists
       - Get `priceOverride` by `store_website_id`
       - If `brands`, `category` and `country` is present
         - Get `priceRecords`
       - If `priceRecords` does not exists
         - Get `priceOverride` by `store_website_id` using `website->id`
         - Get `priceRecords` by various conditions like matching `brand_segment` or `country_code`, get first records.
       - If `priceRecords` does not exists
         - Get `priceRecords` from `PriceOverride` by matching `brand_segment` with `brand`
       - If `priceRecords` does not exists
         - Get `priceRecords` from `PriceOverride` by matching `category_id` with `category`
       - If `priceRecords` does not exists
         - Get `priceRecords` from `PriceOverride` by matching `country_code` with `country`
       - If `priceRecords` exists
         - If `$updated_add_profit` exists
           - calculate `value`
           - Update `price_overrides` table and store result in `$updated_add_profit_row`
           - update `priceRecords->value`
         - If `$priceRecords->calculated` is `+`
           - if `$priceRecords->type` = 'percentage'
             - Calculate `price` and `last_product_total` accordingly
             - Return response
           - else
             - Calculate `price` and `last_product_total` accordingly
             - Return response
         - If `$priceRecords->calculated` is `-`
           - if `$priceRecords->type` = 'percentage'
             - Calculate `price` and `last_product_total` accordingly
             - Return response
           - else
             - Calculate `price` and `last_product_total` accordingly
             - Return response
       - Elseif `$updated_add_profit` or `$checked_add_profit` and not empty
         - If `brand` is empty
           - calculate `last_product_total`
           - Return response
         - If `category` is empty
           - calculate `last_product_total`
           - Return response
         - If `country` is empty
           - calculate `last_product_total`
           - Return response
         - If `brand` & `category` and `country` are not empty, and `checked_add_profit` is empty
           - Create `PriceOverride`
           - calculate `last_product_total`
           - Return response
     - calculate `last_product_total`
     - Return response
   - Prepare `product_list` array
6. Get `SimpleDutyCountry`
7. If `country_code` in request,
   - Get `SimpleDutyCountry` by `country_code`
8. Get `StoreWebsite` where `is_published`, select `title` and `id`
9. Get `StoreWebsite` where `is_published` select `title` and `id` and `website`
10. Get `CategorySegment` where `status` is `1`
11. If `websites` exists in request, get store websites by `websites`
12. Get `storeWebsites` in array
13. If `brand_names` exists in request,
    - Get `brands` by `brand_names` from request
14. If `supplier` exists in request,
    - Get `suppliers` by `supplier` from request
15. If `websites` exists in request,
    - Get `StoreWebsite` by `website`
16. If it's AJAX request
    - Get `count`
    - return response with view `product_price.index_ajax`
17. Return view `product_price.index`
