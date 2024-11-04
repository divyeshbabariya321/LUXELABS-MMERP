<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ReplyCategory;

class Reply extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="reply",type="string")
     * @SWG\Property(property="model",type="string")
     * @SWG\Property(property="deleted_at",type="datetime")
     */
    use SoftDeletes;

    protected $fillable = ['category_id', 'store_website_id', 'reply', 'model', 'push_to_watson', 'pushed_to_google'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ReplyCategory::class, 'category_id');
    }
}
