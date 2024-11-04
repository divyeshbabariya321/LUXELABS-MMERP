# Magento Service

The Magento service is a library for push product to magento. It mainly used in `MagentoServiceJob` job.

When this job trigger, it creates one instance of magento service. Create instance will load `__construct` method to initialize or overwrite global variables.

After than that it will call the push Product function.

1.  ### MagentoService to push product in Magento:
    - Here, multiple checks of the product push will perform in the `pushProduct` function. A new row will be inserted in to the `product_push_journey` along with the condition checked and status of the check`is_checked` as `1` for all the processes in this function.
      - **Condition Check 1 - Website token validation:**
        - Checks if the website has a valid token. If exists, the flow will continue, else the product push flow stop at this step and necessary logs will be updated.
      - **Condition Check 2 - category validation:**
        - Checks if the product has a valid category.
      - **Condition Check 3 - Product readiness validation:**
        - Checks the product's readiness by confirming the following conditions
          - If the product has a valid name
          - If the product has short description
          - if the product has a valid price range
      - **Condition Check 4 - Product brand validation:**
        - Checks if the product has a valid brand.
      - **Condition Check 5 - Product category validation:**
        - Checks if the product has a valid product category
      - **Condition Check 6 - Product reference assignment:**
        - If the `assign_product_references` exists in `$conditions`, then the reference will be assigned
2.  ### Product push flow with assignOperations:
    - Here, gets all the default data and will be assigned to each variable for further calculations. A new row will be inserted in to the `product_push_journey` along with the condition checked and status of the check `is_checked` as `1` for all the processes in this function. Data fetches in this function as follows,
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
3.  ### Product push flow with assignProductOperation:
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
