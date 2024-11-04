<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdGoogleAdRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'headlinePart1' => [
                'required',
                'max:25',
            ],
'headlinePart2' => [
                'required',
                'max:25',
            ],
'headlinePart3' => [
                'required',
                'max:25',
            ],
'description1'  => [
                'required',
                'max:200',
            ],
'description2'  => [
                'required',
                'max:200',
            ],
'finalUrl'      => [
                'required',
                'max:200',
            ],
];
    }
}
