<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteEnvironment extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['store_website_id', 'env_data', 'path', 'value', 'command', 'created_by'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'env_data' => 'array',
        ];
    }

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }

    public function latestStoreWebsiteEnvironmentHistory(): HasOne
    {
        return $this->hasOne(StoreWebsiteEnvironmentHistory::class, 'environment_id')->latest();
    }
}
