<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UiResponsivestatusHistory extends Model
{

    protected $fillable = ['id', 'user_id', 'uicheck_id', 'device_no', 'ui_device_id', 'status', 'old_status', 'created_at'];

    public function stausColor(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopmentStatus::class, 'status', 'id');
    }
}
