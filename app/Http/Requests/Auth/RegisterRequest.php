<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array
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
            'contact_no' => 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'birthdate' => 'nullable',
        ];
    }
}
