<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Category;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="UserUpdatedAttributeHistory"))
 */
class UserUpdatedAttributeHistory extends Model
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
        'old_value', 'new_value', 'attribute_name', 'attribute_id', 'user_id', 'need_to_skip',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getobject()
    {
        if ($this->attribute_name == 'scraped-category') {
            return Category::find($this->new_value);
        }
    }
}
