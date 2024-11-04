<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class EditSocialAdRequest extends FormRequest
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
'name'             => [
                'required',
            ],
'email'            => [
                'required',
            ],
'password'         => [
                'required',
            ],
'status'           => [
                'required',
            ],
];
    }
}
