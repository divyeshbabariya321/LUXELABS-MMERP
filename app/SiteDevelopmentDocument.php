<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class SiteDevelopmentDocument extends Model
{
    use Mediable;

    /**
     * @var string
     *
     * @SWG\Property(property="site_development_id",type="integer")
     * @SWG\Property(property="site_development_category_id",type="integer")
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="subject",type="string")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="created_by",type="integer")
     * @SWG\Property(property="created_at",type="datetime")
     */
    protected $fillable = ['site_development_id', 'site_development_category_id', 'store_website_id', 'subject', 'description', 'created_by', 'created_at'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
