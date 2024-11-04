<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AnalyticsSummary extends Model
{
    /**
     * Fillables for the database
     *
     *
     * @var array
     */
    /**
     * @var string
     *
     * @SWG\Property(property="brand_name",type="string")
     * @SWG\Property(property="gender",type="string")
     * @SWG\Property(property="time",type="time")
     * @SWG\Property(property="country",type="string")
     * @SWG\Property(property="city",type="string")
     */
    protected $fillable = [
        'brand_name', 'gender', 'time',
        'country', 'city',
    ];
}
