<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReplyLog extends Model
{
    use HasFactory;

    public function addToLog($reply_id, $text, $type)
    {
        $this->reply_id = $reply_id;
        $this->message  = $text;
        $this->type     = $type;
        $this->save();
    }
}
