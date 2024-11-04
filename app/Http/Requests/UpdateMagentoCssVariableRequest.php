<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMagentoCssVariableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'project_id' => [
                'required',
            ],
'filename'   => [
                'required',
            ],
'file_path'  => [
                'required',
            ],
'variable'   => [
                'required',
            ],
'value'      => [
                'required',
            ],
];
    }
}
