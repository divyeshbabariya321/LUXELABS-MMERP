<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class AdStoreSocialConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'             => [
                'required',
            ],
'page_token'       => [
                'required',
            ],
'store_website_id' => [
                'required',
            ],
'ad_account_id'    => [
                'required',
            ],
'status'           => [
                'required',
            ],
'api_secret'       => [
                'required',
            ],
'api_key'          => [
                'required',
            ],
];
    }
}
