<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleStoreReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'date'         => [
                'required',
                'date',
            ],
'platform'     => [
                'sometimes',
                'nullable',
                'string',
            ],
'review_count' => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'status'       => [
                'required',
                'string',
            ],
];
    }
}
