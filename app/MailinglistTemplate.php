<?php

namespace App;
use App\MailinglistTemplateCategory;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class MailinglistTemplate extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="mail_class",type="string")
     * @SWG\Property(property="mail_tpl",type="string")
     * @SWG\Property(property="image_count",type="integer")
     * @SWG\Property(property="text_count",type="integer")
     * @SWG\Property(property="example_image",type="string")
     * @SWG\Property(property="subject",type="string")
     * @SWG\Property(property="static_template",type="string")
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="store_website_id",type="integer")
     */
    protected $fillable = ['name', 'mail_class', 'mail_tpl', 'image_count', 'text_count', 'example_image', 'subject', 'static_template', 'category_id', 'store_website_id', 'from_email', 'auto_send', 'duration', 'html_text'];

    public function file(): HasMany
    {
        return $this->hasMany(MailingTemplateFile::class, 'mailing_id', 'id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(MailinglistTemplateCategory::class, 'id', 'category_id');
    }

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public static function getIssueCredit($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Issue Credit')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getOrderConfirmationTemplate($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Order Confirmation')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getTicketCreateTemplate($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Ticket Create')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getOrderStatusChangeTemplate($order_status, $store = null)
    {
        $category = MailinglistTemplateCategory::where('title', $order_status)->first();

        if ($category) {
            return self::getTemplate($category, $store);
        } else {
            $category_default = MailinglistTemplateCategory::where('title', 'Order Status Change')->first();

            return self::getTemplate($category_default, $store);
        }
    }

    public static function getOrderDeliveryDateChangeTemplate($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Order Delivery Date Change')->first();

        return self::getTemplate($category, $store);
    }

    public static function getMailTemplate($order_status, $store = null)
    {
        $category = MailinglistTemplateCategory::where('title', $order_status)->first();

        if ($category) {
            return self::getTemplate($category, $store);
        } else {
            $category_default = MailinglistTemplateCategory::where('title', 'Ticket ACK')->first();

            return self::getTemplate($category_default, $store);
        }
    }

    public static function getOrderCancellationTemplate($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Order Cancellation')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getIntializeReturn($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Initialize Return')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getIntializeRefund($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Initialize Refund')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getIntializeExchange($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Initialize Exchange')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getIntializeCancellation($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Initialize Cancellation')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getNewsletterTemplate($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Newsletter')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getTemplate($category, $store = null)
    {
        if ($store) {
            return self::where('store_website_id', $store)->where('category_id', $category->id)->first();
        } else {
            return self::where(function ($q) {
                $q->whereNull('store_website_id')->orWhere('store_website_id', '=', '')->orWhere('store_website_id', '<=', 0);
            })->where('category_id', $category->id)->first();
        }
    }

    public static function getStatusChangeReturn($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Status Change Return')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getStatusChangeExchange($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Status Change Exchange')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function template($name, $store = null)
    {
        $category = MailinglistTemplateCategory::where('title', $name)->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getBotEmailTemplate($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'botMail')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getReferAFirendTemplate($store = null)
    {
        $category = MailinglistTemplateCategory::where('title', 'Refer_a_frined')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }
}
