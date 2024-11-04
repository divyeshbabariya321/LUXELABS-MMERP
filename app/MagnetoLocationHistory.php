<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagnetoLocationHistory extends Model
{
    protected $table = 'magento_module_locations_histories';

    public function newLocation(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleLocation::class, 'old_location_id');
    }

    public function oldLocation(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleLocation::class, 'new_location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
