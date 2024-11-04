<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GTMetrixCategories extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gt_metrix_categories';

    protected $fillable = [
        'name',
        'source',
        'created_at',
        'updated_at',
    ];
}
