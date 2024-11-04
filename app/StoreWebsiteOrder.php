<?php

namespace App;
use App\StoreWebsiteProductPrice;
use App\StoreWebsite;
use App\MailinglistTemplateCategory;
use App\MailinglistTemplate;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteOrder extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="status_id",type="integer")
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="website_id",type="integer")
     */
    protected $fillable = ['status_id', 'order_id', 'website_id'];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'website_id');
    }

    public function storeWebsiteProductPrice(): HasOne
    {
        return $this->hasOne(StoreWebsiteProductPrice::class, 'store_website_id', 'website_id');
    }

    public function getOrderConfirmationTemplate()
    {
        $category = MailinglistTemplateCategory::where('title', 'Order Confirmation')->first();
        if ($category) {
            // get the template for that cateogry and store website
            return MailinglistTemplate::where('store_website_id', $this->website_id)->where('category_id', $category->id)->first();
        }

        return false;
    }

    public function getOrderStatusChangeTemplate()
    {
        $category = MailinglistTemplateCategory::where('title', 'Order Status Change')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return MailinglistTemplate::where('store_website_id', $this->website_id)->where('category_id', $category->id)->first();
        }

        return false;
    }

    public function getOrderCancellationTemplate()
    {
        $category = MailinglistTemplateCategory::where('title', 'Order Cancellation')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return MailinglistTemplate::where('store_website_id', $this->website_id)->where('category_id', $category->id)->first();
        }

        return false;
    }
}
