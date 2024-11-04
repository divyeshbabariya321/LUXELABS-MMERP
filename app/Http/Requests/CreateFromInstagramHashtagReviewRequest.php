<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFromInstagramHashtagReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'post'      => [
                'required',
            ],
'comment'   => [
                'required',
            ],
'poster'    => [
                'required',
            ],
'commenter' => [
                'required',
            ],
'media_id'  => [
                'required',
            ],
'date'      => [
                'required',
            ],
'code'      => [
                'required',
            ],
];
    }
}
