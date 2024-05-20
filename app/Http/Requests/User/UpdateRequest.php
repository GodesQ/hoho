<?php

namespace App\Http\Requests\User;

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
            "firstname" => "nullable|min:2|max:50",
            "lastname" => "nullable|min:2|max:50",
            "middlename" => "nullable|min:2|max:50",
            "birthdate" => "nullable|date",
            "age" => "nullable|numeric",
            "gender" => "nullable|in:Male,Female",
            "countryCode" => "nullable",
            "contact_no" => "nullable|max:11",
            "interests" => "nullable|array",
            "role" => "nullable|in:guest,anonymous",
            "status" => "required|in:active,inactive,locked"
        ];
    }
}
