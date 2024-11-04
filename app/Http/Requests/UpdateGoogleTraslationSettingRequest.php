<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoogleTraslationSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'email'        => [
                'required',
                'email',
            ],
'last_note'    => [
                'required',
            ],
'status'       => [
                'required',
                'boolean',
            ],
'account_json' => [
                'required',
            ],
'project_id'   => [
                'required',
            ],
];
    }
}
