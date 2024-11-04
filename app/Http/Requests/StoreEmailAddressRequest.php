<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailAddressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'from_name'       => [
                'required',
                'string',
                'max:255',
            ],
'from_address'    => [
                'required',
                'string',
                'max:255',
            ],
'incoming_driver' => [
                'required',
                'string',
                'max:255',
            ],
'driver'          => [
                'required',
                'string',
                'max:255',
            ],
'host'            => [
                'required',
                'string',
                'max:255',
            ],
'port'            => [
                'required',
                'string',
                'max:255',
            ],
'encryption'      => [
                'required',
                'string',
                'max:255',
            ],
'username'        => [
                'required',
                'string',
                'max:255',
            ],
'password'        => [
                'required',
                'string',
                'max:255',
            ],
];
    }
}
