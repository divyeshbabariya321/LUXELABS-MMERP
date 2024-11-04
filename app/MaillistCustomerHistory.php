<?php

namespace App;
use App\User;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MaillistCustomerHistory extends Model
{
    /**
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="attribute",type="string")
     * @SWG\Property(property="old_value",type="string")
     * @SWG\Property(property="new_value",type="string")
     * @SWG\Property(property="model",type="string")
     */
    protected $table = 'maillist_customer_history';

    protected $fillable = [
        'user_id', 'customer_id', 'attribute', 'old_value', 'new_value', 'model',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
