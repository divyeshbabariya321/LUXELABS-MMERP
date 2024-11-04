<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSizeChartBrandSizeChartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'category_id' => [
                'required',
            ],
'size_img'    => [
                'required',
                'mimes:jpeg,jpg,png',
            ],
];
    }
}
