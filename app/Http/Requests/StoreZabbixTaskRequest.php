<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreZabbixTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'task_name'               => [
                'required',
            ],
'zabbix_webhook_data_ids' => [
                'required',
            ],
'assign_to'               => [
                'required',
            ],
];
    }
}
