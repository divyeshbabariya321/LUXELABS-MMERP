<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'image_id'     => [
                'required',
            ],
'publish_date' => [
                'required',
            ],
];
    }
}
