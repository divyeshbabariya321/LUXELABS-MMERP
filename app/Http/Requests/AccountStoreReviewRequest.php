<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountStoreReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'first_name'      => [
                'sometimes',
                'nullable',
                'string',
            ],
'last_name'       => [
                'sometimes',
                'nullable',
                'string',
            ],
'email'           => [
                'sometimes',
                'nullable',
                'email',
            ],
'password'        => [
                'required',
                'min:3',
            ],
'dob'             => [
                'sometimes',
                'nullable',
                'date',
            ],
'platform'        => [
                'required',
                'string',
            ],
'followers_count' => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'posts_count'     => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'dp_count'        => [
                'sometimes',
                'nullable',
                'numeric',
            ],
];
    }
}
