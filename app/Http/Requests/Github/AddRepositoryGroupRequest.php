<?php

namespace App\Http\Requests\Github;

use Illuminate\Foundation\Http\FormRequest;

class AddRepositoryGroupRequest extends FormRequest
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
'repoId'         => [
                'required',
            ],
'group_id'       => [
                'required',
            ],
'permission'     => [
                'required',
            ],
];
    }
}
