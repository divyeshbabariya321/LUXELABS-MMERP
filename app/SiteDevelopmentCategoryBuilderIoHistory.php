<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class SiteDevelopmentCategoryBuilderIoHistory extends Model
{

    protected $fillable = ['site_development_category_id', 'old_value', 'new_value',  'user_id'];

    protected $appends = ['new_value_text', 'old_value_text'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for the new 'old_value_text' attribute
    public function getOldValueTextAttribute()
    {
        if ($this->old_value === 0) {
            return 'No';
        }

        if ($this->old_value === 1) {
            return 'Yes';
        }

        return '-';
    }

    // Accessor for the new 'new_value_text' attribute
    public function getNewValueTextAttribute()
    {
        if ($this->new_value === 0) {
            return 'No';
        }

        if ($this->new_value === 1) {
            return 'Yes';
        }

        return '-';
    }
}
