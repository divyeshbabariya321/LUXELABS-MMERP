<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\StoreWebsite;
use App\User;


class RejectedImages extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="website_id",type="integer")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="status",type="string")
     */
    protected $fillable = [
        'website_id', 'product_id', 'status', 'user_id',
    ];

    public static function getRejectedMediasFromProductId($product_id)
    {
        return  RejectedImages::join('mediables', 'mediables.mediable_id', 'rejected_images.product_id')->leftJoin('media', 'media.id', 'mediables.media_id')->join('store_websites', 'store_websites.id', '=', 'rejected_images.website_id')->where('product_id', $product_id)->get();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store_website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'website_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
