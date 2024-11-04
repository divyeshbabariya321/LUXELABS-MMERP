<?php

namespace App;
use App\Order;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
