<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ListingHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="content",type="integer")
     */
    protected $casts = [
        'content' => 'array',
    ];

    public static function createNewListing($userId = null, $productId = null, $content = [], $action = null)
    {
        // Create new activity for listing history
        $listingHistory             = new ListingHistory();
        $listingHistory->user_id    = $userId;
        $listingHistory->product_id = $productId;
        $listingHistory->content    = $content;
        $listingHistory->action     = $action;

        return $listingHistory->save();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
