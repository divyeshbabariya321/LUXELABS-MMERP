<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteProductCheck extends Model
{
    protected $fillable = ['website_id',
        'website',
        'sku',
        'size',
        'brands',
        'dimensions',
        'composition',
        'english'  => 'Yes',
        'arabic'   => 'Yes',
        'german'   => 'Yes',
        'spanish'  => 'No',
        'french'   => 'No',
        'italian'  => 'No',
        'japanese' => 'No',
        'korean'   => 'No',
        'russian'  => 'No',
        'chinese'  => 'No',
    ];
}
