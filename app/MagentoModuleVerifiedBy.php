<?php

namespace App;
use App\MagentoModule;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoModuleVerifiedBy extends Model
{
    protected $table = 'magento_module_verified_by_histories';

    public function magentoModule(): BelongsTo
    {
        return $this->belongsTo(MagentoModule::class);
    }

    public function newVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(user::class, 'new_verified_by_id');
    }

    public function oldVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(user::class, 'old_verified_by_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
