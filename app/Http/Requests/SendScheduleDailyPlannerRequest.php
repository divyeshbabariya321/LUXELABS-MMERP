<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendScheduleDailyPlannerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'date' => [
                'required',
            ],
'user' => [
                'required',
            ],
];
    }
}
