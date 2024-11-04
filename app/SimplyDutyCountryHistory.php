<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class SimplyDutyCountryHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="country_code",type="string")
     * @SWG\Property(property="country_name",type="string")
     * @SWG\Property(property="default_duty",type="string")
     */
    protected $fillable = ['simple_duty_country_id', 'old_segment', 'new_segment', 'old_duty', 'new_duty'];

    public static function getSelectList()
    {
        return self::pluck('country_name', 'country_code')->toArray();
    }
}
