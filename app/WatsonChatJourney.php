<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatsonChatJourney extends Model
{
    protected $table = 'watson_chat_journey';

    protected $fillable = ['chat_id', 'chat_entered', 'message_received', 'reply_found_in_database', 'reply_searched_in_watson', 'reply', 'response_sent_to_cusomer'];
}
