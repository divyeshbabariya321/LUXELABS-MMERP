<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexColdLeadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['pagination' => [
                'required',
                'integer',
            ],];
    }
}
