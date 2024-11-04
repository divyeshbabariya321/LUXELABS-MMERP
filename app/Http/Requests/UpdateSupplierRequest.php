<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
    //'supplier_category_id'        => 'required|string|max:255',
    'supplier'           => [
                'required',
                'string',
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
    'default_email'      => [
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
