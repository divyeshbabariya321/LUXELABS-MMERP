<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTargetLocationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'country' => [
                'required',
            ],
'region'  => [
                'required',
            ],
'lat'     => [
                'required',
            ],
'lng'     => [
                'required',
            ],
];
    }
}
