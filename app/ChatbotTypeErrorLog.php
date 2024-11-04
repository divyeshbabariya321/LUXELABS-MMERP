<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotTypeErrorLog extends Model
{
    use Mediable;

    /**
     * @var string
     *
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="chatbot_id",type="integer")
     * @SWG\Property(property="phone_number",type="string")
     * @SWG\Property(property="type_error",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = ['id', 'store_website_id', 'call_sid', 'phone_number', 'type_error', 'is_active', 'created_at', 'updated_at'];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'store_website_id', 'id');
    }

    public function chatbotQuestionName(): HasOne
    {
        return $this->hasOne(ChatbotQuestion::class, 'chatbot_id', 'id');
    }
}
