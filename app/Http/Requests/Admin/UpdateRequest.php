<?php

namespace App\Http\Requests\Admin;

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
            "username" => "required|min:3",
            "email" => "required|email",
            "firstname" => "nullable|min:2",
            "middlename" => "nullable|min:2",
            "lastname" => "nullable|min:2",
            "birthdate" => "nullable|date",
            "age" => "nullable|numeric",
            "role" => "required|exists:roles,slug",
        ];
    }
}
