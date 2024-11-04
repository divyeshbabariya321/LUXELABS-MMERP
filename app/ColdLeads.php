<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ColdLeads extends Model
{
    public function threads(): HasOne
    {
        return $this->hasOne(InstagramThread::class, 'cold_lead_id', 'id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function whatsapp(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'platform_id', 'phone');
    }
}
