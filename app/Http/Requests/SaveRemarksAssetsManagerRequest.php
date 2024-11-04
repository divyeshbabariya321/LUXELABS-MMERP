<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRemarksAssetsManagerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'amtua_id' => [
                'required',
            ],
'remarks'  => [
                'required',
            ],
];
    }
}
