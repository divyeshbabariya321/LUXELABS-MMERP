<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class WhatsappBusinessAccountCreateAccountRequest extends FormRequest
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
            'business_phone_number' => 'required',
            'business_account_id' => 'required',
            'business_access_token' => 'required',
            'business_phone_number_id' => 'required',
            'profile_picture_url' => 'sometimes|mimes:jpeg,jpg,png',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = redirect()->route('whatsapp.business.account.index')
            ->with('create_popup', true)
            ->withErrors($validator)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
