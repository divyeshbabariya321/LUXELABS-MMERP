<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileBugTrackingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'file'               => [
                'required',
            ],
'file_creation_date' => [
                'required',
            ],
'remarks'            => [
                'sometimes',
            ],
'bug_id'             => [
                'required',
            ],
'file_read'          => [
                'sometimes',
            ],
'file_write'         => [
                'sometimes',
            ],
];
    }
}
