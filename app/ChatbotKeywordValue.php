<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotKeywordValue extends Model
{
    public $timestamps = false;

    /**
     * @var string
     *
     * @SWG\Property(property="value",type="string")
     * @SWG\Property(property="types",type="string")
     * @SWG\Property(property="chatbot_keyword_id",type="integer")
     */
    protected $fillable = [
        'value', 'chatbot_keyword_id', 'types',
    ];

    public function chatbotKeywordValueTypes(): HasMany
    {
        return $this->hasMany(ChatbotKeywordValueTypes::class, 'chatbot_keyword_value_id', 'id');
    }
}
