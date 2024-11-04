<?php

namespace App;
use App\WebsiteStore;
use App\CustomerCharity;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CustomerCharityWebsiteStore extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="contact_no",type="integer")
     * @SWG\Property(property="email",type="string")
     * @SWG\Property(property="whatsapp_number",type="integer")
     * @SWG\Property(property="assign_to",type="string")
     */
    protected $guarded = [];

    public $timestamps = false;

    public function customerCharity(): HasOne
    {
        return $this->hasOne(CustomerCharity::class, 'id', 'customer_charity_id');
    }

    public function websiteStore(): HasOne
    {
        return $this->hasOne(WebsiteStore::class, 'id', 'website_store_id');
    }
}
