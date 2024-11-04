<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveChildScraperScrapRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'supplier_id' => [
                'required',
            ],
'run_gap'     => [
                'required',
            ],
'start_time'  => [
                'required',
            ],
];
    }
}
