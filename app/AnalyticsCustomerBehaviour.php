<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AnalyticsCustomerBehaviour extends Model
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
     * @SWG\Property(property="pages",type="string")
     * @SWG\Property(property="pageviews",type="string")
     * @SWG\Property(property="uniquePageviews",type="string")
     * @SWG\Property(property="avgTimeOnPage",type="string")
     * @SWG\Property(property="entrances",type="string")
     * @SWG\Property(property="bounceRate",type="string")
     * @SWG\Property(property="exitRate",type="string")
     * @SWG\Property(property="pageValue",type="string")
     */
    protected $fillable = [
        'pages', 'pageviews', 'uniquePageviews',
        'avgTimeOnPage', 'entrances', 'bounceRate',
        'exitRate', 'pageValue', '',
    ];
}
