<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GoogleBillingProjects extends Model
{
    /**
     * @var string
     *
     */

    protected $fillable = [
        'google_billing_master_id',
        'project_id',
        'service_type',
        'dataset_id',
        'table_id',
        'description',
        'created_at',
        'updated_at',
    ];

    public function google_billing_master(): BelongsTo{
        return $this->belongsTo(GoogleBillingMaster::class,'google_billing_master_id','id');
    }
}
