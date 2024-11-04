<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoModuleJsRequireHistory extends Model
{

    protected $fillable = ['magento_module_id', 'files_include', 'native_functionality', 'user_id'];

    public function magento_module(): BelongsTo
    {
        return $this->belongsTo(MagentoModule::class, 'magento_module_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
