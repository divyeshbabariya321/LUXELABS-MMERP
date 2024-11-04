<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class GoogleBillingMaster extends Model
{
    /**
     * @var string
     *
     */
    public $table = 'google_billing_master';

    protected $fillable = [
        'billing_account_name',
        'email',
        'service_file',
        'description',
        'created_at',
        'updated_at',
    ];

    public function google_billing_projects(): HasMany{
        return $this->hasMany(GoogleBillingProjects::class,'google_billing_master_id','id');
    }
}
