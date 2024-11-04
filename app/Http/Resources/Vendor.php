<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Vendor extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'default_phone' => $this->default_phone,
            'whatsapp_number' => $this->whatsapp_number,
            'email' => $this->email,
            'social_handle' => $this->social_handle,
            'website' => $this->website,
            'login' => $this->login,
            'password' => $this->password,
            'gst' => $this->gst,
            'account_name' => $this->account_name,
            'account_swift' => $this->account_swift,
            'account_iban' => $this->account_iban,
            'is_blocked' => $this->is_blocked,
            'frequency' => $this->frequency,
            'reminder_message' => $this->reminder_message,
            'reminder_last_reply' => $this->reminder_last_reply,
            'reminder_from' => $this->reminder_from,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
            'feeback_status' => $this->feeback_status,
            'frequency_of_payment' => $this->frequency_of_payment,
            'bank_name' => $this->bank_name,
            'bank_address' => $this->bank_address,
            'city' => $this->city,
            'country' => $this->country,
            'ifsc_code' => $this->ifsc_code,
            'remark' => $this->remark,
            'chat_session_id' => $this->chat_session_id,
            'type' => $this->type,
            'framework' => $this->framework,
            'flowcharts' => $this->flowcharts,
            'flowchart_date' => $this->flowchart_date,
            'fc_status' => $this->fc_status,
            'question_status' => $this->question_status,
            'rating_question_status' => $this->rating_question_status,
            'price' => $this->price,
            'currency' => $this->currency,
            'price_remarks' => $this->price_remarks,
        ];
    }
}
