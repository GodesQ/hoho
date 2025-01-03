<?php

namespace App\Http\Requests\Food;

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
            "title" => ['required'],
            "merchant_id" => ['required'],
            "image" => ['nullable', 'image', 'max:2048'],
            "price" => ['required', 'numeric'],
            "food_category_id" => ["required"],
            "description" => ["nullable"],
            "note" => ["nullable"],
            'other_images.*' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }
}
