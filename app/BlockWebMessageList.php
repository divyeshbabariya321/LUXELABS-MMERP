<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BlockWebMessageList extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="object_id",type="integer")
     * @SWG\Property(property="object_type",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = [
        'object_id', 'object_type', 'created_at', 'updated_at',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function has_posted_reviews()
    {
        $count = $this->hasMany(Review::class)->where('status', 'posted')->count();

        return $count > 0;
    }
}
