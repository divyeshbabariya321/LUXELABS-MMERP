<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class HsCodeGroup extends Model
{
    public function hsCode(): HasOne
    {
        return $this->hasOne(HsCode::class, 'id', 'hs_code_id');
    }

    public function groupComposition(): HasMany
    {
        return $this->hasMany(HsCodeGroupsCategoriesComposition::class, 'hs_code_group_id', 'id');
    }
}
