# Magento Helper v2.0

`MagentoHelperV2` is a helper which using to do few operation in the magento website. This helper has integrated in the erp to do the operations related to the magento website.

1. **StoreWebsiteController:** In the erp, `StoreWebsiteController` is using this for 4 different purposes.

   - **addMagentouser:** Uses to create user in the magento website. `MagentoHelperV2` has a function `addMagentouser`. In which,
     - Check if a valid token exists, if not new token will be generated from `_connect` function.
     - A post request will be sent to `$website->magento_url."/rest/V1/multistore/adminuser/"` with the details of the user which need to be added to the magento. This user details will be passed from the `StoreWebsiteController`.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and data passed from `StoreWebsiteController`.
     - The response from the request will be decoded and will be returned.
   - **updateMagentouser:** Uses to update existing user in the magento website. `MagentoHelperV2` has a function `updateMagentouser`. In which,
     - Check if a valid token exists, if not new token will be generated from `_connect` function.
     - A post request will be sent to `$website->magento_url."/rest/V1/multistore/editadminuser/"` with the details of the user which need to be updated to the magento. This user details will be passed from the `StoreWebsiteController`.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and data passed from `StoreWebsiteController`.
     - The response from the request will be decoded and will be returned.
   - **deleteMagentouser:** Uses to create user in the magento website. `MagentoHelperV2` has a function `deleteMagentouser`. In which,
     - Check if a valid token exists, if not new token will be generated from `_connect` function.
     - A post request will be sent to `$website->magento_url."/rest/V1/multistore/deleteadminuser/"` with the username of the user which need to be deleted to the magento.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and data passed from `StoreWebsiteController`.
     - The response from the request will be decoded and will be returned.
   - **checkToken:** Uses to check a token is valid or not. in which,
     - A get request will be sent to `$magentoURL."/rest/V1/customers/1"` along with the token.
     - If the response of the request is 200 then the token is valid and `true` will be returned. If the response of the request is not 200 then the token is invalid and `false` will be returned.
     - A log will be inserted in to `store_magento_logs` table with the request details.

2. **CheckLandingProductsMagento:** In the erp, `CheckLandingProductsMagento` is using `updateStockEnableStatus` function to update stock enable status of a product.
3. **MagentoProductApiCallCommand:** In the erp, `MagentoProductApiCallCommand` is using `getProductBySku` function to get product with sku. In this function,
   - Check if a valid token exists, if not new token will be generated from `_connect` function.
   - A get request will be sent to `$website->magento_url . "/rest/V1/products/".$sku` or `$website->magento_url . "/rest/" . trim($store) . "/V1/products/" . $sku` with the `sku` of the product which need to get from magento.
   - If the response has a product then response will be returned or `false` will be returned.
4. **MagentoOrderHandleHelper:** In the erp, `MagentoOrderHandleHelper` is using `getSkuAndColor` function to get product with sku. In this function,
   - `$original_sku` will be converted in to array by splitting with `-` and checks the color is matching with any of the colors. If color matches, color will be assigned to `$result['color']`.
   - Then it will check if there is a product exists in the database with this sku. If so product id will be assigned to `$result['product_id']` and return `color`, `product_id` and `sku` else `sku` will be returned.
5. **OrderController:** In the erp, `OrderController` is using this for 3 different purposes.
   - **changeOrderStatus:** Uses to change the status of an order in the magento website.
     - `$order`, `$website`, `$status` and `$order_product` will be passed to `changeOrderStatus` from the `statusChange` function in the `OrderController`.
     - Checks if a valid token exists, if not new token will be generated from `_connect` function.
     - A post request will be send to the `$website->magento_url . "/rest/V1/orders/$order_id/comments"` api along with current status of the order.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and data other details of the request.
     - If the response of the request is 200 then `true` will be returned. If the response of the request is not 200 then `false` will be returned with necessary error message.
   - **fetchOrderStatus:** Uses to get the status of orders from the magento website.
     - `$website` will be passed to `changeOrderStatus` from the `fetchStatus` function in the `OrderController`.
     - Checks if a valid token exists, if not new token will be generated from `_connect` function.
     - A get request will be send to the `$website->magento_url . "/rest/V1/getorderstatus"` api along with current status of the order.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and other details of the request.
     - If the response of the request is 200 then results from the request will be returned. If the response of the request is not 200 then `500` will be returned with error message.
   - **cancelTransaction:** Uses to cancel and order from the magento website
     - `$order` and `$website` will be passed to `cancelTransaction` from the `cancelTransaction` function in the `OrderController`.
     - Checks if a valid token exists, if not new token will be generated from `_connect` function.
     - Fetches all he items of the order with the order id.
     - A post request will be send to the `$website->magento_url . "/rest/V1/order/".$order_id."/refund"` api along with details of the order.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and other details of the request.
     - The response will be returned.
6. **ReturnExchangeController:** In the erp, `ReturnExchangeController` is using this for 3 different purposes.
   - **changeReturnOrderStatus:** Uses to change the return status of an order.
     - `$status` and , `$returnExchange` will be passed to `changeReturnOrderStatus` from the `update` function in the `ReturnExchangeController`.
     - Gets the website from the `store_websites` table. if the website doesn't exist, `false` with error message will be returned.
     - Checks if a valid token exists, if not new token will be generated from `_connect` function.
     - After multiple checks, a post request will be send to the `$website->magento_url . "/rest/V1/updateReturnStatus"` api along with details of the order.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and data other details of the request.
     - If the response of the request is 200 then `true` will be returned. If the response of the request is not 200 then `false` will be returned with necessary error message.
   - **getReturnOrderStatus:** Uses to fetch the status of return of orders .
     - `$website` will be passed to `getReturnOrderStatus` from the `fetchMagentoStatus` function in the `ReturnExchangeController`.
     - Checks if a valid token exists, if not new token will be generated from `_connect` function.
     - A get request will be send to the `$website->magento_url . "/rest/V1/returnStatusList"` api.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and data other details of the request.
     - If the response of the request is 200 then the result with `true` status will be returned. If the response of the request is not 200 then `false` will be returned with necessary error message.
   - **addReturnOrderStatus:** Uses to add order return status of a website.
     - `$website` and `$request->status` will be passed to `addReturnOrderStatus` from the `statusWebsiteSave` function in the `ReturnExchangeController`.
     - Checks if a valid token exists, if not new token will be generated from `_connect` function.
     - A post request will be send to the `$website->magento_url . "/rest/V1/returnStatus"` api along with status.
     - A log will be inserted in to `store_magento_logs` table with the request start time, api and data other details of the request.
     - If the response of the request is 200 then `true` will be returned. If the response of the request is not 200 then `false` will be returned with necessary error message.
7. **ShopifyHelper:** In the erp, `ShopifyHelper` is using `getSkuAndColor` function to get product with sku. Process of this already mentioned in the `point 4` in this documentation.
8. LogListMagentoController: In the erp, `LogListMagentoController` is using `SKU_SEPERATOR` static variable from `MagentoHelperv2` in `productInformation` function .
