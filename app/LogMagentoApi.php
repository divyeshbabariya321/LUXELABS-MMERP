<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogMagentoApi extends Model
{
    protected $fillable = ['magento_api_search_product_id', 'api_log', 'message'];
}
