<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPushJourney extends Model
{
    protected $table = 'product_push_journey';

    protected $fillable = ['log_list_magento_id', 'product_id', 'condition', 'is_checked'];
}
