<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatBotQuestionReplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'intent_name'  => [
                'required',
            ],
'intent_reply' => [
                'required',
            ],
'question'     => [
                'required',
            ],
];
    }
}
