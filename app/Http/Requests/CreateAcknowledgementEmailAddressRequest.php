<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAcknowledgementEmailAddressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'start_date'  => [
                'required',
            ],
'end_date'    => [
                'required',
            ],
'ack_status'  => [
                'required',
            ],
'ack_message' => [
                'required',
            ],
];
    }
}
