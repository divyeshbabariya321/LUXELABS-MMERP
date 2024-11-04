<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WhitelistIPIpLogRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'server_name' => [
                'required',
            ],
'ip_address'  => [
                'required',
                'ip',
            ],
'comment'     => [
                'required',
            ],
];
    }
}
