<?php

namespace App;
use App\Order;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="invoice_number",type="integer")
     * @SWG\Property(property="invoice_date",type="datetime")
     */
    protected $fillable = [
        'invoice_number',
        'invoice_date',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
