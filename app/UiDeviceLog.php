<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UiDeviceLog extends Model
{

    protected $fillable = ['user_id', 'uicheck_id', 'ui_device_id', 'start_time', 'end_time'];

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
        return $this->belongsTo(UiDevice::class);
    }
}
