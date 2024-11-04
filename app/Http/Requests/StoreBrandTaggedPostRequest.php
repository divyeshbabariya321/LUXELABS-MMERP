<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandTaggedPostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'account_id' => [
                'required',
            ],
'receipts'   => [
                'required',
                'array',
            ],
];
    }
}
