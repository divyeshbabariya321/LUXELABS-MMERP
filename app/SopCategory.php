<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class SopCategory extends Model
{
    protected $table = 'sops_category';

    protected $fillable = ['category_name'];

    public function sop(): BelongsToMany
    {
        return $this->belongsToMany(Sop::class, 'sop_has_categories', 'sop_category_id', 'sop_id');
    }
}
