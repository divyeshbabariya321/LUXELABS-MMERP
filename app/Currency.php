<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="code",type="string")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="rate",type="float")
     */
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'code';

    protected $fillable = [
        'code',
        'name',
        'rate',
    ];

    public static function convert($price, $to, $from = 'EUR')
    {
        // 1000 / 73.14
        if ($to == $from) {
            return $price;
        }

        $euroPrice = 0;
        if ($from != 'EUR') {
            $rate = self::where('code', $from)->first();
            if ($rate) {
                $euroPrice = number_format(($price / $rate->rate), 2, '.', '');
            }
        } else {
            $euroPrice = $price;
        }

        if ($to == 'EUR') {
            return $euroPrice;
        } else {
            $rate = self::where('code', $to)->first();
            if ($rate) {
                return number_format(($price * $rate->rate), 2, '.', '');
            }
        }

        return 0.00;
    }
}
