<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Agent extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="model_id",type="integer")
     * @SWG\Property(property="model_type",type="string")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="phone",type="string")
     * @SWG\Property(property="whatsapp_number",type="string")
     * @SWG\Property(property="address",type="text")
     * @SWG\Property(property="email",type="string")
     */
    protected $fillable = [
        'model_id', 'model_type', 'name', 'phone', 'whatsapp_number', 'address', 'email',
    ];

    public function purchase(): HasOne
    {
        return $this->hasOne(Purchase::class);
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'model_id')->where('model_type', Supplier::class);
    }
}
