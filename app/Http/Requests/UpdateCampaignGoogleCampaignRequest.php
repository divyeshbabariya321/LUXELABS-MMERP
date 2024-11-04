<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignGoogleCampaignRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'campaignName' => [
                'required',
                'max:55',
            ],
'budgetAmount' => [
                'required',
                'max:55',
            ],
'start_date'   => [
                'required',
                'max:15',
            ],
'end_date'     => [
                'required',
                'max:15',
            ],
];
    }
}
