<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestSuiteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'               => [
                'required',
                'string',
            ],
'step_to_reproduce'  => [
                'required',
                'string',
            ],
'url'                => [
                'required',
                'string',
            ],
'bug_environment_id' => [
                'required',
                'string',
            ],
'assign_to'          => [
                'required',
                'string',
            ],
'bug_status_id'      => [
                'required',
                'string',
            ],
'module_id'          => [
                'required',
                'string',
            ],
'remark'             => [
                'required',
                'string',
            ],
'website'            => [
                'required',
                'string',
            ],
];
    }
}
