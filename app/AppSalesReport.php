<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppSalesReport extends Model
{
    protected $table = 'ios_sales_report';

    protected $fillable = ['group_by', 'start_date', 'end_date', 'product_id', 'downloads', 're_downloads', 'uninstalls', 'updates', 'returns', 'net_downloads', 'promos', 'revenue', 'returns_amount', 'edu_downloads', 'gifts', 'gift_redemptions', 'edu_revenue', 'gross_revenue', 'gross_returns_amount', 'gross_edu_revenue', 'business_downloads', 'business_revenue', 'gross_business_revenue', 'standard_downloads', 'standard_revenue', 'gross_standard_revenue', 'app_downloads', 'app_returns', 'iap_amount', 'iap_returns', 'subscription_purchases', 'subscription_returns', 'app_revenue', 'app_returns_amount', 'gross_app_revenue', 'gross_app_returns_amount', 'iap_revenue', 'iap_returns_amount', 'gross_iap_revenue', 'gross_iap_returns_amount', 'subscription_revenue', 'subscription_returns_amount', 'gross_subscription_revenue', 'gross_subscription_returns_amount', 'pre_orders', 'storefront', 'store'];

    public $timestamps = false;
}
