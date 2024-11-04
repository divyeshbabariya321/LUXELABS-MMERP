<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReconsileBrandsLog extends Model
{
    protected $table = 'reconsile_brands_log';

    protected $fillable = [
        'store_webite_id',
        'user_id',
        'error_type',
        'error',
        'created_at',
    ];
}
