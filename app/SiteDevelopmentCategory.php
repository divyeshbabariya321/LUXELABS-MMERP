<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class SiteDevelopmentCategory extends Model
{
    /**
     * @var string
     */
    protected $fillable = ['title', 'master_category_id', 'builder_io'];

    public function development(): HasOne
    {
        return $this->hasOne(SiteDevelopment::class, 'site_development_category_id', 'id');
    }

    public function getDevelopment($categoryId, $websiteId, $id = null)
    {
        $development = null;

        if ($id) {
            $development = SiteDevelopment::where('id', $id);
        } else {
            $development = SiteDevelopment::where('website_id', $websiteId)
                ->where('site_development_category_id', $categoryId)
                ->orderByDesc('created_at');
        }

        return $development->first();
    }

    public function masterCategory(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopmentMasterCategory::class, 'master_category_id');
    }
}
