<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiResponseMessagesTranslationLog extends Model
{
    use HasFactory;

    public function addToLog($api_response_message_id, $text, $type)
    {
        $this->api_response_message_id = $api_response_message_id;
        $this->message                 = $text;
        $this->type                    = $type;
        $this->save();
    }
}
