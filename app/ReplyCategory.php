<?php

namespace App;
use App\Reply;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReplyCategory extends Model
{
    public $fillable = ['name', 'parent_id', 'pushed_to_watson', 'dialog_id', 'intent_id', 'push_to_google'];

    public function approval_leads(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Approval Lead')->orderBy('reply');
    }

    public function sub_categories(): HasMany
    {
        return $this->hasMany(ReplyCategory::class, 'parent_id')->select('id', 'parent_id', 'name')->orderBy('name');
    }

    public function internal_leads(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Internal Lead');
    }

    public function approval_orders(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Approval Order');
    }

    public function internal_orders(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Internal Order');
    }

    public function approval_purchases(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Approval Purchase');
    }

    public function internal_purchases(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Internal Purchase');
    }

    public function product_dispatch(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Product Dispatch');
    }

    public function vendor(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Vendor');
    }

    public function supplier(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id')->where('model', 'Supplier');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(ReplyCategory::class, 'id', 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ReplyCategory::class, 'parent_id', 'id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class, 'category_id');
    }

    public function parentList()
    {
        $parent = $this->parent;
        $arr = [];
        if ($parent) {
            $arr[] = $parent->name;
            $parent = $parent->parent;
            if ($parent) {
                $arr[] = $parent->name;
                $parent = $parent->parent;
                if ($parent) {
                    $arr[] = $parent->name;
                    $parent = $parent->parent;
                }
            }
        }

        return implode(' > ', array_reverse($arr));
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(ReplyCategory::class, 'parent_id');
    }

    public static function getParentCategoriesWithLeadsAndSubCategories()
    {
        return self::select('id', 'name')
            ->with(['approval_leads', 'sub_categories'])
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();
    }
}
