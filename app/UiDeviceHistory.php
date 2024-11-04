<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UiDeviceHistory extends Model
{

    protected $fillable = ['id', 'user_id', 'ui_devices_id', 'uicheck_id',  'device_no', 'message', 'status', 'estimated_time', 'expected_start_time', 'expected_completion_time', 'is_estimated_time_approved', 'created_at'];

    public function stausColor(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopmentStatus::class, 'status', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uicheck(): BelongsTo
    {
        return $this->belongsTo(Uicheck::class);
    }

    public function uiDevice(): BelongsTo
    {
        return $this->belongsTo(UiDevice::class, 'ui_devices_id');
    }
}
