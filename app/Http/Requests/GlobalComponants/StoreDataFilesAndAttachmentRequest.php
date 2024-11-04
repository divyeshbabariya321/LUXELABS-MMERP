<?php

namespace App\Http\Requests\GlobalComponants;

use Illuminate\Foundation\Http\FormRequest;

class StoreDataFilesAndAttachmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'title'    => [
                'required',
            ],
'filename' => [
                'required',
            ],
];
    }
}
