<?php

namespace App;
use App\User;
use App\Remark;
use App\InstructionCategory;
use App\Customer;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="start_time",type="datetime")
     * @SWG\Property(property="end_time",type="datetime")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="instruction",type="string")
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="assigned_to",type="integer")
     * @SWG\Property(property="assigned_from",type="datetime")
     */
    protected $fillable = ['start_time', 'end_time', 'customer_id', 'product_id', 'order_id', 'instruction', 'category_id', 'assigned_to', 'assigned_from'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InstructionCategory::class);
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(Remark::class, 'taskid')->where('module_type', 'instruction')->latest();
    }

    public function assingTo(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    public function assignFrom(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'assigned_from');
    }
}
