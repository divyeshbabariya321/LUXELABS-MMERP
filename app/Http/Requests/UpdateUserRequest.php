<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    
    public function rules(): array
    {
       $id = $this->route('userId');
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email,' . $id],
            'gmail' => ['sometimes', 'nullable', 'email'],
            'phone' => ['sometimes', 'nullable', 'integer', 'unique:users,phone,' . $id],
            'password' => ['same:confirm-password'],
            'roles' => ['required'],
            'slack_id' => ['nullable','string','max:255'],
        ];
    }
}
