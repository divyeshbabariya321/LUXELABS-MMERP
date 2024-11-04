<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditSitejabberQARequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'range'  => [
                'required',
            ],
'range2' => [
                'required',
            ],
'range3' => [
                'required',
            ],
];
    }
}
