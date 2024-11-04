<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdSocialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'          => [
                'required',
            ],
'adset_id'      => [
                'required',
            ],
'adcreative_id' => [
                'required',
            ],
'status'        => [
                'required',
            ],
];
    }
}
