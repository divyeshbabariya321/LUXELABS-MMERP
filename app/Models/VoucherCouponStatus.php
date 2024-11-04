<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherCouponStatus extends Model
{
    use HasFactory;

    public $fillable = [
        'status_name',
        'postman_color',
    ];
}
