<?php

namespace App\Http\Requests\Github;

use Illuminate\Foundation\Http\FormRequest;

class JobNameStoreRepositoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'job_name'     => [
                'required',
            ],
'organization' => [
                'required',
            ],
'repository'   => [
                'required',
            ],
];
    }
}
