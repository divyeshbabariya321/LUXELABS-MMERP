<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\ProductQuicksellGroup;

class QuickSellGroup extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="group",type="string")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="suppliers",type="string")
     * @SWG\Property(property="brands",type="string")
     * @SWG\Property(property="price",type="float")
     * @SWG\Property(property="special_price",type="float")
     */
    protected $fillable = ['group', 'name', 'suppliers', 'brands', 'price', 'special_price'];

    public function getProductsIds(): HasMany
    {
        return $this->hasMany(ProductQuicksellGroup::class, 'quicksell_group_id', 'group');
    }
}
