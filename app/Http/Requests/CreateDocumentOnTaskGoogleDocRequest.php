<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDocumentOnTaskGoogleDocRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'doc_type'  => [
'required',
Rule::in('spreadsheet', 'doc', 'ppt', 'txt', 'xps'),
],
'doc_name'  => [
'required',
'max:800',
],
'task_id'   => ['required'],
'task_type' => ['required'],
];
    }
}
