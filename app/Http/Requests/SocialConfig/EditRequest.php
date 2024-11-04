<?php

namespace App\Http\Requests\SocialConfig;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
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
        // Add account ID to rules to fix account ID not getting stored on update. DEVTASK-24765
        return [
            'store_website_id' => [
                'required',
                'exists:store_websites,id',
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
                'required',
            ],
            'page_token'       => [
                'required',
            ],
            'webhook_token'    => [
                'required',
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
            'account_id'       => [
                'required',
            ], 
        ];
    }
}
