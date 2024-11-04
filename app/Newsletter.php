<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\NewsletterProduct;
use App\StoreWebsite;
use App\Mailinglist;
use App\MailinglistTemplate;

class Newsletter extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="subject",type="string")
     * @SWG\Property(property="mail_list_id",type="integer")
     * @SWG\Property(property="sent_at",type="datetime")
     * @SWG\Property(property="sent_on",type="string")
     * @SWG\Property(property="updated_by",type="integer")
     * @SWG\Property(property="store_website_id",type="integer")
     */
    protected $fillable = [
        'subject', 'store_website_id', 'sent_at', 'sent_on', 'updated_by', 'mail_list_id', 'mail_list_temp_id', 'approved_by_user_id', 'is_flagged_translation',
    ];

    public function newsletterProduct(): HasMany
    {
        return $this->hasMany(NewsletterProduct::class, 'newsletter_id', 'id');
    }

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'newsletter_products', 'newsletter_id', 'product_id', 'id', 'id');
    }

    public function mailinglist(): HasOne
    {
        return $this->hasOne(Mailinglist::class, 'id', 'mail_list_id');
    }

    public function mailinglistTemplate(): HasOne
    {
        return $this->hasOne(MailinglistTemplate::class, 'id', 'mail_list_temp_id');
    }
}
