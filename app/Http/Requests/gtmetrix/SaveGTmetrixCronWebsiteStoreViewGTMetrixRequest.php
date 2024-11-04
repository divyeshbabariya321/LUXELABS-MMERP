<?php

namespace App\Http\Requests\gtmetrix;

use Illuminate\Foundation\Http\FormRequest;

class SaveGTmetrixCronWebsiteStoreViewGTMetrixRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['website' => [
                'required',
            ],];
    }
}
