<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CategoryUpdateUser extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="supplier_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     */
    public $fillable = [
        'supplier_id',
        'user_id',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }
}
