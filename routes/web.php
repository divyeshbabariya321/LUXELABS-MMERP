<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// use App\Http\Controllers\Migration\MigrationController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityConroller;
use App\Http\Controllers\AdsController;
use App\Http\Controllers\AffiliateMarketing\AffiliateMarketingController;
use App\Http\Controllers\AffiliateMarketing\AffiliateMarketingDataController;
use App\Http\Controllers\AffiliateResultController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Api\v1\BrandReviewController;
use App\Http\Controllers\ApiResponseMessageController;
use App\Http\Controllers\AppConnect\AppConnectController;
use App\Http\Controllers\AppointmentRequestController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AssetsManagerController;
use App\Http\Controllers\AssetsManagerUsersAccessController;
use App\Http\Controllers\AttributeReplacementController;
use App\Http\Controllers\AutoCommentHistoryController;
use App\Http\Controllers\AutoRefreshController;
use App\Http\Controllers\AutoReplyController;
use App\Http\Controllers\AutoReplyHashtagsController;
use App\Http\Controllers\BackLinkController;
use App\Http\Controllers\BankStatementController;
use App\Http\Controllers\BenchmarkController;
use App\Http\Controllers\BingWebMasterController;
use App\Http\Controllers\BlogCentralizeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BloggerController;
use App\Http\Controllers\BloggerEmailTemplateController;
use App\Http\Controllers\BloggerPaymentController;
use App\Http\Controllers\BloggerProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandSizeChartController;
use App\Http\Controllers\BrandTaggedPostsController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\BroadcastMessageController;
use App\Http\Controllers\BrokenLinkCheckerController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BugTrackingController;
use App\Http\Controllers\BulkCustomerRepliesController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\CaseReceivableController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategorySegmentController;
use App\Http\Controllers\ChangeDescriptionController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ChatbotMessageLogsController;
use App\Http\Controllers\ChatbotTypeErrorLogController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatGPT\ChatGPTController;
use App\Http\Controllers\ChatMessagesController;
use App\Http\Controllers\CheckListController;
use App\Http\Controllers\CodeShortcutController;
use App\Http\Controllers\ColdLeadBroadcastsController;
use App\Http\Controllers\ColdLeadsController;
use App\Http\Controllers\ColorReferenceController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\CompetitorPageController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\CompositionsController;
use App\Http\Controllers\ConfigRefactorController;
use App\Http\Controllers\ContactBloggerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentManagementController;
use App\Http\Controllers\ConversionRateController;
use App\Http\Controllers\CountryDutyController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\Cron;
use App\Http\Controllers\CronController;
use App\Http\Controllers\CroppedImageReferenceController;
use App\Http\Controllers\CropRejectedController;
use App\Http\Controllers\CsvTranslatorController;
use App\Http\Controllers\CustomerCategoryController;
use App\Http\Controllers\CustomerCharityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DailyActivityController;
use App\Http\Controllers\DailyCashFlowController;
use App\Http\Controllers\DailyPlannerController;
use App\Http\Controllers\DatabaseBackupMonitoringController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DatabaseTableController;
use App\Http\Controllers\DBQueryController;
use App\Http\Controllers\DeliveryApprovalController;
use App\Http\Controllers\DeploymentVersionController;
use App\Http\Controllers\DesignerController;
use App\Http\Controllers\DetailsController;
use App\Http\Controllers\DeveloperMessagesAlertSchedulesController;
use App\Http\Controllers\DevelopmentController;
use App\Http\Controllers\DevOppsController;
use App\Http\Controllers\DigitalMarketingController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\DiscountSalePriceController;
use App\Http\Controllers\DocuemntCategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DomainSearchKeywordController;
use App\Http\Controllers\DubbizleController;
use App\Http\Controllers\EmailAddressesController;
use App\Http\Controllers\EmailContentHistoryController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmailDataExtractionController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\EmailReceiverMasterController;
use App\Http\Controllers\EncryptController;
use App\Http\Controllers\EnvController;
use App\Http\Controllers\ErpEventController;
use App\Http\Controllers\ErpLogController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExotelController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\FacebookPostController;
use App\Http\Controllers\FailedJobController;
use App\Http\Controllers\FaqPushController;
use App\Http\Controllers\FcmNotificationController;
use App\Http\Controllers\FilePermissionController;
use App\Http\Controllers\FlowController;
use App\Http\Controllers\FlowLogController;
use App\Http\Controllers\GeminiAIController;
use App\Http\Controllers\Github;
use App\Http\Controllers\GitHubActionController;
use App\Http\Controllers\GlobalComponants\FilesAndAttachmentsController;
use App\Http\Controllers\GmailDataController;
use App\Http\Controllers\GoogleAddWord;
use App\Http\Controllers\GoogleAdGroupController;
use App\Http\Controllers\GoogleAdGroupKeywordController;
use App\Http\Controllers\GoogleAdReportController;
use App\Http\Controllers\GoogleAdsAccountController;
use App\Http\Controllers\GoogleAdsController;
use App\Http\Controllers\GoogleAdsLogController;
use App\Http\Controllers\GoogleAdsRemarketingController;
use App\Http\Controllers\GoogleAffiliateController;
use App\Http\Controllers\GoogleAppAdController;
use App\Http\Controllers\GoogleBigQueryDataController;
use App\Http\Controllers\GoogleBillingsController;
use App\Http\Controllers\GoogleCampaignLocationController;
use App\Http\Controllers\GoogleCampaignsController;
use App\Http\Controllers\GoogleDeveloperController;
use App\Http\Controllers\GoogleDeveloperLogsController;
use App\Http\Controllers\GoogleDialogFlowController;
use App\Http\Controllers\GoogleDocController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\GoogleFileTranslator;
use App\Http\Controllers\GoogleResponsiveDisplayAdController;
use App\Http\Controllers\GoogleScrapperController;
use App\Http\Controllers\GoogleScreencastController;
use App\Http\Controllers\GoogleSearchController;
use App\Http\Controllers\GoogleSearchImageController;
use App\Http\Controllers\GoogleServerController;
use App\Http\Controllers\GoogleShoppingAdsController;
use App\Http\Controllers\GoogleTranslateController;
use App\Http\Controllers\GoogleTraslationSettingsController;
use App\Http\Controllers\GoogleWebMasterController;
use App\Http\Controllers\GTMatrixErrorLogController;
use App\Http\Controllers\gtmetrix;
use App\Http\Controllers\HashtagController;
use App\Http\Controllers\HashtagPostCommentController;
use App\Http\Controllers\HashtagPostsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HsCodeController;
use App\Http\Controllers\Hubstaff;
use App\Http\Controllers\HubstaffActivitiesController;
use App\Http\Controllers\HubstaffController;
use App\Http\Controllers\HubstaffPaymentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\IndexerStateController;
use App\Http\Controllers\InfluencersController;
use App\Http\Controllers\InstagramAutoCommentsController;
use App\Http\Controllers\InstagramAutomatedMessagesController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\InstagramPostsController;
use App\Http\Controllers\InstagramProfileController;
use App\Http\Controllers\InstructionController;
use App\Http\Controllers\InventoryUpdateLogController;
use App\Http\Controllers\IpLogController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\KeywordassignController;
use App\Http\Controllers\KeywordInstructionController;
use App\Http\Controllers\KeywordsController;
use App\Http\Controllers\KeywordToCategoryController;
use App\Http\Controllers\KeywordVariantController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LaravelLogController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\LeadOrderController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\LearningCategoryController;
use App\Http\Controllers\LearningModuleController;
use App\Http\Controllers\ListingHistoryController;
use App\Http\Controllers\ListingPaymentsController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\LoadTestingController;
use App\Http\Controllers\Logging;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogScraperVsAiController;
use App\Http\Controllers\MagentoBackendDocumentationController;
use App\Http\Controllers\MagentoCareersController;
use App\Http\Controllers\MagentoCommandController;
use App\Http\Controllers\MagentoController;
use App\Http\Controllers\MagentoCssVariableController;
use App\Http\Controllers\MagentoFrontendDocumentationController;
use App\Http\Controllers\MagentoLocationController;
use App\Http\Controllers\MagentoModuleApiHistoryController;
use App\Http\Controllers\MagentoModuleCategoryController;
use App\Http\Controllers\MagentoModuleController;
use App\Http\Controllers\MagentoModuleCronJobHistoryController;
use App\Http\Controllers\MagentoModuleCustomizedHistoryController;
use App\Http\Controllers\MagentoModuleHistoryController;
use App\Http\Controllers\MagentoModuleJsRequireHistoryController;
use App\Http\Controllers\MagentoModuleReturnTypeErrorStatusController;
use App\Http\Controllers\MagentoModuleTypeController;
use App\Http\Controllers\MagentoProblemController;
use App\Http\Controllers\MagentoProductPushErrors;
use App\Http\Controllers\MagentoSettingRevisionHistoryController;
use App\Http\Controllers\MagentoSettingsController;
use App\Http\Controllers\MagentoUserFromErpController;
use App\Http\Controllers\MailBoxController;
use App\Http\Controllers\ManageModulesController;
use App\Http\Controllers\ManageTaskCategoryController;
use App\Http\Controllers\Marketing;
use App\Http\Controllers\Marketing\WhatsappBusinessAccountController;
use App\Http\Controllers\MasterControlController;
use App\Http\Controllers\MasterDevTaskController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Meeting;
use App\Http\Controllers\MemoryUsesController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageQueueHistoryController;
use App\Http\Controllers\MindMapDiagramController;
use App\Http\Controllers\MissingBrandController;
use App\Http\Controllers\ModelNameController;
use App\Http\Controllers\MonetaryAccountController;
use App\Http\Controllers\MonitorJenkinsBuildController;
use App\Http\Controllers\MonitorServerController;
use App\Http\Controllers\MonitStatusController;
use App\Http\Controllers\NegativeCouponResponseController;
use App\Http\Controllers\NewDevTaskController;
use App\Http\Controllers\NewProductInventoryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NodeScrapperCategoryMapController;
use App\Http\Controllers\NotificaitonContoller;
use App\Http\Controllers\NotificationQueueController;
use App\Http\Controllers\OldController;
use App\Http\Controllers\OldIncomingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderReportController;
use App\Http\Controllers\PageNotesCategoriesController;
use App\Http\Controllers\PageNotesController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PictureColorsController;
use App\Http\Controllers\Pinterest\PinterestAccountController;
use App\Http\Controllers\Pinterest\PinterestAdsAccountsController;
use App\Http\Controllers\Pinterest\PinterestCampaignsController;
use App\Http\Controllers\Pinterest\PinterestPinsController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PleskController;
use App\Http\Controllers\PopupModelController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PostmanRequestCreateController;
use App\Http\Controllers\PreAccountController;
use App\Http\Controllers\PriceComparisionController;
use App\Http\Controllers\product_price;
use App\Http\Controllers\ProductApproverController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCropperController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\ProductListerController;
use App\Http\Controllers\ProductLocationController;
use App\Http\Controllers\Products;
use App\Http\Controllers\ProductSearcherController;
use App\Http\Controllers\ProductSelectionController;
use App\Http\Controllers\ProductSupervisorController;
use App\Http\Controllers\ProductTemplatesController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFileManagerController;
use App\Http\Controllers\ProjectThemeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseProductController;
use App\Http\Controllers\PurchaseStatusController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\QuickCustomerController;
use App\Http\Controllers\QuickReplyController;
use App\Http\Controllers\QuickSellController;
use App\Http\Controllers\RedisjobController;
use App\Http\Controllers\RedisQueueController;
use App\Http\Controllers\ReferFriendController;
use App\Http\Controllers\ReferralProgramController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ResourceImgController;
use App\Http\Controllers\ReturnExchangeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoutesController;
use App\Http\Controllers\S3Controller;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesItemController;
use App\Http\Controllers\ScrapController;
use App\Http\Controllers\ScrapLogsController;
use App\Http\Controllers\ScrappedFacebookUserController;
use App\Http\Controllers\scrapperPhyhon;
use App\Http\Controllers\ScrapStatisticsController;
use App\Http\Controllers\ScriptDocumentsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Select2Controller;
use App\Http\Controllers\SemrushController;
use App\Http\Controllers\SentryLogController;
use App\Http\Controllers\Seo;
use App\Http\Controllers\SEOAnalyticsController;
use App\Http\Controllers\SeoToolController;
use App\Http\Controllers\SERankingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SimplyDutyCalculationController;
use App\Http\Controllers\SimplyDutyCategoryController;
use App\Http\Controllers\SimplyDutyCountryController;
use App\Http\Controllers\SimplyDutyCurrencyController;
use App\Http\Controllers\SimplyDutySegmentController;
use App\Http\Controllers\SitejabberQAController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SkuController;
use App\Http\Controllers\SkuFormatController;
use App\Http\Controllers\Slack\SlackChannelController;
use App\Http\Controllers\SocialAccountCommentController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\SocialTagsController;
use App\Http\Controllers\SonarQubeController;
use App\Http\Controllers\SopController;
use App\Http\Controllers\SopShortcutCreateController;
use App\Http\Controllers\SshLoginController;
use App\Http\Controllers\StatusMappingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StoreGTMetrixAccountController;
use App\Http\Controllers\StoreSocialContentStatusController;
use App\Http\Controllers\StoreWebsiteAnalyticsController;
use App\Http\Controllers\StoreWebsiteCountryShippingController;
use App\Http\Controllers\SupplierCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemSizeController;
use App\Http\Controllers\TargetLocationController;
use App\Http\Controllers\TaskCategoriesController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskHistoryController;
use App\Http\Controllers\TaskModuleController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TaskTypesController;
use App\Http\Controllers\TechnicalDebtController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\TestCaseAutomationController;
use App\Http\Controllers\TestCaseController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\TestSuitesController;
use App\Http\Controllers\ThemeStructureController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TimeDoctorActivitiesController;
use App\Http\Controllers\TimeDoctorController;
use App\Http\Controllers\TmpController;
use App\Http\Controllers\TmpTaskController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\TwiliochatController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\TwillioMessageController;
use App\Http\Controllers\UicheckController;
use App\Http\Controllers\UnknownAttributeProductController;
use App\Http\Controllers\UpdateLogController;
use App\Http\Controllers\UserActionsController;
use App\Http\Controllers\UserAvaibilityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEventController;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\UsersAutoCommentHistoriesController;
use App\Http\Controllers\UsersFeedbackHrTicketController;
use App\Http\Controllers\VendorCategoryController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorPaymentController;
use App\Http\Controllers\VendorResumeController;
use App\Http\Controllers\VendorSupplierController;
use App\Http\Controllers\VirtualminDomainController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\VoucherCouponController;
use App\Http\Controllers\WatsonController;
use App\Http\Controllers\WebNotificationController;
use App\Http\Controllers\WebsiteLogController;
use App\Http\Controllers\WeTransferController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\YoutubeController;
use App\Http\Controllers\Zabbix\ItemController;
use App\Http\Controllers\Zabbix\TriggerController;
use App\Http\Controllers\Zabbix\UserController as ZabbixUserController;
use App\Http\Controllers\ZabbixController;
use App\Http\Controllers\ZabbixTaskController;
use App\Http\Controllers\ZabbixWebhookDataController;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\HttpClientTimeout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\MessageQueue\Http\Controllers\MessageQueueController;
use seo2websites\PriceComparisonScraper\PriceComparisonScraper;

Route::get('/api-docs/swagger.json', function () {
    return response()->file(storage_path('api-docs/swagger.json'));
});

Route::post('global_files_and_attachments_store', [FilesAndAttachmentsController::class, 'store_data'])->name('global_files_and_attachments_store');
Route::post('global_files_and_attachments', [FilesAndAttachmentsController::class, 'get_data'])->name('global_files_and_attachments');
Route::get('global_files_and_attachments_download/{filename}', [FilesAndAttachmentsController::class, 'download'])->name('global_files_and_attachments_download');

Auth::routes();

/* ------------------ Modals Route -------------------*/
Route::get('/menu-user-search-modal', function () {
    return view('modals-partial.menu-user-search-model');
});
Route::get('/menu-sop-search-model', function () {
    return view('modals-partial.menu-sop-search-model');
});
Route::get('/exampleModalAppLayout', function () {
    return view('modals-partial.exampleModalAppLayout');
});
Route::get('/menu-email-search', function () {
    return view('modals-partial.menu-email-search-model');
});
Route::get('/menu-sopupdate', function () {
    return view('modals-partial.menu-sopupdate');
});
Route::get('/decline-remarks', function () {
    return view('modals-partial.decline-remarks');
});
Route::get('/quickRequestZoomModal', function () {
    return view('modals-partial.quickRequestZoomModal');
});
Route::get('/commandResponseHistoryModelHeader', function () {
    return view('modals-partial.commandResponseHistoryModelHeader');
});
Route::get('/addPostman_header', function () {
    return view('modals-partial.addPostman_header');
});
Route::get('/instructionAlertModal', function () {
    return view('modals-partial.instructionAlertModal');
});
Route::get('/developerAlertModal', function () {
    return view('modals-partial.developerAlertModal');
});
Route::get('/permission-request-model', function () {
    return view('modals-partial.permission-request-model');
});
Route::get('/showLatestEstimateTime', function () {
    return view('modals-partial.showLatestEstimateTime');
});
Route::get('/todolist-request-model', function () {
    return view('modals-partial.todolist-request-model');
});
Route::get('/menu-create-database-model', function () {
    return view('modals-partial.menu-create-database-model');
});
Route::get('/menu-show-task-model', function () {
    return view('modals-partial.menu-show-task-model');
});
Route::get('/menu-show-dev-task-model', function () {
    return view('modals-partial.menu-show-dev-task-model');
});
Route::get('/menu-todolist-get-model', function () {
    return view('modals-partial.menu-todolist-get-model');
});
Route::get('/menu_user_history_modal', function () {
    return view('modals-partial.menu_user_history_modal');
});
Route::get('/menu_confirmMessageModal', function () {
    return view('modals-partial.menu_confirmMessageModal');
});
Route::get('/menu-upload-document-modal', function () {
    return view('modals-partial.menu-upload-document-modal');
});
Route::get('/menu-blank-modal', function () {
    return view('modals-partial.menu-blank-modal');
});
Route::get('/menu-file-upload-area-section', function () {
    return view('modals-partial.menu-file-upload-area-section');
});
Route::get('/menu-preview-task-image', function () {
    return view('modals-partial.menu-preview-task-image');
});
Route::get('/create-manual-payment', function () {
    return view('modals-partial.create-manual-payment');
});
Route::get('/create-sop-shortcut', function () {
    return view('modals-partial.create-sop-shortcut');
});
Route::get('/system-request', function () {
    return view('modals-partial.system-request');
});
Route::get('/emailAlertModal', function () {
    return view('modals-partial.emailAlertModal');
});
Route::get('/view-quick-email', function () {
    return view('modals-partial.view-quick-email');
});

/* ------------------Twilio functionality Routes[PLEASE DONT MOVE INTO MIDDLEWARE AUTH] ------------------------ */
Route::middleware(['web'])->group(function () {
    Route::get('/twilio/token', [TwilioController::class, 'createToken']);
    Route::post('twilio/ivr', [TwilioController::class, 'ivr'])->name('ivr')->middleware('twilio.voice.validate');
    Route::any('twilio/webhook-error', [TwilioController::class, 'webhookError']);
    Route::post('twilio/workspace/assignment', [TwilioController::class, 'workspaceEvent']);
    Route::post('twilio/assignment-task', [TwilioController::class, 'assignmentTask']);
    Route::post('twilio/call-status', [TwilioController::class, 'callStatus']);
    Route::post('twilio/wait-url', [TwilioController::class, 'waitUrl'])->name('waiturl');
    Route::post('twilio/gatherAction', [TwilioController::class, 'gatherAction']);
    Route::post('twilio/incoming', [TwilioController::class, 'incomingCall']);
    Route::post('twilio/outgoing', [TwilioController::class, 'outgoingCall']);
    Route::get('twilio/getLeadByNumber', [TwilioController::class, 'getLeadByNumber']);
    Route::post('twilio/recordingStatusCallback', [TwilioController::class, 'recordingStatusCallback']);
    Route::post('twilio/handleDialCallStatus', [TwilioController::class, 'handleDialCallStatus']);
    Route::post('twilio/handleOutgoingDialCallStatus', [TwilioController::class, 'handleOutgoingDialCallStatus']);
    Route::post('twilio/storerecording', [TwilioController::class, 'storeRecording']);
    Route::post('twilio/storetranscript', [TwilioController::class, 'storetranscript']);
    Route::post('twilio/eventsFromFront', [TwilioController::class, 'eventsFromFront']);
    Route::post('twilio/events', [TwilioController::class, 'twilioEvents']);
    Route::post('twilio/handleMessageDeliveryStatus/{cid}/{marketingMessageCId}', [TwilioController::class, 'handleMessageDeliveryStatus']);

    Route::any('twilio/twilio_menu_response', [TwilioController::class, 'twilio_menu_response'])->name('twilio_menu_response');
    Route::any('twilio/twilio_call_menu_response', [TwilioController::class, 'twilio_call_menu_response'])->name('twilio_call_menu_response');
    Route::post('twilio/twilio_order_status_and_information_on_call', [TwilioController::class, 'twilio_order_status_and_information_on_call'])->name('twilio_order_status_and_information_on_call');
    Route::post('twilio/twilio_return_refund_exchange_on_call', [TwilioController::class, 'twilio_return_refund_exchange_on_call'])->name('twilio_return_refund_exchange_on_call');

    Route::post('twilio/change_agent_status', [TwilioController::class, 'change_agent_status'])->name('change_agent_status');
    Route::post('twilio/change_agent_call_status', [TwilioController::class, 'change_agent_call_status'])->name('change_agent_call_status');
    Route::post('twilio/add_number', [TwilioController::class, 'addNumber'])->name('add_number');
    Route::post('twilio/update_number_status', [TwilioController::class, 'updateNumberStatus'])->name('update_number_status');
    Route::post('twilio/remove_waiting_call', [TwilioController::class, 'removeWaitingCalls'])->name('remove_waiting_calls');
    Route::post('twilio/get-waiting-call-list', [TwilioController::class, 'getWaitingCallList'])->name('waiting_calls_list');
    Route::post('twilio/leave_message_rec', [TwilioController::class, 'leave_message_rec'])->name('leave_message_rec');
    Route::any('twilio/completed', [TwilioController::class, 'completed'])->name('completed');
    Route::any('twilio/saverecording', [TwilioController::class, 'saveRecording'])->name('saveRecording');
    Route::post('twilio/update-reservation-status', [TwilioController::class, 'updateReservationStatus'])->name('update_reservation_status');

    Route::get('twilio/reject-call-twiml', [TwilioController::class, 'rejectIncomingCallTwiml'])->name('twilio.reject_call_twiml');
    Route::any('twilio/cancel-task-record', [TwilioController::class, 'canceldTaskRecord'])->name('twilio.cancel_task_record');
    Route::any('twilio/store-cancel-task-record', [TwilioController::class, 'storeCanceldTaskRecord'])->name('twilio.store_cancel_task_record');
    Route::any('twilio/store-complete-task-record', [TwilioController::class, 'storeCompleteTaskRecord'])->name('twilio.store_complete_task_record');

    Route::get(
        '/twilio/hangup',
        [TwilioController::class, 'showHangup']
    )->name('hangup');

    Route::post('twilio/handleIncomingCall', [TwilioController::class, 'handleIncomingCall'])->name('handleIncomingCall');

    Route::get('exotel/outgoing', [ExotelController::class, 'call'])->name('exotel.call');
    Route::get('exotel/checkNumber', [ExotelController::class, 'checkNumber']);
    Route::post('exotel/recordingCallback', [ExotelController::class, 'recordingCallback']);
});
Route::post('github/action-webhook', [Github\RepositoryController::class, 'actionWebhook'])->name('github.action.webhook');
/* ---------------------------------------------------------------------------------- */

/* ------------------Twilio functionality Routes[PLEASE DONT MOVE INTO MIDDLEWARE AUTH] ------------------------ */

Route::middleware([CheckPermission::class])->group(function () {

    Route::post('/zoom/webhook', [Meeting\ZoomMeetingController::class, 'webhook']);
    Route::get('/push-notificaiton', [WebNotificationController::class, 'index'])->name('push-notificaiton');
    Route::post('/store-token', [WebNotificationController::class, 'storeToken'])->name('store.token');
    Route::post('/send-web-notification', [WebNotificationController::class, 'sendWebNotification'])->name('send.web-notification');
    Route::get('/get-env-description', [EnvController::class, 'getDescription'])->name('get-env-description');

    Route::post('customer/add_customer_address', [CustomerController::class, 'add_customer_address']);

    // Sendgrid
    Route::prefix('sendgrid')->group(function () {
        Route::post('notifyurl', [Marketing\MailinglistController::class, 'notifyUrl']);
        Route::get('notifyurl', [Marketing\MailinglistController::class, 'notifyUrl']);
    });

    Route::get('send_auto_emails', [Marketing\MailinglistController::class, 'sendAutoEmails']);

    Route::get('textcurl', [Marketing\MailinglistController::class, 'textcurl']);
    // Totem
    Route::prefix('totem')->group(function () {
        Route::get('query-command/{name}', [TasksController::class, 'queryCommand']);
        Route::get('cron-history/{name}', [TasksController::class, 'cronHistory']);
    });

    // Test
    Route::prefix('test')->group(function () {
        Route::get('dummydata', [TestingController::class, 'testingFunction']);
        Route::get('translation', [GoogleTranslateController::class, 'testTranslation']);
        Route::get('testPrice', [TmpTaskController::class, 'testEmail']);
        Route::get('pushProduct', [TmpTaskController::class, 'testPushProduct']);
        Route::get('fixBrandPrice', [TmpTaskController::class, 'fixBrandPrice']);
        Route::get('deleteChatMessages', [TmpTaskController::class, 'deleteChatMessages']);
        Route::get('deleteProductImages', [TmpTaskController::class, 'deleteProductImages']);
        Route::get('deleteQueue', [TmpTaskController::class, 'deleteQueue']);
        Route::get('analytics', [AnalyticsController::class, 'cronShowData']);
        Route::get('analytics-user', [AnalyticsController::class, 'cronGetUserShowData'])->name('test.google.analytics');
        Route::get('dhl', [TmpTaskController::class, 'test']);
    });

    // Zabbix
    Route::prefix('zabbix')->group(function () {
        Route::get('/', [ZabbixController::class, 'index']);
        Route::get('problems', [ZabbixController::class, 'problems'])->name('zabbix.problem');
        Route::get('history', [ZabbixController::class, 'history'])->name('zabbix.history');
    });

    // Memory
    Route::prefix('memory')->group(function () {
        Route::get('/', [MemoryUsesController::class, 'index'])->name('memory.index');
        Route::post('thresold-update', [MemoryUsesController::class, 'updateThresoldLimit'])->name('update.thresold-limit');
    });

    Route::get('/store/unknown/sizes', [ScrapController::class, 'storeUnknownSizes']);
    Route::get('criteria/get/{id}', [PositionController::class, 'list'])->name('get.criteria');

    // Vendors
    Route::prefix('vendors')->group(function () {
        Route::get('create-cv/{id}', [VendorResumeController::class, 'create'])->name('vendors.create.cv');
        Route::post('cv/store', [VendorResumeController::class, 'store'])->name('vendor.cv.store');
        Route::get('create-cv', [VendorResumeController::class, 'create'])->name('vendor.create.cv');
        Route::post('cv/storeCVWithoutLogin', [VendorResumeController::class, 'storeCVWithoutLogin'])->name('vendor.storeCVWithoutLogin');
    });

    /** Magento Module */
    Route::post('auto-build-process', [ProjectController::class, 'pullRequestsBuildProcess']);

    Route::middleware('auth')->group(function () {
        Route::prefix('youtube')->group(function () {
            Route::get('add-chanel', [YoutubeController::class, 'createChanel'])->name('add.chanel');
            Route::get('get-refresh-token', [YoutubeController::class, 'getRefreshToken'])->name('youtubeaccount.get-refresh-token');
            Route::post('refresh-token', [YoutubeController::class, 'refreshToken'])->name('youtubeaccount.refresh_token');
            Route::post('add-chanel/create', [YoutubeController::class, 'storeChanel'])->name('youtubeaccount.createChanel');
            Route::get('edit/{id}', [YoutubeController::class, 'editChannel'])->name('youtubeaccount.editChannel');
            Route::get('video-upload/{id}', [YoutubeController::class, 'viewUploadVideo'])->name('youtubeaccount.viewUpload');
            Route::get('list-video/{id}', [YoutubeController::class, 'listVideo'])->name('youtubeaccount.listVideo');
            Route::get('redirect/{id}', [YoutubeController::class, 'youtubeRedirect'])->name('youtubeaccount.redirect');
            Route::post('channel/update', [YoutubeController::class, 'updateChannel'])->name('youtubeaccount.updateChannel');
            Route::post('video/upload', [YoutubeController::class, 'uploadVideo'])->name('youtubeaccount.uploadVideo');

            Route::get('video/post', [YoutubeController::class, 'postVideo'])->name('youtubeaccount.post');
            Route::get('comment-list/{videoId}', [YoutubeController::class, 'CommentByVideoId'])->name('commentList');
        });

        Route::prefix('blog')->group(function () {
            Route::get('list', [BlogController::class, 'index'])->name('blog.index');
            Route::post('blog-column-visbility', [BlogController::class, 'columnVisbilityUpdate'])->name('blog.column.update');
            Route::get('add', [BlogController::class, 'create'])->name('blog.create');
            Route::get('edit/{id}', [BlogController::class, 'edit'])->name('blog.edit');
            Route::post('store', [BlogController::class, 'store'])->name('store-blog.submit');
            Route::post('update/{id}', [BlogController::class, 'update'])->name('update-blog.submit');
            Route::delete('delete/{id}', [BlogController::class, 'destroy'])->name('update-blog.delete');
            Route::get('history/list', [BlogController::class, 'viewAllHistory'])->name('view-blog-all.history');
            Route::get('view/{id}', [BlogController::class, 'show'])->name('blog.view');
            Route::get('contentview/{id}', [BlogController::class, 'contentView'])->name('blog.contentView');
        });

        Route::prefix('zabbix')->group(function () {
            Route::get('users', [ZabbixUserController::class, 'index'])->name('zabbix.user.index');
            Route::post('user/save', [ZabbixUserController::class, 'save'])->name('zabbix.user.save');
            Route::get('items/{hostId?}', [ItemController::class, 'index'])->name('zabbix.item.index');
            Route::get('user/roles', [ZabbixUserController::class, 'roles'])->name('zabbix.user.roles');
            Route::post('user/role/save', [ZabbixUserController::class, 'rolesSave'])->name('zabbix.user.role.save');
            Route::get('triggers', [TriggerController::class, 'index'])->name('zabbix.trigger.index');
            Route::get('host/detail', [ZabbixController::class, 'detail'])->name('zabbix.host.detail');
            Route::delete('host/delete', [ZabbixController::class, 'delete'])->name('zabbix.host.delete');
            Route::post('host/save', [ZabbixController::class, 'save'])->name('zabbix.host.save');
            Route::post('triggers/save', [TriggerController::class, 'save'])->name('zabbix.trigger.save');
            Route::post('user/delete', [ZabbixUserController::class, 'delete'])->name('zabbix.user.delete');
            Route::post('item/delete', [ItemController::class, 'delete'])->name('zabbix.item.delete');
            Route::post('trigger/change_status', [TriggerController::class, 'changeStatus'])->name('zabbix.trigger.status');
            Route::post('item/save', [ItemController::class, 'save'])->name('zabbix.item.save');
        });

        Route::prefix('discount-sale-price')->group(function () {
            Route::get('/', [DiscountSalePriceController::class, 'index']);
            Route::delete('{id}', [DiscountSalePriceController::class, 'delete']);
            Route::get('type', [DiscountSalePriceController::class, 'type']);
            Route::post('create', [DiscountSalePriceController::class, 'create']);
        });

        Route::get('create-media-image', [CustomerController::class, 'testImage']);
        Route::get('generate-favicon', [HomeController::class, 'generateFavicon']);
        Route::get('logout-refresh', [HomeController::class, 'logoutRefresh'])->name('logout-refresh');

        Route::prefix('products')->group(function () {
            Route::get('affiliate', [ProductController::class, 'affiliateProducts']);
            Route::get('change-category', [ProductController::class, 'changeCategory']);
            Route::post('published', [ProductController::class, 'published']);
            Route::get('pushproductlist', [ProductController::class, 'pushproductlist']);
            Route::get('delete-out-of-stock-products', [ProductController::class, 'deleteOutOfStockProducts']);
        });

        Route::get('/customers/accounts', [CustomerController::class, 'accounts'])->name('customers.accounts');
        // Customer
        Route::prefix('customer')->group(function () {
            Route::post('update', [CustomerController::class, 'customerUpdate']);
            Route::get('update/history/{id}', [CustomerController::class, 'customerUpdateHistory']);
            Route::get('name', [CustomerController::class, 'customerName'])->name('customer.name.show');
        });

        Route::get('/productselection/list', [ProductSelectionController::class, 'sList'])->name('productselection.list');
        Route::get('/productsearcher/list', [ProductSearcherController::class, 'sList'])->name('productsearcher.list');
        Route::post('/productselection/email-set', [ProductSelectionController::class, 'emailTplSet'])->name('productselection.email.set');

        Route::get('/mageOrders', [MagentoController::class, 'get_magento_orders']);

        Route::post('magento-setting-updates', [MagentoController::class, 'magentoSettingUpdate']);

        // Message
        Route::prefix('message')->group(function () {
            Route::get('resend-to-bot', [MessageController::class, 'resendToBot'])->name('message.resendToBot');
            Route::get('/', [MessageController::class, 'index'])->name('message');
            Route::post('/', [MessageController::class, 'store'])->name('message.store');
            Route::post('{message}', [MessageController::class, 'update'])->name('message.update');
            Route::post('{id}/removeImage', [MessageController::class, 'removeImage'])->name('message.removeImage');
        });

        // Chat
        Route::prefix('chat')->group(function () {
            Route::get('getnew', [ChatController::class, 'checkfornew'])->name('checkfornew');
            Route::get('updatenew', [ChatController::class, 'updatefornew'])->name('updatefornew');
        });

        Route::get('users/check/logins', [UserController::class, 'checkUserLogins'])->name('users.check.logins');
        Route::resource('courier', CourierController::class);
        Route::resource('product-location', ProductLocationController::class);

        Route::get('/show-magento-cron-error-list', [Cron\ShowMagentoCronDataController::class, 'showMagentoCronErrorList'])->name('magento-cron-error-list');
        // Show Magento Cron Data
        Route::prefix('show-magento-cron-data')->group(function () {
            Route::get('/', [Cron\ShowMagentoCronDataController::class, 'MagentoCron'])->name('magento-cron-data');
            Route::post('run-magento-cron', [Cron\ShowMagentoCronDataController::class, 'runMagentoCron'])->name('magento-cron-runMagentoCron');
            Route::post('statuscolor', [Cron\ShowMagentoCronDataController::class, 'statusColor'])->name('magento-cron-data.statuscolor');
            Route::post('history', [Cron\ShowMagentoCronDataController::class, 'commandHistoryLog'])->name('magento-cron-commandHistoryLog');
        });

        Route::get('magento_modules_logs/{id}', [MagentoModuleController::class, 'magentoModuleListLogsDetails'])->name('magento_module_listing_logs_details');

        Route::prefix('magento_modules')->group(function () {
            Route::post('verified-status-update', [MagentoModuleController::class, 'verifiedStatusUpdate'])->name('magento_module.verified-status-update');
            Route::get('listing', [MagentoModuleController::class, 'magentoModuleList'])->name('magento_module_listing');
            Route::get('listing-careers', [MagentoCareersController::class, 'index'])->name('magento_module_listing_careers');
            Route::post('listing-careers/filter', [MagentoCareersController::class, 'getCareerByFilter'])->name('magento_module_listing_careers_by_filter');
            Route::post('listing-careers/getCareerRecord', [MagentoCareersController::class, 'getCareerRecord'])->name('fetchedCareerRecords');
            Route::post('listing-careers/create_or_edit', [MagentoCareersController::class, 'createOrEdit'])->name('magento_module_listing_careers_create');
            Route::get('listing_logs', [MagentoModuleController::class, 'magentoModuleListLogs'])->name('magento_module_listing_logs');
            Route::get('ajax-listing-logs', [MagentoModuleController::class, 'magentoModuleListLogsAjax'])->name('magento_modules.ajax-sync-logs');
            Route::get('get-api-value-histories/{magento_module}', [MagentoModuleController::class, 'getApiValueHistories'])->name('magento_module.get-api-value-histories');
            Route::get('get-m2-error-status-histories/{magento_module}', [MagentoModuleController::class, 'getM2ErrorStatusHistories'])->name('magento_module.get-m2-error-status-histories');
            Route::get('get-verified-status-histories/{magento_module}/{type}', [MagentoModuleController::class, 'getVerifiedStatusHistories'])->name('magento_module.get-verified-status-histories');
            Route::post('listingupdate-status', [MagentoModuleController::class, 'magentoModuleUpdateStatus'])->name('magentoModuleUpdateStatus');
            Route::post('sync-modules', [MagentoModuleController::class, 'syncModules'])->name('magento_module.sync-modules');
            Route::post('update-status/logs', [MagentoModuleController::class, 'magentoModuleUpdateStatuslogs'])->name('magentoModuleUpdateStatuslogs');
            Route::get('remark/{magento_module}/{type?}', [MagentoModuleController::class, 'getRemarks'])->name('magento_module_remark.get_remarks');
            Route::post('check-status', [MagentoModuleController::class, 'magentoModuleCheckStatus'])->name('magentoModuleCheckStatus');
            Route::post('remark', [MagentoModuleController::class, 'storeRemark'])->name('magento_module_remark.store');
            Route::get('m2-error-assignee-history', [MagentoModuleController::class, 'getM2ErrorAssigneeHistories'])->name('magento_module.m2-error-assignee-history');
            Route::post('dependency', [MagentoModuleController::class, 'storedependency'])->name('magento_module_dependency.store');
            Route::get('dependency/{id}', [MagentoModuleController::class, 'getDependencyRemarks'])->name('magento_module_dependency.remarks');
            Route::get('module-edit/{id}', [MagentoModuleController::class, 'moduleEdit'])->name('magento_module.module-edit');
            Route::post('index-post', [MagentoModuleController::class, 'indexPost'])->name('magento_module.index-post');
            Route::post('store-verified-status', [MagentoModuleController::class, 'storeVerifiedStatus'])->name('magento_modules.store-verified-status');
            Route::post('store-m2-error-status', [MagentoModuleController::class, 'storeM2ErrorStatus'])->name('magento_modules.store-m2-error-status');
            Route::post('M2remark', [MagentoModuleController::class, 'storeM2Remark'])->name('magento_module_m2_remark.store');
            Route::post('unit-test-status', [MagentoModuleController::class, 'storeUnitTestStatus'])->name('magento_modules.store-unit-test-status');
            Route::post('unit-testremark', [MagentoModuleController::class, 'storeUniTestRemark'])->name('magento_module_unit_test_remark.store');
        });

        Route::post('/updateOptions', [MagentoModuleController::class, 'updateMagentoModuleOptions'])->name('magento_module.update.option');
        Route::get('/verifiedby', [MagentoModuleController::class, 'verifiedByUser'])->name('magento_module.verified.User');
        Route::get('/reviewstandard', [MagentoModuleController::class, 'reviewStandardHistories'])->name('magento_module.review.standard.histories');

        Route::resource('magento_modules', MagentoModuleController::class);
        Route::get('/location', [MagentoModuleController::class, 'locationHistory'])->name('magento_module.location.history');
        Route::get('/description', [MagentoModuleController::class, 'descriptionHistory'])->name('magento_module.description.history');
        Route::get('/used_at', [MagentoModuleController::class, 'usedAtHistory'])->name('magento_module.usedat.history');
        Route::resource('magento_module_locations', MagentoLocationController::class);
        Route::get('/return_type_error/status', [MagentoModuleReturnTypeErrorStatusController::class, 'returnTypeHistory'])->name('magento_module.return_type.history');
        Route::resource('magento_module_return_types', MagentoModuleReturnTypeErrorStatusController::class);
        Route::resource('magento_module_categories', MagentoModuleCategoryController::class);
        Route::post('magento_module_api_histories', [MagentoModuleApiHistoryController::class, 'store'])->name('magento_module_api_histories.store');
        Route::get('magento_module_api_histories/{magento_module}', [MagentoModuleApiHistoryController::class, 'show'])->name('magento_module_api_histories.show');
        Route::post('magento_module_cron_job_histories', [MagentoModuleCronJobHistoryController::class, 'store'])->name('magento_module_cron_job_histories.store');
        Route::get('magento_module_cron_job_histories/{magento_module}', [MagentoModuleCronJobHistoryController::class, 'show'])->name('magento_module_cron_job_histories.show');
        Route::post('magento_module_js_require_histories', [MagentoModuleJsRequireHistoryController::class, 'store'])->name('magento_module_js_require_histories.store');
        Route::get('magento_module_js_require_histories/{magento_module}', [MagentoModuleJsRequireHistoryController::class, 'show'])->name('magento_module_js_require_histories.show');
        Route::post('magento_module_customized_histories', [MagentoModuleCustomizedHistoryController::class, 'store'])->name('magento_module_customized_histories.store');
        Route::get('magento_module_customized_histories/{magento_module}', [MagentoModuleCustomizedHistoryController::class, 'show'])->name('magento_module_customized_histories.show');

        Route::get('magento_module_histories/{magento_module}', [MagentoModuleHistoryController::class, 'show'])->name('magento_module_histories.show');
        Route::get('magento_module/unit-test-user-history', [MagentoModuleController::class, 'getUnitTestUserHistories'])->name('magento_module.unit-test-user-history');
        Route::get('magento_module/unit-test-remark-history', [MagentoModuleController::class, 'getUnitTestRemarkHistories'])->name('magento_module.unit-test-remark-history');
        Route::get('magento_module/unit-test-status-history', [MagentoModuleController::class, 'getUnitTestStatusHistories'])->name('magento_module.unit-status-history');
        Route::get('magento_module/unit-m2-remark-history', [MagentoModuleController::class, 'getM2RemarkHistories'])->name('magento_module.m2-error-remark-history');
        Route::post('magento_module/column-visbility', [MagentoModuleController::class, 'columnVisbilityUpdate'])->name('magento_module.column.update');
        Route::post('sync-logs-column-visbility', [MagentoModuleController::class, 'syncLogsColumnVisbilityUpdate'])->name('magento_module.sync.logs.column.update');
        Route::resource('magento_module_types', MagentoModuleTypeController::class);
        Route::resource('magento-setting-revision-history', MagentoSettingRevisionHistoryController::class);

        // Zabbix Webhook Data
        Route::prefix('zabbix-webhook-data')->group(function () {
            Route::get('remark/{zabbix_webhook_data}', [ZabbixWebhookDataController::class, 'getRemarks'])->name('zabbix-webhook-data.get_remarks');
            Route::get('issues-summary', [ZabbixWebhookDataController::class, 'issuesSummary'])->name('zabbix-webhook-data.issues.summary');
            Route::post('change-status', [ZabbixWebhookDataController::class, 'updateStatus'])->name('zabbix-webhook-data.change.status');
            Route::post('store-remark', [ZabbixWebhookDataController::class, 'storeRemark'])->name('zabbix-webhook-data.store.remark');
            Route::post('store-zabbix-status', [ZabbixWebhookDataController::class, 'storeZabbixStatus'])->name('zabbix-webhook-data.store-zabbix-status');
            Route::post('status-update', [ZabbixWebhookDataController::class, 'StatusColorUpdate'])->name('zabbix-webhook-data-color-update');
        });
        Route::resource('zabbix-webhook-data', ZabbixWebhookDataController::class);
        Route::get('zabbix-task/assignee-histories/{zabbix_task}', [ZabbixTaskController::class, 'getAssigneeHistories'])->name('zabbix-task.get-assignee-histories');
        Route::resource('zabbix-task', ZabbixTaskController::class);

        // Config Refactors
        Route::prefix('config-refactor')->group(function () {
            Route::get('remark/{config_refactor}/{column_name}', [ConfigRefactorController::class, 'getRemarks'])->name('config-refactor.get_remarks');
            Route::get('status/{config_refactor}/{column_name}', [ConfigRefactorController::class, 'getStatuses'])->name('config-refactor.get_status_histories');
            Route::get('user/{config_refactor}', [ConfigRefactorController::class, 'getUsers'])->name('config-refactor.get_users_histories');
            Route::post('store-remark', [ConfigRefactorController::class, 'storeRemark'])->name('config-refactor.store.remark');
            Route::post('change-status', [ConfigRefactorController::class, 'updateStatus'])->name('config-refactor.change.status');
            Route::post('change-user', [ConfigRefactorController::class, 'updateUser'])->name('config-refactor.change.user');
            Route::post('store-status', [ConfigRefactorController::class, 'storeStatus'])->name('config-refactor.store-status');
            Route::post('duplicate-create', [ConfigRefactorController::class, 'duplicateCreate'])->name('config-refactor.duplicate-create');
        });
        Route::resource('config-refactor', ConfigRefactorController::class);

        Route::resource('project-theme', ProjectThemeController::class);

        Route::prefix('theme-structure')->group(function () {
            Route::get('{id?}', [ThemeStructureController::class, 'index'])->name('theme-structure.index');
            Route::get('reload-tree/{id}', [ThemeStructureController::class, 'reloadTree']);
            Route::post('delete-item', [ThemeStructureController::class, 'deleteItem'])->name('theme-structure.delete-item');
            Route::post('/', [ThemeStructureController::class, 'store'])->name('theme-structure.store');
            Route::post('theme-file-store', [ThemeStructureController::class, 'themeFileStore'])->name('theme-structure.theme-file-store');
        });

        Route::prefix('project')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('project.index');
            Route::post('/', [ProjectController::class, 'store'])->name('project.store');
            Route::post('serverenv-store', [ProjectController::class, 'serverenvStore'])->name('project.serverenvStore');
            Route::post('project-type-store', [ProjectController::class, 'projectTypeStore'])->name('project.projectTypeStore');
            Route::post('buildProcess', [ProjectController::class, 'buildProcess'])->name('project.buildProcess');
            Route::post('pullRequests-buildProcess', [ProjectController::class, 'pullRequestsBuildProcess'])->name('project.pullRequests.buildProcess');
            Route::get('build-process-logs/{id?}', [ProjectController::class, 'buildProcessLogs'])->name('project.buildProcessLogs');
            Route::get('build-process-error-logs', [ProjectController::class, 'buildProcessErrorLogs'])->name('project.buildProcessErrorLogs');
            Route::get('build-process-status-logs', [ProjectController::class, 'buildProcessStatusLogs'])->name('project.buildProcessStatusLogs');
            Route::get('{id}', [ProjectController::class, 'edit'])->name('project.edit');
            Route::post('{id}', [ProjectController::class, 'update'])->name('project.update');
            Route::delete('{id}/destroy', [ProjectController::class, 'destroy'])->name('project.destroy');
            Route::post('multiple/buildProcess', [ProjectController::class, 'buildMultipleProcess'])->name('project.Multiple.buildProcess');
        });

        Route::get('get-github-repos', [ProjectController::class, 'getGithubRepos'])->name('project.getGithubRepo');
        Route::get('getGithubBranches', [ProjectController::class, 'getGithubBranches'])->name('project.getGithubBranches');

        /** Magento Frontend Page */
        Route::prefix('magento-frontend')->group(function () {
            Route::get('documentation', [MagentoFrontendDocumentationController::class, 'magentofrontenDocs'])->name('magento_frontend_listing');
            Route::post('store', [MagentoFrontendDocumentationController::class, 'magentofrontendStore'])->name('magento-frontend-store');
            Route::post('remark/store', [MagentoFrontendDocumentationController::class, 'magentofrontendstoreRemark'])->name('magento-frontend-remark-store');
            Route::get('remark', [MagentoFrontendDocumentationController::class, 'magentofrontendgetRemarks'])->name('magento-frontend-get-remarks');
            Route::get('location', [MagentoFrontendDocumentationController::class, 'magentoLocationget'])->name('magento-location-list');
            Route::get('admin', [MagentoFrontendDocumentationController::class, 'magentoAdminget'])->name('magento-admin-list');
            Route::get('frontend', [MagentoFrontendDocumentationController::class, 'magentoFrontend'])->name('magento-frontend-list');
            Route::get('edit/{id}', [MagentoFrontendDocumentationController::class, 'magentofrontendEdit'])->name('magento_frontend_edit');
            Route::post('updateOptions', [MagentoFrontendDocumentationController::class, 'magentofrontendOptions'])->name('magento_frontend.update.option');
            Route::post('update/{id}', [MagentoFrontendDocumentationController::class, 'magentofrontendUpdate'])->name('magento_frontend.update');
            Route::post('folder-store', [MagentoFrontendDocumentationController::class, 'magentofrontendStoreParentFolder'])->name('magento-frontend-parent-folder-store');
            Route::get('parent-folder/history', [MagentoFrontendDocumentationController::class, 'magentofrontendgetparentFolder'])->name('magento-frontend-get-parent-folder');
            Route::post('parent-folder/image-store', [MagentoFrontendDocumentationController::class, 'magentofrontendparentFolderImage'])->name('magento-frontend-parent-folder-image.store');
            Route::post('child-image-store', [MagentoFrontendDocumentationController::class, 'magentofrontendChildImage'])->name('magento-frontend-child-image-store');
            Route::post('child-folder', [MagentoFrontendDocumentationController::class, 'magentofrontendChildfolderstore'])->name('magento-frontend-child-folder-store');
            Route::get('child-folder/history', [MagentoFrontendDocumentationController::class, 'magentofrontendgetChildFolder'])->name('magento-frontend-get-child-folder-history');
            Route::delete('child-folder/{id}', [MagentoFrontendDocumentationController::class, 'magentofrontenddelete'])->name('magento-frontend.destroy');
            Route::get('files/record', [MagentoFrontendDocumentationController::class, 'frontnedUploadedFilesList'])->name('magento-frontend.files.record');
        });
        Route::get('magento-frontendhistories/{id}', [MagentoFrontendDocumentationController::class, 'magentofrontendhistoryShow'])->name('magentofrontend_histories.show');
        Route::get('magento-frontend-categoryhistories/{id}', [MagentoFrontendDocumentationController::class, 'magentofrontendCategoryHistoryShow'])->name('magentofrontend_category.histories.show');

        // Magento CSS Variable
        Route::prefix('magento-css-variable')->group(function () {
            Route::get('value-histories/{id}', [MagentoCssVariableController::class, 'valueHistories'])->name('magento-css-variable.value-histories');
            Route::get('verify-histories/{id}', [MagentoCssVariableController::class, 'verifyHistories'])->name('magento-css-variable.verify-histories');
            Route::get('job-logs/{id}', [MagentoCssVariableController::class, 'jobLogs'])->name('magento-css-variable.job-logs');
            Route::get('logs', [MagentoCssVariableController::class, 'logs'])->name('magento-css-variable.logs');
            Route::post('update-value', [MagentoCssVariableController::class, 'updateValue'])->name("'magento-css-variable.update-value");
            Route::post('update-selected-values', [MagentoCssVariableController::class, 'updateSelectedValues'])->name('magento-css-variable.update-selected-values');
            Route::post('update-values-for-project', [MagentoCssVariableController::class, 'updateValuesForProject'])->name('magento-css-variable.update-values-for-project');
            Route::post('verify/{id}', [MagentoCssVariableController::class, 'verify'])->name('magento-css-variable.verify');
            Route::get('download-csv/{id}', [MagentoCssVariableController::class, 'download'])->name('admin.download.file');
            Route::post('update-verified', [MagentoCssVariableController::class, 'updateSelectedVerified'])->name('magento-css-variable.update-verified');
            Route::post('sync', [MagentoCssVariableController::class, 'syncVariables'])->name('magento-css-variable.sync');
        });
        Route::resource('magento-css-variable', MagentoCssVariableController::class);
        Route::get('/get/backend-dropdown/list', [MagentoBackendDocumentationController::class, 'getBackendDropdownDatas'])->name('getBackendDropdownDatas');

        // Magento Backend
        Route::prefix('magento-backend')->group(function () {
            Route::get('documentation', [MagentoBackendDocumentationController::class, 'magentoBackendeDocs'])->name('magento.backend.listing');
            Route::post('store', [MagentoBackendDocumentationController::class, 'magentoBackendStore'])->name('magento-backend-store');
            Route::post('updateOptions', [MagentoBackendDocumentationController::class, 'magentoBackendOptions'])->name('magento-backend.update.option');
            Route::post('remark/store', [MagentoBackendDocumentationController::class, 'magentobackendstoreRemark'])->name('magento-backend-remark-store');
            Route::post('folder-store', [MagentoBackendDocumentationController::class, 'magentoStorebackendFolder'])->name('magento-backend-parent-folder-store');
            Route::post('description/upload', [MagentoBackendDocumentationController::class, 'magentoBackendDescriptionUpload'])->name('magento-backend-description-upload');
            Route::post('admin/upload', [MagentoBackendDocumentationController::class, 'magentoBackendadminConfigUpload'])->name('magento-backend-admin-upload');
            Route::delete('delete/{id}', [MagentoBackendDocumentationController::class, 'magentobackenddelete'])->name('magento-backend.destroy');
            Route::get('edit/{id}', [MagentoBackendDocumentationController::class, 'magentoBackendEdit'])->name('magento_backend_edit');
            Route::post('update/{id}', [MagentoBackendDocumentationController::class, 'magentoBackendUpdate'])->name('magento_backend.update');
            Route::get('feature', [MagentoBackendDocumentationController::class, 'magentoFeatureget'])->name('magento-feature-list');
            Route::get('template', [MagentoBackendDocumentationController::class, 'magentoTemplateget'])->name('magento-template-list');
            Route::get('details', [MagentoBackendDocumentationController::class, 'magentoBugDetailget'])->name('magento-bug-detail-list');
            Route::get('solutions', [MagentoBackendDocumentationController::class, 'magentoBugSolutionget'])->name('magento-bug-solution-list');
            Route::get('files/record', [MagentoBackendDocumentationController::class, 'getUploadedFilesList'])->name('magento-backend.files.record');
        });

        Route::get('magento-backend-category/histories', [MagentoBackendDocumentationController::class, 'magentoBackendCategoryHistoryShow'])->name('magentobackend_category.histories.show');
        Route::get('magento-backend-postman/histories', [MagentoBackendDocumentationController::class, 'magentoBackendPostmanHistoryShow'])->name('magentobackend_postman.histories.show');
        Route::get('magento-backend-module/histories', [MagentoBackendDocumentationController::class, 'magentoBackendModuleHistoryShow'])->name('magentobackend_module.histories.show');
        Route::get('magento-backend-remark/histories', [MagentoBackendDocumentationController::class, 'magentoBackendRemarkHistoryDataShow'])->name('magentobackend_remark.histories.show');
        Route::post('magento-backendadmin-config-store', [MagentoBackendDocumentationController::class, 'magentoStorebackendadminConfig'])->name('magento-backendadmin-config-store');
        Route::get('magento-backend-description/histories', [MagentoBackendDocumentationController::class, 'magentoBackenddescriptionHistoryShow'])->name('magentobackend_description.histories.show');

        /** redis Job Module */
        Route::get('redis-jobs', [RedisjobController::class, 'index'])->name('redis.jobs');
        Route::get('redis-jobs-list', [RedisjobController::class, 'listData'])->name('redis.jobs.list');
        Route::post('redis-jobs-add', [RedisjobController::class, 'store'])->name('redis.add_radis_job');
        Route::delete('redis-jobs-delete/{id?}', [RedisjobController::class, 'removeQue'])->name('redis.delete_radis_job');
        Route::post('redis-jobs-clearQue/{id?}', [RedisjobController::class, 'clearQue'])->name('redis.clear_que');
        Route::post('redis-jobs-restart_management/{id?}', [RedisjobController::class, 'restartManagement'])->name('redis.restart_management');

        /** SNS Alerts */
        Route::get('sns-alerts', [AlertController::class, 'index'])->name('snsAlerts.list');

        /** CSV Translator */
        Route::prefix('csv-translator')->group(function () {
            Route::get('/', [CsvTranslatorController::class, 'index'])->name('csvTranslator.list');
            Route::post('upload', [CsvTranslatorController::class, 'upload'])->name('csvTranslator.uploadFile');
            Route::post('update', [CsvTranslatorController::class, 'update'])->name('csvTranslator.update');
            Route::post('history', [CsvTranslatorController::class, 'history'])->name('csvTranslator.history');
            Route::post('approvedByAdmin', [CsvTranslatorController::class, 'approvedByAdmin'])->name('csvTranslator.filter');
            Route::post('permissions', [CsvTranslatorController::class, 'userPermissions'])->name('csvTranslator.permission');
        });
        Route::get('csv-filter', [CsvTranslatorController::class, 'filterCsvTranslator']);

        /** Magento Settings */
        Route::prefix('magento-admin-settings')->group(function () {
            Route::get('namehistrory/{id}', [MagentoSettingsController::class, 'namehistrory']);
            Route::get('/', [MagentoSettingsController::class, 'index'])->name('magento.setting.index');
            Route::get('logs', [MagentoSettingsController::class, 'getLogs'])->name('magento.setting.sync-logs');
            Route::get('pushLogs/{settingId}', [MagentoSettingsController::class, 'magentoPushLogs'])->name('magento.setting.logs');
            Route::post('create', [MagentoSettingsController::class, 'create'])->name('magento.setting.create');
            Route::post('update', [MagentoSettingsController::class, 'update'])->name('magento.setting.update');
            Route::get('get-magento-setting/{id}', [MagentoSettingsController::class, 'getMagentoSetting'])->name('magento.setting.get-magento-setting');
            Route::post('push-settings', [MagentoSettingsController::class, 'pushMagentoSettings'])->name('magento.setting.pushMagentoSettings');
            Route::post('push-row-magento-settings', [MagentoSettingsController::class, 'pushRowMagentoSettings'])->name('magento.setting.push-row-magento-settings');
            Route::post('statuscolor', [MagentoSettingsController::class, 'statusColor'])->name('magento.setting.statuscolor');
            Route::post('assign-setting', [MagentoSettingsController::class, 'assignSetting'])->name('magento.setting.assign-setting');
            Route::post('assign-individual-setting', [MagentoSettingsController::class, 'assignIndividualSetting'])->name('magento.setting.assign-individual-setting');
            Route::post('website/stores', [MagentoSettingsController::class, 'websiteStores'])->name('get.website.stores');
            Route::post('website/store/views', [MagentoSettingsController::class, 'websiteStoreViews'])->name('get.website.store.views');
            Route::get('delete/{id}', [MagentoSettingsController::class, 'deleteSetting'])->name('delete.setting');
            Route::get('value-histories/{id}', [MagentoSettingsController::class, 'magentoSettingvalueHistories'])->name('magento.setting.value.histories');
            Route::get('sync-admin-magento-settings', [MagentoSettingsController::class, 'syncAdminMagentoSettings'])->name('magento.setting.sync-admin-magento-settings');
            Route::get('check-sync-admin-magento-settings-process', [MagentoSettingsController::class, 'checkSyncAdminMagentoProcess'])->name('magento.setting.check-sync-admin-magento-settings-process');
            Route::get('sync-and-create-admin-magento-settings/{type}', [MagentoSettingsController::class, 'syncCreateAdminMagentoSettings'])->name('magento.setting.sync-and-create-admin-magento-settings');
            Route::get('check-sync-and-create-admin-magento-settings-process', [MagentoSettingsController::class, 'checkSyncCreateAdminMagentoSettings'])->name('magento.setting.check-sync-and-create-admin-magento-settings-process');
            Route::get('sync-cron-magento-settings', [MagentoSettingsController::class, 'syncCronMagentoSettings'])->name('magento.setting.sync-cron-magento-settings');
            Route::get('check-sync-cron-magento-settings-process', [MagentoSettingsController::class, 'checkSyncCronMagentoProcess'])->name('magento.setting.check-sync-cron-magento-settings-process');
        });

        Route::get('get-all/store-websites/{id}', [MagentoSettingsController::class, 'getAllStoreWebsites'])->name('get.all.store.websites');
        Route::get('magento-get-sync-data', [MagentoSettingsController::class, 'magentoSyncLogSearch'])->name('get.magento.sync.data');

        //Google Web Master Routes
        Route::prefix('googlewebmaster')->group(function () {
            Route::get('get-site-submit-hitory', [GoogleWebMasterController::class, 'getSiteSubmitHitory'])->name('googlewebmaster.get.history');
            Route::post('re-submit-site', [GoogleWebMasterController::class, 'ReSubmitSiteToWebmaster'])->name('googlewebmaster.re-submit.site.webmaster');
            Route::get('submit-site', [GoogleWebMasterController::class, 'SubmitSiteToWebmaster'])->name('googlewebmaster.submit.site.webmaster');
            Route::post('delete-site', [GoogleWebMasterController::class, 'deleteSiteFromWebmaster'])->name('googlewebmaster.delete.site.webmaster');
            Route::get('get-access-token', [GoogleWebMasterController::class, 'googleLogin'])->name('googlewebmaster.get-access-token');
            Route::get('/index', [GoogleWebMasterController::class, 'index'])->name('googlewebmaster.index');

            Route::get('update/sites/data', [GoogleWebMasterController::class, 'updateSitesData'])->name('update.sites.data');
            Route::get('/get-accounts', [GoogleWebMasterController::class, 'getAccounts'])->name('googlewebmaster.get.accounts');
            Route::post('/add-account', [GoogleWebMasterController::class, 'addAccount'])->name('googlewebmaster.account.add');
            Route::get('/accounts/connect/{id}', [GoogleWebMasterController::class, 'connectAccount'])->name('googlewebmaster.account.connect');
            Route::get('/accounts/disconnect/{id}', [GoogleWebMasterController::class, 'disconnectAccount'])->name('googlewebmaster.account.disconnect');
            Route::get('/get-account-notifications', [GoogleWebMasterController::class, 'getAccountNotifications'])->name('googlewebmaster.get.account.notifications');
            Route::get('/all-records', [GoogleWebMasterController::class, 'allRecords'])->name('googlewebmaster.get.records');
        });

        //Bing webmaster routes
        Route::prefix('bing-webmaster')->group(function () {
            Route::get('/index', [BingWebMasterController::class, 'index'])->name('bingwebmaster.index');
            Route::post('/add-account', [BingWebMasterController::class, 'addAccount'])->name('bingwebmaster.account.add');
            Route::get('/get-accounts', [BingWebMasterController::class, 'getAccounts'])->name('bingwebmaster.get.accounts');
            Route::get('/accounts/connect/{id}', [BingWebMasterController::class, 'connectAccount'])->name('bingwebmaster.account.connect');
            Route::get('get-access-token', [BingWebMasterController::class, 'bingLogin'])->name('bingwebmaster.get-access-token');
            Route::get('/accounts/disconnect/{id}', [BingWebMasterController::class, 'disconnectAccount'])->name('bingwebmaster.account.disconnect');
            Route::get('/all-records', [BingWebMasterController::class, 'allRecords'])->name('bingwebmaster.get.records');
            Route::post('delete-site', [BingWebMasterController::class, 'deleteSiteFromWebmaster'])->name('bingwebmaster.delete.site.webmaster');
        });

        //Google Billing Routes
        Route::prefix('google-billing')->group(function () {
            Route::get('/index', [GoogleBillingsController::class, 'index'])->name('google.billing.index');
            Route::post('/store', [GoogleBillingsController::class, 'store'])->name('google.billing.add');
            Route::post('/update/{id}', [GoogleBillingsController::class, 'update'])->name('google.billing.update');
            Route::get('/project-list/{id?}', [GoogleBillingsController::class, 'projectList'])->name('google.billing.project.list');
            Route::get('/detail/{id}', [GoogleBillingsController::class, 'detail'])->name('google.billing.detail');
            Route::get('/delete/{id}', [GoogleBillingsController::class, 'delete'])->name('google.billing.delete');
            Route::get('/get/{id}', [GoogleBillingsController::class, 'get'])->name('google.billing.get');
            Route::post('/store-project', [GoogleBillingsController::class, 'storeProject'])->name('google.billing.add.project');
            Route::post('/update-project/{id}', [GoogleBillingsController::class, 'updateProject'])->name('google.billing.update.project');
            Route::get('/get-project/{id}', [GoogleBillingsController::class, 'getProject'])->name('google.billing.get.project');

        });

        Route::prefix('product')->group(function () {
            Route::get('manual-crop/assign-products', [Products\ManualCroppingController::class, 'assignProductsToUser']);
            Route::resource('manual-crop', Products\ManualCroppingController::class);
            Route::get('hscode', [ProductController::class, 'hsCodeIndex'])->name('product.hscode');
            Route::post('hscode/save-group', [ProductController::class, 'saveGroupHsCode'])->name('hscode.save.group');
            Route::post('hscode/edit-group', [ProductController::class, 'editGroup'])->name('hscode.edit.group');
            Route::post('store-website-description', [ProductController::class, 'storeWebsiteDescription'])->name('product.store.website.description');
            Route::post('test', [ProductController::class, 'test'])->name('product.test.template');
        });

        Route::prefix('logging')->group(function () {
            Route::delete('list-api-logs-delete', [LaravelLogController::class, 'listApiLogsDelete'])->name('list-api-logs-delete');
            Route::any('list/api/logs', [LaravelLogController::class, 'apiLogs'])->name('api-log-list');
            Route::any('list/api/logs/generate-report', [LaravelLogController::class, 'generateReport'])->name('api-log-list-generate-report');
            Route::post('list-magento/product-push-update-infomation', [Logging\LogListMagentoController::class, 'updateProductPushInformation'])->name('update.magento.product-push-information');
            Route::get('list-magento/product-push-update-infomation/summery', [Logging\LogListMagentoController::class, 'updateProductPushInformationSummery'])->name('update.magento.product-push-information-summery');
            Route::post('list-magento/product-push-update-website', [Logging\LogListMagentoController::class, 'updateProductPushWebsite'])->name('update.magento.product-push-website');
            Route::get('list-magento/export', [Logging\LogListMagentoController::class, 'export'])->name('list.magento.logging.export');
            Route::post('list-magento-column-visbility', [Logging\LogListMagentoController::class, 'listmagentoColumnVisbilityUpdate'])->name('list.magento.column.update');
            Route::get('list-magento', [Logging\LogListMagentoController::class, 'index'])->name('list.magento.logging');
            Route::get('list-magento-filter', [Logging\LogListMagentoController::class, 'filterOptions'])->name('list.magento.logging.filterOption');
            Route::get('list-magento/error-reporting', [Logging\LogListMagentoController::class, 'errorReporting'])->name('list.magento.error-reporting');
            Route::get('list-magento/product-information', [Logging\LogListMagentoController::class, 'productInformation'])->name('list.magento.product-information');
            Route::get('list-magento/retry-failed-job', [Logging\LogListMagentoController::class, 'retryFailedJob'])->name('list.magento.retry-failed-job');
            Route::get('list-magento/send-live-product-check', [Logging\LogListMagentoController::class, 'sendLiveProductCheck'])->name('list.magento.send-live-product-check');
            Route::get('list-magento/get-live-product-screenshot', [Logging\LogListMagentoController::class, 'getLiveScreenshot'])->name('list.magento.get-live-screenshot');
            Route::post('list-magento/sync-status-color', [Logging\LogListMagentoController::class, 'syncStatusColor'])->name('list.magento.sync-status-color');
            Route::post('list-magento/{id}', [Logging\LogListMagentoController::class, 'updateMagentoStatus']);
            Route::get('show-error-logs/{product_id}/{website_id?}', [Logging\LogListMagentoController::class, 'showErrorLogs'])->name('list.magento.show-error-logs');
            Route::get('call-journey-by-id/{id}', [Logging\LogListMagentoController::class, 'showJourneyById'])->name('list.magento.show-journey-by-id');
            Route::get('call-journey-horizontal-by-id/{id}', [Logging\LogListMagentoController::class, 'showJourneyHorizontalById'])->name('list.magento.show-journey-horizontal-by-id');
            Route::get('show-error-log-by-id/{id}', [Logging\LogListMagentoController::class, 'showErrorByLogId'])->name('list.magento.show-error-log-by-id');
            Route::get('show-product-push-log/{id}', [Logging\LogListMagentoController::class, 'showProductPushLog'])->name('list.magento.show-product-push-log');
            Route::get('show-prices/{id}', [Logging\LogListMagentoController::class, 'showPrices'])->name('list.magento.show-prices');
            Route::get('list-magento/product-push-infomation', [Logging\LogListMagentoController::class, 'productPushInformation'])->name('list.magento.product-push-information');
            Route::post('list-magento/product-push-histories/{product_id}', [Logging\LogListMagentoController::class, 'productPushHistories'])->name('list.magento.product-push-information-byid');
            Route::get('list-magento/daily-push-log', [Logging\LogListMagentoController::class, 'dailyPushLog'])->name('list.daily-push-log');
            Route::get('list-laravel-logs', [LaravelLogController::class, 'index'])->name('logging.laravel.log');
            Route::get('live-laravel-logs', [LaravelLogController::class, 'liveLogs'])->name('logging.live.logs');
            Route::get('live-laravel-logs-summary', [LaravelLogController::class, 'liveLogsSummary'])->name('logging.live.logs-summary');
            Route::get('live-laravel-logs-single', [LaravelLogController::class, 'liveLogsSingle']);

            Route::get('flow-logs', [FlowLogController::class, 'index'])->name('logging.flow.log');
            Route::get('flow-logs-detail', [FlowLogController::class, 'details'])->name('logging.flow.detail');

            Route::get('keyword-create', [LaravelLogController::class, 'LogKeyword']);
            Route::get('keyword-delete', [LaravelLogController::class, 'LogKeywordDelete']);
            Route::post('assign', [LaravelLogController::class, 'assign'])->name('logging.assign');
            Route::get('sku-logs', [Logging\LogScraperController::class, 'logSKU'])->name('logging.scrap.log');
            Route::get('sku-logs-errors', [Logging\LogScraperController::class, 'logSKUErrors'])->name('logging.sku.errors.log');
            Route::get('list-visitor-logs', [VisitorController::class, 'index'])->name('logging.visitor.log');
            Route::get('live-scraper-logs', [LaravelLogController::class, 'scraperLiveLogs'])->name('logging.live.scraper-logs');
            Route::get('live-laravel-logs/downloads', [LaravelLogController::class, 'liveLogDownloads'])->name('logging.live.downloads');
            Route::get('live-magento-logs/downloads', [LaravelLogController::class, 'liveMagentoDownloads'])->name('logging.live.magento.downloads');
            Route::get('magento-product-api-call', [Logging\LogListMagentoController::class, 'showMagentoProductAPICall'])->name('logging.magento.product.api.call');
            Route::post('magento-product-skus-ajax', [Logging\LogListMagentoController::class, 'getMagentoProductAPIAjaxCall'])->name('logging.magento.product.api.ajax.call');
            Route::get('get-latest-product-for-push', [Logging\LogListMagentoController::class, 'getLatestProductForPush'])->name('logging.magento.get-latest-product-for-push');
            Route::post('delete/magento-api-search-history', [Logging\LogListMagentoController::class, 'deleteMagentoApiData'])->name('delete.magento.api-search-history');
            Route::get('log-magento-apis-ajax', [Logging\LogListMagentoController::class, 'logMagentoApisAjax'])->name('logging.magento.logMagentoApisAjax');
            Route::get('log-magento-product-push-journey', [Logging\LogListMagentoController::class, 'productPushJourney'])->name('logging.magento.product_push_journey');
            Route::post('log-magento-column-visbility', [Logging\LogListMagentoController::class, 'columnVisbilityUpdate'])->name('logging.magento.column.update');
        });

        Route::get('log-scraper-api', [Logging\LogScraperController::class, 'scraperApiLog'])->name('log-scraper.api');
        Route::get('log-scraper', [Logging\LogScraperController::class, 'index'])->name('log-scraper.index');

        Route::prefix('category-messages')->group(function () {
            Route::post('bulk-messages/addToDND', [BulkCustomerRepliesController::class, 'addToDND']);
            Route::post('bulk-messages/removeFromDND', [BulkCustomerRepliesController::class, 'removeFromDND']);
            Route::post('bulk-messages/keyword', [BulkCustomerRepliesController::class, 'storeKeyword'])->name('bulk-messages.store-keyword');
            Route::post('bulk-messages/keyword/update-whatsappno', [BulkCustomerRepliesController::class, 'updateWhatsappNo'])->name('bulk-messages.whatsapp-no');
            Route::post('bulk-messages/send-message', [BulkCustomerRepliesController::class, 'sendMessagesByKeyword'])->name('bulk-messages.send-messages-by-keyword');
            Route::resource('bulk-messages', BulkCustomerRepliesController::class);
            Route::resource('keyword', KeywordToCategoryController::class, ['as' => 'categorykeyword'])->except(['create']);
            Route::resource('category', CustomerCategoryController::class, ['as' => 'customercategorykeyword'])->except(['create', 'show']);
        });

        Route::prefix('seo')->group(function () {
            Route::get('/', [SeoToolController::class, 'index'])->name('seo-tool');
            Route::get('/search', [SeoToolController::class, 'searchSeoFilter'])->name('seo-tool-search');
            Route::post('tool/save', [SeoToolController::class, 'saveTool'])->name('save.seo-tool');
            Route::get('fetch-details', [SeoToolController::class, 'fetchDetails'])->name('fetch-seo-details');
            Route::get('domain-report/{id}/{type?}/{viewId?}/{viewTypeName?}', [DetailsController::class, 'domainDetails'])->name('domain-details');
            Route::post('domain-report/search/{id?}/{type?}/{viewId?}/{viewTypeName?}', [DetailsController::class, 'domainDetailsSearch'])->name('domain-details-search');
            Route::get('domain-report/{id}/{type}', [DetailsController::class, 'domainDetails']);
            Route::get('compitetors-details/{id}', [SeoToolController::class, 'compitetorsDetails'])->name('compitetors-details');
            Route::get('site-audit-details/{id}/{viewId?}/{viewTypeName?}', [DetailsController::class, 'siteAudit'])->name('site-audit-details');
            Route::get('compitetorsdetails/{id}', [DetailsController::class, 'compitetorsDetails'])->name('compitetorsdetails');
            Route::get('backlink-details/{id}/{viewId?}/{viewTypeName?}', [DetailsController::class, 'backlinkDetails'])->name('backlink-details');
            Route::post('backlink-details/search/{id}/{viewId?}/{viewTypeName?}', [DetailsController::class, 'backlinkDetailsSearch'])->name('backlink-details-search');
            Route::get('site-audit/{projectId}', [SeoToolController::class, 'siteAudit']);
            Route::post('site-audit/search/{projectId}/{viewId?}/{viewTypeName?}', [DetailsController::class, 'siteAuditSearch']);
            Route::get('project-list', [SeoToolController::class, 'projectList']);
            Route::post('save-keyword', [SeoToolController::class, 'saveKeyword']);
        });

        /**
         * This route will push the FAQ to series of website with help of API
         */
        Route::post('push/faq', [FaqPushController::class, 'pushFaq']);
        Route::post('push/faq/all', [FaqPushController::class, 'pushFaqAll']);
        Route::post('push/faq/mulitiple', [FaqPushController::class, 'mulitiplepushFaq']);

        // Hubstaff
        Route::prefix('hubstaff')->group(function () {
            Route::get('members', [HubstaffController::class, 'index'])->name('hubstaff.members.index');
            Route::post('members/{id}/save-field', [HubstaffController::class, 'saveMemberField']);
            Route::post('refresh_users', [HubstaffController::class, 'refreshUsers']);
            Route::post('linkuser', [HubstaffController::class, 'linkUser']);
            Route::get('projects', [HubstaffController::class, 'getProjects']);
            Route::post('projects/create', [HubstaffController::class, 'createProject']);
            Route::get('projects/{id}', [HubstaffController::class, 'editProject']);
            Route::put('projects/edit', [HubstaffController::class, 'editProjectData']);
            Route::get('tasks', [HubstaffController::class, 'getTasks']);
            Route::get('tasks/add', [HubstaffController::class, 'addTaskFrom']);
            Route::put('tasks/editData', [HubstaffController::class, 'editTask']);
            Route::post('tasks/addData', [HubstaffController::class, 'addTask']);
            Route::get('tasks/{id}', [HubstaffController::class, 'editTaskForm']);
            Route::get('redirect', [HubstaffController::class, 'redirect']);
            Route::get('debug', [HubstaffController::class, 'debug']);
            Route::get('payments', [UserController::class, 'payments']);
            Route::post('makePayment', [UserController::class, 'makePayment']);
            Route::get('userlist', [HubstaffController::class, 'userList'])->name('hubstaff.userList');
        });

        // Time Doctor
        Route::prefix('time-doctor')->group(function () {
            Route::get('projects', [TimeDoctorController::class, 'getProjects'])->name('time-doctor.projects');
            Route::get('tasks', [TimeDoctorController::class, 'getTasks'])->name('time-doctor.tasks');
            Route::get('members', [TimeDoctorController::class, 'userList'])->name('time-doctor.members');
            Route::post('link_time_doctor_user', [TimeDoctorController::class, 'linkUser']);
            Route::post('saveuseraccount', [TimeDoctorController::class, 'saveUserAccount'])->name('time-doctor.adduser');
            Route::post('get_auth_token', [TimeDoctorController::class, 'getAuthTokens'])->name('time-doctor.getToken');
            Route::post('display-user-account', [TimeDoctorController::class, 'displayUserAccountList'])->name('time-doctor.display-user');
            Route::post('refresh_users_by_id', [TimeDoctorController::class, 'refreshUsersById'])->name('time-doctor.refresh-user-by-id');
            Route::post('refresh_project_by_id', [TimeDoctorController::class, 'refreshProjectsById'])->name('time-doctor.refresh-project-by-id');
            Route::post('saveproject', [TimeDoctorController::class, 'saveProject'])->name('time-doctor.addproject');
            Route::post('get_project_by_id', [TimeDoctorController::class, 'getProjectsById'])->name('time-doctor.get-project-detail');
            Route::post('update_project_by_id', [TimeDoctorController::class, 'updateProjectById'])->name('time-doctor.update-program-by-id');
            Route::post('savetask', [TimeDoctorController::class, 'saveTask'])->name('time-doctor.addtask');
            Route::post('refresh_task_by_id', [TimeDoctorController::class, 'refreshTasksById'])->name('time-doctor.refresh-task-by-id');
            Route::post('get_task_by_id', [TimeDoctorController::class, 'getTasksById'])->name('time-doctor.get-task-detail');
            Route::post('update_task_by_id', [TimeDoctorController::class, 'updateTasksById'])->name('time-doctor.update-task-by-id');
            Route::get('create-account', [TimeDoctorController::class, 'sendInvitations'])->name('time-doctor.create-account');
            Route::post('send_invitation', [TimeDoctorController::class, 'sendSingleInvitation'])->name('time-doctor.send-invitation');
            Route::post('send_bulk_invitation', [TimeDoctorController::class, 'sendBulkInvitation'])->name('time-doctor.send-bulk-invitation');
            Route::get('list-user-account', [TimeDoctorController::class, 'listUserAccountList'])->name('time-doctor.list-user');
            Route::Post('remark-user-account/store', [TimeDoctorController::class, 'listRemarkStore'])->name('time-doctor.remark.store');
            Route::Post('remark-user-account/list', [TimeDoctorController::class, 'getRemarkStore'])->name('time-doctor.remark.get');
            Route::post('validate', [TimeDoctorController::class, 'updateValidate'])->name('time-doctor.updateValidate');
            Route::Post('due-date/list', [TimeDoctorController::class, 'getduedateHistory'])->name('time-doctor.due-date-history.get');
        });

        Route::get('timer/get-timer-alerts', [TimeDoctorController::class, 'getTimerAlerts'])->name('get.timer.alerts');

        Route::prefix('time-doctor/task-creation-logs')->group(function () {
            Route::get('/', [TimeDoctorController::class, 'taskCreationLogs'])->name('time-doctor.task_creation_logs');
            Route::get('/records', [TimeDoctorController::class, 'listTaskCreationLogs'])->name('time-doctor.task_creation_logs.records');
        });

        Route::prefix('time-doctor-activities')->group(function () {
            Route::get('/report', [TimeDoctorActivitiesController::class, 'activityReport'])->name('time-doctor-activtity.report');
            Route::get('/report-download', [TimeDoctorActivitiesController::class, 'activityReportDownload'])->name('time-doctor-activity-report.download');
            Route::get('/payment_data', [TimeDoctorActivitiesController::class, 'activityPaymentData'])->name('time-doctor-activity.payment_data');
            Route::post('/command_execution_manually', [TimeDoctorActivitiesController::class, 'timeDoctorActivityCommandExecution'])->name('time-doctor-activity.command_execution_manually');
            Route::get('/time-doctor-payment-download', [TimeDoctorActivitiesController::class, 'timeDoctorPaymentReportDownload'])->name('time-doctor-payment-report.download');
            Route::get('/addtocashflow', [TimeDoctorActivitiesController::class, 'addtocashflow']);
            Route::post('/account_wise_time_track', [TimeDoctorActivitiesController::class, 'timeDoctorTaskTrackDetails'])->name('time-doctor-activity.account_wise_time_track');
            Route::prefix('notification')->group(function () {
                Route::get('/', [TimeDoctorActivitiesController::class, 'notification'])->name('time-doctor-acitivties.notification.index');
                Route::post('/download', [TimeDoctorActivitiesController::class, 'downloadNotification'])->name('time-doctor-acitivties.notification.download');
                Route::get('/records', [TimeDoctorActivitiesController::class, 'notificationRecords'])->name('time-doctor-acitivties.notification.records');
                Route::post('/save', [TimeDoctorActivitiesController::class, 'notificationReasonSave'])->name('time-doctor-acitivties.notification.save-reason');
                Route::post('/change-status', [TimeDoctorActivitiesController::class, 'changeStatus'])->name('time-doctor-acitivties.notification.change-status');
            });
            Route::prefix('activities')->group(function () {
                Route::get('/', [TimeDoctorActivitiesController::class, 'getActivityUsers'])->name('time-doctor-acitivties.activities');
                Route::get('/details', [TimeDoctorActivitiesController::class, 'getActivityDetails'])->name('time-doctor-acitivties.activity-details');
                Route::post('/details', [TimeDoctorActivitiesController::class, 'approveActivity'])->name('time-doctor-acitivties.approve-activity');
                Route::post('/final-submit', [TimeDoctorActivitiesController::class, 'finalSubmit'])->name('time-doctor-acitivties/activities/final-submit');
                Route::post('/task-notes', [TimeDoctorActivitiesController::class, 'NotesHistory'])->name('time-doctor-acitivties.task.notes');
                Route::get('/save-notes', [TimeDoctorActivitiesController::class, 'saveNotes'])->name('time-doctor-acitivties.task.save.notes');
                Route::get('/approve-all-time', [TimeDoctorActivitiesController::class, 'approveTime'])->name('time-doctor-acitivties.approve.time');
                Route::post('/fetch', [TimeDoctorActivitiesController::class, 'fetchActivitiesFromTimeDoctor'])->name('time-doctor-acitivties/activities/fetch');
                Route::post('/manual-record', [TimeDoctorActivitiesController::class, 'submitManualRecords'])->name('time-doctor-acitivties.manual-record');
                Route::get('/records', [TimeDoctorActivitiesController::class, 'notificationRecords'])->name('time-doctor.notification.records');
                Route::post('/save', [TimeDoctorActivitiesController::class, 'notificationReasonSave'])->name('time-doctor.notification.save-reason');
                Route::post('/change-status', [TimeDoctorActivitiesController::class, 'changeStatus'])->name('time-doctor.notification.change-status');
                Route::get('/approved/pending-payments', [TimeDoctorActivitiesController::class, 'approvedPendingPayments'])->name('time-doctor-acitivties.pending-payments');
                Route::post('/approved/payment', [TimeDoctorActivitiesController::class, 'submitPaymentRequest'])->name('time-doctor-acitivties.payment-request.submit');
                Route::post('/add-efficiency', [TimeDoctorActivitiesController::class, 'AddEfficiency'])->name('time-doctor-acitivties.efficiency.save');
                Route::get('/task-activity', [TimeDoctorActivitiesController::class, 'taskActivity'])->name('time-doctor-acitivties.acitivties.task-activity');
                Route::get('/userTrackTime', [TimeDoctorActivitiesController::class, 'userTreckTime'])->name('time-doctor-acitivties.acitivties.userTreckTime');
            });
        });

        Route::middleware([HttpClientTimeout::class])->group(function () {
            Route::post('global_files_and_attachments_store', [FilesAndAttachmentsController::class, 'store_data'])->name('global_files_and_attachments_store');
            Route::post('global_files_and_attachments', [FilesAndAttachmentsController::class, 'get_data'])->name('global_files_and_attachments');
            Route::get('global_files_and_attachments_download/{filename}', [FilesAndAttachmentsController::class, 'download'])->name('global_files_and_attachments_download');

            // MV: Commenting this line as that is already added outside of this group
            Route::prefix('postman')->group(function () {
                Route::get('/', [PostmanRequestCreateController::class, 'index']);
                Route::get('search', [PostmanRequestCreateController::class, 'search']);
                Route::post('/create', [PostmanRequestCreateController::class, 'store']);
                Route::post('/edit', [PostmanRequestCreateController::class, 'edit']);
                Route::delete('delete', [PostmanRequestCreateController::class, 'destroy']);
                Route::get('addstorewebsiteurlinflutterpostman', [PostmanRequestCreateController::class, 'addStoreWebsiteUrlInFlutterPostman']);
                Route::get('folder', [PostmanRequestCreateController::class, 'folderindex']);
                Route::get('workspace', [PostmanRequestCreateController::class, 'workspaceIndex']);
                Route::get('collection', [PostmanRequestCreateController::class, 'collectionIndex']);
                Route::get('folder/search', [PostmanRequestCreateController::class, 'folderSearch']);
                Route::post('folder/create', [PostmanRequestCreateController::class, 'folderStore']);
                Route::post('workspace/create', [PostmanRequestCreateController::class, 'workspaceStore']);
                Route::post('collection/create', [PostmanRequestCreateController::class, 'collectionStore']);
                Route::post('/folder/edit', [PostmanRequestCreateController::class, 'folderEdit']);
                Route::post('/workspace/edit', [PostmanRequestCreateController::class, 'workspaceEdit']);
                Route::post('/collection/edit', [PostmanRequestCreateController::class, 'collectionEdit']);
                Route::delete('folder/delete', [PostmanRequestCreateController::class, 'folderDestroy']);
                Route::delete('workspace/delete', [PostmanRequestCreateController::class, 'workspaceDestroy']);
                Route::post('history', [PostmanRequestCreateController::class, 'postmanHistoryLog']);
                Route::post('collection/folders', [PostmanRequestCreateController::class, 'getCollectionFolders']);
                Route::post('collection/folder/upsert', [PostmanRequestCreateController::class, 'upsertCollectionFolder']);
                Route::post('collection/folder/delete', [PostmanRequestCreateController::class, 'deleteCollectionFolder']);
                Route::get('call/workspace', [PostmanRequestCreateController::class, 'getPostmanWorkSpaceAPI']);
                Route::get('call/collection', [PostmanRequestCreateController::class, 'getAllPostmanCollectionApi']);
                Route::get('create/collection', [PostmanRequestCreateController::class, 'createPostmanCollectionAPI']);
                Route::get('update/collection', [PostmanRequestCreateController::class, 'updatePostmanCollectionAPI']);
                Route::get('get/collection', [PostmanRequestCreateController::class, 'getPostmanCollectionAndCreateAPI']);
                Route::get('create/folder', [PostmanRequestCreateController::class, 'createPostmanFolder']);
                Route::get('create/request', [PostmanRequestCreateController::class, 'createPostmanRequestAPI']);
                Route::post('send/request', [PostmanRequestCreateController::class, 'sendPostmanRequestAPI']);
                Route::post('requested/history', [PostmanRequestCreateController::class, 'postmanRequestHistoryLog']);
                Route::get('request/history', [PostmanRequestCreateController::class, 'index_request_hisory']);
                Route::post('response/history', [PostmanRequestCreateController::class, 'postmanResponseHistoryLog']);
                Route::get('response/history', [PostmanRequestCreateController::class, 'index_response_hisory']);
                Route::post('add/json/version', [PostmanRequestCreateController::class, 'jsonVersion']);
                Route::post('removeuser/permission', [PostmanRequestCreateController::class, 'removeUserPermission']);
                Route::post('remark/history', [PostmanRequestCreateController::class, 'postmanRemarkHistoryLog']);
                Route::post('user/permission', [PostmanRequestCreateController::class, 'userPermission'])->name('postman.permission');
                Route::post('get/mul/request', [PostmanRequestCreateController::class, 'getMulRequest']);
                Route::post('get/error/history', [PostmanRequestCreateController::class, 'postmanErrorHistoryLog']);
                Route::post('edit/history/', [PostmanRequestCreateController::class, 'postmanEditHistoryLog']);
                Route::post('status/create', [PostmanRequestCreateController::class, 'postmanStatusCreate'])->name('postman.status.create');
                Route::post('update-status', [PostmanRequestCreateController::class, 'updateStatus'])->name('update-status');
                Route::post('update-api-issue-fix-done', [PostmanRequestCreateController::class, 'updateApiIssueFixDone'])->name('update-api-issue-fix-done');
                Route::get('status/histories/{id}', [PostmanRequestCreateController::class, 'postmanStatusHistories'])->name('postman.status.histories');
                Route::get('api-issue-fix-done/histories/{id}', [PostmanRequestCreateController::class, 'postmanApiIssueFixDoneHistories'])->name('postman.api-issue-fix-done.histories');
                Route::post('statuscolor', [PostmanRequestCreateController::class, 'statuscolor'])->name('postman.statuscolor');
                Route::get('countdevtask/{id}', [PostmanRequestCreateController::class, 'taskCount']);
                Route::post('update-postman-field', [PostmanRequestCreateController::class, 'updatePostManField'])->name('postman.updateField');
                Route::post('add-remark', [PostmanRequestCreateController::class, 'addRemark'])->name('postman.addRemark');
                Route::get('responses/history/{id}', [PostmanRequestCreateController::class, 'responsesHistory'])->name('postman.responsesHistory');
            });
            Route::post('postman-column-visbility', [PostmanRequestCreateController::class, 'postmanColumnVisbilityUpdate'])->name('postman.column.update');

            Route::post('run-request-url', [PostmanRequestCreateController::class, 'postmanRunRequestUrl'])->name('postman.runrequesturl');

            Route::get('user-accesses', [AssetsManagerUsersAccessController::class, 'index'])->name('user-accesses.index');

            Route::get('appointment-request', [AppointmentRequestController::class, 'index'])->name('appointment-request.index');
            Route::get('appointment-request/records', [AppointmentRequestController::class, 'records'])->name('appointment-request.records');
            Route::get('appointment-request/record-appointment-request-ajax', [AppointmentRequestController::class, 'recordAppointmentRequestAjax'])->name('appointment-request.index_ajax');
            Route::get('appointment-request-remarks/{id}', [AppointmentRequestController::class, 'AppointmentRequestRemarks'])->name('appointment-request.remarks');
            Route::post('appointment-decline-remarks', [EventController::class, 'declineRemarks'])->name('appointment-request.declien.remarks');

            Route::prefix('script-documents')->group(function () {
                Route::get('/', [ScriptDocumentsController::class, 'index'])->name('script-documents.index');
                Route::get('records', [ScriptDocumentsController::class, 'records'])->name('script-documents.records');
                Route::get('create', [ScriptDocumentsController::class, 'create'])->name('script-documents.create');
                Route::post('store', [ScriptDocumentsController::class, 'store'])->name('script-documents.store');
                Route::get('edit/{id}', [ScriptDocumentsController::class, 'edit'])->name('script-documents.edit');
                Route::post('update', [ScriptDocumentsController::class, 'update'])->name('script-documents.update');
                Route::post('upload-file', [ScriptDocumentsController::class, 'uploadFile'])->name('script-documents.upload-file');
                Route::get('files/record', [ScriptDocumentsController::class, 'getScriptDocumentFilesList'])->name('script-documents.files.record');
                Route::get('record-script-document-ajax', [ScriptDocumentsController::class, 'recordScriptDocumentAjax'])->name('script-documents.index_ajax');
                Route::get('{id}/delete', [ScriptDocumentsController::class, 'destroy']);
                Route::get('countdevtask/{id}', [ScriptDocumentsController::class, 'taskCount']);
                // Route::get('error-logs', [ScriptDocumentsController::class, 'getScriptDocumentErrorLogs'])->name('script-documents.errorlogs');
                Route::get('errorlogslist', [ScriptDocumentsController::class, 'getScriptDocumentErrorLogsList'])->name('script-documents.getScriptDocumentErrorLogsList');
            });
            Route::get('script-documents-histories/{id}', [ScriptDocumentsController::class, 'ScriptDocumentHistory'])->name('script-documents.histories');
            Route::get('script-documents-comment/{id}', [ScriptDocumentsController::class, 'ScriptDocumentComment'])->name('script-documents.comment');
            Route::get('script-documents-histroy-comment/{id}', [ScriptDocumentsController::class, 'ScriptDocumentCommentHistory'])->name('script-documents.histroy_comment');

            Route::post('bug-tracking-column-visbility', [BugTrackingController::class, 'columnVisbilityUpdate'])->name('bug-tracking.column.update');

            // Bug Tracking
            Route::prefix('bug-tracking')->group(function () {
                Route::get('/', [BugTrackingController::class, 'index'])->name('bug-tracking.index');
                Route::get('records', [BugTrackingController::class, 'records'])->name('bug-tracking.records');
                Route::get('create', [BugTrackingController::class, 'create'])->name('bug-tracking.create');
                Route::post('store', [BugTrackingController::class, 'store'])->name('bug-tracking.store');
                Route::get('edit/{id}', [BugTrackingController::class, 'edit'])->name('bug-tracking.edit');
                Route::post('update', [BugTrackingController::class, 'update'])->name('bug-tracking.update');
                Route::post('upload-file', [BugTrackingController::class, 'uploadFile'])->name('bug-tracking.upload-file');
                Route::get('files/record', [BugTrackingController::class, 'getBugFilesList'])->name('bug-tracking.files.record');
                Route::post('assign_user', [BugTrackingController::class, 'assignUser'])->name('bug-tracking.assign_user');
                Route::post('change_bug_type', [BugTrackingController::class, 'changeBugType'])->name('bug-tracking.change_bug_type');
                Route::post('change_module_type', [BugTrackingController::class, 'changeModuleType'])->name('bug-tracking.change_module_type');
                Route::post('severity_user', [BugTrackingController::class, 'severityUser'])->name('bug-tracking.severity_user');
                Route::post('status_user', [BugTrackingController::class, 'statusUser'])->name('bug-tracking.status_user');
                Route::post('sendmessage', [BugTrackingController::class, 'sendMessage'])->name('bug-tracking.sendmessage');
                Route::get('record-tracking-ajax', [BugTrackingController::class, 'recordTrackingAjax'])->name('bug-tracking.index_ajax');
                Route::post('assign_user_bulk', [BugTrackingController::class, 'assignUserBulk'])->name('bug-tracking.assign_user_bulk');
                Route::post('severity_user_bulk', [BugTrackingController::class, 'severityUserBulk'])->name('bug-tracking.severity_user_bulk');
                Route::post('status_user_bulk', [BugTrackingController::class, 'statusUserBulk'])->name('bug-tracking.status_user_bulk');

                Route::post('status', [BugTrackingController::class, 'status'])->name('bug-tracking.status');
                Route::post('statuscolor', [BugTrackingController::class, 'statuscolor'])->name('bug-tracking.statuscolor');
                Route::post('environment', [BugTrackingController::class, 'environment'])->name('bug-tracking.environment');
                Route::post('type', [BugTrackingController::class, 'type'])->name('bug-tracking.type');
                Route::post('severity', [BugTrackingController::class, 'severity'])->name('bug-tracking.severity');
                Route::get('bug-history/{id}', [BugTrackingController::class, 'bugHistory'])->name('bug-tracking.bug-history');
                Route::get('user-history/{id}', [BugTrackingController::class, 'userHistory'])->name('bug-tracking.user-history');
                Route::get('severity-history/{id}', [BugTrackingController::class, 'severityHistory'])->name('bug-tracking.severity-history');
                Route::get('website-history', [BugTrackingController::class, 'websiteHistory'])->name('bug-tracking.website-history');
                Route::get('status-history/{id}', [BugTrackingController::class, 'statusHistory'])->name('bug-tracking.status-history');
                Route::get('communicationData/{id}', [BugTrackingController::class, 'communicationData'])->name('bug-tracking.communicationData');
                Route::get('{id}/delete', [BugTrackingController::class, 'destroy']);
                Route::post('websitelist', [BugTrackingController::class, 'getWebsiteList'])->name('bug-tracking.websitelist');
                Route::get('website', [BugTrackingController::class, 'website'])->name('bug-tracking.website');
                Route::post('checkbug', [BugTrackingController::class, 'checkbug'])->name('bug-tracking.checkbug');
                Route::get('countdevtask/{id}', [BugTrackingController::class, 'taskCount']);
                Route::post('hubstaff_task', [BugTrackingController::class, 'createHubstaffManualTask'])->name('bug-tracking.hubstaff_task');
            });
            Route::get('bug-trackinghistory', [BugTrackingController::class, 'getTrackedHistory'])->name('bug-tracking.history');

            // Test Cases
            Route::prefix('test-cases')->group(function () {
                Route::get('/', [TestCaseController::class, 'index'])->name('test-cases.index');
                Route::get('create', [TestCaseController::class, 'create'])->name('test-cases.create');
                Route::post('store', [TestCaseController::class, 'store'])->name('test-cases.store');
                Route::get('records', [TestCaseController::class, 'records'])->name('test-cases.records');
                Route::get('edit/{id}', [TestCaseController::class, 'edit'])->name('test-cases.edit');
                Route::get('test-case-history/{id}', [TestCaseController::class, 'testCaseHistory'])->name('test-cases.test-cases-history');
                Route::post('update', [TestCaseController::class, 'update'])->name('test-cases.update');
                Route::post('status', [TestCaseController::class, 'updateStatus'])->name('test-cases.status');
                Route::get('{id}/delete', [TestCaseController::class, 'destroy']);
                Route::post('assign_user', [TestCaseController::class, 'assignUser'])->name('test-cases.assign_user');
                Route::post('status_user', [TestCaseController::class, 'statusUser'])->name('test-cases.status_user');
                Route::post('add-test-cases', [TestCaseController::class, 'sendTestCases'])->name('test-cases.sendtestcases');
                Route::get('usertest-history/{id}', [TestCaseController::class, 'usertestHistory'])->name('test-cases.usertest-history');
                Route::get('user-teststatus-history/{id}', [TestCaseController::class, 'userteststatusHistory'])->name('test-cases.userstatus-history');
                Route::delete('delete-multiple-test-cases', [TestCaseController::class, 'deleteTestCases'])->name('test-cases.delete_multiple_test_cases');
                Route::get('module/{id}', [TestCaseController::class, 'testCasesByModule'])->name('test-cases.bymodule');
                Route::get('{id}', [TestCaseController::class, 'show'])->name('test-cases.show');
            });

            // Test Cases Automation
            Route::prefix('test-case-automation')->group(function () {
                Route::get('/', [TestCaseAutomationController::class, 'index'])->name('test-case-automation.index');
                Route::get('records', [TestCaseAutomationController::class, 'records'])->name('test-case-automation.records');
                Route::get('test-case-history/{id}', [TestCaseAutomationController::class, 'testCaseHistory'])->name('test-case-automation.test-cases-history');
                Route::post('status', [TestCaseAutomationController::class, 'status'])->name('test-case-automation.status');
                Route::post('assign_user', [TestCaseAutomationController::class, 'assignUser'])->name('test-case-automation.assign_user');
                Route::post('status_user', [TestCaseAutomationController::class, 'statusUser'])->name('test-case-automation.status_user');
                Route::post('sendmessage', [TestCaseAutomationController::class, 'sendMessage'])->name('test-case-automation.sendmessage');
                Route::post('add-test-cases', [TestCaseAutomationController::class, 'sendTestCases'])->name('test-case-automation.sendtestcases');
                Route::get('usertest-history/{id}', [TestCaseAutomationController::class, 'usertestHistory'])->name('test-case-automation.usertest-history');
                Route::get('user-teststatus-history/{id}', [TestCaseAutomationController::class, 'userteststatusHistory'])->name('test-case-automation.userteststatus-history');
                Route::delete('delete-multiple-test-cases', [TestCaseAutomationController::class, 'deleteTestCases'])->name('test-case-automation.delete_multiple_test_cases');
                Route::get('module/{id}', [TestCaseAutomationController::class, 'testCasesByModule'])->name('test-case-automation.bymodule');
                Route::post('add-test-case-command', [TestCaseAutomationController::class, 'addTestCaseCommand'])->name('test-case-automation.add.command');
                Route::get('run-command/{id}', [TestCaseAutomationController::class, 'runTestCaseCommand'])->name('test-case-command.run');
                Route::get('delete-command/{id}', [TestCaseAutomationController::class, 'deleteTestCaseCommand'])->name('test-case-command.delete');
                Route::get('view-result/{id?}', [TestCaseAutomationController::class, 'viewResult'])->name('test-case-automation.view-result');
            });

            // Test Suites
            Route::prefix('test-suites')->group(function () {
                Route::get('/', [TestSuitesController::class, 'index'])->name('test-suites.index');
                Route::get('records', [TestSuitesController::class, 'records'])->name('test-suites.records');
                Route::get('create', [TestSuitesController::class, 'create'])->name('test-suites.create');
                Route::post('store', [TestSuitesController::class, 'store'])->name('test-suites.store');
                Route::get('edit/{id}', [TestSuitesController::class, 'edit'])->name('test-suites.edit');
                Route::post('update', [TestSuitesController::class, 'update'])->name('test-suites.update');
                Route::post('assign_user', [TestSuitesController::class, 'assignUser'])->name('test-suites.assign_user');
                Route::post('severity_user', [TestSuitesController::class, 'severityUser'])->name('test-suites.severity_user');
                Route::post('status_user', [TestSuitesController::class, 'statusUser'])->name('test-suites.status_user');
                Route::post('sendmessage', [TestSuitesController::class, 'sendMessage'])->name('test-suites.sendmessage');

                Route::post('status', [TestSuitesController::class, 'status'])->name('test-suites.status');
                Route::post('environment', [TestSuitesController::class, 'environment'])->name('test-suites.environment');
                Route::post('type', [TestSuitesController::class, 'type'])->name('test-suites.type');
                Route::post('severity', [TestSuitesController::class, 'severity'])->name('test-suites.severity');
                Route::get('bug-history/{id}', [TestSuitesController::class, 'bugHistory'])->name('test-suites.bug-history');
                Route::get('{id}/delete', [TestSuitesController::class, 'destroy']);

                Route::post('hubstaff_task', [TestSuitesController::class, 'createHubstaffManualTask'])->name('test-suites.hubstaff_task');
            });

            Route::get('test-suiteshistory', [TestSuitesController::class, 'getTrackedHistory'])->name('test-suites.history');

            Route::get('cold-leads/delete', [ColdLeadsController::class, 'deleteColdLead']);
            Route::resource('cold-leads-broadcasts', ColdLeadBroadcastsController::class);
            Route::resource('cold-leads', ColdLeadsController::class);

            Route::prefix('sitejabber')->group(function () {
                Route::post('sitejabber/attach-detach', [SitejabberQAController::class, 'attachOrDetachReviews']);
                Route::post('review/reply', [SitejabberQAController::class, 'sendSitejabberQAReply']);
                Route::get('review/{id}/confirm', [SitejabberQAController::class, 'confirmReviewAsPosted']);
                Route::get('review/{id}/delete', [SitejabberQAController::class, 'detachBrandReviews']);
                Route::get('review/{id}', [SitejabberQAController::class, 'attachBrandReviews']);
                Route::get('accounts', [SitejabberQAController::class, 'accounts']);
                Route::get('reviews', [SitejabberQAController::class, 'reviews']);
                Route::resource('qa', SitejabberQAController::class);
            });

            Route::prefix('pinterest')->group(function () {
                Route::prefix('accounts')->group(function () {
                    Route::get('', [PinterestAccountController::class, 'index'])->name('pinterest.accounts');
                    Route::post('create', [PinterestAccountController::class, 'createAccount'])->name('pinterest.accounts.create');
                    Route::get('{id}', [PinterestAccountController::class, 'getAccount'])->name('pinterest.accounts.get');
                    Route::post('update/{id}', [PinterestAccountController::class, 'updateAccount'])->name('pinterest.accounts.update');
                    Route::post('delete/{id}', [PinterestAccountController::class, 'deleteAccount'])->name('pinterest.accounts.delete');
                    Route::get('connect/login', [PinterestAccountController::class, 'loginAccount'])->name('pinterest.accounts.connect.login');
                    Route::get('connect/{id}', [PinterestAccountController::class, 'connectAccount'])->name('pinterest.accounts.connect');
                    Route::get('refresh/{id}', [PinterestAccountController::class, 'refreshAccount'])->name('pinterest.accounts.refresh');
                    Route::get('disconnect/{id}', [PinterestAccountController::class, 'disconnectAccount'])->name('pinterest.accounts.disconnect');
                    Route::prefix('{id}')->group(function () {
                        Route::get('dashboard', [PinterestAdsAccountsController::class, 'dashboard'])->name('pinterest.accounts.dashboard');
                        Route::prefix('adsAccount')->group(function () {
                            Route::post('create', [PinterestAdsAccountsController::class, 'createAdsAccount'])->name('pinterest.accounts.adsAccount.create');
                        });
                        Route::prefix('boards')->group(function () {
                            Route::get('', [PinterestAdsAccountsController::class, 'boardsIndex'])->name('pinterest.accounts.board.index');
                            Route::post('create', [PinterestAdsAccountsController::class, 'createBoard'])->name('pinterest.accounts.board.create');
                            Route::get('get/{boardId}', [PinterestAdsAccountsController::class, 'getBoard'])->name('pinterest.accounts.board.get');
                            Route::post('update', [PinterestAdsAccountsController::class, 'updateBoard'])->name('pinterest.accounts.board.update');
                            Route::get('delete/{boardId}', [PinterestAdsAccountsController::class, 'deleteBoard'])->name('pinterest.accounts.board.delete');
                        });
                        Route::prefix('board-sections')->group(function () {
                            Route::get('', [PinterestAdsAccountsController::class, 'boardSectionsIndex'])->name('pinterest.accounts.boardSections.index');
                            Route::post('create', [PinterestAdsAccountsController::class, 'createBoardSections'])->name('pinterest.accounts.boardSections.create');
                            Route::get('get/{boardSectionId}', [PinterestAdsAccountsController::class, 'getBoardSection'])->name('pinterest.accounts.boardSections.get');
                            Route::post('update', [PinterestAdsAccountsController::class, 'updateBoardSection'])->name('pinterest.accounts.boardSections.update');
                            Route::get('delete/{boardSectionId}', [PinterestAdsAccountsController::class, 'deleteBoardSection'])->name('pinterest.accounts.boardSections.delete');
                        });
                        Route::prefix('pins')->group(function () {
                            Route::get('', [PinterestPinsController::class, 'pinsIndex'])->name('pinterest.accounts.pin.index');
                            Route::post('create', [PinterestPinsController::class, 'createPin'])->name('pinterest.accounts.pin.create');
                            Route::get('get/{pinId}', [PinterestPinsController::class, 'getPin'])->name('pinterest.accounts.pin.get');
                            Route::post('update', [PinterestPinsController::class, 'updatePin'])->name('pinterest.accounts.pin.update');
                            Route::get('delete/{pinId}', [PinterestPinsController::class, 'deletePin'])->name('pinterest.accounts.pin.delete');
                            Route::get('boards/{boardId}', [PinterestPinsController::class, 'getBoardSections'])->name('pinterest.accounts.pin.board.sections');
                        });
                        Route::prefix('campaigns')->group(function () {
                            Route::get('', [PinterestCampaignsController::class, 'campaignsIndex'])->name('pinterest.accounts.campaign.index');
                            Route::post('create', [PinterestCampaignsController::class, 'createCampaign'])->name('pinterest.accounts.campaign.create');
                            Route::get('get/{campaignId}', [PinterestCampaignsController::class, 'getCampaign'])->name('pinterest.accounts.campaign.get');
                            Route::post('update', [PinterestCampaignsController::class, 'updateCampaign'])->name('pinterest.accounts.campaign.update');
                        });
                        Route::prefix('ads-group')->group(function () {
                            Route::get('', [PinterestCampaignsController::class, 'adsGroupIndex'])->name('pinterest.accounts.adsGroup.index');
                            Route::post('create', [PinterestCampaignsController::class, 'createAdsGroup'])->name('pinterest.accounts.adsGroup.create');
                            Route::get('get/{adsGroupId}', [PinterestCampaignsController::class, 'getAdsGroup'])->name('pinterest.accounts.adsGroup.get');
                            Route::post('update', [PinterestCampaignsController::class, 'updateAdsGroup'])->name('pinterest.accounts.adsGroup.update');
                        });
                        Route::prefix('ads')->group(function () {
                            Route::get('', [PinterestCampaignsController::class, 'adsIndex'])->name('pinterest.accounts.ads.index');
                            Route::post('create', [PinterestCampaignsController::class, 'createAds'])->name('pinterest.accounts.ads.create');
                            Route::get('get/{adsId}', [PinterestCampaignsController::class, 'getAds'])->name('pinterest.accounts.ads.get');
                            Route::post('update', [PinterestCampaignsController::class, 'updateAds'])->name('pinterest.accounts.ads.update');
                        });
                    });
                });
            });

            Route::prefix('database')->group(function () {
                Route::get('/', [DatabaseController::class, 'index'])->name('database.index');
                Route::get('/tables/{id}', [DatabaseTableController::class, 'index'])->name('database.tables');
                Route::post('/tables/view-lists', [DatabaseTableController::class, 'viewList'])->name('database-tables.view-list');
                Route::get('/query-process-list', [DatabaseController::class, 'states'])->name('database.states');
                Route::get('/process-list', [DatabaseController::class, 'processList'])->name('database.process.list');
                Route::get('/process-kill', [DatabaseController::class, 'processKill'])->name('database.process.kill');
                Route::post('/export', [DatabaseController::class, 'export'])->name('database.export');
                Route::get('/command-logs', [DatabaseController::class, 'commandLogs'])->name('database.command-logs');
                Route::get('/tables-list', [DatabaseTableController::class, 'tableList'])->name('database.tables-list');
                Route::post('truncate-tables', [DatabaseTableController::class, 'truncateTables'])->name('truncate-tables');
                Route::post('truncate/table-histories', [DatabaseTableController::class, 'getTruncateTableHistories'])->name('truncate.tables.histories');
            });

            Route::resource('pre-accounts', PreAccountController::class);

            Route::prefix('instagram')->group(function () {
                Route::get('get/hashtag/{word}', [InstagramPostsController::class, 'hashtag']);
                Route::post('post/update-hashtag-post', [InstagramPostsController::class, 'updateHashtagPost']);
                Route::post('post/update-hashtag-post', [InstagramPostsController::class, 'updateHashtagPost']);
                Route::get('post/publish-post/{id}', [InstagramPostsController::class, 'publishPost']);
                Route::get('post/getImages', [InstagramPostsController::class, 'getImages']);
                Route::get('post/getCaptions', [InstagramPostsController::class, 'getCaptions']);
                Route::post('post/multiple', [InstagramPostsController::class, 'postMultiple']);
                Route::post('post/likeUserPost', [InstagramPostsController::class, 'likeUserPost']);
                Route::post('post/acceptRequest', [InstagramPostsController::class, 'acceptRequest']);
                Route::post('post/sendRequest', [InstagramPostsController::class, 'sendRequest']);

                Route::get('auto-comment-history', [UsersAutoCommentHistoriesController::class, 'index']);
                Route::get('auto-comment-history/assign', [UsersAutoCommentHistoriesController::class, 'assignPosts'])->name('auto-comment-history.assign');
                Route::get('auto-comment-history/send-posts', [UsersAutoCommentHistoriesController::class, 'sendMessagesToWhatsappToScrap'])->name('auto-comment-history.send-posts');
                Route::get('auto-comment-history/verify', [UsersAutoCommentHistoriesController::class, 'verifyComment'])->name('auto-comment-history.verify');
                Route::post('store', [InstagramController::class, 'store'])->name('instagram.store');
                Route::get('{id}/edit', [InstagramController::class, 'edit'])->name('instagram.edit');
                Route::put('update/{id}', [InstagramController::class, 'update'])->name('instagram.update');
                Route::get('delete/{id}', [InstagramController::class, 'deleteAccount'])->name('instagram.delete-account');
                Route::resource('auto-comment-report', AutoCommentHistoryController::class);
                Route::resource('auto-comment-hashtags', AutoReplyHashtagsController::class);
                Route::get('flag/{id}', [HashtagController::class, 'flagMedia']);
                Route::get('thread/{id}', [ColdLeadsController::class, 'getMessageThread']);
                Route::post('thread/{id}', [ColdLeadsController::class, 'sendMessage']);
                Route::resource('brand-tagged', BrandTaggedPostsController::class);
                Route::resource('auto-comments', InstagramAutoCommentsController::class);
                Route::post('media/comment', [HashtagController::class, 'commentOnHashtag']);
                Route::get('test/{id}', [AccountController::class, 'test']);
                Route::get('start-growth/{id}', [AccountController::class, 'startAccountGrowth']);
                Route::get('accounts', [InstagramController::class, 'accounts'])->name('instagram.accounts');
                Route::get('notification', [HashtagController::class, 'showNotification']);
                Route::get('hashtag/markPriority', [HashtagController::class, 'markPriority'])->name('hashtag.priority');
                Route::resource('influencer', InfluencersController::class);
                Route::post('influencer-keyword', [InfluencersController::class, 'saveKeyword'])->name('influencers.keyword.save');
                Route::post('influencer-keyword-image', [InfluencersController::class, 'getScraperImage'])->name('influencers.image');
                Route::post('influencer-keyword-status', [InfluencersController::class, 'checkScraper'])->name('influencers.status');
                Route::post('influencer-keyword-start', [InfluencersController::class, 'startScraper'])->name('influencers.start');
                Route::post('influencer-keyword-log', [InfluencersController::class, 'getLogFile'])->name('influencers.log');
                Route::post('influencer-restart-script', [InfluencersController::class, 'restartScript'])->name('influencers.restart');
                Route::post('influencer-stop-script', [InfluencersController::class, 'stopScript'])->name('influencers.stop');

                Route::post('influencer-sort-data', [InfluencersController::class, 'sortData'])->name('influencers.sort');
                Route::resource('automated-reply', InstagramAutomatedMessagesController::class);
                Route::get('/', [InstagramController::class, 'index']);
                Route::get('comments/processed', [HashtagController::class, 'showProcessedComments']);
                Route::get('hashtag/post/comments/{mediaId}', [HashtagController::class, 'loadComments']);
                Route::post('leads/store', [InstagramProfileController::class, 'add']);
                Route::get('profiles/followers/{id}', [InstagramProfileController::class, 'getFollowers']);
                Route::resource('keyword', KeywordsController::class, ['as' => 'mainkeyword']);
                Route::resource('profiles', InstagramProfileController::class);
                Route::get('posts', [InstagramController::class, 'showPosts']);
                Route::resource('hashtagposts', HashtagPostsController::class);
                Route::resource('hashtagpostscomments', HashtagPostCommentController::class);
                Route::get('hashtag/grid/{id}', [HashtagController::class, 'showGrid'])->name('hashtag.grid');
                Route::get('users/grid/{id}', [HashtagController::class, 'showUserGrid'])->name('users.grid');
                Route::get('hashtag/comments/{id?}', [HashtagController::class, 'showGridComments'])->name('hashtag.grid.comments');
                Route::get('hashtag/users/{id?}', [HashtagController::class, 'showGridUsers'])->name('hashtag.users.grid');
                Route::resource('hashtag', HashtagController::class);
                Route::post('hashtag/process/queue', [HashtagController::class, 'rumCommand'])->name('hashtag.command');
                Route::post('hashtag/queue/kill', [HashtagController::class, 'killCommand'])->name('hashtag.command.kill');
                Route::post('hashtag/queue/status', [HashtagController::class, 'checkStatusCommand'])->name('hashtag.command.status');
                Route::get('hashtags/grid', [InstagramController::class, 'hashtagGrid']);
                Route::get('influencers', [HashtagController::class, 'influencer'])->name('influencers.index');
                Route::get('influencers/get-log', [HashtagController::class, 'loginstance']);
                Route::post('influencers/history', [HashtagController::class, 'history'])->name('influencers.index.history');
                Route::post('influencers/reply/add', [HashtagController::class, 'addReply'])->name('influencers.reply.add');
                Route::post('influencers/reply/delete', [HashtagController::class, 'deleteReply'])->name('influencers.reply.delete');
                Route::post('influencers', [HashtagController::class, 'changeCronSetting'])->name('instagram.change.mailing');

                Route::get('comments', [InstagramController::class, 'getComments']);
                Route::post('comments', [InstagramController::class, 'postComment']);
                Route::get('post-media', [InstagramController::class, 'showImagesToBePosted']);
                Route::post('post-media', [InstagramController::class, 'postMedia']);
                Route::get('post-media-now/{schedule}', [InstagramController::class, 'postMediaNow']);
                Route::get('delete-schedule/{schedule}', [InstagramController::class, 'cancelSchedule']);
                Route::get('media/schedules', [InstagramController::class, 'showSchedules']);
                Route::post('media/schedules', [InstagramController::class, 'postSchedules']);
                Route::get('scheduled/events', [InstagramController::class, 'getScheduledEvents']);
                Route::get('schedule/{scheduleId}', [InstagramController::class, 'editSchedule']);
                Route::post('schedule/{scheduleId}', [InstagramController::class, 'updateSchedule']);
                Route::post('schedule/{scheduleId}/attach', [InstagramController::class, 'attachMedia']);

                Route::get('direct-message', [ColdLeadsController::class, 'home']);

                // Media manager
                Route::get('media', [MediaController::class, 'index'])->name('media.index');
                Route::post('media', [MediaController::class, 'upload'])->name('media.upload');
                Route::get('media/files', [MediaController::class, 'files'])->name('media.files');
                Route::delete('media', [MediaController::class, 'delete'])->name('media.delete');

                //Add Post
                Route::get('post/create', [InstagramPostsController::class, 'post'])->name('instagram.post');
                Route::any('post/create/images', [InstagramPostsController::class, 'post'])->name('instagram.post.images');

                Route::get('post', [InstagramPostsController::class, 'viewPost'])->name('post.index');
                Route::get('post/edit', [InstagramPostsController::class, 'editPost'])->name('post.edit');
                Route::post('post/create', [InstagramPostsController::class, 'createPost'])->name('post.store');

                Route::get('users', [InstagramPostsController::class, 'users'])->name('instagram.users');
                Route::post('users/save', [InstagramController::class, 'addUserForPost'])->name('instagram.users.add');
                Route::get('users/{id}', [InstagramPostsController::class, 'userPost'])->name('instagram.users.post');
                Route::post('users/feedback/hr-ticket/create', [UsersFeedbackHrTicketController::class, 'store'])->name('users.feedback.task.create');
                Route::get('users/feedback/get/hr_ticket', [UsersFeedbackHrTicketController::class, 'show']);

                //direct message new
                Route::get('direct', [DirectMessageController::class, 'index'])->name('direct.index');
                Route::post('direct/send', [DirectMessageController::class, 'sendMessage'])->name('direct.send');
                Route::post('direct/sendImage', [DirectMessageController::class, 'sendImage'])->name('direct.send.file');
                Route::post('direct/newChats', [DirectMessageController::class, 'incomingPendingRead'])->name('direct.new.chats');
                Route::post('direct/group-message', [DirectMessageController::class, 'sendMessageMultiple'])->name('direct.group-message');
                Route::post('direct/send-message', [DirectMessageController::class, 'prepareAndSendMessage'])->name('direct.send-message');
                Route::post('direct/latest-posts', [DirectMessageController::class, 'latestPosts'])->name('direct.latest-posts');
                Route::post('direct/messages', [DirectMessageController::class, 'messages'])->name('direct.messages');
                Route::post('direct/history', [DirectMessageController::class, 'history'])->name('direct.history');
                Route::post('direct/infulencers-messages', [DirectMessageController::class, 'influencerMessages'])->name('direct.infulencers-messages');

                Route::post('send/email/influencers', [HashtagController::class, 'sendMailToInfluencers'])->name('send.mail-influencer');
            });

            Route::prefix('social-media')->group(function () {
                Route::get('/instagram-posts/grid', [InstagramPostsController::class, 'grid']);
                Route::get('/instagram-posts', [InstagramPostsController::class, 'index']);
                Route::get('/instagram/message-queue', [InstagramPostsController::class, 'messageQueue'])->name('instagram.message-queue');
                Route::get('/instagram/message-queue/approve', [InstagramPostsController::class, 'messageQueueApprove'])->name('instagram.message-queue.approve');
                Route::post('/instagram/message-queue/settings', [InstagramPostsController::class, 'messageQueueSetting'])->name('instagram.message-queue.settings');
                Route::post('/instagram/message-queue/approve/approved', [InstagramPostsController::class, 'messageQueueApproved'])->name('instagram.message-queue.approved');
            });

            Route::prefix('facebook')->group(function () {
                Route::get('/influencers', [ScrappedFacebookUserController::class, 'index']);
            });

            Route::prefix('comments')->group(function () {
                Route::get('/facebook', [SocialController::class, 'getComments']);
                Route::post('/facebook', [SocialController::class, 'postComment']);
            });

            Route::resource('social-tags', SocialTagsController::class);

            // Whats App Config
            Route::get('instagram-config', [Marketing\InstagramConfigController::class, 'index'])->name('instagram.config.index');
            Route::get('instagram-keyword/create', [Marketing\InstagramConfigController::class, 'keywordStore'])->name('instagram.keyword.create');
            Route::get('instagram-keyword/list', [Marketing\InstagramConfigController::class, 'keywordList'])->name('instagram.keyword.list');
            Route::get('instagram-keyword/delete', [Marketing\InstagramConfigController::class, 'keyworddelete'])->name('instagram.keyword.delete');
            Route::get('instagram-history/{id}', [Marketing\InstagramConfigController::class, 'history'])->name('instagram.config.history');
            Route::post('instagram-config/store', [Marketing\InstagramConfigController::class, 'store'])->name('instagram.config.store');
            Route::post('instagram-config/edit', [Marketing\InstagramConfigController::class, 'edit'])->name('instagram.config.edit');
            Route::post('instagram-config/delete', [Marketing\InstagramConfigController::class, 'destroy'])->name('instagram.config.delete');
            Route::get('instagram-queue/{id}', [Marketing\InstagramConfigController::class, 'queue'])->name('instagram.config.queue');
            Route::post('instagram-queue/delete', [Marketing\InstagramConfigController::class, 'destroyQueue'])->name('instagram.config.delete_queue');
            Route::post('instagram-queue/delete_all/', [Marketing\InstagramConfigController::class, 'destroyQueueAll'])->name('instagram.config.delete_all');
            Route::post('instagram-automation', [Marketing\AccountController::class, 'automation'])->name('automation.form.store');

            //Social Config
            Route::get('accounts/{type?}', [Marketing\AccountController::class, 'index'])->name('accounts.index');
            Route::post('accounts', [Marketing\AccountController::class, 'store'])->name('accounts.store');
            Route::post('accounts/edit', [Marketing\AccountController::class, 'edit'])->name('accounts.edit');
            Route::post('accounts/broadcast', [Marketing\AccountController::class, 'broadcast'])->name('accounts.broadcast');

            Route::get('instagram-queue/delete_queues/{id}', [Marketing\InstagramConfigController::class, 'clearMessagesQueue'])->name('instagram.config.delete_all_queues');
            Route::get('instagram-config/get-barcode', [Marketing\InstagramConfigController::class, 'getBarcode'])->name('instagram.config.barcode');
            Route::get('instagram-config/get-screen', [Marketing\InstagramConfigController::class, 'getScreen'])->name('instagram.config.screen');
            Route::get('instagram-config/restart-script', [Marketing\InstagramConfigController::class, 'restartScript'])->name('instagram.restart.script');
            Route::get('instagram-config/blocked-number', [Marketing\InstagramConfigController::class, 'blockedNumber'])->name('instagram.block.number');

            //Social-Webhook-Logs
            Route::prefix('social-webhook-logs')->group(function () {
                Route::get('/', [LogsController::class, 'socialWebhookLogs'])->name('social-webhook-log.index');
            });

            // logScraperVsAiController
            Route::prefix('log-scraper-vs-ai')->group(function () {
                Route::match(['get', 'post'], '/{id}', [LogScraperVsAiController::class, 'index']);
            });
            Route::get('google-vision', [LogScraperVsAiController::class, 'googleVision'])->name('google.vision');
            Route::get('google-vision-data', [LogScraperVsAiController::class, 'googleVisionData'])->name('google.vision.data');

            Route::prefix('seo')->group(function () {
                Route::post('save-keyword-idea', [DomainSearchKeywordController::class, 'saveKeywordIdea'])->name('save.keyword.idea');
                Route::get('keyword-search', [DomainSearchKeywordController::class, 'searchKeyword'])->name('keyword-search');
            });

            Route::prefix('scrap')->group(function () {
                Route::get('python-site-log', [ScrapController::class, 'getPythonLog'])->name('get.python.log');
                Route::get('python/get-log', [ScrapController::class, 'loginstance'])->name('get.python.logapi');

                Route::post('column-visbility', [ScrapStatisticsController::class, 'columnVisbilityUpdate'])->name('scrap.column.update');
                Route::get('screenshot', [ScrapStatisticsController::class, 'getScreenShot']);
                Route::get('get-last-errors', [ScrapStatisticsController::class, 'getLastErrors']);
                Route::get('log-details', [ScrapStatisticsController::class, 'logDetails'])->name('scrap.log-details');
                Route::get('log/list', [ScrapStatisticsController::class, 'scrapperLogList']);
                Route::get('server-status-history', [ScrapStatisticsController::class, 'serverStatusHistory']);
                Route::get('server-status-process', [ScrapStatisticsController::class, 'serverStatusProcess']);
                Route::get('get-server-scraper-timing', [ScrapStatisticsController::class, 'getScraperServerTiming']);
                Route::get('position-history', [ScrapStatisticsController::class, 'positionHistory']);
                Route::post('position-history-download', [ScrapStatisticsController::class, 'positionHistorydownload']); //Purpose : Download  Position History Route - DEVTASK-4086
                Route::get('statistics/update-field', [ScrapStatisticsController::class, 'updateField']);
                Route::post('statistics/multiple-update-field', [ScrapStatisticsController::class, 'multipleUpdateField'])->name('scrap.multiple.update.field');
                Route::get('statistics/update-scrap-field', [ScrapStatisticsController::class, 'updateScrapperField']);
                Route::get('statistics/show-history', [ScrapStatisticsController::class, 'showHistory']);
                Route::post('statistics/status/create', [ScrapStatisticsController::class, 'ssstatusCreate'])->name('scrap.status.create');
                Route::post('statistics/update-priority', [ScrapStatisticsController::class, 'updatePriority']);
                Route::get('statistics/history', [ScrapStatisticsController::class, 'getHistory']);
                Route::post('statistics/reply/add', [ScrapStatisticsController::class, 'addReply']);
                Route::post('statistics/reply/delete', [ScrapStatisticsController::class, 'deleteReply']);
                Route::get('statistics/server-history', [ScrapStatisticsController::class, 'serverHistory']);
                Route::get('statistics/server-history/close-job', [ScrapStatisticsController::class, 'endJob'])->name('statistics.server-history.close-job');
                Route::get('quick-statistics', [ScrapStatisticsController::class, 'quickView'])->name('statistics.quick');
                Route::resource('statistics', ScrapStatisticsController::class);
                Route::get('getremark', [ScrapStatisticsController::class, 'getRemark'])->name('scrap.getremark');
                Route::get('latest-remark', [ScrapStatisticsController::class, 'getLastRemark'])->name('scrap.latest-remark');
                Route::get('auto-restart', [ScrapStatisticsController::class, 'autoRestart'])->name('scrap.auto-restart');
                Route::post('position-all', [ScrapStatisticsController::class, 'positionAll'])->name('scrap.position-all');
                Route::post('addremark', [ScrapStatisticsController::class, 'addRemark'])->name('scrap.addRemark');
                Route::post('scrap/add/note', [ScrapStatisticsController::class, 'addNote'])->name('scrap/add/note');
                Route::get('facebook/inbox', [FacebookController::class, 'getInbox']);
                Route::resource('facebook', FacebookController::class);
                Route::get('gmails/{id}', [GmailDataController::class, 'show']);
                Route::resource('gmail', GmailDataController::class);
                Route::resource('designer', DesignerController::class);
                Route::resource('sales-item', SalesItemController::class);
                Route::get('/scrap-links', [ScrapController::class, 'scrap_links']);
                Route::get('scrap-links/status/histories/{id}', [ScrapController::class, 'scrapLinksStatusHistories'])->name('scrap_links.status.histories');
                Route::get('/dubbizle', [DubbizleController::class, 'index']);
                Route::post('/dubbizle/set-reminder', [DubbizleController::class, 'updateReminder']);
                Route::post('/dubbizle/bulkWhatsapp', [DubbizleController::class, 'bulkWhatsapp'])->name('dubbizle.bulk.whatsapp');
                Route::get('/dubbizle/{id}/edit', [DubbizleController::class, 'edit']);
                Route::put('/dubbizle/{id}', [DubbizleController::class, 'update']);
                Route::get('/dubbizle/{id}', [DubbizleController::class, 'show'])->name('dubbizle.show');
                Route::get('/products', [ScrapController::class, 'showProductStat']);
                Route::get('/products/auto-rejected-stat', [ProductController::class, 'showAutoRejectedProducts']);
                Route::get('/activity', [ScrapController::class, 'activity'])->name('scrap.activity');
                Route::get('/excel', [ScrapController::class, 'excel_import']);
                Route::post('/excel', [ScrapController::class, 'excel_store']);
                Route::get('/google/images', [ScrapController::class, 'index']);
                Route::post('/google/images', [ScrapController::class, 'scrapGoogleImages']);
                Route::post('/google/images/download', [ScrapController::class, 'downloadImages']);
                Route::get('/scraped-urls', [ScrapController::class, 'scrapedUrls']);
                Route::get('/generic-scraper', [ScrapController::class, 'genericScraper']);
                Route::post('/generic-scraper/save', [ScrapController::class, 'genericScraperSave'])->name('generic.save.scraper');
                Route::post('/generic-scraper/full-scrape', [ScrapController::class, 'scraperFullScrape'])->name('generic.full-scrape');
                Route::get('/generic-scraper/mapping/{id}', [ScrapController::class, 'genericMapping'])->name('generic.mapping');
                Route::post('/generic-scraper/mapping/save', [ScrapController::class, 'genericMappingSave'])->name('generic.mapping.save');
                Route::post('/generic-scraper/mapping/delete', [ScrapController::class, 'genericMappingDelete'])->name('generic.mapping.delete');

                Route::post('/scraper/saveChildScraper', [ScrapController::class, 'saveChildScraper'])->name('save.childrenScraper');
                Route::get('/server-statistics', [ScrapStatisticsController::class, 'serverStatistics'])->name('scrap.scrap_server_status');
                Route::get('/server-statistics/history/{scrap_name}', [ScrapStatisticsController::class, 'serverStatisticsHistory'])->name('scrap.scrap_server_history');
                Route::get('/task-list', [ScrapStatisticsController::class, 'taskList'])->name('scrap.task-list');
                Route::get('/task-list-multiple', [ScrapStatisticsController::class, 'taskListMultiple'])->name('scrap.task-list-multiple');
                Route::get('/killed-list', [ScrapStatisticsController::class, 'killedList'])->name('scrap.killed-list');
                Route::post('/{id}/create', [ScrapStatisticsController::class, 'taskCreate'])->name('scrap.task-list.create');
                Route::post('/{id}/create-multiple', [ScrapStatisticsController::class, 'taskCreateMultiple'])->name('scrap.task-list.create-multiple');
                Route::get('change-user', [ScrapStatisticsController::class, 'changeUser'])->name('scrap.changeUser');

                Route::get('scrap-brand', [BrandController::class, 'scrap_brand'])->name('scrap-brand');

                // Route::get('/{name}', 'ScrapController@showProducts')->name('show.logFile');
                Route::post('/scrap/assignTask', [ScrapController::class, 'assignScrapProductTask'])->name('scrap.assignTask');

                Route::get('servers/statistics', [ScrapController::class, 'getServerStatistics'])->name('scrap.servers.statistics');

                Route::get('logdata/view_scrappers_data', [ScrapStatisticsController::class, 'view_scrappers_data'])->name('scrap.logdata.view_scrappers_data'); //Purpose : Add Route - DEVTASK-20102
                Route::post('assign/scrapper', [ScrapStatisticsController::class, 'assignScrapperIssue'])->name('scrap.assign'); //Purpose : Add Route - DEVTASK-20102
            });

            Route::resource('quick-reply', QuickReplyController::class);

            Route::resource('track', UserActionsController::class);
            Route::get('competitor-page/hide/{id}', [CompetitorPageController::class, 'hideLead'])->name('competitor-page.hide-lead');
            Route::get('competitor-page/approve/{id}', [CompetitorPageController::class, 'approveLead'])->name('competitor-page.approve-lead');
            Route::resource('competitor-page', CompetitorPageController::class);
            Route::resource('target-location', TargetLocationController::class);

            //Legal Module
            Route::post('lawyer-speciality', [LawyerController::class, 'storeSpeciality'])->name('lawyer.speciality.store');
            Route::resource('lawyer', LawyerController::class);
            Route::get('case/{case}/receivable', [CaseReceivableController::class, 'index'])->name('case.receivable');
            Route::post('case/{case}/receivable', [CaseReceivableController::class, 'store'])->name('case.receivable.store');
            Route::put('case/{case}/receivable/{case_receivable}', [CaseReceivableController::class, 'update'])->name('case.receivable.update');
            Route::delete('case/{case}/receivable/{case_receivable}', [CaseReceivableController::class, 'destroy'])->name('case.receivable.destroy');
            Route::resource('case', CaseController::class);
            Route::get('case-costs/{case}', [CaseController::class, 'getCosts'])->name('case.cost');
            Route::post('case-costs', [CaseController::class, 'costStore'])->name('case.cost.post');
            Route::put('case-costs/update/{case_cost}', [CaseController::class, 'costUpdate'])->name('case.cost.update');

            Route::resource('keyword-instruction', KeywordInstructionController::class)->except(['create']);

            Route::prefix('/seo')->name('seo.')->group(function () {
                Route::get('/analytics', [SEOAnalyticsController::class, 'show'])->name('analytics');
                Route::get('/analytics/filter', [SEOAnalyticsController::class, 'filter'])->name('get.analytics.filter');
                Route::post('/analytics/filter', [SEOAnalyticsController::class, 'filter'])->name('analytics.filter');
                Route::post('/analytics/delete/{id}', [SEOAnalyticsController::class, 'delete'])->name('delete_entry');
            });

            Route::get('display/broken-link-details', [BrokenLinkCheckerController::class, 'displayBrokenLinkDetails'])->name('filteredResults');

            Route::get('old-incomings', [OldIncomingController::class, 'index'])->name('oldIncomings');
            Route::get('old-incomings', [OldIncomingController::class, 'index'])->name('filteredOldIncomings');
            Route::post('store/old-incomings', [OldIncomingController::class, 'store'])->name('storeOldIncomings');
            Route::get('edit/old-incomings/{id}', [OldIncomingController::class, 'edit'])->name('editOldIncomings');
            Route::post('update/old-incomings/{id}', [OldIncomingController::class, 'update'])->name('updateOldIncomings');

            // Old Module
            Route::post('old/send/emailBulk', [OldController::class, 'sendEmailBulk'])->name('old.email.send.bulk');
            Route::post('old/send/email', [OldController::class, 'sendEmail'])->name('old.email.send');
            Route::get('old/gettaskremark', [OldController::class, 'getTaskRemark'])->name('old.gettaskremark');
            Route::post('old/addremark', [OldController::class, 'addRemark'])->name('old.addRemark');
            Route::get('old/email/inbox', [OldController::class, 'emailInbox'])->name('old.email.inbox');
            Route::get('old/{old}/payments', [OldController::class, 'paymentindex'])->name('old.payments');
            Route::post('old/{old}/payments', [OldController::class, 'paymentStore'])->name('old.payments.store');
            Route::put('old/{old}/payments/{old_payment}', [OldController::class, 'paymentUpdate'])->name('old.payments.update');
            Route::delete('old/{old}/payments/{old_payment}', [OldController::class, 'paymentDestroy'])->name('old.payments.destroy');
            Route::resource('old', OldController::class);
            Route::post('old/block', [OldController::class, 'block'])->name('old.block');
            Route::post('old/category/create', [OldController::class, 'createCategory'])->name('old.category.create');
            Route::post('old/status/create', [OldController::class, 'createStatus'])->name('old.status.create');
            Route::post('old/update/status', [OldController::class, 'updateOld'])->name('old.update.status');

            //Simple Duty

            //Simple duty category
            Route::get('duty/category', [SimplyDutyCategoryController::class, 'index'])->name('simplyduty.category.index');
            Route::get('duty/category/update', [SimplyDutyCategoryController::class, 'getCategoryFromApi'])->name('simplyduty.category.update');

            Route::get('duty/hscode', [HsCodeController::class, 'index'])->name('simplyduty.hscode.index');

            Route::post('duty/setting', [HsCodeController::class, 'saveKey'])->name('simplyduty.hscode.key');

            //Simple Duty Currency
            Route::get('duty/currency', [SimplyDutyCurrencyController::class, 'index'])->name('simplyduty.currency.index');
            Route::get('duty/currency/update', [SimplyDutyCurrencyController::class, 'getCurrencyFromApi'])->name('simplyduty.currency.update');

            //Simple Duty Country
            Route::get('duty/segment', [SimplyDutySegmentController::class, 'index']);
            Route::get('duty/segment/add', [SimplyDutySegmentController::class, 'segment_add']);
            Route::get('duty/segment/delete', [SimplyDutySegmentController::class, 'segment_delete']);
            Route::get('duty/country', [SimplyDutyCountryController::class, 'index'])->name('simplyduty.country.index');
            Route::get('duty/country/update', [SimplyDutyCountryController::class, 'getCountryFromApi'])->name('simplyduty.country.update');
            Route::get('duty/country/updateduty', [SimplyDutyCountryController::class, 'updateduty'])->name('simplyduty.country.updateduty');
            Route::get('duty/country/addsegment', [SimplyDutyCountryController::class, 'addsegment']);
            Route::post('duty/country/assign-default-value', [SimplyDutyCountryController::class, 'assignDefaultValue']);
            Route::post('duty/country/approve', [SimplyDutyCountryController::class, 'approve']);

            //Simple Duty Calculation
            Route::get('duty/calculation', [SimplyDutyCalculationController::class, 'index'])->name('simplyduty.calculation.index');
            Route::post('duty/calculation', [SimplyDutyCalculationController::class, 'calculation'])->name('simplyduty.calculation');

            //Simply Duty Common
            Route::get('hscode/most-common', [HsCodeController::class, 'mostCommon'])->name('hscode.mostcommon.index');

            //Simply Duty Common
            Route::get('hscode/most-common-category', [HsCodeController::class, 'mostCommonByCategory'])->name('hscode.mostcommon.category');

            Route::get('display/analytics-data', [AnalyticsController::class, 'showData'])->name('showAnalytics');
            Route::post('display/analytics-history', [AnalyticsController::class, 'history'])->name('analytics.history');

            Route::get('display/back-link-details', [BackLinkController::class, 'displayBackLinkDetails'])->name('backLinkFilteredResults');
            Route::get('links-to-post', [SEOAnalyticsController::class, 'linksToPost']);

            Route::prefix('country-duty')->group(function () {
                Route::get('/', [CountryDutyController::class, 'index'])->name('country.duty.index');
                Route::post('/search', [CountryDutyController::class, 'search'])->name('country.duty.search');
                Route::post('/save-country-group', [CountryDutyController::class, 'saveCountryGroup'])->name('save.country.group');
                Route::prefix('list')->group(function () {
                    Route::get('/', [CountryDutyController::class, 'list'])->name('country.duty.list');
                    Route::get('/records', [CountryDutyController::class, 'records'])->name('country.duty.records');
                    Route::post('save', [CountryDutyController::class, 'store'])->name('country.duty.save');
                    Route::post('update-group-field', [CountryDutyController::class, 'updateGroupField'])->name('country.duty.update-group-field');
                    Route::prefix('{id}')->group(function () {
                        Route::get('edit', [CountryDutyController::class, 'edit'])->name('country.duty.edit');
                        Route::get('delete', [CountryDutyController::class, 'delete'])->name('country.duty.delete');
                    });
                });
            });

            //Blogger Module
            Route::get('blogger-email', [BloggerEmailTemplateController::class, 'index'])->name('blogger.email.template');
            Route::put('blogger-email/{bloggerEmailTemplate}', [BloggerEmailTemplateController::class, 'update'])->name('blogger.email.template.update');

            Route::get('blogger/{blogger}/payments', [BloggerPaymentController::class, 'index'])->name('blogger.payments');
            Route::post('blogger/{blogger}/payments', [BloggerPaymentController::class, 'store'])->name('blogger.payments.store');
            Route::put('blogger/{blogger}/payments/{blogger_payment}', [BloggerPaymentController::class, 'update'])->name('blogger.payments.update');
            Route::delete('blogger/{blogger}/payments/{blogger_payment}', [BloggerPaymentController::class, 'destroy'])->name('blogger.payments.destroy');

            Route::resource('blogger', BloggerController::class);

            Route::post('blogger-contact', [ContactBloggerController::class, 'store'])->name('blogger.contact.store');
            Route::put('blogger-contact/{contact_blogger}', [ContactBloggerController::class, 'update'])->name('blogger.contact.update');
            Route::delete('blogger-contact/{contact_blogger}', [ContactBloggerController::class, 'destroy'])->name('contact.blogger.destroy');

            Route::get('display/back-link-details', [BackLinkController::class, 'displayBackLinkDetails'])->name('backLinks');
            Route::get('display/back-link-details', [BackLinkController::class, 'displayBackLinkDetails'])->name('backLinkFilteredResults');
            Route::post('blogger-product-image/{blogger_product}', [BloggerProductController::class, 'uploadImages'])->name('blogger.image.upload');
            Route::get('blogger-product-get-image/{blogger_product}', [BloggerProductController::class, 'getImages'])->name('blogger.image');
            Route::resource('blogger-product', BloggerProductController::class);

            //Monetary Account Module
            Route::get('monetary-account/{id}/history', [MonetaryAccountController::class, 'history'])->name('monetary-account.history');
            Route::resource('monetary-account', MonetaryAccountController::class);

            // Mailchimp Module
            //@todo there is no mailchimp controller class
            //    Route::get('manageMailChimp', [MailchimpController::class, 'manageMailChimp'])->name('manage.mailchimp');
            //    Route::post('subscribe', [MailchimpController::class, 'subscribe'])->name('subscribe');
            //    Route::post('sendCompaign', [MailchimpController::class, 'sendCompaign'])->name('sendCompaign');
            //    Route::get('make-active-subscribers', [MailchimpController::class, 'makeActiveSubscriber'])->name('make.active.subscriber');

            //Hubstaff Module
            Route::get('v1/auth', [Hubstaff\HubstaffController::class, 'authenticationPage'])->name('get.token');
            Route::post('user-details-token', [Hubstaff\HubstaffController::class, 'getToken'])->name('user.token');
            Route::get('get-users', [Hubstaff\HubstaffController::class, 'gettingUsersPage'])->name('get.users');
            Route::post('v1/users', [Hubstaff\HubstaffController::class, 'userDetails'])->name('get.users.api');
            Route::get('get-user-from-id', [Hubstaff\HubstaffController::class, 'showFormUserById'])->name('get.user-fromid');
            Route::post('get-user-from-id', [Hubstaff\HubstaffController::class, 'getUserById'])->name('post.user-fromid');
            Route::get('v1/users/projects', [Hubstaff\HubstaffController::class, 'getProjectPage'])->name('get.user-project-page');
            Route::post('v1/users/projects', [Hubstaff\HubstaffController::class, 'getProjects'])->name('post.user-project-page');

            // ------------Projects---------------
            Route::get('get-projects', [Hubstaff\HubstaffController::class, 'getUserProject'])->name('user.project');
            Route::post('get-projects', [Hubstaff\HubstaffController::class, 'postUserProject'])->name('post.user-project');

            // --------------Tasks---------------
            Route::get('get-project-tasks', [Hubstaff\HubstaffController::class, 'getProjectTask'])->name('project.task');
            Route::post('get-project-taks', [Hubstaff\HubstaffController::class, 'postProjectTask'])->name('post.project-task');

            Route::get('v1/tasks', [Hubstaff\HubstaffController::class, 'getTaskFromId'])->name('get-project.task-from-id');
            Route::post('v1/tasks', [Hubstaff\HubstaffController::class, 'postTaskFromId'])->name('post-project.task-from-id');

            // --------------Organizaitons--------------
            Route::get('v1/organizations', [Hubstaff\HubstaffController::class, 'index'])->name('organizations');
            Route::post('v1/organizations', [Hubstaff\HubstaffController::class, 'getOrganization'])->name('post.organizations');

            Route::get('v1/organization/members', [Hubstaff\HubstaffController::class, 'organizationMemberPage'])->name('organization.members');
            Route::post('v1/organization/members', [Hubstaff\HubstaffController::class, 'showMembers'])->name('post.organization-member');

            // --------------Screenshots--------------
            Route::get('v1/screenshots', [Hubstaff\HubstaffController::class, 'getScreenshotPage'])->name('get.screenshots');
            Route::post('v1/screenshots', [Hubstaff\HubstaffController::class, 'postScreenshots'])->name('post.screenshot');

            // -------------payments----------------
            Route::get('v1/team_payments', [Hubstaff\HubstaffController::class, 'getTeamPaymentPage'])->name('team.payments');
            Route::post('v1/team_payments', [Hubstaff\HubstaffController::class, 'getPaymentDetail'])->name('post.payment-page');

            // ------------Attendance---------------
            Route::get('v2/organizations/attendance-shifts', [Hubstaff\AttendanceController::class, 'index'])->name('attendance.shifts');
            Route::post('v2/organizations/attendance-shifts', [Hubstaff\AttendanceController::class, 'show'])->name('attendance.shifts-post');

            Route::get('display/analytics-data', [AnalyticsController::class, 'showData'])->name('showAnalytics');
            Route::get('display/analytics-data', [AnalyticsController::class, 'showData'])->name('filteredAnalyticsResults');
            Route::get('display/analytics-summary', [AnalyticsController::class, 'analyticsDataSummary'])->name('analyticsDataSummary');
            Route::get('display/analytics-summary', [AnalyticsController::class, 'analyticsDataSummary'])->name('filteredAnalyticsSummary');
            Route::get('display/analytics-customer-behaviour', [AnalyticsController::class, 'customerBehaviourByPage'])->name('customerBehaviourByPage');
            Route::get('display/analytics-customer-behaviour', [AnalyticsController::class, 'customerBehaviourByPage'])->name('filteredcustomerBehaviourByPage');

            // Broken Links
            Route::post('back-link/{id}/updateDomain', [BrokenLinkCheckerController::class, 'updateDomain']);
            Route::post('back-link/{id}/updateTitle', [BrokenLinkCheckerController::class, 'updateTitle']);

            // Article Links

            Route::get('display/articles', [ArticleController::class, 'index'])->name('articleApproval');
            Route::post('article/{id}/updateTitle', [ArticleController::class, 'updateTitle']);
            Route::post('article/{id}/updateDescription', [ArticleController::class, 'updateDescription']);

            //Back Linking
            Route::post('back-linking/{id}/updateTitle', [BackLinkController::class, 'updateTitle']);
            Route::post('back-linking/{id}/updateDesc', [BackLinkController::class, 'updateDesc']);
            Route::post('back-linking/{id}/updateURL', [BackLinkController::class, 'updateURL']);

            //SE Ranking Links
            Route::get('se-ranking/sites', [SERankingController::class, 'getSites'])->name('getSites');
            Route::get('se-ranking/keywords', [SERankingController::class, 'getKeyWords'])->name('getKeyWords');
            Route::get('se-ranking/keywords', [SERankingController::class, 'getKeyWords'])->name('filteredSERankKeywords');
            Route::get('se-ranking/competitors', [SERankingController::class, 'getCompetitors'])->name('getCompetitors');
            Route::get('se-ranking/analytics', [SERankingController::class, 'getAnalytics'])->name('getAnalytics');
            Route::get('se-ranking/backlinks', [SERankingController::class, 'getBacklinks'])->name('getBacklinks');
            Route::get('se-ranking/research-data', [SERankingController::class, 'getResearchData'])->name('getResearchData');
            Route::get('se-ranking/audit', [SERankingController::class, 'getSiteAudit'])->name('getSiteAudit');
            Route::get('se-ranking/competitors/keyword-positions/{id}', [SERankingController::class, 'getCompetitors'])->name('getCompetitorsKeywordPos');
            //Dev Task Planner Route
            Route::get('dev-task-planner', [NewDevTaskController::class, 'index'])->name('newDevTaskPlanner');
            Route::get('dev-task-planner', [NewDevTaskController::class, 'index'])->name('filteredNewDevTaskPlanner');
            //Supplier scrapping info
            Route::get('supplier-scrapping-info', [ProductController::class, 'getSupplierScrappingInfo'])->name('getSupplierScrappingInfo');

            //Routes for flows
            Route::prefix('flow')->group(function () {
                Route::get('/list', [FlowController::class, 'index'])->name('flow.index');
                Route::get('/conditionlist', [FlowController::class, 'conditionlist'])->name('flow.conditionlist');
                Route::get('/conditionliststatus', [FlowController::class, 'conditionListStatus'])->name('flow.conditionliststatus');
                Route::get('/scheduled-emails', [FlowController::class, 'allScheduleEmails'])->name('flow.schedule-emails');
                Route::get('/scheduled-messages', [FlowController::class, 'allScheduleMessages'])->name('flow.schedule-messages');
                Route::post('/update-email', [FlowController::class, 'updateEmail'])->name('flow.update-email');
                Route::post('/update-message', [FlowController::class, 'updateMessage'])->name('flow.update-message');
                Route::post('/delete-email', [FlowController::class, 'deleteEmail'])->name('flow.delete-email');
                Route::post('/delete-message', [FlowController::class, 'deleteMessage'])->name('flow.delete-message');
                Route::get('/{flow_code}', [FlowController::class, 'editFlow'])->name('flow.edit');
                Route::get('/detail/{flow_id}', [FlowController::class, 'flowDetail'])->name('flow.detail');
                Route::post('/create', [FlowController::class, 'createFlow'])->name('flow-create');
                Route::post('/update', [FlowController::class, 'updateFlow'])->name('flow-update');
                Route::post('/update/condition', [FlowController::class, 'updateCondition'])->name('update-condition');
                Route::post('/delete', [FlowController::class, 'flowDelete'])->name('flow-delete');
                Route::post('/action/delete', [FlowController::class, 'flowActionDelete'])->name('flow-action-delete');
                Route::post('/update/actions', [FlowController::class, 'updateFlowActions'])->name('flow-actions-update');
                Route::get('/action/message/{action_id}', [FlowController::class, 'getActionMessage'])->name('flow-action-message-view');
                Route::post('/update/action/message', [FlowController::class, 'updateActionMessage'])->name('flow-action-message');
                Route::post('/create/type', [FlowController::class, 'createType'])->name('flow-type-create');
            });

            Route::prefix('admin')->group(function () {
                Route::get('category/brand/min-max-pricing', [CategoryController::class, 'brandMinMaxPricing']);
                Route::get('category/brand/min-max-pricing-update-default', [CategoryController::class, 'updateMinMaxPriceDefault']);
                Route::post('category/brand/update-min-max-pricing', [CategoryController::class, 'updateBrandMinMaxPricing']);

                Route::post('task/change/status', [TaskModuleController::class, 'updateStatus'])->name('task.change.status');
                Route::post('task/status/create', [TaskModuleController::class, 'createStatus'])->name('task.status.create');
            });

            // pages notes started from here
            Route::prefix('page-notes')->group(function () {
                Route::post('/category/create', [PageNotesController::class, 'createCategory'])->name('pageNotes.createCategory');
                Route::post('create', [PageNotesController::class, 'create'])->name('createPageNote');
                Route::get('list', [PageNotesController::class, 'list'])->name('listPageNote');
                Route::get('edit', [PageNotesController::class, 'edit'])->name('editPageNote');
                Route::post('update', [PageNotesController::class, 'update'])->name('updatePageNote');
                Route::get('delete', [PageNotesController::class, 'delete'])->name('deletePageNote');
                Route::get('records', [PageNotesController::class, 'records'])->name('pageNotesRecords');
                Route::get('/', [PageNotesController::class, 'index'])->name('pageNotes.viewList');
            });
            Route::prefix('instruction-notes')->group(function () {
                Route::post('create', [PageNotesController::class, 'instructionCreate'])->name('instructionCreate');
            });

            Route::post('notesCreate', [PageNotesController::class, 'notesCreate'])->name('notesCreate');
            Route::post('stickyNotesCreate', [PageNotesController::class, 'stickyNotesCreate'])->name('stickyNotesCreate');

            Route::prefix('marketing')->group(function () {
                Route::prefix('whatsapp-business-account')->group(function () {
                    Route::get('', [WhatsappBusinessAccountController::class, 'index'])->name('whatsapp.business.account.index');
                    Route::post('create', [WhatsappBusinessAccountController::class, 'createAccount'])->name('whatsapp.business.account.create');
                    Route::post('update', [WhatsappBusinessAccountController::class, 'updateAccount'])->name('whatsapp.business.account.update');
                    Route::post('delete/{id}', [WhatsappBusinessAccountController::class, 'deleteAccount'])->name('whatsapp.business.account.delete');
                    Route::get('get/{id}', [WhatsappBusinessAccountController::class, 'getAccount'])->name('whatsapp.business.account.get');
                });

                // Whats App Config
                Route::get('whatsapp-config', [Marketing\WhatsappConfigController::class, 'index'])->name('whatsapp.config.index');
                Route::get('whatsapp-history/{id}', [Marketing\WhatsappConfigController::class, 'history'])->name('whatsapp.config.history');
                Route::post('whatsapp-config/store', [Marketing\WhatsappConfigController::class, 'store'])->name('whatsapp.config.store');
                Route::post('whatsapp-config/edit', [Marketing\WhatsappConfigController::class, 'edit'])->name('whatsapp.config.edit');
                Route::post('whatsapp-config/delete', [Marketing\WhatsappConfigController::class, 'destroy'])->name('whatsapp.config.delete');
                Route::get('whatsapp-queue/{id}', [Marketing\WhatsappConfigController::class, 'queue'])->name('whatsapp.config.queue');
                Route::post('whatsapp-queue/delete', [Marketing\WhatsappConfigController::class, 'destroyQueue'])->name('whatsapp.config.delete_queue');
                Route::post('whatsapp-queue/delete_all/', [Marketing\WhatsappConfigController::class, 'destroyQueueAll'])->name('whatsapp.config.delete_all');
                Route::get('whatsapp-queue/delete_queues/{id}', [Marketing\WhatsappConfigController::class, 'clearMessagesQueue'])->name('whatsapp.config.delete_all_queues');
                Route::get('whatsapp-config/get-barcode', [Marketing\WhatsappConfigController::class, 'getBarcode'])->name('whatsapp.config.barcode');
                Route::get('whatsapp-config/get-screen', [Marketing\WhatsappConfigController::class, 'getScreen'])->name('whatsapp.config.screen');
                Route::get('whatsapp-config/delete-chrome', [Marketing\WhatsappConfigController::class, 'deleteChromeData'])->name('whatsapp.config.delete-chrome');
                Route::get('whatsapp-config/restart-script', [Marketing\WhatsappConfigController::class, 'restartScript'])->name('whatsapp.restart.script');
                Route::get('whatsapp-config/logout-script', [Marketing\WhatsappConfigController::class, 'logoutScript'])->name('whatsapp.restart.logout-script');
                Route::get('whatsapp-config/get-status', [Marketing\WhatsappConfigController::class, 'getStatus'])->name('whatsapp.restart.get-status');
                Route::get('whatsapp-config/get-status-info', [Marketing\WhatsappConfigController::class, 'getStatusInfo'])->name('whatsapp.restart.get-status-info');

                Route::get('whatsapp-config/blocked-number', [Marketing\WhatsappConfigController::class, 'blockedNumber'])->name('whatsapp.block.number');

                Route::post('whatsapp-queue/switchBroadcast', [Marketing\BroadcastController::class, 'switchBroadcast'])->name('whatsapp.config.switchBroadcast');

                //Instagram Config

                // Marketing Platform
                Route::get('platforms', [Marketing\MarketingPlatformController::class, 'index'])->name('platforms.index');
                Route::post('platforms/store', [Marketing\MarketingPlatformController::class, 'store'])->name('platforms.store');
                Route::post('platforms/edit', [Marketing\MarketingPlatformController::class, 'edit'])->name('platforms.edit');
                Route::post('platforms/delete', [Marketing\MarketingPlatformController::class, 'destroy'])->name('platforms.delete');

                Route::get('broadcast', [Marketing\BroadcastController::class, 'index'])->name('broadcasts.index');
                Route::get('broadcast/dnd', [Marketing\BroadcastController::class, 'addToDND'])->name('broadcast.add.dnd');
                Route::get('broadcast/gettaskremark', [Marketing\BroadcastController::class, 'getBroadCastRemark'])->name('broadcast.gets.remark');
                Route::post('broadcast/addremark', [Marketing\BroadcastController::class, 'addRemark'])->name('broadcast.add.remark');
                Route::get('broadcast/manual', [Marketing\BroadcastController::class, 'addManual'])->name('broadcast.add.manual');
                Route::post('broadcast/update', [Marketing\BroadcastController::class, 'updateWhatsAppNumber'])->name('broadcast.update.whatsappnumber');
                Route::get('broadcast/sendMessage/list', [Marketing\BroadcastController::class, 'broadCastSendMessage'])->name('broadcast.message.send.list');
                Route::post('broadcast/customer/list', [Marketing\BroadcastController::class, 'getCustomerBroadcastList'])->name('broadcast.customer.list');
                Route::post('broadcast/global/save', [Marketing\BroadcastController::class, 'saveGlobalValues'])->name('broadcast.global.save');
                Route::post('broadcast/enable/count', [Marketing\BroadcastController::class, 'getCustomerCountEnable'])->name('broadcast.enable.count');
                Route::get('broadcast/sendMessage/list', [Marketing\BroadcastController::class, 'broadCastSendMessage'])->name('broadcast.message.send.list');
                Route::post('broadcast/customer/list', [Marketing\BroadcastController::class, 'getCustomerBroadcastList'])->name('broadcast.customer.list');
                Route::post('broadcast/global/save', [Marketing\BroadcastController::class, 'saveGlobalValues'])->name('broadcast.global.save');
                Route::post('broadcast/enable/count', [Marketing\BroadcastController::class, 'getCustomerCountEnable'])->name('broadcast.enable.count');

                Route::get('instagram-broadcast', [Marketing\BroadcastController::class, 'instagram']);

                Route::get('facebook-broadcast', [Marketing\BroadcastController::class, 'facebook']);

                Route::get('mailinglist', [Marketing\MailinglistController::class, 'index'])->name('mailingList');
                Route::get('mailinglist-log', [Marketing\MailinglistController::class, 'getlog'])->name('mailingList.log');
                Route::get('mailinglist-flowlog', [Marketing\MailinglistController::class, 'flowlog'])->name('mailingList.flowlog');
                Route::get('mailinglist-customerlog', [Marketing\MailinglistController::class, 'customerlog'])->name('mailingList.customerlog');

                //  Route::get('mailinglist-flowlog', 'MailinglistController@flowlog')->name('mailingList.flowlog');
                Route::get('mailinglist/{id}', [Marketing\MailinglistController::class, 'show'])->name('mailingList.single');

                Route::get('mailinglist/edit/{id}', [Marketing\MailinglistController::class, 'edit'])->name('mailingList.edit');
                Route::post('mailinglist/update/{id}', [Marketing\MailinglistController::class, 'update'])->name('mailingList.update');

                Route::get('mailinglist/add/{id}/{email}', [Marketing\MailinglistController::class, 'addToList'])->name('mailingList.add_to_list');
                Route::get('mailinglist/delete/{id}/{email}', [Marketing\MailinglistController::class, 'delete'])->name('mailingList.delete');
                Route::get('mailinglist/list/delete/{id}', [Marketing\MailinglistController::class, 'deleteList'])->name('mailingList.delete.list');
                Route::post('mailinglist/create', [Marketing\MailinglistController::class, 'create'])->name('mailingList.create');
                Route::get('mailinglist-add-manual', [Marketing\MailinglistController::class, 'addManual'])->name('mailinglist.add.manual');
                Route::post('addRemark', [Marketing\MailinglistController::class, 'addRemark'])->name('mailingList.addRemark');
                Route::get('gettaskremark', [Marketing\MailinglistController::class, 'getBroadCastRemark'])->name('mailingList.gets.remark');
                Route::post('mailinglist/customer/{id}/source', [Marketing\MailinglistController::class, 'updateCustomerSource'])->name('mailingList.customer.source');

                //Email Leads
                Route::get('emailleads', [Marketing\EmailLeadsController::class, 'index'])->name('emailleads');
                Route::any('emailleads/import', [Marketing\EmailLeadsController::class, 'import'])->name('emailleads.import');
                Route::post('emailleads/assign', [Marketing\EmailLeadsController::class, 'assignList'])->name('emailleads.assign');
                Route::get('emailleads/export', [Marketing\EmailLeadsController::class, 'export'])->name('emailleads.export');
                Route::get('emailleads/show/{id}', [Marketing\EmailLeadsController::class, 'show'])->name('emailleads.show');
                Route::get('emailleads/unsubscribe/{lead_id}/{lead_list_id}', [Marketing\EmailLeadsController::class, 'unsubscribe'])->name('emailleads.unsubscribe');

                Route::get('services', [Marketing\ServiceController::class, 'index'])->name('services');
                Route::post('services/store', [Marketing\ServiceController::class, 'store'])->name('services.store');
                Route::post('services/destroy', [Marketing\ServiceController::class, 'destroy'])->name('services.destroy');
                Route::post('services/update', [Marketing\ServiceController::class, 'update'])->name('services.update');

                Route::get('mailinglist-templates', [Marketing\MailinglistTemplateController::class, 'index'])->name('mailingList-template');
                Route::get('mailinglist-ajax', [Marketing\MailinglistTemplateController::class, 'ajax']);
                Route::post('mailinglist-templates/store', [Marketing\MailinglistTemplateController::class, 'store'])->name('mailingList-template.store');
                Route::post('mailinglist-templates/category/store', [Marketing\MailinglistTemplateCategoryController::class, 'store'])->name('mailingList.category.store');
                Route::post('mailinglist-templates/saveimagesfile', [Marketing\MailinglistTemplateController::class, 'saveimagesfile']);

                Route::post('mailinglist-templates/images_file', [Marketing\MailinglistTemplateController::class, 'images_file']);

                Route::prefix('mailinglist-templates/{id}')->group(function () {
                    Route::get('delete', [Marketing\MailinglistTemplateController::class, 'delete'])->name('mailingList-template.delete');
                });

                Route::get('mailinglist-emails', [Marketing\MailinglistEmailController::class, 'index'])->name('mailingList-emails');
                Route::post('mailinglist-ajax-index', [Marketing\MailinglistEmailController::class, 'ajaxIndex']);
                Route::post('mailinglist-ajax-store', [Marketing\MailinglistEmailController::class, 'store']);
                Route::post('mailinglist-ajax-show', [Marketing\MailinglistEmailController::class, 'showMailingList']);
                Route::post('mailinglist-stats', [Marketing\MailinglistEmailController::class, 'getStats']);
            });

            Route::prefix('checkout')->group(function () {
                Route::post('coupons/remarks', [CouponController::class, 'saveRemarks'])->name('coupons.saveremarks');
                Route::post('coupons/getremarks', [CouponController::class, 'getRemarksHistories'])->name('coupons.getremarks');
                Route::post('coupons-column-visbility', [CouponController::class, 'columnVisbilityUpdate'])->name('coupons.column.update');
                Route::post('coupons/statuscolor', [CouponController::class, 'statuscolor'])->name('coupons.statuscolor');
                Route::post('coupons/store', [CouponController::class, 'store'])->name('coupons.store');
                Route::post('coupons/{id}', [CouponController::class, 'update']);
                Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
                Route::post('coupons/load', [CouponController::class, 'loadData']);
                Route::get('coupons/load', [CouponController::class, 'loadData']);
                Route::delete('coupons/{id}', [CouponController::class, 'destroy']);
                Route::get('coupons/{id}/report', [CouponController::class, 'showReport']);
                Route::get('coupons/report', [CouponController::class, 'showReport']);

                Route::post('/coupon-code-rules', [CouponController::class, 'addRules'])->name('couponcode.store');
                Route::post('/rule-details', [CouponController::class, 'getCouponCodeRuleById'])->name('rule_details');
                Route::post('/sales-rules-update', [CouponController::class, 'updateRules'])->name('salesrules.update');
                Route::post('/generate-code', [CouponController::class, 'generateCouponCode'])->name('generateCode');
                Route::post('/getWebsiteByStore', [CouponController::class, 'getWebsiteByStore'])->name('getWebsiteByStore');
                Route::post('/delete-coupon', [CouponController::class, 'deleteCouponByCode'])->name('deleteCouponByCode');
                Route::any('/delete-rules/{id}', [CouponController::class, 'deleteCouponCodeRuleById'])->name('delete-rules');

                Route::post('/quick-coupon-code-rules', [CouponController::class, 'shortCutFroCreateCoupn'])->name('quick.couponcode.store');
                Route::post('/send-coupons', [CouponController::class, 'sendCoupons'])->name('coupons.send');
                Route::get('log-coupon-code-rule-ajax', [CouponController::class, 'logCouponCodeRuleAjax'])->name('couponcoderule.log.ajax');
            });

            // Keyword Assign
            Route::prefix('keywordassign')->group(function () {
                Route::get('/', [KeywordassignController::class, 'index'])->name('keywordassign.index');
                Route::get('load', [KeywordassignController::class, 'loadData']);
                Route::get('create', [KeywordassignController::class, 'create'])->name('keywordassign.create');
                Route::post('store', [KeywordassignController::class, 'store'])->name('keywordassign.store');
                Route::post('taskcategory', [KeywordassignController::class, 'taskcategory'])->name('keywordassign.taskcategory');
                Route::get('{id}', [KeywordassignController::class, 'edit'])->name('keywordassign.edit');
                Route::post('{id}/update', [KeywordassignController::class, 'update'])->name('keywordassign.update');
                Route::get('{id}/destroy', [KeywordassignController::class, 'destroy'])->name('keywordassign.destroy');
            });
            Route::get('keywordreponse/logs', [KeywordassignController::class, 'keywordreponse_logs'])->name('keywordreponse.logs'); //Purpose : add route for Keyword logs - DEVTASK-4233

            Route::get('/customer-reviews', [ProductController::class, 'customerReviews'])->name('product.customer-reviews');
            Route::post('delete/review', [ProductController::class, 'deleteReview'])->name('product.delete-review');
            Route::post('approve/review', [ProductController::class, 'approveReview'])->name('product.click-approve');

            Route::post('attachImages/queue', [ProductController::class, 'queueCustomerAttachImages'])->name('attachImages.queue');
            Route::post('attachImages/whatsapp', [ProductController::class, 'sendNowCustomerAttachImages'])->name('attachImages.whatsapp');

            Route::prefix('tmp-task')->group(function () {
                Route::get('import-leads', [TmpTaskController::class, 'importLeads'])->name('importLeads');
            });
            // this is temp action
            Route::get('update-purchase-order-product', [PurchaseController::class, 'syncOrderProductId']);
            Route::get('update-media-directory', [TmpController::class, 'updateImageDirectory']);
            Route::resource('page-notes-categories', PageNotesCategoriesController::class);

            Route::prefix('chat-bot')->group(function () {
                Route::get('/connection', [ChatBotController::class, 'connection']);
            });

            // Scrap Logs
            Route::prefix('scrap-logs')->group(function () {
                Route::get('/', [ScrapLogsController::class, 'index']);
                Route::get('get-scrap-logs-status', [ScrapLogsController::class, 'getScrapLogsStatus'])->name('get.scrap.logs.status');
                Route::post('status/save', [ScrapLogsController::class, 'updateLogStatus']);
                Route::get('log-data', [ScrapLogsController::class, 'logdata'])->name('scrap.logdata');
                Route::get('{name}', [ScrapLogsController::class, 'indexByName']);
                Route::get('fetch/{name}/{date}', [ScrapLogsController::class, 'filter']);

                Route::get('file-view/{filename}/{foldername}', [ScrapLogsController::class, 'fileView']);
                Route::get('log-history/{filename}', [ScrapLogsController::class, 'loghistory'])->name('scarp.loghistory');
                Route::get('history/{filename}', [ScrapLogsController::class, 'history'])->name('scarp.history');

                Route::post('status/store', [ScrapLogsController::class, 'store']);
            });

            Route::get('fetchlog', [ScrapLogsController::class, 'fetchlog']);
            Route::get('filtertosavelogdb', [ScrapLogsController::class, 'filtertosavelogdb']);

            Route::put('supplier/language-translate/{id}', [SupplierController::class, 'languageTranslate']);
            Route::put('supplier/priority/{id}', [SupplierController::class, 'priority']);
            Route::get('temp-task/product-creator', [TmpTaskController::class, 'importProduct']);

            // Website
            Route::prefix('website')->group(function () {
                Route::get('website-store-log', [WebsiteLogController::class, 'store'])->name('website.store.log');
                Route::get('website-log-file-view/{path?}', [WebsiteLogController::class, 'websiteLogFileView'])->name('website.log.file.view');
                Route::get('log/file-list', [WebsiteLogController::class, 'index'])->name('website.file.list.log');
                Route::get('search/log/file-list', [WebsiteLogController::class, 'searchWebsiteLog'])->name('search.website.file.list.log');
                Route::get('log/view', [WebsiteLogController::class, 'websiteLogStoreView'])->name('website.log.view');
                Route::get('search/log/view', [WebsiteLogController::class, 'searchWebsiteLogStoreView'])->name('website.search.log.view');
                Route::get('search/log/truncate', [WebsiteLogController::class, 'WebsiteLogTruncate'])->name('website.log.truncate');
                Route::get('search/log/error', [WebsiteLogController::class, 'websiteErrorShow'])->name('website.error.show');
                Route::get('command/log', [WebsiteLogController::class, 'runWebsiteLogCommand'])->name('website.command-log');
                Route::get('search/insert/code-shortcut', [WebsiteLogController::class, 'websiteInsertCodeShortcut'])->name('website.insert.code.shortcut');

                Route::post('status/create', [WebsiteLogController::class, 'websiteLogsStatusCreate'])->name('website.status.create');
                Route::get('countdevtask/{id}', [WebsiteLogController::class, 'taskCount']);
                Route::post('updatestatus', [WebsiteLogController::class, 'updateStatus'])->name('website.updatestatus');
                Route::get('status/histories/{id}', [WebsiteLogController::class, 'websitelogsStatusHistories'])->name('website.status.histories');
                Route::post('updateuser', [WebsiteLogController::class, 'updateUser'])->name('website.updateuser');
                Route::get('user/histories/{id}', [WebsiteLogController::class, 'websitelogsUserHistories'])->name('website.user.histories');
            });

            // Uicheck
            Route::prefix('uicheck')->group(function () {
                Route::get('/', [UicheckController::class, 'index'])->name('uicheck');
                Route::post('store', [UicheckController::class, 'store'])->name('uicheck.store');
                Route::post('dev/status/history', [UicheckController::class, 'getUiDeveloperStatusHistoryLog'])->name('uicheck.dev.status.history');
                Route::post('admin/status/history', [UicheckController::class, 'getUiAdminStatusHistoryLog'])->name('uicheck.admin.status.history');
                Route::post('issue/history', [UicheckController::class, 'getUiIssueHistoryLog'])->name('uicheck.get.issue.history');
                Route::post('user/access', [UicheckController::class, 'access'])->name('uicheck.user.access');
                Route::post('document/upload', [UicheckController::class, 'upload_document'])->name('uicheck.upload-document');
                Route::get('get-document', [UicheckController::class, 'getDocument']);
                Route::post('type/create', [UicheckController::class, 'typeStore'])->name('uicheck.type.store');
                Route::post('type/save', [UicheckController::class, 'typeSave'])->name('uicheck.type.save');
                Route::post('message/history', [UicheckController::class, 'getUiCheckMessageHistoryLog'])->name('uicheck.get.message.history');
                Route::post('set/message/history', [UicheckController::class, 'CreateUiMessageHistoryLog'])->name('uicheck.set.message.history');
                Route::post('get/assign/history', [UicheckController::class, 'getUiCheckAssignToHistoryLog'])->name('uicheck.get.assign.history');
                Route::post('set/duplicate/category', [UicheckController::class, 'createDuplicateCategory'])->name('uicheck.set.duplicate.category');
                Route::post('set/language', [UicheckController::class, 'updateLanguage'])->name('uicheck.set.language');
                Route::post('get/message/history/language', [UicheckController::class, 'getuicheckLanUpdateHistory'])->name('uicheck.get.message.language');
                Route::post('create/attachment', [UicheckController::class, 'saveDocuments'])->name('uicheck.create.attachment');
                Route::get('get/attachment', [UicheckController::class, 'listDocuments'])->name('uicheck.get.attachment');
                Route::post('delete/attachment', [UicheckController::class, 'deleteDocument'])->name('uicheck.delete.attachment');
                Route::post('language/flag', [UicheckController::class, 'languageFlag'])->name('uicheck.language.flag');
                Route::post('translation/flag', [UicheckController::class, 'translationFlag'])->name('uicheck.translation.flag');

                // 5 Device

                Route::post('set/device', [UicheckController::class, 'updateDevice'])->name('uicheck.set.device');
                Route::post('device/upload-documents', [UicheckController::class, 'uploadDocuments'])->name('ui.dev.upload-documents');

                Route::post('get/message/history/dev', [UicheckController::class, 'getuicheckDevUpdateHistory'])->name('uicheck.get.message.dev');

                Route::post('create/dev/attachment', [UicheckController::class, 'saveDevDocuments'])->name('uicheck.create.dev.attachment');
                Route::get('get/dev/attachment', [UicheckController::class, 'devListDocuments'])->name('uicheck.get.dev.attachment');
                Route::post('dev/delete/attachment', [UicheckController::class, 'deleteDevDocument'])->name('uicheck.dev.delete.attachment');
                Route::post('device/status', [UicheckController::class, 'updateDeviceStatus'])->name('uicheck.device.status');
            });

            Route::prefix('variant')->group(function () {
                Route::post('/', [KeywordVariantController::class, 'create'])->name('add.keyword.variant');
                Route::get('/', [KeywordVariantController::class, 'index'])->name('list.keyword.variant');
                Route::delete('/{id}', [KeywordVariantController::class, 'delete'])->name('delete.keyword.variant');
            });

            Route::get('brand/search', [GoogleSearchController::class, 'searchBrand1'])->name('search.brand');

            Route::prefix('uicheck')->group(function () {
                Route::get('get', [UicheckController::class, 'get'])->name('uicheck.get');
                Route::get('responsive', [UicheckController::class, 'responseDevicePage'])->name('uicheck.responsive');
                Route::post('statuscolor', [UicheckController::class, 'statuscolor'])->name('uicheck.statuscolor');
                Route::get('get-device-builder-datas', [UicheckController::class, 'getDeviceBuilderDatas'])->name('uicheck.get-device-builder-datas');
                Route::get('device-builder-datas', [UicheckController::class, 'deviceBuilderDatas'])->name('uicheck.device-builder-datas');
                Route::get('get-builder-html/{id}', [UicheckController::class, 'getBuilderHtml'])->name('uicheck.get-builder-html');
                Route::get('get-builder-download-html/{id}', [UicheckController::class, 'getBuilderDownloadHtml'])->name('uicheck.get-builder-download-html');
                Route::get('get-builder-download-history/{id}', [UicheckController::class, 'getBuilderDownloadHistory'])->name('uicheck.get-builder-download-history');
                Route::post('fetch-device-builder-data', [UicheckController::class, 'fetchDeviceBuilderData'])->name('uicheck.fetch-device-builder-data');
                Route::post('device-builder-datas/store-remark', [UicheckController::class, 'storeBuilderDataRemark'])->name('uicheck.store.builder-data-remark');
                Route::post('device-builder-datas/store-task', [UicheckController::class, 'builderIOTaskstore'])->name('uicheck.store.builder-io-task');
                Route::get('device-builder-datas/get-remarks/{id}', [UicheckController::class, 'getBuilderDataRemarks'])->name('uicheck.get.builder-data-remark');
                Route::post('responsive/status', [UicheckController::class, 'responseDeviceStatusChange'])->name('uicheck.responsive.status');
                Route::post('responsive/user/change', [UicheckController::class, 'responseDeviceUserChange'])->name('uicheck.responsive.user.change');
                Route::post('responsive/approve', [UicheckController::class, 'responseDeviceIsApprovedChange'])->name('uicheck.responsive.approve');
                Route::post('get/responsive/status/history', [UicheckController::class, 'responseDeviceStatusHistory'])->name('get.responsive.status.history');
                Route::get('translation', [UicheckController::class, 'responseTranslatorPage'])->name('uicheck.translation');
                Route::post('translation/status', [UicheckController::class, 'translatorStatusChange'])->name('uicheck.translator.status');
                Route::post('get/translator/status/history', [UicheckController::class, 'translatorStatusHistory'])->name('get.translator.status.history');
                Route::post('assign-user', [UicheckController::class, 'assignNewUser'])->name('uicheck.assignNewuser');
                Route::get('user-history', [UicheckController::class, 'userHistory'])->name('uicheck.userhistory');
                Route::post('responsive/upload-file', [UicheckController::class, 'uploadFile'])->name('uicheck.upload-file');
                Route::get('responsive/files/record', [UicheckController::class, 'getUploadedFilesList'])->name('uicheck.files.record');
                Route::post('add-user', [UicheckController::class, 'addNewUser'])->name('uicheck.addNewuser');
                Route::get('device-logs', [UicheckController::class, 'deviceLogs'])->name('uicheck.device-logs');
                Route::get('device-histories', [UicheckController::class, 'deviceHistories'])->name('uicheck.device-histories');
                Route::post('device-history/time-approve', [UicheckController::class, 'deviceHistoryIstimeApprove'])->name('uicheck.device-history.time-approve');
                Route::post('set/device-log', [UicheckController::class, 'setDeviceLog'])->name('uicheck.set.device-log');
                Route::post('bulk-delete', [UicheckController::class, 'bulkDelete'])->name('uicheck.bulk-delete');
                Route::post('bulk-show', [UicheckController::class, 'bulkShow'])->name('uicheck.bulk-show');
                Route::post('bulk-hide', [UicheckController::class, 'bulkHide'])->name('uicheck.bulk-hide');
                Route::post('bulk-delete-user-wise', [UicheckController::class, 'bulkDeleteUserWise'])->name('uicheck.bulk-delete-user-wise');
                Route::post('bulk-delete-user-wise-multiple', [UicheckController::class, 'bulkDeleteUserWiseMultiple'])->name('uicheck.bulk-delete-user-wise-multiple');
                Route::get('user-access-list', [UicheckController::class, 'userAccessList'])->name('uicheck.user-access-list');
                Route::post('device-builder-status-store', [UicheckController::class, 'deviceBuilderStatusStore'])->name('uicheck.device-builder.status.store');
                Route::post('device-builder-status-update', [UicheckController::class, 'deviceBuilderStatusColorUpdate'])->name('uicheck.device-builder.status.color.update');
                Route::post('device-change-status', [UicheckController::class, 'updateDeviceUpdateStatus'])->name('uicheck.device.update.status');
                Route::get('device-builder-datas/get-statuss/{id}', [UicheckController::class, 'getBuilderDataStatus'])->name('uicheck.get.builder-data-status');

                Route::prefix('history')->group(function () {
                    Route::get('all', [UicheckController::class, 'historyAll'])->name('uicheck.history.all');
                    Route::get('dates', [UicheckController::class, 'historyDates'])->name('uicheck.history.dates');
                });

                Route::prefix('update')->group(function () {
                    Route::post('dates', [UicheckController::class, 'updateDates'])->name('uicheck.update.dates');
                    Route::post('lock', [UicheckController::class, 'updateLock'])->name('uicheck.update.lock');
                });
            });

            Route::prefix('google')->group(function () {
                Route::get('developer-api/anrfilter', [GoogleDeveloperController::class, 'getDeveloperApianrfilter']);
                Route::get('developer-api/crashfilter', [GoogleDeveloperController::class, 'getDevelopercrashfilter']);
                Route::get('/get-keywords', [GoogleSearchController::class, 'index'])->name('google.search-keyword.list');
                Route::resource('/search/keyword', GoogleSearchController::class);
                Route::post('/search/generate-keyword', [GoogleSearchController::class, 'generateKeywords'])->name('keyword.generate');
                Route::get('/search/keyword-priority', [GoogleSearchController::class, 'markPriority'])->name('google.search.keyword.priority');
                Route::get('/search/keyword', [GoogleSearchController::class, 'index'])->name('google.search.keyword');
                Route::get('/search/results', [GoogleSearchController::class, 'searchResults'])->name('google.search.results');
                Route::get('/search/scrap', [GoogleSearchController::class, 'callScraper'])->name('google.search.keyword.scrap');
                Route::post('/search/delete/{id?}', [GoogleSearchController::class, 'destroy'])->name('google.search.keyword.delete');
                Route::resource('/affiliate/keyword', GoogleAffiliateController::class)->names([
                    'index' => 'google.affiliate.keyword.index',
                    'store' => 'google.affiliate.keyword.store',
                    'show' => 'google.affiliate.keyword.show',
                    'edit' => 'google.affiliate.keyword.edit',
                    'update' => 'google.affiliate.keyword.update',
                    'destroy' => 'google.affiliate.keyword.destroy',
                ])->except(['create']);
                Route::get('/affiliate/keyword', [GoogleAffiliateController::class, 'index'])->name('google.affiliate.keyword');
                Route::get('/affiliate/keyword-priority', [GoogleAffiliateController::class, 'markPriority'])->name('google.affiliate.keyword.priority');
                Route::get('/affiliate/results', [GoogleAffiliateController::class, 'searchResults'])->name('google.affiliate.results');
                Route::delete('/affiliate/results/{id}', [GoogleAffiliateController::class, 'deleteSearch']);
                Route::delete('/search/results/{id}', [GoogleSearchController::class, 'deleteSearch']);
                Route::post('affiliate/flag', [GoogleAffiliateController::class, 'flag'])->name('affiliate.flag');
                Route::post('affiliate/email/send', [GoogleAffiliateController::class, 'emailSend'])->name('affiliate.email.send');
                Route::get('/affiliate/scrap', [GoogleAffiliateController::class, 'callScraper'])->name('google.affiliate.keyword.scrap');
                Route::get('developer-api/crash', [GoogleDeveloperController::class, 'getDeveloperApicrash'])->name('google.developer-api.crash');
                Route::get('developer-api/anr', [GoogleDeveloperController::class, 'getDeveloperApianr'])->name('google.developer-api.anr');

                Route::get('developer-api/logs', [GoogleDeveloperLogsController::class, 'index'])->name('google.developer-api.logs');

                Route::get('developer-api/logsfilter', [GoogleDeveloperLogsController::class, 'logsfilter']);
            });

            Route::prefix('github')->group(function () {
                Route::get('/new-pullRequests', [Github\RepositoryController::class, 'listAllNewPullRequests']);
                Route::get('/new-pr-activities', [Github\RepositoryController::class, 'listAllNewPrActivities']);
            });

            Route::get('/calendar', [UserEventController::class, 'index']);
            Route::get('/calendar/events', [UserEventController::class, 'list']);
            Route::get('/calendar/events/showcreateeventmodal', [UserEventController::class, 'showCreateEventModal'])->name('calendar.event.showcreateeventmodal');
            Route::post('/calendar/events', [UserEventController::class, 'createEvent'])->name('calendar.event.create');
            Route::get('/calendar/events/edit/{id}', [UserEventController::class, 'GetEditEvent'])->name('calendar.event.edit');
            Route::post('/calendar/events/update', [UserEventController::class, 'UpdateEvent'])->name('calendar.event.update');
            Route::post('/calendar/events/stop', [UserEventController::class, 'stopEvent'])->name('calendar.event.stop');
            Route::put('/calendar/events/{id}', [UserEventController::class, 'editEvent']);
            Route::delete('/calendar/events/{id}', [UserEventController::class, 'removeEvent']);
            Route::get('updateLog', [UpdateLogController::class, 'index'])->name('updateLog.get');
            Route::get('updateLog/search', [UpdateLogController::class, 'search'])->name('updateLog.get.search');
            Route::delete('updateLog/delete', [UpdateLogController::class, 'destroy'])->name('updateLog.delete');
            Route::get('updateLog/request_headers/show', [UpdateLogController::class, 'requestHeaderShow'])->name('updateLog.request.header.show');

            Route::get('event/getSchedules', [EventController::class, 'getSchedules'])->name('event.getSchedules');
            Route::get('event/get-event-alerts', [EventController::class, 'getEventAlerts'])->name('event.getEventAlerts');
            Route::post('event/save-alert-log', [EventController::class, 'saveAlertLog'])->name('event.saveAlertLog');
            Route::delete('event/delete-schedule/{id}', [EventController::class, 'deleteSchedule'])->name('event.deleteSchedule');
            Route::get('all/events', [EventController::class, 'publicEvents'])->name('event.public');
            Route::post('event/categor/store', [EventController::class, 'eventCategoryStore'])->name('event.category.store');
            Route::post('event/send-appointment-request', [EventController::class, 'sendAppointmentRequest'])->name('event.sendAppointmentRequest');
            Route::post('event/update-appointment-request', [EventController::class, 'updateAppointmentRequest'])->name('event.updateAppointmentRequest');
            Route::post('event/update-user-appointment-request', [EventController::class, 'updateuserAppointmentRequest'])->name('event.updateuserAppointmentRequest');
            Route::get('event/get-appointment-request', [EventController::class, 'getAppointmentRequest'])->name('event.getAppointmentRequest');
            Route::resource('event', EventController::class);
            Route::post('event/reschedule', [EventController::class, 'reschedule'])->name('event.reschedule');
            Route::put('event/stop-recurring/{id}', [EventController::class, 'stopRecurring'])->name('event.stop-recurring');
            Route::post('event/add/remark', [EventController::class, 'addEventsRemarks'])->name('event.remark.add');
            Route::post('event/list/remark', [EventController::class, 'getEventremarkList'])->name('event.remark.list');
            Route::get('/calendar/getObjectEmail', [CalendarController::class, 'getEmailOftheSelectedObject'])->name('calendar.getObjectEmail');
            Route::post('/status/update', [EventController::class, 'statusUpdate'])->name('allevents.status.update');
            Route::post('/useronlinestatus/update', [EventController::class, 'userOnlineStatusUpdate'])->name('useronlinestatus.status.update');
            Route::get('user/detailsget', [EventController::class, 'getUserDetailsForOnline'])->name('getuserforonline');

            Route::put('customer/language-translate/{id}', [CustomerController::class, 'languageTranslate']);
            Route::get('get-language', [CustomerController::class, 'getLanguage'])->name('livechat.customer.language');

            Route::get('/vendor-form', [VendorSupplierController::class, 'vendorForm'])->name('developer.vendor.form');
            Route::get('/supplier-form', [VendorSupplierController::class, 'supplierForm'])->name('developer.supplier.form');

            Route::prefix('product-category')->group(function () {
                Route::get('/history', [ProductCategoryController::class, 'history']);
                Route::get('/', [ProductCategoryController::class, 'index'])->name('product.category.index.list');
                Route::get('/records', [ProductCategoryController::class, 'records'])->name('product.category.records');
                Route::post('/update-category-assigned', [ProductCategoryController::class, 'updateCategoryAssigned'])->name('product.category.update-assigned');
            });

            Route::prefix('product-color')->group(function () {
                Route::get('/history', [ProductColorController::class, 'history']);
                Route::get('/', [ProductColorController::class, 'index'])->name('product.color.index.list');
                Route::get('/records', [ProductColorController::class, 'records'])->name('product.color.records');
                Route::post('/update-color-assigned', [ProductColorController::class, 'updateCategoryAssigned'])->name('product.color.update-assigned');
            });

            Route::prefix('listing-history')->group(function () {
                Route::get('/', [ListingHistoryController::class, 'index'])->name('listing.history.index');
                Route::get('/records', [ListingHistoryController::class, 'records']);
            });

            Route::prefix('ads')->group(function () {
                Route::prefix('account')->group(function () {
                    Route::post('store', [AdsController::class, 'saveaccount'])->name('ads.saveaccount');
                });
                Route::get('/', [AdsController::class, 'index'])->name('ads.index');
                Route::get('/records', [AdsController::class, 'records'])->name('ads.records');
                Route::post('/savecampaign', [AdsController::class, 'savecampaign'])->name('ads.savecampaign');
                Route::post('/savegroup', [AdsController::class, 'savegroup'])->name('ads.savegroup');
                Route::get('/getgroups', [AdsController::class, 'getgroups'])->name('ads.getgroups');
                Route::post('/adsstore', [AdsController::class, 'adsstore'])->name('ads.adsstore');
            });

            Route::prefix('google-remarketing-campaigns')->group(function () {
                Route::post('create', [GoogleAdsRemarketingController::class, 'createCampaign'])->name('googleremarketingcampaigns.createCampaign');
                Route::post('update', [GoogleAdsRemarketingController::class, 'updateCampaign'])->name('googleremarketingcampaigns.updateCampaign');
            });

            Route::prefix('google-campaigns')->group(function () {
                Route::get('/', [GoogleCampaignsController::class, 'index'])->name('googlecampaigns.index');
                Route::get('/list', [GoogleCampaignsController::class, 'campaignslist'])->name('googlecampaigns.campaignslist');
                Route::get('/ads/list', [GoogleCampaignsController::class, 'adslist'])->name('googlecampaigns.adslist');
                Route::get('/responsive-display-ads/list', [GoogleCampaignsController::class, 'display_ads'])->name('googlecampaigns.displayads');
                Route::get('/ads-group-list', [GoogleCampaignsController::class, 'adsgroupslist'])->name('googleadsaccount.adsgroupslist');
                Route::get('/appad-list', [GoogleCampaignsController::class, 'appadlist'])->name('googleadsaccount.appadlist');
                Route::get('/create', [GoogleCampaignsController::class, 'createPage'])->name('googlecampaigns.createPage');
                Route::post('/create', [GoogleCampaignsController::class, 'createCampaign'])->name('googlecampaigns.createCampaign');
                Route::get('/update/{id}', [GoogleCampaignsController::class, 'updatePage'])->name('googlecampaigns.updatePage');
                Route::post('/update', [GoogleCampaignsController::class, 'updateCampaign'])->name('googlecampaigns.updateCampaign');
                Route::delete('/delete/{id}', [GoogleCampaignsController::class, 'deleteCampaign'])->name('googlecampaigns.deleteCampaign');
                //google adwords account
                Route::get('/ads-account', [GoogleAdsAccountController::class, 'index'])->name('googleadsaccount.index');
                Route::get('/ads-account/create', [GoogleAdsAccountController::class, 'createGoogleAdsAccountPage'])->name('googleadsaccount.createPage');
                Route::post('/ads-account/create', [GoogleAdsAccountController::class, 'createGoogleAdsAccount'])->name('googleadsaccount.createAdsAccount');
                Route::get('/ads-account/update/{id}', [GoogleAdsAccountController::class, 'editeGoogleAdsAccountPage'])->name('googleadsaccount.updatePage');
                Route::post('/ads-account/update', [GoogleAdsAccountController::class, 'updateGoogleAdsAccount'])->name('googleadsaccount.updateAdsAccount');
                Route::delete('/ads-account/delete/{id}', [GoogleAdsAccountController::class, 'deleteGoogleAdsAccount'])->name('googleadsaccount.deleteGoogleAdsAccount');
                Route::post('/refresh-token', [GoogleAdsAccountController::class, 'refreshToken'])->name('googleadsaccount.refresh_token');
                Route::get('/get-refresh-token', [GoogleAdsAccountController::class, 'getRefreshToken'])->name('googleadsaccount.get-refresh-token');
                Route::prefix('{id}')->group(function () {
                    Route::prefix('adgroups')->group(function () {
                        Route::get('/', [GoogleAdGroupController::class, 'index'])->name('adgroup.index');
                        Route::get('/create', [GoogleAdGroupController::class, 'createPage'])->name('adgroup.createPage');
                        Route::post('/create', [GoogleAdGroupController::class, 'createAdGroup'])->name('adgroup.createAdGroup');
                        Route::post('/generate-keywords', [GoogleAdGroupController::class, 'generateKeywords'])->name('adgroup.generateKeywords');
                        Route::get('/update/{adGroupId}', [GoogleAdGroupController::class, 'updatePage'])->name('adgroup.updatePage');
                        Route::post('/update', [GoogleAdGroupController::class, 'updateAdGroup'])->name('adgroup.updateAdGroup');
                        Route::delete('/delete/{adGroupId}', [GoogleAdGroupController::class, 'deleteAdGroup'])->name('adgroup.deleteAdGroup');

                        Route::prefix('{adGroupId}')->group(function () {
                            Route::prefix('ads')->group(function () {
                                Route::get('/', [GoogleAdsController::class, 'index'])->name('googleads.index');
                                Route::get('/create', [GoogleAdsController::class, 'createPage'])->name('ads.createPage');
                                Route::post('/create', [GoogleAdsController::class, 'createAd'])->name('ads.craeteAd');
                                Route::delete('/delete/{adId}', [GoogleAdsController::class, 'deleteAd'])->name('ads.deleteAd');
                            });
                        });

                        Route::prefix('{adGroupId}')->group(function () {
                            Route::prefix('responsive-display-ad')->group(function () {
                                Route::get('/', [GoogleResponsiveDisplayAdController::class, 'index'])->name('responsive-display-ad.index');
                                Route::get('/create', [GoogleResponsiveDisplayAdController::class, 'createPage'])->name('responsive-display-ad.createPage');
                                Route::post('/create', [GoogleResponsiveDisplayAdController::class, 'createAd'])->name('responsive-display-ad.craeteAd');
                                Route::delete('/delete/{adId}', [GoogleResponsiveDisplayAdController::class, 'deleteAd'])->name('responsive-display-ad.deleteAd');
                                Route::get('/{adId}', [GoogleResponsiveDisplayAdController::class, 'show'])->name('responsive-display-ad.show');
                            });
                        });

                        Route::prefix('{adGroupId}')->group(function () {
                            Route::prefix('app-ad')->group(function () {
                                Route::get('/', [GoogleAppAdController::class, 'index'])->name('app-ad.index');
                                Route::get('/create', [GoogleAppAdController::class, 'createPage'])->name('app-ad.createPage');
                                Route::post('/create', [GoogleAppAdController::class, 'createAd'])->name('app-ad.craeteAd');
                                Route::get('/{adId}', [GoogleAppAdController::class, 'show'])->name('app-ad.show');
                            });
                        });

                        Route::prefix('{adGroupId}')->group(function () {
                            Route::prefix('ad-group-keyword')->group(function () {
                                Route::get('/', [GoogleAdGroupKeywordController::class, 'index'])->name('ad-group-keyword.index');
                                Route::get('/create', [GoogleAdGroupKeywordController::class, 'createPage'])->name('ad-group-keyword.createPage');
                                Route::post('/create', [GoogleAdGroupKeywordController::class, 'createKeyword'])->name('ad-group-keyword.craeteKeyword');
                                Route::delete('/delete/{keywordId}', [GoogleAdGroupKeywordController::class, 'deleteKeyword'])->name('ad-group-keyword.deleteKeyword');
                            });
                        });

                        Route::prefix('{adGroupId}')->group(function () {
                            Route::prefix('shopping-ad')->group(function () {
                                Route::get('/', [GoogleShoppingAdsController::class, 'index'])->name('shopping-ads.index');
                                Route::post('/create', [GoogleShoppingAdsController::class, 'createAd'])->name('shopping-ads.createAd');
                                Route::delete('/delete/{adId}', [GoogleShoppingAdsController::class, 'deleteAd'])->name('shopping-ads.deleteAd');
                            });
                        });
                    });

                    Route::prefix('google-campaign-location')->group(function () {
                        Route::get('/', [GoogleCampaignLocationController::class, 'index'])->name('google-campaign-location.index');
                        Route::post('/create', [GoogleCampaignLocationController::class, 'createLocation'])->name('google-campaign-location.createLocation');
                        Route::delete('/delete/{locationId}', [GoogleCampaignLocationController::class, 'deleteLocation'])->name('google-campaign-location.deleteLocation');
                    });
                });

                Route::get('google-campaign-location/countries', [GoogleCampaignLocationController::class, 'countries'])->name('google-campaign-location.countries');
                Route::get('google-campaign-location/states', [GoogleCampaignLocationController::class, 'states'])->name('google-campaign-location.states');
                Route::get('google-campaign-location/cities', [GoogleCampaignLocationController::class, 'cities'])->name('google-campaign-location.cities');
                Route::get('google-campaign-location/address', [GoogleCampaignLocationController::class, 'address'])->name('google-campaign-location.address');
                Route::get('/logs', [GoogleAdsLogController::class, 'index'])->name('googleadslogs.index');
                Route::get('/ad-report', [GoogleAdReportController::class, 'index'])->name('googleadreport.index');
            });

            Route::prefix('digital-marketing')->group(function () {
                Route::get('/', [DigitalMarketingController::class, 'index'])->name('digital-marketing.index');
                Route::post('/get-emails', [DigitalMarketingController::class, 'getEmails']);
                Route::get('/records', [DigitalMarketingController::class, 'records'])->name('digital-marketing.records');
                Route::post('/save', [DigitalMarketingController::class, 'save'])->name('digital-marketing.save');
                Route::post('/saveImages', [DigitalMarketingController::class, 'saveImages'])->name('digital-marketing.saveimages');
                Route::prefix('{id}')->group(function () {
                    Route::get('/edit', [DigitalMarketingController::class, 'edit'])->name('digital-marketing.edit');
                    Route::get('/components', [DigitalMarketingController::class, 'components'])->name('digital-marketing.components');
                    Route::post('/components', [DigitalMarketingController::class, 'componentStore'])->name('digital-marketing.components.save');
                    Route::get('/delete', [DigitalMarketingController::class, 'delete'])->name('digital-marketing.delete');
                    Route::get('/files', [DigitalMarketingController::class, 'files'])->name('digital-marketing.files');
                    Route::get('/files-solution', [DigitalMarketingController::class, 'filesSolution'])->name('digital-marketing.filessolution');

                    Route::prefix('solution')->group(function () {
                        Route::get('/', [DigitalMarketingController::class, 'solution'])->name('digital-marketing.solutions');
                        Route::get('/records', [DigitalMarketingController::class, 'solutionRecords'])->name('digital-marketing.solutionrecords');
                        Route::post('/save', [DigitalMarketingController::class, 'solutionSave'])->name('digital-marketing.solution.save');
                        Route::post('/create-usp', [DigitalMarketingController::class, 'solutionCreateUsp'])->name('digital-marketing.solution.create-usp');
                        Route::prefix('{solutionId}')->group(function () {
                            Route::get('/edit', [DigitalMarketingController::class, 'solutionEdit'])->name('digital-marketing.solution.edit');
                            Route::get('/delete', [DigitalMarketingController::class, 'solutionDelete'])->name('digital-marketing.solution.delete');
                            Route::post('/save-usp', [DigitalMarketingController::class, 'solutionSaveUsp'])->name('digital-marketing.solutionusp.save');
                            Route::prefix('research')->group(function () {
                                Route::get('/', [DigitalMarketingController::class, 'research'])->name('digital-marketing.solution.research');
                                Route::get('/records', [DigitalMarketingController::class, 'researchRecords'])->name('digital-marketing.solution.research.records');
                                Route::post('/save', [DigitalMarketingController::class, 'researchSave'])->name('digital-marketing.solution.research.save');
                                Route::prefix('{researchId}')->group(function () {
                                    Route::get('/edit', [DigitalMarketingController::class, 'researchEdit'])->name('digital-marketing.solution.research.edit');
                                    Route::get('/delete', [DigitalMarketingController::class, 'researchDelete'])->name('digital-marketing.solution.research.delete');
                                });
                            });
                        });
                    });
                });
            });

            Route::prefix('return-exchange')->group(function () {
                Route::get('/', [ReturnExchangeController::class, 'index'])->name('return-exchange.list');
                Route::get('/records', [ReturnExchangeController::class, 'records'])->name('return-exchange.records');
                Route::post('/statuscolor', [ReturnExchangeController::class, 'statuscolor'])->name('return-exchange.statuscolor');
                Route::get('/model/{id}', [ReturnExchangeController::class, 'getOrders']);
                Route::get('/getProducts/{id}', [ReturnExchangeController::class, 'getProducts']);
                Route::get('/getRefundInfo/{id}', [ReturnExchangeController::class, 'getRefundInfo']);
                Route::post('/model/{id}/save', [ReturnExchangeController::class, 'save'])->name('return-exchange.save');
                Route::post('/updateCustomers', [ReturnExchangeController::class, 'updateCustomer'])->name('return-exchange.updateCusromer');
                Route::post('/createRefund', [ReturnExchangeController::class, 'createRefund'])->name('return-exchange.createRefund');
                Route::post('/updateRefund', [ReturnExchangeController::class, 'updateRefund'])->name('return-exchange.updateRefund');
                Route::post('/update-estimated-date', [ReturnExchangeController::class, 'updateEstmatedDate'])->name('return-exchange.update-estimated-date');
                Route::get('/status', [ReturnExchangeController::class, 'status'])->name('return-exchange.status');
                Route::post('/status', [ReturnExchangeController::class, 'getStatusByWebsite']);
                Route::post('/status/save', [ReturnExchangeController::class, 'statusWebsiteSave']);
                Route::post('/status/fetch-store-status', [ReturnExchangeController::class, 'fetchMagentoStatus'])->name('fetch-magento.status');
                Route::post('/status/store', [ReturnExchangeController::class, 'saveStatusField'])->name('return-exchange.save.status-field');
                Route::post('/status/create', [ReturnExchangeController::class, 'createStatus'])->name('return-exchange.createStatus');
                Route::post('/status/delete', [ReturnExchangeController::class, 'deleteStatus'])->name('return-exchange.deleteStatus');
                Route::post('/addNewReply', [ReturnExchangeController::class, 'addNewReply'])->name('returnexchange.addNewReply');
                Route::post('/update-status', [ReturnExchangeController::class, 'updateExchangeStatuses'])->name('returnexchange.update-status');
                Route::get('/update-status-log/{id?}', [ReturnExchangeController::class, 'listExchangeStatusesLog'])->name('returnexchange.update_status_log');
                Route::post('/status-send-email', [ReturnExchangeController::class, 'updateStatusEmailSend'])->name('return-exchange.status-send-email');
                Route::post('returnexchange-column-visbility', [ReturnExchangeController::class, 'columnVisbilityUpdate'])->name('returnexchange.column.update');
                Route::prefix('{id}')->group(function () {
                    Route::get('/detail', [ReturnExchangeController::class, 'detail'])->name('return-exchange.detail');
                    Route::get('/delete', [ReturnExchangeController::class, 'delete'])->name('return-exchange.delete');
                    Route::get('/history', [ReturnExchangeController::class, 'history'])->name('return-exchange.history');
                    Route::get('/date-history', [ReturnExchangeController::class, 'estimationHistory'])->name('return-exchange.date-history');
                    Route::get('/product', [ReturnExchangeController::class, 'product'])->name('return-exchange.product');
                    Route::post('/update', [ReturnExchangeController::class, 'update'])->name('return-exchange.update');
                    Route::get('/resend-email', [ReturnExchangeController::class, 'resendEmail'])->name('return-exchange.resend-email');
                    Route::get('/re-generate-coupon', [ReturnExchangeController::class, 'regenerateCoupon'])->name('return-exchange.regenerate-coupon');
                    Route::get('/download-pdf', [ReturnExchangeController::class, 'downloadRefundPdf'])->name('return-exchange.download-pdf');
                });
            });

            /**
             * Shipment module
             */
            Route::prefix('shipment')->group(function () {
                Route::post('send/email', [ShipmentController::class, 'sendEmail'])->name('shipment/send/email');
                Route::get('view/sent/email', [ShipmentController::class, 'viewSentEmail'])->name('shipment/view/sent/email');
                Route::get('waybill-track-histories', [ShipmentController::class, 'viewWaybillTrackHistory'])->name('shipment/waybill-track-histories');
                Route::get('{id}/edit', [ShipmentController::class, 'editShipment'])->name('shipment.editShipment');
                Route::post('{id}/save', [ShipmentController::class, 'saveShipment'])->name('shipment.saveShipment');
                Route::get('customer-details/{id}', [ShipmentController::class, 'showCustomerDetails']);
                Route::post('generate-shipment', [ShipmentController::class, 'generateShipment'])->name('shipment/generate');
                Route::get('get-templates-by-name/{name}', [ShipmentController::class, 'getShipmentByName']);
                Route::post('pickup-request', [ShipmentController::class, 'createPickupRequest'])->name('shipment/pickup-request');
                Route::post('save-box-size', [ShipmentController::class, 'saveBoxSize'])->name('shipment.save-box-size');
            });
            Route::resource('shipment', ShipmentController::class);
            Route::get('shipments/payment_info', [ShipmentController::class, 'getPaymentInfo'])->name('shipment.get-payment-info');
            Route::post('shipments/payment_info', [ShipmentController::class, 'savePaymentInfo'])->name('shipment.save-payment-info');

            /**
             * Twilio account management
             */
            Route::prefix('twilio')->group(function () {
                Route::get('manage-twilio-account', [TwilioController::class, 'manageTwilioAccounts'])->name('twilio-manage-accounts');
                Route::post('add-account', [TwilioController::class, 'addAccount'])->name('twilio-add-account');
                Route::get('delete-account/{id}', [TwilioController::class, 'deleteAccount'])->name('twilio-delete-account');
                Route::get('manage-numbers/{id}', [TwilioController::class, 'manageNumbers'])->name('twilio-manage-numbers');
                Route::get('manage-all-numbers/{id?}', [TwilioController::class, 'manageAllNumbers'])->name('twilio.manage.all.numbers');
                Route::get('manage-numbers-popup/{id?}', [TwilioController::class, 'manageNumbersPopup'])->name('twilio.manage.numbers.popup');
                Route::post('add_user', [TwilioController::class, 'manageUsers'])->name('twilio.add_user');
                Route::post('set_website_time', [TwilioController::class, 'setWebsiteTime'])->name('twilio.set_website_time');
                Route::get('get_website_agent', [TwilioController::class, 'getWebsiteAgent'])->name('twilio.get_website_agent');
                Route::post('set_twilio_key_option', [TwilioController::class, 'setTwilioKey'])->name('twilio.set_twilio_key_options');
                Route::post('greeting_message', [TwilioController::class, 'saveTwilioGreetingMessage'])->name('twilio.set_twilio_greeting_message');
                Route::get('get_website_wise_key_data', [TwilioController::class, 'getTwilioKeyData'])->name('twilio.get_website_wise_key_data');
                Route::get('get_website_wise_key_data_options/{web_site_id?}', [TwilioController::class, 'getTwilioKeyDataOptions'])->name('twilio.get_website_wise_key_data_options');
                Route::get('erp/logs', [TwilioController::class, 'twilioErpLogs'])->name('twilio.erp_logs');
                Route::any('call/journey', [TwilioController::class, 'twilioCallJourney'])->name('twilio.call_journey');
                Route::get('webhook-error/logs', [TwilioController::class, 'twilioWebhookErrorLogs'])->name('twilio.webhook.error.logs');
                Route::get('account-logs', [TwilioController::class, 'twilioAccountLogs'])->name('twilio.account_logs');
                Route::get('conditions', [TwilioController::class, 'getConditions'])->name('twilio.conditions');
                Route::get('conditions/status/update', [TwilioController::class, 'updateConditionStatus'])->name('twilio.condition.update');
                Route::post('save-message-tone', [TwilioController::class, 'saveMessageTone'])->name('twilio.save_tone');
                Route::get('message-tones', [TwilioController::class, 'viewMessageTones'])->name('twilio.view_tone');
                Route::get('reject-incoming-call', [TwilioController::class, 'rejectIncomingCall'])->name('twilio.reject_incoming_call');
                Route::get('block-incoming-call', [TwilioController::class, 'blockIncomingCall'])->name('twilio.block_incoming_call');
                Route::get('delivery-logs', [TwilioController::class, 'twilioDeliveryLogs'])->name('twilio.twilio_delivery_logs');
                Route::post('status-colour-update', [TwilioController::class, 'StatusColourUpdate'])->name('twilio-status-colour-update');
                Route::post('assign-number', [TwilioController::class, 'assignTwilioNumberToStoreWebsite'])->name('assign-number-to-store-website');
                Route::post('call-forward', [TwilioController::class, 'twilioCallForward'])->name('manage-twilio-call-forward');
                Route::post('get-workflow-list', [TwilioController::class, 'getWorkflowList'])->name('get-workflow-list');
                Route::get('call-recordings/{account_id}', [TwilioController::class, 'CallRecordings'])->name('twilio-call-recording');
                Route::get('call-management', [TwilioController::class, 'callManagement'])->name('twilio-call-management');
                Route::get('speech-to-text-logs', [TwilioController::class, 'speechToTextLogs'])->name('twilio-speech-to-text-logs');
                Route::get('call-blocks', [TwilioController::class, 'callBlocks'])->name('twilio.call.blocks');
                Route::get('call-block-delete', [TwilioController::class, 'deleteCallBlocks'])->name('twilio.call.block.delete');
                Route::get('call-statistic', [TwilioController::class, 'callStatistic'])->name('twilio.call.statistic');
                Route::get('call-statistic-delete', [TwilioController::class, 'deleteCallStatistic'])->name('twilio.call.statistic.delete');
                Route::get('incoming-calls/{number_sid}/{number}', [TwilioController::class, 'getIncomingList'])->name('twilio-incoming-calls');
                Route::get('incoming-calls-recording/{call_sid}', [TwilioController::class, 'incomingCallRecording'])->name('twilio-incoming-call-recording');
                Route::get('accept', [TwilioController::class, 'incomingCall'])->name('twilio-accept-call');
            });

            /**
             * Watson account management
             */
            Route::prefix('watson')->group(function () {
                Route::get('accounts', [WatsonController::class, 'index'])->name('watson-accounts');
                Route::post('account', [WatsonController::class, 'store'])->name('watson-accounts.add');
                Route::get('account/{id}', [WatsonController::class, 'show'])->name('watson-accounts.show');
                Route::post('account/{id}', [WatsonController::class, 'update'])->name('watson-accounts.update');
                Route::get('delete-account/{id}', [WatsonController::class, 'destroy'])->name('watson-accounts.delete');
                Route::post('add-intents/{id}', [WatsonController::class, 'addIntentsToWatson'])->name('watson-accounts.add-intents');
                Route::get('accounts', [WatsonController::class, 'index'])->name('watson-accounts');
                Route::post('account', [WatsonController::class, 'store'])->name('watson-accounts.add');
                Route::get('account/{id}', [WatsonController::class, 'show'])->name('watson-accounts.show');
                Route::post('account/{id}', [WatsonController::class, 'update'])->name('watson-accounts.update');
                Route::get('delete-account/{id}', [WatsonController::class, 'destroy'])->name('watson-accounts.delete');
                Route::post('add-intents/{id}', [WatsonController::class, 'addIntentsToWatson'])->name('watson-accounts.add-intents');
            });
            /**
             * Gemini AI account management
             */
            Route::prefix('geminiai')->group(function () {
                Route::get('accounts', [GeminiAIController::class, 'index'])->name('geminiai-accounts');
                Route::post('account', [GeminiAIController::class, 'store'])->name('geminiai-accounts.add');
                Route::get('account/{id}', [GeminiAIController::class, 'show'])->name('geminiai-accounts.show');
                Route::post('account/{id}', [GeminiAIController::class, 'update'])->name('geminiai-accounts.update');
                Route::get('delete-account/{id}', [GeminiAIController::class, 'destroy'])->name('geminiai-accounts.delete');
            });

            Route::get('get-twilio-numbers/{account_id}', [TwilioController::class, 'getTwilioActiveNumbers'])->name('twilio-get-numbers');
            Route::post('set-twilio-work-space', [TwilioController::class, 'setTwilioWorkSpace'])->name('twilio-work-space');
            Route::post('delete-twilio-work-space', [TwilioController::class, 'deleteTwilioWorkSpace'])->name('delete-twilio-work-space');
            Route::post('create-twilio-worker', [TwilioController::class, 'createTwilioWorker'])->name('create-twilio-worker');
            Route::post('create-twilio-priority', [TwilioController::class, 'createTwilioPriority'])->name('create.twilio.priority');
            Route::post('delete-twilio-worker', [TwilioController::class, 'deleteTwilioWorker'])->name('delete-twilio-worker');
            Route::post('delete-twilio-priority', [TwilioController::class, 'deleteTwilioPriority'])->name('delete.twilio.priority');

            Route::post('create-twilio-workflow', [TwilioController::class, 'createTwilioWorkflow'])->name('create-twilio-workflow');
            Route::delete('delete-twilio-workflow', [TwilioController::class, 'deleteTwilioWorkflow'])->name('delete-twilio-workflow');
            Route::post('edit-twilio-workflow', [TwilioController::class, 'editTwilioWorkflow'])->name('edit-twilio-workflow');

            Route::post('create-twilio-activity', [TwilioController::class, 'createTwilioActivity'])->name('create-twilio-activity');
            Route::delete('delete-twilio-activity', [TwilioController::class, 'deleteTwilioActivity'])->name('delete-twilio-activity');

            Route::get('fetch-activities/{workspaceId}', [TwilioController::class, 'fetchActivitiesFromWorkspace'])->name('fetch-activities');
            Route::get('fetch-task-queue/{workspaceId}', [TwilioController::class, 'fetchTaskQueueFromWorkspace'])->name('fetch-task-queue');

            Route::post('create-twilio-task-queue', [TwilioController::class, 'createTwilioTaskQueue'])->name('create-twilio-task-queue');
            Route::delete('delete-twilio-task-queue', [TwilioController::class, 'deleteTwilioTaskQueue'])->name('delete-twilio-task-queue');

            Route::get('/download-mp3/{sid}', [TwilioController::class, 'downloadRecording'])->name('download-mp3');

            //missing brands
            Route::prefix('missing-brands')->group(function () {
                Route::get('/', [MissingBrandController::class, 'index'])->name('missing-brands.index');
                Route::post('store', [MissingBrandController::class, 'store'])->name('missing-brands.store');
                Route::post('reference', [MissingBrandController::class, 'reference'])->name('missing-brands.reference');
                Route::post('multi-reference', [MissingBrandController::class, 'multiReference'])->name('missing-brands.multi-reference');
                Route::post('automatic-merge', [MissingBrandController::class, 'automaticMerge'])->name('missing-brands.automatic-merge');
            });

            Route::prefix('google-dialog')->group(function () {
                Route::get('/accounts', [GoogleDialogFlowController::class, 'index'])->name('google-chatbot-accounts');
                Route::post('/account/create', [GoogleDialogFlowController::class, 'store'])->name('google-chatbot-accounts.add');
                Route::get('/account/get/{id}', [GoogleDialogFlowController::class, 'get'])->name('google-chatbot-accounts.get');
                Route::post('/account/update', [GoogleDialogFlowController::class, 'update'])->name('google-chatbot-accounts.update');
                Route::get('/account/delete/{id}', [GoogleDialogFlowController::class, 'delete'])->name('google-chatbot-accounts.delete');
            });

            Route::post('message-queue/approve/approved', [MessageQueueController::class, 'approved']);
            Route::get('message-queue/delete-chat', [MessageQueueController::class, 'deleteMessageQueue']);
            Route::get('message-counter', [MessageQueueController::class, 'message_counter'])->name('message.counter');

            //Charity Routes
            Route::prefix('charity')->group(function () {
                Route::get('/', [CharityController::class, 'index'])->name('charity');
                Route::any('update', [CharityController::class, 'update'])->name('charity.update');
                Route::post('store', [CharityController::class, 'store'])->name('charity.store');
                Route::get('charity-order/{charity_id}', [CharityController::class, 'charityOrder'])->name('charity.charity-order');
                Route::post('add-status', [CharityController::class, 'addStatus'])->name('charity.add-status');
                Route::post('update-charity-order-status', [CharityController::class, 'updateCharityOrderStatus'])->name('charity.update-charity-order-status');
                Route::post('create-history', [CharityController::class, 'createHistory'])->name('charity.create-history');
                Route::get('view-order-history/{order_id}', [CharityController::class, 'viewHistory'])->name('charity.view-order-history');
            });
            Route::get('charity-search', [CharityController::class, 'charitySearch'])->name('charity-search');
            Route::get('charity-email', [CharityController::class, 'charityEmail'])->name('charity-email');
            Route::get('charity-phone-number', [CharityController::class, 'charityPhoneNumber'])->name('charity-phone-number');

            /*
             * Quick Reply Page
             * */
            Route::get('/quick-replies', [QuickReplyController::class, 'quickReplies'])->name('quick-replies');
            Route::get('/get-store-wise-replies/{category_id}/{store_website_id?}', [QuickReplyController::class, 'getStoreWiseReplies'])->name('store-wise-replies');
            Route::post('/save-store-wise-reply', [QuickReplyController::class, 'saveStoreWiseReply'])->name('save-store-wise-reply');
            Route::post('/copy-store-wise-reply', [QuickReplyController::class, 'copyStoreWiseReply'])->name('copy-store-wise-reply');
            Route::post('/save-sub', [QuickReplyController::class, 'saveSubCat'])->name('save-sub');
            Route::post('/attached-images-grid/customer/create-template', [ProductController::class, 'createTemplate'])->name('attach.cus.create.tpl');

            /**
             * Store Analytics Module
             */
            Route::get('/store-website-analytics/index', [StoreWebsiteAnalyticsController::class, 'index']);
            Route::any('/store-website-analytics/create', [StoreWebsiteAnalyticsController::class, 'create']);
            Route::get('/store-website-analytics/edit/{id}', [StoreWebsiteAnalyticsController::class, 'edit']);
            Route::get('/store-website-analytics/delete/{id}', [StoreWebsiteAnalyticsController::class, 'delete']);
            Route::get('/store-website-analytics/report/{id}', [StoreWebsiteAnalyticsController::class, 'report']);
            Route::get('/analytis/cron/showData', [AnalyticsController::class, 'cronShowData']);

            Route::get('store-website-country-shipping', [StoreWebsiteCountryShippingController::class, 'index'])->name('store-website-country-shipping.index');
            Route::any('store-website-country-shipping/create', [StoreWebsiteCountryShippingController::class, 'create'])->name('store-website-country-shipping.create');
            Route::get('store-website-country-shipping/edit/{id}', [StoreWebsiteCountryShippingController::class, 'edit'])->name('store-website-country-shipping.edit');
            Route::get('store-website-country-shipping/delete/{id}', [StoreWebsiteCountryShippingController::class, 'delete'])->name('store-website-country-shipping.delete');

            Route::get('/attached-images-grid/customer/', [ProductController::class, 'attachedImageGrid']);
            Route::post('/attached-images-grid/add-products/{suggested_products_id}', [ProductController::class, 'attachMoreProducts']); //
            Route::post('/attached-images-grid/remove-products/{customer_id}', [ProductController::class, 'removeProducts']); //
            Route::post('/attached-images-grid/remove-single-product/{customer_id}', [ProductController::class, 'removeSingleProduct']); //
            Route::get('/attached-images-grid/sent-products', [ProductController::class, 'suggestedProducts']);
            Route::post('/attached-images-grid/forward-products', [ProductController::class, 'forwardProducts']); //
            Route::post('/attached-images-grid/resend-products/{suggested_products_id}', [ProductController::class, 'resendProducts']); //
            Route::get('/attached-images-grid/get-products/{type}/{suggested_products_id}/{customer_id}', [ProductController::class, 'getCustomerProducts']);
            Route::get('/suggestedProduct/delete/{ids?}', [ProductController::class, 'deleteSuggestedProduct'])->name('suggestedProduct.delete');
            Route::get('/suggested/product/log', [ProductController::class, 'getSuggestedProductLog'])->name('suggestedProduct.log');

            //referfriend
            Route::prefix('referfriend')->group(function () {
                Route::get('/list', [ReferFriendController::class, 'index'])->name('referfriend.list');
                Route::delete('/delete/{id?}', [ReferFriendController::class, 'destroy'])->name('referfriend.destroy');
                Route::get('/logAjax', [ReferFriendController::class, 'logAjax'])->name('referfriend.logAjax');
            });

            //Twillio-SMS
            Route::prefix('twillio')->group(function () {
                Route::get('/', [TwillioMessageController::class, 'index']);
                Route::get('customers/{groupId}', [TwillioMessageController::class, 'showCustomerList'])->name('customer.group');
                Route::get('errors', [TwillioMessageController::class, 'showErrors'])->name('twilio.errors');
                Route::get('marketing/message/{groupId}', [TwillioMessageController::class, 'messageTitle'])->name('marketing.message');
                Route::post('create/service', [TwillioMessageController::class, 'createService'])->name('create.message.service');
                Route::post('create/message/group', [TwillioMessageController::class, 'createMessagingGroup'])->name('create.message.group');
                Route::post('add/customer', [TwillioMessageController::class, 'addCustomer'])->name('add.customer.group');
                Route::post('remove/customer', [TwillioMessageController::class, 'removeCustomer'])->name('remove.customer.group');
                Route::post('delete/message/group', [TwillioMessageController::class, 'deleteMessageGroup'])->name('delete.message.group');
                Route::post('delete/twilio/error', [TwillioMessageController::class, 'deleteTwilioError'])->name('delete.twilio.error');
                Route::post('create/marketing/message', [TwillioMessageController::class, 'createMarketingMessage'])->name('create.marketing.message');
                Route::any('process/sms', [TwillioMessageController::class, 'processMarketingMessage'])->name('process.twillio.sms');
            });

            //Image-Logs
            Route::prefix('image-logs')->group(function () {
                Route::get('/', [LogsController::class, 'index'])->name('logs.index');
                Route::post('delete/image/log', [LogsController::class, 'deleteLog'])->name('delete.image.log');
            });

            //Image-Logs
            Route::prefix('broadcast-messages')->group(function () {
                Route::get('/', [BroadcastController::class, 'index'])->name('messages.index');
                Route::post('preview-broadcast-numbers', [BroadcastController::class, 'messagePreviewNumbers'])->name('get-numbers');
                Route::post('get/send/message-group', [BroadcastController::class, 'getSendType'])->name('get-send-message-group');

                Route::post('send/message', [BroadcastController::class, 'sendMessage'])->name('send-message');
                Route::post('send/type', [BroadcastController::class, 'sendType'])->name('send-type');
                Route::post('delete/message', [BroadcastController::class, 'deleteMessage'])->name('delete.message');
                Route::post('delete/type', [BroadcastController::class, 'deleteType'])->name('delete.type');
                Route::post('resend/message', [BroadcastController::class, 'resendMessage'])->name('resend-message');
                Route::post('show/message', [BroadcastController::class, 'showMessage'])->name('show-message');
            });

            //ReferralProgram
            Route::prefix('referralprograms')->group(function () {
                Route::get('/list', [ReferralProgramController::class, 'index'])->name('referralprograms.list');
                Route::DELETE('/delete/{id?}', [ReferralProgramController::class, 'destroy'])->name('referralprograms.destroy');
                Route::get('/add', [ReferralProgramController::class, 'create'])->name('referralprograms.add');
                Route::get('/{id?}/edit', [ReferralProgramController::class, 'edit'])->name('referralprograms.edit');
                Route::post('/store', [ReferralProgramController::class, 'store'])->name('referralprograms.store');
                Route::post('/update', [ReferralProgramController::class, 'update'])->name('referralprograms.update');
                Route::get('referralprograms-ajax', [ReferralProgramController::class, 'ajax'])->name('referralprograms.ajax');
            });

            //Google file translator
            Route::prefix('googlefiletranslator')->group(function () {
                Route::get('/list', [GoogleFileTranslator::class, 'index'])->name('googlefiletranslator.list');
                Route::DELETE('/delete/{id?}', [GoogleFileTranslator::class, 'destroy'])->name('googlefiletranslator.destroy');
                Route::get('/add', [GoogleFileTranslator::class, 'create'])->name('googlefiletranslator.add');
                Route::get('/{id?}/edit', [GoogleFileTranslator::class, 'edit'])->name('googlefiletranslator.edit');
                Route::get('/{id?}/download', [GoogleFileTranslator::class, 'download'])->name('googlefiletranslator.download');
                Route::post('/store', [GoogleFileTranslator::class, 'store'])->name('googlefiletranslator.store');
                Route::post('/update', [GoogleFileTranslator::class, 'update'])->name('googlefiletranslator.update');
                Route::get('/{id}/{type}/list-view', [GoogleFileTranslator::class, 'dataViewPage'])->name('googlefiletranslator.list-page.view');
                Route::get('/download-permission', [GoogleFileTranslator::class, 'downloadPermission'])->name('googlefiletranslator.downlaod.permission');
                Route::post('/user-view-permission', [GoogleFileTranslator::class, 'userViewPermission'])->name('googlefiletranslator.user-view.permission');
                Route::get('/edit-value', [GoogleFileTranslator::class, 'editValue'])->name('googlefiletranslator.edit.value');
                Route::get('googlefiletranslator/{id}', [GoogleFileTranslator::class, 'tranalteHistoryShow'])->name('googlefiletranslator_histories.show');
                Route::get('googlefiletranslatorstatus/{id}', [GoogleFileTranslator::class, 'tranalteStatusHistoryShow'])->name('googlefiletranslator_histories_status.show');
                Route::post('status-change', [GoogleFileTranslator::class, 'statusChange'])->name('googlefiletranslator_histories.status');
                Route::get('/download-csv/{id}/{type}', [GoogleFileTranslator::class, 'downloadCsv'])->name('store-website.download.csv');
                Route::post('/get-translated-text-score', [GoogleFileTranslator::class, 'getTranslatedTextScore'])->name('googlefiletranslator.get-multi-score');
            });

            //Translation
            Route::prefix('translation')->group(function () {
                Route::get('/list', [TranslationController::class, 'index'])->name('translation.list');
                Route::get('translate-logs', [TranslationController::class, 'translateLog'])->name('translation.log');
                Route::post('mark-as-resolve', [TranslationController::class, 'markAsResolve'])->name('translation.log.markasresolve');
                Route::DELETE('/delete/{id?}', [TranslationController::class, 'destroy'])->name('translation.destroy');
                Route::DELETE('translate-logs/delete/{id?}', [TranslationController::class, 'translateLogDelete'])->name('translation.log.destroy');
                Route::get('/add', [TranslationController::class, 'create'])->name('translation.add');
                Route::get('/{id?}/edit', [TranslationController::class, 'edit'])->name('translation.edit');
                Route::post('/store', [TranslationController::class, 'store'])->name('translation.store');
                Route::post('/update', [TranslationController::class, 'update'])->name('translation.update');
            });

            Route::resource('taskcategories', TaskCategoriesController::class);
            Route::delete('tasklist/{id}', [TaskCategoriesController::class, 'delete']);
            Route::delete('tasksubject/{id}', [TaskCategoriesController::class, 'destroy']);
            Route::resource('zabbix', ZabbixController::class)->except(['show']);
            Route::get('/search/hosts', [ZabbixController::class, 'autoSuggestHosts']);

            Route::resource('checklist', CheckListController::class);
            Route::get('checklist/view/{id}', [CheckListController::class, 'view'])->name('checklist.view');
            Route::post('checklist/subjects', [CheckListController::class, 'subjects'])->name('checklist.subjects');
            Route::post('checklist/add_checklist', [CheckListController::class, 'add'])->name('checklist.add');
            Route::post('checklist/get_checked_value', [CheckListController::class, 'checked'])->name('checklist.get.checked');
            Route::post('checklist/checklist_update', [CheckListController::class, 'checklistUpdate'])->name('checklist.update.c');
            Route::post('checklist/add-remark', [CheckListController::class, 'subjectRemarkCreate'])->name('checklist.add.remark');
            Route::post('checklist/list', [CheckListController::class, 'subjectRemarkList'])->name('checklist.remark.list');

            Route::resource('devoops', DevOppsController::class);
            Route::delete('devoopslist/{id}', [DevOppsController::class, 'delete']);
            Route::delete('devoopssublist/{id}', [DevOppsController::class, 'subdelete']);
            Route::post('devoopssublist/remarks', [DevOppsController::class, 'saveRemarks'])->name('devoopssublist.saveremarks');
            Route::post('devoopssublist/getremarks', [DevOppsController::class, 'getRemarksHistories'])->name('devoopssublist.getremarks');
            Route::get('devoops/countdevtask/{id}', [DevOppsController::class, 'taskCount']);
            Route::post('devoops/status/create', [DevOppsController::class, 'createStatus'])->name('devoops.status.create');
            Route::post('devoops/statuscolor', [DevOppsController::class, 'statuscolor'])->name('devoops.statuscolor');
            Route::post('devoops/status/update', [DevOppsController::class, 'updateStatus'])->name('devoops.status.update');
            Route::post('devoopssublist/getstatus', [DevOppsController::class, 'getStatusHistories'])->name('devoopssublist.getstatus');
            Route::post('devoopssublist/upload-file', [DevOppsController::class, 'uploadFile'])->name('devoopssublist.upload-file');
            Route::get('devoopssublist/files/record', [DevOppsController::class, 'getUploadedFilesList'])->name('devoopssublist.files.record');
            Route::post('devoops/task/upload-document', [DevOppsController::class, 'uploadDocument']);
            Route::get('devoops/task/get-document', [DevOppsController::class, 'getDocument']);

            // Vouchers and Coupons
            Route::prefix('vouchers-coupons')->group(function () {
                Route::get('/', [VoucherCouponController::class, 'index'])->name('list.voucher');
                Route::post('/statuscolor', [VoucherCouponController::class, 'statuscolor'])->name('voucher.statuscolor');
                Route::post('/statuscreate', [VoucherCouponController::class, 'statusCreate'])->name('voucher.status.create');
                Route::post('/updatestatus', [VoucherCouponController::class, 'updateStatus'])->name('voucher.update-status');
                Route::get('/status/histories/{id}', [VoucherCouponController::class, 'statusHistories'])->name('voucher.status.histories');

                Route::post('/remarks', [VoucherCouponController::class, 'saveRemarks'])->name('voucher.saveremarks');
                Route::post('/getremarks', [VoucherCouponController::class, 'getRemarksHistories'])->name('voucher.getremarks');

                Route::post('/plateform/create', [VoucherCouponController::class, 'plateformStore'])->name('voucher.plateform.create');
                Route::post('/store', [VoucherCouponController::class, 'store'])->name('voucher.coupon.store');
                Route::post('/edit', [VoucherCouponController::class, 'edit'])->name('voucher.coupon.edit');
                Route::post('/voucher/remark/{id}', [VoucherCouponController::class, 'storeRemark'])->name('voucher.store.remark');
                Route::post('/voucher/delete', [VoucherCouponController::class, 'delete'])->name('voucher.coupon.delete');
                Route::post('/coupon/code/create', [VoucherCouponController::class, 'couponCodeCreate'])->name('voucher.code.create');
                Route::post('/coupon/code/list', [VoucherCouponController::class, 'couponCodeList'])->name('voucher.code.list');
                Route::post('/coupon/code/order/create', [VoucherCouponController::class, 'couponCodeOrderCreate'])->name('voucher.code.order.create');
                Route::post('/coupon/code/order/list', [VoucherCouponController::class, 'couponCodeOrderList'])->name('voucher.code.order.list');
                Route::post('/voucher/code/delete', [VoucherCouponController::class, 'couponCodeDelete'])->name('voucher.code.delete');
                Route::post('/voucher/code/order/delete', [VoucherCouponController::class, 'couponCodeOrderDelete'])->name('voucher.code.order.delete');
                Route::post('/coupon-type/create', [VoucherCouponController::class, 'coupontypeStore'])->name('voucher.coupon.type.create');
                Route::get('/coupon-type/list', [VoucherCouponController::class, 'couponTypeList'])->name('voucher.coupon.type.list');
                Route::get('vouchers/coupon-code/list', [VoucherCouponController::class, 'voucherscouponCodeList'])->name('list.voucher.coupon.code');
            });

            //Affiliates
            Route::prefix('affiliates')->group(function () {
                Route::get('/', [AffiliateResultController::class, 'index'])->name('affiliates.list');
                Route::POST('/delete', [AffiliateResultController::class, 'destroy'])->name('affiliates.destroy');
                Route::get('/{id?}/edit', [AffiliateResultController::class, 'edit'])->name('affiliates.edit');
            });

            //FCM Notifications
            Route::prefix('pushfcmnotification')->group(function () {
                Route::get('/list', [FcmNotificationController::class, 'index'])->name('pushfcmnotification.list');
                Route::DELETE('/delete/{id?}', [FcmNotificationController::class, 'destroy'])->name('pushfcmnotification.destroy');
                Route::get('/add', [FcmNotificationController::class, 'create'])->name('pushfcmnotification.add');
                Route::get('/{id?}/edit', [FcmNotificationController::class, 'edit'])->name('pushfcmnotification.edit');
                Route::post('/store', [FcmNotificationController::class, 'store'])->name('pushfcmnotification.store');
                Route::post('/update', [FcmNotificationController::class, 'update'])->name('pushfcmnotification.update');
                Route::get('/error-list', [FcmNotificationController::class, 'errorList'])->name('pushfcmnotification.errorList');
            });

            Route::get('/image-remark/sotre', [scrapperPhyhon::class, 'imageRemarkStore'])->name('image-remark.store');
            Route::get('/change-category/remarks-show', [scrapperPhyhon::class, 'changeCatRemarkList'])->name('change-category.remarks-show');
            Route::get('/scrapper-python', [scrapperPhyhon::class, 'index'])->name('scrapper.phyhon.index');
            Route::post('/scrapper-python/delete', [scrapperPhyhon::class, 'delete'])->name('scrapper.phyhon.delete');
            Route::get('/scrapper-python/list-images', [scrapperPhyhon::class, 'listImages'])->name('scrapper.phyhon.listImages');
            Route::post('/scrapper-python/call', [scrapperPhyhon::class, 'callScrapper'])->name('scrapper.call');
            Route::get('/scrapper-python/history', [scrapperPhyhon::class, 'history'])->name('scrapper.history');
            Route::get('/scrapper-python/actionHistory', [scrapperPhyhon::class, 'actionHistory'])->name('scrapper.action.history');
            Route::get('/scrapper-python/image/url_list', [scrapperPhyhon::class, 'imageUrlList'])->name('scrapper.image.urlList');
            Route::post('/scrapper-python/{id}/url', [scrapperPhyhon::class, 'flagImageUrl'])->name('scrapper.url.flag');

            Route::post('/scrapper-python/reject-image', [scrapperPhyhon::class, 'rejectScrapperImage'])->name('scrapper.reject,image');

            Route::get('/set/default/store/{website?}/{store?}/{checked?}', [scrapperPhyhon::class, 'setDefaultStore'])->name('set.default.store');
            Route::get('/set/flag/store/{website?}/{store?}/{checked?}', [scrapperPhyhon::class, 'setFlagStore'])->name('set.flag.store');

            Route::get('/get/website/stores/{website?}', [scrapperPhyhon::class, 'websiteStoreList'])->name('website.store.list');
            Route::get('/get/stores/language/{website?}', [scrapperPhyhon::class, 'storeLanguageList'])->name('store.language.list');
            Route::post('/scrapper-python/store-apk', [scrapperPhyhon::class, 'storeAPKFile'])->name('store.scrapper.apk');

            Route::get('google-keyword-search', [GoogleAddWord\googleAddsController::class, 'index'])->name('google-keyword-search');
            Route::get('google-keyword-search-v6', [GoogleAddWord\googleAddsV6Controller::class, 'main'])->name('google-keyword-search-v6');
            Route::get('google-keyword-search-v2', [GoogleAddWord\googleAddsController::class, 'generatekeywordidea'])->name('google-keyword-search-v2');
            Route::resource('google-traslation-settings', GoogleTraslationSettingsController::class);
            Route::post('google-traslation-settings/update-translation-plan', [GoogleTraslationSettingsController::class, 'updateTranslationPlan'])->name('update.translation.plan');

            Route::prefix('admin')->group(function () {
                Route::any('/erp-log', [ErpLogController::class, 'index'])->name('erp-log');
                Route::any('/sentry-log', [SentryLogController::class, 'index'])->name('sentry-log');
                Route::post('sentry-log/display-user-account', [SentryLogController::class, 'displayUserAccountList'])->name('sentry.display-user');
                Route::post('sentry-log/saveuseraccount', [SentryLogController::class, 'saveUserAccount'])->name('sentry.adduser');
                Route::post('sentry-log/refresh_logs', [SentryLogController::class, 'refreshLogs'])->name('sentry.refresh-logs');
                Route::post('sentry-log/status/create', [SentryLogController::class, 'sentryStatusCreate'])->name('sentry.status.create');
                Route::post('sentry-log/statuscolor', [SentryLogController::class, 'statuscolor'])->name('sentry.statuscolor');
                Route::get('sentry-log/countdevtask/{id}', [SentryLogController::class, 'taskCount']);
                Route::post('sentry-log/updatestatus', [SentryLogController::class, 'updateStatus'])->name('sentry.updatestatus');
                Route::get('sentry-log/status/histories/{id}', [SentryLogController::class, 'sentryStatusHistories'])->name('sentry.status.histories');
                Route::any('/database-log', [ScrapLogsController::class, 'databaseLog']);
                Route::get('/database-log/enable', [ScrapLogsController::class, 'enableMysqlAccess']);
                Route::get('/database-log/disable', [ScrapLogsController::class, 'disableMysqlAccess']);
                Route::get('/database-log/history', [ScrapLogsController::class, 'disableEnableHistory']);
                Route::get('/database-log/truncate', [ScrapLogsController::class, 'databaseTruncate']);
                Route::prefix('plan')->group(function () {
                    Route::get('/', [PlanController::class, 'index'])->name('plan.index');
                    Route::post('/create', [PlanController::class, 'store'])->name('plan.store');
                    Route::get('/edit', [PlanController::class, 'edit'])->name('plan.edit');
                    Route::post('/{id}/update', [PlanController::class, 'update'])->name('plan.update');
                    Route::get('/delete/{id}', [PlanController::class, 'delete'])->name('plan.delete');
                    Route::get('/{id}/plan-action', [PlanController::class, 'planAction']);
                    Route::get('/{id}/plan-action-addons', [PlanController::class, 'planActionAddOn'])->name('plan.action.addons');
                    Route::post('/plan-action/store', [PlanController::class, 'planActionStore'])->name('plan.action.store');
                    Route::post('/plan-action/solutions-store', [PlanController::class, 'planSolutionsStore'])->name('plan.solution.store');
                    Route::get('/plan-action/solutions-get/{id}', [PlanController::class, 'planSolutionsGet'])->name('plan.show.solutions');

                    Route::post('plan/basis/create', [PlanController::class, 'newBasis'])->name('plan.create.basis');
                    Route::post('plan/type/create', [PlanController::class, 'newType'])->name('plan.create.type');
                    Route::post('plan/category/create', [PlanController::class, 'newCategory'])->name('plan.create.category');
                    Route::post('plan/status/update', [PlanController::class, 'changeStatusCategory'])->name('plan.status.update');
                    Route::post('plan/add/remark', [PlanController::class, 'addPlanRemarks'])->name('plan.reamrk.add');
                    Route::post('plan/list/remark', [PlanController::class, 'getRemarkList'])->name('plan.remark.list');
                });
                Route::prefix('slack-channel')->group(function () {
                    Route::get('/', [SlackChannelController::class, 'index'])->name('slack.channel.index');
                    Route::post('/create', [SlackChannelController::class, 'store'])->name('slack.channel.store');
                    Route::get('/edit', [SlackChannelController::class, 'edit'])->name('slack.channel.edit');
                    Route::post('/status/update', [SlackChannelController::class, 'changeStatus'])->name('slack.channel.status.update');
                });
            });

            //System size
            Route::prefix('system')->group(function () {
                Route::get('/size', [SystemSizeController::class, 'index'])->name('system.size');
                Route::get('/size/store', [SystemSizeController::class, 'store'])->name('system.size.store');
                Route::get('/size/update', [SystemSizeController::class, 'update'])->name('system.size.update');
                Route::get('/size/delete', [SystemSizeController::class, 'delete'])->name('system.size.delete');

                Route::get('/size/managercheckexistvalue', [SystemSizeController::class, 'managercheckexistvalue'])->name('system.size.managercheckexistvalue');
                Route::post('/size/managerstore', [SystemSizeController::class, 'managerstore'])->name('system.size.managerstore');
                Route::get('/size/manageredit', [SystemSizeController::class, 'manageredit'])->name('system.size.manageredit');
                Route::post('/size/managerupdate', [SystemSizeController::class, 'managerupdate'])->name('system.size.managerupdate');
                Route::get('/size/managerdelete', [SystemSizeController::class, 'managerdelete'])->name('system.size.managerdelete');
                Route::get('/size/exports', [SystemSizeController::class, 'exports'])->name('system.size.exports');

                Route::post('size/push', [SystemSizeController::class, 'pushSystemSize']);

                Route::prefix('auto-refresh')->group(static function () {
                    Route::get('/', [AutoRefreshController::class, 'index'])->name('auto.refresh.index');
                    Route::post('/create', [AutoRefreshController::class, 'store'])->name('auto.refresh.store');
                    Route::get('/{id}/edit', [AutoRefreshController::class, 'edit'])->name('auto.refresh.edit');
                    Route::post('/{id}/update', [AutoRefreshController::class, 'update'])->name('auto.refresh.update');
                    Route::get('/{id}/delete', [AutoRefreshController::class, 'delete'])->name('auto.refresh.delete');
                });
            });

            Route::get('/admin-menu/db-query', [DBQueryController::class, 'index'])->name('admin.databse.menu.direct.dbquery');
            Route::post('/admin-menu/db-query/get-columns', [DBQueryController::class, 'columns'])->name('admin.databse.menu.direct.dbquery.columns');
            Route::post('/admin-menu/db-query/confirm', [DBQueryController::class, 'confirm'])->name('admin.databse.menu.direct.dbquery.confirm');
            Route::post('/admin-menu/db-query/delete/confirm', [DBQueryController::class, 'deleteConfirm'])->name('admin.databse.menu.direct.dbquery.delete.confirm');
            Route::post('/admin-menu/db-query/update', [DBQueryController::class, 'updateDBQuery'])->name('admin.databse.menu.direct.dbquery.update');
            Route::post('/admin-menu/db-query/delete', [DBQueryController::class, 'deleteDBQuery'])->name('admin.databse.menu.direct.dbquery.delete');
            Route::post('/admin-menu/db-query/command_execution', [DBQueryController::class, 'command_execution'])->name('admin.command_execution'); //Purpose : Add Route for Command Exicute - DEVTASK-19941
            Route::get('/admin-menu/db-query/command_execution_history', [DBQueryController::class, 'command_execution_history'])->name('admin.command_execution_history'); //Purpose : Add Route for Command Exicution History data - DEVTASK-19941
            Route::get('/admin-menu/db-query/report-download', [DBQueryController::class, 'ReportDownload'])->name('admin.db-query.download');

            Route::prefix('totem')->group(function () {
                Route::get('/', [TasksController::class, 'dashboard'])->name('totem.dashboard');

                Route::prefix('tasks')->group(function () {
                    Route::get('/', [TasksController::class, 'index'])->name('totem.tasks.all');
                    Route::get('{task}', [TasksController::class, 'view'])->name('totem.task.view');
                    Route::get('{task}/execution-history', [TasksController::class, 'executionHistory'])->name('totem.task.execution-history');
                    Route::post('{task}/delete', [TasksController::class, 'destroy'])->name('totem.delete.task');
                    Route::post('{task}/edit', [TasksController::class, 'update'])->name('totem.task.update');
                    Route::post('create', [TasksController::class, 'store'])->name('totem.create.task');
                    Route::post('{task}/status', [TasksController::class, 'status'])->name('totem.task.status');
                    Route::get('{task}/development-task', [TasksController::class, 'developmentTask'])->name('totem.task.developmentTask');
                    Route::post('{task}/get-error', [TasksController::class, 'totemCommandError'])->name('totem.task.get-error');
                    Route::post('enable-disable', [TasksController::class, 'enableDisableCron'])->name('totem.task.enable-disable');
                    Route::post('assign-users', [TasksController::class, 'assignUsers'])->name('totem.task.assign-users');
                    Route::post('bulk-assign', [TasksController::class, 'bulkAssign'])->name('totem.task.bulk-assign');
                });
            });

            Route::prefix('select2')->group(function () {
                Route::get('customers', [Select2Controller::class, 'customers'])->name('select2.customer');
                Route::get('customersByMultiple', [Select2Controller::class, 'customersByMultiple'])->name('select2.customerByMultiple');
                Route::get('users', [Select2Controller::class, 'users'])->name('select2.user');
                Route::get('users_vendors', [Select2Controller::class, 'users_vendors'])->name('select2.uservendor');
                Route::get('suppliers', [Select2Controller::class, 'suppliers'])->name('select2.suppliers');
                Route::get('updatedby-users', [Select2Controller::class, 'updatedbyUsers'])->name('select2.updatedby_users');
                Route::get('scraped-brand', [Select2Controller::class, 'scrapedBrand'])->name('select2.scraped-brand');
                Route::get('brands', [Select2Controller::class, 'allBrand'])->name('select2.brands');
                Route::get('categories', [Select2Controller::class, 'allCategory'])->name('select2.categories');
                Route::get('websites', [Select2Controller::class, 'allWebsites'])->name('select2.websites');
                Route::get('tasks', [Select2Controller::class, 'allTasks'])->name('select2.tasks');
                Route::get('task-categories', [Select2Controller::class, 'taskCategory'])->name('select2.taskcategories');
                Route::get('zabbix-webhook-data', [Select2Controller::class, 'zabbixWebhookData'])->name('select2.zabbix-webhook-data');
                Route::get('sop-categories', [Select2Controller::class, 'sopCategories'])->name('select2.sop-categories');

                Route::get('time-doctor-accounts', [Select2Controller::class, 'timeDoctorAccounts'])->name('select2.time_doctor_accounts');
                Route::get('time-doctor-projects', [Select2Controller::class, 'timeDoctorProjects'])->name('select2.time_doctor_projects');
                Route::get('time-doctor-projects-ajax', [Select2Controller::class, 'timeDoctorProjectsAjax'])->name('select2.time_doctor_projects_ajax');
                Route::get('time-doctor-accounts-for-task', [Select2Controller::class, 'timeDoctorAccountsForTask'])->name('select2.time_doctor_accounts_for_task');
                Route::get('shortcut-platform', [Select2Controller::class, 'shortcutplatform'])->name('select2.shortcutplatform');
                Route::get('shortcut-suppliers', [Select2Controller::class, 'shortcutSuppliers'])->name('select2.shortcutsuplliers');
                Route::get('shortcut-folders', [Select2Controller::class, 'shortcutFolders'])->name('select2.shortcutfolders');
                Route::get('shortcut-product-colors', [Select2Controller::class, 'productColors'])->name('select2.productsColors');
                Route::get('shortcut-product-sizesystem', [Select2Controller::class, 'producsizeSystem'])->name('select2.productsSizesystem');
                Route::get('shortcut-documentCategory', [Select2Controller::class, 'shortcutdocumentCategory'])->name('select2.documentCategory');
                Route::get('vocher-platforms', [Select2Controller::class, 'vochuerPlatform'])->name('select2.vochers_platforms');
                Route::get('vocher-emails', [Select2Controller::class, 'vochuerEmail'])->name('select2.vochers_emails');
                Route::get('vocher-whatsapp/config', [Select2Controller::class, 'vochuerWhatsappconfig'])->name('select2.vochers_whatsapp_config');
                Route::get('magento-frontend/category', [Select2Controller::class, 'magentoCreateFromCategory'])->name('select2.magento-frontend-category');
            });

            //Magento Product Error
            Route::prefix('magento-product-error')->group(function () {
                Route::get('/', [MagentoProductPushErrors::class, 'index'])->name('magento-productt-errors.index');
                Route::get('/records', [MagentoProductPushErrors::class, 'records'])->name('magento-productt-errors.records');
                Route::post('/loadfiled', [MagentoProductPushErrors::class, 'getLoadDataValue']);
                Route::get('/download', [MagentoProductPushErrors::class, 'groupErrorMessage'])->name('magento_product_today_common_err');
                Route::get('/magento_product_today_common_err_report', [MagentoProductPushErrors::class, 'groupErrorMessageReport'])->name('magento_product_today_common_err_report'); //Purpose : Add Route for get Data - DEVTASK-20123
            });

            Route::prefix('message-queue-history')->group(function () {
                Route::get('/', [MessageQueueHistoryController::class, 'index'])->name('message-queue-history.index');
                Route::get('/records', [MessageQueueHistoryController::class, 'records'])->name('message-queue-history.records');
            });

            Route::prefix('custom-chat-message')->group(function () {
                Route::get('/', [ChatMessagesController::class, 'customChatListing'])->name('custom-chat-message.index');
                Route::get('/records', [ChatMessagesController::class, 'customChatRecords']);
            });

            Route::prefix('lead-order')->group(function () {
                Route::get('/', [LeadOrderController::class, 'index'])->name('lead-order.index');
                Route::get('/product/log', [LeadOrderController::class, 'leadProductPriceLog'])->name('lead.order.product.log');
                Route::get('/cal/log', [LeadOrderController::class, 'leadProductPriceCalLog'])->name('lead.product.cal.log');
            });

            Route::resource('taskcategories', TaskCategoriesController::class);
            Route::delete('tasklist/{id}', [TaskCategoriesController::class, 'delete']);
            Route::delete('tasksubject/{id}', [TaskCategoriesController::class, 'destroy']);
            Route::resource('zabbix', ZabbixController::class)->except(['show']);
            Route::get('/search/hosts', [ZabbixController::class, 'autoSuggestHosts']);

            Route::resource('checklist', CheckListController::class);
            Route::get('checklist/view/{id}', [CheckListController::class, 'view'])->name('checklist.view');
            Route::post('checklist/subjects', [CheckListController::class, 'subjects'])->name('checklist.subjects');
            Route::post('checklist/add_checklist', [CheckListController::class, 'add'])->name('checklist.add');
            Route::post('checklist/get_checked_value', [CheckListController::class, 'checked'])->name('checklist.get.checked');
            Route::post('checklist/checklist_update', [CheckListController::class, 'checklistUpdate'])->name('checklist.update.c');
            Route::post('checklist/add-remark', [CheckListController::class, 'subjectRemarkCreate'])->name('checklist.add.remark');
            Route::post('checklist/list', [CheckListController::class, 'subjectRemarkList'])->name('checklist.remark.list');

            Route::resource('devoops', DevOppsController::class);
            Route::delete('devoopslist/{id}', [DevOppsController::class, 'delete']);
            Route::delete('devoopssublist/{id}', [DevOppsController::class, 'subdelete']);
            Route::post('devoopssublist/remarks', [DevOppsController::class, 'saveRemarks'])->name('devoopssublist.saveremarks');
            Route::post('devoopssublist/getremarks', [DevOppsController::class, 'getRemarksHistories'])->name('devoopssublist.getremarks');
            Route::get('devoops/countdevtask/{id}', [DevOppsController::class, 'taskCount']);
            Route::post('devoops/status/create', [DevOppsController::class, 'createStatus'])->name('devoops.status.create');
            Route::post('devoops/statuscolor', [DevOppsController::class, 'statuscolor'])->name('devoops.statuscolor');
            Route::post('devoops/status/update', [DevOppsController::class, 'updateStatus'])->name('devoops.status.update');
            Route::post('devoopssublist/getstatus', [DevOppsController::class, 'getStatusHistories'])->name('devoopssublist.getstatus');
            Route::post('devoopssublist/upload-file', [DevOppsController::class, 'uploadFile'])->name('devoopssublist.upload-file');
            Route::get('devoopssublist/files/record', [DevOppsController::class, 'getUploadedFilesList'])->name('devoopssublist.files.record');
            Route::post('devoops/task/upload-document', [DevOppsController::class, 'uploadDocument']);
            Route::get('devoops/task/get-document', [DevOppsController::class, 'getDocument']);

            // Vouchers and Coupons
            Route::prefix('vouchers-coupons')->group(function () {
                Route::post('/update', [VoucherCouponController::class, 'update'])->name('voucher.coupon.update');
                Route::post('/edit', [VoucherCouponController::class, 'edit'])->name('voucher.edit.view');
            });

            //TODOLIST::
            Route::prefix('todolist')->group(function () {
                Route::get('/', [TodoListController::class, 'index'])->name('todolist');
                Route::post('/store', [TodoListController::class, 'store'])->name('todolist.store');
                Route::post('/ajax_store', [TodoListController::class, 'ajax_store'])->name('todolist.ajax_store');
                Route::post('/edit', [TodoListController::class, 'edit'])->name('todolist.edit');
                Route::post('/update', [TodoListController::class, 'update'])->name('todolist.update');
                Route::post('/remark/history', [TodoListController::class, 'getRemarkHistory'])->name('todolist.remark.history');
                Route::post('/status/store', [TodoListController::class, 'storeStatus'])->name('todolist.status.store');
                Route::post('/status/update', [TodoListController::class, 'statusUpdate'])->name('todolist.status.update');
                Route::post('/category/store', [TodoListController::class, 'storeTodoCategory'])->name('todolist.category.store');
                Route::post('/category/update', [TodoListController::class, 'todoCategoryUpdate'])->name('todolist.category.update');
                Route::post('/status/color-update', [TodoListController::class, 'StatusColorUpdate'])->name('todolist-color-updates');
                Route::delete('/{id}/destroy', [TodoListController::class, 'destroy'])->name('todolist.destroy');
                Route::post('/remark/historypost', [TodoListController::class, 'remarkPostHistory'])->name('todolist.remark.history.post');
                Route::get('/get/todolist/search/', [TodoListController::class, 'searchTodoListHeader'])->name('todolist.module.search');
                Route::get('/gettodocategoryandstatus', [TodoListController::class, 'getTodoCategoryAndStatus'])->name('todolist.category.status');
            });

            Route::prefix('google-docs')->name('google-docs')->group(function () {
                Route::get('/', [GoogleDocController::class, 'index'])->name('.index');
                Route::post('/', [GoogleDocController::class, 'create'])->name('.create');
                Route::post('/permission-update', [GoogleDocController::class, 'permissionUpdate'])->name('.permission.update');
                Route::post('/permission-remove', [GoogleDocController::class, 'permissionRemove'])->name('.permission.remove');
                Route::post('/permission-view', [GoogleDocController::class, 'permissionView'])->name('.permission.view');
                Route::delete('/{id}/destroy', [GoogleDocController::class, 'destroy'])->name('.destroy');
                Route::get('/header/search', [GoogleDocController::class, 'googledocSearch'])->name('.google.module.search');
                Route::get('{id}/edit', [GoogleDocController::class, 'edit'])->name('.edit');
                Route::post('/update', [GoogleDocController::class, 'update'])->name('.update');
                Route::post('task', [GoogleDocController::class, 'createDocumentOnTask'])->name('.task');
                Route::get('task/show', [GoogleDocController::class, 'listDocumentOnTask'])->name('.task.show');
                Route::post('category/update', [GoogleDocController::class, 'updateGoogleDocCategory'])->name('.category.update');
                Route::post('category/create', [GoogleDocController::class, 'createGoogleDocCategory'])->name('.category.create');
                Route::get('list', [GoogleDocController::class, 'getGoogleDocList'])->name('.list');
                Route::post('assign/user-permission', [GoogleDocController::class, 'assignUserPermission'])->name('.assign-user-permission');
                Route::post('/remove/permission', [GoogleDocController::class, 'googleDocRemovePermission'])->name('.googleDocRemovePermission');
                Route::post('/add/mulitple/permission', [GoogleDocController::class, 'addMulitpleDocPermission'])->name('.addMulitpleDocPermission');
                Route::get('filename', [GoogleDocController::class, 'googleDocumentList'])->name('.filename');
                Route::get('tasks', [GoogleDocController::class, 'googleTasksList'])->name('.tasks');
            });

            Route::prefix('google-drive-screencast')->name('google-drive-screencast')->group(function () {
                Route::get('/', [GoogleScreencastController::class, 'index'])->name('.index');
                Route::post('/', [GoogleScreencastController::class, 'create'])->name('.create');
                Route::post('/permission-update', [GoogleScreencastController::class, 'driveFilePermissionUpdate'])->name('.permission.update');
                Route::delete('/{id}/destroy', [GoogleScreencastController::class, 'destroy'])->name('.destroy');
                Route::get('/task-files/{taskId}', [GoogleScreencastController::class, 'getTaskDriveFiles']);
                Route::post('/update', [GoogleScreencastController::class, 'update'])->name('.update');
                Route::get('/list/google-screen-cast', [GoogleScreencastController::class, 'getGoogleScreencast'])->name('.getGooglesScreencast');
                Route::post('/remove/permission', [GoogleScreencastController::class, 'driveFileRemovePermission'])->name('.driveFileRemovePermission');
                Route::post('/add/mulitple/permission', [GoogleScreencastController::class, 'addMultipleDocPermission'])->name('.addMultipleDocPermission');
            });

            Route::prefix('seo')->group(function () {
                Route::prefix('content')->group(function () {
                    Route::get('', [Seo\ContentController::class, 'index'])->name('seo.content.index');
                    Route::post('seo-content-column-visbility', [Seo\ContentController::class, 'columnVisbilityUpdate'])->name('seo.content.column.update');
                    Route::post('statuscolor', [Seo\ContentController::class, 'statuscolor'])->name('seo.content.statuscolor');
                    Route::get('create', [Seo\ContentController::class, 'create'])->name('seo.content.create');
                    Route::post('store', [Seo\ContentController::class, 'store'])->name('seo.content.store');
                    Route::get('{id}/edit', [Seo\ContentController::class, 'edit'])->name('seo.content.edit');
                    Route::post('{id}/update', [Seo\ContentController::class, 'update'])->name('seo.content.update');
                    Route::get('{id}/show', [Seo\ContentController::class, 'show'])->name('seo.content.show');
                });

                Route::prefix('content-status')->group(function () {
                    Route::get('', [Seo\ContentStatusController::class, 'index'])->name('seo.content-status.index');
                    Route::get('create', [Seo\ContentStatusController::class, 'create'])->name('seo.content-status.create');
                    Route::post('store', [Seo\ContentStatusController::class, 'store'])->name('seo.content-status.store');
                    Route::get('{id}/edit', [Seo\ContentStatusController::class, 'edit'])->name('seo.content-status.edit');
                    Route::post('{id}/update', [Seo\ContentStatusController::class, 'update'])->name('seo.content-status.update');
                });

                Route::prefix('company')->group(function () {
                    Route::get('', [Seo\CompanyController::class, 'index'])->name('seo.company.index');
                    Route::get('create', [Seo\CompanyController::class, 'create'])->name('seo.company.create');
                    Route::post('store', [Seo\CompanyController::class, 'store'])->name('seo.company.store');
                    Route::get('{id}/edit', [Seo\CompanyController::class, 'edit'])->name('seo.company.edit');
                    Route::post('{id}/update', [Seo\CompanyController::class, 'update'])->name('seo.company.update');
                    Route::post('column-visbility', [Seo\CompanyController::class, 'columnVisbilityUpdate'])->name('seo.company.column.update');
                    Route::post('statuscolor', [Seo\CompanyController::class, 'statuscolor'])->name('seo.company.statuscolor');
                });

                Route::prefix('company-type')->group(function () {
                    Route::get('', [Seo\CompanyTypeController::class, 'index'])->name('seo.company-type.index');
                    Route::get('create', [Seo\CompanyTypeController::class, 'create'])->name('seo.company-type.create');
                    Route::post('store', [Seo\CompanyTypeController::class, 'store'])->name('seo.company-type.store');
                    Route::get('edit/{id}', [Seo\CompanyTypeController::class, 'edit'])->name('seo.company-type.edit');
                    Route::post('update/{id}', [Seo\CompanyTypeController::class, 'update'])->name('seo.company-type.update');
                    Route::post('destroy/{id}', [Seo\CompanyTypeController::class, 'destroy'])->name('seo.company-type.destroy');
                });
            });

            //Queue Management::
            Route::prefix('system-queue')->group(function () {
                Route::get('/', [RedisQueueController::class, 'index'])->name('redisQueue.list');
                Route::post('/store', [RedisQueueController::class, 'store'])->name('redisQueue.store');
                Route::post('/edit', [RedisQueueController::class, 'edit'])->name('redisQueue.edit');
                Route::post('/update', [RedisQueueController::class, 'update'])->name('redisQueue.update');
                Route::post('/delete', [RedisQueueController::class, 'delete'])->name('redisQueue.delete');
                Route::post('/execute', [RedisQueueController::class, 'execute'])->name('redisQueue.execute');
                Route::post('/execute-horizon', [RedisQueueController::class, 'executeHorizon'])->name('redisQueue.executeHorizon');
                Route::get('/command-logs/{id}', [RedisQueueController::class, 'commandLogs'])->name('redisQueue.commandLogs');
                Route::get('/sync', [RedisQueueController::class, 'syncQueues'])->name('redisQueue.sync');
            });

            Route::prefix('affiliate-marketing')->group(function () {
                Route::prefix('provider-accounts')->group(function () {
                    Route::get('', [AffiliateMarketingController::class, 'providerAccounts'])->name('affiliate-marketing.providerAccounts');
                    Route::get('{id}', [AffiliateMarketingController::class, 'getProviderAccount'])->name('affiliate-marketing.getProviderAccount');
                    Route::post('create', [AffiliateMarketingController::class, 'createProviderAccount'])->name('affiliate-marketing.createProviderAccount');
                    Route::post('update/{id}', [AffiliateMarketingController::class, 'updateProviderAccount'])->name('affiliate-marketing.updateProviderAccount');
                    Route::post('delete', [AffiliateMarketingController::class, 'deleteProviderAccount'])->name('affiliate-marketing.deleteProviderAccount');
                });

                Route::prefix('provider-details')->group(function () {
                    Route::get('', [AffiliateMarketingDataController::class, 'index'])->name('affiliate-marketing.provider.index');
                    Route::post('create', [AffiliateMarketingDataController::class, 'createAffiliateGroup'])->name('affiliate-marketing.provider.createGroup');
                    Route::post('update/{id}', [AffiliateMarketingDataController::class, 'updateAffiliateGroup'])->name('affiliate-marketing.provider.updateGroup');
                    Route::get('{id}', [AffiliateMarketingDataController::class, 'getAffiliateGroup'])->name('affiliate-marketing.provider.getGroup');
                    Route::post('sync', [AffiliateMarketingDataController::class, 'syncData'])->name('affiliate-marketing.provider.syncData');
                });

                Route::prefix('programs')->group(function () {
                    Route::get('', [AffiliateMarketingDataController::class, 'programIndex'])->name('affiliate-marketing.provider.program.index');
                    Route::get('commission-type', [AffiliateMarketingDataController::class, 'programCommissionType'])->name('affiliate-marketing.provider.program.commissionType');
                    Route::post('programme-sync', [AffiliateMarketingDataController::class, 'programSync'])->name('affiliate-marketing.provider.program.sync');
                });

                Route::prefix('commissions')->group(function () {
                    Route::get('', [AffiliateMarketingDataController::class, 'commissionIndex'])->name('affiliate-marketing.provider.commission.index');
                    Route::get('{id}', [AffiliateMarketingDataController::class, 'commissionGet'])->name('affiliate-marketing.provider.commission.get');
                    Route::post('update', [AffiliateMarketingDataController::class, 'commissionUpdate'])->name('affiliate-marketing.provider.commission.update');
                    Route::post('approve/{id}', [AffiliateMarketingDataController::class, 'commissionApproveDisapprove'])->name('affiliate-marketing.provider.commission.approveDisapprove');
                    Route::post('commission-sync', [AffiliateMarketingDataController::class, 'commissionSync'])->name('affiliate-marketing.provider.commission.sync');
                });

                Route::prefix('affiliates')->group(function () {
                    Route::get('', [AffiliateMarketingDataController::class, 'affiliateIndex'])->name('affiliate-marketing.provider.affiliate.index');
                    Route::get('{id}', [AffiliateMarketingDataController::class, 'affiliateGet'])->name('affiliate-marketing.provider.affiliate.get');
                    Route::post('create', [AffiliateMarketingDataController::class, 'affiliateCreate'])->name('affiliate-marketing.provider.affiliate.create');
                    Route::post('delete/{id}', [AffiliateMarketingDataController::class, 'affiliateDelete'])->name('affiliate-marketing.provider.affiliate.delete');
                    Route::get('payout-methods/{id}', [AffiliateMarketingDataController::class, 'affiliatePayoutMethods'])->name('affiliate-marketing.provider.affiliate.payoutMethods');
                    Route::post('update/payout-methods/{id}', [AffiliateMarketingDataController::class, 'affiliateUpdatePayoutMethod'])->name('affiliate-marketing.provider.affiliate.updatePayoutMethods');
                    Route::post('add-to-programme', [AffiliateMarketingDataController::class, 'affiliateAddToProgramme'])->name('affiliate-marketing.provider.affiliate.addToProgramme');
                    Route::post('affiliate-sync', [AffiliateMarketingDataController::class, 'affiliateSync'])->name('affiliate-marketing.provider.affiliate.sync');
                });

                Route::prefix('payments')->group(function () {
                    Route::get('', [AffiliateMarketingDataController::class, 'paymentsIndex'])->name('affiliate-marketing.provider.payments.index');
                    Route::post('create', [AffiliateMarketingDataController::class, 'paymentsCreate'])->name('affiliate-marketing.provider.payments.create');
                    Route::post('cancel/{id}', [AffiliateMarketingDataController::class, 'paymentsCancel'])->name('affiliate-marketing.provider.payments.cancel');
                    Route::post('payments-sync', [AffiliateMarketingDataController::class, 'paymentsSync'])->name('affiliate-marketing.provider.payments.sync');
                });

                Route::prefix('conversions')->group(function () {
                    Route::get('', [AffiliateMarketingDataController::class, 'conversionIndex'])->name('affiliate-marketing.provider.conversion.index');
                    Route::post('create', [AffiliateMarketingDataController::class, 'conversionCreate'])->name('affiliate-marketing.provider.conversion.create');
                    Route::post('update', [AffiliateMarketingDataController::class, 'conversionUpdate'])->name('affiliate-marketing.provider.conversion.update');
                    Route::post('delete/{id}', [AffiliateMarketingDataController::class, 'conversionDelete'])->name('affiliate-marketing.provider.conversion.delete');
                    Route::post('add-commission', [AffiliateMarketingDataController::class, 'conversionAddCommission'])->name('affiliate-marketing.provider.conversion.addCommission');
                    Route::post('conversion-sync', [AffiliateMarketingDataController::class, 'conversionSync'])->name('affiliate-marketing.provider.conversion.sync');
                });

                Route::prefix('customer')->group(function () {
                    Route::get('', [AffiliateMarketingDataController::class, 'customerIndex'])->name('affiliate-marketing.provider.customer.index');
                    Route::post('create', [AffiliateMarketingDataController::class, 'customerCreate'])->name('affiliate-marketing.provider.customer.create');
                    Route::post('delete/{id}', [AffiliateMarketingDataController::class, 'customerDelete'])->name('affiliate-marketing.provider.customer.delete');
                    Route::post('cancel/{id}', [AffiliateMarketingDataController::class, 'customerCancelUnCancel'])->name('affiliate-marketing.provider.customer.cancelUncancel');
                    Route::post('customer-sync', [AffiliateMarketingDataController::class, 'customerSync'])->name('affiliate-marketing.provider.customer.sync');
                });
            });

            Route::prefix('chat-gpt')->group(function () {
                Route::get('', [ChatGPTController::class, 'index'])->name('chatgpt.index');
                Route::get('request', [ChatGPTController::class, 'requestApi'])->name('chatgpt.request');
                Route::post('response', [ChatGPTController::class, 'getResponse'])->name('chatgpt.response');
            });

            // Create magento user.
            Route::prefix('magento-users')->group(function () {
                Route::get('', [MagentoUserFromErpController::class, 'index'])->name('magento-user-from-erp.index');
                Route::post('create', [MagentoUserFromErpController::class, 'magentoUserCreate'])->name('magento-user-from-erp.create');
                Route::post('roles', [MagentoUserFromErpController::class, 'getRoles'])->name('magento-user-from-erp.roles');
                Route::post('account-status', [MagentoUserFromErpController::class, 'accountStatus'])->name('magento-user-from-erp.account-status');
            });

            Route::get('monitor-jenkins-build/list', [MonitorJenkinsBuildController::class, 'list'])->name('monitor-jenkins-build.list');
            Route::resource('monitor-jenkins-build', MonitorJenkinsBuildController::class);
            Route::get('jenkins-build/truncate', [MonitorJenkinsBuildController::class, 'truncateJenkinsbulids'])->name('monitor-jenkins-build.truncate');
            Route::get('jenkins-build/insert-code-shortcut', [MonitorJenkinsBuildController::class, 'insertCodeShortcut'])->name('monitor-jenkins-insert-code-shortcut');

            /** Website Monitor */
            Route::prefix('monitor-server')->group(function () {
                Route::get('list', [MonitorServerController::class, 'list'])->name('monitor-server.list');
                Route::get('get-server-uptimes/{id}', [MonitorServerController::class, 'getServerUptimes'])->name('monitor-server.get-server-uptimes');
                Route::get('get-server-users/{id}', [MonitorServerController::class, 'getServerUsers'])->name('monitor-server.get-server-users');
                Route::get('get-server-history/{id}', [MonitorServerController::class, 'getServerHistory'])->name('monitor-server.get-server-history');
                Route::get('history/truncate', [MonitorServerController::class, 'logHistoryTruncate'])->name('monitor-server.log.history.truncate');
            });
            Route::resource('monitor-server', MonitorServerController::class);

            Route::get('deployement-version/list', [DeploymentVersionController::class, 'listDeploymentVersion'])->name('deployement-version.index');
            Route::get('deploye-version-jenkins', [DeploymentVersionController::class, 'deployVersion'])->name('deployement-version-jenkis');
            Route::get('/deploye-version/history/{id}', [DeploymentVersionController::class, 'deployVersionHistory'])->name('deployement-version-history');
            Route::post('restore-version-jenkins', [DeploymentVersionController::class, 'restoreRevision'])->name('deployement-restore-revision');

            Route::get('/git-actions', [GitHubActionController::class, 'index'])->name('git-action-lists');

            Route::prefix('magento-problems')->group(function () {
                Route::get('/', [MagentoProblemController::class, 'index'])->name('magento-problems-lists');
                Route::post('status/create', [MagentoProblemController::class, 'magentoProblemStatusCreate'])->name('magento-problems.status.create');
                Route::get('countdevtask/{id}', [MagentoProblemController::class, 'taskCount']);
                Route::post('updatestatus', [MagentoProblemController::class, 'updateStatus'])->name('magento-problems.updatestatus');
                Route::get('status/histories/{id}', [MagentoProblemController::class, 'magentoproblemsStatusHistories'])->name('magento-problems.status.histories');
                Route::post('updateuser', [MagentoProblemController::class, 'updateUser'])->name('magento-problems.updateuser');
                Route::get('user/histories/{id}', [MagentoProblemController::class, 'magentoproblemsUserHistories'])->name('magento-problems.user.histories');
            });

            Route::get('monit-status/list', [MonitStatusController::class, 'listMonitStatus'])->name('monit-status.index');
            Route::post('monit-status/command/run', [MonitStatusController::class, 'runCommand'])->name('monit-status.command.run');
            Route::get('monit-api-histories/{id}', [MonitStatusController::class, 'monitApiHistory'])->name('monit-status.api.histories');

            Route::prefix('indexerstate')->group(function () {
                Route::get('list', [IndexerStateController::class, 'index'])->name('indexer-state.index');
                Route::get('elastic_connection', [IndexerStateController::class, 'elasticConnect'])->name('indexer-state.elastic-conn');
                Route::get('reindex', [IndexerStateController::class, 'reindex'])->name('indexer-state.reindex');
                Route::post('save', [IndexerStateController::class, 'save'])->name('indexer-state.save');
                Route::get('masterslave', [IndexerStateController::class, 'masterSlave'])->name('indexer-state.master-slave');
                Route::get('logs/{id?}', [IndexerStateController::class, 'logs'])->name('indexer-state.logs');
            });

            //Import excel file for bank statement - S
            Route::prefix('bank-statement')->group(function () {
                Route::get('list', [BankStatementController::class, 'index'])->name('bank-statement.index');
                Route::get('import-file', [BankStatementController::class, 'showImportForm'])->name('bank-statement.import');
                Route::post('import-file/submit', [BankStatementController::class, 'import'])->name('bank-statement.import.submit');
                Route::get('import-file/map/{id}/{heading_row_number?}', [BankStatementController::class, 'map'])->name('bank-statement.import.map');
                Route::post('import-file/heading-row-number', [BankStatementController::class, 'heading_row_number_check'])->name('bank-statement.import.map.number.check');
                Route::post('import-file/map/{id}/{heading_row_number?}', [BankStatementController::class, 'map_import'])->name('bank-statement.import.map.submit');
                Route::get('import-file/mapped-data/{id}', [BankStatementController::class, 'mapped_data'])->name('bank-statement.import.mapped.data');
                Route::get('preview-file/{id}', [BankStatementController::class, 'previewFile'])->name('bank-statement.preview');
            });

            Route::post('user-search-global/', [UserController::class, 'searchUserGlobal'])->name('user-search-global');
            Route::resource('email-receiver-master', EmailReceiverMasterController::class);
            Route::resource('blog-centralize', BlogCentralizeController::class);

            //Mind Map
            Route::resource('mind-map', MindMapDiagramController::class);
            //Node Scrapper Category Mapping
            Route::put('scrapper-category-map/update-multiple', [NodeScrapperCategoryMapController::class, 'updateMultiple'])->name('scrapper-category-map.mutliple.update');
            Route::get('scrapper-category-map/get-category-by-search', [NodeScrapperCategoryMapController::class, 'getCategoryBySearch']);
            Route::get('scrapper-category-map/get-categories-by-id', [NodeScrapperCategoryMapController::class, 'getCategoryById']);
            Route::post('scrapper-category-map-paging', [NodeScrapperCategoryMapController::class, 'index'])->name('scrapper-category-map.paging');
            Route::resource('scrapper-category-map', NodeScrapperCategoryMapController::class);

            Route::prefix('appconnect')->group(function () {
                Route::get('/usage', [AppConnectController::class, 'getUsageReport'])->name('appconnect.app-users');
                Route::get('/sales', [AppConnectController::class, 'getSalesReport'])->name('appconnect.app-sales');
                Route::post('/column-visibility-update-app-sales', [AppConnectController::class, 'columnVisibilityUpdateAppSales'])->name('appconnect.app-sales.column.update');
                Route::get('/subscription', [AppConnectController::class, 'getSubscriptionReport'])->name('appconnect.app-sub');
                Route::get('/ads', [AppConnectController::class, 'getAdsReport'])->name('appconnect.app-ads');
                Route::get('/ratings', [AppConnectController::class, 'getRatingsReport'])->name('appconnect.app-rate');
                Route::get('/payments', [AppConnectController::class, 'getPaymentReport'])->name('appconnect.app-pay');
                Route::get('/usagefilter', [AppConnectController::class, 'getUsageReportfilter']);
                Route::get('/salesfilter', [AppConnectController::class, 'getSalesReportfilter']);
                Route::get('/subscriptionfilter', [AppConnectController::class, 'getSubscriptionReportfilter']);
                Route::get('/adsfilter', [AppConnectController::class, 'getAdsReportfilter']);
                Route::get('/ratingsfilter', [AppConnectController::class, 'getRatingsReportfilter']);
                Route::get('/paymentsfilter', [AppConnectController::class, 'getPaymentReportfilter']);
            });
        });

        Route::middleware(['auth', 'optimizeImages'])->group(function () {
            //Crop Reference
            Route::get('crop-references', [CroppedImageReferenceController::class, 'index']);
            Route::get('crop-references-grid', [CroppedImageReferenceController::class, 'grid'])->name('grid.reference');
            Route::get('crop-references-grid/cropStats', [CroppedImageReferenceController::class, 'cropStats']);
            Route::get('crop-references-grid/manage-instances', [CroppedImageReferenceController::class, 'manageInstance']);
            Route::post('crop-references-grid/add-instance', [CroppedImageReferenceController::class, 'addInstance']);
            Route::get('crop-references-grid/delete-instance', [CroppedImageReferenceController::class, 'deleteInstance']);
            Route::get('crop-references-grid/start-instance', [CroppedImageReferenceController::class, 'startInstance']);
            Route::get('crop-references-grid/stop-instance', [CroppedImageReferenceController::class, 'stopInstance']);
            //Ajax request for select2
            Route::get('/crop-references-grid/getCategories', [CroppedImageReferenceController::class, 'getCategories']);
            Route::get('/crop-references-grid/getProductIds', [CroppedImageReferenceController::class, 'getProductIds']);
            Route::get('/crop-references-grid/getBrands', [CroppedImageReferenceController::class, 'getBrands']);
            Route::get('/crop-references-grid/getSupplier', [CroppedImageReferenceController::class, 'getSupplier']);
            Route::get('crop-referencesx', [CroppedImageReferenceController::class, 'index']);
            Route::get('/crop-references-grid/log-instance', [CroppedImageReferenceController::class, 'loginstance']);
            Route::post('crop-references-visbility', [CroppedImageReferenceController::class, 'cropColumnVisbilityUpdate'])->name('crop_references.column.update');
            Route::get('crop-references-log-history', [CroppedImageReferenceController::class, 'cropReferencesLogs'])->name('crop-references.logs');

            Route::get('/magento/status', [MagentoController::class, 'addStatus']);
            Route::post('/magento/status/save', [MagentoController::class, 'saveStatus'])->name('magento.save.status');

            Route::post('crop-references-grid/reject', [CroppedImageReferenceController::class, 'rejectCropImage']);

            Route::get('public-key', [EncryptController::class, 'index'])->name('encryption.index');
            Route::post('save-key', [EncryptController::class, 'saveKey'])->name('encryption.save.key');
            Route::post('forget-key', [EncryptController::class, 'forgetKey'])->name('encryption.forget.key');

            Route::get('reject-listing-by-supplier', [ProductController::class, 'rejectedListingStatistics']);
            Route::get('lead-auto-fill-info', [LeadsController::class, 'leadAutoFillInfo']);

            Route::get('color-reference/used-products', [ColorReferenceController::class, 'usedProducts']);

            Route::get('color-reference-fix-issue', [ColorReferenceController::class, 'cmdcallcolorfix'])->name('erp-color-fix-cmd');

            Route::get('color-reference/affected-product', [ColorReferenceController::class, 'affectedProduct']);
            Route::post('color-reference/update-color', [ColorReferenceController::class, 'updateColor']);
            Route::post('color-reference/update-color-miltiple', [ColorReferenceController::class, 'updateColorMultiple']);
            Route::resource('color-reference', ColorReferenceController::class);
            Route::get('color-reference-group', [ColorReferenceController::class, 'groupColor']);
            Route::get('/color-reference/group/{name}/{threshold}', [ColorReferenceController::class, 'colorGroupBy']);

            Route::get('compositions/{id}/used-products', [CompositionsController::class, 'usedProducts'])->name('compositions.used-products');
            Route::get('compositions/affected-product', [CompositionsController::class, 'affectedProduct']);
            Route::post('compositions/update-composition', [CompositionsController::class, 'updateComposition']);
            Route::post('compositions/update-multiple-composition', [CompositionsController::class, 'updateMultipleComposition']);
            Route::post('compositions/update-all-composition', [CompositionsController::class, 'updateAllComposition']);
            Route::post('compositions/replace-composition', [CompositionsController::class, 'replaceComposition'])->name('compositions.replace');
            Route::get('compositions/{id}/history', [CompositionsController::class, 'history'])->name('compositions.history');
            Route::get('compositions/delete-unused', [CompositionsController::class, 'deleteUnused'])->name('compositions.delete.unused');
            Route::post('compositions/update-name', [CompositionsController::class, 'updateName'])->name('compositions.update.name');
            Route::get('compositions/groups', [CompositionsController::class, 'compositionsGroups'])->name('compositions.groups');
            Route::get('compositions/group/{threshold}', [CompositionsController::class, 'compositionsGroupBy']);
            Route::post('compositions/delete-composition', [CompositionsController::class, 'deleteComposition'])->name('compositions.delete');
            Route::resource('compositions', CompositionsController::class);
            Route::get('incorrect-attributes', [UnknownAttributeProductController::class, 'index'])->name('incorrect-attributes');
            Route::post('attribute-assignment', [UnknownAttributeProductController::class, 'attributeAssignment'])->name('incorrect-attributes.attribute-assignment');
            Route::post('get-product-attribute-details', [UnknownAttributeProductController::class, 'getProductAttributeDetails'])->name('incorrect-attributes.get_product_attribute_detail');
            Route::post('get-product-attribute-history', [UnknownAttributeProductController::class, 'getProductAttributeHistory'])->name('incorrect-attributes.get_product_attribute_history');
            Route::post('update-attribute-assignment', [UnknownAttributeProductController::class, 'updateAttributeAssignment'])->name('incorrect-attributes.update-attribute-assignment');
            Route::get('crop-rejected-final-approval-images', [CropRejectedController::class, 'index'])->name('crop-rejected-final-approval-images');

            Route::post('descriptions/store', [ChangeDescriptionController::class, 'store'])->name('descriptions.store');

            Route::post('descriptions/delete', [ChangeDescriptionController::class, 'destroy'])->name('descriptions.delete');

            Route::resource('descriptions', ChangeDescriptionController::class)->except(['store']);

            Route::get('crop/approved', [ProductCropperController::class, 'getApprovedImages'])->name('product.crop.approved');
            Route::get('order-cropped-images', [ProductCropperController::class, 'showCropVerifiedForOrdering'])->name('product.order.cropped.images');
            Route::post('save-sequence/{id}', [ProductCropperController::class, 'saveSequence']);
            Route::get('skip-sequence/{id}', [ProductCropperController::class, 'skipSequence']);
            Route::get('reject-sequence/{id}', [ProductCropperController::class, 'rejectSequence']);
            Route::post('ammend-crop/{id}', [ProductCropperController::class, 'ammendCrop']);
            Route::get('products/auto-cropped', [ProductCropperController::class, 'getListOfImagesToBeVerified'])->name('product.auto.cropped');
            Route::get('products/crop-issue-summary', [ProductCropperController::class, 'cropIssuesPage'])->name('product.crop.issue.summary');
            Route::get('products/rejected-auto-cropped', [ProductCropperController::class, 'showRejectedCrops'])->name('product.rejected.auto.cropped');
            Route::get('products/auto-cropped/{id}', [ProductCropperController::class, 'showImageToBeVerified']);
            Route::get('products/auto-cropped/{id}/show-rejected', [ProductCropperController::class, 'showRejectedImageToBeverified']);
            Route::get('products/auto-cropped/{id}/approve', [ProductCropperController::class, 'approveCrop']);
            Route::post('products/auto-cropped/{id}/approve-rejected', [ProductCropperController::class, 'approveRejectedCropped']);
            Route::get('products/auto-cropped/{id}/reject', [ProductCropperController::class, 'rejectCrop']);
            Route::get('products/auto-cropped/{id}/crop-approval-confirmation', [ProductCropperController::class, 'cropApprovalConfirmation']);
            Route::get('customer/livechat-redirect', [LiveChatController::class, 'reDirect']);
            Route::get('livechat/setting', [LiveChatController::class, 'setting']);
            Route::post('livechat/save', [LiveChatController::class, 'save'])->name('livechat.save');
            Route::post('livechat/remove', [LiveChatController::class, 'remove'])->name('livechat.remove');
            Route::resource('roles', RoleController::class)->except(['update']);
            Route::post('roles/update/{id}', [RoleController::class, 'update'])->name('roles.update');
            Route::resource('permissions', PermissionController::class);
            Route::get('permissions/grandaccess/users', [PermissionController::class, 'users'])->name('permissions.users');
            Route::get('permissions/grandaccess/delete', [PermissionController::class, 'delete_record'])->name('permissions.delete');
            Route::get('unauthorized', [RoleController::class, 'unAuthorized']);
            Route::get('search_role', [RoleController::class, 'search_role'])->name('search_role');
            Route::get('users/logins', [UserController::class, 'login'])->name('users.login.index');
            Route::post('users/status-change', [UserController::class, 'statusChange']);
            Route::get('users/loginips', [UserController::class, 'loginIps'])->name('users.login.ips');
            Route::get('users/add-system-ip', [UserController::class, 'addSystemIp']);
            Route::post('users/add-system-ip-from-text', [UserController::class, 'addSystemIpFromText']);
            Route::get('users/delete-system-ip', [UserController::class, 'deleteSystemIp']);
            Route::get('users/bulk-delete-system-ip', [UserController::class, 'bulkDeleteSystemIp']);
            Route::get('permissions/grandaccess/users', [PermissionController::class, 'users'])->name('permissions.users');
            Route::get('userlogs', [UserLogController::class, 'index'])->name('userlogs.index');
            Route::get('userlogs/{$id}', [UserLogController::class, 'index']);
            Route::get('userlogs/datatables', [UserLogController::class, 'getData'])->name('userlogs.datatable');
            Route::get('users/{id}/assigned', [UserController::class, 'showAllAssignedProductsForUser']);
            Route::post('users/{id}/unassign/products', [UserController::class, 'unassignProducts']);
            Route::post('users/{id}/assign/products', [UserController::class, 'assignProducts'])->name('user.assign.products');
            Route::post('users/{id}/activate', [UserController::class, 'activate'])->name('user.activate');
            Route::resource('users', UserController::class);
            Route::resource('listing-payments', ListingPaymentsController::class);
            Route::post('users/changeWhatsapp', [UserController::class, 'changeWhatsapp'])->name('user.changewhatsapp');

            Route::get('products/assign-product', [ProductController::class, 'getPreListProducts'])->name('products.product-assign');
            Route::post('products/assign-product', [ProductController::class, 'assignProduct'])->name('products.product-assign-submit');
            Route::post('products/assign-product/no-wise', [ProductController::class, 'assignProductNoWise'])->name('products.product-assign-no-wise');
            // Translation Language
            Route::post('translationLanguage/add', [ProductController::class, 'translationLanguage'])->name('translation.language.add');
            // Product Translation Rejection
            Route::post('productTranslation/reject', [ProductController::class, 'productTranslationRejection'])->name('product.translation.rejection');

            Route::get('products/product-translation', [ProductController::class, 'productTranslation'])->name('products.product-translation');
            Route::get('products/product-translation/{id}', [ProductController::class, 'viewProductTranslation'])->name('products.product-translation.view');
            Route::post('products/product-translation/submit/{producsed}', [ProductController::class, 'editProductTranslation'])->name('products.product-translation.edit');
            Route::get('products/product-translation/details/{id}/{locale}', [ProductController::class, 'getProductTranslationDetails'])->name('products.product-translation.locale');
            Route::get('product/listing/users', [ProductController::class, 'showListigByUsers']);
            Route::get('products/listing', [ProductController::class, 'listing'])->name('products.listing');
            Route::get('products/listing/final', [ProductController::class, 'approvedListing'])->name('products.listing.approved');
            Route::post('products-listing-final-column-visbility', [ProductController::class, 'plfColumnVisbilityUpdate'])->name('products.plfcolumn.update');
            Route::post('products-listing-final/statuscolor', [ProductController::class, 'statuscolor'])->name('products.statuscolor');
            Route::get('products/listing/conditions-check', [ProductController::class, 'magentoConditionsCheck'])->name('products.magentoConditionsCheck');
            Route::post('products/listing/autocompleteForFilter', [ProductController::class, 'autocompleteForFilter'])->name('products.autocompleteForFilter');
            Route::get('products/product-translation/{id}/get-translated-text-score', [ProductController::class, 'getTranslatedTextScore'])->name('products.product-translation.get-translated-text-score');

            Route::get('products/listing/conditions-check-logs/{llm_id}/{swId}', [ProductController::class, 'magentoConditionsCheckLogs'])->name('products.magentoConditionsCheckLogs');
            Route::get('products/get-loglist-magento-detail/{llm_id}', [ProductController::class, 'getLogListMagentoDetail'])->name('products.getLogListMagentoDetail');
            Route::get('products/push/magento/conditions', [ProductController::class, 'pushToMagentoConditions'])->name('products.push.conditions');
            Route::get('products/conditions/status/update', [ProductController::class, 'updateConditionStatus'])->name('products.push.condition.update');
            Route::get('products/listing/final/{images?}', [ProductController::class, 'approvedListing'])->name('products.listing.approved.images');
            Route::get('products/conditions/upteamstatus/update', [ProductController::class, 'updateConditionUpteamStatus'])->name('products.push.condition.update.upteam.status');
            Route::get('products/listing/scrapper/{images?}', [ProductController::class, 'approvedScrapperImages'])->name('products.listing.scrapper.images');
            Route::get('products/listing/scrapper/{images}/{id}', [ProductController::class, 'approvedScrapperImagesCompare'])->name('products.listing.scrapper.images.comare');
            Route::post('products/listing/scrapper-images-truncate', [ProductController::class, 'truncateScrapperImagesMedia'])->name('products.listing.scrapper.images.truncate');

            Route::post('products/listing/final/pushproduct', [ProductController::class, 'pushProduct']);
            Route::post('products/listing/final/process-conditions-check', [ProductController::class, 'processProductsConditionsCheck'])->name('products.processProductsConditionsCheck');
            Route::post('products/listing/push-to-magento', [ProductController::class, 'pushProductsToMagento'])->name('products.pushToMagento');
            Route::get('products/listing/magento-push-status', [ProductController::class, 'magentoPushStatusForMagentoCheck'])->name('products.magentoPushStatus');
            Route::post('products/changeautopushvalue', [ProductController::class, 'changeAutoPushValue']);
            Route::post('products/listing/magento-push-status/autocomplete', [ProductController::class, 'autocompleteSearchPushStatus'])->name('products.autocompleteSearchPushStatus');
            Route::post('product/image/order/change', [ProductController::class, 'changeimageorder']);

            Route::post('products/customer/charity/savewebsite', [CustomerCharityController::class, 'savewebsite']);
            Route::post('products/customer/charity/getwebsite', [CustomerCharityController::class, 'getwebsite']);
            Route::post('products/customer/charity/deletewebsite', [CustomerCharityController::class, 'deletewebsite']);

            Route::get('products/customer/charity', [CustomerCharityController::class, 'index'])->name('customer.charity');
            Route::post('products/customer/charity/{id?}', [CustomerCharityController::class, 'store'])->name('customer.charity.post');
            Route::delete('products/customer/charity/{id?}', [CustomerCharityController::class, 'delete'])->name('customer.charity.delete');
            Route::get('customer-charity-search', [CustomerCharityController::class, 'charitySearch'])->name('customer.charity-search');
            Route::get('customer-charity-email', [CustomerCharityController::class, 'charityEmail'])->name('customer.charity-email');
            Route::get('customer-charity-phone-number', [CustomerCharityController::class, 'charityPhoneNumber'])->name('customer.charity-phone-number');
            Route::get('customer-charity/get-websites/{id}', [CustomerCharityController::class, 'charityWebsites'])->name('charity.websites');
            Route::post('customer-charity/get-websites/{id}', [CustomerCharityController::class, 'addCharityWebsites'])->name('save.charity.websites');
            Route::get('customer-charity/get-website-store/{charity_id}', [CustomerCharityController::class, 'getCharityWebsiteStores'])->name('charity.website.stores');

            Route::get('products/listing/final-crop', [ProductController::class, 'approvedListingCropConfirmation']);
            Route::get('products/get-push-websites', [ProductController::class, 'getWebsites']);
            Route::post('products/listing/final-crop-image', [ProductController::class, 'cropImage'])->name('products.crop.image');

            Route::get('products/listing/magento', [ProductController::class, 'approvedMagento'])->name('products.listing.magento');
            Route::get('products/listing/rejected', [ProductController::class, 'showRejectedListedProducts']);
            Route::get('product/listing-remark', [ProductController::class, 'addListingRemarkToProduct'])->name('product.listing.magento.remark');
            Route::get('product/update-listing-remark', [ProductController::class, 'updateProductListingStats']);
            Route::post('product/crop_rejected_status', [ProductController::class, 'crop_rejected_status']);
            Route::post('product/all_crop_rejected_status', [ProductController::class, 'all_crop_rejected_status']);

            // Added Mass Action
            Route::get('product/delete-product', [ProductController::class, 'deleteProduct'])->name('products.mass.delete');
            Route::get('products/approveProduct', [ProductController::class, 'approveProduct'])->name('products.mass.approve');
            Route::get('product/relist-product', [ProductController::class, 'relistProduct']);
            Route::get('products/stats', [ProductController::class, 'productStats']);

            Route::get('products/size', [ProductController::class, 'productSizeLog'])->name('products.size');
            Route::get('products/status-history', [ProductController::class, 'productScrapLog'])->name('products.status-history');
            Route::post('products/getsuppliers', [ProductController::class, 'getProductSupplierList'])->name('products.getsuppliers');
            Route::get('products/description', [ProductController::class, 'productDescription'])->name('products.description');
            Route::get('/product-hisotry', [ProductController::class, 'productDescriptionHistory'])->name('scrap.product-hisotry');
            Route::post('products/description/update', [ProductController::class, 'productDescriptionUpdate'])->name('products.description.update');
            Route::get('products/multi-description', [ProductController::class, 'productMultiDescription'])->name('products.multidescription');
            Route::post('products/multi-description-sky-check', [ProductController::class, 'productMultiDescriptionCheck'])->name('products.multidescription.skucheck');
            Route::get('/products/multi-description-sku', [ProductController::class, 'productMultiDescriptionSku'])->name('products.multidescription.sku');
            Route::post('products/multi-description-sky-update', [ProductController::class, 'productMultiDescriptionUpdate'])->name('products.multidescription.update');
            Route::post('products-status-history-column-visbility', [ProductController::class, 'columnVisbilityUpdate'])->name('products.column.update');
            Route::post('products/{id}/updateName', [ProductController::class, 'updateName']);
            Route::post('products/{id}/updateDescription', [ProductController::class, 'updateDescription']);
            Route::post('products/{id}/updateComposition', [ProductController::class, 'updateComposition']);
            Route::post('products/{id}/updateColor', [ProductController::class, 'updateColor']);
            Route::post('products/{id}/updateCategory', [ProductController::class, 'updateCategory']);
            Route::post('products/{id}/updateSize', [ProductController::class, 'updateSize']);
            Route::post('products/{id}/updatePrice', [ProductController::class, 'updatePrice']);
            Route::get('products/{id}/quickDownload', [ProductController::class, 'quickDownload'])->name('products.quick.download');
            Route::post('products/{id}/quickUpload', [ProductController::class, 'quickUpload'])->name('products.quick.upload');
            Route::any('products/{id}/listMagento', [ProductController::class, 'listMagento']);
            Route::any('products/pushProductTest', [ProductController::class, 'pushProductTest'])->name('products.push.product.test');
            Route::post('products/multilistMagento', [ProductController::class, 'multilistMagento']);
            Route::post('products/{id}/unlistMagento', [ProductController::class, 'unlistMagento']);
            Route::post('products/{id}/approveMagento', [ProductController::class, 'approveMagento']);
            Route::post('products/{id}/updateMagento', [ProductController::class, 'updateMagento']);
            Route::post('products/updateMagentoProduct', [ProductController::class, 'updateMagentoProduct'])->name('product.update.magento');
            Route::post('products/{id}/approveProduct', [ProductController::class, 'approveProduct']);
            Route::post('products/{id}/originalCategory', [ProductController::class, 'originalCategory']);
            Route::post('products/{id}/originalColor', [ProductController::class, 'originalColor']);
            Route::post('products/{id}/submitForApproval', [ProductController::class, 'submitForApproval']);
            Route::get('products/{id}/category-history', [ProductCategoryController::class, 'history']);
            Route::get('products/{id}/get-translation-product', [ProductController::class, 'getTranslationProduct']);
            Route::post('products/{id}', [ProductController::class, 'updateApprovedBy']);
            Route::post('products/add/def_cust/{id}', [ProductController::class, 'add_product_def_cust'])->name('products.add.def_cust');
            Route::post('products/{id}/changeCategorySupplier', [ProductController::class, 'changeAllCategoryForAllSupplierProducts']);
            Route::post('products/{id}/changeColorSupplier', [ProductController::class, 'changeAllColorForAllSupplierProducts']);
            Route::resource('products', ProductController::class);
            Route::resource('attribute-replacements', AttributeReplacementController::class);
            Route::post('products/bulk/update', [ProductController::class, 'bulkUpdate'])->name('products.bulk.update');
            Route::post('products/{id}/archive', [ProductController::class, 'archive'])->name('products.archive');
            Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
            Route::get('/manual-image-upload', [ProductSelectionController::class, 'manualImageUpload'])->name('manual.image.upload');
            Route::resource('productselection', ProductSelectionController::class);
            Route::get('productattribute/delSizeQty/{id}', [ProductAttributeController::class, 'delSizeQty']);
            Route::resource('productattribute', ProductAttributeController::class);
            Route::resource('productsearcher', ProductSearcherController::class);
            Route::resource('productimagecropper', ProductCropperController::class);
            Route::resource('productsupervisor', ProductSupervisorController::class);
            Route::resource('productlister', ProductListerController::class);
            Route::resource('productapprover', ProductApproverController::class);

            Route::get('productinventory/product-images/{id}', [ProductInventoryController::class, 'getProductImages'])->name('productinventory.product-images');
            Route::get('productinventory/out-of-stock', [ProductInventoryController::class, 'getStockwithZeroQuantity'])->name('productinventory.out-of-stock');
            Route::get('productinventory/out-of-stock-product-log', [ProductInventoryController::class, 'outOfStockProductLog'])->name('productinventory.out-of-stock-product-log');
            Route::get('productinventory/product-rejected-images/{id}', [ProductInventoryController::class, 'getProductRejectedImages'])->name('productinventory.product-rejected-images');
            Route::post('productinventory/import', [ProductInventoryController::class, 'import'])->name('productinventory.import');
            Route::get('productinventory/list', [ProductInventoryController::class, 'list'])->name('productinventory.list');
            Route::get('productinventory/inventory-list', [ProductInventoryController::class, 'inventoryList'])->name('productinventory.inventory-list');
            Route::post('productinventory-column-visbility', [ProductInventoryController::class, 'columnVisbilityUpdate'])->name('productinventory.column.update');
            Route::get('productinventory/new-inventory-list', [ProductInventoryController::class, 'inventoryListNew'])->name('productinventory.inventory-list-new');
            Route::get('download-report', [ProductInventoryController::class, 'downloadReport'])->name('download-report');
            Route::get('download-scrapped-report', [ProductInventoryController::class, 'downloadScrapReport'])->name('download-scrapped-report');
            Route::post('productinventory/change-size-system', [ProductInventoryController::class, 'changeSizeSystem'])->name('productinventory.change-size-system');
            Route::post('productinventory/change-product-status', [ProductInventoryController::class, 'updateStatus'])->name('productinventory.update-status');
            Route::post('productinventory/store-erp-size', [ProductInventoryController::class, 'changeErpSize'])->name('productinventory.change-erp-size');
            Route::get('productinventory/scrape-log', [ProductInventoryController::class, 'scrapelog']);
            Route::get('productinventory/inventory-history/{id}', [ProductInventoryController::class, 'inventoryHistory'])->name('productinventory.inventory-history');
            Route::post('productinventory/merge-scrap-brand', [ProductInventoryController::class, 'mergeScrapBrand'])->name('productinventory.merge-scrap-brand');

            Route::get('product/history/by/supplier', [ProductInventoryController::class, 'supplierProductHistory'])->name('supplier.product.history');
            Route::get('product/history/by/supplier-brand', [ProductInventoryController::class, 'supplierProductHistoryBrand'])->name('supplier.product.history.brand');
            Route::get('product/discount/files', [ProductInventoryController::class, 'supplierDiscountFiles'])->name('supplier.discount.files');
            Route::post('product/discount/files', [ProductInventoryController::class, 'exportExcel'])->name('supplier.discount.files.post');
            Route::get('product/discount/excel/files', [ProductInventoryController::class, 'download_excel'])->name('excel.files');
            Route::post('product/mapping/excel', [ProductInventoryController::class, 'mapping_excel'])->name('product.mapping.excel');
            Route::post('product/export/mapping/excel', [ProductInventoryController::class, 'export_mapping_excel'])->name('product.mapping.export.excel');

            Route::get('supplier/{supplier}/products/summary/', [ProductInventoryController::class, 'supplierProductSummary'])->name('supplier.product.summary');

            Route::get('productinventory/all-suppliers/{id}', [ProductInventoryController::class, 'getSuppliers'])->name('productinventory.all-suppliers');
            Route::resource('productinventory', ProductInventoryController::class);

            Route::prefix('product-inventory')->group(function () {
                Route::get('/', [NewProductInventoryController::class, 'index'])->name('product-inventory.new');
                Route::get('/upteam/logs', [NewProductInventoryController::class, 'upteamLogs'])->name('product-inventory.upteam.logs');
                Route::get('fetch/images', [NewProductInventoryController::class, 'fetchImgGoogle'])->name('product-inventory.fetch.img.google');
                Route::post('/push-in-shopify-records', [NewProductInventoryController::class, 'pushInStore'])->name('product-inventory.pushInStore');
                Route::prefix('{id}')->group(function () {
                    Route::get('push-in-shopify', [NewProductInventoryController::class, 'pushInShopify'])->name('product-inventory.push-in-shopify');
                });
                Route::get('/search/sku', [NewProductInventoryController::class, 'autoSuggestSku']);
            });

            Route::prefix('google-drive')->group(function () {
                Route::get('/', [GoogleDriveController::class, 'index'])->name('google-drive.new');
                Route::post('/create', [GoogleDriveController::class, 'create'])->name('google-drive.create');
            });

            Route::get('log_history/discount/', [ProductInventoryController::class, 'discountlogHistory'])->name('log-history/discount/brand');

            Route::post('facebook-posts/save', [FacebookPostController::class, 'store'])->name('facebook-posts/save');
            Route::get('facebook-posts/create', [FacebookPostController::class, 'create'])->name('facebook-posts.create');
            Route::resource('facebook-posts', FacebookPostController::class);

            Route::post('facebook-posts/save', [FacebookPostController::class, 'store'])->name('facebook-posts/save');
            Route::get('facebook-posts/create', [FacebookPostController::class, 'create'])->name('facebook-posts.create');
            Route::resource('facebook-posts', FacebookPostController::class);

            Route::resource('sales', SaleController::class);
            Route::resource('stock', StockController::class);
            Route::post('stock/track/package', [StockController::class, 'trackPackage'])->name('stock.track.package');
            Route::delete('stock/{id}/permanentDelete', [StockController::class, 'permanentDelete'])->name('stock.permanentDelete');
            Route::post('stock/privateViewing/create', [StockController::class, 'privateViewingStore'])->name('stock.privateViewing.store');
            Route::get('stock/private/viewing', [StockController::class, 'privateViewing'])->name('stock.private.viewing');
            Route::delete('stock/private/viewing/{id}', [StockController::class, 'privateViewingDestroy'])->name('stock.private.viewing.destroy');
            Route::post('stock/private/viewing/upload', [StockController::class, 'privateViewingUpload'])->name('stock.private.viewing.upload');
            Route::post('stock/private/viewing/{id}/updateStatus', [StockController::class, 'privateViewingUpdateStatus'])->name('stock.private.viewing.updateStatus');
            Route::post('stock/private/viewing/{id}/updateOfficeBoy', [StockController::class, 'updateOfficeBoy'])->name('stock.private.viewing.updateOfficeBoy');

            Route::post('sop', [SopController::class, 'store'])->name('sop.store');
            Route::get('sop', [SopController::class, 'index'])->name('sop.index');

            Route::post('sop/category', [SopController::class, 'categoryStore'])->name('sop.category'); // sop category store route
            Route::get('sop/category-list', [SopController::class, 'categorylist'])->name('sop.categorylist'); // sop category store route
            Route::get('sop/category-listajax', [SopController::class, 'categorylistajax'])->name('sop.categorylistajax'); // sop category store route
            Route::delete('sop/category/delete', [SopController::class, 'categoryDelete'])->name('sop.category.delete'); // sop category store route
            Route::post('sop/category/update', [SopController::class, 'categoryUpdate'])->name('sop.category.update'); // sop category store route
            Route::post('sop/update-sop-category', [SopController::class, 'updateSopCategory'])->name('sop.update-sop-category');

            Route::delete('sop/{id}', [SopController::class, 'delete'])->name('sop.delete');
            Route::get('sop/edit', [SopController::class, 'edit'])->name('editName');
            Route::post('update', [SopController::class, 'update'])->name('updateName');
            Route::get('sop/search', [SopController::class, 'search']);
            Route::get('sop/search-ajax', [SopController::class, 'ajaxsearch'])->name('menu.sop.search');
            Route::get('sop/search-modal', [SopController::class, 'getSopSearchModal'])->name('menu.sop.searchmodal');
            Route::get('email/search-ajax', [EmailController::class, 'ajaxsearch'])->name('menu.email.search');
            Route::get('email/search-modal', [EmailController::class, 'getEmailSearchModal'])->name('menu.email.searchmodal');
            Route::get('soplogs', [SopController::class, 'sopnamedata_logs'])->name('sopname.logs');
            Route::get('sop/DownloadData/{id}', [SopController::class, 'downloaddata'])->name('sop.download');
            Route::get('sop/permission-data', [SopController::class, 'sopPermissionData'])->name('sop.permission-data');
            Route::get('sop/permission-list', [SopController::class, 'sopPermissionList'])->name('sop.permission-list');
            Route::get('sop/permission/user-list', [SopController::class, 'sopPermissionUserList'])->name('sop.permission.user-list');
            Route::get('sop/remove-permission', [SopController::class, 'sopRemovePermission'])->name('sop.remove.permission');

            Route::get('product/delete-image', [ProductController::class, 'deleteImage'])->name('product.deleteImages');

            Route::post('create/shortcut-sop', [SopShortcutCreateController::class, 'createShortcut'])->name('shortcut.sop.create');

            // Delivery Approvals
            Route::post('deliveryapproval/{id}/updateStatus', [DeliveryApprovalController::class, 'updateStatus'])->name('deliveryapproval.updateStatus');
            Route::resource('deliveryapproval', DeliveryApprovalController::class);

            Route::get('brand/list', [BrandController::class, 'show'])->name('brand.list'); //Purpose : upload logo - DEVTASK-4278
            Route::get('brand/get_all_images', [BrandController::class, 'get_all_images'])->name('brand.get_all_images'); //Purpose : upload logo - DEVTASK-4278
            Route::get('brand/logo_data', [BrandController::class, 'fetchlogos'])->name('brand.logo_data'); //Purpose : Get Brand Logo - DEVTASK-4278
            Route::post('brand/uploadlogo', [BrandController::class, 'uploadlogo'])->name('brand.uploadlogo'); //Purpose : upload logo - DEVTASK-4278
            Route::post('brand/set_logo_with_brand', [BrandController::class, 'set_logo_with_brand'])->name('brand.set_logo_with_brand'); //Purpose : upload logo with brand - DEVTASK-4278
            Route::post('brand/remove_logo', [BrandController::class, 'remove_logo'])->name('brand.remove_logo'); //Purpose : remove logo - DEVTASK-4278
            Route::post('brand/assign-default-value', [BrandController::class, 'assignDefaultValue'])->name('brand.assignDefaultValue'); //Purpose : remove logo - DEVTASK-4278

            // For Brand size chart
            Route::get('brand/size/chart', [BrandSizeChartController::class, 'index'])->name('brand/size/chart');
            Route::get('brand/create/size/chart', [BrandSizeChartController::class, 'createSizeChart'])->name('brand/create/size/chart');
            Route::post('brand/store/size/chart', [BrandSizeChartController::class, 'storeSizeChart'])->name('brand/store/size/chart');
            Route::post('brand/size/get-child', [BrandSizeChartController::class, 'getChild'])->name('brand.getChild');

            Route::post('brand/store-category-segment-discount', [BrandController::class, 'storeCategorySegmentDiscount'])->name('brand.store_category_segment_discount');
            Route::post('brand/attach-website', [BrandController::class, 'attachWebsite']);
            Route::post('brand/change-segment', [BrandController::class, 'changeSegment']);
            Route::post('brand/next-step', [BrandController::class, 'changeNextStep']);
            Route::post('brand/update-reference', [BrandController::class, 'updateReference']);
            Route::post('brand/merge-brand', [BrandController::class, 'mergeBrand']);
            Route::post('brand/unmerge-brand', [BrandController::class, 'unMergeBrand'])->name('brand.unmerge-brand');
            Route::get('brand/{id}/create-remote-id', [BrandController::class, 'createRemoteId']);
            Route::get('brand/{id}/activities', [BrandController::class, 'activites'])->name('brand.activities');
            Route::get('brand/fetch-new', [BrandController::class, 'fetchNewBrands'])->name('brand.fetchnew');
            Route::post('brand/approve', [BrandController::class, 'approve']);
            Route::resource('brand', BrandController::class);
            Route::put('brand/priority/{id}', [BrandController::class, 'priority']);

            Route::get('get-subcategories', [ReplyController::class, 'getSubcategories']);
            Route::get('reply/editreply', [ReplyController::class, 'editReply'])->name('editReply');
            Route::resource('reply', ReplyController::class);

            Route::post('reply/statuscolor', [ReplyController::class, 'statusColor'])->name('reply.statuscolor');
            Route::post('reply/category/setDefault', [ReplyController::class, 'categorySetDefault'])->name('reply.category.setDefault');
            Route::post('reply/chatbot/questions', [ReplyController::class, 'chatBotQuestionT'])->name('reply.create.chatbot_questions');
            Route::post('reply/category/store', [ReplyController::class, 'categoryStore'])->name('reply.category.store');
            Route::post('reply/subcategory/store', [ReplyController::class, 'subcategoryStore'])->name('reply.subcategory.store');
            Route::get('reply-list', [ReplyController::class, 'replyList'])->name('reply.replyList');
            Route::post('reply-list/delete', [ReplyController::class, 'replyListDelete'])->name('reply.replyList.delete');
            Route::post('reply-list/update', [ReplyController::class, 'replyUpdate'])->name('reply.replyUpdate');
            Route::post('reply-list/update-stores', [ReplyController::class, 'replyUpdateStores'])->name('reply.replyUpdateStores');
            Route::get('reply-history', [ReplyController::class, 'getReplyedHistory'])->name('reply.replyhistory');
            Route::get('reply-logs', [ChatbotMessageLogsController::class, 'replyLogs'])->name('reply.replylogs');
            Route::post('reply-translate', [ReplyController::class, 'replyTranslate'])->name('reply.replytranslate');
            Route::get('reply-translate-list', [ReplyController::class, 'replyTranslateList'])->name('reply.replyTranslateList');

            Route::post('/reply-translate-list/update', [ReplyController::class, 'replyTranslateUpdate'])->name('reply.replyTranslateupdate');
            Route::post('/reply-translate-list/history', [ReplyController::class, 'replyTranslatehistory'])->name('reply.replyTranslatehistory');
            Route::post('/reply-translate-list/approvedByAdmin', [ReplyController::class, 'approvedByAdmin'])->name('reply.approved_by_admin');
            Route::post('/reply-translate-list/permissions', [ReplyController::class, 'quickRepliesPermissions'])->name('reply.permissions');

            Route::post('/reply-translate-list/removepermissions', [ReplyController::class, 'removepermissions'])->name('remove.permissions');
            Route::post('/reply-translate-list/get-translated-text-score', [ReplyController::class, 'getTranslatedTextScore'])->name('reply.get-multi-score');

            Route::post('show-reply-logs', [ReplyController::class, 'show_logs'])->name('reply.show_logs');
            Route::get('reply/log/lists', [ReplyController::class, 'replyLogList'])->name('reply.listing');
            Route::post('reply-mulitiple/flag', [ReplyController::class, 'replyMulitiple'])->name('reply.mulitiple.flag');

            // Auto Replies
            Route::post('autoreply/{id}/updateReply', [AutoReplyController::class, 'updateReply']);
            Route::post('autoreply/delete-chat-word', [AutoReplyController::class, 'deleteChatWord']);
            Route::get('autoreply/replied-chat/{id}', [AutoReplyController::class, 'getRepliedChat']);
            Route::post('autoreply/save-group', [AutoReplyController::class, 'saveGroup'])->name('autoreply.save.group');
            Route::post('autoreply/save-group/phrases', [AutoReplyController::class, 'saveGroupPhrases'])->name('autoreply.save.group.phrases');
            Route::post('autoreply/save-by-question', [AutoReplyController::class, 'saveByQuestion']);
            Route::post('autoreply/delete-most-used-phrases', [AutoReplyController::class, 'deleteMostUsedPharses'])->name('chatbot.delete-most-used-pharses');
            Route::get('autoreply/get-phrases', [AutoReplyController::class, 'getPhrases']);
            Route::post('autoreply/phrases/reply', [AutoReplyController::class, 'getPhrasesReply'])->name('autoreply.group.phrases.reply');
            Route::get('autoreply/phrases/reply-response', [AutoReplyController::class, 'getPhrasesReplyResponse'])->name('autoreply.group.phrases.reply.response');
            Route::resource('autoreply', AutoReplyController::class);

            Route::get('most-used-words', [AutoReplyController::class, 'mostUsedWords'])->name('chatbot.mostUsedWords');
            Route::get('most-used-phrases', [AutoReplyController::class, 'mostUsedPhrases'])->name('chatbot.mostUsedPhrases');
            Route::get('most-used-phrases/deleted', [AutoReplyController::class, 'mostUsedPhrasesDeleted'])->name('chatbot.mostUsedPhrasesDeleted');
            Route::get('most-used-phrases/deleted/records', [AutoReplyController::class, 'mostUsedPhrasesDeletedRecords'])->name('chatbot.mostUsedPhrasesDeletedRecords');

            Route::post('settings/update', [SettingController::class, 'update']);
            Route::get('settings/telescope', [SettingController::class, 'getTelescopeSettings']);
            Route::post('settings/telescope/update', [SettingController::class, 'updateTelescopeSettings']);
            Route::post('settings/updateAutomatedMessages', [SettingController::class, 'updateAutoMessages'])->name('settings.update.automessages');
            Route::resource('settings', SettingController::class);

            Route::get('conversion/rates', [ConversionRateController::class, 'index']);
            Route::post('conversion/rate/update', [ConversionRateController::class, 'update'])->name('conversion.rate.update');

            Route::get('category/child-categories', [CategoryController::class, 'childCategory'])->name('category.child-category');
            Route::get('category/edit-category', [CategoryController::class, 'childEditCategory'])->name('category.child-edit-category');
            Route::post('category/{edit}/edit-category', [CategoryController::class, 'updateCategory'])->name('category.child-update-category');
            Route::post('category/{edit}/update-days-cancelation', [CategoryController::class, 'updateCancelationPolicy'])->name('category.update-cancelation-policy');
            Route::post('category/get-days-cancelation-log', [CategoryController::class, 'getCategoryCancellationPolicyLog'])->name('category.get_cancelation_policy_log');
            Route::get('category/references/used-products', [CategoryController::class, 'usedProducts']);
            Route::post('category/references/update-reference', [CategoryController::class, 'updateReference']);
            Route::get('category/references', [CategoryController::class, 'mapCategory'])->name('category.map-category');
            Route::post('category/references', [CategoryController::class, 'saveReferences'])->name('category.save-references');
            Route::post('category/references/affected-product', [CategoryController::class, 'affectedProduct']);
            Route::post('category/references/affected-product-new', [CategoryController::class, 'affectedProductNew']);
            Route::post('category/references/update-category', [CategoryController::class, 'updateCategoryReference']);
            Route::post('category/references/update-multiple-category', [CategoryController::class, 'updateMultipleCategoryReference']);
            Route::post('category/update-field', [CategoryController::class, 'updateField']);
            Route::post('category/reference', [CategoryController::class, 'saveReference']);
            Route::post('category/save-form', [CategoryController::class, 'saveForm'])->name('category.save.form');
            Route::get('category/delete-unused', [CategoryController::class, 'deleteUnused'])->name('category.delete.unused');

            //new category reference
            Route::get('category/new-references', [CategoryController::class, 'newCategoryReferenceIndex']);
            Route::post('category/new-references/save-category', [CategoryController::class, 'saveCategoryReference'])->name('category.new-references.save-category');
            Route::get('category/fix-autosuggested', [CategoryController::class, 'fixAutoSuggested'])->name('category.fix-autosuggested');
            Route::get('category/fix-autosuggested-string', [CategoryController::class, 'fixAutoSuggestedString'])->name('category.fix-autosuggested-via-str');
            Route::get('category/{id}/history', [CategoryController::class, 'history']);
            Route::get('category/{id}/historyForScraper', [CategoryController::class, 'historyForScraper']);
            Route::post('category/change-push-type', [CategoryController::class, 'changePushType']);
            Route::get('sizes/references', [SizeController::class, 'sizeReference']);
            Route::get('sizes/{id}/used-products', [SizeController::class, 'usedProducts']);
            Route::POST('category/ScraperUserHistory', [CategoryController::class, 'ScraperUserHistory'])->name('ScraperUserHistory');

            //Group by new references category with filter % wise like
            Route::get('category/new-references-group', [CategoryController::class, 'newCategoryReferenceGroup']);
            Route::get('category/group/{name}/{threshold}', [CategoryController::class, 'newCategoryReferenceGroupBy']);

            Route::post('sizes/references/chamge', [SizeController::class, 'referenceAdd']);
            Route::get('sizes/affected-product', [SizeController::class, 'affectedProduct']);
            Route::post('sizes/update-sizes', [SizeController::class, 'updateSizes']);
            Route::get('sizes/new-references', [SizeController::class, 'newSizeReferences']);
            Route::post('sizes/new-references/update-size', [SizeController::class, 'updateNewSizeReferences']);
            Route::resource('category', CategoryController::class)->except('show');
            Route::resource('category-segment', CategorySegmentController::class);

            Route::get('resourceimg/searchimg', [ResourceImgController::class, 'searchResourceimg'])->name('resourceimg.searchimg');
            Route::resource('resourceimg', ResourceImgController::class);
            Route::get('resourceimg/pending/1', [ResourceImgController::class, 'pending']);
            Route::post('add-resource', [ResourceImgController::class, 'addResource'])->name('add.resource');
            Route::post('add-resourceCat', [ResourceImgController::class, 'addResourceCat'])->name('add.resourceCat');
            Route::post('edit-resourceCat', [ResourceImgController::class, 'editResourceCat'])->name('edit.resourceCat');
            Route::post('remove-resourceCat', [ResourceImgController::class, 'removeResourceCat'])->name('remove.resourceCat');
            Route::post('acitvate-resourceCat', [ResourceImgController::class, 'activateResourceCat'])->name('activate.resourceCat');

            Route::get('resourceimg/pending', [ResourceImgController::class, 'pending']);
            Route::post('resourceimg/status/create', [ResourceImgController::class, 'resourceStatusCreate'])->name('resourceimg.status.create');
            Route::post('resourceimg/statuscolor', [ResourceImgController::class, 'statuscolor'])->name('resourceimg.statuscolor');
            Route::post('resourceimg/resourceimg-update-status', [ResourceImgController::class, 'updateStatus'])->name('resourceimg-update-status');
            Route::get('resourceimg/status/histories/{id}', [ResourceImgController::class, 'resourceimgStatusHistories'])->name('resourceimg.status.histories');
            Route::post('resourceimg/remarks', [ResourceImgController::class, 'saveRemarks'])->name('resourceimg.saveremarks');
            Route::post('resourceimg/getremarks', [ResourceImgController::class, 'getRemarksHistories'])->name('resourceimg.getremarks');
            Route::post('resourceimg/getimages', [ResourceImgController::class, 'getResourcesImages'])->name('resourceimg.getimages');

            Route::post('delete-resource', [ResourceImgController::class, 'deleteResource'])->name('delete.resource');
            Route::get('images/resource/{id}', [ResourceImgController::class, 'imagesResource'])->name('images/resource');
            Route::post('show-images/resource', [ResourceImgController::class, 'showImagesResource'])->name('show-images/resource');

            Route::resource('benchmark', BenchmarkController::class);

            // adding lead routes
            Route::get('leads/imageGrid', [LeadsController::class, 'imageGrid'])->name('leads.image.grid');
            Route::post('leads/sendPrices', [LeadsController::class, 'sendPrices'])->name('leads.send.prices');
            Route::resource('leads', LeadsController::class);
            Route::post('leads/{id}/changestatus', [LeadsController::class, 'updateStatus']);
            Route::delete('leads/permanentDelete/{leads}', [LeadsController::class, 'permanentDelete'])->name('leads.permanentDelete');
            Route::resource('chat', ChatController::class);
            Route::get('erp-leads', [LeadsController::class, 'erpLeads'])->name('erp-leads.erpLeads');
            Route::post('erp-leads-column-visbility', [LeadsController::class, 'columnVisbilityUpdate'])->name('erp-leads.column.update');
            Route::post('erp-leads/statuscolor', [LeadsController::class, 'statuscolor'])->name('erp-leads.statuscolor');
            Route::post('erp-leads/enable-disable', [LeadsController::class, 'enableDisable'])->name('erp-leads.enable-disable');
            Route::post('erp-leads-send-message', [LeadsController::class, 'sendMessage'])->name('erp-leads-send-message');
            Route::get('erp-leads/response', [LeadsController::class, 'erpLeadsResponse'])->name('leads.erpLeadsResponse');
            Route::get('erp-leads/history', [LeadsController::class, 'erpLeadsHistory'])->name('leads.erpLeadsHistory');
            Route::post('erp-leads/{id}/changestatus', [LeadsController::class, 'updateErpStatus']);
            Route::get('erp-leads/edit', [LeadsController::class, 'erpLeadsEdit'])->name('leads.erpLeads.edit');
            Route::get('erp-leads/create', [LeadsController::class, 'erpLeadsCreate'])->name('leads.erpLeads.create');
            Route::get('erp-leads/status/create', [LeadsController::class, 'erpLeadsStatusCreate'])->name('erpLeads.status.create');
            Route::post('erp-leads/status/update', [LeadsController::class, 'erpLeadsStatusUpdate'])->name('erpLeads.status.update');
            Route::get('erp-leads/status/change', [LeadsController::class, 'erpLeadStatusChange'])->name('erpLeads.status.change');
            Route::post('erp-leads/store', [LeadsController::class, 'erpLeadsStore'])->name('leads.erpLeads.store');
            Route::get('erp-leads/delete', [LeadsController::class, 'erpLeadDelete'])->name('leads.erpLeads.delete');
            Route::get('erp-leads/customer-search', [LeadsController::class, 'customerSearch'])->name('leads.erpLeads.customerSearch');
            Route::post('erp-lead-block-customer', [LeadsController::class, 'blockcustomerlead'])->name('leads.block.customer');

            //Manage Brand Category
            Route::get('erp-manage/category', [LeadsController::class, 'manageLeadsCategory'])->name('manage.leads.category');
            Route::get('erp-manage/brand', [LeadsController::class, 'manageLeadsBrand'])->name('manage.leads.brand');
            Route::post('erp-manage/save/leads/brands', [LeadsController::class, 'saveLeadsBrands'])->name('save.leads.brands');
            Route::post('erp-manage/save/leads/categories', [LeadsController::class, 'saveLeadsCategories'])->name('save.leads.categories');

            //Cron
            Route::get('cron', [CronController::class, 'index'])->name('cron.index');
            Route::get('cron/run', [CronController::class, 'runCommand'])->name('cron.run.command');
            Route::get('cron/history/{id}', [CronController::class, 'history'])->name('cron.history');
            Route::post('cron/history/show', [CronController::class, 'historySearch'])->name('cron.history.search');
            Route::post('cron/gethistory/{id}', [CronController::class, 'getCronHistory']);

            Route::prefix('store-website')->middleware('auth')->group(static function () {
                Route::get('/status/all', [OrderController::class, 'viewAllStatuses'])->name('store-website.all.status');
                Route::get('/status/edit/{id}', [OrderController::class, 'viewEdit'])->name('store-website.status.edit');
                Route::post('/status/edit/{id}', [OrderController::class, 'editStatus'])->name('store-website.status.submitEdit');
                Route::get('/status/create', [OrderController::class, 'viewCreateStatus']);
                Route::post('/status/create', [OrderController::class, 'createStatus'])->name('store-website.submit.status');
                Route::get('/status/fetch', [OrderController::class, 'viewFetchStatus']);
                Route::post('/status/fetch', [OrderController::class, 'fetchStatus'])->name('store-website.fetch.status');
                Route::get('/status/fetchMasterStatus/{id}', [OrderController::class, 'fetchMasterStatus']);
                Route::get('/status/history', [OrderController::class, 'statusHistory']);
            });

            //plesk
            Route::prefix('plesk')->middleware('auth')->group(static function () {
                Route::get('/domains', [PleskController::class, 'index'])->name('plesk.domains');
                Route::get('/domains/mail/create/{id}', [PleskController::class, 'create'])->name('plesk.domains.view-mail-create');
                Route::post('/domains/mail/create/{id}', [PleskController::class, 'submitMail'])->name('plesk.domains.submit-mail');
                Route::post('/domains/mail/delete/{id}', [PleskController::class, 'deleteMail'])->name('plesk.domains.delete-mail');
                Route::get('/domains/mail/accounts/{id}', [PleskController::class, 'getMailAccounts'])->name('plesk.domains.mail-accounts');
                Route::post('/domains/mail/change-password', [PleskController::class, 'changePassword'])->name('plesk.domains.mail-accounts.change-password');
                Route::get('/domains/view/{id}', [PleskController::class, 'show'])->name('plesk.domains.view');
            });

            Route::prefix('virtualmin')->middleware('auth')->group(static function () {
                Route::get('/domains', [VirtualminDomainController::class, 'index'])->name('virtualmin.domains');
                Route::get('/domains/sync', [VirtualminDomainController::class, 'syncDomains'])->name('virtualmin.domains.sync');
                Route::post('/domains/update-dates', [VirtualminDomainController::class, 'updateDates'])->name('virtualmin.domains.update-dates');
                Route::get('/domains/{id}/enable', [VirtualminDomainController::class, 'enableDomain'])->name('virtualmin.domains.enable');
                Route::get('/domains/{id}/disable', [VirtualminDomainController::class, 'disableDomain'])->name('virtualmin.domains.disable');
                Route::get('/domains/{id}/delete', [VirtualminDomainController::class, 'deleteDomain'])->name('virtualmin.domains.delete');
                Route::get('/domains/histories', [VirtualminDomainController::class, 'domainShow'])->name('virtualmin.domains.history');
                Route::post('domains/create', [VirtualminDomainController::class, 'domainCreate'])->name('virtualmin.domains.create');
                Route::get('/domains/{id}/managecloud', [VirtualminDomainController::class, 'managecloudDomain'])->name('virtualmin.domains.managecloud');
                Route::post('domains/createadns', [VirtualminDomainController::class, 'adnsCreate'])->name('virtualmin.domains.createadns');
                Route::post('/domains/dnsdelete', [VirtualminDomainController::class, 'deletednsDomain'])->name('virtualmin.domains.dnsdelete');
                Route::get('/domains/dnshistories', [VirtualminDomainController::class, 'domainShowDns'])->name('virtualmin.domains.dnshistories');
                Route::get('/domains/dnsedit', [VirtualminDomainController::class, 'dnsedit'])->name('virtualmin.domains.dnsedit');
                Route::post('/domains/dnsupdate', [VirtualminDomainController::class, 'dnsupdate'])->name('virtualmin.domains.dnsupdate');
                Route::post('/domains/domainstatusupdate', [VirtualminDomainController::class, 'domainstatusupdate'])->name('virtualmin.domains.domainstatusupdate');
            });

            Route::prefix('sonarqube')->group(function () {
                Route::post('project/create', [SonarqubeController::class, 'createProject'])->name('sonarqube.createProject');
                Route::get('project/search', [SonarqubeController::class, 'searchProject'])->name('sonarqube.list.Project');
                Route::get('issues/search', [SonarqubeController::class, 'searchIssues'])->name('sonarqube.list.page');
                Route::get('user_tokens/search', [SonarqubeController::class, 'searchUserTokens'])->name('sonarqube.user.projects');
                Route::get('countdevtask/{id}', [SonarqubeController::class, 'taskCount']);
            });

            //plesk
            Route::prefix('content-management')->middleware('auth')->group(static function () {
                Route::get('/', [ContentManagementController::class, 'index'])->name('content-management.index');
                Route::get('/preview-img/{id}', [ContentManagementController::class, 'previewImage'])->name('content-management.preview-img');
                Route::get('/manage/show-history', [ContentManagementController::class, 'showHistory'])->name('content-management.manage.show-history');
                Route::get('/social/account/create', [ContentManagementController::class, 'viewAddSocialAccount'])->name('content-management.social.create');
                Route::post('/social/account/create', [ContentManagementController::class, 'addSocialAccount'])->name('content-management.social.submit');
                Route::get('/manage/{id}', [ContentManagementController::class, 'manageContent'])->name('content-management.manage');
                Route::post('/social/account/post', [ContentManagementController::class, 'postSocialAccount'])->name('content-management.social.post');
                Route::get('/social/account/pagepost', [ContentManagementController::class, 'pagePost'])->name('content-management.social.pagepost');

                Route::get('/manage/task-list/{id}', [ContentManagementController::class, 'getTaskList'])->name('content-management.manage.task-list');
                Route::get('/manage/preview-img/{id}', [ContentManagementController::class, 'previewCategoryImage'])->name('content-management.manage.preview-img');
                Route::get('/manage/milestone-task/{id}', [ContentManagementController::class, 'getTaskMilestones'])->name('content-management.manage.milestone-task');
                Route::post('/manage/save-category', [ContentManagementController::class, 'saveContentCategory'])->name('content-management.manage.save-category');
                Route::post('/manage/edit-category', [ContentManagementController::class, 'editCategory'])->name('content-management.category.edit');
                Route::post('/manage/save-content', [ContentManagementController::class, 'saveContent'])->name('content-management.manage.save-content');
                Route::post('/upload-documents', [ContentManagementController::class, 'uploadDocuments'])->name('content-management.upload-documents');
                Route::post('/save-documents', [ContentManagementController::class, 'saveDocuments'])->name('content-management.save-documents');
                Route::post('/delete-document', [ContentManagementController::class, 'deleteDocument'])->name('content-management.delete-documents');
                Route::post('/send-document', [ContentManagementController::class, 'sendDocument'])->name('content-management.send-documents');
                Route::post('/save-reviews', [ContentManagementController::class, 'saveReviews'])->name('content-management.save-reviews');
                Route::post('/manage/milestone-task/submit', [ContentManagementController::class, 'submitMilestones'])->name('content-management.submit-milestones');
                Route::post('/manage/attach/images', [ContentManagementController::class, 'getAttachImages'])->name('content-management.attach.images');
                Route::get('/download/attach/images', [ContentManagementController::class, 'downloadAttachImages'])->name('content-management.download.image');
                Route::prefix('{id}')->group(function () {
                    Route::get('list-documents', [ContentManagementController::class, 'listDocuments'])->name('content-management.list-documents');
                    Route::prefix('remarks')->group(function () {
                        Route::get('/', [ContentManagementController::class, 'remarks'])->name('content-management.remarks');
                        Route::post('/', [ContentManagementController::class, 'saveRemarks'])->name('content-management.saveRemarks');
                    });
                });
                Route::get('newsletter-email/store', [ContentManagementController::class, 'emailStore'])->name('content-management.emailStore');

                Route::prefix('contents')->group(function () {
                    Route::get('/', [ContentManagementController::class, 'viewAllContents'])->name('content-management.contents');
                });
            });

            //SEMrush Account Management
            Route::get('semrush/manage-semrush-account', [SemrushController::class, 'manageSemrushAccounts'])->name('semrush-manage-accounts');

            //SEMrush
            Route::prefix('semrush')->middleware('auth')->group(static function () {
                Route::get('/domain_report', [SemrushController::class, 'index'])->name('semrush.domain_report');
                Route::get('/keyword_report', [SemrushController::class, 'keyword_report'])->name('semrush.keyword_report');
                Route::get('/url_report', [SemrushController::class, 'url_report'])->name('semrush.url_report');
                Route::get('/backlink_reffring_report', [SemrushController::class, 'backlink_reffring_report'])->name('semrush.backlink_reffring_report');
                Route::get('/publisher_display_ad', [SemrushController::class, 'publisher_display_ad'])->name('semrush.publisher_display_ad');
                Route::get('/traffic_analitics_report', [SemrushController::class, 'traffic_analitics_report'])->name('semrush.traffic_analitics_report');
                Route::get('/competitor_analysis', [SemrushController::class, 'competitor_analysis'])->name('semrush.competitor_analysis');
            });

            Route::prefix('content-management-status')->group(function () {
                Route::get('/', [StoreSocialContentStatusController::class, 'index'])->name('content-management-status.index');
                Route::post('save', [StoreSocialContentStatusController::class, 'save'])->name('content-management-status.save');
                Route::post('statusEdit', [StoreSocialContentStatusController::class, 'statusEdit'])->name('content-management-status.edit-status');
                Route::post('store', [StoreSocialContentStatusController::class, 'store'])->name('content-management-status.store');
                Route::post('merge-status', [StoreSocialContentStatusController::class, 'mergeStatus'])->name('content-management-status.merge-status');
                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [StoreSocialContentStatusController::class, 'edit'])->name('content-management-status.edit');
                    Route::get('delete', [StoreSocialContentStatusController::class, 'delete'])->name('content-management-status.delete');
                });
            });

            Route::get('/hr-ticket/countdevtask/{id}/{user_id?}/{vendor_id?}', [UserManagementController::class, 'taskCount'])->name('hr-ticket.countdevtask');

            // Instruction
            Route::get('instruction/quick-instruction', [InstructionController::class, 'quickInstruction']);
            Route::post('instruction/store-instruction-end-time', [InstructionController::class, 'storeInstructionEndTime']);
            Route::get('instruction/list', [InstructionController::class, 'list'])->name('instruction.list');
            Route::resource('instruction', InstructionController::class);
            Route::post('instruction/complete', [InstructionController::class, 'complete'])->name('instruction.complete');
            Route::post('instruction/pending', [InstructionController::class, 'pending'])->name('instruction.pending');
            Route::post('instruction/verify', [InstructionController::class, 'verify'])->name('instruction.verify');
            Route::post('instruction/skipped-count', [InstructionController::class, 'skippedCount'])->name('instruction.skipped.count');
            Route::post('instruction/verifySelected', [InstructionController::class, 'verifySelected'])->name('instruction.verify.selected');
            Route::get('instruction/complete/alert', [InstructionController::class, 'completeAlert'])->name('instruction.complete.alert');
            Route::post('instruction/category/store', [InstructionController::class, 'categoryStore'])->name('instruction.category.store');

            Route::get('order/{id}/send/confirmationEmail', [OrderController::class, 'sendConfirmation'])->name('order.send.confirmation.email');
            Route::post('order/{id}/refund/answer', [OrderController::class, 'refundAnswer'])->name('order.refund.answer');
            Route::post('order/send/Delivery', [OrderController::class, 'sendDelivery'])->name('order.send.delivery');
            Route::post('order/deleteBulkOrders', [OrderController::class, 'deleteBulkOrders'])->name('order.deleteBulkOrders');
            Route::post('order/{id}/send/suggestion', [OrderController::class, 'sendSuggestion'])->name('order.send.suggestion');
            Route::post('order/{id}/changestatus', [OrderController::class, 'updateStatus']);
            Route::post('order/{id}/sendRefund', [OrderController::class, 'sendRefund']);
            Route::post('order/{id}/uploadForApproval', [OrderController::class, 'uploadForApproval'])->name('order.upload.approval');
            Route::post('order/{id}/deliveryApprove', [OrderController::class, 'deliveryApprove'])->name('order.delivery.approve');
            Route::get('order/{id}/printAdvanceReceipt', [OrderController::class, 'printAdvanceReceipt'])->name('order.advance.receipt.print');
            Route::get('order/{id}/emailAdvanceReceipt', [OrderController::class, 'emailAdvanceReceipt'])->name('order.advance.receipt.email');
            Route::get('order/{id}/generateInvoice', [OrderController::class, 'generateInvoice'])->name('order.generate.invoice');
            Route::get('order/{id}/send-invoice', [OrderController::class, 'sendInvoice'])->name('order.send.invoice');
            Route::get('order/{id}/send-order-email', [OrderController::class, 'sendOrderEmail'])->name('order.send.email');
            Route::get('order/{id}/preview-invoice', [OrderController::class, 'previewInvoice'])->name('order.perview.invoice');
            Route::post('order/{id}/createProductOnMagento', [OrderController::class, 'createProductOnMagento'])->name('order.create.magento.product');
            Route::get('order/{id}/download/PackageSlip', [OrderController::class, 'downloadPackageSlip'])->name('order.download.package-slip');
            Route::get('order/track/packageSlip', [OrderController::class, 'trackPackageSlip'])->name('order.track.package-slip');
            Route::delete('order/permanentDelete/{order}', [OrderController::class, 'permanentDelete'])->name('order.permanentDelete');
            Route::get('order/products/list', [OrderController::class, 'products'])->name('order.products');
            Route::get('order/missed-calls', [OrderController::class, 'missedCalls'])->name('order.missed-calls');
            Route::get('order/call-management', [OrderController::class, 'callManagement'])->name('order.call-management');
            Route::get('order/current-call-management', [OrderController::class, 'getCurrentCallInformation'])->name('order.current-call-management');
            Route::get('order/current-call-number', [OrderController::class, 'getCurrentCallNumber'])->name('order.current-call-number');
            Route::get('order/missed-calls/orders/{id}', [OrderController::class, 'getOrdersFromMissedCalls'])->name('order.getOrdersFromMissedCalls');
            Route::get('order/calls/history', [OrderController::class, 'callsHistory'])->name('order.calls-history');
            Route::post('order/calls/add-status', [OrderController::class, 'addStatus'])->name('order.store.add-status');
            Route::post('order/calls/store-status/{id}', [OrderController::class, 'storeStatus'])->name('order.store.store-status');
            Route::post('order/calls/send-message', [OrderController::class, 'sendWhatappMessageOrEmail'])->name('order.send-message.whatsapp-or-email');
            Route::post('order/update/customer', [OrderController::class, 'updateCustomer'])->name('order.update.customer');
            Route::post('order/generate/awb/number', [OrderController::class, 'generateAWB'])->name('order.generate.awb');
            Route::post('order/update/customer', [OrderController::class, 'updateCustomer'])->name('order.update.customer');
            Route::post('order/generate/awb/dhl', [OrderController::class, 'generateAWBDHL'])->name('order.generate.awbdhl');
            Route::get('order/generate/awb/rate-request', [OrderController::class, 'generateRateRequet'])->name('get.order.generate.rate-request');
            Route::post('order/generate/awb/rate-request', [OrderController::class, 'generateRateRequet'])->name('order.generate.rate-request');
            Route::get('orders/download', [OrderController::class, 'downloadOrderInPdf']);
            Route::get('order/email/download/{order_id?}/{email_id?}', [OrderController::class, 'downloadOrderMailPdf'])->name('order.generate.order-mail.pdf');
            Route::post('order/{id}/change-status-template', [OrderController::class, 'statusChangeTemplate']);
            Route::post('order/product/change-status-temp', [OrderController::class, 'prodctStatusChangeTemplate']);
            Route::post('order/change-status', [OrderController::class, 'statusChange']);
            Route::post('order/product/change-status', [OrderController::class, 'productItemStatusChange']);
            Route::post('order/order-product-status-change', [OrderController::class, 'orderProductStatusChange'])->name('order.order-product-status-change');
            Route::post('order/preview-sent-mails', [OrderController::class, 'orderPreviewSentMails']);
            Route::get('customer/getcustomerinfo', [CustomerController::class, 'customerinfo'])->name('customer.getcustomerinfo');

            Route::get('order/customer/list', [OrderController::class, 'customerList'])->name('order.customerList');
            Route::get('order/call/history/status', [OrderController::class, 'callhistoryStatusList'])->name('order.callhistoryStatusList');
            Route::get('order/store/website', [OrderController::class, 'storeWebsiteList'])->name('order.storeWebsiteList');

            Route::get('order/invoices', [OrderController::class, 'viewAllInvoices']);
            Route::get('order/invoices/saveLater', [OrderController::class, 'saveLaterCreate']);
            Route::get('order/invoices/saveLaterList', [OrderController::class, 'saveLaterList']);
            Route::get('order/invoices/ViewsaveLaterList/{id}', [OrderController::class, 'ViewsaveLaterList']);
            Route::post('order/create-product', [OrderController::class, 'createProduct'])->name('order.create.product');

            Route::get('order/{id}/edit-invoice', [OrderController::class, 'editInvoice'])->name('order.edit.invoice');
            Route::post('order/edit-invoice', [OrderController::class, 'submitEdit'])->name('order.submitEdit.invoice');
            Route::post('orders-column-visbility', [OrderController::class, 'ordersColumnVisbilityUpdate'])->name('orders.column.update');
            //TODO::invoice wthout order
            Route::get('invoice/without-order', [OrderController::class, 'createInvoiceWithoutOrderNumber'])->name('invoice.without.order');
            Route::get('order/order-search', [OrderController::class, 'searchOrderForInvoice'])->name('order.search.invoice');
            Route::get('customers/customer-search', [OrderController::class, 'getCustomers'])->name('customer.search');
            Route::get('customers/company-address-search', [OrderController::class, 'getCustomers'])->name('customer.address.search');
            Route::get('order/website-address-search', [OrderController::class, 'getCompany'])->name('company.address.search');
            Route::get('customers/product-search', [OrderController::class, 'getSearchedProducts'])->name('product.search');
            Route::get('order/{id}/add-invoice', [OrderController::class, 'addInvoice'])->name('order.add.invoice');
            Route::post('order/submit-invoice', [OrderController::class, 'submitInvoice'])->name('order.submit.invoice');

            //view
            Route::get('order/view-invoice/{id}', [OrderController::class, 'viewInvoice'])->name('order.view.invoice');
            Route::get('order/invoices/{id}/get-details', [OrderController::class, 'getInvoiceDetails'])->name('order.view.invoice.get.details');
            Route::post('order/invoices/{id}/update-details', [OrderController::class, 'updateDetails'])->name('order.view.invoice.update.details');

            Route::post('order/invoices/add-product', [OrderController::class, 'addProduct'])->name('order.view.invoice.add.product');
            Route::post('order/invoices/search-product', [OrderController::class, 'searchProduct'])->name('order.search.product');
            Route::get('order/download-invoice/{id}', [OrderController::class, 'downloadInvoice'])->name('order.download.invoice');
            Route::post('order/update-customer-address', [OrderController::class, 'updateCustomerInvoiceAddress'])->name('order.update.customer.address');
            Route::get('order/{id}/mail-invoice', [OrderController::class, 'mailInvoice'])->name('order.mail.invoice');
            Route::get('order/{id}/get-invoice-customer-email', [OrderController::class, 'getInvoiceCustomerEmail'])->name('get.invoice.customer.email');

            Route::get('order/get-invoice-customer-email-selected', [OrderController::class, 'getInvoiceCustomerEmailSelected']);
            Route::get('order/mail-invoice-multi-select', [OrderController::class, 'mailInvoiceMultiSelect']);
            Route::get('order/get-order-invoice-users', [OrderController::class, 'GetInvoiceOrderUsers']);

            Route::get('order/update-delivery-date', [OrderController::class, 'updateDelDate'])->name('order.updateDelDate');
            Route::get('order/view-est-delivery-date-history', [OrderController::class, 'viewEstDelDateHistory'])->name('order.viewEstDelDateHistory');
            Route::post('order/addNewReply', [OrderController::class, 'addNewReply'])->name('order.addNewReply');
            Route::post('order/orderChangeStatusHistory', [OrderController::class, 'orderChangeStatusHistory'])->name('order.orderChangeStatusHistory');
            Route::post('order/get-customer-address', [OrderController::class, 'getCustomerAddress'])->name('order.customer.address');
            Route::post('order/get-error-logs', [OrderController::class, 'getOrderErrorLog'])->name('order.error.log');
            Route::post('order/get-email-error-logs', [OrderController::class, 'getOrderExceptionErrorLog'])->name('order.get.email.error.logs');
            Route::post('order/get-email-send-logs', [OrderController::class, 'getOrderEmailSendLog'])->name('order.get.email.send.logs');
            Route::get('order/{id}/get-sms-send-logs', [OrderController::class, 'getOrderSmsSendLog'])->name('order.get.sms.send.logs');
            Route::get('order/get-email-send-journey-logs', [OrderController::class, 'getOrderEmailSendJourneyLog'])->name('order.get.email.send.journey.logs');
            Route::get('order/get-email-send-journey-step-logs', [OrderController::class, 'getOrderEmailSendJourneyStepLog'])->name('order.get.email.send.journey.step.logs');
            Route::get('order/get-order-status-journey', [OrderController::class, 'getOrderStatusJourney'])->name('order.get.order.status.journey');
            Route::get('order/get-order-journey', [OrderController::class, 'getOrderJourney'])->name('order.get.order.journey');
            Route::post('orders-journey-column-visbility', [OrderController::class, 'columnVisbilityUpdate'])->name('orders.journey.column.update');
            Route::post('orders-journey/getproducts', [OrderController::class, 'getOrderProductsList'])->name('orders.journey.products');
            Route::get('order/charity-order', [OrderController::class, 'charity_order']);
            Route::post('order/cancel-transaction', [OrderController::class, 'cancelTransaction'])->name('order.canceltransaction');
            Route::post('order/payload', [OrderController::class, 'getOrderPayloadList'])->name('order.payload');
            Route::post('order/change-return-status', [OrderController::class, 'returnStatus'])->name('order.change_return_status');
            Route::get('order/status/color-code', [OrderController::class, 'orderStatusColorCode'])->name('order.status.color');
            Route::post('order/status/Update', [OrderController::class, 'orderStatusColorCodeUpdate'])->name('order.status.color.Update');

            Route::resource('order', OrderController::class);

            Route::resource('status-mapping', StatusMappingController::class);

            Route::post('order/payment-history', [OrderController::class, 'paymentHistory'])->name('order.paymentHistory');
            Route::post('order/magento-log-list', [OrderController::class, 'getOrderMagentoErrorLogList'])->name('order.magento.log.list');
            Route::post('order/status/store', [OrderReportController::class, 'statusStore'])->name('status.store');
            Route::post('order/report/store', [OrderReportController::class, 'store'])->name('status.report.store');

            Route::get('order-refund-status-message', [OrderReportController::class, 'orderRefundStatusMessage'])->name('order.status.messages');
            Route::post('order/status/flag', [OrderReportController::class, 'setFlag'])->name('order.status.flag');

            //emails
            Route::post('email/reply-list-by-category', [EmailController::class, 'getReplyListByCategory'])->name('getReplyListByCategory');
            Route::post('email/reply-from-quick-reply', [EmailController::class, 'getReplyListFromQuickReply'])->name('getReplyListFromQuickReply');
            Route::get('email/replyMail/{id}', [EmailController::class, 'replyMail']);
            Route::post('email/replyMail', [EmailController::class, 'submitReply'])->name('email.submit-reply');
            Route::get('email/replyAllMail/{id}', [EmailController::class, 'replyAllMail'])->name('email.reply-all');
            Route::post('email/replyAllMails', [EmailController::class, 'submitReplyAll'])->name('email.submit-reply-all');
            Route::get('email/forwardMail/{id}', [EmailController::class, 'forwardMail']);
            Route::post('email/forwardMail', [EmailController::class, 'submitForward'])->name('email.submit-forward');
            Route::post('email/resendMail/{id}', [EmailController::class, 'resendMail']);
            Route::put('email/{id}/mark-as-read', [EmailController::class, 'markAsRead']);
            Route::post('email/{id}/excel-import', [EmailController::class, 'excelImporter']);
            Route::post('email/{id}/get-file-status', [EmailController::class, 'getFileStatus']);
            Route::resource('email', EmailController::class);
            Route::post('email/statuscolor', [EmailController::class, 'statuscolor'])->name('email.statuscolor');
            Route::post('email-column-visbility', [EmailController::class, 'emailsColumnVisbilityUpdate'])->name('email.column.update');
            Route::get('email/events/{originId}', [EmailController::class, 'getEmailEvents']);
            Route::get('sendgrid/email/events', [EmailController::class, 'getAllEmailEvents']);
            Route::get('sendgrid/email/events/journey', [EmailController::class, 'getAllEmailEventsJourney'])->name('email.event.journey');
            Route::post('sendgrid/email/events/color', [EmailController::class, 'eventColor'])->name('email.event.color');
            Route::get('email/emaillog/{emailId}', [EmailController::class, 'getEmailLogs']);
            Route::post('email/filter-options', [EmailController::class, 'getEmailFilterOptions']);
            Route::get('email/category/mappings', [EmailController::class, 'getCategoryMappings'])->name('email.category.mappings');

            Route::get('email/order_data/{email?}', [EmailController::class, 'index']); //Purpose : Add Route -  DEVTASK-18283
            Route::post('email/platform-update', [EmailController::class, 'platformUpdate'])->name('email.platform-update');

            Route::post('email/category', [EmailController::class, 'category']);
            Route::post('email/status', [EmailController::class, 'status']);
            Route::post('email/update_email', [EmailController::class, 'updateEmail']);
            Route::resource('mailbox', MailBoxController::class);
            Route::post('email/assign-modal', [EmailController::class, 'assignModel'])->name('assignModel');
            Route::post('email/update-model-color', [EmailController::class, 'updateModelColor'])->name('updateModelColor');
            Route::post('email/getModelNames', [EmailController::class, 'getModelNames'])->name('getModelNames');
            Route::post('email/get-category-log', [EmailController::class, 'getEmailCategoryChangeLogs'])->name('getEmailCategoryChangeLogs');
            Route::post('email/get-status-log', [EmailController::class, 'getEmailStatusChangeLogs'])->name('getEmailStatusChangeLogs');
            Route::post('bluckAction', [EmailController::class, 'bluckAction'])->name('bluckAction');
            Route::any('syncroniseEmail', [EmailController::class, 'syncroniseEmail'])->name('syncroniseEmail');
            Route::post('changeStatus', [EmailController::class, 'changeStatus'])->name('changeStatus');
            Route::post('change-email-category', [EmailController::class, 'changeEmailCategory'])->name('changeEmailCategory');
            Route::post('change-email-status', [EmailController::class, 'changeEmailStatus'])->name('changeEmailStatus');

            Route::get('email-remark', [EmailController::class, 'getRemark'])->name('email.getremark');
            Route::post('email-remark', [EmailController::class, 'addRemark'])->name('email.addRemark');
            Route::get('email/email-frame/{id}', [EmailController::class, 'viewEmailFrame']);
            Route::get('email/email-frame-info/{id}', [EmailController::class, 'viewEmailFrameInfo']);
            Route::get('technical/read', [EmailController::class, 'updateEmailRead'])->name('website.email.update');
            Route::get('quick/email/read', [EmailController::class, 'quickEmailList'])->name('quick.email.list');
            Route::get('email/email-replise/{id}', [EmailController::class, 'getEmailreplies']);

            // Zoom Meetings
            Route::post('meeting/update-personal-meeting', [Meeting\ZoomMeetingController::class, 'updatePersonalMeeting'])->name('meetings.update.personal');
            Route::post('meeting/create', [Meeting\ZoomMeetingController::class, 'createMeeting'])->name('zoom-meeting.create-meeting');
            Route::get('meeting/allmeetings', [Meeting\ZoomMeetingController::class, 'getMeetings']);
            Route::get('meetings/show-data', [Meeting\ZoomMeetingController::class, 'showData'])->name('meetings.show.data');
            Route::get('meetings/show', [Meeting\ZoomMeetingController::class, 'allMeetings'])->name('meetings.show');
            Route::get('meetings/all', [Meeting\ZoomMeetingController::class, 'allMeetings'])->name('meetings.all.data');
            Route::post('meeting/fetch-recordings', [Meeting\ZoomMeetingController::class, 'fetchRecordings'])->name('meeting.fetch.recordings');
            Route::post('meeting/fetch-participants', [Meeting\ZoomMeetingController::class, 'fetchParticipants'])->name('meeting.fetch.participants');
            Route::get('meeting/list/fetch-participants', [Meeting\ZoomMeetingController::class, 'listParticipants'])->name('meeting.list.participants');
            Route::get('meeting/list/participants', [Meeting\ZoomMeetingController::class, 'allParticipantsList'])->name('list.all.participants');
            Route::get('meeting/list/error-logs', [Meeting\ZoomMeetingController::class, 'listErrorLogs'])->name('meeting.list.error-logs');
            Route::get('meeting/list/recordings/{id}', [Meeting\ZoomMeetingController::class, 'listRecordings'])->name('meeting.list.recordings');
            Route::post('meeting/update-description', [Meeting\ZoomMeetingController::class, 'updateMeetingDescription'])->name('meeting.description.update');
            Route::get('meeting/download-recordings/{id}', [Meeting\ZoomMeetingController::class, 'downloadRecords'])->name('meeting.download.file');
            Route::post('meeting/download-recordings/permission', [Meeting\ZoomMeetingController::class, 'addUserPermission'])->name('meeting.add.user.permission');
            Route::get('recording-description/histories', [Meeting\ZoomMeetingController::class, 'listDescriptionHistory'])->name('recording.description.show');
            Route::post('meeting-description', [Meeting\ZoomMeetingController::class, 'storeMeetingDescription'])->name('meeting.store.description');
            Route::get('meeting-description/histories', [Meeting\ZoomMeetingController::class, 'meetingDescriptionHistory'])->name('meeting.description.show');
            Route::get('/videos/recoirding-show', [Meeting\ZoomMeetingController::class, 'showVideo'])->name('recording.video.show');
            Route::get('/videos/participant-recoirding-show', [Meeting\ZoomMeetingController::class, 'showParticipantVideo'])->name('participant-recording.video.show');
            Route::get('all/participant/lists', [Meeting\ZoomMeetingController::class, 'listAllParticipants'])->name('list.all-participants');

            Route::post('meeting/update-participant-description', [Meeting\ZoomMeetingController::class, 'updateParticipantDescription'])->name('participant.description.update');
            Route::get('participant-description/histories', [Meeting\ZoomMeetingController::class, 'participantDescriptionHistory'])->name('participant.description.show');

            Route::prefix('task')->group(function () {
                Route::prefix('information')->group(function () {
                    Route::get('get', [TaskModuleController::class, 'taskGet'])->name('task.information.get');
                });

                Route::prefix('update')->group(function () {
                    Route::post('start-date', [TaskModuleController::class, 'taskUpdateStartDate'])->name('task.update.start-date');
                    Route::post('due-date', [TaskModuleController::class, 'taskUpdateDueDate'])->name('task.update.due-date');
                    Route::post('cost', [TaskModuleController::class, 'updateCost'])->name('task.update.cost');
                    Route::post('approximate', [TaskModuleController::class, 'updateApproximate'])->name('task.update.approximate');
                });

                Route::prefix('history')->group(function () {
                    Route::get('start-date/index', [TaskHistoryController::class, 'historyStartDate'])->name('task.history.start-date.index');
                    Route::get('due-date/index', [TaskHistoryController::class, 'historyDueDate'])->name('task.history.due-date.index');
                    Route::get('cost/index', [TaskHistoryController::class, 'historyCost'])->name('task.history.cost.index');
                    Route::get('approximate/index', [TaskHistoryController::class, 'historyApproximate'])->name('task.history.approximate.index');

                    Route::post('approve', [TaskHistoryController::class, 'approve'])->name('task.history.approve');
                    Route::get('approve/history', [TaskHistoryController::class, 'approveHistory'])->name('task.history.approve-history');
                });

                Route::get('dropdown-user-wise', [TaskModuleController::class, 'dropdownUserWise'])->name('task.dropdown-user-wise');
                Route::get('dropdown-slot-wise', [TaskModuleController::class, 'dropdownSlotWise'])->name('task.dropdown-slot-wise');
                Route::prefix('slot')->group(function () {
                    Route::post('assign', [TaskModuleController::class, 'slotAssign'])->name('task.slot.assign');
                    Route::post('move', [TaskModuleController::class, 'slotMove'])->name('task.slot.move');
                });

                Route::get('task-modules', [TaskModuleController::class, 'indexModules'])->name('task.task-modules');
            });

            Route::post('task/reminder', [TaskModuleController::class, 'updateTaskReminder'])->name('task.reminder.update');
            Route::post('task/statuscolor', [TaskModuleController::class, 'statuscolor'])->name('task.statuscolor');
            Route::get('task/time/history', [TaskModuleController::class, 'getTimeHistory'])->name('task.time.history');
            Route::get('task/categories', [TaskModuleController::class, 'getTaskCategories'])->name('task.categories');
            Route::get('task/list', [TaskModuleController::class, 'list'])->name('task.list');
            Route::get('tasks/devgettaskremark', [TaskModuleController::class, 'devgetTaskRemark'])->name('task.devgettaskremark');
            Route::get('task/get-discussion-subjects', [TaskModuleController::class, 'getDiscussionSubjects'])->name('task.discussion-subjects');
            Route::post('task/flag', [TaskModuleController::class, 'flag'])->name('task.flag');
            Route::post('remark/flag', [TaskModuleController::class, 'remarkFlag'])->name('remark.flag');
            Route::post('task/{id}/plan', [TaskModuleController::class, 'plan'])->name('task.plan');
            Route::post('task/assign/messages', [TaskModuleController::class, 'assignMessages'])->name('task.assign.messages');
            Route::post('task/loadView', [TaskModuleController::class, 'loadView'])->name('task.load.view');
            Route::post('task/bulk-complete', [TaskModuleController::class, 'completeBulkTasks'])->name('task.bulk.complete');
            Route::post('task/bulk-delete', [TaskModuleController::class, 'deleteBulkTasks'])->name('task.bulk.delete');
            Route::post('task/send-document', [TaskModuleController::class, 'sendDocument'])->name('task.send-document');
            Route::post('task/message/reminder', [TaskModuleController::class, 'messageReminder'])->name('task.message.reminder');
            Route::post('task/{id}/convertTask', [TaskModuleController::class, 'convertTask'])->name('task.convert.appointment');
            Route::post('task/{id}/updateSubject', [TaskModuleController::class, 'updateSubject'])->name('task.update.subject');
            Route::post('task/{id}/addNote', [TaskModuleController::class, 'addNote'])->name('task.add.note');
            Route::post('task/{id}/addSubnote', [TaskModuleController::class, 'addSubnote'])->name('task.add.subnote');
            Route::post('task/{id}/updateCategory', [TaskModuleController::class, 'updateCategory'])->name('task.update.category');
            Route::post('task/list-by-user-id', [TaskModuleController::class, 'taskListByUserId'])->name('task.list.by.user.id');
            Route::post('task/set-priority', [TaskModuleController::class, 'setTaskPriority'])->name('task.set.priority');
            Route::get('/task/assign/master-user', [TaskModuleController::class, 'assignMasterUser'])->name('task.asign.master-user');
            Route::get('task/CommunicationTaskStatus', [TaskModuleController::class, 'CommunicationTaskStatus'])->name('task.CommunicationTaskStatus'); // Purpose : Create Route for Assign Task To User - DEVTASK-21234
            Route::get('task/AssignTaskToUser', [TaskModuleController::class, 'AssignTaskToUser'])->name('task.AssignTaskToUser'); // Purpose : Create Route for Assign Task To User - DEVTASK-21234
            Route::post('task/AssignMultipleTaskToUser', [TaskModuleController::class, 'AssignMultipleTaskToUser'])->name('task.AssignMultipleTaskToUser');
            Route::post('/task/upload-documents', [TaskModuleController::class, 'uploadDocuments'])->name('task.upload-documents');
            Route::post('/task/save-documents', [TaskModuleController::class, 'saveDocuments'])->name('task.save-documents');
            Route::get('/task/preview-img/{id}', [TaskModuleController::class, 'previewTaskImage'])->name('task.preview-img');
            Route::get('/task/complete/{taskid}', [TaskModuleController::class, 'complete'])->name('task.complete');
            Route::get('/task/start/{taskid}', [TaskModuleController::class, 'start'])->name('task.start');
            Route::get('/statutory-task/complete/{taskid}', [TaskModuleController::class, 'statutoryComplete'])->name('task.statutory.complete');
            Route::post('/task/addremark', [TaskModuleController::class, 'addRemark'])->name('task.addRemark');
            Route::get('tasks/getremark', [TaskModuleController::class, 'getremark'])->name('task.getremark');
            Route::get('tasks/gettaskremark', [TaskModuleController::class, 'getTaskRemark'])->name('task.gettaskremark');
            Route::post('task/{id}/makePrivate', [TaskModuleController::class, 'makePrivate']);
            Route::post('task/{id}/isWatched', [TaskModuleController::class, 'isWatched']);
            Route::post('task-remark/{id}/delete', [TaskModuleController::class, 'archiveTaskRemark'])->name('task.archive.remark');
            Route::post('tasks/deleteTask', [TaskModuleController::class, 'deleteTask']);
            Route::post('tasks/send-brodcast', [TaskModuleController::class, 'sendBrodCast'])->name('task.send-brodcast');
            Route::post('tasks/{id}/delete', [TaskModuleController::class, 'archiveTask'])->name('task.archive');
            Route::post('task/deleteStatutoryTask', [TaskModuleController::class, 'deleteStatutoryTask']);

            Route::post('/task/send', [TaskModuleController::class, 'SendTask'])->name('task.send/user');
            Route::post('/task/send-sop', [TaskModuleController::class, 'SendTaskSOP'])->name('task.send/Sop');

            Route::get('task/export', [TaskModuleController::class, 'exportTask'])->name('task.export');
            Route::post('task/addRemarkStatutory', [TaskModuleController::class, 'addRemark'])->name('task.addRemarkStatutory');

            Route::get('task/search/', [TaskModuleController::class, 'searchTask'])->name('task.module.search');
            Route::get('task/{id}', [TaskModuleController::class, 'show'])->name('task.module.show');

            Route::resource('task', TaskModuleController::class);
            Route::post('task-column-visbility', [TaskModuleController::class, 'taskColumnVisbilityUpdate'])->name('task.column.update');

            Route::post('task/time/history/approve/sendMessage', [TaskModuleController::class, 'sendReviseMessage'])->name('task.time.history.approve.sendMessage');
            Route::post('task/time/history/approve/sendRemindMessage', [TaskModuleController::class, 'sendRemindMessage'])->name('task.time.history.approve.sendRemindMessage');
            Route::get('/get-site-development-task', [TaskModuleController::class, 'getSiteDevelopmentTask'])->name('get.site.development.task');

            Route::post('task/create-get-remark', [TaskModuleController::class, 'taskCreateGetRemark'])->name('task.create.get.remark');
            Route::post('task/update/priority-no', [TaskModuleController::class, 'updatePriorityNo'])->name('task.update.updatePriorityNo');
            Route::post('task/time/history/approve', [TaskModuleController::class, 'approveTimeHistory'])->name('task.time.history.approve');
            Route::post('task/time/history/start', [TaskModuleController::class, 'startTimeHistory'])->name('task.time.history.start');
            Route::get('task/time/tracked/history', [TaskModuleController::class, 'getTrackedHistory'])->name('task.time.tracked.history');
            Route::post('task/create/hubstaff_task', [TaskModuleController::class, 'createHubstaffManualTask'])->name('task.create.hubstaff_task');
            Route::get('task/timer/history', [TaskModuleController::class, 'getTimeHistoryStartEnd'])->name('task.timer.history');
            Route::get('task/update/milestone', [TaskModuleController::class, 'saveMilestone'])->name('task.update.milestone');
            Route::get('task/get/details', [TaskModuleController::class, 'getDetails'])->name('task.json.details');
            Route::post('task/get/save-notes', [TaskModuleController::class, 'saveNotes'])->name('task.json.saveNotes');
            Route::post('task_category/{id}/approve', [TaskCategoryController::class, 'approve']);
            Route::post('task_category/change-status', [TaskCategoryController::class, 'changeStatus']);
            Route::resource('task_category', TaskCategoryController::class);
            Route::post('task/addWhatsAppGroup', [TaskModuleController::class, 'addWhatsAppGroup'])->name('task.add.whatsapp.group');
            Route::post('task/addGroupParticipant', [TaskModuleController::class, 'addGroupParticipant'])->name('task.add.whatsapp.participant');
            Route::post('task/create-task-from-shortcut', [TaskModuleController::class, 'createTaskFromSortcut'])->name('task.create.task.shortcut');
            Route::post('task/create-multiple-task-from-shortcut', [TaskModuleController::class, 'createMultipleTaskFromSortcut'])->name('task.create.multiple.task.shortcut');
            Route::post('task/create-multiple-task-from-shortcutpostman', [TaskModuleController::class, 'createMultipleTaskFromSortcutPostman'])->name('task.create.multiple.task.shortcutpostman');
            Route::post('task/create-multiple-task-from-shortcutuserschedules', [TaskModuleController::class, 'createMultipleTaskFromSortcutUserSchedules'])->name('task.create.multiple.task.shortcutuserschedules');
            Route::post('task/create-multiple-task-from-shortscriptdocument', [TaskModuleController::class, 'createMultipleTaskFromScriptDocument'])->name('task.create.multiple.task.shortscriptdocument');
            Route::post('task/create-multiple-task-from-shortcutsentry', [TaskModuleController::class, 'createMultipleTaskFromSortcutSentry'])->name('task.create.multiple.task.shortcutsentry');
            Route::post('task/create-multiple-task-from-shortcutmagentoproblems', [TaskModuleController::class, 'createMultipleTaskFromSortcutMagentoProblems'])->name('task.create.multiple.task.shortcutmagentoproblems');
            Route::post('task/create-multiple-task-from-shortcutdevoops', [TaskModuleController::class, 'createMultipleTaskFromSortcutDevOops'])->name('task.create.multiple.task.shortcutdevoops');
            Route::post('task/create-multiple-task-from-shortcutwebsitelogs', [TaskModuleController::class, 'createMultipleTaskFromSortcutWebsiteLogs'])->name('task.create.multiple.task.shortcutwebsitelogs');
            Route::post('task/get/websitelist', [TaskModuleController::class, 'getWebsiteList'])->name('get.task.websitelist');
            Route::get('task/user/history', [TaskModuleController::class, 'getUserHistory'])->name('task/user/history');
            Route::post('task/recurring-history', [TaskModuleController::class, 'recurringHistory'])->name('task.recurringHistory');
            Route::post('task/create-multiple-task-from-shortcut-bugtrack', [TaskModuleController::class, 'createMultipleTaskFromSortcutBugtrack'])->name('task.create.multiple.task.shortcut.bugtrack');
            Route::post('task/upload-file', [TaskModuleController::class, 'uploadFile'])->name('task.upload-file');
            Route::get('task/files/record', [TaskModuleController::class, 'getUploadedFilesList'])->name('task.files.record');
            Route::get('task/module/history/{id}', [TaskModuleController::class, 'taskModuleListLogHistory'])->name('task.log.histories.show');
            Route::get('task/deletedevtask', [TaskModuleController::class, 'deletedevtask'])->name('task.delete.task');
            Route::get('task/preview-img-task/{id}', [TaskModuleController::class, 'previewTaskImage'])->name('task.preview.img.task');
            Route::post('task/send-sop', [TaskModuleController::class, 'SendTaskSOP'])->name('task.sendSop');
            Route::post('task/create-multiple-task-from-shortcutsonar', [TaskModuleController::class, 'createMultipleTaskFromSortcutSonar'])->name('task.create.multiple.task.shortcutsonar');

            Route::resource('learning', LearningModuleController::class);
            Route::get('learning/status/history', [LearningModuleController::class, 'getStatusHistory'])->name('learning/status/history');
            Route::post('learning/due_date-change', [LearningModuleController::class, 'saveDueDateUpdate'])->name('learning-due-change');
            Route::get('learning/duedate/history', [LearningModuleController::class, 'getDueDateHistory'])->name('learning/duedate/history');
            Route::resource('learning_category', LearningCategoryController::class);
            Route::post('learning_category/submodule', [LearningCategoryController::class, 'getSubModule']);
            Route::post('learning/create-learning-from-shortcut', [LearningModuleController::class, 'createLearningFromSortcut']);
            Route::post('learning-module/update', [LearningModuleController::class, 'learningModuleUpdate'])->name('learning-module.update');
            Route::post('/learning/save-documents', [LearningModuleController::class, 'saveDocuments'])->name('learning.save-documents');
            Route::get('learning/{id}', [LearningModuleController::class, 'show'])->name('learning.module.show');

            Route::get('/', [MasterControlController::class, 'index'])->name('home');
            Route::get('/master-dev-task', [MasterDevTaskController::class, 'index'])->name('master.dev.task');

            Route::get('project-file-manager/list', [ProjectFileManagerController::class, 'listTree'])->name('project-file-manager.list');
            Route::post('project-file-manager/get-latest-size', [ProjectFileManagerController::class, 'getLatestSize'])->name('project-file-manager.get-latest-size');
            Route::post('project-file-manager/delete-file', [ProjectFileManagerController::class, 'deleteFile'])->name('project-file-manager.delete-file');
            Route::get('project-file-manager', [ProjectFileManagerController::class, 'index'])->name('project-file-manager.index');
            Route::post('project-file-manager/insertsize', [ProjectFileManagerController::class, 'insertsize'])->name('project-file-manager.insertsize');
            Route::post('project-file-manager/update', [ProjectFileManagerController::class, 'update'])->name('project-file-manager.update');
            Route::get('size/log_history/discount/', [ProjectFileManagerController::class, 'sizelogHistory'])->name('size/log-history/discount');
            Route::get('file-name/file-size', [ProjectFileManagerController::class, 'getfilenameandsize'])->name('file-name/file-size.get');

            // Daily Planner
            Route::post('dailyplanner/complete', [DailyPlannerController::class, 'complete'])->name('dailyplanner.complete');
            Route::post('dailyplanner/reschedule', [DailyPlannerController::class, 'reschedule'])->name('dailyplanner.reschedule');
            Route::post('dailyplanner/history', [DailyPlannerController::class, 'history'])->name('dailyplanner.history');
            Route::post('dailyplanner/send/schedule', [DailyPlannerController::class, 'sendSchedule'])->name('dailyplanner.send.vendor.schedule');
            Route::post('dailyplanner/resend-notification', [DailyPlannerController::class, 'resendNotification'])->name('dailyplanner.resend.notification');
            // Route::resource('dailyplanner', DailyPlannerController::class);
            Route::post('dailyplanner', [DailyPlannerController::class, 'index'])->name('dailyplanner.index');

            Route::resource('refund', RefundController::class);

            // Contacts
            Route::resource('contact', ContactController::class);

            Route::get('/notifications', [NotificaitonContoller::class, 'index'])->name('notifications');
            Route::get('/notificaitonsJson', [NotificaitonContoller::class, 'json'])->name('notificationJson');
            Route::get('/salesNotificaitonsJson', [NotificaitonContoller::class, 'salesJson'])->name('salesNotificationJson');
            Route::post('/notificationMarkRead/{notificaion}', [NotificaitonContoller::class, 'markRead'])->name('notificationMarkRead');
            Route::get('/deQueueNotfication', [NotificationQueueController::class, 'deQueueNotficationNew']);

            Route::post('/productsupervisor/approve/{product}', [ProductSupervisorController::class, 'approve'])->name('productsupervisor.approve');
            Route::post('/productsupervisor/reject/{product}', [ProductSupervisorController::class, 'reject'])->name('productsupervisor.reject');
            Route::post('/productlister/isUploaded/{product}', [ProductListerController::class, 'isUploaded'])->name('productlister.isuploaded');
            Route::post('/productapprover/isFinal/{product}', [ProductApproverController::class, 'isFinal'])->name('productapprover.isfinal');

            Route::get('/productinventory/in/stock', [ProductInventoryController::class, 'instock'])->name('productinventory.instock');
            Route::post('/productinventory/in/stock/update-field', [ProductInventoryController::class, 'updateField'])->name('productinventory.instock.update-field');
            Route::get('/productinventory/in/delivered', [ProductInventoryController::class, 'inDelivered'])->name('productinventory.indelivered');
            Route::get('/productinventory/in/stock/instruction-create', [ProductInventoryController::class, 'instructionCreate'])->name('productinventory.instruction.create');
            Route::post('/productinventory/in/stock/instruction', [ProductInventoryController::class, 'instruction'])->name('productinventory.instruction');
            Route::get('/productinventory/in/stock/location-history', [ProductInventoryController::class, 'locationHistory'])->name('productinventory.location.history');
            Route::post('/productinventory/in/stock/dispatch-store', [ProductInventoryController::class, 'dispatchStore'])->name('productinventory.dispatch.store');
            Route::get('/productinventory/in/stock/dispatch', [ProductInventoryController::class, 'dispatchCreate'])->name('productinventory.dispatch.create');
            Route::post('/productinventory/stock/{product}', [ProductInventoryController::class, 'stock'])->name('productinventory.stock');
            Route::get('productinventory/in/stock/location/change', [ProductInventoryController::class, 'locationChange'])->name('productinventory.location.change');

            Route::post('discount/file/update', [ProductInventoryController::class, 'updategenericprice'])->name('discount.file.update');
            Route::post('retailfromdisc/file/update', [ProductInventoryController::class, 'conditionprice'])->name('condition.file.update');
            Route::post('retailfromexceptionsdisc/file/update', [ProductInventoryController::class, 'exceptionsprice'])->name('condition-exceptions.file.update');

            Route::prefix('google-search-image')->group(function () {
                Route::get('/', [GoogleSearchImageController::class, 'index'])->name('google.search.image');
                Route::post('/crop', [GoogleSearchImageController::class, 'crop'])->name('google.search.crop');
                Route::post('/crop-search', [GoogleSearchImageController::class, 'searchImageOnGoogle'])->name('google.search.crop.post');
                Route::post('details', [GoogleSearchImageController::class, 'details'])->name('google.search.details');
                Route::post('queue', [GoogleSearchImageController::class, 'queue'])->name('google.search.queue');
                Route::post('/multiple-products', [GoogleSearchImageController::class, 'getImageForMultipleProduct'])->name('google.product.queue');
                Route::post('/image-crop-sequence', [GoogleSearchImageController::class, 'cropImageSequence'])->name('google.crop.sequence');
                Route::post('/update-product-status', [GoogleSearchImageController::class, 'updateProductStatus'])->name('google.product.status');
                Route::post('product-by-image', [GoogleSearchImageController::class, 'getProductFromImage'])->name('google.product.image');
            });
            Route::get('/product-search-image', [GoogleSearchImageController::class, 'searchImageList'])->name('google.search.product.image');

            Route::prefix('search-image')->group(function () {
                Route::get('/', [GoogleSearchImageController::class, 'product'])->name('google.search.product');
                Route::post('/', [GoogleSearchImageController::class, 'product'])->name('google.search.product-save');
            });

            Route::prefix('multiple-search-image')->group(function () {
                Route::get('/', [GoogleSearchImageController::class, 'nultipeImageProduct'])->name('google.search.multiple');
                Route::post('/save-images', [GoogleSearchImageController::class, 'multipleImageStore'])->name('multiple.google.search.product-save');
                Route::post('/single-save-images', [GoogleSearchImageController::class, 'getProductFromText'])->name('multiple.google.product-save');
            });

            Route::prefix('approve-search-image')->group(function () {
                Route::get('/', [GoogleSearchImageController::class, 'approveProduct'])->name('google.approve.product');
                Route::post('/approve-images-product', [GoogleSearchImageController::class, 'approveTextGoogleImagesToProduct'])->name('approve.google.search.images.product');
                Route::post('/reject', [GoogleSearchImageController::class, 'rejectProducts'])->name('reject.google.search.text.product');
            });

            Route::get('category', [CategoryController::class, 'manageCategory'])->name('category');
            Route::get('category-log', [CategoryController::class, 'logCategory'])->name('category.log');
            Route::get('/push-category-in-live', [CategoryController::class, 'pushCategoryInLive']);
            Route::get('category-11', [CategoryController::class, 'manageCategory11'])->name('category-11');
            Route::post('add-category', [CategoryController::class, 'addCategory'])->name('add.category');
            Route::post('category/{category}/edit', [CategoryController::class, 'edit'])->name('update.category');
            Route::post('category/remove', [CategoryController::class, 'remove'])->name('category.remove');
            Route::post('category/storeCopyCategory', [CategoryController::class, 'storeCopyCategory'])->name('category.storeCopy');

            Route::get('productSearch/', [SaleController::class, 'searchProduct']);
            Route::post('productSearch/', [SaleController::class, 'searchProduct']);

            Route::get('user-search/', [UserController::class, 'searchUser']);
            Route::post('user-search/', [UserController::class, 'searchUser'])->name('user-search');

            Route::get('activity/', [ActivityConroller::class, 'showActivity'])->name('activity');
            Route::post('activity/modal', [ActivityConroller::class, 'recentActivities']);
            Route::get('graph/', [ActivityConroller::class, 'showGraph'])->name('graph');
            Route::get('graph/user', [ActivityConroller::class, 'showUserGraph'])->name('graph_user');

            Route::get('search/', [SearchController::class, 'search'])->name('search');
            Route::get('pending/{roletype}', [SearchController::class, 'getPendingProducts'])->name('pending');

            Route::get('loadEnvManager/', [EnvController::class, 'loadEnvManager'])->name('load_env_manager');

            //Attach Products
            Route::get('attachProducts/{model_type}/{model_id}/{type?}/{customer_id?}', [ProductController::class, 'attachProducts'])->name('attachProducts');
            Route::post('attachProductToModel/{model_type}/{model_id}/{product_id}', [ProductController::class, 'attachProductToModel'])->name('attachProductToModel');
            Route::post('deleteOrderProduct/{order_product}', [OrderController::class, 'deleteOrderProduct'])->name('deleteOrderProduct');
            Route::get('attachImages/{model_type}/{model_id?}/{status?}/{assigned_user?}', [ProductController::class, 'attachImages'])->name('attachImages');
            Route::post('selected_customer/sendMessage', [ProductController::class, 'sendMessageSelectedCustomer'])->name('whatsapp.send_selected_customer');
            Route::post('selected_customer/assignGroup', [ProductController::class, 'assignGroupSelectedCustomer'])->name('twilio.assign_group_selected_customer');
            Route::post('selected_customer/createGroup', [ProductController::class, 'createGroupSelectedCustomer'])->name('twilio.create_group_selected_customer');

            // landing page
            Route::prefix('landing-page')->group(function () {
                Route::get('/', [LandingPageController::class, 'index'])->name('landing-page.index');
                Route::post('/save', [LandingPageController::class, 'save'])->name('landing-page.save');
                Route::get('/records', [LandingPageController::class, 'records'])->name('landing-page.records');
                Route::post('/store', [LandingPageController::class, 'store'])->name('landing-page.store');
                Route::post('/update-time', [LandingPageController::class, 'updateTime'])->name('landing-page.updateTime');
                Route::get('/image/{id}/{productId}/delete', [LandingPageController::class, 'deleteImage'])->name('landing-page.deleteImage');
                Route::post('create_status', [LandingPageController::class, 'createStatus'])->name('landing-page-create-status.store');
                Route::get('/approve-status', [LandingPageController::class, 'approveStatus'])->name('landing-page.approveStatus');
                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [LandingPageController::class, 'edit'])->name('landing-page.edit');
                    Route::get('delete', [LandingPageController::class, 'delete'])->name('landing-page.delete');
                    Route::get('push-to-shopify', [LandingPageController::class, 'pushToShopify'])->name('landing-page.push-to-shopify');
                    Route::get('change-store', [LandingPageController::class, 'changeStore'])->name('landing-page.change.store');
                    Route::get('push-to-magento', [LandingPageController::class, 'pushToMagentoPro'])->name('landing-page.push-to-magento');
                    Route::get('push-to-magento-status', [LandingPageController::class, 'updateMagentoStock'])->name('landing-page.push-to-magento-status');
                });
            });

            Route::prefix('newsletters')->group(function () {
                Route::get('/', [NewsletterController::class, 'index'])->name('newsletters.index');
                Route::post('/save', [NewsletterController::class, 'save'])->name('newsletters.save');
                Route::get('/records', [NewsletterController::class, 'records'])->name('newsletters.records');
                Route::post('/store', [NewsletterController::class, 'store'])->name('newsletters.store');
                Route::get('/image/{id}/{productId}/delete', [NewsletterController::class, 'deleteImage'])->name('newsletters.deleteImage');
                Route::get('/review-translate/{language?}', [NewsletterController::class, 'reviewTranslate'])->name('newsletters.review.translate');
                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [NewsletterController::class, 'edit'])->name('newsletters.edit');
                    Route::get('delete', [NewsletterController::class, 'delete'])->name('newsletters.delete');
                    Route::get('change-store', [NewsletterController::class, 'changeStore'])->name('newsletters.change.store');
                    Route::get('preview', [NewsletterController::class, 'preview'])->name('newsletters.preview');
                    Route::post('translate', [NewsletterController::class, 'translate'])->name('newsletters.translate');
                    Route::get('get-translated-text-score', [NewsletterController::class, 'getTranslatedTextScore'])->name('newsletters.get-score');
                });
                Route::post('/get-multi-translated-text-score', [NewsletterController::class, 'getMultiTranslatedTextScore'])->name('newsletters.get-multi-score');
            });

            Route::prefix('size')->group(function () {
                Route::get('/', [SizeController::class, 'index'])->name('size.index');
                Route::post('/save', [SizeController::class, 'save'])->name('size.save');
                Route::get('/records', [SizeController::class, 'records'])->name('size.records');
                Route::post('/store', [SizeController::class, 'store'])->name('size.store');
                Route::post('push-to-store', [SizeController::class, 'pushToStore'])->name('size.push.to.store');
                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [SizeController::class, 'edit'])->name('size.edit');
                    Route::get('delete', [SizeController::class, 'delete'])->name('size.delete');
                });
            });

            Route::post('download', [MessageController::class, 'downloadImages'])->name('download.images');

            Route::get('quickSell', [QuickSellController::class, 'index'])->name('quicksell.index');
            Route::post('quickSell', [QuickSellController::class, 'store'])->name('quicksell.store');
            Route::post('quickSell/edit', [QuickSellController::class, 'update'])->name('quicksell.update');
            Route::post('quickSell/saveGroup', [QuickSellController::class, 'saveGroup'])->name('quicksell.save.group');
            Route::get('quickSell/pending', [QuickSellController::class, 'pending'])->name('quicksell.pending');
            Route::post('quickSell/activate', [QuickSellController::class, 'activate'])->name('quicksell.activate');
            Route::get('quickSell/search', [QuickSellController::class, 'search'])->name('quicksell.search');
            Route::post('quickSell/groupUpdate', [QuickSellController::class, 'groupUpdate'])->name('quicksell.group.update');
            Route::get('quickSell/quick-sell-group-list', [QuickSellController::class, 'quickSellGroupProductsList']);
            Route::post('quickSell/quicksell-product-delete', [QuickSellController::class, 'quickSellGroupProductDelete']);

            // Chat messages
            Route::get('chat-messages/{object}/{object_id}/loadMoreMessages', [ChatMessagesController::class, 'loadMoreMessages']);
            Route::post('chat-messages/{id}/set-reviewed', [ChatMessagesController::class, 'setReviewed']);
            Route::post('chat-messages/downloadChatMessages', [ChatMessagesController::class, 'downloadChatMessages'])->name('chat.downloadChatMessages');
            Route::get('chat-messages/dnd-list', [ChatMessagesController::class, 'dndList'])->name('chat.dndList');
            Route::get('chat-messages/dnd-list/records', [ChatMessagesController::class, 'dndListRecords'])->name('chat.dndList.records');
            Route::post('chat-messages/dnd-list/move-dnd', [ChatMessagesController::class, 'moveDnd'])->name('chat.dndList.moveDnd');
            // Customers
            Route::get('customer/credit', [CustomerController::class, 'storeCredit']);
            Route::get('customer/credit/logs/{id}', [LiveChatController::class, 'customerCreditLogs']);
            Route::get('customer/credit-repush/{id}', [LiveChatController::class, 'creditRepush']);
            Route::get('customer/priority-points', [CustomerController::class, 'customerPriorityPoints'])->name('customer.priority.points');
            Route::get('customer/add-priority-points', [CustomerController::class, 'addCustomerPriorityPoints'])->name('customer.add.priority.points');
            Route::get('customer/get-priority-points/{id?}', [CustomerController::class, 'getCustomerPriorityPoints'])->name('customer.get.priority.points');
            Route::post('customer/websites', [CustomerController::class, 'getWebsiteCustomers']);

            Route::get('customer/priority-range-points/', [CustomerController::class, 'getCustomerPriorityRangePoints'])->name('customer.get.priority.range.points');
            Route::get('customer/priority-all-range-points/{id?}', [CustomerController::class, 'selectCustomerPriorityRangePoints'])->name('customer.all.select.priority.range.points');
            Route::get('customer/priority-range-points/{id?}', [CustomerController::class, 'getSelectCustomerPriorityRangePoints'])->name('customer.get.select.priority.range.points');
            Route::get('customer/add-priority-range-points', [CustomerController::class, 'addCustomerPriorityRangePoints'])->name('customer.add.priority.range.points');
            Route::get('customer/delete-priority-range-points/{id?}', [CustomerController::class, 'deleteCustomerPriorityRangePoints'])->name('customer.delete.priority.range.points');

            Route::get('customer/exportCommunication/{id}', [CustomerController::class, 'exportCommunication'])->name('customer.export-communication');
            Route::get('customer/test', [CustomerController::class, 'customerstest']);
            Route::post('customer/reminder', [CustomerController::class, 'updateReminder']);
            Route::post('supplier/reminder', [SupplierController::class, 'updateReminder']);
            Route::post('supplier/excel-import', [SupplierController::class, 'excelImport']);
            Route::post('vendors/reminder', [VendorController::class, 'updateReminder']);
            Route::post('customer/add-note/{id}', [CustomerController::class, 'addNote']);
            Route::post('supplier/add-note/{id}', [SupplierController::class, 'addNote']);
            Route::get('customers/{id}/post-show', [CustomerController::class, 'postShow'])->name('get.customer.post.show');
            Route::post('customers/{id}/post-show', [CustomerController::class, 'postShow'])->name('customer.post.show');
            Route::post('customers/{id}/sendAdvanceLink', [CustomerController::class, 'sendAdvanceLink'])->name('customer.send.advanceLink');
            Route::get('customers/{id}/loadMoreMessages', [CustomerController::class, 'loadMoreMessages']);
            Route::get('customer/search', [CustomerController::class, 'search']);
            Route::get('customers', [CustomerController::class, 'index'])->name('customer.index');
            Route::post('add-reply-category', [CustomerController::class, 'addReplyCategory'])->name('add.reply.category');
            Route::post('destroy-reply-category', [CustomerController::class, 'destroyReplyCategory'])->name('destroy.reply.category');
            Route::get('customers-load', [CustomerController::class, 'load'])->name('customer.load');
            Route::post('customer/{id}/initiateFollowup', [CustomerController::class, 'initiateFollowup'])->name('customer.initiate.followup');
            Route::post('customer/{id}/stopFollowup', [CustomerController::class, 'stopFollowup'])->name('customer.stop.followup');
            Route::get('customer/export', [CustomerController::class, 'export'])->name('customer.export');
            Route::post('customer/merge', [CustomerController::class, 'merge'])->name('customer.merge');
            Route::post('customer/import', [CustomerController::class, 'import'])->name('customer.import');
            Route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create');
            Route::post('customer/block', [CustomerController::class, 'block'])->name('customer.block');
            Route::post('customer/flag', [CustomerController::class, 'flag'])->name('customer.flag');
            Route::post('customer/in-w-list', [CustomerController::class, 'addInWhatsappList'])->name('customer.in-w-list');
            Route::post('customer/prioritize', [CustomerController::class, 'prioritize'])->name('customer.priority');
            Route::post('customer/create', [CustomerController::class, 'store'])->name('customer.store');
            Route::get('customer/broadcast', [CustomerController::class, 'broadcast'])->name('customer.broadcast.list');
            Route::get('customer/broadcast-details', [CustomerController::class, 'broadcastDetails'])->name('customer.broadcast.details');
            Route::get('customer/broadcast-send-price', [CustomerController::class, 'broadcastSendPrice'])->name('customer.broadcast.run');
            Route::get('customer/contact-download/{id}', [CustomerController::class, 'downloadContactDetailsPdf'])->name('customer.download.contact-pdf');
            Route::get('customer/{id}', [CustomerController::class, 'show'])->name('customer.show');
            Route::get('customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
            Route::post('customer/{id}/edit', [CustomerController::class, 'update'])->name('customer.update');
            Route::post('customer/{id}/updateNumber', [CustomerController::class, 'updateNumber'])->name('customer.update.number');
            Route::post('customer/{id}/updateDND', [CustomerController::class, 'updateDnd'])->name('customer.update.dnd');
            Route::post('customer/{id}/updatePhone', [CustomerController::class, 'updatePhone'])->name('customer.update.phone');
            Route::delete('customer/{id}/destroy', [CustomerController::class, 'destroy'])->name('customer.destroy');
            Route::post('customer/send/message/all/{validate?}', [WhatsAppController::class, 'sendToAll'])->name('customer.whatsapp.send.all');
            Route::get('customer/stop/message/all', [WhatsAppController::class, 'stopAll'])->name('customer.whatsapp.stop.all');
            Route::get('customer/email/fetch', [CustomerController::class, 'emailFetch'])->name('customer.email.fetch');
            Route::get('customer/email/inbox', [CustomerController::class, 'emailInbox'])->name('customer.email.inbox');
            Route::post('customer/email/send', [CustomerController::class, 'emailSend'])->name('customer.email.send');
            Route::post('customer/send/suggestion', [CustomerController::class, 'sendSuggestion'])->name('customer.send.suggestion');
            Route::post('customer/send/instock', [CustomerController::class, 'sendInstock'])->name('customer.send.instock');
            Route::post('customer/issue/credit', [CustomerController::class, 'issueCredit'])->name('customer.issue.credit');
            Route::post('customer/attach/all', [CustomerController::class, 'attachAll'])->name('customer.attach.all');
            Route::post('customer/sendScraped/images', [CustomerController::class, 'sendScraped'])->name('customer.send.scraped');
            Route::post('customer/change-whatsapp-no', [CustomerController::class, 'changeWhatsappNo'])->name('customer.change.whatsapp');
            Route::post('customer/update-field', [CustomerController::class, 'updateField'])->name('customer.update.field');
            Route::post('customer/send-contact-details', [CustomerController::class, 'sendContactDetails'])->name('customer.send.contact');
            Route::post('customer/contact-download-donload', [CustomerController::class, 'downloadContactDetails'])->name('customer.download.contact');
            Route::post('customer/create-kyc', [CustomerController::class, 'createKyc'])->name('customer.create.kyc');

            Route::get('quickcustomer', [CustomerController::class, 'quickcustomer'])->name('quickcustomer');
            Route::get('quick-customer', [QuickCustomerController::class, 'index'])->name('quick.customer.index');
            Route::get('quick-customer/records', [QuickCustomerController::class, 'records'])->name('quick.customer.records');
            Route::post('quick-customer/add-whatsapp-list', [QuickCustomerController::class, 'addInWhatsappList'])->name('quick.customer.add-whatsapp-list');

            Route::get('broadcast', [BroadcastMessageController::class, 'index'])->name('broadcast.index');
            Route::get('broadcast/images', [BroadcastMessageController::class, 'images'])->name('broadcast.images');
            Route::post('broadcast/imagesUpload', [BroadcastMessageController::class, 'imagesUpload'])->name('broadcast.images.upload');
            Route::post('broadcast/imagesLink', [BroadcastMessageController::class, 'imagesLink'])->name('broadcast.images.link');
            Route::delete('broadcast/{id}/imagesDelete', [BroadcastMessageController::class, 'imagesDelete'])->name('broadcast.images.delete');
            Route::get('broadcast/calendar', [BroadcastMessageController::class, 'calendar'])->name('broadcast.calendar');
            Route::post('broadcast/restart', [BroadcastMessageController::class, 'restart'])->name('broadcast.restart');
            Route::post('broadcast/restart/{id}', [BroadcastMessageController::class, 'restartGroup'])->name('broadcast.restart.group');
            Route::post('broadcast/delete/{id}', [BroadcastMessageController::class, 'deleteGroup'])->name('broadcast.delete.group');
            Route::post('broadcast/stop/{id}', [BroadcastMessageController::class, 'stopGroup'])->name('broadcast.stop.group');
            Route::post('broadcast/{id}/doNotDisturb', [BroadcastMessageController::class, 'doNotDisturb'])->name('broadcast.donot.disturb');

            Route::get('purchases', [PurchaseController::class, 'index'])->name('purchase.index');
            Route::get('purchase/calendar', [PurchaseController::class, 'calendar'])->name('purchase.calendar');
            Route::post('purchase/{id}/updateDelivery', [PurchaseController::class, 'updateDelivery']);
            Route::post('purchase/{id}/assignBatch', [PurchaseController::class, 'assignBatch'])->name('purchase.assign.batch');
            Route::post('purchase/{id}/assignSplitBatch', [PurchaseController::class, 'assignSplitBatch'])->name('purchase.assign.split.batch');
            Route::post('purchase/export', [PurchaseController::class, 'export'])->name('purchase.export');
            Route::post('purchase/merge', [PurchaseController::class, 'merge'])->name('purchase.merge');
            Route::post('purchase/sendExport', [PurchaseController::class, 'sendExport'])->name('purchase.send.export');
            Route::get('purchase/{id}', [PurchaseController::class, 'show'])->name('purchase.show');
            Route::get('purchase/{id}/edit', [PurchaseController::class, 'edit'])->name('purchase.edit');
            Route::post('purchase/{id}/changestatus', [PurchaseController::class, 'updateStatus']);
            Route::post('purchase/{id}/changeProductStatus', [PurchaseController::class, 'updateProductStatus']);
            Route::post('purchase/{id}/saveBill', [PurchaseController::class, 'saveBill']);
            Route::post('purchase/{id}/downloadFile', [PurchaseController::class, 'downloadFile'])->name('purchase.file.download');
            Route::post('purchase/{id}/confirmProforma', [PurchaseController::class, 'confirmProforma'])->name('purchase.confirm.Proforma');
            Route::get('purchase/download/attachments', [PurchaseController::class, 'downloadAttachments'])->name('purchase.download.attachments');
            Route::delete('purchase/{id}/delete', [PurchaseController::class, 'destroy'])->name('purchase.destroy');
            Route::delete('purchase/{id}/permanentDelete', [PurchaseController::class, 'permanentDelete'])->name('purchase.permanentDelete');
            Route::get('purchaseGrid/{page?}', [PurchaseController::class, 'purchaseGrid'])->name('purchase.grid');
            Route::post('purchaseGrid', [PurchaseController::class, 'store'])->name('purchase.store');
            Route::post('purchase/product/replace', [PurchaseController::class, 'productReplace'])->name('purchase.product.replace');
            Route::post('purchase/product/create/replace', [PurchaseController::class, 'productCreateReplace'])->name('purchase.product.create.replace');
            Route::get('purchase/product/{id}', [PurchaseController::class, 'productShow'])->name('purchase.product.show');
            Route::post('purchase/product/{id}', [PurchaseController::class, 'updatePercentage'])->name('purchase.product.percentage');
            Route::post('purchase/product/{id}/remove', [PurchaseController::class, 'productRemove'])->name('purchase.product.remove');
            Route::get('purchase/email/inbox', [PurchaseController::class, 'emailInbox'])->name('purchase.email.inbox');
            Route::get('purchase/email/fetch', [PurchaseController::class, 'emailFetch'])->name('purchase.email.fetch');
            Route::post('purchase/email/send', [PurchaseController::class, 'emailSend'])->name('purchase.email.send');
            Route::post('purchase/email/resend', [PurchaseController::class, 'emailResend'])->name('purchase.email.resend');
            Route::post('purchase/email/reply', [PurchaseController::class, 'emailReply'])->name('purchase.email.reply');
            Route::get('pc/test', [PictureColorsController::class, 'index']);
            Route::post('purchase/email/forward', [PurchaseController::class, 'emailForward'])->name('purchase.email.forward');
            Route::get('download/crop-rejected/{id}/{type}', [ProductCropperController::class, 'downloadImagesForProducts']);

            Route::post('purchase/sendmsgsupplier', [PurchaseController::class, 'sendmsgsupplier'])->name('purchase.sendmsgsupplier');
            Route::get('get-supplier-msg', [PurchaseController::class, 'getMsgSupplier'])->name('get.msg.supplier');
            Route::post('purchase/send/emailBulk', [PurchaseController::class, 'sendEmailBulk'])->name('purchase.email.send.bulk');
            Route::resource('purchase-status', PurchaseStatusController::class);

            // Master Plan
            Route::get('mastercontrol/clearAlert', [MasterControlController::class, 'clearAlert'])->name('mastercontrol.clear.alert');
            Route::resource('mastercontrol', MasterControlController::class);

            Route::get('purchase-product/getexcel', [PurchaseProductController::class, 'getexcel'])->name('purchase-product.getexcel'); //Purpose : Set route for Excel - DEVTASK-4236
            Route::get('purchase-product/getallproducts', [PurchaseProductController::class, 'getallproducts'])->name('purchase-product.getallproducts'); //Purpose : Set route for Excel - DEVTASK-4236
            Route::post('purchase-product/send_Products_Data', [PurchaseProductController::class, 'send_Products_Data'])->name('purchase-product.send_Products_Data'); //Purpose : Set route for Excel - DEVTASK-4236
            Route::get('purchase-product/download_excel_file', [PurchaseProductController::class, 'download_excel_file'])->name('purchase-product.download_excel_file'); //Purpose : Set route for Excel - DEVTASK-4236
            Route::post('purchase-product/set_template', [PurchaseProductController::class, 'set_template'])->name('purchase-product.set_template'); //Purpose : Set route for Template - DEVTASK-4236
            Route::get('purchase-product/get_template', [PurchaseProductController::class, 'get_template'])->name('purchase-product.get_template'); //Purpose : Set route for Template - DEVTASK-4236
            Route::post('purchase-product/edit_excel_file', [PurchaseProductController::class, 'edit_excel_file'])->name('purchase-product.edit_excel_file'); //Purpose : Set route for Excel - DEVTASK-4236
            Route::get('purchase-product/openfile/{excel_id?}/{version?}', [PurchaseProductController::class, 'open_excel_file'])->name('purchase-product.openfile');
            Route::post('purchase-product/update_excel_sheet', [PurchaseProductController::class, 'update_excel_sheet'])->name('purchase-product.update_excel_sheet');
            Route::get('purchase-product/get_excel_data_supplier_wise', [PurchaseProductController::class, 'get_excel_data_supplier_wise'])->name('purchase-product.get_excel_data_supplier_wise');
            Route::post('purchase-product/send_excel_file', [PurchaseProductController::class, 'send_excel_file'])->name('purchase-product.send_excel_file');

            Route::get('purchase-product/not_mapping_product_supplier_list', [PurchaseProductController::class, 'not_mapping_product_supplier_list'])->name('not_mapping_product_supplier_list'); //Purpose : Get not mapping supplier - DEVTASK-19941

            Route::post('purchase-product/change-status/{id}', [PurchaseProductController::class, 'changeStatus']);
            Route::post('purchase-product/change-main-status/{id}', [PurchaseProductController::class, 'changeMainStatus']);
            Route::post('purchase-product/getstatus', [PurchaseProductController::class, 'getStatusHistories'])->name('purchase-product.getstatus');
            Route::post('purchase-product/submit-status', [PurchaseProductController::class, 'createStatus']);
            Route::get('purchase-product/send-products/{type}/{supplier_id}', [PurchaseProductController::class, 'sendProducts']);
            Route::get('purchase-product/get-products/{type}/{supplier_id}', [PurchaseProductController::class, 'getProducts']);
            Route::get('purchase-product/get-suppliers', [PurchaseProductController::class, 'getSuppliers']);
            Route::post('purchase-product/saveDefaultSupplier', [PurchaseProductController::class, 'saveDefaultSupplier']);
            Route::post('purchase-product/saveFixedPrice', [PurchaseProductController::class, 'saveFixedPrice']);
            Route::post('purchase-product/saveDiscount', [PurchaseProductController::class, 'saveDiscount']);
            Route::get('purchase-product/supplier-details/{order_id}', [PurchaseProductController::class, 'getSupplierDetails']);
            Route::get('purchase-product/lead-supplier-details/{lead_id}', [PurchaseProductController::class, 'leadSupplierDetails']);

            Route::get('purchase-product/customer-details/{type}/{order_id}', [PurchaseProductController::class, 'getCustomerDetails']);
            Route::post('purchase-product/statuscolor', [PurchaseProductController::class, 'statuscolor'])->name('purchase-product.statuscolor');
            Route::post('purchase-product-column-visbility', [PurchaseProductController::class, 'ppColumnVisbilityUpdate'])->name('purchase-product.column.update');
            Route::resource('purchase-product', PurchaseProductController::class);

            Route::post('purchase-product/insert_suppliers_product', [PurchaseProductController::class, 'insert_suppliers_product'])->name('purchase-product.insert_suppliers_product');

            Route::get('purchaseproductorders/list', [PurchaseProductController::class, 'purchaseproductorders'])->name('purchaseproductorders.list'); //Purpose : Add Route for Purchase Product Order - DEVTASK-4236
            Route::post('purchaseproductorders/update', [PurchaseProductController::class, 'purchaseproductorders_update'])->name('purchaseproductorders.update'); //Purpose : Add Route for Purchase Product Order - DEVTASK-4236
            Route::post('purchaseproductorders/purchase-status-change', [PurchaseProductController::class, 'purchaseStatusChange'])->name('purchaseproductorders.purchase-status-change'); //Purpose : Add Route - DEVTASK-23362
            Route::get('purchaseproductorders/logs', [PurchaseProductController::class, 'purchaseproductorders_logs'])->name('purchaseproductorders.logs'); //Purpose : Add Route for Purchase Product Order - DEVTASK-4236
            Route::get('purchaseproductorders/flows', [PurchaseProductController::class, 'purchaseproductorders_flows'])->name('purchaseproductorders.flows'); //Purpose : Add Route for Purchase Product Order - DEVTASK-4236
            Route::get('purchaseproductorders/orderdata', [PurchaseProductController::class, 'purchaseproductorders_orderdata'])->name('purchaseproductorders.orderdata'); //Purpose : Add Route for Purchase Product Order - DEVTASK-4236
            Route::post('purchaseproductorders/saveuploads', [PurchaseProductController::class, 'purchaseproductorders_saveuploads'])->name('purchaseproductorders.saveuploads'); //Purpose : Add Route for Purchase Product Order - DEVTASK-4236
            Route::post('purchaseproductorders-column-visbility', [PurchaseProductController::class, 'purchaseproductordersColumnVisbilityUpdate'])->name('purchaseproductorders.column.update');
            Route::post('purchaseproductorders/statuscolor', [PurchaseProductController::class, 'statuscolorpp'])->name('purchaseproductorders.statuscolor');

            // Cash Vouchers
            Route::get('/voucher/payment/request', [VoucherController::class, 'paymentRequest'])->name('voucher.payment.request');
            Route::post('/voucher/payment/request', [VoucherController::class, 'createPaymentRequest'])->name('voucher.payment.request-submit');
            Route::get('/voucher/payment/{id}', [VoucherController::class, 'viewPaymentModal'])->name('voucher.payment');
            Route::post('/voucher/payment/{id}', [VoucherController::class, 'submitPayment'])->name('voucher.payment.submit');
            Route::post('voucher/{id}/approve', [VoucherController::class, 'approve'])->name('voucher.approve');
            Route::post('voucher/store/category', [VoucherController::class, 'storeCategory'])->name('voucher.store.category');
            Route::post('voucher/{id}/reject', [VoucherController::class, 'reject'])->name('voucher.reject');
            Route::post('voucher/{id}/resubmit', [VoucherController::class, 'resubmit'])->name('voucher.resubmit');
            Route::get('/voucher/manual-payment', [VoucherController::class, 'viewManualPaymentModal'])->name('voucher.payment.manual-payment');
            Route::post('/voucher/manual-payment', [VoucherController::class, 'manualPaymentSubmit'])->name('voucher.payment.manual-payment-submit');
            Route::post('/voucher/paid-selected-payment', [VoucherController::class, 'paidSelected'])->name('voucher.payment.paid-selected');
            Route::get('/voucher/paid-selected-payment-list', [VoucherController::class, 'paidSelectedPaymentList'])->name('voucher.payment.paid-selected-payment-list');
            Route::post('/voucher/pay-multiple', [VoucherController::class, 'payMultiple'])->name('voucher.payment.pay-multiple');

            Route::resource('voucher', VoucherController::class);
            Route::post('voucher/payment-history', [VoucherController::class, 'paymentHistory'])->name('voucher.paymentHistory');
            Route::post('/upload-documents', [VoucherController::class, 'uploadDocuments'])->name('voucher.upload-documents');
            Route::post('/voucher/save-documents', [VoucherController::class, 'saveDocuments'])->name('voucher.save-documents');
            Route::get('/voucher/{id}/list-documents', [VoucherController::class, 'listDocuments'])->name('voucher.list-documents');
            Route::post('/voucher/delete-document', [VoucherController::class, 'deleteDocument'])->name('voucher.delete-documents');

            // Budget
            Route::resource('budget', BudgetController::class);
            Route::post('budget/category/store', [BudgetController::class, 'categoryStore'])->name('budget.category.store');
            Route::post('budget/subcategory/store', [BudgetController::class, 'subCategoryStore'])->name('budget.subcategory.store');

            //Comments
            Route::post('doComment', [CommentController::class, 'store'])->name('doComment');
            Route::post('deleteComment/{comment}', [CommentController::class, 'destroy'])->name('deleteComment');
            Route::get('message/updatestatus', [MessageController::class, 'updatestatus'])->name('message.updatestatus');
            Route::get('message/loadmore', [MessageController::class, 'loadmore'])->name('message.loadmore');

            //Push Notifications new
            Route::get('/new-notifications', [PushNotificationController::class, 'index'])->name('pushNotification.index');
            Route::get('/pushNotifications', [PushNotificationController::class, 'getJson'])->name('pushNotifications');
            Route::post('/pushNotificationMarkRead/{push_notification}', [PushNotificationController::class, 'markRead'])->name('pushNotificationMarkRead');
            Route::post('/pushNotificationMarkReadReminder/{push_notification}', [PushNotificationController::class, 'markReadReminder'])->name('pushNotificationMarkReadReminder');
            Route::post('/pushNotification/status/{push_notification}', [PushNotificationController::class, 'changeStatus'])->name('pushNotificationStatus');

            Route::post('dailyActivity/store', [DailyActivityController::class, 'store'])->name('dailyActivity.store');
            Route::post('dailyActivity/quickStore', [DailyActivityController::class, 'quickStore'])->name('dailyActivity.quick.store');
            Route::get('dailyActivity/complete/{id}', [DailyActivityController::class, 'complete']);
            Route::get('dailyActivity/start/{id}', [DailyActivityController::class, 'start']);
            Route::get('dailyActivity/get', [DailyActivityController::class, 'get'])->name('dailyActivity.get');

            Route::get('/get/feedback-table/data', [UserManagementController::class, 'addFeedbackTableData'])->name('user.get-feedback-table-data');
            Route::get('/get/feedback-table/datavendor', [UserManagementController::class, 'addFeedbackTableDataVendor'])->name('user.get-feedback-table-datavendor');
            Route::post('feedback/remarks', [UserManagementController::class, 'saveRemarks'])->name('feedback.saveremarks');
            Route::post('feedback/getremarks', [UserManagementController::class, 'getRemarksHistories'])->name('feedback.getremarks');
            Route::post('feedback/statuscolor', [UserManagementController::class, 'statuscolor'])->name('feedback.statuscolor');
            Route::post('feedback/status/create', [UserManagementController::class, 'statusCreate'])->name('feedback.status.create');
            Route::get('/save/user-category/status', [UserManagementController::class, 'statusHistory'])->name('user.save.status');
            Route::get('/get/user-category/status', [UserManagementController::class, 'getStatusHistory'])->name('user.get.status.data');
            Route::get('/save/user-category/sop', [UserManagementController::class, 'sopHistory'])->name('user.save.sop');
            Route::get('/get/user-category/sop', [UserManagementController::class, 'getSopHistory'])->name('user.get.sop.data');
            Route::get('/save/user-category/sop-comment', [UserManagementController::class, 'sopHistoryComment'])->name('user.save.sop.comment');
            Route::get('/get/user-category/sop-comment', [UserManagementController::class, 'getSopCommentHistory'])->name('user.get.sop-comment.data');

            // Complete the task
            // Route::get('/task/count/{taskid}', 'TaskModuleController@taskCount')->name('task.count');
            Route::get('delete/task/note', [TaskModuleController::class, 'deleteTaskNote'])->name('delete/task/note');
            Route::get('hide/task/remark', [TaskModuleController::class, 'hideTaskRemark'])->name('hide/task/remark');

            // Social Media Image Module
            Route::get('lifestyle/images/grid', [ImageController::class, 'index'])->name('image.grid');
            Route::get('lifestyle/images/grid-new', [ImageController::class, 'indexNew'])->name('image.grid.new');
            Route::post('images/grid', [ImageController::class, 'store'])->name('image.grid.store');
            Route::post('images/grid/attachImage', [ImageController::class, 'attachImage'])->name('image.grid.attach');
            Route::get('images/grid/approvedImages', [ImageController::class, 'approved'])->name('image.grid.approved');
            Route::get('images/grid/finalApproval', [ImageController::class, 'final'])->name('image.grid.final.approval');
            Route::get('images/grid/{id}', [ImageController::class, 'show'])->name('image.grid.show');
            Route::get('images/grid/{id}/edit', [ImageController::class, 'edit'])->name('image.grid.edit');
            Route::post('images/grid/{id}/edit', [ImageController::class, 'update'])->name('image.grid.update');
            Route::delete('images/grid/{id}/delete', [ImageController::class, 'destroy'])->name('image.grid.delete');
            Route::post('images/grid/{id}/approveImage', [ImageController::class, 'approveImage'])->name('image.grid.approveImage');
            Route::get('images/grid/{id}/download', [ImageController::class, 'download'])->name('image.grid.download');
            Route::post('images/grid/make/set', [ImageController::class, 'set'])->name('image.grid.set');
            Route::post('images/grid/make/set/download', [ImageController::class, 'setDownload'])->name('image.grid.set.download');
            Route::post('images/grid/update/schedule', [ImageController::class, 'updateSchedule'])->name('image.grid.update.schedule');
            Route::post('images/searchQueue', [ImageController::class, 'imageQueue'])->name('image.queue');

            Route::post('leads/save-leave-message', [LeadsController::class, 'saveLeaveMessage'])->name('leads.message.save');

            Route::get('imported/leads', [ColdLeadsController::class, 'showImportedColdLeads']);
            Route::get('imported/leads/save', [ColdLeadsController::class, 'addLeadToCustomer']);

            // Development
            Route::post('development/task/move-to-progress', [DevelopmentController::class, 'moveTaskToProgress']);
            Route::post('development/task/complete-task', [DevelopmentController::class, 'completeTask']);
            Route::post('development/task/assign-task', [DevelopmentController::class, 'updateAssignee']);
            Route::post('development/task/relist-task', [DevelopmentController::class, 'relistTask']);
            Route::post('development/task/update-status', [DevelopmentController::class, 'changeTaskStatus']);
            Route::post('development/task/upload-document', [DevelopmentController::class, 'uploadDocument'])->name('development.task.upload-document');
            Route::post('development/task/bulk-delete', [DevelopmentController::class, 'deleteBulkTasks']);
            Route::get('development/task/get-document', [DevelopmentController::class, 'getDocument']);
            Route::get('development/task/export-excel', [DevelopmentController::class, 'exportExcel'])->name('development.task.exportExcel');
            Route::get('development/task/export-task', [DevelopmentController::class, 'exportTask'])->name('development.task.exportTask');
            Route::get('development/task/all-tasks', [DevelopmentController::class, 'viewAllTasks'])->name('development.task.viewAllTasks');
            Route::get('development/task/search/', [DevelopmentController::class, 'searchDevTask'])->name('devtask.module.search');
            Route::post('development/add/scrapper', [DevelopmentController::class, 'addScrapper'])->name('development.add-scrapper');
            Route::get('development/task/{id}/scrapper-logs', [DevelopmentController::class, 'getScrapperLogsByTaskId'])->name('development.get-scrapper-logs-by-task');
            Route::get('development/countscrapper/{id}', [DevelopmentController::class, 'taskScrapper']);
            Route::post('development/updatescrapperdata', [DevelopmentController::class, 'UpdateScrapper'])->name('development.updatescrapperdata');
            Route::post('development/updatescrapperremarksdata', [DevelopmentController::class, 'UpdateScrapperRemarks'])->name('development.updatescrapperremarksdata');

            Route::resource('task-types', TaskTypesController::class);

            Route::resource('development-messages-schedules', DeveloperMessagesAlertSchedulesController::class);
            Route::get('development', [DevelopmentController::class, 'index'])->name('development.index');
            Route::post('development/task/list-by-user-id', [DevelopmentController::class, 'taskListByUserId'])->name('development.task.list.by.user.id');
            Route::post('development/task/set-priority', [DevelopmentController::class, 'setTaskPriority'])->name('development.task.set.priority');
            Route::post('development/create', [DevelopmentController::class, 'store'])->name('development.store');
            Route::post('development/{id}/edit', [DevelopmentController::class, 'update'])->name('development.update');
            Route::post('development/{id}/verify', [DevelopmentController::class, 'verify'])->name('development.verify');
            Route::get('development/verify/view', [DevelopmentController::class, 'verifyView'])->name('development.verify.view');
            Route::delete('development/{id}/destroy', [DevelopmentController::class, 'destroy'])->name('development.destroy');
            Route::post('development/updateCost', [DevelopmentController::class, 'updateCost'])->name('development.update.cost');
            Route::post('development/{id}/status', [DevelopmentController::class, 'updateStatus'])->name('development.update.status');
            Route::post('development/{id}/updateTask', [DevelopmentController::class, 'updateTask'])->name('development.update.task');
            Route::post('development/{id}/updatePriority', [DevelopmentController::class, 'updatePriority'])->name('development.update.priority');
            Route::post('development/upload-attachments', [DevelopmentController::class, 'uploadAttachDocuments'])->name('development.upload.files');
            Route::get('download-file', [DevelopmentController::class, 'downloadFile'])->name('download.file');

            Route::post('development/reminder', [DevelopmentController::class, 'updateDevelopmentReminder']);
            Route::post('log_status/change/{id}', [MagentoProductPushErrors::class, 'changeStatus']);
            Route::post('log_history/list/{id}', [MagentoProductPushErrors::class, 'getHistory']);

            Route::get('development/list', [DevelopmentController::class, 'issueTaskIndex'])->name('development.task.issue.index');
            Route::get('development/scrapping/list', [DevelopmentController::class, 'scrappingTaskIndex'])->name('development.scrapping.index');

            Route::get('scrap/development/list', [DevelopmentController::class, 'scrappingTaskIndex'])->name('development.scrap.index');
            Route::get('development/scrap/list', [DevelopmentController::class, 'devScrappingTaskIndex'])->name('development.scrapper.index');
            Route::get('development/change-user', [DevelopmentController::class, 'changeUser'])->name('development.issue.change_user');
            Route::post('development/change-user', [DevelopmentController::class, 'changeUserStore'])->name('development.changeuser.store');
            Route::get('development-scrapper-data/{id}', [DevelopmentController::class, 'developmentScrapperData'])->name('development.scrapper_data');
            Route::get('development-scrapper-images-data/{id}', [DevelopmentController::class, 'developmentScrapperImagesData'])->name('development.scrapper_images_data');
            Route::post('scrapper-column-visbility', [DevelopmentController::class, 'scrapperColumnVisbilityUpdate'])->name('scrapper.column.update');
            Route::post('development-scrapper-data', [DevelopmentController::class, 'developmentGetScrapperData'])->name('development.getscrapperdata');
            Route::post('development/historyscrapper', [DevelopmentController::class, 'devScrappingTaskHistoryIndex'])->name('development.historyscrapper');
            Route::get('development/scrapperhistory/{id}', [DevelopmentController::class, 'devScrappingTaskHistory'])->name('development.scrapper_hisotry');
            Route::post('development-scrapper-update-all-statusdata', [DevelopmentController::class, 'developmentUpdateAllScrapperStatusData'])->name('development.updateallstatusdata');

            Route::post('ds-column-visbility', [DevelopmentController::class, 'dsColumnVisbilityUpdate'])->name('ds.column.update');
            Route::post('dl-column-visbility', [DevelopmentController::class, 'dlColumnVisbilityUpdate'])->name('dl.column.update');
            Route::get('development/summarylist', [DevelopmentController::class, 'summaryList'])->name('development.summarylist');
            Route::get('development/summary_list', [DevelopmentController::class, 'summaryListDev'])->name('development.summary_list');
            Route::post('development/statuscolor', [DevelopmentController::class, 'statuscolor'])->name('development.statuscolor');
            Route::get('development/flagtask', [DevelopmentController::class, 'flagtask'])->name('development.flagtask');
            Route::post('development/gettasktimemessage', [DevelopmentController::class, 'gettasktimemessage'])->name('development.gettasktimemessage');
            Route::post('development/getlogtasktimemessage', [DevelopmentController::class, 'getlogtasktimemessage'])->name('development.getlogtasktimemessage');
            Route::get('development/users', [DevelopmentController::class, 'usersList'])->name('development.userslist');
            Route::get('development/automatic/tasks', [DevelopmentController::class, 'automaticTasks'])->name('development.automatic.tasks');
            Route::post('development/automatic/tasks', [DevelopmentController::class, 'automaticTasks'])->name('development.automatic.tasks_post');
            Route::get('development/task-summary', [DevelopmentController::class, 'developmentTaskSummary'])->name('development.tasksSummary');
            Route::post('development/task-list', [DevelopmentController::class, 'developmentTaskList'])->name('development.tasksList');
            Route::post('save/task/message', [DevelopmentController::class, 'saveTaskMessage'])->name('development.taskmessage');
            Route::post('save/tasktime/message', [DevelopmentController::class, 'saveTaskTimeMessage'])->name('development.tasktimemessage');
            Route::post('development/issue/list-by-user-id', [DevelopmentController::class, 'listByUserId'])->name('development.issue.list.by.user.id');
            Route::post('development/issue/set-priority', [DevelopmentController::class, 'setPriority'])->name('development.issue.set.priority');
            Route::post('development/time/history/approve', [DevelopmentController::class, 'approveTimeHistory'])->name('development/time/history/approve');
            Route::post('development/time/history/start', [DevelopmentController::class, 'startTimeHistory'])->name('development/time/history/start');
            Route::post('development/time/history/approve/sendMessage', [DevelopmentController::class, 'sendReviseMessage'])->name('development/time/history/approve/sendMessage');
            Route::post('development/time/history/approve/sendRemindMessage', [DevelopmentController::class, 'sendRemindMessage'])->name('development/time/history/approve/sendRemindMessage');
            Route::get('development/timer/history', [DevelopmentController::class, 'getTimeHistoryStartEnd'])->name('development.timer.history');
            Route::post('development/time/meeting/approve/{task_id}', [DevelopmentController::class, 'approveMeetingHistory'])->name('development/time/meeting/approve');
            Route::post('development/time/meeting/store', [DevelopmentController::class, 'storeMeetingTime'])->name('development/time/meeting/store');
            Route::get('development/issue/create', [DevelopmentController::class, 'issueCreate'])->name('development.issue.create');
            Route::post('development/issue/create', [DevelopmentController::class, 'issueStore'])->name('development.issue.store');
            Route::get('development/issue/user/assign', [DevelopmentController::class, 'assignUser']);
            Route::get('development/issue/master/assign', [DevelopmentController::class, 'assignMasterUser']);
            Route::get('development/issue/team-lead/assign', [DevelopmentController::class, 'assignTeamlead']);
            Route::get('development/issue//tester/assign', [DevelopmentController::class, 'assignTester']);
            Route::get('development/issue/time/meetings', [DevelopmentController::class, 'getMeetingTimings']);
            Route::get('development/issue/module/assign', [DevelopmentController::class, 'changeModule']);
            Route::get('development/issue/user/resolve', [DevelopmentController::class, 'resolveIssue'])->name('development.issue.user.resolve');
            Route::get('development/issue/estimate_date/assign', [DevelopmentController::class, 'saveEstimateTime']);
            Route::get('development/date/history', [DevelopmentController::class, 'getDateHistory'])->name('development/date/history');
            Route::get('development/status/history', [DevelopmentController::class, 'getStatusHistory'])->name('development/status/history');
            Route::get('development/issue/estimate_minutes/assign', [DevelopmentController::class, 'saveEstimateMinutes'])->name('development.issue.estimate_minutes.store');
            Route::get('development/issue/priority-no/assign', [DevelopmentController::class, 'savePriorityNo'])->name('development.issue.savePriorityNo.store');
            Route::get('development/issue/responsible-user/assign', [DevelopmentController::class, 'assignResponsibleUser']);
            Route::get('development/issue/milestone/assign', [DevelopmentController::class, 'saveMilestone']);
            Route::get('development/issue/language/assign', [DevelopmentController::class, 'saveLanguage']);
            Route::post('development/{id}/assignIssue', [DevelopmentController::class, 'issueAssign'])->name('development.issue.assign');
            Route::delete('development/{id}/issueDestroy', [DevelopmentController::class, 'issueDestroy'])->name('development.issue.destroy');
            Route::get('development/overview', [DevelopmentController::class, 'overview'])->name('development.overview');
            Route::get('development/task-detail/{id}', [DevelopmentController::class, 'taskDetail'])->name('taskDetail');
            Route::get('development/new-task-popup', [DevelopmentController::class, 'openNewTaskPopup'])->name('openNewTaskPopup');
            Route::post('development/status/create', [DevelopmentController::class, 'statusStore'])->name('development.status.store');
            Route::post('development/module/create', [DevelopmentController::class, 'moduleStore'])->name('development.module.store');
            Route::delete('development/module/{id}/destroy', [DevelopmentController::class, 'moduleDestroy'])->name('development.module.destroy');
            Route::post('development/{id}/assignModule', [DevelopmentController::class, 'moduleAssign'])->name('development.module.assign');
            Route::post('development/comment/create', [DevelopmentController::class, 'commentStore'])->name('development.comment.store');
            Route::post('task/comment/create', [DevelopmentController::class, 'taskComment'])->name('task.comment.store');
            Route::post('development/{id}/awaiting/response', [DevelopmentController::class, 'awaitingResponse'])->name('development.comment.awaiting.response');
            Route::post('development/cost/store', [DevelopmentController::class, 'costStore'])->name('development.cost.store');

            // Development
            Route::get('development', [DevelopmentController::class, 'index'])->name('development.index');
            Route::get('development/update-values', [DevelopmentController::class, 'updateValues']);
            Route::post('development/create', [DevelopmentController::class, 'store'])->name('development.store');
            Route::post('development/{id}/edit', [DevelopmentController::class, 'update'])->name('development.update');
            Route::delete('development/{id}/destroy', [DevelopmentController::class, 'destroy'])->name('development.destroy');

            Route::get('development/issue/list', [DevelopmentController::class, 'issueIndex'])->name('development.issue.index');
            Route::get('development/issue/create', [DevelopmentController::class, 'issueCreate'])->name('development.issue.create');
            Route::post('development/issue/create', [DevelopmentController::class, 'issueStore'])->name('development.issue.store');
            Route::post('development/{id}/assignIssue', [DevelopmentController::class, 'issueAssign'])->name('development.issue.assign');
            Route::delete('development/{id}/issueDestroy', [DevelopmentController::class, 'issueDestroy'])->name('development.issue.destroy');

            Route::post('development/module/create', [DevelopmentController::class, 'moduleStore'])->name('development.module.store');
            Route::delete('development/module/{id}/destroy', [DevelopmentController::class, 'moduleDestroy'])->name('development.module.destroy');
            Route::post('development/{id}/assignModule', [DevelopmentController::class, 'moduleAssign'])->name('development.module.assign');

            Route::post('development/comment/create', [DevelopmentController::class, 'commentStore'])->name('development.comment.store');
            Route::post('development/{id}/awaiting/response', [DevelopmentController::class, 'awaitingResponse'])->name('development.comment.awaiting.response');

            Route::get('development/time/history', [DevelopmentController::class, 'getTimeHistory'])->name('development/time/history');
            Route::get('development/time/history/approved', [DevelopmentController::class, 'getTimeHistoryApproved'])->name('development/time/history/approved');
            Route::get('development/lead/time/history', [DevelopmentController::class, 'getLeadTimeHistory'])->name('development/lead/time/history');
            Route::get('development/user/history', [DevelopmentController::class, 'getUserHistory'])->name('development/user/history');
            Route::get('development/tracked/history', [DevelopmentController::class, 'getTrackedHistory'])->name('development/tracked/history');
            Route::post('development/create/hubstaff_task', [DevelopmentController::class, 'createHubstaffManualTask'])->name('development/create/hubstaff_task');
            Route::get('development/pull/history', [DevelopmentController::class, 'getPullHistory'])->name('development/pull/history');

            Route::prefix('development')->group(function () {
                Route::get('task/get', [DevelopmentController::class, 'taskGet'])->name('development.task.get');

                Route::prefix('update')->group(function () {
                    Route::post('start-date', [DevelopmentController::class, 'actionStartDateUpdate'])->name('development.update.start-date');
                    Route::post('estimate-date', [DevelopmentController::class, 'saveEstimateDate'])->name('development.update.estimate-date');
                    Route::post('estimate-due-date', [DevelopmentController::class, 'saveEstimateDueDate'])->name('development.update.estimate-due-date');
                    Route::post('cost', [DevelopmentController::class, 'saveAmount'])->name('development.save.cost');
                    Route::post('estimate-minutes', [DevelopmentController::class, 'saveEstimateMinutes'])->name('development.update.estimate-minutes');
                    Route::post('lead-estimate-minutes', [DevelopmentController::class, 'saveLeadEstimateTime'])->name('development.update.lead-estimate-minutes');
                    Route::post('lead-estimate-minutes/approve', [DevelopmentController::class, 'approveLeadTimeHistory'])->name('development.approve.lead-estimate-minutes');
                });

                Route::prefix('history')->group(function () {
                    Route::get('start-date/index', [DevelopmentController::class, 'historyStartDate'])->name('development.history.start-date.index');
                    Route::get('estimate-date/index', [DevelopmentController::class, 'historyEstimateDate'])->name('development.history.estimate-date.index');
                    Route::get('cost/index', [DevelopmentController::class, 'historyCost'])->name('development.history.cost.index');
                    Route::post('approve', [DevelopmentController::class, 'historyApproveSubmit'])->name('development-task.history.approve');
                    Route::get('approve/history', [DevelopmentController::class, 'historyApproveList'])->name('development-task.history.approve-history');
                });

                Route::post('upload-file', [DevelopmentController::class, 'uploadFile'])->name('development.upload-file');
                Route::get('files/record', [DevelopmentController::class, 'getUploadedFilesList'])->name('development.files.record');

                Route::get('task/show-estimate', [DevelopmentController::class, 'showTaskEstimateTime'])->name('task.estimate.list');
                Route::get('task/show-estimate-alert', [DevelopmentController::class, 'showTaskEstimateTimeAlert'])->name('task.estimate.alert');
            });

            // Passwords Manager
            Route::get('passwords', [PasswordController::class, 'index'])->name('password.index');
            Route::post('passwords/change', [PasswordController::class, 'changePasswords'])->name('passwords.change');
            Route::post('password/store', [PasswordController::class, 'store'])->name('password.store');
            Route::get('password/passwordManager', [PasswordController::class, 'manage'])->name('password.manage');
            Route::get('/search/username', [PasswordController::class, 'autoSuggestUsername']);
            Route::get('/search/email', [PasswordController::class, 'autoSuggestEmail']);
            Route::post('password/change', [PasswordController::class, 'changePassword'])->name('password.change');
            Route::post('password/sendWhatsApp', [PasswordController::class, 'sendWhatsApp'])->name('password.sendwhatsapp');
            Route::post('password/update', [PasswordController::class, 'update'])->name('update.password');
            Route::post('password/getHistory', [PasswordController::class, 'getHistory'])->name('password.history');
            Route::post('password/create-get-remark', [PasswordController::class, 'passwordCreateGetRemark'])->name('password.create.get.remark');

            Route::get('password/search', [PasswordController::class, 'passwordsSearch'])->name('password.search');
            Route::post('password/show/edit-data', [PasswordController::class, 'passwordsShowEditdata'])->name('password.show.edit-data');

            Route::post('password/send/email', [PasswordController::class, 'passwordSendEmail'])->name('password.send.email');
            Route::get('password/email/history', [PasswordController::class, 'passwordSendEmailHistory'])->name('password.email.history');

            //Language Manager
            Route::get('languages', [LanguageController::class, 'index'])->name('language.index');
            Route::post('language/store', [LanguageController::class, 'store'])->name('language.store');
            Route::post('language/update', [LanguageController::class, 'update'])->name('language.update');
            Route::post('language/delete', [LanguageController::class, 'delete'])->name('language.delete');

            // Documents Manager
            Route::get('development/document/list', [DocumentController::class, 'documentList'])->name('development.document.list');
            Route::get('documents', [DocumentController::class, 'index'])->name('document.index');
            Route::get('documents-email', [DocumentController::class, 'email'])->name('document.email');
            Route::post('document/store', [DocumentController::class, 'store'])->name('document.store');
            Route::post('document/{id}/update', [DocumentController::class, 'update'])->name('document.update');
            Route::get('document/{id}/download', [DocumentController::class, 'download'])->name('document.download');
            Route::delete('document/{id}/destroy', [DocumentController::class, 'destroy'])->name('document.destroy');
            Route::post('document/send/emailBulk', [DocumentController::class, 'sendEmailBulk'])->name('document.email.send.bulk');
            Route::get('document/gettaskremark', [DocumentController::class, 'getTaskRemark'])->name('document.gettaskremark');
            Route::post('document/uploadocument', [DocumentController::class, 'uploadDocument'])->name('document.uploadDocument');
            Route::post('document/addremark', [DocumentController::class, 'addRemark'])->name('document.addRemark');
            Route::get('document/shortcut-list', [DocumentController::class, 'listShorcut'])->name('documentShorcut.list');

            //Document Cateogry
            Route::post('documentcategory/add', [DocuemntCategoryController::class, 'addCategory'])->name('documentcategory.add');

            //SKU
            Route::get('sku-format/datatables', [SkuFormatController::class, 'getData'])->name('skuFormat.datatable');
            Route::get('sku-format/history', [SkuFormatController::class, 'history'])->name('skuFormat.history');
            Route::resource('sku-format', SkuFormatController::class);
            Route::post('sku-format/update', [SkuFormatController::class, 'update'])->name('sku.update');
            Route::get('sku/color-codes', [SkuController::class, 'colorCodes'])->name('sku.color-codes');
            Route::get('sku/color-codes-update', [SkuController::class, 'colorCodesUpdate'])->name('sku.color-codes-update');

            // Cash Flow Module
            Route::get('cashflow/hubstuff-command-log', [CashFlowController::class, 'hubstuffCommandLog'])->name('cashflow.hubstuff.log');
            Route::get('cashflow/flow-logs-detail', [CashFlowController::class, 'hubstuffCommandLogDetail'])->name('cashflow.hubstuff.detail');

            Route::get('cashflow/{id}/download', [CashFlowController::class, 'download'])->name('cashflow.download');
            Route::get('cashflow/mastercashflow', [CashFlowController::class, 'mastercashflow'])->name('cashflow.mastercashflow');
            Route::post('cashflow/do-payment', [CashFlowController::class, 'doPayment'])->name('cashflow.do-payment');
            Route::get('cashflow/getbnamelist', [CashFlowController::class, 'getBnameList']);
            Route::get('cashflow/getPaymentDetails', [CashFlowController::class, 'getPaymentDetails'])->name('cashflow.getPaymentDetails');
            Route::resource('cashflow', CashFlowController::class);
            Route::resource('dailycashflow', DailyCashFlowController::class);

            //URL Routes Module
            Route::get('routes', [RoutesController::class, 'index'])->name('routes.index');
            Route::get('routes/sync', [RoutesController::class, 'sync'])->name('routes.sync');
            Route::any('routes/update/{id}', [RoutesController::class, 'update'])->name('routes.update');
            Route::post('routes/email-alert', [RoutesController::class, 'updateEmailAlert'])->name('routes.update.email-alert');

            // Reviews Module
            Route::post('review/createFromInstagramHashtag', [ReviewController::class, 'createFromInstagramHashtag']);
            Route::post('review/restart-script', [ReviewController::class, 'restartScript']);
            Route::get('review/instagram/reply', [ReviewController::class, 'replyToPost']);
            Route::post('review/instagram/dm', [ReviewController::class, 'sendDm']);
            Route::get('review/{id}/updateStatus', [ReviewController::class, 'updateStatus']);
            Route::post('review/{id}/updateStatus', [ReviewController::class, 'updateStatus']);
            Route::post('review/{id}/updateReview', [ReviewController::class, 'updateReview']);
            Route::resource('review', ReviewController::class);
            Route::post('review/schedule/create', [ReviewController::class, 'scheduleStore'])->name('review.schedule.store');
            Route::put('review/schedule/{id}', [ReviewController::class, 'scheduleUpdate'])->name('review.schedule.update');
            Route::post('review/schedule/{id}/status', [ReviewController::class, 'scheduleUpdateStatus'])->name('review.schedule.updateStatus');
            Route::delete('review/schedule/{id}/destroy', [ReviewController::class, 'scheduleDestroy'])->name('review.schedule.destroy');
            Route::get('account/{id}', [AccountController::class, 'show']);
            Route::post('account/igdm/{id}', [AccountController::class, 'sendMessage']);
            Route::post('account/bulk/{id}', [AccountController::class, 'addMessageSchedule']);
            Route::post('account/create', [ReviewController::class, 'accountStore'])->name('account.store');
            Route::put('account/{id}', [ReviewController::class, 'accountUpdate'])->name('review.account.update');
            Route::delete('account/{id}/destroy', [ReviewController::class, 'accountDestroy'])->name('account.destroy');

            Route::get('brand-review/get', [BrandReviewController::class, 'getAllBrandReview']);

            Route::resource('brand-review/get', BrandReviewController::class);
            // Threads Routes
            Route::resource('thread', ThreadController::class);
            Route::post('thread/{id}/status', [ThreadController::class, 'updateStatus'])->name('thread.updateStatus');

            // Complaints Routes
            Route::resource('complaint', ComplaintController::class);
            Route::post('complaint/{id}/status', [ComplaintController::class, 'updateStatus'])->name('complaint.updateStatus');

            // Vendor Module
            Route::get('vendors-autocomplete', [VendorController::class, 'getVendorAutocomplete'])->name('vendors.autocomplete');
            Route::post('vendors/sorting', [VendorController::class, 'sortingVendorFlowchart'])->name('vendors.sorting');
            Route::get('vendors/flow-chart/{id}', [VendorController::class, 'flowChart'])->name('vendors.flow-chart');
            Route::get('vendors/all-section', [VendorController::class, 'vendorAllSection'])->name('vendors.all-section');
            Route::get('vendors/question-answer', [VendorController::class, 'questionAnswer'])->name('vendors.question-answer');
            Route::get('vendors/rating-question-answer', [VendorController::class, 'ratingquestionAnswer'])->name('vendors.rating.question-answer');
            Route::get('vendors/product', [VendorController::class, 'product'])->name('vendors.product.index');
            Route::post('vendors/store', [VendorController::class, 'store'])->name('vendors.store');
            Route::post('vendors/storeshortcut', [VendorController::class, 'storeshortcut'])->name('vendors.storeshortcut');
            Route::post('vendors/reply/add', [VendorController::class, 'addReply'])->name('vendors.reply.add');
            Route::get('vendors/reply/delete', [VendorController::class, 'deleteReply'])->name('vendors.reply.delete');
            Route::post('vendors/send/emailBulk', [VendorController::class, 'sendEmailBulk'])->name('vendors.email.send.bulk');
            Route::post('vendors/create-user', [VendorController::class, 'createUser'])->name('vendors.create.user');
            Route::post('vendors/edit-vendor', [VendorController::class, 'editVendor'])->name('vendors.edit-vendor');
            Route::post('vendors/send/message', [VendorController::class, 'sendMessage'])->name('vendors/send/message');
            Route::post('vendors/send/email', [VendorController::class, 'sendEmail'])->name('vendors.email.send');
            Route::get('vendors/email/inbox', [VendorController::class, 'emailInbox'])->name('vendors.email.inbox');
            Route::post('vendors/product', [VendorController::class, 'productStore'])->name('vendors.product.store');
            Route::put('vendors/product/{id}', [VendorController::class, 'productUpdate'])->name('vendors.product.update');
            Route::delete('vendors/product/{id}', [VendorController::class, 'productDestroy'])->name('vendors.product.destroy');
            Route::get('vendors/{vendor}/payments', [VendorPaymentController::class, 'index'])->name('vendors.payments');
            Route::post('vendors/{vendor}/payments', [VendorPaymentController::class, 'store'])->name('vendors.payments.store');
            Route::put('vendors/{vendor}/payments/{vendor_payment}', [VendorPaymentController::class, 'update'])->name('vendors.payments.update');
            Route::delete('vendors/{vendor}/payments/{vendor_payment}', [VendorPaymentController::class, 'destroy'])->name('vendors.payments.destroy');
            Route::get('vendors/search-rqa-modal', [VendorController::class, 'getVendorRatingData'])->name('vendors.rqa.modal');
            Route::resource('vendors', VendorController::class)->except(['store']);
            Route::post('vendors/add/framwork', [VendorController::class, 'framworkAdd']);
            Route::post('vendors/add/frequency', [VendorController::class, 'frequencyAdd']);
            Route::post('vendors/statuscolor', [VendorController::class, 'statuscolor'])->name('vendors.statuscolor');
            Route::post('vendors/update-status', [VendorController::class, 'updateStatus'])->name('vendor.status.update');
            Route::get('vendors/meetings/list', [VendorController::class, 'zoomMeetingList'])->name('vendor.meeting.list');
            Route::post('vendors/update-meeting-description', [VendorController::class, 'updateMeetingDescription'])->name('vendor.meeting.update');
            Route::post('vendors/refresh-meetings-recordings', [VendorController::class, 'refreshMeetingList'])->name('vendor.meeting.refresh');
            Route::post('vendors/sync-meetings-recordings', [VendorController::class, 'syncMeetingsRecordings'])->name('vendor.meetings.recordings.sync');
            Route::post('vendors/column-visbility', [VendorController::class, 'columnVisbilityUpdate'])->name('vendors.column.update');
            Route::post('vendors/delete-flowchart-category', [VendorController::class, 'deleteFlowchartCategory'])->name('delete.flowchart-category');
            Route::post('vendors/delete-flowchart-status', [VendorController::class, 'deleteFlowchartstatus'])->name('delete.flowchart-status');
            Route::post('vendors/delete-qa-category', [VendorController::class, 'deleteQACategory'])->name('delete.qa-category');
            Route::post('vendors/delete-qa-status', [VendorController::class, 'deleteQAStatus'])->name('delete.qa-status');
            Route::post('vendors/delete-rqa-category', [VendorController::class, 'deleteRQACategory'])->name('delete.rqa-category');
            Route::post('vendors/delete-v-status', [VendorController::class, 'deleteVStatus'])->name('delete.v-status');
            Route::post('vendors/delete-rqa-status', [VendorController::class, 'deleteRQAStatus'])->name('delete.rqa-status');
            Route::post('vendors/flowchart-sort-order', [VendorController::class, 'flowchartSortOrder'])->name('vendors.flowchart-sort-order');
            Route::post('vendors/qa-sort-order', [VendorController::class, 'qaSortOrder'])->name('vendors.qa-sort-order');
            Route::post('vendors/rqa-sort-order', [VendorController::class, 'rqaSortOrder'])->name('vendors.rqa-sort-order');

            Route::get('negative/coupon/response', [NegativeCouponResponseController::class, 'index'])->name('negative.coupon.response');
            Route::get('negative/coupon/response/search', [NegativeCouponResponseController::class, 'search'])->name('negative.coupon.response.search');

            //Position
            Route::post('positions/store', [PositionController::class, 'store'])->name('positions.store');

            Route::post('vendors/cv/store', [VendorResumeController::class, 'store'])->name('vendor.cv.store');

            Route::get('vendors/cv/index', [VendorResumeController::class, 'index'])->name('vendor.cv.index');
            Route::get('vendors/cv/search', [VendorResumeController::class, 'search'])->name('vendor.cv.search');
            Route::post('vendors/cv/get-work-experience', [VendorResumeController::class, 'getWorkExperience'])->name('vendors.cv.get-work-experience');
            Route::post('vendors/cv/get-education', [VendorResumeController::class, 'getEducation'])->name('vendors.cv.education');
            Route::post('vendors/cv/get-address', [VendorResumeController::class, 'getAddress'])->name('vendors.cv.address');

            Route::get('vendor/status/history', [VendorController::class, 'vendorStatusHistory'])->name('vendor.status.history.get');
            Route::get('vendor/price/history', [VendorController::class, 'vendorPriceHistory'])->name('vendor.price.history.get');
            Route::get('vendor/remark/history', [VendorController::class, 'vendorRemarkHistory'])->name('vendor.remark.history.get');
            Route::post('vendor/remark/history', [VendorController::class, 'vendorRemarkPostHistory'])->name('vendor.remark.history.post');
            Route::get('vendor/status/history/detail', [VendorController::class, 'vendorDetailStatusHistory'])->name('vendor.status.history.detail');
            Route::post('vendor/addStatusDetail', [VendorController::class, 'addStatus'])->name('vendors.addStatus');

            Route::get('vendor-search', [VendorController::class, 'vendorSearch'])->name('vendor-search');
            Route::get('vendor-search-phone', [VendorController::class, 'vendorSearchPhone'])->name('vendor-search-phone');
            Route::get('vendor-search-email', [VendorController::class, 'vendorSearchEmail'])->name('vendor-search-email');

            Route::post('vendors/email', [VendorController::class, 'email'])->name('vendors.email');
            Route::post('vendot/block', [VendorController::class, 'block'])->name('vendors.block');
            Route::post('vendors/inviteGithub', [VendorController::class, 'inviteGithub']);
            Route::post('vendors/inviteHubstaff', [VendorController::class, 'inviteHubstaff']);
            Route::post('vendors/changeHubstaffUserRole', [VendorController::class, 'changeHubstaffUserRole']);
            Route::post('vendors/change-status', [VendorController::class, 'changeStatus']);
            Route::get('vendor_category/assign-user', [VendorController::class, 'assignUserToCategory']);
            Route::post('vendor/changeWhatsapp', [VendorController::class, 'changeWhatsapp'])->name('vendor.changeWhatsapp');
            Route::post('vendor/status/create', [VendorController::class, 'statusStore'])->name('vendor.status.store');
            Route::post('vendor/flowchart/create', [VendorController::class, 'flowchartStore'])->name('vendor.flowchart.store');
            Route::post('vendor/flowchart-master/create', [VendorController::class, 'masterFlowchartStore'])->name('vendor.flowchart.master.store');
            Route::get('vendor/flowchart-master', [VendorController::class, 'getAllMasterFlowcharts'])->name('vendors.flowchart.master');
            Route::delete('vendor/flowchart-master/delete/{id?}', [VendorController::class, 'masterFlowchartDestroy'])->name('vendor.flowchart.master.destroy');
            Route::post('vendor/flowchart-master/update/{id?}', [VendorController::class, 'masterFlowchartUpdate'])->name('vendor.flowchart.master.update');
            Route::post('vendor/updateflowchart', [VendorController::class, 'assignMasterFlowchartToVendor'])->name('vendors.flowchart');
            Route::post('vendor/flowchart/remarks', [VendorController::class, 'saveVendorFlowChartRemarks'])->name('vendors.flowchart.saveremarks');
            Route::post('vendor/flowchart/getremarks', [VendorController::class, 'getFlowChartRemarksHistories'])->name('vendors.flowchart.getremarks');
            Route::post('vendors/flowchart/column-visbility', [VendorController::class, 'vendorFlowChartVolumnVisbilityUpdate'])->name('vendors.flowchart.column.update');

            Route::post('vendors/rqa/column-visbility', [VendorController::class, 'vendorRqaVolumnVisbilityUpdate'])->name('vendors.rqa.column.update');
            Route::post('vendors/qa/column-visbility', [VendorController::class, 'vendorQaVolumnVisbilityUpdate'])->name('vendors.qa.column.update');
            Route::post('vendor/updatefeedbackstatus', [VendorController::class, 'vendorFeedbackStatus'])->name('vendors.feedbackstatus');
            Route::post('vendor/question/create', [VendorController::class, 'questionStore'])->name('vendor.question.store');
            Route::post('vendor/notes/create', [VendorController::class, 'notesStore'])->name('vendor.notes.store');
            Route::post('vendor/flowchartnotes/create', [VendorController::class, 'flowchartnotesStore'])->name('vendor.flowchart.notes.store');
            Route::post('vendor/rquestion/create', [VendorController::class, 'rquestionStore'])->name('vendor.rquestion.store');
            Route::post('vendor/question/getquestion', [VendorController::class, 'getVendorQuestions'])->name('vendors.getquestion');
            Route::post('vendor/question/answer', [VendorController::class, 'saveVendorQuestionAnswer'])->name('vendors.question.saveanswer');
            Route::post('vendor/rquestion/answer', [VendorController::class, 'saveVendorRatingQuestionAnswer'])->name('vendors.question.saveranswer');
            Route::post('vendor/question/getanswer', [VendorController::class, 'getQuestionAnswerHistories'])->name('vendors.question.getgetanswer');
            Route::post('vendor/rquestion/getanswer', [VendorController::class, 'getRatingQuestionAnswerHistories'])->name('vendors.rquestion.getgetanswer');
            Route::post('vendor/questionansert', [VendorController::class, 'vendorQuestionAnswerStatus'])->name('vendors.questionansert');
            Route::post('vendor/rquestionansert', [VendorController::class, 'vendorRatingQuestionAnswerStatus'])->name('vendors.rquestionansert');
            Route::post('vendor/rquestionansertstatus/create', [VendorController::class, 'rqaStatusCreate'])->name('vendors.rqastatus.create');
            Route::post('vendor/rquestionansertstatuscolor', [VendorController::class, 'rqastatuscolor'])->name('vendors.rqastatuscolor');
            Route::post('vendor/questionansertstatus/create', [VendorController::class, 'qaStatusCreate'])->name('vendors.qastatus.create');
            Route::post('vendor/questionansertstatuscolor', [VendorController::class, 'qastatuscolor'])->name('vendors.qastatuscolor');
            Route::post('vendor/flowchartstatus/create', [VendorController::class, 'flowchartStatusCreate'])->name('vendors.flowchartstatus.create');
            Route::post('vendor/flowchartstatuscolor', [VendorController::class, 'flowchartstatuscolor'])->name('vendors.flowchartstatuscolor');
            Route::post('vendor/update-rqastatus', [VendorController::class, 'rqaupdateStatus'])->name('rqa-update-status');
            Route::post('vendor/rqastatus/histories', [VendorController::class, 'rqaStatusHistories'])->name('vendors.rqastatus.histories');
            Route::post('vendor/update-qastatus', [VendorController::class, 'qaupdateStatus'])->name('qa-update-status');
            Route::post('vendor/qastatus/histories', [VendorController::class, 'qaStatusHistories'])->name('vendors.qastatus.histories');
            Route::post('vendor/update-flowchartstatus', [VendorController::class, 'flowchartupdateStatus'])->name('flowchart-update-status');
            Route::post('vendor/flowchartstatus/histories', [VendorController::class, 'flowchartStatusHistories'])->name('vendors.flowchartstatus.histories');
            Route::post('vendor/questionanswer/notes', [VendorController::class, 'getVendorRatingQuestionsAnswerNotes'])->name('vendors.getrquestionnotes');
            Route::post('vendors/feedback/column-visbility', [UserManagementController::class, 'vendorFeedbackVolumnVisbilityUpdate'])->name('vendors.feedback.column.update');
            Route::post('vendors/search-flowchart-header/', [VendorController::class, 'searchVendorFlowcharts'])->name('vendors.flowcharts.search');
            Route::post('vendors/search-flowchart/', [VendorController::class, 'searchforVendorFlowcharts'])->name('vendors.flowchartssearch');
            Route::post('vendors/search-qa-header/', [VendorController::class, 'searchVendorQa'])->name('vendors.qa.search');
            Route::post('vendors/search-rqa-header/', [VendorController::class, 'searchVendorRQa'])->name('vendors.rqa.search');
            Route::post('vendor/flowchart/notes', [VendorController::class, 'getVendorFlowchartNotes'])->name('vendors.getflowchartnotes');
            Route::post('vendor/flowchartupdatesorting', [VendorController::class, 'flowchartupdatesorting'])->name('vendors.flowchartupdatesorting');
            Route::post('vendors/emails/', [VendorController::class, 'searchforVendorEmails'])->name('vendors.emails.action');
            Route::post('vendor/flowchart/notes-update', [VendorController::class, 'getVendorFlowchartUpdateNotes'])->name('vendors.getflowchartupdatenotes');
            Route::post('vendors/delete-flowchart-notes', [VendorController::class, 'deleteFlowchartnotes'])->name('delete.flowchart-notes');
            Route::post('vendor/rqa/notes-update', [VendorController::class, 'getVendorrqaUpdateNotes'])->name('vendors.getrqaupdatenotes');
            Route::post('vendors/delete-rqa-notes', [VendorController::class, 'deleteRqnotes'])->name('delete.rqa-notes');
            Route::post('supplier/emails/', [VendorController::class, 'searchforSupplierEmails'])->name('supplier.emails.action');

            Route::prefix('hubstaff-payment')->group(function () {
                Route::get('/', [HubstaffPaymentController::class, 'index'])->name('hubstaff-payment.index');
                Route::get('records', [HubstaffPaymentController::class, 'records'])->name('hubstaff-payment.records');
                Route::post('save', [HubstaffPaymentController::class, 'save'])->name('hubstaff-payment.save');
                Route::post('merge-category', [HubstaffPaymentController::class, 'mergeCategory'])->name('hubstaff-payment.merge-category');
                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [HubstaffPaymentController::class, 'edit'])->name('hubstaff-payment.edit');
                    Route::get('delete', [HubstaffPaymentController::class, 'delete'])->name('hubstaff-payment.delete');
                });
            });

            Route::prefix('hubstaff-activities')->group(function () {
                Route::get('/report', [HubstaffActivitiesController::class, 'activityReport'])->name('hubstaff-acitivtity.report');
                Route::get('/report-download', [HubstaffActivitiesController::class, 'activityReportDownload'])->name('hubstaff-acitivtity-report.download');
                Route::get('/payment_data', [HubstaffActivitiesController::class, 'activityPaymentData'])->name('hubstaff-acitivtity.payment_data');
                Route::post('/command_execution_manually', [HubstaffActivitiesController::class, 'HubstaffActivityCommandExecution'])->name('hubstaff-acitivtity.command_execution_manually');
                Route::get('/hubstaff-payment-download', [HubstaffActivitiesController::class, 'HubstaffPaymentReportDownload'])->name('hubstaff-payment-report.download');
                Route::get('/addtocashflow', [HubstaffActivitiesController::class, 'addtocashflow']);

                Route::prefix('notification')->group(function () {
                    Route::get('/', [HubstaffActivitiesController::class, 'notification'])->name('hubstaff-acitivties.notification.index');
                    Route::post('/download', [HubstaffActivitiesController::class, 'downloadNotification'])->name('hubstaff-acitivties.notification.download');
                    Route::get('/records', [HubstaffActivitiesController::class, 'notificationRecords'])->name('hubstaff.notification.records');
                    Route::post('/save', [HubstaffActivitiesController::class, 'notificationReasonSave'])->name('hubstaff.notification.save-reason');
                    Route::post('/change-status', [HubstaffActivitiesController::class, 'changeStatus'])->name('hubstaff.notification.change-status');
                });
                Route::prefix('activities')->group(function () {
                    Route::get('/', [HubstaffActivitiesController::class, 'getActivityUsers'])->name('hubstaff-acitivties.activities');
                    Route::get('/details', [HubstaffActivitiesController::class, 'getActivityDetails'])->name('hubstaff-acitivties.activity-details');
                    Route::post('/details', [HubstaffActivitiesController::class, 'approveActivity'])->name('hubstaff-acitivties.approve-activity');
                    Route::post('/final-submit', [HubstaffActivitiesController::class, 'finalSubmit'])->name('hubstaff-activities/activities/final-submit');
                    Route::post('/task-notes', [HubstaffActivitiesController::class, 'NotesHistory'])->name('hubstaff-activities.task.notes');
                    Route::get('/save-notes', [HubstaffActivitiesController::class, 'saveNotes'])->name('hubstaff-activities.task.save.notes');
                    Route::get('/approve-all-time', [HubstaffActivitiesController::class, 'approveTime'])->name('hubstaff-acitivties.approve.time');
                    Route::post('/fetch', [HubstaffActivitiesController::class, 'fetchActivitiesFromHubstaff'])->name('hubstaff-activities/activities/fetch');
                    Route::post('/manual-record', [HubstaffActivitiesController::class, 'submitManualRecords'])->name('hubstaff-acitivties.manual-record');
                    Route::get('/records', [HubstaffActivitiesController::class, 'notificationRecords'])->name('hubstaff-acitivties.notification.records');
                    Route::post('/save', [HubstaffActivitiesController::class, 'notificationReasonSave'])->name('hubstaff-acitivties.notification.save-reason');
                    Route::post('/change-status', [HubstaffActivitiesController::class, 'changeStatus'])->name('hubstaff-acitivties.notification.change-status');
                    Route::get('/approved/pending-payments', [HubstaffActivitiesController::class, 'approvedPendingPayments'])->name('hubstaff-acitivties.pending-payments');
                    Route::post('/approved/payment', [HubstaffActivitiesController::class, 'submitPaymentRequest'])->name('hubstaff-acitivties.payment-request.submit');
                    Route::post('/add-efficiency', [HubstaffActivitiesController::class, 'AddEfficiency'])->name('hubstaff-acitivties.efficiency.save');
                    Route::get('/task-activity', [HubstaffActivitiesController::class, 'taskActivity'])->name('hubstaff-acitivties.acitivties.task-activity');
                    Route::get('/userTreckTime', [HubstaffActivitiesController::class, 'userTreckTime'])->name('hubstaff-acitivties.acitivties.userTreckTime');
                });

                Route::post('save', [HubstaffPaymentController::class, 'save'])->name('hubstaff-acitivties.payment.save');
                Route::post('merge-category', [HubstaffPaymentController::class, 'mergeCategory'])->name('hubstaff-acitivties.merge-category');
                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [HubstaffPaymentController::class, 'edit'])->name('hubstaff-acitivties.edit');
                    Route::get('delete', [HubstaffPaymentController::class, 'delete'])->name('hubstaff-acitivties.delete');
                });
            });

            Route::prefix('manage-modules')->group(function () {
                Route::get('/', [ManageModulesController::class, 'index'])->name('manage-modules.index');
                Route::get('records', [ManageModulesController::class, 'records'])->name('manage-modules.records');
                Route::post('save', [ManageModulesController::class, 'save'])->name('manage-modules.save');
                Route::post('merge-module', [ManageModulesController::class, 'mergeModule'])->name('manage-modules.merge-module');
                Route::get('remove-module', [ManageModulesController::class, 'removeDeveloperModules'])->name('manage-modules.remove-module');
                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [ManageModulesController::class, 'edit'])->name('manage-modules.edit');
                    Route::get('delete', [ManageModulesController::class, 'delete'])->name('manage-modules.delete');
                });
                Route::get('countdevtask/{id}/{search_keyword}', [ManageModulesController::class, 'taskCount']);
            });

            Route::prefix('load-testing')->middleware('auth')->group(function () {
                Route::get('/', [LoadTestingController::class, 'index'])->name('load-testing.index');
                Route::get('response', [LoadTestingController::class, 'response']);
                Route::post('create', [LoadTestingController::class, 'create']);
                Route::get('submit-request/{id}', [LoadTestingController::class, 'submitRequest'])->name('submit-request');
                Route::get('view-result/{id?}', [LoadTestingController::class, 'viewResult'])->name('view-result');
                Route::get('jmter-post-data/{id}', [LoadTestingController::class, 'sendDataToJmeterApi'])->name('jmter-post-data');
                Route::get('get-jmter-response/{id}', [LoadTestingController::class, 'getJmeterResponse'])->name('get.jmter.response');

            });
            Route::prefix('manage-task-category')->group(function () {
                Route::get('/', [ManageTaskCategoryController::class, 'index'])->name('manage-task-category.index');
                Route::get('records', [ManageTaskCategoryController::class, 'records'])->name('manage-task-category.records');
                Route::post('save', [ManageTaskCategoryController::class, 'save'])->name('manage-task-category.save');
                Route::post('merge-module', [ManageTaskCategoryController::class, 'mergeModule'])->name('manage-task-category.merge-module');
                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [ManageTaskCategoryController::class, 'edit'])->name('manage-task-category.edit');
                    Route::get('delete', [ManageTaskCategoryController::class, 'delete'])->name('manage-task-category.delete');
                });
            });

            Route::prefix('vendor-category')->group(function () {
                Route::get('/', [VendorCategoryController::class, 'index'])->name('vendor-category.index');
                Route::get('records', [VendorCategoryController::class, 'records'])->name('vendor-category.records');
                Route::post('save', [VendorCategoryController::class, 'save'])->name('vendor-category.save');
                Route::post('merge-category', [VendorCategoryController::class, 'mergeCategory'])->name('vendor-category.merge-category');
                Route::get('/permission', [VendorCategoryController::class, 'usersPermission'])->name('vendor-category.permission');
                Route::post('/update/permission', [VendorCategoryController::class, 'updatePermission'])->name('vendor-category.update.permission');
                Route::get('/', [VendorCategoryController::class, 'index'])->name('vendor-category.index');

                Route::prefix('{id}')->group(function () {
                    Route::get('edit', [VendorCategoryController::class, 'edit'])->name('vendor-category.edit');
                    Route::get('delete', [VendorCategoryController::class, 'delete'])->name('vendor-category.delete');
                });
            });

            Route::resource('vendor_category', VendorCategoryController::class);

            // Suppliers Module
            Route::get('supplier/categorycount', [SupplierController::class, 'addSupplierCategoryCount'])->name('supplier.count');
            Route::post('supplier/saveCategoryCount', [SupplierController::class, 'saveSupplierCategoryCount'])->name('supplier.count.save');
            Route::post('supplier/getCategoryCount', [SupplierController::class, 'getSupplierCategoryCount'])->name('supplier.count.get');
            Route::post('supplier/updateCategoryCount', [SupplierController::class, 'updateSupplierCategoryCount'])->name('supplier.count.update');
            Route::post('supplier/deleteCategoryCount', [SupplierController::class, 'deleteSupplierCategoryCount'])->name('supplier.count.delete');

            Route::get('supplier-priority', [SupplierController::class, 'getPrioritiesList'])->name('supplier-priority.list');
            Route::post('supplier/add_new_priority', [SupplierController::class, 'addNewPriority'])->name('supplier.add_new_priority');
            Route::get('supplier/get-supplier', [SupplierController::class, 'getSupplierForPriority'])->name('supplier.get_supplier');
            Route::post('supplier/update_priority', [SupplierController::class, 'updateSupplierPriority'])->name('supplier.update_priority');
            Route::get('supplier/getSupplierPriorityList', [SupplierController::class, 'getSupplierPriorityList'])->name('supplier.get_supplier_priority_list');

            Route::get('supplier/brandcount', [SupplierController::class, 'addSupplierBrandCount'])->name('supplier.brand.count');
            Route::post('supplier/saveBrandCount', [SupplierController::class, 'saveSupplierBrandCount'])->name('supplier.brand.count.save');
            Route::post('supplier/getBrandCount', [SupplierController::class, 'getSupplierBrandCount'])->name('supplier.brand.count.get');
            Route::post('supplier/updateBrandCount', [SupplierController::class, 'updateSupplierBrandCount'])->name('supplier.brand.count.update');
            Route::post('supplier/deleteBrandCount', [SupplierController::class, 'deleteSupplierBrandCount'])->name('supplier.brand.count.delete');

            Route::get('supplier/get-scraped-brands', [SupplierController::class, 'getScrapedBrandAndBrandRaw'])->name('supplier.scrapedbrands.list');
            Route::post('supplier/update-scraped-brands', [SupplierController::class, 'updateScrapedBrandFromBrandRaw'])->name('supplier.scrapedbrands.update');
            Route::post('supplier/remove-scraped-brands', [SupplierController::class, 'removeScrapedBrand'])->name('supplier.scrapedbrands.remove');
            Route::post('supplier/copy-scraped-brands', [SupplierController::class, 'copyScrapedBrandToBrand'])->name('supplier.scrapedbrands.copy');
            Route::post('supplier/update-brands', [SupplierController::class, 'updateScrapedBrandFromBrandRaw'])->name('supplier.brands.update');
            Route::post('supplier/send/emailBulk', [SupplierController::class, 'sendEmailBulk'])->name('supplier.email.send.bulk');
            Route::post('supplier/change-whatsapp-no', [SupplierController::class, 'changeWhatsappNo'])->name('supplier.change.whatsapp');
            Route::get('supplier/{id}/loadMoreMessages', [SupplierController::class, 'loadMoreMessages']);
            Route::post('supplier/flag', [SupplierController::class, 'flag'])->name('supplier.flag');
            Route::post('supplier/trasnlate/history', [SupplierController::class, 'MessageTranslateHistory'])->name('supplier.history');
            Route::resource('supplier', SupplierController::class);
            Route::resource('google-server', GoogleServerController::class);
            Route::post('log-google-cse', [GoogleServerController::class, 'logGoogleCse'])->name('log.google.cse');

            Route::post('email-addresses/password/change', [EmailAddressesController::class, 'passwordChange'])->name('email.password.change');
            Route::post('email-addresses/sendon/whatsapp', [EmailAddressesController::class, 'sendToWhatsApp'])->name('email.password.sendwhatsapp');
            Route::post('email-addresses/assign', [EmailAddressesController::class, 'assignUsers'])->name('email-addresses.assign');
            Route::post('/email-addresses/single-email-run-cron', [EmailAddressesController::class, 'singleEmailRunCron']);
            Route::get('email-addresses/run-histories-truncate', [EmailAddressesController::class, 'runHistoriesTruncate'])->name('email-addresses.run-histories-truncate');
            Route::get('email-addresses/run-job/lists', [EmailAddressesController::class, 'listEmailRunLogs'])->name('email-addresses.run-histories-listing');
            Route::resource('email-addresses', EmailAddressesController::class);
            Route::post('email-addresses/create-acknowledgement', [EmailAddressesController::class, 'createAcknowledgement'])->name('email-addresses.create.acknowledgement');
            Route::get('email-addresses/countemailacknowledgement/{id}', [EmailAddressesController::class, 'acknowledgementCount']);
            Route::post('email-addresses/email-alert', [EmailAddressesController::class, 'setEmailAlert'])->name('email-addresses.email-alert');
            Route::post('email/geterroremailhistory', [EmailAddressesController::class, 'getErrorEmailHistory']);
            Route::get('email/failed/download/history', [EmailAddressesController::class, 'downloadFailedHistory'])->name('email.failed.download');
            Route::post('email/getemailhistory/{id}', [EmailAddressesController::class, 'getEmailAddressHistory']);
            Route::get('email/Emailaddress/search', [EmailAddressesController::class, 'searchEmailAddress'])->name('email.address.search');
            Route::post('email/Emailaddress/update', [EmailAddressesController::class, 'updateEmailAddress'])->name('email.address.update');
            Route::get('email/get-related-account/{id}', [EmailAddressesController::class, 'getRelatedAccount']);

            Route::post('supplier/block', [SupplierController::class, 'block'])->name('supplier.block');
            Route::post('supplier/saveImage', [SupplierController::class, 'saveImage'])->name('supplier.image');
            Route::post('supplier/change-status', [SupplierController::class, 'changeStatus']);
            Route::post('supplier/change/category', [SupplierController::class, 'changeCategory'])->name('supplier/change/category');
            Route::post('supplier/change/status', [SupplierController::class, 'changeSupplierStatus'])->name('supplier/change/status');
            Route::post('supplier/change/subcategory', [SupplierController::class, 'changeSubCategory'])->name('supplier/change/subcategory');
            Route::post('supplier/add/category', [SupplierController::class, 'addCategory'])->name('supplier/add/category');
            Route::post('supplier/add/subcategory', [SupplierController::class, 'addSubCategory'])->name('supplier/add/subcategory');
            Route::post('supplier/add/status', [SupplierController::class, 'addStatus'])->name('supplier/add/status');
            Route::post('supplier/add/suppliersize', [SupplierController::class, 'addSupplierSize'])->name('supplier/add/suppliersize');
            Route::post('supplier/change/inventorylifetime', [SupplierController::class, 'editInventorylifetime'])->name('supplier/change/inventorylifetime');
            Route::post('supplier/change/scrapper', [SupplierController::class, 'changeScrapper'])->name('supplier/change/scrapper');
            Route::post('supplier/change/language', [SupplierController::class, 'changeLanguage'])->name('supplier/change/language');
            Route::post('supplier/send/message', [SupplierController::class, 'sendMessage'])->name('supplier/send/message');
            Route::post('supplier/change/mail', [SupplierController::class, 'changeMail'])->name('supplier/change/mail');
            Route::post('supplier/change/phone', [SupplierController::class, 'changePhone'])->name('supplier/change/phone');
            Route::post('supplier/change/size', [SupplierController::class, 'changeSize'])->name('supplier/change/size');
            Route::post('supplier/change/size-system', [SupplierController::class, 'changeSizeSystem'])->name('supplier/change/size-system');
            Route::post('supplier/change/whatsapp', [SupplierController::class, 'changeWhatsapp'])->name('supplier/change/whatsapp');
            Route::get('supplier/category/permission', [SupplierCategoryController::class, 'usersPermission'])->name('supplier/category/permission');
            Route::post('supplier/category/update/permission', [SupplierCategoryController::class, 'updatePermission'])->name('supplier/category/update/permission');
            Route::post('supplier/add/pricernage', [SupplierController::class, 'addPriceRange'])->name('supplier/add/pricernage');
            Route::post('supplier/change/pricerange', [SupplierController::class, 'changePriceRange'])->name('supplier/change/pricerange');

            // API Response
            Route::get('api-response', [ApiResponseMessageController::class, 'index'])->name('api-response-message');
            Route::post('api-response', [ApiResponseMessageController::class, 'store'])->name('api-response-message.store');
            Route::post('/getEditModal', [ApiResponseMessageController::class, 'getEditModal'])->name('getEditModal');
            Route::post('api-response/lodeTranslation', [ApiResponseMessageController::class, 'lodeTranslation'])->name('api-response-message.lodeTranslation');
            Route::post('api-response/message-translate', [ApiResponseMessageController::class, 'messageTranslate'])->name('api-response-message.messageTranslate');
            Route::get('api-response/message-translate-list', [ApiResponseMessageController::class, 'messageTranslateList'])->name('api-response-message.messageTranslateList');
            Route::post('api-response/message-translate-approve', [ApiResponseMessageController::class, 'messageTranslateApprove'])->name('api-response-message.messageTranslateApprove');
            Route::post('/api-response-message-update', [ApiResponseMessageController::class, 'update'])->name('api-response-message.updateResponse');
            Route::get('/api-response-message-dalete/{id}', [ApiResponseMessageController::class, 'destroy'])->name('api-response-message.responseDelete');

            Route::get('assets-manager/list', [ApiResponseMessageController::class, 'indexJson'])->name('assetsManager.list');
            Route::get('assets-manager/loadTable', [ApiResponseMessageController::class, 'loadTable'])->name('assetsManager.loadTable');
            Route::resource('assets-manager', AssetsManagerController::class);
            Route::post('assets-manager/add-note/{id}', [AssetsManagerController::class, 'addNote']);
            Route::post('assets-manager/update-status', [AssetsManagerController::class, 'updateStatus'])->name('assets-manager.update-status');
            Route::post('assets-manager/payment-history', [AssetsManagerController::class, 'paymentHistory'])->name('assetsmanager.paymentHistory');
            Route::post('assets-manager/log', [AssetsManagerController::class, 'assetManamentLog'])->name('assetsmanager.assetManamentLog');
            Route::post('assets-manager/magento-dev-update-script-history/{asset_manager_id?}', [AssetsManagerController::class, 'getMagentoDevScriptUpdatesLogs']);
            Route::post('assets-manager/magento-dev-script-update', [AssetsManagerController::class, 'magentoDevScriptUpdate']);
            Route::post('assets-manager/userchanges/history', [AssetsManagerController::class, 'userChangesHistoryLog'])->name('assetsmanager.userchange.history');
            Route::post('assets-manager/plateform/add', [AssetsManagerController::class, 'plateFormStore'])->name('asset.manage.plateform.add');
            Route::post('assets-manager/send/email', [AssetsManagerController::class, 'assetsManagerSendEmail'])->name('asset.manage.send.email');
            Route::post('assets-manager/records/permission', [AssetsManagerController::class, 'assetsManagerRecordPermission'])->name('asset.manage.records.permission');
            Route::post('assets-manager/linkuser/list', [AssetsManagerController::class, 'linkUserList'])->name('assetsmanager.linkuser.list');
            Route::post('assets-manager/users', [AssetsManagerController::class, 'assetManamentUsers'])->name('assetsmanager.assetManamentUsers');
            Route::post('assets-manager/users_access', [AssetsManagerController::class, 'assetManamentUsersAccess'])->name('assetsmanager.assetManamentUsersAccess');
            Route::post('assets-manager/user-access-create', [AssetsManagerController::class, 'createUserAccess']);
            Route::post('assets-manager/user-access-delete', [AssetsManagerController::class, 'deleteUserAccess']);
            Route::get('assets-manager/user_accesses', [AssetsManagerController::class, 'assetsManagerUserAccessList'])->name('assets_manager_user_accesses');
            Route::get('assets-manager.users', [AssetsManagerController::class, 'assetsUserList'])->name('assetsmanager.users');
            Route::get('assets-manager-user-access-request/{id}', [AssetsManagerController::class, 'userAccessRequest'])->name('assetsmanager.user_access_request');
            Route::post('assets-manager-column-visbility', [AssetsManagerController::class, 'asColumnVisbilityUpdate'])->name('assetsmanager.column.update');
            Route::post('assets-manager/terminal-user-access-create', [AssetsManagerController::class, 'createTerminalUserAccess']);
            Route::post('assets-manager/terminal_users_access', [AssetsManagerController::class, 'assetManamentTerminalUsersAccess'])->name('assetsmanager.assetManamentTerminalUsersAccess');
            Route::post('assets-manager/terminal-user-access-delete', [AssetsManagerController::class, 'deleteTerminalUserAccess']);
            Route::post('assets-manager/remarks', [AssetsManagerController::class, 'saveRemarks'])->name('assetsmanager.saveremarks');
            Route::post('assets-manager/getremarks', [AssetsManagerController::class, 'getRemarksHistories'])->name('assetsmanager.getremarks');
            Route::post('assets-manager/updateup', [AssetsManagerController::class, 'updateUsernamePassword'])->name('assetsmanager.updateup');

            // Agent Routes
            Route::resource('agent', AgentController::class);
            Route::prefix('product-templates')->middleware('auth')->group(function () {
                Route::get('/', [ProductTemplatesController::class, 'index'])->name('product.templates');
                Route::get('/log', [ProductTemplatesController::class, 'getlog'])->name('product.templates.log');
                Route::post('/', [ProductTemplatesController::class, 'index'])->name('agent.product.templates');
                Route::get('response', [ProductTemplatesController::class, 'response']);
                Route::post('create', [ProductTemplatesController::class, 'create']);
                Route::post('reload-image', [ProductTemplatesController::class, 'fetchImage']);
                Route::get('destroy/{id}', [ProductTemplatesController::class, 'destroy']);
                Route::get('select-product-id', [ProductTemplatesController::class, 'selectProductId']);
                Route::get('image', [ProductTemplatesController::class, 'imageIndex'])->name('product.index.image');
                Route::get('/get-log', [ProductTemplatesController::class, 'loginstance'])->name('product.templates.getlog');
            });

            Route::prefix('load-testing')->middleware('auth')->group(function () {
                Route::get('/', [LoadTestingController::class, 'index'])->name('load-testing.index');
                Route::get('response', [LoadTestingController::class, 'response']);
                Route::post('create', [LoadTestingController::class, 'create']);
                Route::get('submit-request/{id}', [LoadTestingController::class, 'submitRequest'])->name('submit-request');
                Route::get('view-result/{id?}', [LoadTestingController::class, 'viewResult'])->name('view-result');
                Route::get('jmter-post-data/{id}', [LoadTestingController::class, 'sendDataToJmeterApi'])->name('jmter-post-data');
            });

            Route::prefix('templates')->middleware('auth')->group(function () {
                Route::get('/', [TemplatesController::class, 'index'])->name('templates');
                Route::get('response', [TemplatesController::class, 'response']);
                Route::get('fetch/bearbanner/templates', [TemplatesController::class, 'updateTemplatesFromBearBanner'])->name('fetch.bearbanner.templates');
                Route::post('update/bearbanner/template', [TemplatesController::class, 'updateBearBannerTemplate'])->name('update.bearbanner.template');
                Route::post('create', [TemplatesController::class, 'create']);
                Route::post('edit', [TemplatesController::class, 'edit']);
                Route::get('destroy/{id}', [TemplatesController::class, 'destroy']);
                Route::get('generate-template-category-branch', [TemplatesController::class, 'generateTempalateCategoryBrand']);
                Route::get('type', [TemplatesController::class, 'typeIndex'])->name('templates.type');
            });

            Route::prefix('load-testing')->middleware('auth')->group(function () {
                Route::get('/', [LoadTestingController::class, 'index'])->name('load-testing.index');
                Route::get('response', [LoadTestingController::class, 'response']);
                Route::post('create', [LoadTestingController::class, 'create']);
                Route::get('submit-request/{id}', [LoadTestingController::class, 'submitRequest'])->name('submit-request');
                Route::get('view-result/{id?}', [LoadTestingController::class, 'viewResult'])->name('view-result');
                Route::get('jmter-post-data/{id}', [LoadTestingController::class, 'sendDataToJmeterApi'])->name('jmter-post-data');

                Route::get('result', [LoadTestingController::class, 'filterResult'])->name('load-testing.result');
                Route::post('result', [LoadTestingController::class, 'filterResult'])->name('load-testing.result.filter');
            });

            Route::prefix('code-shortcuts')->middleware('auth')->group(function () {
                Route::get('/', [CodeShortcutController::class, 'index'])->name('code-shortcuts');
                Route::post('/store', [CodeShortcutController::class, 'store'])->name('code-shortcuts.store');
                Route::put('/{id}/update', [CodeShortcutController::class, 'update'])->name('code-shortcuts.update');
                Route::get('/{id}/destory', [CodeShortcutController::class, 'destory'])->name('code-shortcuts.destory');
                Route::post('/shortcut/platform/store', [CodeShortcutController::class, 'shortcutPlatformStore'])->name('code-shortcuts.platform.store');
                Route::get('/shortcut/notes', [CodeShortcutController::class, 'getShortcutnotes'])->name('code.get.Shortcut.notes');
                Route::get('/folder/list', [CodeShortcutController::class, 'shortcutListFolder'])->name('code.get.Shortcut.folder.list');
                Route::Post('/folder/create', [CodeShortcutController::class, 'shortcutCreateFolder'])->name('code.get.Shortcut.folder.create');
                Route::post('/folder/edit', [CodeShortcutController::class, 'shortcutEditFolder']);
                Route::delete('/folder/delete', [CodeShortcutController::class, 'shortcutDeleteFolder']);
                Route::post('/folder/user/permission', [CodeShortcutController::class, 'shortcutUserPermission'])->name('folder.permission');
                Route::get('code-shortcut/truncate', [CodeShortcutController::class, 'CodeShortCutTruncate'])->name('codeShort.log.truncate');
                Route::get('code-shortcut-title/{id}', [CodeShortcutController::class, 'getListCodeShortCut'])->name('code.get.Shortcut.data');
                Route::post('create/shortcut-code', [CodeShortcutController::class, 'createShortcutCode'])->name('shortcut.code.create');
            });

            Route::prefix('erp-events')->middleware('auth')->group(function () {
                Route::get('/', [ErpEventController::class, 'index'])->name('erp-events');
                Route::post('/store', [ErpEventController::class, 'store'])->name('erp-events.store');
                Route::get('/dummy', [ErpEventController::class, 'dummy'])->name('erp-events.dummy');
            });

            Route::get('/drafted-products', [ProductController::class, 'draftedProducts']);
            Route::get('/drafted-products/edit', [ProductController::class, 'editDraftedProduct']);
            Route::post('/drafted-products/edit', [ProductController::class, 'editDraftedProducts']);
            Route::post('/drafted-products/delete', [ProductController::class, 'deleteDraftedProducts']);
            Route::post('/drafted-products/addtoquicksell', [ProductController::class, 'addDraftProductsToQuickSell']);
            Route::post('/drafted-products/send-lead-price', [ProductController::class, 'sendLeadPrice']);
            Route::get('twillio-missing-keywrods', [ChatbotTypeErrorLogController::class, 'index'])->name('chatbot.type.error.log');

            //emails_extraction
            Route::resource('email-data-extraction', EmailDataExtractionController::class);
            Route::prefix('email-data-extraction')->group(function () {
                Route::get('/replyMail/{id}', [EmailDataExtractionController::class, 'replyMail']);
                Route::post('/replyMail', [EmailDataExtractionController::class, 'submitReply'])->name('email-data-extraction.submit-reply');

                Route::get('/forwardMail/{id}', [EmailDataExtractionController::class, 'forwardMail']);
                Route::post('/forwardMail', [EmailDataExtractionController::class, 'submitForward'])->name('email-data-extraction.submit-forward');

                Route::post('/resendMail/{id}', [EmailDataExtractionController::class, 'resendMail']);
                Route::put('/{id}/mark-as-read', [EmailDataExtractionController::class, 'markAsRead']);
                Route::post('/{id}/excel-import', [EmailDataExtractionController::class, 'excelImporter']);
                Route::post('/{id}/get-file-status', [EmailDataExtractionController::class, 'getFileStatus']);

                Route::get('/events/{originId}', [EmailDataExtractionController::class, 'getEmailEvents']);
                Route::get('/emaillog/{emailId}', [EmailDataExtractionController::class, 'getEmailLogs']);

                Route::get('/order_data/{email?}', [EmailDataExtractionController::class, 'index']); //Purpose : Add Route -  DEVTASK-18283
                Route::post('/platform-update', [EmailDataExtractionController::class, 'platformUpdate']);

                Route::post('/update_email', [EmailDataExtractionController::class, 'updateEmail']);

                Route::post('/bluckAction', [EmailDataExtractionController::class, 'bluckAction'])->name('email-data-extraction.bluckAction');
                Route::post('/changeStatus', [EmailDataExtractionController::class, 'changeStatus'])->name('email-data-extraction.changeStatus');
                Route::post('/change-email-category', [EmailDataExtractionController::class, 'changeEmailCategory'])->name('email-data-extraction.changeEmailCategory');

                Route::get('/email-remark', [EmailDataExtractionController::class, 'getRemark'])->name('email-data-extraction.getremark');
                Route::post('/email-remark', [EmailDataExtractionController::class, 'addRemark'])->name('email-data-extraction.addRemark');
            });

            Route::prefix('user-avaibility')->group(function () {
                Route::get('search', [UserAvaibilityController::class, 'search'])->name('user-avaibility.search');
                Route::get('list', [UserAvaibilityController::class, 'index'])->name('user-avaibility.index');
                Route::post('save', [UserAvaibilityController::class, 'save'])->name('user-avaibility.save');
            });
        });

        Route::post('livechat/incoming', [LiveChatController::class, 'incoming']);
        Route::post('livechat/getChats', [LiveChatController::class, 'getChats'])->name('livechat.get.message');
        Route::post('livechat/getCustomerInfo', [LiveChatController::class, 'customerInfo'])->name('livechat.get.customerInfo');
        Route::post('livechat/getLastChats', [LiveChatController::class, 'getLastChats'])->name('livechat.last.message');
        Route::post('livechat/getChatsWithoutRefresh', [LiveChatController::class, 'getChatMessagesWithoutRefresh'])->name('livechat.message.withoutrefresh');
        Route::post('livechat/sendMessage', [LiveChatController::class, 'sendMessage'])->name('livechat.send.message');
        Route::post('livechat/sendFile', [LiveChatController::class, 'sendFile'])->name('livechat.send.file');
        Route::post('livechat/getUserList', [LiveChatController::class, 'getUserList'])->name('livechat.get.userlist');
        Route::post('livechat/save-token', [LiveChatController::class, 'saveToken'])->name('livechat.save.token');
        Route::post('livechat/check-new-chat', [LiveChatController::class, 'checkNewChat'])->name('livechat.new.chat');

        Route::get('livechat/getLiveChats', [LiveChatController::class, 'getLiveChats'])->name('livechat.get.chats');
        Route::get('livechat/getLiveChats/logs/{chatId}', [LiveChatController::class, 'getChatLogs'])->name('livechat.get.chatlogs');
        Route::get('livechat/getLiveChats/eventlogs/{chatId}', [LiveChatController::class, 'getChatEventLogs'])->name('livechat.event.chatlogs');
        Route::get('livechat/getLiveChats/eventlogs', [LiveChatController::class, 'getAllChatEventLogs'])->name('livechat.event.logs');

        Route::get('livechat/getorderdetails', [LiveChatController::class, 'getorderdetails'])->name('livechat.getorderdetails');
        Route::get('livechat/chat-header-model', [LiveChatController::class, 'chatHeaderModel'])->name('livechat.chatHeaderModel');

        Route::get('twilio/getChats', [TwiliochatController::class, 'getTwilioChat'])->name('twilio.get.chats');
        Route::get('twilio/chats/delete/{id}', [TwiliochatController::class, 'chatsDelete'])->name('twilio.chats.delete');
        Route::get('twilio/chats/edit', [TwiliochatController::class, 'twilioChatsEdit'])->name('twilio.chats.edit');
        Route::any('twilio/chats/update', [TwiliochatController::class, 'twilioChatsUpdate'])->name('twilio.chats.update');

        Route::get('/brand-review', [BrandReviewController::class, 'index']);
        Route::post('/brand-review/store', [BrandReviewController::class, 'store'])->name('brandreview.store');

        Route::prefix('livechat')->group(function () {
            Route::post('/attach-image', [LiveChatController::class, 'attachImage'])->name('live-chat.attach.image');
            Route::post('/get-livechat-coupon-code', [LiveChatController::class, 'getLiveChatCouponCode'])->name('get-livechat-coupon-code');
            Route::post('/send-livechat-coupon-code', [LiveChatController::class, 'sendLiveChatCouponCode'])->name('send-livechat-coupon-code');
        });
        /* ---------------------------------------------------------------------------------- */

        Route::post('livechat/send-file', [LiveChatController::class, 'sendFileToLiveChatInc'])->name('livechat.upload.file');
        Route::get('livechat/get-customer-info', [LiveChatController::class, 'getLiveChatIncCustomer'])->name('livechat.customer.info');
        /*------------------------------------------- livechat tickets -------------------------------- */
        Route::get('livechat/tickets', [LiveChatController::class, 'tickets'])->name('livechat.get.tickets');
        Route::get('livechat/loadreplycategories', [LiveChatController::class, 'loadReplyCategories'])->name('loadreplycategories');
        Route::post('livechat-tickets-column-visbility', [LiveChatController::class, 'columnVisbilityUpdate'])->name('livechat.column.update');
        Route::post('livechat/tickets/update-ticket', [LiveChatController::class, 'updateTicket'])->name('livechat.tickets.update-ticket');
        Route::post('livechat/tickets/approve-ticket', [LiveChatController::class, 'approveTicket'])->name('livechat.tickets.approve-ticket');
        Route::post('livechat/tickets/ticket-data', [LiveChatController::class, 'ticketData'])->name('livechat.tickets.ticket-data');
        Route::get('livechat-replise/{id}', [SocialAccountCommentController::class, 'getEmailreplies']);
        Route::post('livechat/statuscolor', [LiveChatController::class, 'statuscolor'])->name('livechat.statuscolor');
        Route::post('tickets/email-send', [LiveChatController::class, 'sendEmail'])->name('tickets.email.send');
        Route::post('tickets/assign-ticket', [LiveChatController::class, 'AssignTicket'])->name('tickets.assign');
        Route::post('tickets/add-ticket-status', [LiveChatController::class, 'TicketStatus'])->name('tickets.add.status');
        Route::post('tickets/change-ticket-status', [LiveChatController::class, 'ChangeStatus'])->name('tickets.status.change');
        Route::post('tickets/change-ticket-date', [LiveChatController::class, 'ChangeDate'])->name('tickets.date.change');
        Route::post('tickets/send-brodcast', [LiveChatController::class, 'sendBrodcast'])->name('tickets.send-brodcast');
        Route::post('tickets/delete_tickets', [LiveChatController::class, 'delete_tickets'])->name('livetickets.delete');

        Route::get('tickets/emails/{ticketId}', [LiveChatController::class, 'fetchEmailsOnTicket'])->name('livetickets.fetchEmailsOnTicket');

        Route::post('livechat/create-ticket', [LiveChatController::class, 'createTickets'])->name('livechat.create.ticket');
        Route::get('livechat/get-tickets-data', [LiveChatController::class, 'getTicketsData'])->name('livechat.get.tickets.data');
        Route::post('livechat/create-credit', [LiveChatController::class, 'createCredits'])->name('livechat.create.credit');
        Route::post('credit/email-credit-log', [CustomerController::class, 'creditEmailLog'])->name('credit.get.email.log');
        Route::get('livechat/get-credits-data', [LiveChatController::class, 'getCreditsData'])->name('livechat.get.credits.data');
        Route::get('livechat/get-credits-email-privew', [LiveChatController::class, 'creditEmailPriview'])->name('livechat.get.credits.email.privew');

        Route::post('whatsapp/incoming', [WhatsAppController::class, 'incomingMessage']);
        Route::post('whatsapp/incomingNew', [WhatsAppController::class, 'incomingMessageNew']);
        Route::post('whatsapp/outgoingProcessed', [WhatsAppController::class, 'outgoingProcessed']);
        Route::post('whatsapp/webhook', [WhatsAppController::class, 'webhook']);
        Route::post('whatsapp/webhook-official', [WhatsAppController::class, 'webhookOfficial']);
        Route::get('whatsapp/webhook-official', [WhatsAppController::class, 'webhookOfficialVerify']);

        Route::get('whatsapp/pullApiwha', [WhatsAppController::class, 'pullApiwha']);

        Route::post('whatsapp/sendMessage/{context}', [WhatsAppController::class, 'sendMessage'])->name('whatsapp.send');
        Route::post('whatsapp/sendMultipleMessages', [WhatsAppController::class, 'sendMultipleMessages']);
        Route::post('whatsapp/approve/{context}', [WhatsAppController::class, 'approveMessage']);
        Route::get('whatsapp/pollMessages/{context}', [WhatsAppController::class, 'pollMessages']);
        Route::get('whatsapp/pollMessagesCustomer', [WhatsAppController::class, 'pollMessagesCustomer']);
        Route::get('whatsapp/updatestatus/', [WhatsAppController::class, 'updateStatus']);
        Route::post('whatsapp/updateAndCreate/', [WhatsAppController::class, 'updateAndCreate']);
        Route::post('whatsapp/forwardMessage/', [WhatsAppController::class, 'forwardMessage'])->name('whatsapp.forward');
        Route::post('whatsapp/{id}/fixMessageError', [WhatsAppController::class, 'fixMessageError']);
        Route::post('whatsapp/{id}/resendMessage', [WhatsAppController::class, 'resendMessage']);
        Route::get('message/resend', [WhatsAppController::class, 'resendMessage2']);
        Route::get('message/delete', [WhatsAppController::class, 'delete']);

        Route::post('list/autoCompleteMessages', [WhatsAppController::class, 'autoCompleteMessages']);
        Route::get('google/bigData/bigQuery', [GoogleBigQueryDataController::class, 'index'])->name('google.bigdata');
        Route::post('/google/bigData/bigQuery/column-visibility-update', [GoogleBigQueryDataController::class, 'columnVisibilityUpdate'])->name('google.bigdata.column.update');
        Route::get('google/bigData/search', [GoogleBigQueryDataController::class, 'search'])->name('google.bigdata.search');
        Route::delete('google/bigData/delete', [GoogleBigQueryDataController::class, 'destroy'])->name('google.bigdata.delete');

        Route::get('instagram/logs', [InstagramPostsController::class, 'instagramUserLogs'])->name('instagram.logs');
        Route::post('instagram/history', [InstagramPostsController::class, 'history'])->name('instagram.accounts.histroy');
        Route::get('instagram/addmailinglist', [HashtagController::class, 'addmailinglist']);

        Route::get('customer/credit/histories/{id?}', [CustomerController::class, 'creditHistory'])->middleware('auth');
        Route::get('test', [WhatsAppController::class, 'getAllMessages']);
        Route::get('display/broken-link-details', [BrokenLinkCheckerController::class, 'displayBrokenLinkDetails'])->name('brokenLinks');

        Route::any('/jobs', [JobController::class, 'index'])->middleware('auth')->name('jobs.list');
        Route::get('/jobs/{id}/delete', [JobController::class, 'delete'])->middleware('auth')->name('jobs.delete');
        Route::post('/jobs/delete-multiple', [JobController::class, 'deleteMultiple'])->middleware('auth')->name('jobs.delete.multiple');
        Route::any('/jobs/alldelete/{id}', [JobController::class, 'alldelete'])->middleware('auth')->name('jobs.alldelete');

        Route::any('/failedjobs', [FailedJobController::class, 'index'])->middleware('auth')->name('failedjobs.list');
        Route::get('/failedjobs/{id}/delete', [FailedJobController::class, 'delete'])->middleware('auth')->name('failedjobs.delete');
        Route::post('/failedjobs/delete-multiple', [FailedJobController::class, 'deleteMultiple'])->middleware('auth')->name('failedjobs.delete.multiple');
        Route::any('/failedjobs/alldelete/{id}', [FailedJobController::class, 'alldelete'])->middleware('auth')->name('failedjobs.alldelete');

        Route::get('/wetransfer-queue', [WeTransferController::class, 'index'])->middleware('auth')->name('wetransfer.list');
        Route::get('/wetransfer/logs', [WeTransferController::class, 'logs'])->middleware('auth')->name('wetransfer.logs');

        Route::get('inventory-update/logs', [InventoryUpdateLogController::class, 'grid'])->name('inventory_update_log.grid.reference');

        Route::post('/wetransfer/re-downloads-files', [WeTransferController::class, 'reDownloadFiles'])->middleware('auth')->name('wetransfer.reDownload.files');

        Route::post('/supplier/manage-scrap-brands', [SupplierController::class, 'manageScrapedBrands'])->middleware('auth')->name('manageScrapedBrands');

        Route::get('/model/name/get', [ModelNameController::class, 'index'])->middleware('auth')->name('get.model.name');
        Route::post('/model/name/store', [ModelNameController::class, 'store'])->middleware('auth')->name('model.name.store');
        Route::delete('/model/name/delete', [ModelNameController::class, 'destroy'])->middleware('auth')->name('model.name.delete');
        Route::post('/model/name/edit', [ModelNameController::class, 'edit'])->middleware('auth')->name('model.name.edit');
        Route::post('/model/name/update', [ModelNameController::class, 'update'])->middleware('auth')->name('model.name.update');

        Route::middleware(['auth', 'role_or_permission:Admin|deployer'])->group(function () {
            Route::prefix('github')->group(function () {
                Route::resource('/organizations', Github\OrganizationController::class);
                Route::post('/github-task/store', [Github\RepositoryController::class, 'githubTaskStore'])->name('github.github-task.store');
                Route::post('/addtoken', [Github\RepositoryController::class, 'githubAddToken'])->name('github.addtoken');
                //Route::post('/addtokenhistory', [Github\RepositoryController::class, 'addGithubTokenHistory'])->name('github.addtokenhistory');
                Route::post('/pull-request-activities/update', [Github\RepositoryController::class, 'pullRequestActivitiesUpdate'])->name('github.pull-request-activities.update');
                Route::post('/repos/job-name-store', [Github\RepositoryController::class, 'jobNameStore'])->name('github.job-name.store');
                Route::post('/repos/sync-repo-labels', [Github\RepositoryController::class, 'syncRepoLabels'])->name('github.sync-repo-labels');
                Route::get('/repos/list-repo-labels', [Github\RepositoryController::class, 'listRepoLabels'])->name('github.list-repo-labels');
                Route::post('/repos/get-repo-data', [Github\RepositoryController::class, 'getRepositoryDara'])->name('github.repo-data');
                Route::post('/repos/update-repo-label-message', [Github\RepositoryController::class, 'updateRepoLabelMessage'])->name('github.update-repo-label-message');
                Route::get('/repos/get-github-jobs', [Github\RepositoryController::class, 'getGithubJobs'])->name('github.get-jobs');
                Route::get('/repos/get-github-actions-jobs', [Github\RepositoryController::class, 'getGithubActionsAndJobs'])->name('github.get-actions-jobs');
                Route::get('repos/{organization_id?}', [Github\RepositoryController::class, 'listRepositories']);
                Route::get('/repos/{id}/users', [Github\UserController::class, 'listUsersOfRepository']);
                Route::get('/repos/{id}/users/add', [Github\UserController::class, 'addUserToRepositoryForm']);
                Route::get('/repos/{id}/branches', [Github\RepositoryController::class, 'getRepositoryDetails']);
                Route::get('/repos/{id}/pull-request', [Github\RepositoryController::class, 'listPullRequests']);
                Route::post('/repos/{id}/pull-request/{pr}/close', [Github\RepositoryController::class, 'closePullRequestFromRepo']);
                Route::get('/repos/{id}/actions', [Github\RepositoryController::class, 'actionWorkflows']);
                Route::get('/repos/{id}/github-actions', [Github\RepositoryController::class, 'ajaxActionWorkflows']);
                Route::get('/repos/{id}/branch/merge', [Github\RepositoryController::class, 'mergeBranch']);
                Route::get('/repos/{id}/deploy', [Github\RepositoryController::class, 'deployBranch']);
                Route::post('/repos/{id}/branch', [Github\RepositoryController::class, 'deleteBranchFromRepo']);
                Route::post('/repos/{id}', [Github\RepositoryController::class, 'deleteNumberOfBranchesFromRepo']);
                Route::post('/repos/{id}/actions/jobs/{jobId}/rerun', [Github\RepositoryController::class, 'rerunGithubAction']);
                Route::post('/add_user_to_repo', [Github\UserController::class, 'addUserToRepository']);
                Route::get('/users', [Github\UserController::class, 'listOrganizationUsers']);
                Route::get('/users/{userId}', [Github\UserController::class, 'userDetails']);
                Route::get('/groups', [Github\GroupController::class, 'listGroups']);
                Route::post('/groups/users/add', [Github\GroupController::class, 'addUser']);
                Route::post('/groups/repositories/add', [Github\GroupController::class, 'addRepository']);
                Route::get('/groups/{groupId}', [Github\GroupController::class, 'groupDetails']);
                Route::get('/groups/{groupId}/repos/{repoId}/remove', [Github\GroupController::class, 'removeRepositoryFromGroup']);
                Route::get('/groups/{groupId}/users/{userId}/organization/{organizationId}/remove', [Github\GroupController::class, 'removeUsersFromGroup']);
                Route::get('/groups/{groupId}/users/add', [Github\GroupController::class, 'addUserForm']);
                Route::get('/groups/{groupId}/repositories/add', [Github\GroupController::class, 'addRepositoryForm']);
                Route::get('/sync', [Github\SyncController::class, 'index']);
                Route::get('/sync/start', [Github\SyncController::class, 'startSync']);
                Route::get('/repo_user_access/{id}/remove', [Github\UserController::class, 'removeUserFromRepository']);
                Route::post('/linkUser', [Github\UserController::class, 'linkUser']);
                Route::post('/modifyUserAccess', [Github\UserController::class, 'modifyUserAccess']);
                Route::get('/pullRequests', [Github\RepositoryController::class, 'listAllPullRequests']);
                Route::get('/pull-request-review-comments/{repoId}/{pullNumber}', [Github\RepositoryController::class, 'getPullRequestReviewComments']);
                Route::get('/pull-request-activities/{repoId}/{pullNumber}', [Github\RepositoryController::class, 'getPullRequestActivities']);
                Route::get('/list-created-tasks', [Github\RepositoryController::class, 'listCreatedTasks']);
                Route::get('/pr-error-logs/{repoId}/{pullNumber}', [Github\RepositoryController::class, 'getPrErrorLogs']);
                Route::get('/gitDeplodError', [Github\RepositoryController::class, 'getGitMigrationErrorLog'])->name('gitDeplodError');
                Route::get('/branches', [Github\RepositoryController::class, 'branchIndex'])->name('github.branchIndex');
                Route::get('/actions', [Github\RepositoryController::class, 'actionIndex'])->name('github.actionIndex');
                Route::get('/repo/status', [Github\RepositoryController::class, 'repoStatusCheck'])->name('github.repoStatusCheck');
                Route::get('/repo/pr-request', [Github\RepositoryController::class, 'getLatestPullRequests'])->name('github.pr.request');
                Route::get('repo-histories/{id}', [Github\RepositoryController::class, 'githubTokenHistory'])->name('github.token.histories');
                Route::post('/repos/{id}/sync', [Github\RepositoryController::class, 'syncRepositories'])->name('github.sync-repositories');
                Route::get('/repos/{organizationId}/branches/{repoId}/sync', [Github\RepositoryController::class, 'syncBranches'])->name('github.sync-branches');
                Route::get('/repos/{id}/pull-request/sync', [Github\RepositoryController::class, 'syncPullRequests'])->name('github.sync-pull-request');
                // Route::get('/repos/{id}/actions/sync', [Github\RepositoryController::class, 'syncActions'])->name('github.sync-actions');
            });
        });

        Route::middleware(['auth', 'role_or_permission:Admin|deployer'])->group(function () {
            Route::get('/deploy-node', [Github\RepositoryController::class, 'deployNodeScrapers']);
        });

        Route::prefix('calendar/public')->group(function () {
            Route::get('/{id}', [UserEventController::class, 'publicCalendar']);
            Route::get('/events/{id}', [UserEventController::class, 'publicEvents']);
            Route::get('/event/suggest-time/{invitationId}', [UserEventController::class, 'suggestInvitationTiming']);
            Route::post('/event/suggest-time/{invitationId}', [UserEventController::class, 'saveSuggestedInvitationTiming']);
        });

        /****Webhook URL for twilio****/
        Route::any('/run-webhook/{sid}', [TwilioController::class, 'runWebhook']);

        Route::any('fetch/customers', [TwillioMessageController::class, 'fetchCustomers']);

        //CommonMailPopup

        // auth not applied
        Route::post('/common/sendEmail', [CommonController::class, 'sendCommonEmail'])->name('common.send.email');
        Route::post('/common/sendclanaderLinkEmail', [CommonController::class, 'sendClanaderLinkEmail'])->name('common.send.clanaderLinkEmail');
        Route::get('/common/getmailtemplate', [CommonController::class, 'getMailTemplate'])->name('common.getmailtemplate');

        //for email templates page
        Route::get('getTemplateProduct', [TemplatesController::class, 'getTemplateProduct'])->middleware('auth')->name('get.template.product');

        Route::post('displayContentModal', [EmailContentHistoryController::class, 'displayModal'])->name('displayContentModal');
        Route::post('add_content', [EmailContentHistoryController::class, 'store'])->name('add_content');

        Route::get('gtmetrix', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'index'])->name('gt-metrix');
        Route::get('gtmetrix-url', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'website_url'])->name('gt-metrix-url');
        Route::post('gtmetrix-url/add', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'add_website_url'])->name('gt-metrix-add-url');
        Route::post('gtmetrix/multi-add-in-process', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'add_website_url'])->name('gt-metrix-multi-process-url');
        Route::post('gtmetrix/deleteurl', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'delete_website_url'])->name('deleteurl');
        Route::post('gtmetrix/run-current-url', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'runCurrentUrl'])->name('run.currunt.url');
        Route::get('gtmetrix/status/{status}', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'saveGTmetrixCronStatus'])->name('gt-metrix.status');
        Route::post('gtmetrix/run-event', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'runErpEvent'])->name('gt-metrix.runEvent');
        Route::post('gtmetrix/multi-run-event', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'MultiRunErpEvent'])->name('gt-metrix.MultiRunEvent');
        Route::get('gtmetrix/history/{id}', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'history'])->name('gtmetrix.history');
        Route::post('gtmetrix/history', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'history'])->name('gtmetrix.hitstory');
        Route::get('gtmetrix/web-history', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'webHistory'])->name('gtmetrix.web-hitstory');
        Route::post('gtmetrix/save-time', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'saveGTmetrixCronType'])->name('saveGTmetrixCronType');
        Route::post('gtmetrix/toggle', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'toggleFlag'])->name('gtmetrix.toggle.flag');
        Route::get('gtmetrix/getpagespeedstats/{type}/{id}', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'getstats'])->name('gtmetrix.getPYstats');
        Route::post('gtmetrix/savegtmetrixcron', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'saveGTmetrixCron']);
        Route::get('gtmetrix/getstatscomparison/{id}', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'getstatsComparison'])->name('gtmetrix.getstatsCmp');
        Route::any('gtmetrix/categories', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'listGTmetrixCategories'])->name('gtmetrix.category.list');
        Route::any('gtmetrix/gtmetrixReport', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'listWebsiteWiseCategories'])->name('gtmetrix.Report.list');
        Route::post('gtmetrix/gtmetrixReportData', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'WebsiteWiseCategoriesReport'])->name('gtmetrix.single.report');
        Route::post('gtmetrix-error-tables', [GTMatrixErrorLogController::class, 'truncateTables'])->name('gtmetrix.error.truncate-tables');
        Route::get('gtmetrix/error-index', [GTMatrixErrorLogController::class, 'index'])->name('gtmetrix.error.index.list');
        Route::any('gtmetrix/error-list', [GTMatrixErrorLogController::class, 'listGTmetrixError'])->name('gtmetrix.error.list');
        Route::get('gtmetrix-accounts', [StoreGTMetrixAccountController::class, 'index'])->name('GtMetrixAccount.index');
        Route::get('gtmetrixAccount/edit-info/{id}', [StoreGTMetrixAccountController::class, 'edit'])->name('account.edit');
        Route::get('gtmetrixAccount/create', [StoreGTMetrixAccountController::class, 'create'])->name('account.create');
        Route::DELETE('gtmetrixAccount/delete/{id?}', [StoreGTMetrixAccountController::class, 'destroy'])->name('gtmetrix.account.destroy');
        Route::get('gtmetrixAccount/show', [StoreGTMetrixAccountController::class, 'show'])->name('account.show');
        Route::post('gtmetrixAccount/update', [StoreGTMetrixAccountController::class, 'update'])->name('account.update');
        Route::post('gtmetrixAccount/store', [StoreGTMetrixAccountController::class, 'store'])->name('gtmetrix.account.store');
        Route::get('gtmetrixcategoryWeb', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'CategoryWiseWebsiteReport'])->name('gtm.cetegory.web');
        Route::post('gtmetrixcategoryWeb-column-visbility', [gtmetrix\WebsiteStoreViewGTMetrixController::class, 'columnVisbilityUpdate'])->name('gtmetrix.column.update');

        Route::get('product-pricing', [product_price\ProductPriceController::class, 'index'])->name('product.pricing');
        Route::get('product-autocomplete', [product_price\ProductPriceController::class, 'getProductAutocomplete'])->name('product.autocomplete');
        Route::post('store-website-product-prices/approve', [product_price\ProductPriceController::class, 'approve']);
        Route::get('store-website-product-prices', [product_price\ProductPriceController::class, 'store_website_product_prices'])->name('store-website-product-prices');
        Route::get('store-website-product-prices/history', [product_price\ProductPriceController::class, 'storewebsiteproductpriceshistory']);
        Route::get('store-website-product-skus', [product_price\ProductPriceController::class, 'store_website_product_skus'])->name('store-website-product-skus');
        Route::get('product-update-logs', [product_price\ProductPriceController::class, 'productUpdateLogs'])->name('product.update.logs');
        Route::post('product-pricing/update-segment', [product_price\ProductPriceController::class, 'update_product'])->name('product.pricing.update.segment');
        Route::post('product-pricing/add_profit', [product_price\ProductPriceController::class, 'update_product'])->name('product.pricing.update.add_profit');
        Route::post('product-pricing/add_duty', [product_price\ProductPriceController::class, 'update_product'])->name('product.pricing.update.add_duty');
        Route::get('product-generic-pricing', [product_price\ProductPriceController::class, 'genericPricing'])->name('product.generic.pricing');
        Route::post('product-duty-price', [product_price\ProductPriceController::class, 'updateProductPrice'])->name('updateDutyPrice');
        Route::post('product-segment-price', [product_price\ProductPriceController::class, 'updateProductPrice'])->name('updateSegmentPrice');
        Route::post('product-update', [product_price\ProductPriceController::class, 'updateProduct'])->name('product_update');

        Route::get('whatsapp-log', [Logging\WhatsappLogsController::class, 'getWhatsappLog'])->name('whatsapp.log');
        Route::get('chatbot-message-log', [ChatbotMessageLogsController::class, 'index'])->name('chatbot.messages.logs');
        Route::get('watson-journey', [LiveChatController::class, 'watsonJourney'])->name('watson.journey');
        Route::get('watson-journey-ajax', [LiveChatController::class, 'ajax'])->name('watson.ajax'); //pawan added for ajax call for filter
        Route::post('pushwaston', [ChatbotMessageLogsController::class, 'pushwaston']);

        Route::get('sync-to-watson', [ChatbotMessageLogsController::class, 'pushQuickRepliesToWaston']);
        Route::get('sync-to-google', [ChatbotMessageLogsController::class, 'pushQuickRepliesToGoogle']);
        Route::post('push-reply-to-watson', [ChatbotMessageLogsController::class, 'pushRepyToWaston']);

        Route::get('chatbot-message-log/{id}/history', [ChatbotMessageLogsController::class, 'chatbotMessageLogHistory'])->name('chatbot.messages.chatbot.message.log.history');

        //Magento Command
        Route::post('magento/command/permission/user', [MagentoCommandController::class, 'userPermission'])->name('magento.command.user.permission');
        Route::get('magento/command', [MagentoCommandController::class, 'index'])->name('magento.command');
        Route::get('magento/magento_command', [MagentoCommandController::class, 'index_command'])->name('magento.magento_command');
        Route::get('magento/get-command', [MagentoCommandController::class, 'getMagentoCommand'])->name('magento.getMagentoCommand');
        Route::get('magento/command/search', [MagentoCommandController::class, 'search'])->name('magento.command.search');
        Route::get('magento/magento_command/searchcron', [MagentoCommandController::class, 'searchcron'])->name('magento.command.searchcron');
        Route::post('magento/command/add', [MagentoCommandController::class, 'store'])->name('magento.command.add');
        Route::post('magento/command/addcommand', [MagentoCommandController::class, 'storecommand'])->name('magento.command.addcommand');
        Route::post('magento/command/run', [MagentoCommandController::class, 'runCommand'])->name('magento.command.run');
        Route::get('magento/command/run-magento-command-logs', [MagentoCommandController::class, 'magentoCommandLogs'])->name('magento.command.magentoCommandLogs');
        Route::post('magento/command/runmagentocommand', [MagentoCommandController::class, 'runMagentoCommand'])->name('magento.command.runmagentocommand');
        Route::post('magento/command/run-on-multiple-website', [MagentoCommandController::class, 'runOnMultipleWebsite'])->name('magento.command.runOnMultipleWebsite');
        Route::post('magento/command/run-mysql-command', [MagentoCommandController::class, 'runMySqlQuery'])->name('magento.command.runMySqlQuery');
        Route::get('magento/command/run-mysql-command-logs', [MagentoCommandController::class, 'mySqlQueryLogs'])->name('magento.command.mySqlQueryLogs');
        Route::post('magento/command/edit', [MagentoCommandController::class, 'edit'])->name('magento.command.edit');
        Route::post('magento/command/editcommand', [MagentoCommandController::class, 'editcommand'])->name('magento.command.editcommand');
        Route::post('magento/command/history', [MagentoCommandController::class, 'commandHistoryLog'])->name('magento.command.history');
        Route::post('magento/cron/history', [MagentoCommandController::class, 'cronHistoryLog'])->name('magento.cron.history');
        Route::delete('magento/command/delete', [MagentoCommandController::class, 'destroy'])->name('magento.command.delete');
        Route::delete('magento/command/deletecommand', [MagentoCommandController::class, 'deletecommand'])->name('magento.command.deletecommand');
        Route::get('/magento/command/run-mulitiple-command-logs', [MagentoCommandController::class, 'getMulitipleCommands'])->name('magento.mulitiple.command.lists');

        // Google Scrapper Keyword
        Route::get('/google-scrapper', [GoogleScrapperController::class, 'index'])->name('google-scrapper.index');
        Route::post('google-scrapper-keyword', [GoogleScrapperController::class, 'saveKeyword'])->name('google-scrapper.keyword.save');
        Route::get('/hubstuff_activity_command', function () {
            \Illuminate\Support\Facades\Artisan::call('HubstuffActivity:Command');
        });

        Route::get('test', [ScrapController::class, 'listCron']);
        Route::get('command', function () {
            \Illuminate\Support\Facades\Artisan::call('create-mailinglist-influencers');
            \Illuminate\Support\Facades\Artisan::call('migrate');
        });

        Route::get('test-cron', function () {
            \Illuminate\Support\Facades\Artisan::call('GT-metrix-test-get-report');
        });

        Route::get('/get/dropdown/list', [GoogleScreencastController::class, 'getDropdownDatas'])->name('getDropdownDatas');

        // Task Summary::
        Route::get('task-summary', [TaskController::class, 'taskSummary'])->name('tasksSummary');
        Route::post('task-list', [TaskController::class, 'taskList'])->name('tasksList');
        Route::get('users-list', [TaskController::class, 'usersList'])->name('usersList');
        Route::get('status-list', [TaskController::class, 'statusList'])->name('statusList');

        Route::get('event-schedule/{userid}/{event_slug}', [CalendarController::class, 'showUserEvent'])->name('guest.schedule-event');
        Route::get('event-schedule-slot', [CalendarController::class, 'getEventScheduleSlots'])->name('guest.schedule-event-slot');
        Route::post('event-schedule-slot', [CalendarController::class, 'createSchedule'])->name('guest.create-schedule');
        Route::get('ip/logs', [IpLogController::class, 'getIPLogs'])->name('get.ip.logs');
        Route::post('/whitelist-ip', [IpLogController::class, 'whitelistIP'])->name('whitelist-ip');
        Route::get('database/backup/lists', [DatabaseBackupMonitoringController::class, 'getDbBackupLists'])->name('get.backup.monitor.lists');
        Route::get('database/backup/error', [DatabaseBackupMonitoringController::class, 'dbErrorShow'])->name('db.error.show');
        Route::get('/update-is-resolved', [DatabaseBackupMonitoringController::class, 'updateIsResolved'])->name('db.update.isResolved');
        Route::post('database/backup/store-status', [DatabaseBackupMonitoringController::class, 'storeDbStatus'])->name('db-store-status');
        Route::post('database/backup//status-update', [DatabaseBackupMonitoringController::class, 'statusDbColorUpdate'])->name('db-backup-color-update');
        Route::post('database/change-status', [DatabaseBackupMonitoringController::class, 'dbUpdateStatus'])->name('db-backup.change.status');

        Route::get('ssh/logins', [SshLoginController::class, 'getSshLogins'])->name('get.ssh.logins');
        Route::get('file/permissions', [FilePermissionController::class, 'getFilePermissions'])->name('get.file.permissions');

        Route::get('/technical-debt', [TechnicalDebtController::class, 'index'])->name('technical-debt-lists');
        Route::post('frame-work/store', [TechnicalDebtController::class, 'frameWorkStore'])->name('frame-work-store');
        Route::post('technical/store', [TechnicalDebtController::class, 'technicalDeptStore'])->name('technical-debt-store');
        Route::get('/technical/debt/remark', [TechnicalDebtController::class, 'technicalDebtGetRemark'])->name('technical-debt-remark');

        // Global column visibility update
        Route::post('globla-column-visbility', [DevelopmentController::class, 'globalColumnVisbilityUpdate'])->name('global.column.update');

        Route::middleware(['auth'])->group(function () {
            Route::get('scrap/development/monitoring', [DevelopmentController::class, 'scrapperMonitoring'])->name('development.scrap.monitoring');
            Route::post('scrap/development/monitoring', [DevelopmentController::class, 'storeScrapperMonitoring'])->name('development.scrap.monitoring.store');
            Route::post('scrap/development/monitoring', [DevelopmentController::class, 'scrapperMonitoring'])->name('development.scrap.monitoring.filter');
            Route::post('scrap/development/monitoring/create', [DevelopmentController::class, 'storeScrapperMonitoring'])->name('development.scrap.monitoring.store');
        });

        Route::middleware('auth')->group(function () {
            Route::prefix('s3-files')->group(function () {
                Route::get('/', [S3Controller::class, 'index'])->name('s3-files.index');
                Route::delete('/', [S3Controller::class, 'destroy'])->name('s3-files.destroy');
                Route::post('/', [S3Controller::class, 'moveToGlacierS3'])->name('s3-files.move-to-glacier');
            });
        });

        Route::get('email-alert-logs', [EmailLogController::class, 'index'])->name('email-alert-log.index');
        Route::delete('email-alert-logs', [EmailLogController::class, 'truncateEmailAlertLogs'])->name('email-alert-log.truncate');

        Route::get('email-logs', [EmailLogController::class, 'index'])->name('email-log.index');

        Route::prefix('email-log')->group(function () {
            Route::get('list', [EmailLogController::class, 'list'])->name('email-log.list');
            Route::post('ajax-list', [EmailLogController::class, 'ajaxList'])->name('email-log.ajax-list');
            Route::post('remove', [EmailLogController::class, 'remove'])->name('email-log.remove');
        });

        Route::get('get-php-info', function () {
            echo phpinfo();
        });

        Route::get('testPriceComparison', [PriceComparisionController::class, 'testPriceComparison'])->name('testPriceComparison');

        Route::get('reverb-test', function () {
            broadcast(new App\Events\ReverbTest);
        });

        Route::prefix('tickets')->group(function () {
            Route::get('/', [TicketController::class, 'index']);
            Route::post('update-price', [TicketController::class, 'updatePrice'])->name('ticket.update.price');
            Route::get('send-notification/{ticket_id}', [TicketController::class, 'sendNotification'])->name('ticket.send.notification');

        });
        // Popup models routes
        Route::controller(PopupModelController::class)->group(function () {
            Route::name('model.')->group(function () {
                Route::get('load-list-code-shortcut-title', 'loadListCodeShortcutTitleModel')->name('loadListCodeShortcutTitleModel');
                Route::get('creat-event-model', 'createEventModel')->name('createEventModel');
                Route::get('vendor-flowchart-header-model', 'vendorFlowChartHeaderModel')->name('vendorFlowChartHeaderModel');
                Route::get('vendor-qa-header-model', 'vendorQAHeaderModel')->name('vendorQAHeaderModel');
                Route::get('vendor-rating-qa-header-model', 'vendorRatingQAHeaderModel')->name('vendorRatingQAHeaderModel');
                Route::get('create-resource-model', 'createResourceModel')->name('createResourceModel');
                Route::get('create-vendor-shortcut-model', 'createVendorShortCutModel')->name('createVendorShortCutModel');
                Route::get('quick-instruction-note-model', 'quickInstructionNoteModel')->name('quickInstructionNoteModel');
                Route::get('keyword-quick-reply-model', 'keywordQuickReplyModel')->name('keywordQuickReplyModel');
                Route::get('create-dev-task-model', 'createDevTaskModel')->name('createDevTaskModel');
                Route::get('create-zoom-meeting-model', 'createZoomMeetingModel')->name('createZoomMeetingModel');
                Route::get('view-all-participants-model', 'viewAllParticipantsModel')->name('viewAllParticipantsModel');
                Route::get('add-voucher-model', 'addvoucherModel')->name('addvoucherModel');
                Route::get('list-documentation-model', 'listDocumentationModel')->name('listDocumentationModel');
                Route::get('create-documentation-model', 'createDocumentationModel')->name('createDocumentationModel');
                Route::get('github-pr-list-model', 'githubPrListModel')->name('githubPrListModel');
                Route::get('database-backup-monitoring-model', 'databaseBackupMonitoringModel')->name('databaseBackupMonitoringModel');
                Route::get('time-doctor-logs-model', 'timeDoctorLogsModel')->name('timeDoctorLogsModel');
                Route::get('zabbix-issue-model', 'zabbixIssueModel')->name('zabbixIssueModel');
                Route::get('live-laravel-logs-model', 'liveLaravelLogsModel')->name('liveLaravelLogsModel');
                Route::get('event-alert-model', 'eventAlertsModel')->name('eventAlertsModel');
                Route::get('search-command-model', 'searchCommandModel')->name('searchCommandModel');
                Route::get('create-event-shortcut-model', 'createEventShortcutModel')->name('createEventShortcutModel');
                Route::get('user-availability-model', 'userAvailabilityModel')->name('userAvailabilityModel');
                Route::get('search-google-doc-model', 'searchGoogleDocModel')->name('searchGoogleDocModel');
                Route::get('create-google-doc-model', 'createGoogleDocModel')->name('createGoogleDocModel');
                Route::get('code-shortcut-model', 'codeShortcutModel')->name('codeShortcutModel');
                Route::get('google-drive-screencast-model', 'googleDriveScreenCastModel')->name('googleDriveScreenCastModel');
                Route::get('upload-screencast-model', 'uploadScreenCastModel')->name('uploadScreenCastModel');
                Route::get('password-create-modal', 'passwordCreateModal')->name('passwordCreateModal');
                Route::get('short-cut-notes-create', 'shortCutNotesCreate')->name('shortCutNotesCreate');
                Route::get('search-password-model', 'searchPasswordModel')->name('searchPasswordModel');
                Route::get('script-document-error-log-model', 'scriptDocumentErrorLogModel')->name('scriptDocumentErrorLogModel');
                Route::get('magento-cron-error-status-model', 'magentoCronErrorStatusModel')->name('magentoCronErrorStatusModel');
                Route::get('jenkins-build-status-model', 'jenkinsBuildStatusModel')->name('jenkinsBuildStatusModel');
                Route::get('monitor-status-model', 'monitorStatusModel')->name('monitorStatusModel');
                Route::get('create-database-model', 'createDatabaseModel')->name('createDatabaseModel');
                Route::get('permission-request-model', 'permissionRequestModel')->name('permissionRequestModel');
                Route::get('task-and-activity-model', 'taskAndActivityModel')->name('taskAndActivityModel');
                Route::get('quick-dev-task-model', 'quickDevTaskModel')->name('quickDevTaskModel');
                Route::get('system-request-model', 'systemRequestModel')->name('systemRequestModel');
                Route::get('quick-appointment-request-model', 'quickAppointmentRequestModel')->name('quickAppointmentRequestModel');

                Route::get('load-contact-model', 'loadContactModel')->name('loadContactModel');
                Route::get('load-task-category-model', 'loadTaskCategoryModel')->name('loadTaskCategoryModel');
                Route::get('load-task-view-model', 'loadTaskViewModel')->name('loadTaskViewModel');
                Route::get('load-whatsapp-group-model', 'loadWhatsappGroupModel')->name('loadWhatsappGroupModel');
                Route::get('load-task-reminder-model', 'loadTaskReminderModel')->name('loadTaskReminderModel');
                Route::get('load-confirm-message-model', 'loadConfirmMessageModel')->name('loadConfirmMessageModel');
                Route::get('load-csv-export-model', 'loadCsvExportModel')->name('loadCsvExportModel');
                Route::get('load-reminder-message-model', 'loadreminderMessageModel')->name('loadreminderMessageModel');
                Route::get('load-task-status-model', 'loadtaskStatusModel')->name('loadtaskStatusModel');
                Route::get('load-tracked-time-history-model', 'loadTrackedTimeHistoryModel')->name('loadTrackedTimeHistoryModel');
                Route::get('load-timer-history-model', 'loadTimerHistoryModel')->name('loadTimerHistoryModel');
                Route::get('load-user-history-model', 'loadUserHistoryModel')->name('loadUserHistoryModel');
                Route::get('load-column-visibility-model', 'loadColumnvisibilityModel')->name('loadColumnvisibilityModel');
                Route::get('load-status-colour-model', 'loadstatusColourModel')->name('loadstatusColourModel');
                Route::get('load-priority-model', 'loadPriorityModel')->name('loadPriorityModel');
                Route::get('load-all-task-category-model', 'loadAllTaskCategoryModel')->name('loadAllTaskCategoryModel');
                Route::get('load-chat-list-history-model', 'loadchatListHistoryModel')->name('loadchatListHistoryModel');
                Route::get('load-create-task-model', 'loadCreateTaskModel')->name('loadCreateTaskModel');
                Route::get('load-preview-task-image-model', 'loadPreviewTaskImageModel')->name('loadPreviewTaskImageModel');
                Route::get('load-load-preview-task-create-model', 'loadPreviewTaskCreateModel')->name('loadPreviewTaskCreateModel');
                Route::get('load-file-upload-area-section-model', 'loadFileUploadAreaSectionModel')->name('loadFileUploadAreaSectionModel');
                Route::get('load-send-message-text-box-model', 'loadSendMessageTextBoxModel')->name('loadSendMessageTextBoxModel');
                Route::get('load-preview-document-model', 'loadPreviewDocumentModel')->name('loadPreviewDocumentModel');
                Route::get('load-recurring-history-model', 'loadRecurringHistoryModel')->name('loadRecurringHistoryModel');
                Route::get('load-task-create-log-listing-model', 'loadTaskCreateLogListingModel')->name('loadTaskCreateLogListingModel');
                Route::get('load-created-task-model', 'loadCreateDTaskModel')->name('loadCreateDTaskModel');
                Route::get('load-task-google-doc-model', 'loadTaskGoogleDocModel')->name('loadTaskGoogleDocModel');
                Route::get('load-task-google-doc-list-model', 'loadTaskGoogleDocListModel')->name('loadTaskGoogleDocListModel');
                Route::get('load-uploade-task-file-model', 'loadUploadeTaskFileModel')->name('loadUploadeTaskFileModel');
                Route::get('load-display-task-file-upload-model', 'loadDisplayTaskFileUploadModel')->name('loadDisplayTaskFileUploadModel');
                Route::get('load-record-voice-notes-model', 'loadRecordVoiceNotesModel')->name('loadRecordVoiceNotesModel');
                Route::get('load-status-quick-history-model', 'loadStatusQuickHistoryModel')->name('loadStatusQuickHistoryModel');
            });
        });
    });
});

Route::get('test-scraper/{url?}', function ($url) {
    // $url = "https://www.farfetch.com/uk/shopping/women/alexander-mcqueen-off-shoulder-knitted-top-item-22325996.aspx";
    // $url = "https://www.farfetch.com/gd/shopping/men/marc-jacobs-the-shoulder-bag-item-18377353.aspx?storeid=11696";
    $url = ! empty($url) ? $url : 'https://www.farfetch.com/uk/shopping/men/balenciaga-speed-lace-up-sneakers-item-14664488.aspx?storeid=9506';
    $data = PriceComparisonScraper::interactWithProductPageForPrice($url, 'uk', 0);
    dd($data);
});
