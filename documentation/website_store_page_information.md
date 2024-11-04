# Store Website Page Documentation

This module helps to create a list of website pages which user can push/pull/edit/delete/get history/set language/view it

#### #Route file

>     `\Modules\StoreWebsite\Routes\web.php`

#### #Controller file

>     `\Modules\StoreWebsite\Http\Controllers\PageController.php`

#### #Model File

>     `\app\StoreWebsitePage.php`

#### #View File

>     `\Modules\StoreWebsite\Resources\views\page\index.blade.php`

#### #Another View files

>     `\Modules\StoreWebsite\Resources\views\page\templates\list-template.php`
>     	This view template is used preparing the table content and paginate with help of JS
>     `\Modules\StoreWebsite\Resources\views\category-seo\templates\create-website-template.php`
>     	This View template has bootstrap modal, when some one click on `edit` button, all the result(html) is append into this modal.

#### #JS file

>     `\public\js\store-website-page.js`
>     	- 	This file is used to perform the ajax function like fetch records, edit records, save update records, push and pull the records
>     `\public\js\common-helper.js`

# First Step

#### #Page Load

**1**. On loading the page it render from view file and there is div id `page-view-result`. JS file will hit the the ajax to get the records after loading the page with the help of `page.init` function in view file. This function trigger `getResults` function in same file, which hit the ajax to get the records with help of ajax.
**2**. `/page/records` is ajax which is pointed towards `controller file` (refer controller file)
**3**. `records` function in `PageController` handles this request and return the suitable data with pagination
**4**. After getting the response from ajax `/page/records` js file call the function `showResults` which handles the data response, where function get the template `template-result-block` from file `list-template.php` and render the HTML in that template including pagination.
**5**. This is how page complete the loading initially.

##### #Filters

> There are some filters on page which are also working on page
> **1**. Language Drop down

    - To filter the page as per language

**2**. Store Website Drop Down - To filter the page as per store website
**3**. Pushed (True/False) - To filter the page which are pushed or not
**4**. Keyword search - To search any keyword in title or content in page

`records` function in `PageController` handles this thing as well
**1**. If `keyword` field is not blank or null in posted data then it will search into `title` and `content` of `store_website_pages` table.
**2**. If `language` field is not blank or null in posted data then it will match the `language` field in `store_website_pages` table
**3**. If `store_website_id` (website of store) field is not blank or null in posted data then it will match the `store_website_id` field in `store_website_pages` table
**4**. If `is_pushed` (page is pushed on website) field is not blank or null in posted data then it will match the `is_pushed` field in `store_website_pages` table

### #Create new page

Plus icon on top right corner in search bar helps to create the page. On click of this buttton it will show a modal, where user will enter the data about page including title, content, keyword, language, active status, website.

In this popup there are other options which helps the user to create the page easily.
**1**. Copy To specific page `(Update data) (Issue/Bug)` - This feature update the parameter which ever are selected in checkbox `Meta Title`, `Meta Keywords`, `Entire site Urls` in selected page. these parameters are replaced by the value which

**2**. Get the copy of another page - This feature helps to fetch the data from a particular page and set all data in their respective fields. - `loadPage` function hit when we click on the refresh icon and user can find this function in js file `\public\js\store-website-page.js` which points to `loadPage` method in `PageController`. - `/page/{page_id}/load-page` is route which return the data as per the `{page_id}` including `content`, `meta_desc`, `meta_keyword`, `meta_title` in response. - `afterLoadPage` is callback function after getting the response from server, which handle the data and set it into the their box as per the selection.

User will enter all the data `title`, `meta keywords`,`meta title` and other fields in popup and then in last user need to select the `store website` and `stores` of that website. Hit the `save changes` button, will hit the ajax `/store-website/page/save` which points to `store` function in `PageController` controller.
In this function system store the data but along with it, system translate the string `title`, `meta_title` etc and save in the DB respective to their language.
Post that system set the queue to push the data into magento which push the page to selected website.

### #Push Storewebsite

This button helps to push all the website pages to their respective websites with the help of queue
`pushPageInLive` function triggers when some click on the button in `\public\js\store-website-page.js` and hit the ajax `"/page/{page_id}/push-website-in-live"` which points to `pushWebsiteInLive` method in `PageController`
`PushPageToMagento` queue is used in this process which push the page with the help of queue named `magetwo`;

### #Pull Storewebsite

This button helps to pull all the website pages to their respective websites with the help of queue
`pullPageInLive` function triggers when some click on the button in `\public\js\store-website-page.js` and hit the ajax `"/page/{page_id}/pull-website-in-live"` which points to `pullWebsiteInLive` method in `PageController`
This pull the data from that website and store in the database and also make the logs of pulling

### #Pull Logs

This button fetch the logs of website store.
`loadLogs` function triggers when some click on the button in `\public\js\store-website-page.js` and hit the ajax `"/page/{page_id}/pull/logs"` which points to `pullLogs` method in `PageController`
This will return the logs activity of all website which ever the system has tried to pull the data/pages.

### #Action Menu

This menu contains 8 submenu actions which user can perform against any page.

**1**. Edit

- This menu is used to edit the page as per the ID of page, on click of this icon it triggers the `editRecord` function in `\public\js\store-website-page.js` which points to url ajax `"/page/{page_id}/edit"` where `{page_id}` is the ID of the page from table, it ended up on `edit` method in `PageController`. This method return the data as per the ID of page otherwise it will return 500 response with error message.
-     After getting the response `editResult` method will get the template `template-create-website` content from `Modules\StoreWebsite\Resources\views\category-seo\templates\create-website-template.php` this templates and render the data automatically and insert into popup so that user can view it.
-     After making the changes user press the `save changes` button, which will hit the ajax `/store-website/page/save` which points to `store` function in `PageController` controller. In this function system store the data but along with it, system translate the string `title`, `meta_title` etc and save in the DB respective to their language. Post that system set the queue to push the data into magento which push the page to selected website.

**2**. Push

- This menu is used to push particular page as per the ID of page, on click of this icon it triggers the `push` function in `\public\js\store-website-page.js` which points to url ajax `"/page/{page_id}/push"` where `{page_id}` is the ID of the page from table, it ended up on `push` method in `PageController`.
- `PushPageToMagento` queue is used in this process which push the page with the help of queue named `magetwo`;

**3**. Pull

- This menu is used to pull particular page as per the ID of page, on click of this icon it triggers the `pull` function in `\public\js\store-website-page.js` which points to url ajax `"/page/{page_id}/pull"` where `{page_id}` is the ID of the page from table, it ended up on `pull` method in `PageController`.
- `MagentoHelper` helper is used in this process which pull the page and insert into db.

**4**. Delete

- This menu is used to delete particular page as per the ID of page, on click of this icon it triggers the `deleteRecord` function in `\public\js\store-website-page.js` which points to url ajax `"/page/{page_id}/delete"` where `{page_id}` is the ID of the page from table, it ended up on `deleteRecord` method in `PageController` and delete the record from DB.

**5**. History

- This menu is used to get history of particular page as per the ID of page, on click of this icon it triggers the `loadHistory` function in `\public\js\store-website-page.js` which points to url ajax `"/page/{page_id}/history"` where `{page_id}` is the ID of the page from table, it ended up on `pageHistory` method in `PageController` and fetch the history of the page from model `StoreWebsitePageHistory`.
- After getting the response from Ajax it goes back to call back function named `afterLoadHistory` which prepare the data in table format and insert into `#preview-history-tbody` table.

**6**. Translate For Other Language

- This menu is used to translate the page string in all languages which are active. on click of this icon it triggers the `translateForOtherLanguage` function in `\public\js\store-website-page.js` which points to url ajax `"/page/{page_id}/translate-for-other-langauge"` where `{page_id}` is the ID of the page from table, it ended up on `translateForOtherLanguage` method in `PageController`.
- After getting the details about that page, system get all the languages which are active right now, and with the help of google translation it translate the content `title`, `meta_title`,`meta_keywords`,`meta_description`,`content_heading`,`content` and save in DB.

**7**. Activities

- This menu is used to get activities log of particular page as per the ID of page, on click of this icon it triggers the `loadActivities` function in `\public\js\store-website-page.js` which points to url ajax `"/page/{page_id}/activities"` where `{page_id}` is the ID of the page from table, it ended up on `pageActivities` method in `PageController` and fetch the activity of the page from model `Activity`.
- After getting the response from Ajax it goes back to call back function named `afterLoadActivities` which prepare the data in table format and insert into `#preview-activities-tbody` table.

**7**. Logs

- This menu is used to get pull log of particular page as per the ID of page, on click of this icon it triggers the `loadLogs` function in `\public\js\store-website-page.js` which points to url ajax `"/page/{page_id}/pull/logs"` where `{page_id}` is the ID of the page from table, it ended up on `pullLogs` method in `PageController` and fetch the pull logs of the page from model `StoreWebsitePagePullLog`.
- After getting the response from Ajax it goes back to call back function named `afterLoadLogs` which prepare the data in table format and insert into `#page-logs-tbodyy` table.
