<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAWBDHLOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'pickup_time'                        => [
                'required',
            ],
'currency'                           => [
                'required',
            ],
'box_length'                         => [
                'required',
            ],
'box_width'                          => [
                'required',
            ],
'box_height'                         => [
                'required',
            ],
'notes'                              => [
                'required',
            ],
'customer_name'                      => [
                'required',
            ],
'customer_city'                      => [
                'required',
            ],
'customer_country'                   => [
                'required',
            ],
'customer_phone'                     => [
                'required',
            ],
'customer_address1'                  => [
                'required',
                'max:45',
            ],
'customer_pincode'                   => [
                'required',
            ],
'items'                              => [
                'required',
            ],
'items.*.name'                       => [
                'required',
            ],
'items.*.qty'                        => [
                'required',
                'numeric',
            ],
'items.*.unit_price'                 => [
                'required',
            ],
'items.*.net_weight'                 => [
                'required',
            ],
'items.*.gross_weight'               => [
                'required',
            ],
'items.*.manufacturing_country_code' => [
                'required',
            ],
'items.*.hs_code'                    => [
                'required',
            ],
'description'                        => [
                'required',
            ],
];
    }
}
