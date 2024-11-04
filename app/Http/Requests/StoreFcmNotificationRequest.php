<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFcmNotificationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'title'       => [
                'required',
            ],
'url'         => [
                'required',
                'exists:store_websites,website',
            ],
'sent_at'     => [
                'required',
            ],
'body'        => [
                'required',
                'string',
            ],
'expired_day' => [
                'required',
                'integer',
            ],
];
    }
}
