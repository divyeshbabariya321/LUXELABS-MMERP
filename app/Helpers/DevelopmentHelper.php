<?php

namespace App\Helpers;
use App\Uicheck;
use App\UiLanguage;
use App\UiDevice;
use App\TimeDoctor\TimeDoctorTaskEfficiency;
use App\TimeDoctor\TimeDoctorActivity;
use App\Task;
use App\StoreWebsiteProductAttribute;
use App\StoreWebsite;
use App\SiteCroppedImages;
use App\Setting;
use App\ScrapedProducts;
use App\Product;
use App\Models\ProductListingFinalStatus;
use App\Models\EmailStatus;
use App\Mediables;
use App\Loggers\LogListMagento;
use App\LogScraperVsAi;
use App\ListingPayments;
use App\Hubstaff\HubstaffActivity;
use App\HubstaffTaskEfficiency;
use App\Helpers;
use App\FlowPath;
use App\FlowMessage;
use App\FlowAction;
use App\DeveloperTaskHistory;
use App\Customer;
use App\CroppedImageReference;
use App\ChatMessage;
use App\CategorySegmentDiscount;
use App\Category;
use App\Brand;

use App\AutoRefreshPage;
use App\DeveloperTask;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DevelopmentHelper
{
    public static function getDeveloperTasks($developerId, $status, $task_type)
    {
        // Get open tasks for developer
        $developerTasks = DeveloperTask::where('user_id', $developerId)
            ->join('task_types', 'task_types.id', '=', 'developer_tasks.task_type_id')
            ->select('*', 'developer_tasks.id as task_id')
            ->where('parent_id', '=', '0')
            ->where('status', $status)
            ->where('task_type_id', $task_type)
            ->orderBy('priority')
            ->orderBy('subject')
            ->get();

        // Return developer tasks
        return $developerTasks;
    }

    public static function scrapTypes()
    {
        return [
            '1' => 'Typescript',
            '2' => 'NodeJS Request/Cheerio',
            '3' => 'NodeJS Puppeteer',
            '4' => 'NodeJS Puppeteer with URL list',
            '5' => 'NodeJS Puppeteer Luminati with URL list',
            '6' => 'Py Scraper',
        ];
    }

    public static function scrapTypeById($id)
    {
        if (! empty($id)) {
            return isset(self::scrapTypes()[$id]) ? self::scrapTypes()[$id] : '';
        }

        return '';
    }

    public static function needToApproveMessage()
    {
        $approveMessage = 0;

        $approvalmodel = Setting::where('name', 'is_approve_message_btn')->first();
        if ($approvalmodel) {
            $approveMessage = $approvalmodel->val;
        }

        return $approveMessage;
    }

    public static function getAutoRefreshPage($path)
    {
        $userId = Auth::id();

        return AutoRefreshPage::where(['page' => $path, 'user_id' => $userId])->first();
    }

    public static function approvedUser($img)
    {
        return User::find($img->approved_user)->name;
    }

    public static function trackedTimeHubstaffActivity($taskid)
    {
        return HubstaffActivity::where('task_id', $taskid)->sum('tracked');
    }

    public static function timeHistoryDeveloperTaskHistory($devTaskId)
    {
        return DeveloperTaskHistory::where('developer_task_id', $devTaskId)->where('attribute', 'estimation_minute')->where('is_approved', 1)->first();
    }

    public static function getFlowMessage($flowActionId)
    {
        return FlowMessage::where('action_id', $flowActionId)->first();
    }

    public static function getFlowPath($flowActionId, $pathFor = 'yes')
    {
        return FlowPath::where('parent_action_id', $flowActionId)->where('path_for', $pathFor)->pluck('id')->first();
    }

    public static function getActionData($pathId)
    {
        return FlowAction::leftJoin('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
            ->select('flow_actions.*', 'flow_types.type')->where(['path_id' => $pathId])->orderBy('rank')
            ->get();
    }

    public static function getDeveloperTask($developerTaskId)
    {
        return DeveloperTask::where('id', $developerTaskId)->first();
    }

    public static function getDeveloperTaskHistory($developerTaskId)
    {
        return DeveloperTaskHistory::where('developer_task_id', $developerTaskId)->where('attribute', 'estimation_minute')->where('is_approved', 1)->first();
    }

    public static function getEfficiency($memberUserId, $recordOnDate, $recordOnHour)
    {
        return HubstaffTaskEfficiency::where('user_id', $memberUserId)->where('date', $recordOnDate)->where('time', $recordOnHour)->first();
    }

    public static function getStatusColor($emailStatus)
    {
        return EmailStatus::where('id', $emailStatus)->first();
    }

    public static function getFirstUiDeviceByUicheckAndUserId($uiCheckId, $userID)
    {
        return UiDevice::where('device_no', 1)->where('uicheck_id', $uiCheckId)->where('user_id', $userID)->first();
    }

    public static function getFirstUiLanguageByUicheckId($uiCheckId)
    {
        return UiLanguage::where('languages_id', 2)->where('uicheck_id', $uiCheckId)->first();
    }

    public static function getFirstUicheckBySiteDevelopmentCatId($siteDevelopmentCatId)
    {
        return Uicheck::where('site_development_category_id', $siteDevelopmentCatId)->first();
    }

    public static function getTimeDoctorActivitySumByTaskId($taskId)
    {
        return TimeDoctorActivity::where('task_id', $taskId)->sum('tracked');
    }

    public static function getFirstDeveloperTaskHistoryByDevTaskID($devTaskId)
    {
        return DeveloperTaskHistory::where('developer_task_id', $devTaskId)->where('attribute', 'estimation_minute')->where('is_approved', 1)->first();
    }

    public static function getDeveloperTaskById($id)
    {
        return DeveloperTask::where('id', $id)->first();
    }

    public static function getTimeDoctorTaskEfficiencyByUserIdAndDateTime($userId, $date, $time)
    {
        return TimeDoctorTaskEfficiency::where('user_id', $userId)->where('date', $date)->where('time', $time)->first();
    }

    public static function getFirstDeveloperTaskHistoryByDevTaskIDAndModel($devTaskId)
    {
        return DeveloperTaskHistory::where('developer_task_id', $devTaskId)->where('attribute', 'estimation_minute')->where('model', Task::class)->first();
    }

    public static function getTaskById($id)
    {
        return Task::where('tasks.id', $id)->select('tasks.*', DB::raw('(SELECT remark FROM developer_tasks_history WHERE developer_task_id=tasks.id ORDER BY id DESC LIMIT 1) as task_remark'), DB::raw('(SELECT new_value FROM task_history_for_start_date WHERE task_id=tasks.id ORDER BY id DESC LIMIT 1) as task_start_date'), DB::raw("(SELECT new_due_date FROM task_due_date_history_logs WHERE task_id=tasks.id AND task_type='TASK' ORDER BY id DESC LIMIT 1) as task_new_due_date"))->first();
    }

    public static function getTaskHistoryByTaskID($taskID)
    {
        return Task::getDeveloperTasksHistory($taskID);
    }

    public static function getCustomer($id)
    {
        return Customer::find($id);
    }

    public static function getChatMessage($id)
    {
        return ChatMessage::find($id);
    }

    public static function getMediables($id)
    {
        return Mediables::where('media_id', $id)->where('mediable_type', Product::class)->first();
    }

    public static function getCategorySegmentDiscounts($key, $id)
    {
        return CategorySegmentDiscount::where('brand_id', $key)->where('category_segment_id', $id)->first();
    }

    public static function getCroppedImageReference($id)
    {
        return CroppedImageReference::where('new_media_id', $id)->first();
    }

    public static function getScrapedProducts($sku)
    {
        return ScrapedProducts::select('description', 'website')->where('sku', $sku)->get();
    }

    public static function getValidatedScrapedProducts($sku)
    {
        return ScrapedProducts::where('sku', $sku)->where('validated', 1)->get();
    }

    public static function getSiteCroppedImageAnyCropExists($id)
    {
        return SiteCroppedImages::where('product_id', $id)->first();
    }

    public static function getSiteCroppedImage($productId, $siteId)
    {
        return SiteCroppedImages::where('product_id', $productId)->where('website_id', $siteId)->first();
    }

    public static function getStoreWebsiteProductAttribute($productId)
    {
        return StoreWebsiteProductAttribute::join('store_websites', 'store_websites.id', 'store_website_product_attributes.store_website_id')->where('product_id', $productId)->select('store_website_product_attributes.description', 'store_websites.title')->get();
    }

    public static function getStoreWebsite()
    {
        return StoreWebsite::pluck('website', 'id')->toArray();
    }

    public static function getBrandSegmentPrice($brand_segment, $category)
    {
        return Brand::getSegmentPrice($brand_segment, $category);
    }

    public static function getCategoryPathById($product_category_id)
    {
        return Category::getCategoryPathById($product_category_id);
    }

    public static function getCroppingGridImageByCategoryId($product_category)
    {
        return Category::getCroppingGridImageByCategoryId($product_category);
    }

    public static function getScrapedProductsWhereSku($sku)
    {
        return ScrapedProducts::select('description', 'website', 'validated', 'url', 'last_inventory_at')->where('sku', $sku)->get();
    }

    public static function getProductListingFinalStatus($status)
    {
        return ProductListingFinalStatus::where('status_name', $status)->first();
    }

    public static function getCategoryIdByKeyword($category, $gender, $additionalParam = null)
    {
        return LogScraperVsAi::getCategoryIdByKeyword($category, $gender, $additionalParam);
    }

    public static function getUserName($product_user_id)
    {
        return User::find($product_user_id)->name;
    }

    public static function getLogScraperVsAiWhere($product_id)
    {
        return LogScraperVsAi::where('product_id', $product_id)->get();
    }

    public static function getListingPayments($user_id, $date)
    {
        return ListingPayments::where('user_id', $user_id)->where('paid_at', $date)->get();
    }

    public static function getLogListMagento($product_id, $product_sw_id)
    {
        return LogListMagento::select('id', 'magento_status')->where('product_id', $product_id)->where('store_website_id', $product_sw_id)->orderBy('id', 'desc')->first();
    }

    public static function getUserListingRates($userId)
    {
        $user = User::find($userId);

        return [
            'listing_approval_rate' => $user->listing_approval_rate,
            'listing_rejection_rate' => $user->listing_rejection_rate,
        ];
    }
}
