<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdsetSocialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'             => [
                'required',
            ],
'destination_type' => [
                'required',
            ],
'status'           => [
                'required',
            ],
'campaign_id'      => [
                'required',
            ],
'start_time'       => [
                'required',
            ],
'end_time'         => [
                'required',
            ],
'billing_event'    => [
                'required',
            ],
'bid_amount'       => [
                'required',
            ],
];
    }
}
