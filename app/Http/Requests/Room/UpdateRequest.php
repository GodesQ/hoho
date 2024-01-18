<?php

namespace App\Http\Requests\Room;

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
            "room_name" => "required",
            "merchant_id" => "required",
            "price" => "required|numeric",
            "available_pax"=> "required",
            "description" => "required",
            "amenities" => "nullable",
            'other_images.*' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }
}
