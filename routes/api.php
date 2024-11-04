<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\v1\AffiliateController;
use App\Http\Controllers\Api\v1\BrandReviewController;
use App\Http\Controllers\Api\v1\BuyBackController;
use App\Http\Controllers\Api\v1\GiftCardController;
use App\Http\Controllers\Api\v1\GoogleScrapperController;
use App\Http\Controllers\Api\v1\PushFcmNotificationController;
use App\Http\Controllers\Api\v1\ReferaFriend;
use App\Http\Controllers\Api\v1\TicketController;
use App\Http\Controllers\Api\v1\VendorController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrokenLinkCheckerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DevelopmentController;
use App\Http\Controllers\DevOppsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EnvController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\FacebookPostController;
use App\Http\Controllers\Github\RepositoryController;
use App\Http\Controllers\GitHubActionController;
use App\Http\Controllers\GoogleAffiliateController;
use App\Http\Controllers\GoogleSearchController;
use App\Http\Controllers\HashtagController;
use App\Http\Controllers\InfluencersController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\InstagramPostsController;
use App\Http\Controllers\InstantMessagingController;
use App\Http\Controllers\LaravelLogController;
use App\Http\Controllers\Logging;
use App\Http\Controllers\MagentoCareersController;
use App\Http\Controllers\MagentoCustomerReferenceController;
use App\Http\Controllers\MagentoProblemController;
use App\Http\Controllers\MissingBrandController;
use App\Http\Controllers\NodeScrapperCategoryMapController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PriceComparisionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCropperController;
use App\Http\Controllers\Products;
use App\Http\Controllers\ProductTemplatesController;
use App\Http\Controllers\QuickSellController;
use App\Http\Controllers\ResourceImgController;
use App\Http\Controllers\ScrapController;
use App\Http\Controllers\scrapperPhyhon;
use App\Http\Controllers\ScrapStatisticsController;
use App\Http\Controllers\SearchQueueController;
use App\Http\Controllers\Shopify\ShopifyController;
use App\Http\Controllers\SimplyDutyCalculationController;
use App\Http\Controllers\SimplyDutyCountryController;
use App\Http\Controllers\SimplyDutyCurrencyController;
use App\Http\Controllers\SocialWebhookController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\UpdateLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\WeTransferController;
use Illuminate\Support\Facades\Route;
use Modules\ChatBot\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::post('login', [Api\v1\Auth\LoginController::class, 'login']);
Route::post('register', [Api\v1\Auth\LoginController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('mailinglist/add', [Api\v1\MailinglistController::class, 'add']);
    Route::post('fetch-credit-balance', [CustomerController::class, 'fetchCreditBalance']);
    Route::post('deduct-credit', [CustomerController::class, 'deductCredit']);
    Route::post('add-env', [EnvController::class, 'addEnv'])->name('add-env');
    Route::post('edit-env', [EnvController::class, 'editEnv'])->name('edit-env');

    Route::post('add-credit', [CustomerController::class, 'addCredit']);

    Route::post('customer/add_customer_data', [CustomerController::class, 'add_customer_data']); //Purpose : Add Customer Data - DEVTASK-19932

    // Scrape
    Route::prefix('scrape')->group(function () {
        Route::post('queue', [Products\ScrapeController::class, 'getUrlFromQueue']);
        Route::get('process', [Products\ScrapeController::class, 'processDataFromScraper']);
        Route::post('send-screenshot', [ScrapController::class, 'sendScreenshot']);
        Route::post('send-position', [ScrapController::class, 'sendPosition']);

        Route::post('process-product-links', [ScrapController::class, 'processProductLinks']);
        Route::post('process-product-links-by-brand', [ScrapController::class, 'processProductLinksByBrand']);
    });

    // Messages
    Route::prefix('messages')->group(function () {
        Route::get('/{thread}', [InstagramController::class, 'getThread']);
        Route::post('/{thread}', [InstagramController::class, 'replyToThread']);
    });

    Route::post('sync-product', [ScrapController::class, 'syncGnbProducts']); // This function is not found in controller
    Route::post('scrap-products/add', [ScrapController::class, 'syncProductsFromNodeApp']);
    Route::post('add-product-entries', [ScrapController::class, 'addProductEntries']); // This function is not found in controller
    Route::post('add-product-images', [ScrapController::class, 'getProductsForImages']); // This function is not found in controller
    Route::post('save-product-images', [ScrapController::class, 'saveImagesToProducts']); // This function is not found in controller
    Route::post('save-product-images2', [ScrapController::class, 'saveImagesToProducts2']); // This function is not found in controller
    Route::post('save-supplier', [ScrapController::class, 'saveSupplier']);
    Route::get('hashtags', [HashtagController::class, 'sendHashtagsApi']);

    Route::post('link/image-crop', [ProductController::class, 'saveImage']);

    Route::resource('stat', ScrapStatisticsController::class);

    // Crop
    Route::prefix('crop')->group(function () {
        Route::get('/', [ProductController::class, 'giveImage']);
        Route::get('amends', [ProductCropperController::class, 'giveAmends']);
        Route::post('amends', [ProductCropperController::class, 'saveAmends']);
    });

    // Products
    Route::prefix('products')->group(function () {
        Route::get('auto-rejected', [ScrapController::class, 'getProductsToScrape']);
        Route::post('auto-rejected', [ScrapController::class, 'saveScrapedProduct']); // This function is not found in controller
        Route::get('get-products-to-scrape', [ScrapController::class, 'getProductsToScrape']); // This function is also call for other route
        Route::post('save-scraped-product', [ScrapController::class, 'saveScrapedProduct']); // This function is not found in controller
        Route::get('new-supplier', [ScrapController::class, 'getProductsToScrape']); // This function is also call for other route
        Route::post('new-supplier', [ScrapController::class, 'saveFromNewSupplier']);
        Route::get('enhance', [Products\ProductEnhancementController::class, 'index']);
        Route::post('enhance', [Products\ProductEnhancementController::class, 'store']);
    });

    Route::post('twilio-conference', [TwilioController::class, 'outgoingCallConference']);
    Route::post('twilio-conference-mute', [TwilioController::class, 'muteConferenceNumber']);
    Route::post('twilio-conference-hold', [TwilioController::class, 'holdConferenceNUmber']);
    Route::post('twilio-conference-remove', [TwilioController::class, 'removeConferenceNumber']);

    Route::get('broken-link-details', [BrokenLinkCheckerController::class, 'getBrokenLinkDetails']);

    Route::post('users/updatePermission', [PermissionController::class, 'updatePermission']);
    Route::post('userLogs', [UserLogController::class, 'store']);

    Route::post('values-as-per-user', [DocumentController::class, 'getDataByUserType'])->name('getDataByUserType');
    Route::post('values-as-per-category', [ResourceImgController::class, 'getSubCategoryByCategory'])->name('imageResourceSubcategory');
    Route::post('get-customers', [QuickSellController::class, 'getCustomers'])->name('getCustomers'); // This function is not found in controller

    Route::prefix('product-template')->group(function () {
        Route::get('/', [ProductTemplatesController::class, 'apiIndex']);
        Route::post('/', [ProductTemplatesController::class, 'apiSave']);
    });

    Route::post('new-product-template', [ProductTemplatesController::class, 'NewApiSave']);

    Route::get('{client}/{numberFrom}/get-im', [InstantMessagingController::class, 'getMessage']);
    Route::post('{client}/{numberFrom}/webhook', [InstantMessagingController::class, 'processWebhook']);
    Route::get('{client}/{numberFrom}/im-status-update', [InstantMessagingController::class, 'updatePhoneStatus']);

    Route::post('{client}/{numberFrom}/social-message', [FacebookController::class, 'storeMessages']); // This function is not found in controller

    //Competitor Facebook
    Route::get('{client}/{numberFrom}/competitor', [FacebookController::class, 'competitor']); // This function is not found in controller

    Route::post('{client}/{numberFrom}/competitor', [FacebookController::class, 'saveCompetitor']); // This function is not found in controller

    // Facebook
    Route::prefix('facebook')->group(function () {
        //Scrapped facebook users
        Route::post('scrape-user', [FacebookController::class, 'apiPost']);
        Route::post('post', [FacebookController::class, 'facebookPost']);
        Route::post('post/status', [FacebookPostController::class, 'setPostStatus']);
        Route::post('account', [FacebookPostController::class, 'getPost']);
    });

    // Duty
    Route::prefix('duty')->group(function () {
        Route::prefix('v1')->group(function () {
            Route::get('get-currencies', [SimplyDutyCurrencyController::class, 'sendCurrencyJson']);
            Route::get('get-countries', [SimplyDutyCountryController::class, 'sendCountryJson']);
            Route::post('calculate', [SimplyDutyCalculationController::class, 'calculate']);
        });
    });

    // INSTAGRAM
    Route::prefix('instagram')->group(function () {
        Route::post('post', [InstagramPostsController::class, 'apiPost']);
        Route::get('send-account/{token}', [InstagramPostsController::class, 'sendAccount']);
        Route::get('get-comments-list/{username}', [InstagramPostsController::class, 'getComments']);
        Route::post('comment-sent', [InstagramPostsController::class, 'commentSent']);
        Route::get('get-hashtag-list', [InstagramPostsController::class, 'getHashtagList']);
        Route::post('create', [AccountController::class, 'createAccount']);
        //Get all the instagram accounts attached to keywords
        Route::get('accounts', [InfluencersController::class, 'getKeywordsWithAccount']);
    });

    //Giving All Brands with Reference
    Route::get('brands', [BrandController::class, 'brandReference']);

    // SUPPLIERS
    Route::post('supplier/brands-raw', [SupplierController::class, 'apiBrandsRaw']);

    //Google
    Route::prefix('google')->group(function () {
        Route::get('keywords', [GoogleSearchController::class, 'getKeywordsApi']);
        Route::post('search-results', [GoogleSearchController::class, 'apiPost']);
        //Google affiliate search
        Route::prefix('affiliate')->group(function () {
            Route::get('keywords', [GoogleAffiliateController::class, 'getKeywordsApi']);
            Route::post('search-results', [GoogleAffiliateController::class, 'apiPost']);
        });
    });

    //Wetransfer
    Route::get('wetransfer', [WeTransferController::class, 'getLink']);
    Route::post('wetransfer-file-store', [WeTransferController::class, 'storeFile']);

    // Scraper

    Route::prefix('scraper')->group(function () {
        Route::get('next', [ScrapController::class, 'sendScrapDetails']);
        Route::post('endtime', [ScrapController::class, 'recieveScrapDetails']);
        // Scraper ready api
        Route::post('ready', [ScrapController::class, 'scraperReady']);
        Route::post('completed', [ScrapController::class, 'scraperCompleted']);
        Route::get('need-to-start', [ScrapController::class, 'needToStart']);
        Route::get('update-restart-time', [ScrapController::class, 'updateRestartTime']);
        Route::get('auto-restart', [ScrapController::class, 'needToAutoRestart']);
    });

    // Search
    Route::prefix('search')->group(function () {
        Route::get('/{type}', [SearchQueueController::class, 'index']);
        Route::post('/{type}', [SearchQueueController::class, 'upload_content']);
    });

    //Magneto Customer Reference Store
    Route::post('magento/customer-reference', [MagentoCustomerReferenceController::class, 'store']);
    Route::post('product-live-status', [Logging\LogListMagentoController::class, 'updateLiveProductCheck']);

    // Node
    Route::prefix('node')->group(function () {
        Route::post('restart-script', [ScrapController::class, 'restartNode']);
        Route::post('update-script', [ScrapController::class, 'updateNode']);
        Route::post('kill-script', [ScrapController::class, 'killNode']);
        Route::post('get-status', [ScrapController::class, 'getStatus']);
        Route::get('get-log', [ScrapController::class, 'getLatestLog'])->name('scraper.get.log.list');
    });

    // Local
    Route::prefix('local')->group(function () {
        Route::post('instagram-post', [InstagramPostsController::class, 'saveFromLocal']);
        Route::get('instagram-user-post', [InstagramPostsController::class, 'getUserForLocal']);
    });

    Route::prefix('v1')->group(function () {
        Route::prefix('product')->group(function () {
            Route::prefix('{sku}')->group(function () {
                Route::get('price', [Api\v1\ProductController::class, 'price']);
            });
        });

        Route::prefix('account')->group(function () {
            Route::post('create', [Api\v1\AccountController::class, 'create']);
        });
    });

    // Scraper ready api
    Route::get('scraper-needed-products', [ScrapController::class, 'scraperNeeded']);

    // Shopify
    Route::prefix('shopify')->group(function () {
        Route::post('customer/create', [ShopifyController::class, 'setShopifyCustomers']);
        Route::post('order/create', [ShopifyController::class, 'setShopifyOrders']);
    });

    // Price Comparision
    Route::prefix('price_comparision')->group(function () {
        Route::get('{type}', [PriceComparisionController::class, 'index']);
        Route::post('store', [PriceComparisionController::class, 'storeComparision']);
        Route::post('details', [PriceComparisionController::class, 'sendDetails']);
    });

    //order details api for a customer
    Route::get('customer/order-details', [OrderController::class, 'customerOrderDetails']);

    //refer a friend api
    Route::post('friend/referral/create', [ReferaFriend::class, 'store']);

    //Ticket
    Route::prefix('ticket')->group(function () {
        Route::post('create', [TicketController::class, 'store']);
        Route::post('send', [TicketController::class, 'sendTicketsToCustomers']);
    });

    Route::post('store_reviews', [Api\v1\CustomerController::class, 'storeReviews']);
    Route::get('all-reviews', [Api\v1\CustomerController::class, 'allReviews']);

    // Gift Cards
    Route::prefix('giftcards')->group(function () {
        Route::post('add', [GiftCardController::class, 'store']);
        Route::get('check-giftcard-coupon-amount', [GiftCardController::class, 'checkGiftcardCouponAmount']);
    });

    //Affiliate Api
    Route::post('affiliate/add', [AffiliateController::class, 'store']);
    Route::post('influencer/add', [AffiliateController::class, 'store']);

    //buyback cards api
    Route::get('orders/products', [BuyBackController::class, 'checkProductsForBuyback']);
    Route::post('return-exchange-buyback/create', [BuyBackController::class, 'store']);

    // Notification
    Route::prefix('notification')->group(function () {
        //Push Notification Api
        Route::post('create', [PushFcmNotificationController::class, 'create']);
        Route::post('update-lang', [PushFcmNotificationController::class, 'updateLang']);
    });

    //Saving Not Found Brand
    Route::get('missing-brand/save', [MissingBrandController::class, 'saveMissingBrand']);
    // Scraper info
    Route::get('{supplierName}/supplier-list', [SupplierController::class, 'supplierList']);

    //Store data into the laravel_logs
    Route::post('laravel-logs/save', [LaravelLogController::class, 'saveNewLogData']);

    Route::post('templates/create/webhook', [TemplatesController::class, 'createWebhook']);
    Route::post('product/templates/update/webhook', [ProductTemplatesController::class, 'updateWebhook'])->name('api.product.update.webhook');

    // Order
    Route::prefix('order')->group(function () {
        //check for order cancellation
        Route::post('check-cancellation', [Api\v1\ProductController::class, 'checkCancellation']);
        Route::post('check-return', [Api\v1\ProductController::class, 'checkReturn']);
        Route::post('check-category-is-eligibility', [Api\v1\ProductController::class, 'checkCategoryIsEligibility']);
        //Sync Transaction with order
        Route::post('sync-transaction', [OrderController::class, 'syncTransaction']);
    });

    // Wishlist
    Route::prefix('wishlist')->group(function () {
        Route::post('create', [Api\v1\ProductController::class, 'wishList']);
        Route::post('remove', [Api\v1\ProductController::class, 'wishListRemove']);
    });

    Route::post('github/gettoken', [RepositoryController::class, 'addGithubTokenHistory']);

    Route::post('magento/order-create', [MagentoCustomerReferenceController::class, 'createOrder']);

    Route::post('scraper-images-save', [scrapperPhyhon::class, 'imageSave']);

    // Review
    Route::prefix('review')->group(function () {
        //New API for trust pilot reviews
        Route::get('get', [BrandReviewController::class, 'getAllBrandReview']);
        Route::post('scrap', [BrandReviewController::class, 'storeReview']);
    });

    Route::post('google-scrapper-data', [GoogleScrapperController::class, 'extractedData']);

    //Out Of Stock Subscribe
    Route::post('out-of-stock-subscription', [Api\v1\OutOfStockSubscribeController::class, 'Subscribe']);
    Route::any('get-order-stat', [Api\v1\OutOfStockSubscribeController::class, 'getOrderState']);
    Route::post('customer/add_cart_data', [Api\v1\CustomerController::class, 'add_cart_data']);

    // Social
    Route::prefix('social')->group(function () {
        // Social Webhook
        Route::get('webhook', [SocialWebhookController::class, 'verifyWebhook']);
        Route::get('webhookfbtoken', [SocialWebhookController::class, 'webhookfbtoken']);
        Route::post('webhook', [SocialWebhookController::class, 'webhook']);
        Route::post('fbtoken', [SocialWebhookController::class, 'fbtoken']);
    });

    Route::post('updateLog', [UpdateLogController::class, 'store']);

    Route::middleware('api')->prefix('auth')->group(function ($router) {
        Route::post('logout', [Api\v1\Auth\LoginController::class, 'logout']);
        Route::post('refresh', [Api\v1\Auth\LoginController::class, 'refresh']);
        Route::post('me', [Api\v1\Auth\LoginController::class, 'me']);
    });
    Route::middleware('custom.api.auth')->group(function () {
        Route::get('/chatbot/messages', [MessageController::class, 'messagesJson']);
        Route::get('/email/{email?}', [EmailController::class, 'emailJson']);
        Route::get('/todolist', [TodoListController::class, 'indexJson']);
        Route::post('/todolist/update', [TodoListController::class, 'updateJson']);
        Route::delete('/todolist/delete/{id}', [TodoListController::class, 'destroyJson']);
    });

    Route::post('users/add-system-ip-from-email', [UserController::class, 'addSystemIpFromEmail']);

    Route::post('/github-action', [GitHubActionController::class, 'store']);

    Route::post('/magento-problem', [MagentoProblemController::class, 'store']);
    Route::get('magento_modules/listing-careers', [MagentoCareersController::class, 'listingApi'])->name('magento_module_listing_careers_listing_api');

    Route::post('scrapper-category-map', [NodeScrapperCategoryMapController::class, 'store']);
    Route::post('get-scrapper-category-map', [NodeScrapperCategoryMapController::class, 'getRecord']);

    // Add scrapper Ajax request
    Route::get('development/view-scrapper/{id}', [DevelopmentController::class, 'viewScrapper']);

    // Devoops
    Route::prefix('devoops')->group(function () {
        Route::prefix('subcategory')->group(function () {
            // To get devoops subcategory for selected category
            Route::get('/{id}', [DevOppsController::class, 'getSubcategoryByCategory']);
            Route::get('/', [DevOppsController::class, 'getAllSubcategory']);
        });
    });

    // Receive SNS alerts
    Route::post('alert', [AlertController::class, 'store']);

    // Add vendor request for react component
    Route::get('vendors', [VendorController::class, 'index']);
});
