<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleAdsAccount extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="googleadsaccounts",type="string")
     * @SWG\Property(property="account_name",type="string")
     * @SWG\Property(property="store_websites",type="string")
     * @SWG\Property(property="config_file_path",type="string")
     * @SWG\Property(property="notes",type="string")
     * @SWG\Property(property="status",type="string")
     */
    protected $table = 'googleadsaccounts';

    protected $fillable = [
        'google_customer_id',
        'account_name',
        'store_websites',
        'config_file_path',
        'notes',
        'status',
        'google_adwords_client_account_email',
        'google_adwords_client_account_password',
        'google_adwords_manager_account_customer_id',
        'google_adwords_manager_account_email',
        'google_adwords_manager_account_password',
        'google_adwords_manager_account_developer_token',
        'oauth2_client_id',
        'oauth2_client_secret',
        'oauth2_refresh_token',
        'google_map_api_key',
        'google_merchant_center_account_id',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(GoogleAdsCampaign::class, 'account_id');
    }
}
