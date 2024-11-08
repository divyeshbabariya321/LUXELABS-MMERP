<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class OldCategory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="category",type="string")
     */
    protected $fillable = ['category'];

    public function old(): BelongsTo
    {
        return $this->belongsTo(Old::class, 'id', 'category_id');
    }
}
