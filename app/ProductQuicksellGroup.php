<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ProductQuicksellGroup extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="quicksell_group_id",type="integer")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="product_quicksell_groups",type="string")
     */

    protected $fillable = ['quicksell_group_id', 'product_id'];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
