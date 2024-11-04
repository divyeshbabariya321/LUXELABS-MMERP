<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveVendorRatingQuestionAnswerVendorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'vendor_id'   => [
                'required',
            ],
'question_id' => [
                'required',
            ],
'answer'      => [
                'required',
            ],
];
    }
}
