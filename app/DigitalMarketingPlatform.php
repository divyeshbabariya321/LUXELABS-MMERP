<?php

namespace App;
use App\DigitalMarketingSolution;
use App\DigitalMarketingPlatformComponent;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class DigitalMarketingPlatform extends Model
{
    /**
     * @SWG\Property(property="platform",type="string")
     * @SWG\Property(property="sub_platform",type="string")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    const STATUS = [
        0 => 'Draft',
        1 => 'Active',
        2 => 'Inactive',
        3 => 'Planned',
        4 => 'Do not need',
    ];

    protected $fillable = [
        'platform',
        'sub_platform',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    public function solutions(): HasMany
    {
        return $this->hasMany(DigitalMarketingSolution::class, 'digital_marketing_platform_id', 'id');
    }

    public function components(): HasMany
    {
        return $this->hasMany(DigitalMarketingPlatformComponent::class, 'digital_marketing_platform_id', 'id');
    }
}
