<?php

namespace App;
use App\StoreWebsite;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CustomerReview extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="email",type="string")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="platform_id",type="integer")
     * @SWG\Property(property="stars",type="string")
     * @SWG\Property(property="comment",type="string")
     * @SWG\Property(property="status",type="integer")
     */
    use SoftDeletes;

    use Mediable;

    protected $fillable = [
        'email', 'name', 'store_website_id', 'platform_id', 'stars', 'comment', 'status',
    ];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }
}
