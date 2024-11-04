<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetsManagerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'          => [
                'required',
            ],
'link'          => [
                'required',
            ],
'asset_type'    => [
                'required',
            ],
'start_date'    => [
                'required',
            ],
'category_id'   => [
                'required',
            ],
'purchase_type' => [
                'required',
            ],
'payment_cycle' => [
                'required',
            ],
'amount'        => [
                'required',
            ],
];
    }
}
