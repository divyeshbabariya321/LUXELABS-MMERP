<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class TargetLocation extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="region_data",type="string")
     */
    protected $casts = [
        'region_data' => 'array',
    ];

    public function people(): HasMany
    {
        return $this->hasMany(InstagramUsersList::class, 'location_id', 'id');
    }
}
