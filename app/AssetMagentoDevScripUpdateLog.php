<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetMagentoDevScripUpdateLog extends Model
{

    protected $fillable = [
        'asset_manager_id',
        'user_id',
        'ip',
        'command_name',
        'response',
        'site_folder',
        'error',
    ];
}
