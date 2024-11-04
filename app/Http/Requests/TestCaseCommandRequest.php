<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestCaseCommandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'site_to_use' => 'required',
            'browser' => 'required',
            'headless' => 'required',
            'test' => 'required'
        ];
    }
}
