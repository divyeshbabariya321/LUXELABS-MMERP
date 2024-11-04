<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTicketLiveChatRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'id'       => [
                'required',
                'numeric',
            ],
'users_id' => [
                'required',
                'numeric',
            ],
];
    }
}
