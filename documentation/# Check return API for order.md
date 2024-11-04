# Check return API for order

##### Request Parameter

- website
- order_id
- product_sku

##### Response

API return `has_return_request` parameter `true` or `false`. It is just returning parameter true and false. There are various conditions

1. join with orders table
2. join with order product table
3. join with return exchange products
4. match the SKU in order_products table
5. match website_id from store_website_orders
6. match platform_order_id from store_website_orders

Next step is get the days_refund of any order products.
Next Step is to get the category and fetch the refund days of that particular category

/_
Catgeory Code is commented due to some reason
_/

These two will help to get the days of refund if the product is demanding for the refunds before the expiry then it will return `true` otherwise `false`.

On top of this, `orders` table is working which helps to get order record. and basis on `created_at` column, system considered the days which are left behind, if `order_return_request` is marked with true/1 value then it would directly return
['has_return_request' => true]

Now final condition which is basis on all above query
if product days refund is available
if category days refund is available
if product days is less then standard expiry days
if category refund days is less than standard category expiry days

then it will return
['has_return_request' => true]

In final of everything, It will prepare the message as per the language, will return to API.
