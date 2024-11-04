<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeminiAIRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'store_website_id' => 'required|integer|unique:faqs',
            'api_key' => 'required|string',
            'api_url' => 'required|url',
            'prompt' => 'required|string',
            'fallback_message' => 'required|string'
        ];
    }
}
