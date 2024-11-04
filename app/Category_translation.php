<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Category_translation extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="locale",type="string")
     * @SWG\Property(property="title",type="string")
     * @SWG\Property(property="site_id",type="integer")
     * @SWG\Property(property="is_rejected",type="boolen")
     */
    protected $fillable = [
        'category_id',
        'locale',
        'title',
        'site_id',
        'is_rejected',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function site(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'site_id');
    }
}
