<?php

namespace App\Http\Requests\AffiliateMarketing;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateProviderAccountsRequest extends FormRequest
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
            'store_website_id' => 'required',
            'affiliates_provider_id' => 'required',
            'api_key' => 'required',
            'status' => 'required|in:true,false',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = redirect()->route('affiliate-marketing.providerAccounts')
            ->with('create_popup', true)
            ->withErrors($validator)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
