<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStatusOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'order_status_id'        => [
                'required',
            ],
'store_website_id'       => [
                'required',
            ],
'store_master_status_id' => [
                'required',
            ],
];
    }
}
