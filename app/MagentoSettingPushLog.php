<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoSettingPushLog extends Model
{

    protected $fillable = [
        'store_website_id',
        'setting_id',
        'command',
        'command_output',
        'status',
        'job_id',
        'command_server',
    ];

    public function setting(): BelongsTo
    {
        return $this->belongsTo(MagentoSetting::class, 'setting_id');
    }
}
