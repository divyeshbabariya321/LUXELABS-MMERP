<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPaymentRequestHubstaffActivityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'amount'    => [
                'required',
            ],
'user_id'   => [
                'required',
            ],
'starts_at' => [
                'required',
            ],
];
    }
}
