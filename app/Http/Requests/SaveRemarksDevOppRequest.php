<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRemarksDevOppRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'main_category_id' => [
                'required',
            ],
'sub_category_id'  => [
                'required',
            ],
'remarks'          => [
                'required',
            ],
];
    }
}
