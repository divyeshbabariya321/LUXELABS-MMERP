<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoogleScreencastRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'id'          => ['required'],
'file_name'   => ['required'],
'file_id'     => ['required'],
'file_remark' => ['required'],
];
    }
}
