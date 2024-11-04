<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'body'       => [
                'required',
            ],
'moduleid'   => [
                'required',
            ],
'moduletype' => [
                'required',
            ],
'status'     => [
                'required',
            ],
];
    }
}
