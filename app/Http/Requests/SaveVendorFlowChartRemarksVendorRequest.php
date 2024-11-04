<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveVendorFlowChartRemarksVendorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'vendor_id'     => [
                'required',
            ],
'flow_chart_id' => [
                'required',
            ],
'remarks'       => [
                'required',
            ],
];
    }
}
