<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstructionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'instruction' => [
                'required',
                'min:3',
            ],
'customer_id' => [
                'required',
                'numeric',
            ],
'assigned_to' => [
                'required',
                'numeric',
            ],
];
    }
}
