<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutoReplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'type'         => [
                'required',
                'string',
            ],
'keyword'      => [
                'sometimes',
                'nullable',
                'string',
            ],
'reply'        => [
                'required',
                'min:3',
                'string',
            ],
'sending_time' => [
                'sometimes',
                'nullable',
                'date',
            ],
'repeat'       => [
                'sometimes',
                'nullable',
                'string',
            ],
'is_active'    => [
                'sometimes',
                'nullable',
                'integer',
            ],
];
    }
}
