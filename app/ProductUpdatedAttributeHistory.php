<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="ProductUpdatedAttributeHistory"))
 */
class ProductUpdatedAttributeHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="old_value",type="string")
     * @SWG\Property(property="new_value",type="string")
     * @SWG\Property(property="attribute_name",type="string")
     * @SWG\Property(property="attribute_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     */
    protected $fillable = [
        'old_value', 'new_value', 'attribute_name', 'attribute_id', 'product_id', 'user_id',
    ];

    public function old_category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'old_value');
    }

    public function new_category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'new_value');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
