<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailResendPurchaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'purchase_id' => [
                'required',
                'numeric',
            ],
'email_id'    => [
                'required',
                'numeric',
            ],
'recipient'   => [
                'required',
                'email',
            ],
];
    }
}
