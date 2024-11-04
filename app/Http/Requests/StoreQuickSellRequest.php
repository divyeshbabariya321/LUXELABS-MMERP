<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuickSellRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'sku'      => [
                'required',
                'unique:products',
            ],
'images.*' => [
                'required ',
                ' mimes:jpeg,bmp,png,jpg',
            ],
];
    }
}
