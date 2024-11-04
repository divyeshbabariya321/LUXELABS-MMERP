<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleResponsiveDisplayAdCreateAdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'headline1' => 'required|max:30',
            'headline2' => 'required|max:30',
            'headline3' => 'required|max:30',
            'description1' => 'required|max:90',
            'description2' => 'required|max:90',
            'long_headline' => 'required|max:90',
            'business_name' => 'required|max:25',
            'final_url' => 'required|max:200|url',
            'marketing_images' => 'required|array|max:15',
            'marketing_images.*' => 'mimes:jpeg,png,gif|dimensions:min_width=600,min_height=314,ratio=1.91:1',
            'square_marketing_images' => 'required|array|max:15',
            'square_marketing_images.*' => 'mimes:jpeg,png,gif|dimensions:min_width=300,min_height=300,ratio=1:1',
        ];
    }
}
