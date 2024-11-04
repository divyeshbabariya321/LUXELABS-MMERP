# Get Images for Crop (Crop API)

- This functionality is used to generate cropping attribute of specific product
- Process start from `giveImage` function of the `ProductController` file.
- There are two parameters used to get product details and parameters are product_id and supplier_id
- Checking If product_id is requested then it will get data from products table by checking product_id condition and category should be > 3 required.
- Checking If supplier_id is requested then it will get data from products table also join with product_suppliers table by checking supplier_id condition and category should be > 3 required.
- If not requested product_id and supplier_id the it will get data by checking status_id, category, stock, media, and priority condition.
- Gettting data from mediables table with checking tag is original
- Gettting data from mediables table with checking tag is not original
- Process of deleting old images of specific product.
- Getting data from media table with checking mediables table ids of specific product and after getting data it will setting media_id attribute to specific media.
- Getting other information related to category.
- Getting specific product related unique store websites by tag groupping.
- Inside store website loop through check availibility in site_cropped_images table If it not exists then collecting cropper_color data and push.
- Checking parent category condition if it's empty then update product status to 33($attributeRejectCategory)
- If parent category is not empty then check if debug is requested then update product status to 15($isBeingCropped)
- Check If child category is `Unknown Category` then remove this text.
- Providing response and it store to `crop_image_get_requests` table

# Save Images for Crop (Save Image API)

- This functionality is used to save cropped images of specific product
- Process start from `saveImage` function of the `ProductController` file.
- This function first save the request and crop_image_get_request_id to `crop_image_http_request_responses` table.
- If request has a file then `productMediaCount` calculated from total original images(media) of that product.
- Media uploader through upload image to `product/` directory and inside product_id directory
- Checking is color is requested then convert color name into hexa format.
- Checking the store website count is existed with the total image and if it true then getting store website detail by checking cropper_color condition otherwise it will assign tag value to `constants.media_gallery_tag` from the config.
- If store website details found then getting store_websites data by tag
- Inside store_websites loop check data to site_cropped_images table in exists or not, If not exists then it will create new record to site_cropped_images table.
- Updating product with media and crop_count increment by 1.
- Creating new entry to `cropped_image_references` table
- Updating `cropped_image_reference_id` value of `crop_image_http_request_responses` table.
- Getting total number crop images as `cropCount` of specific products from `cropped_image_references` table
- `productMediaCount` will be multiple by number of store website where it exist
- If `productMediaCount` is less than or equals to `cropCount` then product's status_id changed based on it price.
  - If price is not null and in range of min and max price status set as `finalApproved`(9) otherwise set as `priceCheck`(41), scrap priority is also set to 0.
- In case above consition not satisfy, product status won't change and save as it is.
- If product category is > 0 and category `status_after_autocrop` is > 0 then calling `updateStatus` function and updating product status.
- If product status is `AI`(3) then call `ProductAi` job.
  - Inside `ProductAi` updating status to `autoCrop`(4)
  - Getting specific product all media url in `arrImages` and checking it's empty then it will return.
  - Setting category,color,composite, and gender to `resultScraper`
  - `resultAI` in getting properties from images by using `GoogleVisionHelper`.
  - Store specific product data to `log_scraper_vs_ai` table
  - Updating product color to `$resultAI->color` if product color is empty
  - Updating product status to `autoCrop`(4)
  - Creating log with message `Successfully handled AI`
- Finally If not any exception get then updating `response` in `status` value to `success` of `crop_image_http_request_responses` table otherwise `response` in `status` value set to `status`.

# Crop Reference grid

- This page used to manage cropped image details, crop newly uploaded product images, add new images

## Fetch data

- Process start from `grid` function of the `CroppedImageReferenceController` file.
- This grid will display data from `cropped_image_references` table with eloquent relationship and also join with `products` table.
- Defaults it gives `10` record grid.
- There are five filter condition applied and If any one is not empty then will apply filter while fetching records.
- Three count fetched from DB:
  - Total products is total number of records from `cropped_image_references` table.
  - Pending products count calculates based on status_id is 4($autoCrop) from same table.
  - Pending categoroy products count calculate based on status_id is 33($attributeRejectCategory) and for this `stock` must be greater than or equals to 1.
- If `customer_range` requested and not null then total crop images for requested date.

# Listing

- After fetching data from `cropped_image_references` table then assign it to one datagrid.
- Action column contain drop down to change status as Reject Product, Images Not Cropped Correctly, No Images Shown, Grid Not Shown, Blurry Image, First Image Not Available, Dimension Not Available, Wrong Grid Showing For Category, Incorrect Category, Only One Image Available, and Image incorrect.
- Last column issue contain issue detail and chat history.

# Filter

- There are five filters available in top of the page. Select or enter data in filter feilds and click on filter icon will filter data from DB and updated result will display in grid.
- Filters are productId, status, category, brands and supplier.

# Add Issue

- This functionality is used to submit an issue with creating new task and new branch on github for selected image from grid.
- First we need to select images from datagrid then click on `Add Issue` button which will open popup to store issue.
- Inside popup shows two inputs subject and assignee
- Click on `Add` to button from this popup then request come to `issueStore` method in the `DevelopmentController` file.
- In issueStore function first request validating creating then after create a new task to `developer_tasks` table.
- Checking if Issue is already exist or not If it exists then return to back.
- Finding `developer_modules` is exist or not If it not exists then it will create new developer module.
- Creating new task `developer_tasks` table.
- Checking github repositoy in `github_repositories` table If it' exists then it will create a new branch in github
- If `images` file requested then it uploads to `issue/task_id/image_per_folder/` folder and attach with this task.
- **sendMessage**
  - User will be notified on WhatsApp when new issue assigned to them.
  - Creating a POST request with two request paramters. First is requestData array which additionaly contain `issue_id`, `message`, and `status` while second parameter is context which value is `issue`.
  - Calling `sendMessage` function by passing both parameter as argument.
  - `sendMessage` function(WhatsAppController.php) is first validate request parameters. Next it conditionaly check for the second argument requested for `$context`.
  - In our case context is set to 'issue' so it will send message to whom task assign
  - If task assigned to `to_master` then he will receive message. Same for `to_team_lead` and `to_tester`.
  - Checking request_type from request and call `sendWithThirdApi`function.
  - This function will log message request in log file and send message to WhatsApp by specific domain. - Domain can select from is user admin or user's whatsapp number has provider or not

# Reject Image

- This functionality is used to update product to status rejected.
- First we need to select images from datagrid then after When click on `Reject Image` then shows reject cropping reason dropdown
- After selecting reason then request come to `rejectCrop` method in the `ProductCropperController` file.
- If product status_id is 18(cropRejected) OR 22(manualImageUpload) then it request is to return.
- If remark is `Image incorrect` then change product status_id to 22(manualImageUpload) otherwise it will set to 18(cropRejected).
- Updatting product crop related columns with setting `is_crop_rejected` to 1.
- Checking `crop_approved_by` > 0 then create new entry to `listing_histories` table by setting action to `CROP_APPROVAL_DENIED`.
- Creating new entry to `listing_histories` table by setting action to `CROP_REJECTED`.
- Creating new entry to `user_product_feedbacks` table by setting action to `CROP_APPROVAL_REJECTED`.

# Instances

- Instance is setup to cropping of uploaded image and that can manage from `Instance` button from top right corner of this page.
- Once we hit this button will open popup from where user can manage instances.
- Multiple instances are added for having more capactiy on cropping.
- New instance can directly add from top section of popup by entring instace id and comment
- Same popup contain list of all saved instance. This list contain information of Instace Id, Comment, Created date and Action column
- Action column contain four icons named start instance, stop instance, delete instance and log instance.
- When user start any instance it will find instance from `cropping_instance` table and call API on another server to start cropping from Python script
- API URL is "config('constants.py_crop_script').'/start'" where py_crop_script defined either in config file of env file.
- Stop instance will stop process of cropping on another server by requesting "config('constants.py_crop_script').'/stop'" API
- Delete instance will delete requested instance from `cropping_instance` table.
- Log instance will get logs from http://173.212.203.50:100/get-logs API by providing instance id and date in request(This date can select from top right corner of Manage Crop Instances popup).
