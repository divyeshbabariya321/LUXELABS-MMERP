<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;

class StoreWebsiteTwilioNumber extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="store_website_twilio_numbers",type="string")
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="twilio_active_number_id",type="integer")
     * @SWG\Property(property="message_available",type="string")
     * @SWG\Property(property="message_not_available",type="string")
     * @SWG\Property(property="message_busy",type="string")
     */

    protected $fillable = [
        'store_website_id', 'twilio_active_number_id', 'message_available', 'message_not_available', 'message_busy', 'twilio_credentials_id', 'end_work_message',
        'greeting_message', 'category_menu_message', 'sub_category_menu_message', 'speech_response_not_available_message',
    ];

    public function store_website(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }
}
