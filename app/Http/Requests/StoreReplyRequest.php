<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'reply'       => [
                'required',
                'string',
            ],
'category_id' => [
                'required',
                'numeric',
            ],
'model'       => [
                'required',
            ],
];
    }
}
