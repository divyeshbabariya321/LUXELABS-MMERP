<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UiDeviceBuilderIoData extends Model
{
    protected $table = 'ui_device_builder_io_datas';

    protected $fillable = [
        'id',
        'uicheck_id',
        'ui_device_id',
        'title',
        'html',
        'builder_created_date',
        'builder_last_updated',
        'builder_created_by',
        'builder_last_updated_by',
        'status_id',
        'task_id',
    ];

    // Define custom accessors
    public function getBuilderCreatedDateAttribute($value)
    {
        if ($value) {
            return $this->formatTimestamp($value);
        }

        return null;
    }

    // Helper function to format timestamps
    protected function formatTimestamp($timestamp)
    {
        if ($timestamp) {
            $timestampInSeconds = $timestamp / 1000; // Convert milliseconds to seconds

            return Carbon::createFromTimestamp($timestampInSeconds)->format('Y-m-d H:i:s');
        }

        return null;
    }

    public function uiDevice(): BelongsTo
    {
        return $this->belongsTo(UiDevice::class);
    }

    public function UiBuilderStatusColour(): BelongsTo
    {
        return $this->belongsTo(UiDeviceBuilderIoDataStatus::class, 'status_id');
    }
}
