<?php

namespace App\Http\Requests\AffiliateMarketing;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCreateRequest extends FormRequest
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
            'referral_code' => 'sometimes',
            'tracking_id' => 'sometimes',
            'click_id' => 'sometimes',
            'coupon' => 'sometimes',
            'currency' => 'sometimes',
            'asset_id' => 'required',
            'customer_id' => 'required',
            'status' => 'sometimes',
            'user_agent' => 'sometimes',
            'ip' => 'sometimes',
        ];
    }
}
