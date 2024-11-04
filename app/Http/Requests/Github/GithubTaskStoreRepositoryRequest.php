<?php

namespace App\Http\Requests\Github;

use Illuminate\Foundation\Http\FormRequest;

class GithubTaskStoreRepositoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'task_name'        => [
                'required',
            ],
'task_details'     => [
                'required',
            ],
'selected_rows'    => [
                'required',
            ],
'selected_repo_id' => [
                'required',
            ],
'assign_to'        => [
                'required',
            ],
];
    }
}
