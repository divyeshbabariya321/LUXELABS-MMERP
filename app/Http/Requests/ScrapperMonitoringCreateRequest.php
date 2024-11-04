<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScrapperMonitoringCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'task_id'       => 'required',
            'scrapper_name' => 'required',
            'need_proxy'    => 'required',
            'move_to_aws'   => 'required',
            'remarks'       => 'required',
        ];

        if (auth()->user()->isAdmin()) {
            $rules['user_id'] = 'required';
        }

        return $rules;
    }
}
