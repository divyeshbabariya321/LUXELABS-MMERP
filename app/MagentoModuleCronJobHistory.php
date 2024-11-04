<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoModuleCronJobHistory extends Model
{

    protected $fillable = ['magento_module_id', 'cron_time', 'frequency', 'cpu_memory', 'comments', 'user_id'];

    public function magento_module(): BelongsTo
    {
        return $this->belongsTo(MagentoModule::class, 'magento_module_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
