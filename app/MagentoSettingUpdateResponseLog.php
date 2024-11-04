<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoSettingUpdateResponseLog extends Model
{
    protected $fillable = [
        'website_id',
        'magento_setting_id',
        'response',
    ];
}
