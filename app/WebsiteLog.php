<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteLog extends Model
{
    protected $fillable = [
        'id',
        'sql_query',
        'time',
        'module',
        'website_id',
        'type',
        'created_at',
        'updated_at',
        'error',
        'file_path',
    ];
}
