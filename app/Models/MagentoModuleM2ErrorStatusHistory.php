<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleM2ErrorStatusHistory extends Model
{
    use HasFactory;

    public function newM2ErrorStatus(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleM2ErrorStatus::class, 'new_m2_error_status_id');
    }

    public function oldM2ErrorStatus(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleM2ErrorStatus::class, 'old_m2_error_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
