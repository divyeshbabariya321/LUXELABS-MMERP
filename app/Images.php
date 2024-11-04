<?php

namespace App;
use App\Tag;
use App\Product;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class Images extends Model
{
    use Mediable;
    use SoftDeletes;

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'image_tags', 'image_id', 'tag_id');
    }

    public function saveFromSearchQueues($path, $link, $filename)
    {
        return copy($link, $path.'/'.$filename);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productImg($id, $notId)
    {
        return $this->where('product_id', $id)->whereNotNull('product_id')->orderByDesc('id')->get();
    }

    public function approvedUser()
    {
        return $this->belongsTo(User::class, 'approved_user');
    }
}
