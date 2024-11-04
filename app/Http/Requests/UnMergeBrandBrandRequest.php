<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnMergeBrandBrandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'brand_name'    => [
                'required',
            ],
'from_brand_id' => [
                'required',
            ],
];
    }
}
