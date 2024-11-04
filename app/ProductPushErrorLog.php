<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\StoreWebsite;

class ProductPushErrorLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="url",type="string")
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="request_data",type="string")
     * @SWG\Property(property="response_data",type="string")
     * @SWG\Property(property="response_status",type="string")
     * @SWG\Property(property="store_website_id",type="interger")
     */
    protected $fillable = [
        'url',
        'product_id',
        'message',
        'request_data',
        'response_data',
        'response_status',
        'store_website_id',
        'url',
        'condition_id',
    ];

    public static function log($url, $productId, $message, $status = null, $storeWebsiteId = null, $request_data = null, $response_data = null, $logId = null, $conditionId = null)
    {
        // Write to database
        $logListMagento                   = new ProductPushErrorLog();
        $logListMagento->url              = $url;
        $logListMagento->product_id       = $productId;
        $logListMagento->message          = $message;
        $logListMagento->store_website_id = $storeWebsiteId;
        $logListMagento->condition_id     = $conditionId;
        $logListMagento->response_status  = strpos($message, '.php') !== false ? 'php' : $status;
        $logListMagento->request_data     = json_encode($request_data);
        $logListMagento->response_data    = json_encode($response_data);
        if ($logId) {
            $logListMagento->log_list_magento_id = $logId;
        }
        $logListMagento->save();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store_website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }
}
