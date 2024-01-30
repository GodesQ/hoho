<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => "required|min:4",
            "acronym" => "required",
            "region" => "nullable",
            "icon" => "nullable|image",
            "featured_image" => "nullable|image",
            "visibility" => "nullable",
            "description" => "nullable",
            "images" => "nullable|array",
        ];
    }
}
