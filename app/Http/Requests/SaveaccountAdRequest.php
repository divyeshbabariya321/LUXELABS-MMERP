<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveaccountAdRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'account_name' => [
                'required',
            ],
'config_file'  => [
                'required',
            ],
'status'       => [
                'required',
            ],
];
    }
}
