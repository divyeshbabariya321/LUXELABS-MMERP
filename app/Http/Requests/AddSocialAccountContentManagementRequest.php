<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSocialAccountContentManagementRequest extends FormRequest
{
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
'url'              => [
                'required',
            ],
'username'         => [
                'required',
            ],
'password'         => [
                'required',
            ],
];
    }
}
