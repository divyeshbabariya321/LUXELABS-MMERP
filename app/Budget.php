<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Budget extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="amount",type="integer")
     * @SWG\Property(property="type",type="string")
     * @SWG\Property(property="budget_category_id",type="integer")
     * @SWG\Property(property="budget_subcategory_id",type="integer")
     */
    protected $fillable = [
        'description', 'date', 'amount', 'type', 'budget_category_id', 'budget_subcategory_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BudgetCategory::class, 'budget_category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(BudgetCategory::class, 'budget_subcategory_id');
    }
}
