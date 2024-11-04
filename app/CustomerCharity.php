<?php

namespace App;
use App\CustomerCharityWebsiteStore;
use App\ChatMessage;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CustomerCharity extends Model
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

    public function whatsappAll($needBroadCast = false): HasMany
    {
        if ($needBroadCast) {
            return $this->hasMany(ChatMessage::class, 'charity_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(ChatMessage::class, 'charity_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function storeWebsites(): HasMany
    {
        return $this->hasMany(CustomerCharityWebsiteStore::class, 'customer_charity_id', 'id');
    }
}
