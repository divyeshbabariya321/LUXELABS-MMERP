<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class LogStoreWebsiteUser extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="username",type="string")
     * @SWG\Property(property="useremail",type="string")
     * @SWG\Property(property="password",type="string")
     * @SWG\Property(property="first_name",type="string")
     * @SWG\Property(property="last_name",type="string")
     * @SWG\Property(property="website_mode",type="string")
     * @SWG\Property(property="log_msg",type="string")
     */
    protected $fillable = [
        'store_website_id',
        'useremail',
        'username',
        'password',
        'first_name',
        'last_name',
        'website_mode',
        'log_msg',
    ];
}
