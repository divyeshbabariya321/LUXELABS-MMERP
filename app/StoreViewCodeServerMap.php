<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class StoreViewCodeServerMap extends Model
{
    protected $table = 'store_view_code_server_map';

    protected $fillable = [
        'id',
        'code',
        'server_id',
    ];
}
