<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreVendorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'vendor_id'       => [
                'required',
                'numeric',
            ],
'images.*'        => [
                'sometimes',
                'nullable',
                'image',
            ],
'date_of_order'   => [
                'required',
                'date',
            ],
'name'            => [
                'required',
                'string',
                'max:255',
            ],
'qty'             => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'price'           => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'payment_terms'   => [
                'sometimes',
                'nullable',
                'string',
            ],
'recurring_type'  => [
                'required',
                'string',
            ],
'delivery_date'   => [
                'sometimes',
                'nullable',
                'date',
            ],
'received_by'     => [
                'sometimes',
                'nullable',
                'string',
            ],
'approved_by'     => [
                'sometimes',
                'nullable',
                'string',
            ],
'payment_details' => [
                'sometimes',
                'nullable',
                'string',
            ],
];
    }
}
