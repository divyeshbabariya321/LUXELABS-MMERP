<?php

namespace App\Http\Requests\AffiliateMarketing;

use Illuminate\Foundation\Http\FormRequest;

class AffiliateCreateRequest extends FormRequest
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
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'affiliate_group_id' => 'required|numeric',
        ];
    }
}
