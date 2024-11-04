<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReferralProgramRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'             => [
                'required',
            ],
'uri'              => [
                'required',
                'exists:store_websites,website',
            ],
'credit'           => [
                'required',
                'integer',
            ],
'currency'         => [
                'required',
                'string',
            ],
'lifetime_minutes' => [
                'integer',
            ],
];
    }
}
