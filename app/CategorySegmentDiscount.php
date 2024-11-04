<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategorySegmentDiscount extends Model
{
    protected $fillable = ['brand_id', 'category_segment_id', 'amount', 'amount_type'];
}
