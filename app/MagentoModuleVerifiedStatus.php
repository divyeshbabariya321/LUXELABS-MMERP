<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModuleVerifiedStatus extends Model
{
    protected $table = 'magento_module_verified_status';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'color',
    ];
}
