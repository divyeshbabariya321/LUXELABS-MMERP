<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotDialogErrorLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="response",type="string")
     */
    protected $fillable = ['status', 'response', 'reply_id', 'request'];

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }

    public function chatbot_dialog(): BelongsTo
    {
        return $this->belongsTo(ChatbotDialog::class);
    }
}
