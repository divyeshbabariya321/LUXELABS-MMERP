<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerCharityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'category_id'          => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'name'                 => [
                'required',
                'string',
                'max:255',
            ],
'address'              => [
                'sometimes',
                'nullable',
                'string',
            ],
'phone'                => [
                'required',
                'nullable',
                'numeric',
            ],
'email'                => [
                'sometimes',
                'nullable',
                'email',
            ],
'social_handle'        => [
                'sometimes',
                'nullable',
            ],
'website'              => [
                'sometimes',
                'nullable',
            ],
'login'                => [
                'sometimes',
                'nullable',
            ],
'password'             => [
                'sometimes',
                'nullable',
            ],
'gst'                  => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'account_name'         => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'account_iban'         => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'account_swift'        => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'frequency_of_payment' => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'bank_name'            => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'bank_address'         => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'city'                 => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'country'              => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'ifsc_code'            => [
                'sometimes',
                'nullable',
                'max:255',
            ],
'remark'               => [
                'sometimes',
                'nullable',
                'max:255',
            ],
];
    }
}
