<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'         => [
                'required',
                'min:3',
                'max:255',
            ],
'email'        => [
                'required_without_all:phone,instahandler',
                'nullable',
                'email',
            ],
'phone'        => [
                'required_without_all:email,instahandler',
                'nullable',
                'numeric',
                'digits:12',
                'unique:customers',
            ],
'instahandler' => [
                'required_without_all:email,phone',
                'nullable',
                'min:3',
                'max:255',
            ],
'rating'       => [
                'required',
                'numeric',
            ],
'address'      => [
                'sometimes',
                'nullable',
                'min:3',
                'max:255',
            ],
'city'         => [
                'sometimes',
                'nullable',
                'min:3',
                'max:255',
            ],
'country'      => [
                'sometimes',
                'nullable',
                'min:2',
                'max:255',
            ],
'pincode'      => [
                'sometimes',
                'nullable',
                'max:6',
            ],
];
    }
}
