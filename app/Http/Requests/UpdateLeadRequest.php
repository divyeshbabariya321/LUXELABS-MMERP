<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'customer_id'  => [
                'required',
            ],
'client_name'  => [
                '',
            ],
'contactno'    => [
                'sometimes',
                'nullable',
                'numeric',
                'regex:/^[91]{2}/',
                'digits:12',
            ],
'instahandler' => [
                '',
            ],
'rating'       => [
                'required',
            ],
'status'       => [
                'required',
            ],
'solophone'    => [
                '',
            ],
'comments'     => [
                '',
            ],
'userid'       => [
                '',
            ],
'created_at'   => [
                'required',
                'date_format:"Y-m-d H:i"',
            ],
];
    }
}
