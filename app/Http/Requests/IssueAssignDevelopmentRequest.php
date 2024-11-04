<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueAssignDevelopmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['user_id' => [
                'required',
                'integer',
            ],];
    }
}
