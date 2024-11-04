<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'supplier'           => [
                'required',
                'string',
                'unique:suppliers',
                'max:255',
            ],
'address'            => [
                'sometimes',
                'nullable',
                'string',
            ],
'phone'              => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'default_phone'      => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'whatsapp_number'    => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'email'              => [
                'sometimes',
                'nullable',
                'email',
            ],
'social_handle'      => [
                'sometimes',
                'nullable',
            ],
'scraper_name'       => [
                'sometimes',
                'nullable',
            ],
'product_type'       => [
                'sometimes',
                'nullable',
            ],
'inventory_lifetime' => [
                'sometimes',
                'nullable',
            ],
'gst'                => [
                'sometimes',
                'nullable',
                'max:255',
            ],
];
    }
}
