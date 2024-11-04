<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreColdLeadBroadcastRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'            => [
                'required',
            ],
'number_of_users' => [
                'required',
            ],
'frequency'       => [
                'required',
            ],
'message'         => [
                'required',
            ],
'started_at'      => [
                'required',
            ],
'status'          => [
                'required',
            ],
];
    }
}
