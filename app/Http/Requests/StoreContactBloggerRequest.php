<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactBloggerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'             => [
                'required',
            ],
'email'            => [
                'required',
                'email',
            ],
'instagram_handle' => [
                'required',
            ],
];
    }
}
