<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'          => [
                'required',
            ],
'details'       => [
                'required',
            ],
'type'          => [
                'required',
            ],
'related'       => [
                '',
            ],
'assigned_user' => [
                'required',
            ],
'remark'        => [
                '',
            ],
'minutes'       => [
                '',
            ],
'comments'      => [
                '',
            ],
'status'        => [
                '',
            ],
'userid'        => [
                '',
            ],
];
    }
}
