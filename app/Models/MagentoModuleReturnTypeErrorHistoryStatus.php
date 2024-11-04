<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleReturnTypeErrorHistoryStatus extends Model
{
    use HasFactory;

    protected $table = 'magento_module_return_type_error_status_histories';

    public function newLocation(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleReturnTypeErrorStatus::class, 'new_location_id');
    }

    public function oldLocation(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleReturnTypeErrorStatus::class, 'old_location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
