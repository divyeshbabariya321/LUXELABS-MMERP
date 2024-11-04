<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatGptResponses extends Model
{
    protected $fillable = ['prompt', 'response', 'response_data'];
}
