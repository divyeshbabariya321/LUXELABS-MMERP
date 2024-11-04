<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteCategoryUserHistory extends Model
{
    protected $table = 'store_website_category_user_history';

    protected $fillable = [
        'store_id',
        'category_id',
        'user_id',
        'website_action',
    ];
}
