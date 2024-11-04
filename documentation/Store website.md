# Store Website, Website Store and Website Store View

![](https://github.com/ludxb/erp/blob/DEVTASK-22338/documentation/images/store-website-terminology.jpeg "Store website terminology")

Store websites are the representation of the magento websites, which keeps website data for the each magento website. Each store website has it's own analytics, attributes, brands, categories, category seos, colors, goals, images, orders, pages, page histories, page pull logs, products, product attributes, product checks, product prices, product price histories, product screenshots, remarks, sale price, seo formats, sizes, twilio numbers, users and users histories. These values are handling with the following tables: `store_websites`, `store_websites_country_shipping`, `store_website_analytics`, `store_website_attributes`, `store_website_brands`, `store_website_brand_histories`, `store_website_categories`, `store_website_category_seos`, `store_website_colors`, `store_website_goals`, `store_website_goal_remarks`, `store_website_images`, `store_website_orders`, `store_website_pages`, `store_website_page_histories`, `store_website_page_pull_logs`, `store_website_products`, `store_website_product_attributes`, `store_website_product_checks`, `store_website_product_prices`, `store_website_product_price_histories`, `store_website_product_screenshots`, `store_website_remarks`, `store_website_sales_prices`, `store_website_seo_formats`, `store_website_sizes`, `store_website_twilio_numbers`, `store_website_users`, `store_website_user_history`.

- **Navigation:** of the store website s managing with the `store-website.js` file. All the actions of the store website module is handling in tis file.
- **Create store website:** To create a website, all inputs in the create site form need to be filled, which includes, basic details of the website, send blue credentials, magento website details including API Token, server credentials, mysql database credentials and FCM server credentials. Once the create form submits,
  - Form validation will be done.
  - Checks is_debug_true is not null and server ip exist, if so DB log will be enabled with `enableDBLog` function. In `enableDBLog` it will connect to the mangento server by executing a command and the result will be returned.
  - Checks the key file exists, if exists, then will check the key path exists or not. if not exist, the path will be created with 777 permission. Then. the file will be moved to the file path.
  - Then all the data from the form will be saved in to the `store_websites` table.
  - If username exist in the form data, a row will be inserted to the `store_website_users` table with use credentials.
  - 3 rows with 3 mysql database details will be inserted to the `chat_message` table.
  - Then store website will be mapped to all site development category and the `200` status will be returned.
  - **Edit store website:** Store website list has an option to edit a store website. Once user clicks on the edit button of a site, existing data related to that website will be displayed in a modal. User can edit and save from that modal. Edit process also will follow the same process in create website, but it will check the existence of the website and will update the data.
  - **Delete store website:** Deletes exiting sore website.
  - **Attach category:** Attach an existing category or new category with the store website.
  - **Attach brand and mark up:** Attach brand and mark up with the store website.
  - **SEO format:** Defines SEO format of a website.
  - **Magento Store User History:** Shows the history of users accessed the website.

## Websites

A store website can be assigned to different countries, these allocation can be done with website store module.

- **Create:** Website can be created by adding data in the create form. User have to enter name, code of the website store , website, and root category.
- **Edit:** An edit option is available in this module to edit the basic data that we entered in the creation time of the website store.
- **View** option will leads to all the website stores of a website store.
- **Delete** option will delete a website store.
- **Push website:** will push the website to magento. _PushWebsiteToMagento_ job is using to push the website to magento.
  - Website will be pushed by using _MagentoHelper::pushWebsite_ and once pushed an _id_ will be returned, and this _id_ will be saved as _platform_id_ in the website table to the corresponding website. API using to push to magento: _/rest/V1/customrestapi/saveWebsiteStore_
  - As part of the website pushing, website store also will be pushed to magento using _MagentoHelper::pushWebsiteStore_ and an _id_ will be returned which represents the magento website store, that _id_ will be stored as _platform_id_ in the _website_stores_ table. _platform_id_ from the website sent as _website_id_ along with necessary details to magento. API using to push to magento: _/rest/V1/customrestapi/saveWebsiteStore_
  - One the above step done, then the website store views will also push to magento by _MagentoHelper::pushWebsiteStoreView_. This will also return and _id_ which represents the entry in the magento side. that _id_ will be saved as _plaform_id_ in the _website_store_views_ table. When a website store view pushed _platform_id_ of the website will set as _website_id_ and _platform_id_ of the website store will set as _group_id_ in the magento side. API using to push to magento: _/rest/V1/customrestapi/saveWebsiteStore_
- **Create Default Stores:** This will create a website, website store and website store views but will not be pushed to magento. Website, website store and website store views will be create for all the countries belongs to the code that user enter in to the Default store creation form.
- **Merge Website:** We can move website(s) or website store(s) and website store view(s)s which is belongs to a store website to another store website.
- **Copy Website/Copy Websites:** We can move website(s) or website store(s) and website store view(s)s which is belongs to a store website to another store website.

## Website store

A store website can be assigned to different locations, these allocation can be done with website store module. The data from the store website will be used in the website store as well, only the location will change based on the user selection.

- **Create:** Website store can be created by adding data in the create form. User have to enter name, code of the website store , website, and root category.
- **Edit:** An edit option is available in this module to edit the basic data that we entered in the creation time of the website store.
- **View** option will leads to all the website store views of a website store.
- **Delete** option will delete a website store.
- **Push:** Website store will be pushed to magento by _MagentoHelper::pushWebsiteStore_ and an _id_ will be returned from magento, this will be stored as _platform_id_ id in the website_stores table belongs to the pushed website store.

## Website store view

A website store may have to display in native languages other than english, in website store view, user can create a version of website with a specific language that user required.

- Store view can be created by choosing language, status, website store and entering code and sort order.
- **Delete** option will delete a website store view.
- **Push:** Website store view will be pushed to magento by _MagentoHelper::pushWebsiteStoreView_ and an _id_ will be returned from magento, this will be stored as _platform_id_ id in the website_store_views table belongs to the pushed website store view.
