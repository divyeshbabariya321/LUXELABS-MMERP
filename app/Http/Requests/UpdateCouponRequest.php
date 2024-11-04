<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'code'                 => [
                'required',
            ],
'description'          => [
                'required',
            ],
'start'                => [
                'required',
                'date_format:Y-m-d H:i',
            ],
'expiration'           => [
                'sometimes',
                'nullable',
                'date_format:Y-m-d H:i',
                'after:start',
            ],
'discount_fixed'       => [
                'nullable',
                'numeric',
            ],
'discount_percentage'  => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'minimum_order_amount' => [
                'sometimes',
                'nullable',
                'integer',
            ],
'maximum_usage'        => [
                'sometimes',
                'nullable',
                'integer',
            ],
];
    }
}
