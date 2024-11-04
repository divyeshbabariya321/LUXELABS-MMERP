<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class LogStoreWebsiteCategory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="log_case_id",type="string")
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="log_detail",type="string")
     * @SWG\Property(property="log_msg",type="string")
     */
    protected $fillable = [
        'log_case_id',
        'category_id',
        'log_detail',
        'log_msg',
    ];
}
