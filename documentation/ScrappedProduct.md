# Scrapped Product

Scrapped product is used to sync products from nodeApp.
The scrapped product journey starts from `syncProductsFromNodeApp` function in the `ScrapController`.

1. ### From Node Js Scraper Product comes to ERP to api -> scrap-products/add (ScrapController@syncProductsFromNodeApp)
2. ### From there product is save to ScrappedProduct
   - **2.1) Fix common mistakes as in**
     - 2.1.1) if category is not array , we merge it and make array
     - 2.1.2) Convert if € is present to EUR currency
     - 2.1.3) Convert if £ is present to GBP
     - 2.1.4) Convert if $ is present to USD
     - 2.1.5) Convert if US$ is present to USD currency
     - 2.1.6) Make proper array for images replace '%20'
     - 2.1.7) Merge price parameter to request If request parameter price is not empty
   - **2.2) LogScrapeValidationUsingRequest : ( With Error Means any product which doesnt have that value wont be stored in Database and Warning means Product will be stored )**
     - 2.2.1) validate if Website name is present with error
     - 2.2.2) check if its proper url and url is present with error
     - 2.2.3) sku cannot be empty
     - 2.2.4) SKU Regrex -
       brand not found or sku_format regex generated an exception or sku_format_without_color regex generated an exception then return warning
     - 2.2.5) validate Brand cannot be empty with error
     - 2.2.6) validate Description is empty with warning
     - 2.2.7) Size system cannot be empty with error
     - 2.2.8) validate Property For Category with warning
     - 2.2.9) validate Product without Image with warning
     - 2.2.10) validate Product has Image with error
     - 2.2.11) validate Currency and currency should be min 3 character with error
     - 2.2.12) validate Price should not be empty with ERROR
       - 2.2.12.1) Comma in the price
       - 2.2.12.2) More than one dot in the price
       - 2.2.12.3) Price must be of type float/double
     - 2.2.13) validate Discounted price must be of type float/double with error
   - **2.3) Try to Get Proper SKU using ProductHelper::getSku -> here we remove special character like (/,-,\_,+,|,\\)**
   - **2.4) We try to Get Brand from brand reference or if brand name is proper then from brands**
   - **2.5) Get Category From Properties**
   - **2.6) Remove categories if it is matching with sku**
   - **2.7) Get Color From Properties**
   - **2.8) Get Compostion From Properties**
   - **2.9) Scraped Product Price Correction .**
   - **2.10) Check if scraped_product is found update the details or store new scraped_product.**
3. ### Saving Scraped Product
   - **Fields Saved in Scraper Products :**
     - `images`,
     - `sku` ,
     - `original_sku` ,
     - `discounted_price` ,
     - `is_sale` ,
     - `has_sku` ,
     - `url` ,
     - `title` (ProductHelper::getRedactedText use attribute replacement for name)
     - `description` (ProductHelper::getRedactedText use attribute replacement for short_description)
     - `properties` ,
     - `currency` ,
     - `price` ,
     - `price_eur` ( When currency is EUR ) ,
     - `last_inventory_at` ,
     - `website` ,
     - `brand_id` ,
     - `category` ,
     - `validated` ,
     - `validation_result` (Whole Validation Result is saved here)
4. ### Saving Scrapper Request
   - check if scraper of same id have records with same day , then update the end time,request_sent and request_failed otherwise it's create new entry in `scrap_request_histories` table
5. ### After saving product in Scrapped Product we send it to ProductsCreator::createProduct to create Product
   - **5.1 Search For Supplier, If Supplier is not found Error Log Is Generated For Product**
   - **5.1 Get Supplier Language Details If Found Then Google Traslation Can Translate Data And Assign To Scraped Product**
   - **5.2 formatPrices :**
     - 5.2.1) price_eur => Main Price From Product
     - 5.2.2) price_inr => Based on Product Brand Price is converted to INR using euro_to_inr field , if brand euro_to_inr is not present then it will take default value from Setting::get('euro_to_inr') so price will be ( Main Price From Product \* euro_to_inr )
     - 5.2.3) price_eur_special => main price - ( main price \* Brand deduction_percentage ) / 100
     - 5.2.4) price_inr_special => indian price - ( indian price \* Brand deduction_percentage ) / 100
     - 5.2,5) price_eur_discounted => main price - ( main price \* Brand sales_discount ) / 100
     - 5.2.6) price_inr_discounted => indian price - ( indian price \* Brand sales_discount ) / 100
   - **5.3) getting composition , color, size, lmeasurement, hmeasurement, dmeasurement, measurement_size_type, made_in, category**
   - **5.4) Try to Get Proper SKU using ProductHelper::getSku -> here we remove special character like (/,-,\_,+,|,\\)**
   - **5.5 Getting Color From Products ( ColorNamesReference::getProductColorFromObject($image) )**
     - 5.5.1) getting unique color names
     - 5.5.2) Check if color exists in this case color refenrece we don't found so we need to add that one
     - 5.5.3) color can be found in url
     - 5.5.4) color can be found in title
     - 5.5.5) can be found in description
   - **5.8 Getting Compositions and Description Erp name**
   - **5.7 saving SupplierBrandCount, SupplierCategoryCount and SkuColorReferences**
   - **5.8 Check if SKU already exist**
     - 5.8.1) Check if we can update the title - using ProductStatus table
     - 5.8.2) Check if we can update the short description - using ProductStatus table
     - 5.8.3) Check if we can update the color - using ProductStatus table
     - 5.8.4) Check if we can update the composition - using ProductStatus table
     - 5.8.4) Check if we can update the category - using ProductStatus table
     - 5.8.5) Update with scraped sizes
     - 5.8.6) Update Store measurement
     - 5.8.7) attachImagesToProduct -
       -Images are stored with Original Tag
     - 5.8.8) checkExternalScraperNeed -> this checks for title , short_description , price... if the product doesn't have this three value then the product goes for external scrapper
     - 5.8.9) isNeedToIgnore - If Product color is white... product is updated to status StatusHelper::$manualCropping
     - 5.8.10) Product Stock is updated by 1
       and then we save ProductSupplier
   - **5.9 Same Pattern is performed while saving Product**
