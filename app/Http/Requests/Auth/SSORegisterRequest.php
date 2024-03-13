<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SSORegisterRequest extends FormRequest
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
            'username' => [
                'required',
                'max:30',
                new \App\Rules\UniqueAcrossTables('users', 'admins', 'username')
            ],
            'email' => [
                'required',
                'email',
                new \App\Rules\UniqueAcrossTables('users', 'admins', 'email')
            ],
            'password' => 'required',
            'contact_number' => 'required',
            'firstname' => 'nullable',
            'lastname' => 'nullable',
            'middlename' => 'nullable',
            'birthdate' => 'nullable',
            'gender' => 'nullable',
            'country_of_residence' => 'nullable',
        ];
    }
}
