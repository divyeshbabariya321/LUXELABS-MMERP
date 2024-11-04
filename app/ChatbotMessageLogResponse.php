<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessageLogResponse extends Model
{
    protected $fillable = [
        'chatbot_message_log_id', 'request', 'response', 'status',
    ];

    public function chatbotMessageLog(): BelongsTo
    {
        return $this->belongsTo(ChatbotMessageLog::class, 'chatbot_message_log_id', 'id');
    }

    public static function StoreLogResponse($params)
    {
        $chat_message_log_response = new self;
        $chat_message_log_response->chatbot_message_log_id = $params['chatbot_message_log_id'];
        $chat_message_log_response->request = $params['request'];
        $chat_message_log_response->response = $params['response'];
        $chat_message_log_response->status = $params['status'];
        $chat_message_log_response->save();

        return $chat_message_log_response->id;
    }
}
