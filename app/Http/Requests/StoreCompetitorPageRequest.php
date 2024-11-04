<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitorPageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'     => [
                'required',
            ],
'username' => [
                'required',
            ],
'platform' => [
                'required',
            ],
];
    }
}
