<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppPaymentReport extends Model
{
    protected $table = 'ios_payments_report';

    protected $fillable = ['group_by', 'start_date', 'end_date', 'product_id', 'revenue', 'converted_revenue', 'financial_revenue', 'estimated_revenue', 'storefront', 'store'];

    public $timestamps = false;
}
