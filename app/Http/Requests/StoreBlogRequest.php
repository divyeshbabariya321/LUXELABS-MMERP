<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'user_id'       => [
                'required',
            ],
'idea'          => [
                'nullable',
                'max:524',
            ],
'content'       => [
                'nullable',
            ],
'plaglarism'    => [
                'nullable',
                'max:8',
            ],
'internal_link' => [
                'nullable',
                'max:524',
            ],
'external_link' => [
                'nullable',
                'max:524',
            ],
'meta_desc'     => [
                'nullable',
                'max:524',
            ],
'url_structure' => [
                'nullable',
                'max:524',
            ],
'facebook'      => [
                'nullable',
                'max:256',
            ],
'instagram'     => [
                'nullable',
                'max:524',
            ],
'twitter'       => [
                'nullable',
                'max:256',
            ],
'google'        => [
                'nullable',
                'max:256',
            ],
'bing'          => [
                'nullable',
                'max:256',
            ],
];
    }
}
