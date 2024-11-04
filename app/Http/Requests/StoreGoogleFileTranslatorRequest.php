<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoogleFileTranslatorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'tolanguage' => [
                'required',
            ],
'file'       => [
                'required',
                'max:10000',
                'mimes:csv,txt',
            ],
];
    }
}
