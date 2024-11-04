<?php

namespace App\Loggers;
use App\StoreWebsiteProductScreenshot;
use App\StoreWebsite;
use App\ProductPushJourney;
use App\Product;
use App\Loggers\LogListMagentoSyncStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogListMagento extends Model
{
    protected $fillable = [
        'product_id',
        'queue',
        'queue_id',
        'size_chart_url',
        'extra_attributes',
        'message',
        'created_at',
        'updated_at',
        'magento_status',
        'store_website_id',
        'sync_status',
        'languages',
        'user_id',
        'tried',
        'total_request_assigned',
    ];

    public static function log($productId, $message, $severity = 'info', $storeWebsiteId = null, $syncStatus = null, $languages = null)
    {
        // Write to log file
        Log::channel('listMagento')->$severity($message);

        // Write to database
        $logListMagento = new LogListMagento();
        $logListMagento->product_id = $productId;
        $logListMagento->message = $message;
        $logListMagento->store_website_id = $storeWebsiteId;
        $logListMagento->sync_status = $syncStatus;
        $logListMagento->languages = $languages;
        $logListMagento->user_id = @Auth::user()->id;
        $logListMagento->save();

        // Return
        return $logListMagento;
    }

    public static function updateMagentoStatus($id, $status)
    {
        return self::where('id', $id)->update([
            'magento_status' => $status,
        ]);
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function screenshot()
    {
        return StoreWebsiteProductScreenshot::where('product_id', $this->product_id)->where('store_website_id', $this->store_website_id)->get();
    }

    public function logListMagentoSyncStatus()
    {
        return LogListMagentoSyncStatus::where('name', $this->sync_status)->first();
    }

    public function getProductPushJourneyContitions()
    {
        return ProductPushJourney::where('log_list_magento_id', $this->id)->pluck('condition')->toArray();
    }
}
