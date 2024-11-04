<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuilderIOTaskstoreUicheckRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'task_name'     => [
                'required',
            ],
'selected_rows' => [
                'required',
            ],
'assign_to'     => [
                'required',
            ],
];
    }
}
