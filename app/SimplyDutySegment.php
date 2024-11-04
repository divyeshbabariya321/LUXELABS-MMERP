<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class SimplyDutySegment extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="country_code",type="string")
     * @SWG\Property(property="country_name",type="string")
     * @SWG\Property(property="default_duty",type="string")
     */
    protected $fillable = ['segment', 'price'];
}
