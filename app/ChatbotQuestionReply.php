<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotQuestionReply extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="suggested_reply",type="string")
     * @SWG\Property(property="chatbot_questions_reply",type="string")
     */
    protected $fillable = ['suggested_reply'];

    public $table = 'chatbot_questions_reply';

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id');
    }
}
