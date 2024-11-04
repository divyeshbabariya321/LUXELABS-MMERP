<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectProductSupervisorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'role'   => [
                'required',
            ],
'reason' => [
                'required',
            ],
];
    }
}
