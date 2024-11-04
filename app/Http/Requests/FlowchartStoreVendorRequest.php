<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlowchartStoreVendorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'master_id' => [
                'required',
            ],
'name'      => [
                'required',
                'string',
            ],
'sorting'   => [
                'required',
                'numeric',
            ],
];
    }
}
