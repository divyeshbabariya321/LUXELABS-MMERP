<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChannelYoutubeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'store_websites'       => [
                'required',
            ],
'status'               => [
                'required',
            ],
'email'                => [
                'required',
                'email',
            ],
'oauth2_client_id'     => [
                'required',
            ],
'oauth2_client_secret' => [
                'required',
            ],
'oauth2_refresh_token' => [
                'required',
            ],
];
    }
}
