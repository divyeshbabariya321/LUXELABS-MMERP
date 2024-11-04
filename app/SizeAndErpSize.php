<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SizeAndErpSize extends Model
{
    protected $table = 'size_erp_sizes';

    protected $fillable = [
        'size', 'system_size_id', 'erp_size_id',
    ];
}
