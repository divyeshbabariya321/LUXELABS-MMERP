<?php

namespace App\Http\Requests\Github;

use Illuminate\Foundation\Http\FormRequest;

class AddUserGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'organizationId' => [
                'required',
            ],
'group_id'       => [
                'required',
            ],
'role'           => [
                'required',
            ],
'username'       => [
                'required',
            ],
];
    }
}
