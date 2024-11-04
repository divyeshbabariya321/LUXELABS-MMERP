<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScrapperDevelopmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'task_id'     => [
                'required',
            ],
'column_name' => [
                'required',
            ],
'status'      => [
                'required',
            ],
];
    }
}
