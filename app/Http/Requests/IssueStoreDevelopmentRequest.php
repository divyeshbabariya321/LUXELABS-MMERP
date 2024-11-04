<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueStoreDevelopmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'priority' => [
                'required',
                'integer',
            ],
'issue'    => [
                'required',
                'min:3',
            ],
];
    }
}
