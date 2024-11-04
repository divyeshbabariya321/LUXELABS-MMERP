<?php

namespace App\Http\Requests\gtmetrix;

use Illuminate\Foundation\Http\FormRequest;

class SaveGTmetrixCronTypeWebsiteStoreViewGTMetrixRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['type' => [
                'required',
            ],];
    }
}
