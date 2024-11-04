<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    /**
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['name'];

    public function documents(): BelongsTo
    {
        return $this->belongsTo(Documents::class, 'id', 'category_id');
    }
}
