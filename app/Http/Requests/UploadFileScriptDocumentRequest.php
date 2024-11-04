<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileScriptDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'images'             => [
                'required',
            ],
'file_creation_date' => [
                'required',
            ],
'remarks'            => [
                'sometimes',
            ],
'script_document_id' => [
                'required',
            ],
];
    }
}
