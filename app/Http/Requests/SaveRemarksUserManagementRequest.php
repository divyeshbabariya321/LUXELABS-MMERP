<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRemarksUserManagementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'user_feedback_category_id' => [
                'required',
            ],
'user_feedback_vendor_id'   => [
                'required',
            ],
'remarks'                   => [
                'required',
            ],
];
    }
}
