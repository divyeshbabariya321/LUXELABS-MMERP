<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuickSellRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['images.*' => [
                'sometimes ',
                ' mimes:jpeg,bmp,png,jpg',
            ],];
    }
}
