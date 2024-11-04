<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioWorkspace extends Model
{

    protected $fillable = ['twilio_credential_id', 'workspace_name', 'workspace_sid', 'workspace_response', 'deleted', 'callback_url'];
}
