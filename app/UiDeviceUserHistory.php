<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UiDeviceUserHistory extends Model
{

    protected $fillable = ['user_id', 'uicheck_id', 'ui_device_id', 'old_user_id', 'new_user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function oldUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'old_user_id');
    }

    public function newUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_user_id');
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
