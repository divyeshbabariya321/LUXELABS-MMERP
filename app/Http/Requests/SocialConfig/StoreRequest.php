<?php

namespace App\Http\Requests\SocialConfig;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'store_website_id' => [
                'required',
            ],
            'platform'         => [
                'required',
            ],
            'name'             => [
                'required',
            ],
            'status'           => [
                'required',
            ],
            'page_id'          => [
                'nullable',
            ],
            'account_id'       => [
                'nullable',
            ],
            'page_token'       => [
                'nullable',
            ],
            'webhook_token'    => [
                'nullable',
            ],
            'ad_account_id'    => [
                'nullable',
                'exists:social_ad_accounts,id',
            ],
            'api_secret'       => [
                'required',
            ],
            'api_key'          => [
                'required',
            ],
            'user_name'        => [
                'nullable',
            ],
            'phone_number'     => [
                'nullable',
            ],
            'password'         => [
                'nullable',
            ],
        ];
    }
}
