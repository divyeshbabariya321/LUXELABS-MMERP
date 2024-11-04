<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaintRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'customer_id'    => [
                'sometimes',
                'nullable',
                'integer',
            ],
'platform'       => [
                'sometimes',
                'nullable',
                'string',
            ],
'complaint'      => [
                'required',
                'string',
                'min:3',
            ],
'thread.*'       => [
                'sometimes',
                'nullable',
                'string',
            ],
'account_id.*'   => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'link'           => [
                'sometimes',
                'nullable',
                'url',
            ],
'where'          => [
                'sometimes',
                'nullable',
                'string',
            ],
'username'       => [
                'sometimes',
                'nullable',
                'string',
            ],
'name'           => [
                'sometimes',
                'nullable',
                'string',
            ],
'plan_of_action' => [
                'sometimes',
                'nullable',
                'string',
            ],
'date'           => [
                'required',
                'date',
            ],
];
    }
}
