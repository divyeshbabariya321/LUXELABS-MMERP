<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailEvent extends Model
{
    protected $fillable = ['id', 'list_contact_id', 'template_id', 'sent', 'delivered', 'opened', 'spam', 'spam_date', 'created_at', 'updated_at'];
}
