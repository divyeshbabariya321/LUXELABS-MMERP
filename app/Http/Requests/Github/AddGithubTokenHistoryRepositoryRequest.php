<?php

namespace App\Http\Requests\Github;

use Illuminate\Foundation\Http\FormRequest;

class AddGithubTokenHistoryRepositoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'github_repositories_id' => [
                'required',
            ],
'details'                => [
                'required',
            ],
];
    }
}
