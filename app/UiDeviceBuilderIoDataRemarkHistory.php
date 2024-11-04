<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UiDeviceBuilderIoDataRemarkHistory extends Model
{

    protected $fillable = [
        'user_id',
        'ui_device_builder_io_data_id',
        'remarks',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uiDeviceBuilderIoData(): BelongsTo
    {
        return $this->belongsTo(UiDeviceBuilderIoData::class);
    }
}
