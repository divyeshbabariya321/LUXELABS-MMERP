<?php

namespace App\Http\Requests\AffiliateMarketing;

use Illuminate\Foundation\Http\FormRequest;

class PaymentsCreateRequest extends FormRequest
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
            'affiliate_id' => 'required',
            'amount' => 'required',
            'currency' => 'required|max:3',
        ];
    }
}
