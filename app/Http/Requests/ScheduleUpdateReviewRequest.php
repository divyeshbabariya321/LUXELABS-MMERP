<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleUpdateReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'account_id'   => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'customer_id'  => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'date'         => [
                'required',
                'date',
            ],
'posted_date'  => [
                'sometimes',
                'nullable',
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
'review_link'  => [
                'sometimes',
                'nullable',
                'string',
            ],
'status'       => [
                'required',
                'string',
            ],
];
    }
}
