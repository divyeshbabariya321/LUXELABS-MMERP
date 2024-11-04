<?php

namespace App;
use App\MagentoModule;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoModuleVerifiedStatusHistory extends Model
{
    public function magentoModule(): BelongsTo
    {
        return $this->belongsTo(MagentoModule::class);
    }

    public function newStatus(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleVerifiedStatus::class, 'new_status_id');
    }

    public function oldStatus(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleVerifiedStatus::class, 'old_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
