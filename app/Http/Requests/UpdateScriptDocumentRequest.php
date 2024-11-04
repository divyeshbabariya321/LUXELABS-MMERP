<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScriptDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'file'            => [
                'required',
                'string',
            ],
'usage_parameter' => [
                'required',
                'string',
            ],
'category'        => [
                'required',
                'string',
            ],
'comments'        => [
                'required',
                'string',
            ],
'author'          => [
                'required',
                'string',
            ],
'description'     => [
                'required',
            ],
'location'        => [
                'required',
            ],
'last_run'        => [
                'required',
            ],
'status'          => [
                'required',
            ],
];
    }
}
