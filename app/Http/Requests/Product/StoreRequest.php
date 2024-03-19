<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'required',
            'merchant_id' => 'required',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2000',
            'stock' => 'required|numeric',
            'description' => 'nullable|max:250',
            'other_images.*' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'other_images.*.image' => 'The :attribute at position :array_position must be an image.',
            'other_images.*.mimes' => 'The :attribute at position :array_position must be a file of type: :values.',
            'other_images.*.max' => 'The :attribute at position :array_position may not be greater than :max kilobytes.',
        ];
    }
}
