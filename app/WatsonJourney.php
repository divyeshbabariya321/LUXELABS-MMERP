<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatsonJourney extends Model
{
    protected $table = 'watson_journey';

    protected $fillable = ['chatbot_message_log_id', 'question_id', 'question_created', 'question_example_created', 'question_reply_inserted', 'question_pushed', 'dialog_inserted', 'response'];
}
