<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkWhatsappDubbizleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'group'   => [
                'required',
                'string',
            ],
'message' => [
                'required',
                'string',
            ],
];
    }
}
