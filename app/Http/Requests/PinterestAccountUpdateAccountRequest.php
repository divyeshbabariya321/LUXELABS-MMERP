<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PinterestAccountUpdateAccountRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pinterest_application_name' => 'required',
            'pinterest_client_id' => 'required',
            'pinterest_client_secret' => 'required',
            'is_active' => 'required|in:true,false',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = redirect()->route('pinterest.accounts')
            ->with('create_popup', true)
            ->withErrors($validator)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
