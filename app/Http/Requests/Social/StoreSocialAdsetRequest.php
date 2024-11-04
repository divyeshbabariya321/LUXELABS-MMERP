<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialAdsetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'config_id'     => [
                'required',
            ],
'campaign_id'   => [
                'required',
            ],
'name'          => [
                'required',
            ],
'billing_event' => [
                'required',
            ],
'start_time'    => [
                'required',
            ],
'end_time'      => [
                'required',
            ],
'bid_amount'    => [
                'nullable',
            ],
'status'        => [
                'required',
            ],
];
    }
}
