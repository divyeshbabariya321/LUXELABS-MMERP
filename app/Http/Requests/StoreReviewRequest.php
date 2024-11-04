<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'title'      => [
                'required',
            ],
'review'     => [
                'required',
            ],
'account_id' => [
                'required',
            ],
];
    }
}
