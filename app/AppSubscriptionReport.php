<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppSubscriptionReport extends Model
{
    protected $table = 'ios_subscription_report';

    protected $fillable = ['group_by', 'start_date', 'end_date', 'product_id', 'active_subscriptions', 'active_free_trials', 'new_subscriptions', 'cancelled_subscriptions', 'new_trials', 'trial_conversion_rate', 'mrr', 'actual_revenue', 'renewals', 'first_year_subscribers', 'non_first_year_subscribers', 'reactivations', 'transitions_out', 'trial_cancellations', 'transitions_in', 'activations', 'cancellations', 'trial_conversions', 'churn', 'gross_revenue', 'gross_mrr', 'active_grace', 'new_grace', 'grace_drop_off', 'grace_recovery', 'new_trial_grace', 'trial_grace_drop_off', 'trial_grace_recovery', 'active_trials', 'active_discounted_subscriptions', 'all_active_subscriptions', 'paying_subscriptions', 'all_subscribers', 'storefront', 'store'];

    public $timestamps = false;
}
