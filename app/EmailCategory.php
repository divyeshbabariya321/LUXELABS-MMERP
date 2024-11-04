<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EmailCategoryHistory;
use Illuminate\Database\Eloquent\Model;

class EmailCategory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="email_category",type="string")
     * @SWG\Property(property="category_name",type="string")
     */
    protected $table = 'email_category';

    protected $fillable = ['category_name'];

    public function categoryHistory(): HasMany
    {
        return $this->hasMany(EmailCategoryHistory::class, 'category_id');
    }
}
