<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'review'        => [
                'required',
                'string',
            ],
'posted_date'   => [
                'sometimes',
                'nullable',
                'date',
            ],
'review_link'   => [
                'sometimes',
                'nullable',
                'string',
            ],
'serial_number' => [
                'sometimes',
                'nullable',
                'string',
            ],
'platform'      => [
                'sometimes',
                'nullable',
                'string',
            ],
'account_id'    => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'customer_id'   => [
                'sometimes',
                'nullable',
                'numeric',
            ],
];
    }
}
