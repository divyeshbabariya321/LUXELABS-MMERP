<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteVersion extends Model
{
    protected $table = 'store_website_version';

    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['store_website_id', 'version', 'build_id'];

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id');
    }
}
