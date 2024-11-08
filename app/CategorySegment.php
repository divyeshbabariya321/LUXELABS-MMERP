<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CategorySegment extends Model
{
    public function categorySegmentDiscount(): HasMany
    {
        return $this->hasMany(CategorySegmentDiscount::class, 'category_segment_id', 'id');
    }

    public function category(): HasMany
    {
        return $this->hasMany(Category::class, 'category_segment_id', 'id');
    }
}
