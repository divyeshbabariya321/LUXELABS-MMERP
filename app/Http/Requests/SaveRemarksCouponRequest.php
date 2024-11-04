<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRemarksCouponRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'coupon_code_rules_id' => [
                'required',
            ],
'remarks'              => [
                'required',
            ],
];
    }
}
