<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoogleAdsAccountGoogleAdsAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'google_customer_id'                             => [
                'required',
                'integer',
            ],
'account_name'                                   => [
                'required',
            ],
'store_websites'                                 => [
                'required',
            ],
'status'                                         => [
                'required',
            ],
'google_adwords_client_account_email'            => [
                'required',
                'email',
            ],
'google_adwords_client_account_password'         => [
                'required',
            ],
'google_adwords_manager_account_customer_id'     => [
                'required',
                'integer',
            ],
'google_adwords_manager_account_developer_token' => [
                'required',
            ],
'google_adwords_manager_account_email'           => [
                'required',
                'email',
            ],
'google_adwords_manager_account_password'        => [
                'required',
            ],
'oauth2_client_id'                               => [
                'required',
            ],
'oauth2_client_secret'                           => [
                'required',
            ],
'oauth2_refresh_token'                           => [
                'required',
            ],
'google_map_api_key'                             => [
                'required',
            ],
'google_merchant_center_account_id'              => [
                'required',
                'integer',
            ],
];
    }
}
