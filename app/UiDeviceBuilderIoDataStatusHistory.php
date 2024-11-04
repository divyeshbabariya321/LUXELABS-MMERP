<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UiDeviceBuilderIoDataStatusHistory extends Model
{

    protected $fillable = [
        'user_id',
        'ui_device_builder_io_data_id',
        'old_status_id',
        'new_status_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newStatus(): BelongsTo
    {
        return $this->belongsTo(UiDeviceBuilderIoDataStatus::class, 'new_status_id');
    }

    public function oldStatus(): BelongsTo
    {
        return $this->belongsTo(UiDeviceBuilderIoDataStatus::class, 'old_status_id');
    }
}
