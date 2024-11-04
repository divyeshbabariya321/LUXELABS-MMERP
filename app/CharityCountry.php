<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CharityCountry extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="contact_no",type="integer")
     * @SWG\Property(property="email",type="string")
     * @SWG\Property(property="whatsapp_number",type="integer")
     * @SWG\Property(property="assign_to",type="string")
     */
    protected $guarded = [];
}
