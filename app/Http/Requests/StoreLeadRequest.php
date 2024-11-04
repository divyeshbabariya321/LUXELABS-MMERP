<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'customer_id' => [
                'required',
            ], 'instahandler' => '', 'rating' => [
                'required',
            ], 'status' => [
                'required',
            ], 'solophone' => '', 'comments' => '', 'userid' => '', 'address' => '', 'multi_brand' => '', 'email' => '', 'source' => '', 'assigned_user' => '', 'selected_product', 'size', 'leadsourcetxt', 'created_at' => 'required|date_format:"Y-m-d H:i"', 'whatsapp_number'
];
    }
}
