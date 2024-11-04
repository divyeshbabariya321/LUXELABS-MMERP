<?php

namespace App\Http\Requests\Github;

use Illuminate\Foundation\Http\FormRequest;

class PullRequestActivitiesUpdateRepositoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'prIds'  => [
                'required',
            ],
'repoId' => [
                'required',
            ],
];
    }
}
