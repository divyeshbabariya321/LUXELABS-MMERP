<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UiDevice extends Model
{

    protected $fillable = ['id', 'user_id', 'uicheck_id', 'device_no', 'languages_id', 'message', 'status', 'is_approved', 'estimated_time', 'expected_start_time', 'expected_completion_time', 'created_at'];

    public function uichecks(): BelongsTo
    {
        return $this->belongsTo(Uicheck::class, 'uicheck_id', 'id');
    }

    public function lastUpdatedHistory(): HasOne
    {
        return $this->hasOne(UiDeviceHistory::class, 'ui_devices_id', 'id')->orderByDesc('updated_at');
    }

    public function lastUpdatedStatusHistory(): HasOne
    {
        return $this->hasOne(UiResponsivestatusHistory::class, 'ui_device_id', 'id')->orderByDesc('id');
    }

    public function uiDeviceHistories(): HasMany
    {
        return $this->hasMany(UiDeviceHistory::class, 'ui_devices_id');
    }

    public function stausColor(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopmentStatus::class, 'status', 'id');
    }
}
