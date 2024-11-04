<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostMediaInstagramRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date',
            ],
            'hour' => [
                'required',
                'numeric',
                'min:0',
                'max:23',
            ],
            'minute' => [
                'required',
                'numeric',
                'min:0',
                'max:59',
            ],
        ];
    }
}
