<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateLoadTestingRequest extends FormRequest
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
        return [
            'no_of_virtual_user' => [
                'required',
                'numeric',
            ],
            'ramp_time'          => [
                'required',
            ],
            'duration'       => [
                'required',
            ],
            'domain_name'         => [
                'required',
            ],
            'loop_count'         => [
                'required',
                'numeric',
            ],
            'domain_name'         => [
                'required',
            ],
            'request_method'         => [
                'required',
                'string',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
        'errors' => $validator->errors(),
        'status' => true,
        'code' => 0, 'message' => "Missing required details"
        ], 200));
    }
}
