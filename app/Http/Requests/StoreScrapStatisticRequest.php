<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScrapStatisticRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'supplier' => [
                'required',
            ],
'type'     => [
                'required',
            ],
'url'      => [
                'required',
            ],
];
    }
}
