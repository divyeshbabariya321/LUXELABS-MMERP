<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBugTrackingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'summary'            => [
                'required',
                'string',
            ],
'step_to_reproduce'  => [
                'required',
                'string',
            ],
'bug_type_id'        => [
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
'bug_severity_id'    => [
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
                'array',
            ],
];
    }
}
