<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AutoCompleteMessage extends Model
{
    protected $fillable = [
        'id', 'message', 'created_at', 'updated_at',
    ];
}
