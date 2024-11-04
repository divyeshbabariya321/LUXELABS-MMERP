<?php

namespace App;
use App\DigitalMarketingSolutionAttribute;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class DigitalMarketingSolution extends Model
{
    /**
     * @SWG\Property(property="digital_marketing_platform_id",type="integer")
     * @SWG\Property(property="provider",type="string")
     * @SWG\Property(property="website",type="string")
     * @SWG\Property(property="contact",type="string")
     */
    public $timestamps = false;

    protected $fillable = [
        'provider',
        'website',
        'contact',
        'digital_marketing_platform_id',
    ];

    public function attributes(): HasMany
    {
        return $this->hasMany(DigitalMarketingSolutionAttribute::class, 'digital_marketing_solution_id', 'id');
    }
}
