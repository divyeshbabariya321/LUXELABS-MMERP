<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CopyStoreWiseReplyQuickReplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'reply_id'         => [
                'required',
            ],
'website_store_id' => [
                'required',
            ],
];
    }
}
