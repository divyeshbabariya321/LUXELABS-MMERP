<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteProductCsv extends Model
{
    protected $fillable = ['store_website_id', 'path'];
}
