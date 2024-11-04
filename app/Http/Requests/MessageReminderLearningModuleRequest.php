<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageReminderLearningModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'message_id'    => [
                'required',
                'numeric',
            ],
'reminder_date' => [
                'required',
            ],
];
    }
}
