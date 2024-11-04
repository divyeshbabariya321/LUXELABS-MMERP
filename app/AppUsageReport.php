<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppUsageReport extends Model
{
    protected $table = 'ios_usage_report';

    protected $fillable = ['group_by', 'start_date', 'end_date', 'product_id', 'crashes', 'sessions', 'app_store_views', 'unique_app_store_views', 'daily_active_devices', 'monthly_active_devices', 'paying_users', 'impressions', 'uninstalls', 'unique_impressions', 'avg_daily_active_devices', 'avg_optin_rate', 'storefront', 'store'];

    public $timestamps = false;
}
