<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SopHasCategory;
use Illuminate\Database\Eloquent\Model;
use App\Email;

class Sop extends Model
{

    protected $fillable = ['name', 'content', 'user_id'];

    protected $appends = ['selected_category_ids'];

    public function purchaseProductOrderLogs(): HasOne
    {
        return $this->hasOne(PurchaseProductOrderLog::class, 'purchase_product_order_id', 'id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'model_id', 'id');
    }

    public function sopCategory(): BelongsToMany
    {
        return $this->belongsToMany(SopCategory::class, 'sop_has_categories', 'sop_id', 'sop_category_id');
    }

    public function hasSopCategory(): HasMany
    {
        return $this->hasMany(SopHasCategory::class, 'sop_id', 'id');
    }

    /**
     * Model accrssor and mutator
     */
    public function getSelectedCategoryIdsAttribute()
    {
        return $this->sopCategory()->pluck('sops_category.id')->toArray();
    }
}
