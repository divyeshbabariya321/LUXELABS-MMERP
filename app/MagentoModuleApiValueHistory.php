<?php

namespace App;
use App\MagentoModule;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoModuleApiValueHistory extends Model
{
    public function magentoModule(): BelongsTo
    {
        return $this->belongsTo(MagentoModule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
