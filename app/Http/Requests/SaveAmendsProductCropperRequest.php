<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveAmendsProductCropperRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'file'       => [
                'required',
            ],
'product_id' => [
                'required',
            ],
'media_id'   => [
                'required',
            ],
'amend_id'   => [
                'required',
            ],
];
    }
}
