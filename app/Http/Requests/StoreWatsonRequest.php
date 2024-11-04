<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWatsonRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'store_website_id'       => [
                'required',
                'integer',
            ],
'api_key'                => [
                'required',
                'string',
            ],
'work_space_id'          => [
                'required',
                'string',
            ],
'assistant_id'           => [
                'required',
                'string',
            ],
'url'                    => [
                'required',
                'string',
            ],
'speech_to_text_api_key' => [
                'required',
                'string',
            ],
'speech_to_text_url'     => [
                'required',
                'string',
            ],
'user_name'              => [
                'required',
                'string',
            ],
'password'               => [
                'required',
                'string',
            ],
];
    }
}
