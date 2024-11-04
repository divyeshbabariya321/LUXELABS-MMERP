<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemeFileStoreThemeStructureRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'name'      => [
                'required',
            ],
'theme_id'  => [
                'required',
            ],
'is_file'   => [
                'required',
                'boolean',
            ],
'parent_id' => [
                'required',
                'exists:theme_structure,id',
            ],
];
    }
}
