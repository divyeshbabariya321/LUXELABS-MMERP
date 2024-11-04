<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageWhatsAppRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'customer_id'             => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'supplier_id'             => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'erp_user'                => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'status'                  => [
                'required',
                'numeric',
            ],
'assigned_to'             => [
                'sometimes',
                'nullable',
            ],
'lawyer_id'               => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'case_id'                 => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'blogger_id'              => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'document_id'             => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'quicksell_id'            => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'old_id'                  => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'site_development_id'     => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'social_strategy_id'      => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'store_social_content_id' => [
                'sometimes',
                'nullable',
                'numeric',
            ],
'payment_receipt_id'      => [
                'sometimes',
                'nullable',
                'numeric',
            ],
];
    }
}
