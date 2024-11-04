<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'sales_person_name' => [
                'required',
            ],
'allocated_to'      => [
                'required',
            ],
'description'       => [
                'required',
            ],
'image'             => [
                'mimes:jpeg,bmp,png,jpg',
            ],
];
    }
}
