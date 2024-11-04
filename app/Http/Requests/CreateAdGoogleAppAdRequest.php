<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdGoogleAppAdRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'headline1'    => [
                'required',
                'max:30',
            ],
'headline2'    => [
                'required',
                'max:30',
            ],
'headline3'    => [
                'required',
                'max:30',
            ],
'description1' => [
                'required',
                'max:90',
            ],
'description2' => [
                'required',
                'max:90',
            ],
'images'       => [
                'nullable',
                'array',
                'max:20',
            ],
'images.*'     => [
                'mimes:jpeg,png,gif',
            ],
];
    }
}
