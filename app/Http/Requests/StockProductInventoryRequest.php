<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockProductInventoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ['stock' => [
                'required',
                'numeric',
                'min:0',
            ],];
    }
}
