<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CategoryCancellationPolicyLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="category_id",type="string")
     * @SWG\Property(property="change_parent_id",type="integer")
     * @SWG\Property(property="day_type",type="integer")
     * @SWG\Property(property="day_change",type="string")
     * @SWG\Property(property="day_old",type="integer")
     * @SWG\Property(property="status",type="integer")
     */
    public $fillable = ['id', 'category_id', 'change_parent_id', 'day_type', 'day_change', 'day_old', 'status'];

    public function user(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
