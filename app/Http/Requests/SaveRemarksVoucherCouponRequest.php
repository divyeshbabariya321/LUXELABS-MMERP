<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRemarksVoucherCouponRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'voucher_coupons_id' => [
                'required',
            ],
'remark'             => [
                'required',
            ],
];
    }
}
