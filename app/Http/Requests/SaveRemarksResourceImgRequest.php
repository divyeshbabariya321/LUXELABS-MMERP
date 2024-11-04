<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRemarksResourceImgRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'resource_images_id' => [
                'required',
            ],
'remarks'            => [
                'required',
            ],
];
    }
}
