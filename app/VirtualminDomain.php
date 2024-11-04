<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualminDomain extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'identifier_id',
        'is_enabled',
        'start_date',
        'expiry_date',
        'rocket_loader',
    ];

    protected $appends = ['is_enabled_text'];

    public function getIsEnabledTextAttribute()
    {
        $isEnabledText = '';
        if ($this->is_enabled === 1) {
            $isEnabledText = 'Enabled';
        } else {
            $isEnabledText = 'Disabled';
        }

        return $isEnabledText;
    }
}
