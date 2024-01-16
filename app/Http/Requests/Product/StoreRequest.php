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
            'quantity' => 'required|numeric',
            'description' => 'nullable|max:250'
        ];
    }
}
